<?php

require_once  "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "model/Item.php";
require_once "framework/Tools.php";
require_once "model/User.php";

class ControllerItem extends Controller{

    public function index() : void {
        $this->browse();
    }


    public function browse() : void {
        $user = $this->get_user_or_redirect();
        $items_participated_in = Item::get_items_participated_in($user->id);
        $other_available_items = Item::get_other_available_items($user->id);
        (new View("item_browse"))->show(["items_participated_in" => $items_participated_in, "other_available_items" => $other_available_items, "user"=>$user]);
    }
    public function open() : void {

        $user=$this->get_user_or_redirect();
        $from="";
        if(isset($_GET["param2"])){
            $from=$_GET["param2"];
        }
    
        if(isset($_GET["param1"])){
            $is_open=Item::is_open_anonce($_GET["param1"]);
            if(isset($_GET["param3"])){
                 $item =Item::get_item_by_id($_GET["param1"],$_GET["param3"]);
            }else{
                $item =Item::get_item_by_id($_GET["param1"],1);
        }
        $pictures=Item::get_other_pictures_by_id($_GET["param1"]);
        $bids=Item::get_bids($_GET["param1"]);
         } else{
            $this->index();
         }

        (new View("item_open"))->show(["item"=>$item,"pictures"=>$pictures,"bids"=>$bids,"is_open"=>$is_open,"user"=>$user,"from"=>$from]);
    }
    public function buy() : void {
        $user=$this->get_user_or_redirect();
        $now="2026-01-02 18:30:20";
        

        if(isset($_GET['param1'])  ){
            $item_id=$_GET['param1'];
            $item =Item::get_item_by_id($item_id,1);
            Item::buy($user->id,$_GET['param1'],"2026-01-02 18:30:20",$item['buy_now_price']);
              
        }
        $this->redirect("Item","open",$item_id);
        
    }
    public function bid() : void {
       $user=$this->get_user_or_redirect();
       $now="2026-01-02 18:30:20";
       if(isset($_GET['param1'])){
        $item_id=$_GET['param1'];
        $amount=$_POST['min_bid'];
        Item::buy($user->id,$item_id,$now,$amount);
       }
       $this->redirect("item","open",$item_id);
    }
    public function edit() : void {
        $user=$this->get_user_or_redirect();
        (new View("item_edit"))->show();
        

    }

    

    public function list() : void {
        $user = $this->get_user_or_redirect();
        $active_items = Item::get_user_active_items($user->id);
        $closed_unsold_items = Item::get_user_closed_unsold_items($user->id);
        $sold_items = Item::get_user_sold_items($user->id);
        (new View("item_list"))->show(["active_items" => $active_items, "closed_unsold_items" => $closed_unsold_items, "sold_items" => $sold_items, "user"=>$user]);
    }

    public function manage_images() : void{
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_GET['param1']) && $user->is_item_owner($_GET['param1'])){    // pas oublier de check aussi si l'objet appartient au user connecte
            $item_id = $_GET['param1'];
            if (isset($_FILES["image"]) && $_FILES["image"]["name"] != ""){// si on a upload une image
                $image = $_FILES["image"];                 
                
                // validation de l'image
                $errors = $this->validate_image($image);
                if (count($errors) == 0){ // si le fichier est valide on peut proceder a son upload
                    $file_name = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
                    $picture_path = "uploads/items/$item_id/$file_name.jpg";
                    // on upload le fichier dans le repertoire uploads
                    // il faut aussi redimensionner l'image et prendre le thumbnail
                    move_uploaded_file($_FILES["image"]["tmp_name"], $picture_path);
                    // on l'insere dans la db
                    Item::upload_image($item_id, $picture_path);
                }
            }
            if (isset($_POST["left"])){ // si on a clique sur la fleche de gauche
                $this->swap_priority($item_id, $_POST["left"], -1);
            }
            else if (isset($_POST["right"])){ // si on a clique sur la fleche de droite
                $this->swap_priority($item_id, $_POST["right"], 1);
            }

        
            $item_pictures = Item::get_other_pictures_by_id($item_id);
            $item_title = Item::get_item_title($item_id);
            // chercher l'item par son id, ainsi que toutes ses photos
            // si item existe on instancie la vue, sinon on ne fait rien (afficher un message d'erreur peut etre)
            (new View("manage_images"))->show(["user"=>$user, "item_id" => $item_id, "item_pictures" => $item_pictures, "item_title" => $item_title, "errors" => $errors]);    
        }
    }     

    private function swap_priority(int $item_id, int $old_priority, int $increment) : void {
        $new_priority = $old_priority + $increment;
        Item::swap_priority($item_id, $old_priority, $new_priority);
    }

    private function validate_image(array $image) : array{
        $max_size = 5 * 1024 * 1024; // 5MB
        $errors = [];
        if ($image["size"] > $max_size){
            $errors[] = "File size cannot exceed 5MB";
        }
        if (!in_array($image["type"], ["image/png", "image/gif", "image/jpeg", ".jpg", "image/webp"])){
            $errors[] = "File type must be .png, .gif, .jpg, .webp";
        }
        return $errors;
    }
}
?>