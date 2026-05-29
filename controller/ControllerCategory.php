<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerCategory extends Controller 
{

    public function index() : void 
    {
        $user = $this->get_user_or_redirect();
        $this->redirect("item");
    }

    public function manage() : void {
        $user = $this->get_user_or_redirect();

        if ($user->get_role() !== "admin"){
            $this->redirect("item");
        }
        (new View("manage_categories"))->show(["user" => $user]);
    }
}