<?php
require_once "framework/Controller.php";

class ControllerTest extends Controller {
  
    public function index(): void {
      
      $this->redirect("user","login");  
    }
}