<?php
require_once "framework/Model.php";
class User extends Model {
    public function __construct(public string $pseudo,public string $hashed_password,public string $email,public string $full_name, public string $role,public ?string $picture_path= null){

    }
    private static function check_password(string $clear_password, string $hash) : bool {
        return password_verify($clear_password,$hash);
    }
    public static function get_user_by_email(string $email): User | false{
        $query =self::execute("SELECT * FROM users where email= :email",["email"=>$email]);
        $data=$query->fetch();
        if($query->rowCount()==0){
            return false ; 
        }else{
            return new User($data["pseudo"],$data["password"],$data["email"],$data["full_name"],$data["role"],$data["picture_path"]);
        }
    }
    public static function validate_login(string $email, string $password) : array {
        $errors=[];
        $user=User::get_user_by_email($email);
        if($user){
            if(!self::check_password($password,$user->hashed_password)){
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[]="Can't find a member with the email '$email'.Please signe up";
        }
        return $errors;

    }
}
?>
