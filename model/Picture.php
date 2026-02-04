<?php
require_once "framework/Model.php";
require_once "utils/ImageUtils.php";


class Picture extends Model
{

    public function __construct(
        private int $item,
        private int $priority,
        private string $path
    ) {

        $this->item = $item;
        $this->priority = $priority;
        $this->path = $path;
    }


    // renvoie une liste de pictures appartenant a un item
    public static function get_pictures(int $item_id): array
    {
        $sql = "SELECT * FROM item_pictures WHERE item = :item_id";
        $query = self::execute($sql, ["item_id" => $item_id]);
        $pictures = $query->fetchAll();

        return Picture::map_to_array($pictures);
    }


    // map chaque row en un objet Picture
    private static function map_to_array(array | bool $pictures): array
    {
        if (!$pictures)
            return [];

        $array_of_pictures = [];
        foreach ($pictures as $picture) {
            $array_of_pictures[] = new Picture($picture["item"], $picture["priority"], $picture["picture_path"]);
        }
        return $array_of_pictures;
    }

    public function get_thumbnail_path(): string
    {
        return ImageUtils::get_thumbnail_path($this->path);
    }

    public function  get_item_id(): int
    {
        return $this->item;
    }

    public function get_priority(): int
    {
        return $this->priority;
    }

    public function get_path(): string
    {
        return $this->path;
    }

    public function delete(): void
    { // supprime une image dans la table item_pictures
        $sql = "DELETE FROM item_pictures WHERE item = :item_id AND priority = :priority";
        $query = self::execute($sql, ["item_id" => $this->item, "priority" => $this->priority]);
        // ensuite il faut actualiser les priorites des images ayant une priorite > $priority, faire -1 a chaque priorite se trouvant apres
        $this->update_priorities($this->item, $this->priority);
    }

    private function update_priorities(): void
    { // mets a jour les priorites suivant celle de l'image (non inclus) apres la suppression de celle ci
        $sql = "UPDATE item_pictures SET priority = (priority - 1) WHERE priority > :priority AND item = :item_id";
        $query = self::execute($sql, ["priority" => $this->priority, "item_id" => $this->item]);
    }

    public static function upload_image(int $item, string $path): void
    {
        $priority = Picture::get_new_picture_priority($item);
        $query = self::execute(
            "INSERT INTO item_pictures(item, priority, picture_path) VALUES (:item_id, :priority, :picture_path)",
            ["item_id" => $item, "priority" => $priority, "picture_path" => $path]
        );
    }

    private static function get_new_picture_priority(int $item_id): int
    {
        $sql = "SELECT (MAX(priority)+1) as next_priority FROM item_pictures WHERE item = :item_id";
        $query = self::execute($sql, ["item_id" => $item_id]);
        $res = $query->fetch();
        return $res["next_priority"] == null ? 1 : $res["next_priority"];
    }

    public static function swap_priorities(int $item_id, int $old_priority, int $new_priority): void
    {
        // Pour update deux champs sous contrainte, il faut faire un assignation avec une variable temp
        // si old = 1 et new = 2, old = -1, new = 1, old = 2, car sinon erreur
        $old_priorities = [$old_priority, $new_priority, -1];
        $new_priorites = [-1, $old_priority, $new_priority];
        for ($i = 0; $i < 3; $i++) {
            Picture::update_priority($item_id, $old_priorities[$i], $new_priorites[$i]);
        }
    }

    private static function update_priority(int $item_id, int $old_priority, int $new_priority): void
    {
        $sql = "UPDATE item_pictures SET priority = :new_priority WHERE priority = :old_priority AND item = :item_id;";
        $query = self::execute($sql, ["new_priority" => $new_priority, "old_priority" => $old_priority, "item_id" => $item_id]);
    }
}
