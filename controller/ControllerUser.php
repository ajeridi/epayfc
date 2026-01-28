<?php
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'utils/ImageUtils.php';

class ControllerUser extends Controller
{

    public function index(): void
    {
        if ($this->user_logged()) {
            $this->redirect("item", "browse");
        } else {
            (new View("login"))->show();
        }
    }
    public function debug()
    {
        if ($_GET['param1'] == 1) {
            $email = "boverhaegen@epfc.eu";
        } elseif ($_GET['param1'] == 2) {
            $email = "mamichel@epfc.eu";
        } elseif ($_GET['param1'] == 3) {
            $email = "quhouben@epfc.eu";
        } else {
            $email ="xapigeolet@epfc.eu";
        }

        $this->log_user(User::get_user_by_email($email), "item", "browse");
    }

    public function login(): void
    {
        if (isset($_POST["logout"])) {
            $this->logout();
        } else {

            $email = '';
            $password = '';
            $errors = [];

            if (isset($_POST['email']) && isset($_POST['password'])) {

                $email = $_POST['email'];
                $password = $_POST['password'];
                $errors = User::validate_login($email, $password);

                if (empty($errors)) {
                    $this->log_user(User::get_user_by_email($email), "item", "browse");
                }
            }

            (new View("login"))->show(["email" => $email, "password" => $password, "errors" => $errors]);
        }
    }
    public function profile(): void
    {
        $user = $this->get_user_or_redirect();
        

        (new View('user_profile'))->show([ "user" => $user]);
    }

    public function edit_profile(): void
    {
        // check si user est connecte sinon redirect
        $user = $this->get_user_or_redirect();
        $errors = [];
        $newUser = null;
        // si on a submit via post 
        if (isset($_POST["saveProfile"])) {

            $fullname = "";
            $username = "";
            $email = "";
            $iban = "";

            if (isset($_POST["fullname"])) {
                $fullname = Tools::sanitize($_POST["fullname"]);
            }
            if (isset($_POST["username"])) {
                $username = Tools::sanitize($_POST["username"]);
            }
            if (isset($_POST["email"])) {
                $email = Tools::sanitize($_POST["email"]);
            }
            if (isset($_POST["iban"])) {
                $iban = Tools::sanitize($_POST["iban"]);
            }

            $newUser = new User($user->get_id(), $username, $user->get_hashed_password(), $email, $fullname, $user->get_role(), $user->get_picture_path(), $iban);
            $errors = $newUser->validate();
        }
        // si on a aucune erreur et que le newUser n'est pas null, on doit le sauvegarder dans la db et rediriger vers profile
        if ($this->has_no_errors($errors) && $newUser != null) {
            $newUser->save_user();
            $_SESSION["user"] = $newUser;
            $this->redirect("user", "profile");
        } else {
            (new View("edit_profile"))->show(["user" => $user, "newUser" => $newUser, "errors" => $errors]);
        }
    }

    private function has_no_errors(array $errors): bool
    {
        if (empty($errors)) {
            return true;
        }
        foreach ($errors as $error) {
            if (!empty($error)) {

                return false;
            }
        }
        return true;
    }
    public function manage_profile_picture()
    {

        $user = $this->get_user_or_redirect();
        $errors=[];
        $picture_path=null;

        if(isset($_FILES['image']) &&  $_FILES['image']['name'] !=""){

            $image=$_FILES['image'];
            $errors=ImageUtils::validate_image($image);

            if(count($errors)==0){

            $full_image = ImageUtils::scale_image($image, 100,101);
            $thumbnail = ImageUtils::scale_image($image, 100,101);
            $file_name = pathinfo($image["name"], PATHINFO_FILENAME);
            $user_id=$user->get_id();
            $picture_path = "uploads/users/$user_id/$file_name.jpg";
            ImageUtils::save_image_file($full_image, $picture_path);
            ImageUtils::save_image_file($thumbnail, ImageUtils::get_thumbnail_path($picture_path));

             $this::actualise_path($user,$picture_path);
            
            }

        }

        if(isset($_POST['delete'])){
             $this::actualise_path($user,$picture_path); 
        }
        (new View('manage_profile_picture'))->show(['user' => $user,'errors'=>$errors]);
    }

    public function change_password(): void
    {
        
        $user = $this->get_user_or_redirect();

        // si user sauvegarde en haut de la page
        $errors = [];

        if (isset($_POST["savePassword"])) {
            // verification du mot de passe actuel
            if (isset($_POST["currentPassword"])) {
                $currentPassword = Tools::sanitize($_POST["currentPassword"]);
                $errors["currentPassword"] = $user->checkPassword($currentPassword);
                //verification du nouveau mot de passe
                if ($this->has_no_errors($errors) && isset($_POST["newPassword"])) {
                    $newPassword = Tools::sanitize($_POST["newPassword"]);
                    $errors["newPassword"] = $user->validate_password($newPassword);
                    if ($this->has_no_errors($errors["newPassword"])) {
                        //confirmation 
                        if (isset($_POST["confirmPassword"])) {
                            $confirmPassword = Tools::sanitize($_POST["confirmPassword"]);
                            if ($confirmPassword !== $newPassword) {
                                $errors["newPassword"][] = "You have to enter twice the same password";
                            }
                        }
                    }
                    //sauvegarde du nouveau mot de passe
                    if ($this->has_no_errors($errors)) {
                        $user->save_new_password($newPassword);
                        $this->redirect("user", "profile");
                    }
                }
            }
        }

        (new View("change_password"))->show(["user" => $user, "errors" => $errors]);
    }
    private static function actualise_path($user,$picture_path) : void {

         if($user->get_picture_path() !== null){
            ImageUtils::delete_image_files($user->get_picture_path());
         }
             $user->set_picture_path($picture_path);
             $user->delete_picture(); 
           
    }
   
}
