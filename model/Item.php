<?php

require_once "framework/Model.php";

class Item extends Model {

    private static $items_participated_in_sql =  "SELECT DISTINCT vit.*, 
                                    owners.full_name owner_full_name, 
                                    bidders.id bidder_id, 
                                    ip.priority,
                                    ip.picture_path,
                                    (SELECT COUNT(*)
                                    FROM item_pictures ip2
                                    WHERE ip2.item = vit.id) as number_of_pictures
                                FROM v_items_status vit 
                                JOIN item_pictures ip ON vit.id = ip.item 
                                JOIN users as owners ON vit.owner = owners.id
                                JOIN bids b ON b.item = vit.id
                                JOIN users as bidders ON bidders.id = b.owner
                                WHERE   ip.priority = 1 AND
                                vit.buy_now_reached != 1 AND
                                bidders.id = :user_id";


    public static function get_items_participated_in(int $user_id) : array{
        $query = self::execute(Item::$items_participated_in_sql, ["user_id" => $user_id]);
        return $query->fetchAll();
    } 

    public static function get_other_available_items(int $user_id) : array{
        $sql = " SELECT DISTINCT vit.*, 
                owners.full_name owner_full_name, 
                ip.priority,
                ip.picture_path,
                (SELECT COUNT(*)
                FROM item_pictures ip2
                WHERE ip2.item = vit.id) as number_of_pictures
            FROM v_items_status vit 
            JOIN item_pictures ip ON vit.id = ip.item 
            JOIN users as owners ON vit.owner = owners.id
            WHERE   ip.priority = 1
            AND vit.buy_now_reached != 1
            AND owners.id != :user_id
            AND vit.id NOT IN (SELECT vit2.id
                                FROM v_items_status vit2
                                JOIN bids b2 ON b2.item = vit2.id
                                JOIN users u ON u.id = b2.owner
                                WHERE u.id = :user_id)";
       $query = self::execute($sql, ["user_id" => $user_id]); 
       return $query->fetchAll();
    }
}
?>