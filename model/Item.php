<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Item extends Model {
     
    public function __construct(public int $id ,public string $title,public ?string $description,public int $owner,public string $created_at,public ?int $buy_now_price,public ? int $starting_bid ,public string $picture_path ){
    }

    private static $items_participated_in_sql =  "SELECT DISTINCT vit.*, owners.full_name owner_full_name, b.amount, 
   (SELECT ip.picture_path from item_pictures ip WHERE ip.priority = 1 AND ip.item = vit.id) as picture_path, (SELECT COUNT(*) FROM item_pictures ip2 WHERE ip2.item = vit.id) as number_of_pictures, 
    CONCAT(TIMESTAMPDIFF(DAY, :current_date_time, vit.end_at), 'd ', TIMESTAMPDIFF(HOUR, :current_date_time, vit.end_at) % 24, 'h') as time_left
                                FROM v_items_status vit 
                                JOIN users as owners ON vit.owner = owners.id
                                JOIN bids b ON b.item = vit.id
                                WHERE 
                                b.amount= (SELECT MAX(b2.amount) FROM bids b2 WHERE b2.owner = :user_id AND b2.item = b.item) AND
                                vit.buy_now_reached = 0 AND
                                b.owner = :user_id AND
                                vit.end_at > :current_date_time
                                ORDER BY vit.end_at";


    public static function get_items_participated_in(int $user_id) : array{
       $current_date_time =AppTime::get_current_datetime();
        $query = self::execute(Item::$items_participated_in_sql, ["user_id" => $user_id, "current_date_time" => $current_date_time]);
        return $query->fetchAll();
    } 

    public static function get_other_available_items(int $user_id) : array{
        $current_date_time =AppTime::get_current_datetime();
        $sql = " SELECT DISTINCT vit.*, 
                owners.full_name owner_full_name, 
                (SELECT ip.picture_path from item_pictures ip WHERE ip.priority = 1 AND ip.item = vit.id) as picture_path,
                (SELECT COUNT(*)
                FROM item_pictures ip2
                WHERE ip2.item = vit.id) as number_of_pictures,
                CONCAT(TIMESTAMPDIFF(DAY, :current_date_time, vit.end_at), 'd ', TIMESTAMPDIFF(HOUR, :current_date_time, vit.end_at) % 24, 'h') as time_left
            FROM v_items_status vit 
            JOIN users as owners ON vit.owner = owners.id
            WHERE vit.buy_now_reached = 0
            AND vit.end_at > :current_date_time
            AND owners.id != :user_id
            AND vit.id NOT IN (SELECT bids.item
                                FROM bids
                                WHERE bids.owner = :user_id)
            ORDER BY vit.end_at";
       $query = self::execute($sql, ["user_id" => $user_id, "current_date_time" => $current_date_time]); 
       return $query->fetchAll();
    }


    public static function get_user_active_items(int $user_id) : array{
        $current_date_time =AppTime::get_current_datetime();
        

        $sql = "SELECT vit.id, vit.title, owners.full_name, vit.starting_bid, vit.max_bid, vit.is_auction, vit.is_direct_sale, vit.buy_now_price, vit.has_buy_now, (SELECT COUNT(*) FROM item_pictures ip WHERE ip.item = vit.id) as number_of_pictures, 
                   (SELECT ip.picture_path from item_pictures ip WHERE ip.priority = 1 AND ip.item = vit.id) as picture_path, 
                    CONCAT(TIMESTAMPDIFF(DAY, :current_date_time, vit.end_at), 'd ', TIMESTAMPDIFF(HOUR, :current_date_time, vit.end_at) % 24, 'h') as time_left
                FROM v_items_status vit
                JOIN users as owners on owners.id = vit.owner
                WHERE owners.id = :user_id AND 
                vit.buy_now_reached = 0 AND 
                vit.end_at > :current_date_time
                ORDER BY vit.end_at";

       $query = self::execute($sql, ["user_id" => $user_id, "current_date_time" => $current_date_time]); 
       return $query->fetchAll();

    }


    public static function get_user_closed_unsold_items(int $user_id) : array{
        $current_date_time =AppTime::get_current_datetime();
        

        $sql = "SELECT vit.id, vit.title, owners.full_name, vit.starting_bid, vit.max_bid, vit.is_auction, vit.is_direct_sale, vit.buy_now_price, vit.has_buy_now, (SELECT COUNT(*) FROM item_pictures ip WHERE ip.item = vit.id) as number_of_pictures, 
                (SELECT ip.picture_path from item_pictures ip WHERE ip.priority = 1 AND ip.item = vit.id) as picture_path
                FROM v_items_status vit
                JOIN users as owners on owners.id = vit.owner
                WHERE owners.id = :user_id AND 
                vit.bid_count = 0 AND 
                vit.end_at <= :current_date_time 
                ORDER BY vit.end_at";

       $query = self::execute($sql, ["user_id" => $user_id, "current_date_time" =>  $current_date_time]); 
       return $query->fetchAll();

    }

    
    public static function get_user_sold_items(int $user_id) : array{
        

        $sql = "SELECT vit.id, vit.title, owners.full_name, vit.starting_bid, vit.max_bid, vit.is_auction, vit.is_direct_sale, vit.buy_now_price, vit.has_buy_now, 
                (SELECT COUNT(*) FROM item_pictures ip WHERE ip.item = vit.id) as number_of_pictures, 
                (SELECT ip2.picture_path from item_pictures ip2 WHERE ip2.priority = 1 AND ip2.item = vit.id) as picture_path 
                FROM v_items_status vit
                JOIN users as owners on owners.id = vit.owner
                WHERE owners.id = :user_id AND 
                vit.buy_now_reached = 1
                ORDER BY vit.end_at";

       $query = self::execute($sql, ["user_id" => $user_id]); 
       return $query->fetchAll();

    }

    public static function get_item_by_id(int $item_id,int $priority) : array | bool {
        $query=self::execute("SELECT is_direct_sale,has_buy_now, v_items_status.id,title , description,v_items_status.created_at, end_at, bid_count,buy_now_price,max_bid,item_pictures.picture_path ,priority,u1.pseudo,u1.picture_path as user_path ,u1.role,starting_bid
                                 FROM  v_items_status join item_pictures on v_items_status.id=item_pictures.item 
                                  join users u1 on v_items_status.owner=u1.id
                                            WHERE v_items_status.id= :id
                                            and item_pictures.priority=:priority",["id"=>$item_id,"priority"=>$priority]);
        $data=$query->fetch();
       
            return $data;
        

    }
     public static function get_item(int $item_id) : array {
        $query=self::execute("SELECT is_direct_sale,has_buy_now, v_items_status.id,title , description,v_items_status.created_at, end_at, bid_count,buy_now_price,max_bid,u1.pseudo,u1.picture_path as user_path ,u1.role,starting_bid
                                 FROM  v_items_status 
                                  join users u1 on v_items_status.owner=u1.id
                                            WHERE v_items_status.id= :id
                                            ",["id"=>$item_id]);
        $data=$query->fetch();
    
       
            return $data;
        

    }
    public static function get_other_pictures_by_id(int $item_id): array {
        $query=self::execute("SELECT * FROM item_pictures WHERE item = :item ",["item"=>$item_id]);
        $data=$query->fetchAll();
        return $data;
    }
    public static function get_bids(int $item_id):array {
        $query=self::execute("SELECT owner ,pseudo , created_at ,amount  FROM bids join users on bids.owner=users.id WHERE item= :item ORDER BY -created_at",["item" =>$item_id]);
        $data=$query->fetchAll();
        return $data;
    }
       public static function is_open_anonce(int $item_id): bool {
         $current_date_time=AppTime::get_current_datetime();
        $query=self::execute("SELECT * FROM v_items_status Where id= :id and end_at > :current_date_time and ((is_auction = 1 and buy_now_reached = 0 ) or (is_direct_sale=1 and not_purchased_direct_sale=1))",["id"=>$item_id,"current_date_time"=> $current_date_time]);

        if($query->rowCount()==0){
            return false ;
        }else{
            return true; 
        }

    }
    public static function get_item_ino(int $item_id): array {
        $query=self::execute("SELECT id ,title,description ,duration_days,starting_bid,buy_now_price from items where id=:id",["id"=>$item_id]);
        $data=$query->fetch();
        return $data;
    }
    public static function buy($owner,$item_id,$at,$amount): void {
        $bids=item::get_item_by_id($item_id,1);
        if($amount>$bids['max_bid'] && $amount>=$bids['starting_bid']){
                 $query=self::execute("INSERT INTO bids(owner,item,created_at,amount) VALUES (:owner,:item,:created_at,:amount)",["owner"=>$owner,"item"=>$item_id,"created_at"=>$at,"amount"=>$amount]);
        }

    }
    public static function validat_item($owner,$title,$description,$duration_days,$buy_now_price,$starting_bid,$saleprice): array {
        
        $errors[]=[];
        $errors['title']=[];
        $errors['description']=[];
        $errors['buy_now_price']=[];
        $errors['starting_bid']=[];
        $errors['saleprice']=[];
 
       if(!Item::unicite_title_for_owner($owner,$title)){
        $errors['title'][]="This title already exist, Please choose another one !";
       }
        if(strlen($title)==0){
            $errors['title'][]="Title is required";
        }
        if(strlen($title)<3){
            $errors['title'][]="Title lenght must be between 3 and 255 characters";
            
        }
        if($description!=null && strlen($description)<3){
            $errors['description'][]="Description lenght must be betwenn 3 and 255 characters";
           
        }
        if($starting_bid != ""  &&  $starting_bid < 0){
            $errors["starting_bid"][]="Starting bid must be positif ";
            
        }
        if( $buy_now_price != "" &&  $buy_now_price <= 0 ){
            $errors['buy_now_price'][]="the buy now price must be positif";
            
        }
        if($starting_bid =="" && $saleprice==""){
            $errors["buy_now_price"][]="Type requiered";
             $errors["saleprice"][]="Type requiered";
        }
        if($starting_bid != "" && $buy_now_price!="" && $buy_now_price<$starting_bid ){
            $errors["buy_now_price"][]="the buy now price most be sup then the starting bid";
           
        }
        if($starting_bid != "" &&  $saleprice != ""){
            $errors["buy_now_price"][]="Cannot create both auction and direct sale";
            $errors["saleprice"][]="Cannot create both auction and direct sale";

            
        }
       
        return $errors;


    }
    private static function unicite_title_for_owner($owner ,$title): bool {
        $result=true ;
        $query=self::execute("SELECT title from items where owner=:owner",["owner"=>$owner]);
        $data=$query->fetchAll();
        foreach($data as $tit){
            if(Tools::sanitize($title)===$tit){
                $result=false ;
            }
        }
        return $result;


    }
    public static function put_edition_item($id,$owner,$title,$description,$duration_days,$buy_now_price,$starting_bid,$saleprice): int {
        $current_date_time=AppTime::get_current_datetime();
        if($buy_now_price == ""){
            if($starting_bid != "" && $starting_bid !=0){
                $buy_now_price=null;
            } else{
            $buy_now_price=$saleprice;
            }
        }
        if($id==0){
            self::execute("INSERT INTO items (owner,title,description,duration_days,buy_now_price,starting_bid,created_at)  VALUE (:owner,:title,:description,:duration_days,:buy_now_price,:starting_bid,:created_at) ",['owner'=>$owner,'title'=>$title,'description'=>$description,'duration_days'=>$duration_days,'starting_bid'=>$starting_bid,'buy_now_price'=>$buy_now_price,'created_at'=>$current_date_time]);
            $id_nv=self::lastInsertId();
            return $id_nv;
        } else{
            self::execute("UPDATE items SET title=:title,description=:description,buy_now_price=:buy_now_price,duration_days=:duration_days,starting_bid=:starting_bid WHERE id =:id ",['title'=>$title,'description'=>$description,'duration_days'=>$duration_days,'starting_bid'=>$starting_bid,'buy_now_price'=>$buy_now_price, 'id' => $id]);
            return $id;
        }
        

    }
    public static function is_Direct_sale(int $item_id): bool {
         $item=Item::get_item_by_id($item_id,1);

         if($item['is_direct_sale']==1){
            return true;
         }else{
            return false;
         }

    }

   
    public static function get_item_title(int $item_id) : string {
        $query = self::execute("SELECT title from items WHERE id = :item_id", 
        ["item_id" => $item_id]);
        $result = $query->fetch();
        return $result["title"];
    }

    public static function upload_image(int $item_id, string $picture_path) : void {
        // on regarde quelle est la priorite de la derniere image de l'item
        $priority = Item::get_new_picture_priority($item_id);
        $query = self::execute("INSERT INTO item_pictures(item, priority, picture_path) VALUES (:item_id, :priority, :picture_path)",
        ["item_id" => $item_id, "priority" => $priority ,"picture_path" => $picture_path]);
    }

    public static function get_new_picture_priority(int $item_id) : int {
        $sql = "SELECT (MAX(priority)+1) as next_priority FROM item_pictures WHERE item = :item_id";
        $query = self::execute($sql, ["item_id" => $item_id]);
        $res = $query->fetch();
        return $res["next_priority"] == null ? 1 : $res["next_priority"];
    }

    public static function swap_priority(int $item_id, int $old_priority, int $new_priority) : void {
        // Pour update deux champs sous contrainte, il faut faire un assignation avec une variable temp
        // si old = 1 et new = 2, old = -1, new = 1, old = 2, car sinon erreur
        Item::update_priority($item_id, $old_priority, -1);
        Item::update_priority($item_id, $new_priority, $old_priority);
        Item::update_priority($item_id, -1, $new_priority);
    }

    public static function update_priority(int $item_id, int $old_priority, int $new_priority) : void {
        $sql = "UPDATE item_pictures SET priority = :new_priority WHERE priority = :old_priority AND item = :item_id;";    
        $query = self::execute($sql,["new_priority" => $new_priority, "old_priority" => $old_priority, "item_id" => $item_id]);
    }

    public static function delete_picture(int $item_id, int $priority) : void {
        // d'abord supprimer l'image    
        $sql = "DELETE FROM item_pictures WHERE item = :item_id AND priority = :priority";
        $query = self::execute($sql, ["item_id" => $item_id, "priority" => $priority]);
        // ensuite il faut actualiser les priorites des images ayant une priorite > $priority, faire -1 a chaque priorite se trouvant apres
        $sql = "UPDATE item_pictures SET priority = (priority - 1) WHERE priority > :priority AND item = :item_id";
        $query = self::execute($sql, ["priority" => $priority, "item_id" => $item_id ]);
    }

    public static function get_picture_path(int $item_id, int $priority) : string {
        $sql = "SELECT picture_path FROM item_pictures WHERE item = :item_id AND priority = :priority";
        $query = self::execute($sql, ["item_id" => $item_id, "priority" => $priority]);
        $res = $query->fetch();
        return $res["picture_path"];
    }

    public static function get_salesItem_by_user(int $user_id): array {
        
        $current_date_time=AppTime::get_current_datetime();
        $query=self::execute("SELECT distinct v.id,v.title,v.owner,v.created_at,v.buy_now_price,v.end_at,v.bid_count,v.max_bid as amount,v.is_direct_sale,v.is_auction,v.starting_bid,u.pseudo,v.has_buy_now,count(ip.picture_path) as number_of_pictures,(SELECT picture_path FROM item_pictures WHERE item = v.id AND priority = 1) as picture_path,
                                (SELECT distinct u.pseudo  from  bids b  join users u on b.owner=u.id where b.item=v.id order by -b.created_at LIMIT 1) as buyer , (SELECT distinct  b.created_at  from  bids b  join users u on b.owner=u.id where b.item=v.id order by -b.created_at LIMIT 1) as created_at
                              from v_items_status v join users u on v.owner=u.id 
                                                            join item_pictures ip on ip.item=v.id
                                                            join bids b on b.item=v.id
                                       Where v.owner=:owner and ((v.is_auction=1 and v.end_at < :current_date) or
                                        (v.has_buy_now=1 and v.buy_now_reached=1))
                                        GROUP BY v.id,ip.item,v.has_buy_now,v.title,v.owner,b.amount,v.created_at,v.buy_now_price,v.end_at,v.is_direct_sale,v.bid_count,v.max_bid,v.is_auction,v.starting_bid,u.pseudo,buyer,created_at,picture_path",["owner"=>$user_id,"current_date"=> $current_date_time]);
       $data=$query->fetchAll();
       
       
       return $data ;
    }
    public static function cal_total_avg_nbrOfSales(array $salesItem) :  array{
        $total=0.0 ;
        $nbrOfSales=0;
        foreach($salesItem as $sale){
            $total=$total + $sale['amount'];
            $nbrOfSales=$nbrOfSales+1;
        }
        $info['total']=(double)$total;
        $info['avg']=$total/$nbrOfSales;
        $info['nbr']=$nbrOfSales;
        return $info;

    }
    public static function find_loyer_bider(array $buyer,String $indice): ? array {
        if(empty($buyer)){
            return null ;
        }
        $buyrArray=[];
        foreach($buyer  as $item) {
            $buyrArray[]=$item[$indice];

        }

        $count = array_count_values($buyrArray);
        $max = max($count);
        $loyer_bider=array_keys($count,$max,true);
        return $loyer_bider;
        


    }
    public static function get_purchases_item(int $user_id) : array {
        $current_date_time=AppTime::get_current_datetime();
        $query=self::execute(" SELECT distinct v.id,v.title,v.owner,v.created_at,v.buy_now_price,v.end_at,v.bid_count,v.max_bid as amount,v.is_direct_sale,v.is_auction,v.starting_bid,u.pseudo,v.has_buy_now,( Select count(*) from item_pictures where item_pictures.item=v.id) as number_of_pictures,(SELECT picture_path FROM item_pictures WHERE item = v.id AND priority = 1) as picture_path,
                                (SELECT distinct u.pseudo  from  bids b  join users u on b.owner=u.id where b.item=v.id order by -b.created_at LIMIT 1) as buyer , (SELECT distinct  b.created_at  from  bids b  join users u on b.owner=u.id where b.item=v.id order by -b.created_at LIMIT 1) as created_at 
                                                               from v_items_status v join users u on v.owner=u.id 
                                                            join item_pictures ip on ip.item=v.id
                                                            join bids b on b.item=v.id 
                                                            where ((v.is_auction=1 and v.end_at < :current_date) or
                                        (v.has_buy_now=1 and v.buy_now_reached=1)) and  v.id = (SELECT b1.item from bids b1  where b1.amount=v.max_bid and b1.owner=:user_id )",['user_id'=>$user_id,"current_date"=>" $current_date_time"]);
      $data=$query->fetchAll();
      return $data ;   ;                                 

                               
    } 
    
    

   
   
}
?>