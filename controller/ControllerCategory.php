<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once 'model/Category.php';

class ControllerCategory extends Controller 
{

    public function index() : void 
    {
        $user = $this->get_user_or_redirect();
        $this->redirect("item");
    }

    public function manage() : void {
        $user = $this->get_user_or_redirect();
        $categories = [];

        if ($user->get_role() !== "admin"){
            $this->redirect("item");
        }

        $categories = Category::get_categories_ordered_by_priority();        
        (new View("manage_categories"))->show(["user" => $user, "categories" => $categories]);
    }
}