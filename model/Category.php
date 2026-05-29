<?php

require_once "framework/Model.php";

class Category extends Model implements JsonSerializable
{
    public function __construct(
        private int $id,
        private string $name,
        private int $priority,
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

    public function set_name(string $name) : void {
        $this->name = $name;
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
            'priority' => $this->priority,                                                                                  
        ];
    }


    public static function get_category_by_id(int $id) : Category {
        $query = self::execute("SELECT * FROM categories WHERE id = :id", ["id" => $id]);
        $result = $query->fetch();

        return new Category($result['id'], $result['name'], $result['priority']);
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

    public function is_empty() : bool {
        $query = self::execute("SELECT count(*) = 0 from item_categories where category = :category_id", ["category_id" => $this->id]);

        $result = $query->fetchColumn();
        return $result;
    }

    // methode qui fait monter un categorie, donc diminue sa priorite de 1
    public function move_up() : void {
        $from = $this->get_priority();
        $to = $this->get_priority()-1;
        $this->swap_priorities($from, $to);
    }
    
    public function move_down() : void {
        $from = $this->get_priority();
        $to = $this->get_priority()+1;
        $this->swap_priorities($from, $to);
    }

    // methode qui swap deux priorites
    private function swap_priorities(int $from, int $to) : void {
        // donc on peut dire que celui du dessus prend -$from et celui du bas -$to, puis remettre les priorite < 0 en positif
        self::execute("UPDATE categories SET priority = -1 * :new_priority WHERE priority = :old_priority", ["new_priority" => $to, "old_priority" => $from]);
        self::execute("UPDATE categories SET priority = -1 * :old_priority WHERE priority = :new_priority", ["new_priority" => $to, "old_priority" => $from]);
        self::execute("UPDATE categories SET priority = -1 * priority WHERE priority < 0", []);
    }

    //methode qui va valider le nom d'une categorie
    public function validate_name(string $new_name) : string | null {
        $min_length = Configuration::get("min_category_name_length");
        $max_length = Configuration::get("max_category_name_length");

        if (strlen($new_name) < $min_length  || strlen($new_name) > $max_length) 
            return "Category name length must be between ".$min_length." and ".$max_length." characters.";
        if (!self::is_unique($new_name)){
            return "Category name must be unique";
        }
        return null;
    }

    private function is_unique(string $new_name) : bool {
        if ($new_name === $this->name) return true; // si le nouveau nom est celui de la categorie, le nom est unique
        $query = self::execute("select NOT EXISTS(select 1 from categories WHERE lower(name) = lower(:new_name)) as is_unique", ["new_name" => $new_name]);

        return $query->fetchColumn();
    }

    public function persist() : void {
        if ($this->id != 0){
            self::execute("UPDATE categories SET name = :name, priority = :priority WHERE id = :id", ["id" => $this->id, "name" => $this->name, "priority" => $this->priority]);
        }
        else {
            self::execute("INSERT INTO categories(name, priority) VALUES(:name, (SELECT MAX(c.priority) from categories c) + 1)",["name" => $this->name]);
        }
    }
}
