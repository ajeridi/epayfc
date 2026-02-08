<?php

require_once  "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "model/Item.php";
require_once "framework/Tools.php";
require_once "model/User.php";
require_once "utils/ImageUtils.php";


class ControllerItem extends Controller
{

    public function index(): void
    {
        $this->browse();
    }
    public function items(): void
    {
        $this->redirect("item", "list");
    }


    public function browse(): void
    {
        $user = $this->get_user_or_false();

        if ($user != false) { // si $user == false nous sommes en mode invité
            $items_participated_in = Item::get_items_participatedIn($user->get_id());
            $other_available_items = Item::get_other_available_item($user->get_id());
        } else {
            $items_participated_in = [];
            $other_available_items = Item::get_other_available_item(-1);    // aucun user a l'id -1
        }
        (new View("item_browse"))->show(["items_participated_in" => $items_participated_in, "other_available_items" => $other_available_items, "user" => $user]);
    }


    public function open(): void
    {
        $user = $this->get_user_or_false();

        $from = "";
        $priority = 1;

        if (isset($_GET["param1"]) && is_numeric($_GET["param1"])) {
            $item = Item::get_item_by_id($_GET["param1"]);
            $pictures = $item->get_pictures();

            if (isset($_GET["param2"])) {
                $from = substr(stristr($_GET["param2"], "-"), 1);
                if (isset($_GET["param3"]) && is_numeric($_GET["param3"])) {
                    $priority = $_GET["param3"];
                }
            }
        } else {
            throw new Exception("No Item specified or Item not found");
        }
        if (!$item->is_open_item() && $user->get_id() !=  $item->owner &&  (count($item->bids) == 0 || $user->get_id() != $item->bids['0']->owner)) {
            throw new Exception("This Item seem to not be yours.");
        } else {
            $owner_picture = User::get_path_picture($item->owner);
            $owner_role = User::get_role_by_id($item->owner);

            (new View("item_open"))->show(["item" => $item, "owner_picture" => $owner_picture, "owner_role" => $owner_role, "pictures" => $pictures, "user" => $user, "from" => $from, "priority" => $priority]);
        }
    }

    public function bid(): void
    {
        $user = $this->get_user_or_redirect();
        $current_date = AppTime::get_current_datetime();
        if ($_POST) {
            $item_id = $_GET['param1'];
            $amount = str_replace(",", ".",($_POST['buy']) ? $_POST['buy'] : $_POST['min_bid']);
            
            Item::buy($user->get_id(), $item_id, $current_date, $amount);
            $this->redirect("item", "open", $item_id);
        }
        $this->index();
    }

    private function build_errors_array() : array
    {   $errors[] = [];
        $errors['title'] = [];
        $errors['description'] = [];
        $errors['duration_days'] = [];
        $errors['starting_bid'] = [];
        $errors['buy_now_price'] = [];
        $errors['saleprice'] = [];
        return $errors;
    }
    private function build_info_array(Item | null  $item,bool $first ) : array
    {
        $info =[]; 

        if($item!==null){
        
            $info['id']=$item->id;
            $info['title'] = $first ? $item->title : Tools::sanitize($_POST['title']) ;
            $info['description'] =$first ? $item->description :Tools::sanitize($_POST['description']);
            $info['duration_days'] =$first ? $item->duration_days : Tools::sanitize($_POST['duration_days']);;
            $info['starting_bid'] =$first ?  $item->starting_bid :Tools::sanitize($_POST['starting_bid']); ;
            if($item->starting_bid != null){
                $info['buy_now_price'] = $first ? $item->buy_now_price : $info['buy_now_price'] = Tools::sanitize($_POST['buy_now_price']); ;
                $info['saleprice']=$first ? null :  $info['saleprice'] = Tools::sanitize($_POST['saleprice']);
            }else {
                $info['saleprice'] =  $first ? $item->buy_now_price :$info['saleprice'] = Tools::sanitize($_POST['saleprice']);
                $info['buy_now_price']= $first ? null : $info['buy_now_price'] = Tools::sanitize($_POST['buy_now_price']); 
            }
            
        } else  {
            
            $info['id']=0; 
            $info['title']=$first ? null : Tools::sanitize($_POST['title']);
            $info['description']=$first ? null : Tools::sanitize($_POST['description']);
            $info['duration_days']=$first ? null : Tools::sanitize($_POST['duration_days']);
            $info['starting_bid'] =$first ? null : Tools::sanitize($_POST['starting_bid']);
            $info['buy_now_price']=$first ? null : Tools::sanitize($_POST['buy_now_price']);
            $info['saleprice']=$first ? null : Tools::sanitize($_POST['saleprice']);
    }

        return $info ;
    }

