<?php

require_once  "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "model/Item.php";
require_once "framework/Tools.php";
require_once "model/User.php";
require_once "utils/ImageUtils.php";


class ControllerItem extends Controller{

    public function index() : void {
        $this->browse();
    }
    public function items() : void {
        $this->redirect("item","list");
        
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

        $user=$this->get_user_or_redirect();
        $from="";
        if(isset($_GET["param2"])){
            $from=substr(stristr($_GET["param2"],"-"),1);
        }
        
    
        if(isset($_GET["param1"]) && is_numeric($_GET["param1"])){
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
         if(!$is_open && $user->pseudo != $item['pseudo'] && $user->pseudo !=$bids['0']['pseudo']){
             $this->index();
         }else{
        
        (new View("item_open"))->show(["item"=>$item,"pictures"=>$pictures,"bids"=>$bids,"is_open"=>$is_open,"user"=>$user,"from"=>$from]);
         }
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
        $item=Item::get_item_by_id($_GET['param1'],1);
        if(!isset($_GET['param1']) || !is_numeric($_GET['param1']) || $item['pseudo']!= $user->pseudo){
            $this->index();
            return;
        }

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
                $errors = ImageUtils::validate_image($image);
                if (count($errors) == 0){ // si le fichier est valide on peut proceder a son upload
                    // redimensionner le fichier en deux images une en 1080p et une autre en 360px 
                    $full_image = ImageUtils::scale_image($image, 1080);
                    $thumbnail = ImageUtils::scale_image($image, 360);

                    // sauvegarde des nouvelles images dans le repertoire uploads
                    $file_name = pathinfo($image["name"], PATHINFO_FILENAME);
                    $picture_path = "uploads/items/$item_id/$file_name.jpg";

                    ImageUtils::save_image_file($full_image, $picture_path);
                    ImageUtils::save_image_file($thumbnail, ImageUtils::get_thumbnail_path($picture_path));
                    
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

    private function delete_image(int $item_id, int $priority) : void {
            $image_path = Item::get_picture_path($item_id, $priority);
            Item::delete_picture($item_id, $priority);
            ImageUtils::delete_image_files($image_path);
    }
   
    public  function sales(){
        $user=$this->get_user_or_redirect();
        $salesItem=Item::get_salesItem_by_user($user->id);
        $info=Item::cal_total_avg_nbrOfSales($salesItem);
        $loyer_buyer=Item::find_loyer_bider($salesItem,"buyer");
        $nom=$this::concate_names($loyer_buyer);
     
        (new View("item_closed"))->show(["salesItem"=>$salesItem,"info"=>$info,"loyer_buyer"=>$loyer_buyer,"nom"=>$nom,'user'=>$user]);



    }
    public function purchases() : void {
        $user=$this->get_user_or_redirect();
         $salesItem=Item::get_purchases_item($user->id);
         $info=Item::cal_total_avg_nbrOfSales($salesItem);
         $top_seller=Item::find_loyer_bider($salesItem,"pseudo");
         $nom=$this::concate_names($top_seller);
    
        (new View("item_closed"))->show(["salesItem"=>$salesItem,'info'=>$info,'loyer_buyer'=>$top_seller,"nom"=>$nom,'user'=>$user]);
        
    }
    private function concate_names(array $loyer_buyer): string{
            $nom="" ;
            foreach($loyer_buyer as $buyer){
            $nom = ($nom == "" ? $buyer : $nom." et ".$buyer);
            }
            return $nom; 


    }
}
?>