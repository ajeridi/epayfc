<?php

require_once  "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "framework/Tools.php";

class ControllerItem extends Controller{

    public function index() : void {
        $this->browse();
    }


    public function browse() : void {
        (new View("item_browse"))->show();
    }

}
?>