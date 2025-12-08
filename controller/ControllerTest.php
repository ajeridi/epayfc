<?php
require_once "framework/Controller.php";

class ControllerTest extends Controller {
  
    public function index(): void {
      $errors=null ;
      $this->redirect("user","login");  
    }
}