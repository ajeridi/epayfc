<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Item extends Model {
    public function __construct(public int $id ,public string $title,public ?string $description,public int $owner,public string $created_at,public ?int $buy_now_price,public ? int $starting_bid ,public string $picture_path ){
    }

    private static $items_participated_in_sql =  "SELECT DISTINCT vit.*, owners.full_name owner_full_name, b.amount, ip.picture_path, (SELECT COUNT(*) FROM item_pictures ip2 WHERE ip2.item = vit.id) as number_of_pictures, 
    CONCAT(TIMESTAMPDIFF(DAY, :current_date_time, vit.end_at), 'd ', TIMESTAMPDIFF(HOUR, :current_date_time, vit.end_at) % 24, 'h') as time_left
                                FROM v_items_status vit 
                                JOIN item_pictures ip ON vit.id = ip.item 
                                JOIN users as owners ON vit.owner = owners.id
                                JOIN bids b ON b.item = vit.id
                                WHERE   ip.priority = 1 AND
                                vit.buy_now_reached = 0 AND
                                b.owner = :user_id AND
                                vit.end_at > :current_date_time
                                ORDER BY vit.end_at";


    public static function get_items_participated_in(int $user_id) : array{
        $query = self::execute(Item::$items_participated_in_sql, ["user_id" => $user_id, "current_date_time" => "2025-11-19 16:11:00"]);
        return $query->fetchAll();
    } 

    public static function get_other_available_items(int $user_id) : array{
        $sql = " SELECT DISTINCT vit.*, 
                owners.full_name owner_full_name, 
                ip.picture_path,
                (SELECT COUNT(*)
                FROM item_pictures ip2
                WHERE ip2.item = vit.id) as number_of_pictures,
                CONCAT(TIMESTAMPDIFF(DAY, :current_date_time, vit.end_at), 'd ', TIMESTAMPDIFF(HOUR, :current_date_time, vit.end_at) % 24, 'h') as time_left
            FROM v_items_status vit 
            JOIN item_pictures ip ON vit.id = ip.item 
            JOIN users as owners ON vit.owner = owners.id
            WHERE   ip.priority = 1
            AND vit.buy_now_reached = 0
            AND vit.end_at > :current_date_time
            AND owners.id != :user_id
            AND vit.id NOT IN (SELECT bids.item
                                FROM bids
                                WHERE bids.owner = :user_id)
            ORDER BY vit.end_at";
       $query = self::execute($sql, ["user_id" => $user_id, "current_date_time" => "2025-11-19 16:11:00"]); 
       return $query->fetchAll();
    }


    public static function get_user_active_items(int $user_id) : array{
        

        $sql = "SELECT vit.id, vit.title, owners.full_name, vit.starting_bid, vit.max_bid, vit.is_auction, vit.is_direct_sale, vit.buy_now_price, vit.has_buy_now, (SELECT COUNT(*) FROM item_pictures ip WHERE ip.item = vit.id) as number_of_pictures, ip.picture_path, CONCAT(TIMESTAMPDIFF(DAY, :current_date_time, vit.end_at), 'd ', TIMESTAMPDIFF(HOUR, :current_date_time, vit.end_at) % 24, 'h') as time_left
                FROM v_items_status vit
                JOIN users as owners on owners.id = vit.owner
                JOIN item_pictures ip ON ip.item = vit.id
                WHERE owners.id = :user_id AND 
                vit.buy_now_reached = 0 AND 
                vit.end_at > :current_date_time AND
                ip.priority = 1
                ORDER BY vit.end_at";

       $query = self::execute($sql, ["user_id" => $user_id, "current_date_time" => "2025-11-19 16:11:00"]); 
       return $query->fetchAll();

    }


    public static function get_user_closed_unsold_items(int $user_id) : array{
        

        $sql = "SELECT vit.id, vit.title, owners.full_name, vit.starting_bid, vit.max_bid, vit.is_auction, vit.is_direct_sale, vit.buy_now_price, vit.has_buy_now, (SELECT COUNT(*) FROM item_pictures ip WHERE ip.item = vit.id) as number_of_pictures, ip.picture_path
                FROM v_items_status vit
                JOIN users as owners on owners.id = vit.owner
                JOIN item_pictures ip ON ip.item = vit.id
                WHERE owners.id = :user_id AND 
                vit.buy_now_reached = 0 AND 
                vit.end_at <= :current_date_time AND
                ip.priority = 1
                ORDER BY vit.end_at";

       $query = self::execute($sql, ["user_id" => $user_id, "current_date_time" => "2025-11-19 16:11:00"]); 
       return $query->fetchAll();

    }

    
    public static function get_user_sold_items(int $user_id) : array{
        

        $sql = "SELECT vit.id, vit.title, owners.full_name, vit.starting_bid, vit.max_bid, vit.is_auction, vit.is_direct_sale, vit.buy_now_price, vit.has_buy_now, (SELECT COUNT(*) FROM item_pictures ip WHERE ip.item = vit.id) as number_of_pictures, ip.picture_path 
                FROM v_items_status vit
                JOIN users as owners on owners.id = vit.owner
                JOIN item_pictures ip ON ip.item = vit.id
                WHERE owners.id = :user_id AND 
                vit.buy_now_reached = 1 AND 
                ip.priority = 1
                ORDER BY vit.end_at";

       $query = self::execute($sql, ["user_id" => $user_id]); 
       return $query->fetchAll();

    }

    public static function get_item_by_id(int $item_id,int $priority) : array {
        $query=self::execute("SELECT is_direct_sale,has_buy_now, v_items_status.id,title , description,v_items_status.created_at, end_at, bid_count,buy_now_price,max_bid,item_pictures.picture_path ,priority,u1.pseudo,u1.picture_path as user_path ,u1.role,starting_bid
                                 FROM  v_items_status join item_pictures on v_items_status.id=item_pictures.item 
                                  join users u1 on v_items_status.owner=u1.id
                                            WHERE v_items_status.id= :id
                                            and item_pictures.priority=:priority",["id"=>$item_id,"priority"=>$priority]);
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
       public static function is_open_anonce(int $item_id) {
        $query=self::execute("SELECT * FROM v_items_status Where id= :id and end_at > :current_date_time and ((is_auction = 1 and buy_now_reached = 0 ) or (is_direct_sale=1 and not_purchased_direct_sale=1))",["id"=>$item_id,"current_date_time"=> "2025-11-10 16:11:00"]);

        if($query->rowCount()==0){
            return false ;
        }else{
            return true; 
        }

    }
    public static function buy($owner,$item_id,$at,$amount): void {
        $bids=item::get_item_by_id($item_id,1);
        if($amount>$bids['max_bid'] && $amount>=$bids['starting_bid']){
                 $query=self::execute("INSERT INTO bids(owner,item,created_at,amount) VALUES (:owner,:item,:created_at,:amount)",["owner"=>$owner,"item"=>$item_id,"created_at"=>$at,"amount"=>$amount]);
        }

    }
   
}
?>