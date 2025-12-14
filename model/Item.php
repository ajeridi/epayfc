<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Item extends Model {

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

}
?>