    public function edit() : void 
    {
        $action='edit';
        $user=$this->get_user_or_redirect();
        
        if(!isset($_GET['param1']) || !is_numeric($_GET['param1'])){
            $this->index();
        } else {
            $item=Item::get_item_by_id($_GET['param1']);           
            if($item->owner!= $user->get_id() || !$item->is_open_item() ||count($item->bids)!=0){
                throw new Exception("you can edit this ");
                return;
            }
        }  
        if(!$_POST){
            $item=Item::get_item_by_id($_GET['param1']);  

            $errors=$this::build_errors_array();
            $info=$this::build_info_array($item,true);

            (new View('item_edit'))->show(['info'=>$info,'errors'=>$errors,'action'=>$action]);
        } else {

            $info=$this::build_info_array($item,false);
            $errors = $this::build_errors_array();
            $errors=Item::validat_item($user->get_id(), $info,$errors);
              if (count($errors['title']) == 0 && count($errors['description']) == 0 &&  count($errors['duration_days'])==0
               && count($errors['saleprice']) == 0 && count($errors['buy_now_price']) == 0 && count($errors['starting_bid']) == 0){
                Item::put_edition_item( $user->get_id(), $info);
                 $this->redirect("item", "open",$info['id'], "from-items");
               } else {
                (new View("item_edit"))->show(['info' => $info, 'errors' => $errors]);
               }      
        }
        
    }


    public function add() :  void
    {
        $action='add';
        $user = $this->get_user_or_redirect();
        $errors=$this::build_errors_array();
        if(!$_POST){
            $info=$this::build_info_array(null,true);
            (new View("item_edit"))->show(['info' => $info, 'errors' => $errors,'action'=>$action]);
        } else{
            $info=$this::build_info_array(null,false);
            $errors=Item::validat_item($user->get_id(),$info,$errors);
            if (count($errors['title']) == 0 && count($errors['description']) == 0 &&  count($errors['duration_days'])==0
               && count($errors['saleprice']) == 0 && count($errors['buy_now_price']) == 0 && count($errors['starting_bid']) == 0){
                $info['id']=Item::put_edition_item( $user->get_id(), $info);
                 $this->redirect("item", "open",$info['id']);
               } else {
                (new View("item_edit"))->show(['info' => $info, 'errors' => $errors,'action'=>$action]);
               }      
        }   
    }



    public function list(): void
    {
        $user = $this->get_user_or_redirect();
        $active_items = Item::get_user_active_item($user->get_id());
        $closed_unsold_items = Item::get_user_closed_unsold_item($user->get_id());
        $sold_items = Item::get_user_sold_item($user->get_id());
        (new View("item_list"))->show(["active_items" => $active_items, "closed_unsold_items" => $closed_unsold_items, "sold_items" => $sold_items, "user" => $user]);
    }


    public function delete(): void
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $item_id = Tools::sanitize($_GET['param1']);
            $item = Item::get_item_by_id($item_id);

