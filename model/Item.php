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
    public static function get_item_by_id(int $item_id) : array {
        $query=self::execute("SELECT * FROM items join item_pictures ON items.id=item_pictures.item join v_items_status on items.id=v_items_status.id WHERE items.id= :id  ORDER BY item_pictures.priority",["id"=>$item_id]);
        $data=$query->fetch();
       
            return $data;
        

    }
    public static function get_other_pictures_by_id(int $item_id): array {
        $query=self::execute("SELECT * FROM item_pictures WHERE item = :item and priority != 1 ",["item"=>$item_id]);
        $data=$query->fetchAll();
        return $data;
    }
}
?>