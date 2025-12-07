<?php

require_once "framework/Model.php";

class Item extends Model {


    public static function get_items() : array{
    $sql = "SELECT  vit.*, 
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
        WHERE ip.priority = 1";
        

        $query = self::execute($sql, []);
        return $query->fetchAll();
    } 
}
?>