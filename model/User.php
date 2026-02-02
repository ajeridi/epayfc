<?php
require_once "framework/Model.php";
class User extends Model {
    public function __construct(private int $id, private string $pseudo,private string $hashed_password,private string $email,private string $full_name, private string $role,private ?string $picture_path = null, private ?string $iban=null){}

    public function get_id(): int {
        return $this->id ;
    }

    public function get_full_name(){
        return $this->full_name;
    }

    public function get_email(){
        return $this->email;
    }

    public function get_pseudo(){
        return $this->pseudo;
    }

    public function get_password(){
        return $this->hashed_password;
    }

    public function get_role(){
        return $this->role;
    }

    public function get_picture_path(){
        return $this->picture_path; 
    }
    
    public function get_iban(){
        return $this->iban;
    }

    public function set_picture_path($picture_path) : void {
        $this->picture_path=$picture_path;     
    }

    
    private static function check_password(string $clear_password, string $hash) : bool {
        return password_verify($clear_password,$hash);
    }

    public function checkPassword(string $entered_password) : array {
        $errors = [];
        if (!$this->check_password($entered_password, $this->hashed_password)){
            $errors[] = "Password incorrect.";
        }
        return $errors;
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
     public static function get_full_name_by_id(int $user_id) : String {
        $query=self::execute("SELECT full_name from users WHERE id = :id",["id"=> $user_id]);
        $data=$query->fetch();
        return $data['full_name'];
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
        if (strlen($this->full_name) < Configuration::get("name_min_length")){
            $errors[] = "Full name must be at least 3 characters long.";
        }
        if (!$this->is_unique_full_name($this->full_name)){
            $errors[] = "Full name is not unique.";
        }
        return $errors;
    }

    private function validate_username() : array {
        $errors = [];
        if (strlen($this->pseudo) < Configuration::get("name_min_length") || strlen($this->pseudo) > Configuration::get("name_max_length")){
            $errors[] = "Username must be between 3 and 30 characters long.";
        }
        if (!$this->is_unique_pseudo($this->pseudo)){
            $errors[] = "Username is not unique.";
        }
        return $errors;
    }

    private function validate_email() : array {
        $errors = [];
        if (!$this->is_unique_email()){
            $errors[] = "Email is not unique";
        }
        return $errors;
    }

    public function is_unique_pseudo() : bool {
        return $this->is_unique($this->pseudo, "pseudo");
    }

    public function is_unique_email() : bool {
        return $this->is_unique($this->email, "email");
    } 

public function is_unique_full_name() : bool {
        return $this->is_unique($this->full_name, "full_name");
    } 

    private function is_unique(string $property, string $column) : bool {
        $sql = "SELECT id FROM users WHERE $column = :property";
        $query = self::execute($sql,["property" => $property]);
        $res = $query->fetch();
        // c'est unique lorsqu'aucun id existe avec cette propriete ou bien si l'id est le meme que le user
        return $res == false || $res["id"] == $this->id;
    }

    private function validate_iban() : array {
        $errors = [];
        if ($this->iban != "" && !preg_match('/^BE\d{2}(?: \d{4}){3}$/', $this->iban)){
            $errors[] = "IBAN format not valid.";
        }
        return $errors;
    }

    public function save_user() : void {
        $sql = "UPDATE users SET 
                    full_name = :full_name,
                    email = :email,
                    pseudo = :pseudo,
                    iban = :iban
                WHERE id = :id;";
        $query = self::execute($sql, ["full_name" => $this->full_name, "pseudo" => $this->pseudo, "email" => $this->email, "iban" => $this->iban, "id" => $this->id]);
    }
   
    public function delete_picture(): void{
        self::execute("UPDATE users SET picture_path = :picture_path where id=:id",['id'=>$this->id,'picture_path'=>$this->picture_path]);
    }

    public function validate_password(string $newPasssword) : array {
        $errors = [];
        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9])[\x20-\x7E]{8,16}$/";
        if (!preg_match($regex, $newPasssword)){
            $errors[] = "Password not valid.";
        }
        return $errors;
    }

    public static function confirm_password(string $password, string $confirmPassword) : array{
        $error = [];
        if ($password !== $confirmPassword){
            $error[] = "You have to enter twice the same password.";
        }
        return $error;
    }

    public function save_new_password(string $newPasssword) : void {
        $hashed_new_password = password_hash($newPasssword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = :new_password WHERE id = :user_id";
        $query = self::execute($sql, ["new_password" => $hashed_new_password, "user_id" => $this->id]);
        $this->hashed_password = $hashed_new_password;
    }

    public function insert_new_user() : void {
        $sql = "INSERT INTO users(full_name, email, pseudo, password, role) VALUES(:fullname, :email, :pseudo, :password, :role)";
        $query = self::execute($sql, ["fullname" => $this->full_name, "email" => $this->email, "pseudo" => $this->pseudo, "password" => $this->hashed_password, "role" => $this->role]);
    }
    public static function get_pseudo_by_id(int $id): string {
        $query=self::execute("SELECT u.pseudo FROM users u WHERE u.id = :id ",["id"=>$id]);
        $data=$query->fetch();
        return $data['pseudo'];
    }
    public static function get_path_picture(int $id) :  string {
         $query=self::execute("SELECT u.picture_path FROM users u WHERE u.id = :id ",["id"=>$id]);
        $data=$query->fetch();
        if($data['picture_path'] == null){
            return "";
        }
        return $data['picture_path'];
    }
    public static function get_role_by_id(int $id) : string {
         $query=self::execute("SELECT u.role FROM users u WHERE u.id = :id ",["id"=>$id]);
        $data=$query->fetch();
        return $data['role'];
    }

    public function has_profile_picture() : bool{
        $sql = "SELECT picture_path FROM users WHERE id = :user_id";
        $query = self::execute($sql, ["user_id" => $this->id]);
        return $query->fetchColumn() != null;
    }
}
?>
