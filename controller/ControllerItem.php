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

}
?>