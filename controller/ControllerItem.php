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
        $user=$this->get_user_or_redirect();

        $from="";
        $priority=1;

        if(isset($_GET["param1"]) && is_numeric($_GET["param1"])){
            $item =Item::get_item_by_id($_GET["param1"]);     
            $pictures=$item->get_pictures();
            
            if(isset($_GET["param2"])){
                $from=substr(stristr($_GET["param2"],"-"),1);
                if(isset($_GET["param3"]) && is_numeric($_GET["param3"])){
                    $priority=$_GET["param3"];
                }
            }                    
        } else{
              throw new Exception("No Item specified or Item not found");
        }
        if(!$item->is_open_item() && $user->get_id() !=  $item->owner &&  (count($item->bids)==0 || $user->get_id() != $item->bids['0']->owner)){
              throw new Exception("This Item seem to not be yours.");
        }else{
          $owner_picture = User::get_path_picture($item->owner);
          $owner_role=User::get_role_by_id($item->owner) ;

        (new View("item_open"))->show(["item"=>$item,"owner_picture"=>$owner_picture,"owner_role"=>$owner_role,"pictures"=>$pictures,"user"=>$user,"from"=>$from,"priority"=>$priority]);
        }
        
    }
 
    public function bid(): void
    {
        $user = $this->get_user_or_redirect();
        $current_date = AppTime::get_current_datetime();
            if($_POST){
            $item_id = $_GET['param1'];
            $amount = ($_POST['buy']) ? $_POST['buy'] : $_POST['min_bid'] ;
            Item::buy($user->get_id(), $item_id, $current_date, $amount);
            $this->redirect("item", "open", $item_id);
            }
            $this->index();
        
    }
    public function edit() : void {
        $user=$this->get_user_or_redirect();
        
        if(isset($_GET['param1']) && is_numeric($_GET['param1'])){
        $item=Item::get_item_by_id($_GET['param1']);     
            if($item->owner!= $user->get_id() || !$item->is_open_item() ||count($item->bids)!=0){
                throw new Exception("you can edit this ");
                return;
            }
   
        }
        if (count($_POST)) {
            $ok = true;

            $user = $this->get_user_or_redirect();
            $info = [];
            $info['id'] = $_GET['param1'];

            $title = Tools::sanitize($_POST['title']);
            $info['title'] = $title;
            $description = Tools::sanitize($_POST['description']);
            $info['description'] = $description;
            $duration_days = Tools::sanitize($_POST['duration_days']);
            $info['duration_days'] = $duration_days;
            $starting_bid = Tools::sanitize($_POST['starting_bid']);
            $info['starting_bid'] = $starting_bid;
            $buy_now_price = Tools::sanitize($_POST['buy_now_price']);
            $info['buy_now_price'] = $buy_now_price;
            $saleprice = Tools::sanitize($_POST['saleprice']);
            $info['saleprice'] = $saleprice;


            $errors = Item::validat_item($user->get_id(), $title, $description, $duration_days, $buy_now_price, $starting_bid, $saleprice);
            if (count($errors['title']) == 0 && count($errors['description']) == 0 &&  count($errors['duration_days']) == 0 && count($errors['saleprice']) == 0 && count($errors['buy_now_price']) == 0 && count($errors['starting_bid']) == 0) {
                $id = $_GET['param1'];
                $info['id'] = $id;
                $id_nv = Item::put_edition_item($id, $user->get_id(), $title, $description, $duration_days, $buy_now_price, $starting_bid, $saleprice);
                $this->redirect("item", "open", $id_nv, "from-items");
            } else {

                (new View("item_edit"))->show(['info' => $info, 'errors' => $errors, 'ok' => $ok, "action" => "edit"]);
            }
        } else {
            $ok = false;
            $user = $this->get_user_or_redirect();
            $item_id = $_GET['param1'];
            $info = Item::get_item_ino($item_id);
            if (Item::is_Direct_sale($item_id)) {
                $info['duration_days'] = $info['duration_days'];
                $info['starting_bid'] = null;
                $info['saleprice'] = $info['buy_now_price'];
                $info['buy_now_price'] = null;
            } else {
                $info['saleprice'] = null;
            }
            $errors[] = [];
            $errors['duration_days'] = [];
            $errors['title'] = [];
            $errors['description'] = [];
            $errors['buy_now_price'] = [];
            $errors['starting_bid'] = [];
            $errors['saleprice'] = [];
            $action = "edit";

            (new View("item_edit"))->show(['info' => $info, 'errors' => $errors, 'ok' => $ok, "action" => $action]);
        }
    }


    public function add()
    {
        $user = $this->get_user_or_redirect();
        $action = "add";
        $errors[] = [];
        $errors['title'] = [];
        $errors['description'] = [];
        $errors['buy_now_price'] = [];
        $errors['starting_bid'] = [];
        $errors['saleprice'] = [];
        $errors['duration_days'] = [];
        $info['id'] = 0;
        (new View("item_edit"))->show(['info' => $info, 'errors' => $errors, 'action' => $action]);
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
        if(isset($_GET['param1']) && is_numeric($_GET['param1'])){
            $item_id=Tools::sanitize($_GET['param1']);
            $item=Item::get_item_by_id($item_id);
           
            if($item->owner!= $user->get_id()|| count($item->bids)!=0 || !$item->is_open_item() ){
                 throw new Exception("You can't delete this item ");
            }
            $owner = User::get_full_name_by_id($item->owner);
            (new View("delete_confirm"))->show(["owner" => $owner, "title" => $item->title, "id" => $item_id]);
        }
    }
    public function delete_item() : void{
        $user=$this->get_user_or_redirect();
        $item_id=Tools::sanitize($_GET['param1']);
        $item=Item::get_item_by_id($item_id);
       
        if($item->owner!= $user->get_id() || count($item->bids)!=0 || !$item->is_open_item()){
                 throw new Exception("You can't delete this item ");
         } else {
         $item->delete();   
         $this->redirect('item','list');
         }
    }


    public function manage_images(): void
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_GET['param1']) && $user->is_item_owner($_GET['param1']) ) {    // pas oublier de check aussi si l'objet appartient au user connecte et que l'item est actif
            $item_id = $_GET['param1'];
            $item= Item::get_item_by_id($item_id);
            if($item->is_open_item()) {
                if (isset($_FILES["image"]) && $_FILES["image"]["name"] != "") { // si on a upload une image
                    $image = $_FILES["image"];

                        // validation de l'image
                    $errors = ImageUtils::validate_image($image);
                    if (count($errors) == 0) { // si le fichier est valide on peut proceder a son upload
                        //verifier si un dossier existe dans uploads en regardant si l'item en question possede une image
                        if ($item->get_number_of_pictures() == 0)
                            mkdir("uploads/items/$item->id");
                        // redimensionner le fichier en deux images une en 1080p et une autre en 360px 
                        $formatted_images = $this->create_new_images($image);
                        // sauvegarde des nouvelles images dans le repertoire uploads
                        $new_image_path = $this->make_image_path($image, $item->id);
                        $this->save_new_images($formatted_images, $new_image_path);
                        // on l'insere dans la db
                        Item::upload_image($item->id, $new_image_path);
                        $this->redirect("item", "manage_images", "$item->id");
                    }
                }
            }
        }

        if (isset($_POST["left"])) { // si on a clique sur la fleche de gauche
            $this->swap_priority($item->id, $_POST["left"], -1);
        } else if (isset($_POST["right"])) { // si on a clique sur la fleche de droite
            $this->swap_priority($item->id, $_POST["right"], 1);
        } else if (isset($_POST["delete"])) {
            $this->delete_image($item->id, $_POST["delete"]);
            // si le dossier est vide, donc nb_pictures == 0, on le supprime
            if (Item::get_picture_count($item->id) == 0) 
                rmdir("uploads/items/$item->id");
        }

        // recherche des donnees a afficher sur la page (actualisee)
        $item_pictures = Picture::get_pictures($item->id);

        // si on requete post -> redirection vers GET sinon on affiche la page avec les erreurs
        if (count($_POST) != 0) 
            $this->redirect("item", "manage_images", "$item->id");
        (new View("manage_images"))->show(["user" => $user, "item" => $item, "item_pictures" => $item_pictures, "errors" => $errors]);
    }

    private function create_new_images(array $image): array
    {
        $new_images["full_image"] = ImageUtils::scale_image($image, 1080);
        $new_images["thumbnail"] = ImageUtils::scale_image($image, 360);
        return $new_images;
    }

    private function save_new_images(array $images, string $path) : void {
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

    private function swap_priority(int $item_id, int $old_priority, int $increment): void
    {
        $new_priority = $old_priority + $increment;
        Item::swap_priority($item_id, $old_priority, $new_priority);
    }

    private function delete_image(int $item_id, int $priority): void
    {
        $image_path = Item::get_picture_path($item_id, $priority);
        Item::delete_picture($item_id, $priority);
        ImageUtils::delete_image_files($image_path);
    }

    public  function sales()
    {
        $user = $this->get_user_or_redirect();
        $action = "sales";
        $salesItem = Item::get_user_sold_item($user->get_id());
        $info = Item::cal_total_avg_nbrOfSales($salesItem);
        $loyer_buyer = Item::find_loyer_bider($salesItem, "buyer");
        $nom = $this::concate_names($loyer_buyer);

        (new View("item_closed"))->show(["salesItem" => $salesItem, "info" => $info, "loyer_buyer" => $loyer_buyer, "nom" => $nom, 'user' => $user, 'action' => $action]);
    }

    public function purchases() : void {
        $user=$this->get_user_or_redirect();
         $action="purchases";
         $salesItem=Item::get_purchases_items($user);
         $info=Item::cal_total_avg_nbrOfSales($salesItem);
         $top_seller=Item::find_loyer_bider($salesItem,"pseudo");
         $nom=$this::concate_names($top_seller);
    
        (new View("item_closed"))->show(["salesItem"=>$salesItem,'info'=>$info,'loyer_buyer'=>$top_seller,"nom"=>$nom,'user'=>$user,'action'=>$action]);
        
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