            if ($item->owner != $user->get_id() || count($item->bids) != 0 || !$item->is_open_item()) {
                throw new Exception("You can't delete this item ");
            }
            $owner = User::get_full_name_by_id($item->owner);
            (new View("delete_confirm"))->show(["owner" => $owner, "title" => $item->title, "id" => $item_id]);
        }
    }
    public function delete_item(): void
    {
        $user = $this->get_user_or_redirect();
        $item_id = Tools::sanitize($_GET['param1']);
        $item = Item::get_item_by_id($item_id);

        if ($item->owner != $user->get_id() || count($item->bids) != 0 || !$item->is_open_item()) {
            throw new Exception("You can't delete this item ");
        } else {
            $this->delete_item_files($item);
            $item->delete();
            $this->redirect('item', 'list');
        }
    }

    private function delete_item_files(Item $item) : void {
        foreach($item->get_pictures() as $picture) {
            ImageUtils::delete_image_files($picture->get_path());
        }
        rmdir("uploads/items/$item->id");
    }


    public function manage_images(): void
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_GET['param1']) && is_numeric($_GET['param1']) && $user->is_item_owner($_GET['param1'])) {    // pas oublier de check aussi si l'objet appartient au user connecte et que l'item est actif
            $item_id = $_GET['param1'];
            $item = Item::get_item_by_id($item_id);
            if (!$item->is_open_item())
                $this->redirect("item");
            if (isset($_FILES["image"]) && $_FILES["image"]["name"] != "") { // si on a upload une image
                $image = $_FILES["image"];
                $errors = ImageUtils::validate_image($image); // validation de l'image
                if (count($errors) == 0) // si le fichier est valide on peut proceder a son upload
                    $this->upload_image($item, $image);
            }
            if (isset($_POST["move"])) { // si on a clique sur la fleche de gauche
                $this->swap_priority($item->id, $_POST["move"]);
            } else if (isset($_POST["delete"])) {
                $this->delete_image($item, $_POST["delete"]);
            }
            if (count($_POST) != 0) // si on requete post -> redirection vers GET sinon on affiche la page avec les erreurs
                $this->redirect("item", "manage_images", "$item->id");
            (new View("manage_images"))->show(["user" => $user, "item" => $item, "errors" => $errors]);
        }
        else{
        $this->redirect("item");
        }
    }


    private function upload_image(Item $item, array $image): void
    {
        //verifier si un dossier existe dans uploads en regardant si l'item en question possede une image
        $this->create_dir_if_not_exists($item);
        // redimensionner le fichier en deux images une en 1080p et une autre en 360px 
        $formatted_images = $this->create_new_images($image);
        // sauvegarde des nouvelles images dans le repertoire uploads
        $new_image_path = $this->make_image_path($image, $item->id);
        $this->save_new_images($formatted_images, $new_image_path);
        // on l'insere dans la db
        $item->upload_image($new_image_path);
        $this->redirect("item", "manage_images", "$item->id");
    }

    private function create_dir_if_not_exists(Item $item): void
    {
        if ($item->get_number_of_pictures() == 0)
            mkdir("uploads/items/$item->id");
    }

    private function create_new_images(array $image): array
    {
        $new_images["full_image"] = ImageUtils::scale_image($image, Configuration::get("picture_resolution"));
        $new_images["thumbnail"] = ImageUtils::scale_image($image, Configuration::get("thumbnail_resolution"));
        return $new_images;
    }

    private function save_new_images(array $images, string $path): void
    {
        ImageUtils::save_image_file($images["full_image"], $path);
        ImageUtils::save_image_file($images["thumbnail"], ImageUtils::get_thumbnail_path($path));
    }

    private function make_image_path(array $image, $item_id): string
    {
        $file_name = str_replace(" ", "", pathinfo($image["name"], PATHINFO_FILENAME));
        $unique_file_name = uniqid($file_name); // creation d'un nom unique pour eviter d'avoir deux images avec le meme path dans la DB
        $picture_path = "uploads/items/$item_id/$unique_file_name.jpg";
        return $picture_path;
    }

    private function swap_priority(int $item_id, int $post_value): void
    {
        $increment = $post_value < 0 ? -1 : 1;
        $old_priority = $post_value * $increment; 
        $new_priority = $post_value * $increment + $increment;
        Picture::swap_priorities($item_id, $old_priority, $new_priority);
    }

    private function delete_image(Item $item, int $priority): void
    {
        $image_path = $item->get_path($priority);
        $item->delete_picture($priority);
        ImageUtils::delete_image_files($image_path);
        $this->check_for_empty_picture_dir($item);
    }

    private function check_for_empty_picture_dir(Item $item): void
    {
        $item = Item::get_item_by_id($item->id);
        // si le dossier est vide, donc nb_pictures == 0, on le supprime
        if ($item->get_number_of_pictures() == 0)
            rmdir("uploads/items/$item->id");
    }

    public  function sales() : void
    {
        $user = $this->get_user_or_redirect();
        $action = "sales";
        $salesItem = Item::get_user_sold_item($user->get_id());
        $info = Item::cal_total_avg_nbrOfSales($salesItem);
        $loyer_buyer = Item::find_loyer_bider($salesItem, "buyer");
        $nom = $this::concate_names($loyer_buyer);

        (new View("item_closed"))->show(["salesItem" => $salesItem, "info" => $info, "loyer_buyer" => $loyer_buyer, "nom" => $nom, 'user' => $user, 'action' => $action]);
    }

    public function purchases(): void
    {
        $user = $this->get_user_or_redirect();
        $action = "purchases";
        $salesItem = Item::get_purchases_items($user);
        $info = Item::cal_total_avg_nbrOfSales($salesItem);
        $top_seller = Item::find_loyer_bider($salesItem, "pseudo");
        $nom = $this::concate_names($top_seller);

        (new View("item_closed"))->show(["salesItem" => $salesItem, 'info' => $info, 'loyer_buyer' => $top_seller, "nom" => $nom, 'user' => $user, 'action' => $action]);
    }

    private function concate_names(array $loyer_buyer): string
    {
        $nom = "";
        foreach ($loyer_buyer as $buyer) {
            $nom = ($nom == "" ? $buyer : $nom . " et " . $buyer);
        }
        return $nom;
    }
}
