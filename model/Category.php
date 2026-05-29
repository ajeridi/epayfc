<?php

require_once "framework/Model.php";

class Category extends Model implements JsonSerializable
{
    public function __construct(
        private int $id,
        private string $name,
        private int $priority
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->priority = $priority;
    }

    //getters
    public function get_id(): int
    {
        return $this->id;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_priority(): int
    {
        return $this->priority;
    }

    public function jsonSerialize(): mixed
    {
        // converti une categorie en json
        return [
            'id' => $this->id,
            'name' => $this->name,
            'priority' => $this->priority
        ];
    }


    public static function get_item_categories(int $item_id): array
    {
        $sql = "SELECT c.* FROM item_categories it 
                    JOIN categories c on it.category = c.id 
                    WHERE item = :item_id
                    ORDER BY c.name ASC;";
        $query = self::execute($sql, ["item_id" => $item_id]);

        $response = $query->fetchall();

        $categories = [];

        foreach ($response as $category) {
            $categories[] = new Category($category['id'], $category['name'], $category['priority']);
        }

        return $categories;
    }

    public static function get_categories(): array
    {
        $query = self::execute("SELECT * FROM Categories", []);
        $result = $query->fetchAll();

        $categories = [];
        foreach ($result as $category) {
            $categories[] = new Category($category['id'], $category['name'], $category['priority']);
        }
        return $categories;
    }

    public static function get_categories_ordered_by_priority() : array 
    {
        $query = self::execute("SELECT * FROM categories ORDER BY priority ASC", []);
        $result = $query->fetchAll();

        return self::create_array_of_categories($result);
    }

    private static function create_array_of_categories(array $query_result) : array {
        $categories = [];
         foreach ($query_result as $category) {
            $categories[] = new Category($category['id'], $category['name'], $category['priority']);
        }
        return $categories;
    }
}
