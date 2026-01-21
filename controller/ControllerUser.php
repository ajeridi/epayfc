<?php
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerUser extends Controller {
    public function index(): void
    {
        if($this->user_logged()){
            $this->redirect("item","browse");
        } else{
            (new View("login"))->show();
        }
    }
    public function login() : void {
        $email='';
        $password='';
        $errors=[];
        if(isset($_POST['email']) && isset($_POST['password'])){
            $email=$_POST['email'];
            $password=$_POST['password'];
            $errors=User::validate_login($email,$password);
            if(empty($errors)){
                $this->log_user(User::get_user_by_email($email),"item","browse");
            }
        }
        (new View("login"))->show(["email" => $email,"password" =>$password,"errors" => $errors]);
    }
    public function profile() : void {
        $user= $this->get_user_or_redirect();
        $info=User::get_user_by_id($user->id);
        (new View('user_profile'))->show(["info"=>$info]);
        
    }
}
?>