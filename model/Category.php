<?php

require_once "framework/Model.php";

class Category extends Model
{
    public function __construct(
        private int $id,
        private string $name,
        private int $priority
    ){
        $this->id = $id;
        $this->name = $name;
        $this->priority = $priority;
    }

    //getters
    public function get_id() : int {
        return $this->id;
    }

    public function get_name() : string {
        return $this->name;
    }

    public function get_priority() : int {
        return $this->priority;
    }


    public static function get_item_categories(int $item_id) : array {
        $sql = "SELECT c.* FROM item_categories it 
                    JOIN categories c on it.category = c.id 
                    WHERE item = :item_id
                    ORDER BY c.name ASC;";
        $query = self::execute($sql, ["item_id" => $item_id]);

        $response = $query->fetchall();

        $categories = [];

        foreach ($response as $category){
            $categories[] = new Category($category['id'], $category['name'], $category['priority']);
        }

        return $categories;
    }

}