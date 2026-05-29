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

        // on regarde si on est arrive via post ($_POST est un array, donc vide si pas de requete post)
        if (!empty($_POST)){
            if (isset($_POST['up'])){ // si on appuye sur le bouton up
                $category_id = $_POST['up'];
                $this->move_up($category_id); // update dans la db
                $this->redirect("category", "manage"); // redirect (PRG);
            }
           else if (isset($_POST['down'])){ // si on appuye sur le bouton up
                $category_id = $_POST['down'];
                $this->move_down($category_id); // update dans la db
                $this->redirect("category", "manage"); // redirect (PRG);
            } 
        }

        $categories = Category::get_categories_ordered_by_priority();        
        (new View("manage_categories"))->show(["user" => $user, "categories" => $categories]);
    }

    private function move_up(int $id){
        $category = Category::get_category_by_id($id);
        $category->move_up();
    }
    
    private function move_down(int $id){
        $category = Category::get_category_by_id($id);
        $category->move_down();
    }
}