<?php
require_once "framework/Model.php";
class User extends Model {
    public function __construct(public int $id, public string $pseudo,public string $hashed_password,public string $email,public string $full_name, public string $role,public ?string $picture_path= null, public ?string $iban=null,){

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
            return new User($data["id"], $data["pseudo"],$data["password"],$data["email"],$data["full_name"],$data["role"],$data["picture_path"], $data["iban"]);
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
     public static function get_full_name_by_id(int $user_id) : array  {
        $query=self::execute("SELECT* from users WHERE id = :id",["id"=> $user_id]);
        $data=$query->fetch();
        return $data;
    }
    public static function get_user_by_id(int $user_id): array {
        $query= self::execute("SELECT full_name , pseudo ,picture_path ,email FROM users where id=:id",["id"=>$user_id]);
        $data=$query->fetch();
        return $data; 
    }


    public function is_item_owner(int $item_id) : bool { //methode qui check si un user est l'owner d'un item
        $query = self::execute("SELECT id FROM items WHERE owner = :owner_id AND id = :item_id", ["owner_id" => $this->id, "item_id" => $item_id]);
        $res = $query->fetch();
        return is_bool($res) ? $res : count($res) > 0; // si res est un bool, c'est que res est vide
    }


    public function validate() : array {
        $errors = [];
        
        $errors["fullname"] = $this->validate_full_name();
        $errors["username"] = $this->validate_username();
        $errors["email"] = $this->validate_email();
        $errors["iban"] = $this->validate_iban();

        return $errors;
    }

    private function validate_full_name() : array {
        $errors = [];
        if (strlen($this->full_name) < 3){
            $errors[] = "Name must be at least 3 characters long.";
        }
        return $errors;
    }

    private function validate_username() : array {
        $errors = [];
        if (strlen($this->pseudo) < 3 || strlen($this->pseudo) > 30){
            $errors[] = "Username must be between 3 and 30 characters long.";
        }
        if (!User::is_unique_pseudo($this->pseudo)){
            $errors[] = "Username is not unique.";
        }
        return $errors;
    }

    private function validate_email() : array {
        $errors = [];
        if (!User::is_unique_email($this->email)){
            $errors[] = "Email is not unique";
        }
        return $errors;
    }

    public static function is_unique_pseudo(string $pseudo) : bool {
        return User::is_unique($pseudo, "pseudo");
    }

    public static function is_unique_email(string $email) : bool {
        return User::is_unique($email, "email");
    } 

    public static function is_unique(string $property, string $column) : bool {
        $sql = "SELECT id FROM users WHERE :column = :property";
        $query = self::execute($sql,["column" => $column, "property" => $property]);
        $res = $query->fetch();
        return $res == false;
    }

    private function validate_iban() : array {
        $errors = [];
        if (!preg_match('/^BE\d{2}(?: \d{4}){3}$/', $this->iban)){
            $errors[] = "IBAN format not valid.";
        }
        return $errors;
    }

}
?>
