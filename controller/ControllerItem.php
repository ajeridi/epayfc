<?php

require_once  "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "model/Item.php";
require_once "framework/Tools.php";

class ControllerItem extends Controller{

    public function index() : void {
        $this->browse();
    }


    public function browse() : void {
        $items_participated_in = Item::get_items_participated_in(1);
        $other_available_items = Item::get_other_available_items(1);
        (new View("item_browse"))->show(["items_participated_in" => $items_participated_in, "other_available_items" => $other_available_items]);
    }

}
?>