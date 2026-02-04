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
        
        if ($user != false){ // si $user == false nous sommes en mode invité
            $items_participated_in = Item::get_items_participatedIn($user->get_id());
            $other_available_items = Item::get_other_available_item($user->get_id());
           
        }
        else {
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
            $pictures=Item::get_other_pictures_by_id($_GET["param1"]);
            
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

        if(!$first) {
            if($item!=null){
                $info['id']=$item->id;
            } else {
                 $info['id']=0;
            }
            $info['title'] =Tools::sanitize($_POST['title']);
            $info['description'] =Tools::sanitize($_POST['description']);
            $info['duration_days'] = Tools::sanitize($_POST['duration_days']);
            $info['starting_bid'] =  Tools::sanitize($_POST['starting_bid']);
            $info['buy_now_price'] = Tools::sanitize($_POST['buy_now_price']);
            $info['saleprice'] = Tools::sanitize($_POST['saleprice']);
        } else {
            $info['id']=$item->id;
            $info['title'] =$item->title;
            $info['description'] =$item->description;
            $info['duration_days'] =$item->duration_days;
            $info['starting_bid'] =  $item->starting_bid;
            if($item->starting_bid != null){
                $info['buy_now_price'] = $item->buy_now_price;
            }else {
                $info['saleprice'] = $item->buy_now_price;
            }
        
        }
        return $info ;
    }

    public function edit() : void 
    {
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

            (new View('item_edit'))->show(['info'=>$info,'errors'=>$errors,'action'=>'edit']);
        } else {

            $info=$this::build_info_array($item,false);
            $errors = $this::build_errors_array();
            $errors=Item::validat_item($user->get_id(), $info,$errors);
              if (count($errors['title']) == 0 && count($errors['description']) == 0 &&  count($errors['duration_days'])==0
               && count($errors['saleprice']) == 0 && count($errors['buy_now_price']) == 0 && count($errors['starting_bid']) == 0){
                Item::put_edition_item($info['id'], $user->get_id(), $info);
                 $this->redirect("item", "open",$info['id'], "from-items");
               } else {
                (new View("item_edit"))->show(['info' => $info, 'errors' => $errors, "action"=>"edit"]);
               }      
        }
        
    }
    public function add()
    {
        $user = $this->get_user_or_redirect();
        $action="add";
        $info['id']=0;
        $errors=$this::build_errors_array();
        if(!$_POST){
            $info=[];
            (new View("item_edit"))->show(['info' => $info['id'], 'errors' => $errors,'action'=>$action]);
        } else{
            $info=$this::build_info_array(null,false);
            $errors=Item::validat_item($user->get_id(),$info,$errors);
            if (count($errors['title']) == 0 && count($errors['description']) == 0 &&  count($errors['duration_days'])==0
               && count($errors['saleprice']) == 0 && count($errors['buy_now_price']) == 0 && count($errors['starting_bid']) == 0){
                $info['id']=Item::put_edition_item($info['id'], $user->get_id(), $info);
                 $this->redirect("item", "open",$info['id'], "from-items");
               } else {
                (new View("item_edit"))->show(['info' => $info, 'errors' => $errors, "action"=>"edit"]);
               }      
        }   
    }



    public function list(): void
    {
        $user = $this->get_user_or_redirect();
        $active_items = Item::get_user_active_item($user->get_id());
        $closed_unsold_items = Item::get_user_closed_unsold_item($user->get_id());
        $sold_items = Item::get_user_sold_item($user->get_id());
        (new View("item_list"))->show(["active_items" => $active_items, "closed_unsold_items" => $closed_unsold_items, "sold_items" => $sold_items, "user"=>$user]);
    }
    public function delete() : void {
        $user = $this->get_user_or_redirect();
        if(isset($_GET['param1']) && is_numeric($_GET['param1'])){
            $item_id=Tools::sanitize($_GET['param1']);
            $item=Item::get_item_by_id($item_id);
           
            if($item->owner!= $user->get_id()|| count($item->bids)!=0 || !$item->is_open_item() ){
                 throw new Exception("You can't delete this item ");
            }
            $owner=User::get_full_name_by_id($item->owner);
        (new View("delete_confirm"))->show(["owner"=>$owner,"title"=>$item->title,"id"=>$item_id]);
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
                        if (Item::get_picture_count($item_id) == 0) {
                            mkdir("uploads/items/$item_id");
                        }

                        // redimensionner le fichier en deux images une en 1080p et une autre en 360px 
                        $full_image = ImageUtils::scale_image($image, 1080);
                        $thumbnail = ImageUtils::scale_image($image, 360);

                        // sauvegarde des nouvelles images dans le repertoire uploads
                        $file_name = pathinfo($image["name"], PATHINFO_FILENAME);
                        $unique_file_name = uniqid($file_name); // creation d'un nom unique pour eviter d'avoir deux images avec le meme path dans la DB
                        $picture_path = "uploads/items/$item_id/$unique_file_name.jpg";

                        ImageUtils::save_image_file($full_image, $picture_path);
                        ImageUtils::save_image_file($thumbnail, ImageUtils::get_thumbnail_path($picture_path));

                        // on l'insere dans la db
                        Item::upload_image($item_id, $picture_path);
                    }
                }

                if (isset($_POST["left"])) { // si on a clique sur la fleche de gauche
                    $this->swap_priority($item_id, $_POST["left"], -1);
                } else if (isset($_POST["right"])) { // si on a clique sur la fleche de droite
                    $this->swap_priority($item_id, $_POST["right"], 1);
                } else if (isset($_POST["delete"])) {
                    $this->delete_image($item_id, $_POST["delete"]);
                    // si le dossier est vide, donc nb_pictures == 0, on le supprime
                    if (Item::get_picture_count($item_id) == 0) {
                        rmdir("uploads/items/$item_id");
                    }
                }


                // recherche des donnees a afficher sur la page
                $item_pictures = Item::get_other_pictures_by_id($item_id);
                $item_title = Item::get_item_title($item_id);

                // si on requete post -> redirection vers GET sinon on affiche la page avec les erreurs
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && count($errors) == 0) {
                    $this->redirect("item", "manage_images", "$item_id");
                }
                (new View("manage_images"))->show(["user" => $user, "item_id" => $item_id, "item_pictures" => $item_pictures, "item_title" => $item_title, "errors" => $errors]);
            }else{
                  $this->redirect("item");
            }
        }
        else {
            $this->redirect("item");
        }
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
   
    public  function sales(){
        $user=$this->get_user_or_redirect();
        $action="sales";
        $salesItem=Item::get_user_sold_item($user->get_id());
        $info=Item::cal_total_avg_nbrOfSales($salesItem);
        $loyer_buyer=Item::find_loyer_bider($salesItem,"buyer");
        $nom=$this::concate_names($loyer_buyer);
     
        (new View("item_closed"))->show(["salesItem"=>$salesItem,"info"=>$info,"loyer_buyer"=>$loyer_buyer,"nom"=>$nom,'user'=>$user,'action'=>$action]);



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
