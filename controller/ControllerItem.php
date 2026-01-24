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
        $user = $this->get_user_or_false();
        if ($user != false){ // si $user == false nous sommes en mode invité
            $items_participated_in = Item::get_items_participated_in($user->id);
            $other_available_items = Item::get_other_available_items($user->id);
        }
        else {
            $items_participated_in = [];
            $other_available_items = Item::get_other_available_items(-1);    // aucun user a l'id -1
        }
        (new View("item_browse"))->show(["items_participated_in" => $items_participated_in, "other_available_items" => $other_available_items, "user"=>$user]);
    }
    public function open() : void {

        $user=$this->get_user_or_false();
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
         if(!$item){
            $item=Item::get_item($_GET["param1"]);
            $item['picture_path']="";
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
        if(count($_POST)){
            $ok=true; 
        $user=$this->get_user_or_redirect();
        $info=[];
        $info['id']=$_GET['param1'];
        $title=Tools::sanitize($_POST['title']);
        $info['title']=$title;
        $description=Tools::sanitize($_POST['description']);
        $info['description']=$description;
        $duration_days=Tools::sanitize($_POST['duration_days']);
        $info['duration_days']=$duration_days;
        $starting_bid=Tools::sanitize($_POST['starting_bid']);
        $info['starting_bid']=$starting_bid;
        $buy_now_price=Tools::sanitize($_POST['buy_now_price']);
        $info['buy_now_price']=$buy_now_price;
        $saleprice=Tools::sanitize($_POST['saleprice']);
        $info['saleprice']=$saleprice;
        
        
        $errors=Item::validat_item($user->id,$title,$description,$duration_days,$buy_now_price,$starting_bid,$saleprice);
        if(count($errors['title'])==0 && count($errors['description'])==0 && count($errors['buy_now_price'])==0 && count($errors['starting_bid'])==0){
            $id=$_GET['param1'];
            $info['id']=$id;
            $id_nv=Item::put_edition_item($id,$user->id,$title,$description,$duration_days,$buy_now_price,$starting_bid,$saleprice);
            $this->redirect("item","open",$id_nv,"from-items");
        } else{
             
             (new View("item_edit"))->show(['info'=>$info,'errors'=>$errors,'ok'=>$ok ]);
        }

        } else {
        $ok=false;
        $user=$this->get_user_or_redirect();
        $item_id=$_GET['param1'];
        $info= Item::get_item_ino($item_id);
        if(Item::is_Direct_sale($item_id)){
            $info['starting_bid']=null;
            $info['saleprice']=$info['buy_now_price'];
            $info['buy_now_price']=null;
        }else{
            $info['saleprice']=null ;
        }
        $errors[]=[];
        $errors['title']=[];
        $errors['description']=[];
        $errors['buy_now_price']=[];
        $errors['starting_bid']=[];
        $errors['saleprice']=[];

        (new View("item_edit"))->show(['info'=>$info,'errors'=>$errors,'ok'=>$ok]);
    }     
    }
    public function add(){
        $user=$this->get_user_or_redirect();
        $errors[]=[];
        $errors['title']=[];
        $errors['description']=[];
        $errors['buy_now_price']=[];
        $errors['starting_bid']=[];
        $errors['saleprice']=[];
        $info['id']=0;
        (new View("item_edit"))-> show(['info'=>$info,'errors'=>$errors]);

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
                    // redimensionner le fichier en deux images une en 1080p et une autre en 360px 
                    $full_image = $this->scale_image($image, 1080);
                    $thumbnail = $this->scale_image($image, 360);

                    // sauvegarde des nouvelles images dans le repertoire uploads
                    $file_name = pathinfo($image["name"], PATHINFO_FILENAME);
                    $picture_path = "uploads/items/$item_id/$file_name";
                    imagejpeg($full_image, "$picture_path.jpg");
                    imagejpeg($thumbnail, $picture_path."_thumbnail.jpg");
                    
                    // on l'insere dans la db
                    Item::upload_image($item_id, $picture_path.".jpg");
                }
            }
            if (isset($_POST["left"])){ // si on a clique sur la fleche de gauche
                $this->swap_priority($item_id, $_POST["left"], -1);
            }
            else if (isset($_POST["right"])){ // si on a clique sur la fleche de droite
                $this->swap_priority($item_id, $_POST["right"], 1);
            }
            else if (isset($_POST["delete"])){
                $this->delete_image($item_id, $_POST["delete"]);
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

    private function scale_image(array $image, float $width) : GdImage { // methode qui change la resolution d'une image
        $converted_image = $this->convert_to_GDImage($image);
        return imagescale($converted_image, $width);
    }

    private function convert_to_GDImage(array $image) : GdImage | false { // methode qui converti un fichier $_FILES["image] en GDImage
        $image_type = $image["type"];
        $image_name = $image["tmp_name"];
        switch ($image_type) {
            case "image/png" :
                return imagecreatefrompng($image_name);
            case "image/gif" :
                return imagecreatefromgif($image_name);
            case "image/jpeg" :
                return imagecreatefromjpeg($image_name);
            case "image/webp" :
                return imagecreatefromwebp($image_name);
        }
        return false;
    }

    private function get_thumbnail_path(string $path) : string { // prends le path de la photo principale et renvoie le path du thumbnail
        $split_path = pathinfo($path);
        return $split_path['dirname'].'/'.$split_path['filename'].'_thumbnail'.".".$split_path['extension'];
    }

    private function delete_image(int $item_id, int $priority) : void {
            $image_path = Item::get_picture_path($item_id, $priority);
            Item::delete_picture($item_id, $priority);
            unlink($image_path);
            unlink($this->get_thumbnail_path($image_path));
    }
   
    public  function sales(){
        $user=$this->get_user_or_redirect();
        $salesItem=Item::get_salesItem_by_user($user->id);
     
        (new View("item_closed"))->show(["salesItem"=>$salesItem]);



    }
}
?>