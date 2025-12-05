<?php

require_once "framework/Model.php";

class Item extends Model {


    public static function get_items() : array{
    $sql = "SELECT i.*, ip.priority, ip.picture_path 
        FROM items i 
        JOIN item_pictures ip ON i.id = ip.item 
        WHERE ip.priority = 1";

        $query = self::execute($sql, []);
        return $query->fetchAll();
    } 
}
?>