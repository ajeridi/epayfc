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
        (new View("item_browse"))->show(["items_participated_in" => $items_participated_in, "other_available_items" => $other_available_items]);
    }
    public function open() : void {
        if(isset($_GET["param"])){

            $item =Item::get_item_by_id($_GET["param"]);
        }
        (new View("item_open"))->show(["item"=>$item]);
    }

}
?>