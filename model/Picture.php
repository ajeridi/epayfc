<?php
require_once "framework/Model.php";
require_once "utils/ImageUtils.php";


class Picture extends Model
{

    public function __construct(
        private int $item,
        private int $priority,
        private string $path) {

        $this->item = $item;
        $this->priority = $priority;
        $this->path = $path;
    }


    // renvoie une liste de pictures appartenant a un item
    public static function get_pictures(int $item_id) : array {
        $sql = "SELECT * FROM item_pictures WHERE item = :item_id";
        $query = self::execute($sql, ["item_id" => $item_id]);
        $pictures = $query->fetchAll();

        return Picture::map_to_array($pictures);
    }


    // map chaque row en un objet Picture
    private static function map_to_array(array | bool $pictures) : array {
        if (!$pictures)
            return [];

        $array_of_pictures = [];
        foreach($pictures as $picture){
            $array_of_pictures[] = new Picture($picture["item"], $picture["priority"], $picture["picture_path"]);
        }
        return $array_of_pictures;
    }

    public function get_thumbnail_path() : string {
        return ImageUtils::get_thumbnail_path($this->path);
    }

    public function  get_item_id() : int {
        return $this->item;
    }

    public function get_priority() : int {
        return $this->priority;
    }

    public function get_path() {
        return $this->path;
    }
}
