<?php
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once 'model/Category.php';

class ControllerCategory extends Controller
{

    public function index(): void
    {
        $user = $this->get_user_or_redirect();
        $this->redirect("item");
    }

    public function manage(): void
    {
        $user = $this->get_user_or_redirect();
        $categories = [];
        $errors = [];


        if ($user->get_role() !== "admin") {
            $this->redirect("item");
        }

        // on regarde si on est arrive via post ($_POST est un array, donc vide si pas de requete post)
        if (!empty($_POST)) {
            if (isset($_POST['up'])) { // si on appuye sur le bouton up
                $category_id = $_POST['up'];
                $this->move_up($category_id); // update dans la db
                $this->redirect("category", "manage"); // redirect (PRG);
            } else if (isset($_POST['down'])) { // si on appuye sur le bouton down
                $category_id = $_POST['down'];
                $this->move_down($category_id); // update dans la db
                $this->redirect("category", "manage"); // redirect (PRG);
            } else if (isset($_POST['save'])) { // si on appuye sur le bouton save / ou add, meme mecanisme
                $id = $_POST['save'];
                $new_name = Tools::sanitize($_POST['category_' . $id]);
                $errors["category_" . $id] = $this->validate_name($id, $new_name);
                if ($errors["category_" . $id] == null) { // si pas d'erreur, persist et redirection
                    $this->save_name($id, $new_name);
                    $this->redirect("category", "manage");
                }
            } else if (isset($_POST["delete"])) { //si on appuye sur le bouton delete
                $category = Category::get_category_by_id($_POST['delete']);
                (new View('delete_category'))->show(["category" => $category]);
                return;
            }
        }

        $categories = Category::get_categories_ordered_by_priority();
        (new View("manage_categories"))->show(["user" => $user, "categories" => $categories, "errors" => $errors, "post" => $_POST]);
    }

    private function move_up(int $id)
    {
        $category = Category::get_category_by_id($id);
        $category->move_up();
    }

    private function move_down(int $id)
    {
        $category = Category::get_category_by_id($id);
        $category->move_down();
    }

    private function validate_name(int $id, string $new_name): string | null
    {
        if ($id == 0) $category = new Category(0, '', 0); // si id = 0, nouvelle categorie 
        else $category = Category::get_category_by_id($id); // sinon on va chercher la categorie existante
        $error = $category->validate_name($new_name);

        return $error;
    }

    private function save_name(int $id, string $new_name)
    {
        if ($id == 0) $category = new Category($id, $new_name, 0);
        else {
            $category = Category::get_category_by_id($id);
            $category->set_name($new_name);
        }
        $category->persist();
    }

    public function delete(): void
    {
        $user = $this->get_user_or_redirect();
        if ($user->get_role() !== "admin") $this->redirect("item");
        if (isset($_GET['param1']) && is_numeric($_GET["param1"])) {
            $category = Category::get_category_by_id($_GET['param1']);
            $category->delete();
            $this->redirect("category", "manage");
        } else {
            $this->redirect("category", "manage");
        }
    }



    // SERVICES 
    public function update_priorities_service(): void
    {
        $user = $this->get_user_or_redirect();

        header("Content-Type: application/json");

        if ($user->get_role() !== "admin") {
            http_response_code(403);
            echo json_encode(["success" => false, "error" => "Forbidden"]);
            return;
        }

        if (!isset($_POST["order"]) || !is_array($_POST["order"])) {
            echo json_encode(["success" => false, "error" => "Invalid order"]);
            return;
        }

        Category::update_priorities($_POST["order"]);

        echo json_encode(["success" => true]);
    }

    public function validate_name_service(): void
    {
        $user = $this->get_user_or_redirect();

        header("Content-Type: application/json");

        if ($user->get_role() !== "admin") {
            http_response_code(403);
            echo json_encode([
                "valid" => false,
                "error" => "Forbidden"
            ]);
            return;
        }

        $id = intval($_POST["id"] ?? 0);
        $name = Tools::sanitize($_POST["name"] ?? "");

        $error = $this->validate_name($id, $name);

        echo json_encode([
            "valid" => $error === null,
            "error" => $error
        ]);
    }

    public function save_name_service(): void
    {
        $user = $this->get_user_or_redirect();

        header("Content-Type: application/json");

        if ($user->get_role() !== "admin") {
            http_response_code(403);
            echo json_encode([
                "success" => false,
                "error" => "Forbidden"
            ]);
            return;
        }

        $id = intval($_POST["id"] ?? 0);
        $name = Tools::sanitize($_POST["name"] ?? "");

        $error = $this->validate_name($id, $name);

        if ($error !== null) {
            echo json_encode([
                "success" => false,
                "error" => $error
            ]);
            return;
        }

        $this->save_name($id, $name);

        echo json_encode([
            "success" => true,
            "name" => $name
        ]);
    }

    public function delete_service(): void
    {
        $user = $this->get_user_or_redirect();

        header("Content-Type: application/json");

        if ($user->get_role() !== "admin") {
            http_response_code(403);
            echo json_encode([
                "success" => false,
                "error" => "Forbidden"
            ]);
            return;
        }

        $id = intval($_POST["id"] ?? 0);

        if ($id <= 0) {
            echo json_encode([
                "success" => false,
                "error" => "Invalid category"
            ]);
            return;
        }

        $category = Category::get_category_by_id($id);

        if (!$category->is_empty()) {
            echo json_encode([
                "success" => false,
                "error" => "Category is not empty"
            ]);
            return;
        }

        $category->delete();

        echo json_encode([
            "success" => true
        ]);
    }
}
