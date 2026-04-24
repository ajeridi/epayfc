<?php

use PSpell\Config;

require_once "framework/Model.php";
require_once "utils/AppTime.php";
require_once "model/Bid.php";
require_once "model/Picture.php";

function cmp(Item $i1, Item $i2): int
{
    $date1 = $i1->closed_at();
    $date2 = $i2->closed_at();

    $dates1 = new DateTime($date1);
    $dates2 = new DateTime($date2);
    if ($dates1 == $dates2) {
        return 0;
    }

    return ($dates1 > $dates2) ? 1 : -1;
}

class Item extends Model implements JsonSerializable
{

    public function __construct(
        private int $id,
        private string $title,
        private int $owner,
        private string $created_at,
        private int $duration_days,
        private ?string $pseudo,
        private ?string $description = null,
        private ?float $buy_now_price = null,
        private ?float $starting_bid = null,
        private ?array $bids = [],
        private ?array $pictures = []
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->pseudo = $pseudo;
        $this->created_at = $created_at;
        $this->duration_days = $duration_days;
        $this->description = $description;
        $this->buy_now_price = $buy_now_price;
        $this->starting_bid = $starting_bid;
    }

    public function jsonSerialize(): mixed
    {
        // converti un item en json
        return [
            'id' => $this->id,
            'title' => $this->title,
            'owner' => $this->owner,
            'pseudo' => $this->pseudo,
            'created_at' => $this->created_at,
            'duration_days' => $this->duration_days,
            'description' => $this->description,
            'buy_now_price' => $this->buy_now_price,
            'starting_bid' => $this->starting_bid,
            'bids' => $this->bids,
            'pictures' => $this->pictures
        ];
    }

    public function get_id() : int {
        return $this->id;
    }

    public function get_title() : string {
        return $this->title;
    }

    public function get_owner() : int {
        return $this->owner;
    }

    public function get_created_at() : string {
        return $this->created_at;
    }

    public function get_duration_days() : int {
        return $this->duration_days;
    }

    public function get_pseudo() : string | null {
        return $this->pseudo;
    }

    public function get_description() : string | null {
        return $this->description;
    }

    public function get_buy_now_price() : float | null {
        return $this->buy_now_price;
    }

    public function get_starting_bid() : float | null {
        return $this->starting_bid;
    }

    public function get_bids() : array | null {
        return $this->bids;
    }
    
    public function get_pictures(): array | null {
        return $this->pictures;
    }
    
    private static function build_Item(array | bool $array): Item
    {
        if (is_bool($array)) {
            throw new Exception("Unable to find the data record in the table");
        } else {

            $item = new Item(
                $array['id'],
                $array['title'],
                $array['owner'],
                $array['created_at'],
                $array['duration_days'],
                $array['pseudo'],
                $array['description'],
                $array['buy_now_price'],
                $array['starting_bid']
            );
            $item->bids = Bid::get_item_bids($item->id);
            $item->pictures = Picture::get_pictures($item->id);
            return $item;
        }
    }



    public static function get_items_participatedIn(int $user_id): array
    {
        $items = Item::get_other_items($user_id);
        $data = [];
        foreach ($items as $item) {
            if ($item->is_open_item() && $item->is_participated($user_id)) {
                $data[] = $item;
            }
        }
        return $data;
    }
    public static function get_other_available_item(int $user_id): array
    {
        $items = Item::get_other_items($user_id);
        $data = [];
        foreach ($items as $item) {
            if ($item->is_open_item() && !$item->is_participated($user_id)) {
                $data[] = $item;
            }
        }
        return $data;
    }

    public static function get_user_active_item(int $user_id): array
    {
        $items = Item::get_my_items($user_id);
        $data = [];
        foreach ($items as $item) {
            if ($item->is_open_item()) {
                $data[] = $item;
            }
        }
        return $data;
    }

    public static function get_user_closed_unsold_item(int $user_id): array
    {
        $items = Item::get_my_items($user_id);
        $data = [];
        foreach ($items as $item) {
            if (!$item->is_open_item() && $item->get_max_bid() == null) {
                $data[] = $item;
            }
        }
        return $data;
    }
    public static function  get_user_sold_item(int $user_id): array
    {
        $items = Item::get_my_items($user_id);
        $data = [];
        foreach ($items as $item) {
            if (!$item->is_open_item() && $item->get_max_bid() != null) {
                $data[] = $item;
            }
        }
        usort($data, 'cmp');

        return $data;
    }
    public static function get_purchases_items($user): array
    {
        $items = Item::get_other_items($user->get_id());
        $data = [];
        foreach ($items as $item) {
            if (!$item->is_open_item() && $item->is_highest_bidder($user->get_id())) {
                $data[] = $item;
            }
        }
        usort($data, 'cmp');

        return $data;
    }

    private static function get_other_items(int $user_id): array
    {

        $query = self::execute(
            "SELECT Distinct i.id,i.title,i.owner,i.created_at,i.duration_days
                                ,i.description,i.buy_now_price,i.starting_bid,u.pseudo,vi.end_at 
                                FROM items i join users u on i.owner = u.id join v_items_status vi
                                  on vi.id = i.id  Where i.owner != :user_id ORDER BY vi.end_at ",
            [":user_id" => $user_id]
        );
        $data = $query->fetchAll();
        $array = [];
        foreach ($data as $item) {
            $array[] = Item::build_Item($item);
        }
        return $array;
    }

    private static function get_my_items(int $user_id): array
    {

        $query = self::execute("SELECT Distinct i.id,i.title,i.owner,i.created_at,i.duration_days
                             ,i.description,i.buy_now_price,i.starting_bid, users.pseudo
                                FROM items i join v_items_status vi on vi.id=i.id join users on
                             users.id = i.owner Where i.owner = :user_id ORDER BY vi.end_at ", [":user_id" => $user_id]);
        $data = $query->fetchAll();
        $array = [];
        foreach ($data as $item) {
            $array[] = Item::build_Item($item);
        }
        return $array;
    }

    private function is_participated(int $user_id): bool
    {
        foreach ($this->bids as $bid) {
            if ($bid->get_owner() == $user_id) {
                return true;
            }
        }
        return false;
    }


    public  function is_open_item(): bool
    {
        $current_date_time = AppTime::get_current_datetime();
        if (($this->end_at() < $current_date_time || count($this->bids) != 0 && ($this->buy_now_price != null && $this->buy_now_price <= $this->bids['0']->get_amount()))) {
            return false;
        }
        return true;
    }


    public function get_path(int $priority = 1): string
    {
        if ($this->pictures == [] || !isset($this->pictures[$priority - 1]))
            return "";
        return $this->pictures[$priority - 1]->get_path();
    }

    public function get_thumbnail_path(int $priority = 1): string
    {
        if ($this->pictures == [] || !isset($this->pictures[$priority - 1]))
            return "";
        return $this->pictures[$priority - 1]->get_thumbnail_path();
    }


    public function get_pictures_by_id(): array
    {
        return Picture::get_pictures($this->id);
    }


    public function get_picture(int $priority): Picture
    {
        return $this->pictures[$priority - 1];
    }


    public function get_number_of_pictures(): int
    {
        return count($this->pictures);
    }

    public function get_max_bid(): float | null
    {

        if (count($this->bids) == 0) {
            return null;
        }
        return $this->bids['0']->get_amount();
    }


    public function delete(): void
    {
        self::execute("DELETE FROM bids WHERE item= :id  ;
                         DELETE FROM item_pictures WHERE item= :id ;
                         DELETE FROM items WHERE id = :id", ["id" => $this->id]);
    }


    public static function get_item_by_id(int $item_id): Item | bool
    {


        $query = self::execute("SELECT  i.id,i.title,i.owner,i.created_at,
                             i.duration_days,i.description,i.buy_now_price,i.starting_bid,users.pseudo
                           FROM items i join users on users.id = i.owner WHERE i.id= :id ", ["id" => $item_id]);
        $array = $query->fetch();
        $item = Item::build_Item($array);


        return $item;
    }


    public static function buy(int $owner, int $item_id, string $at, string $amount): void
    {
        $item = item::get_item_by_id($item_id);
        if (!preg_match("/^[1-9]\d*(?:[.,]\d{1,2})?$/", $amount)) {
        } else {
            $amount = str_replace(',', '.', $amount);
            if ($amount > $item->get_max_bid() && $amount >= $item->starting_bid) {
                $query = self::execute("INSERT INTO bids(owner,item,created_at,amount) VALUES (:owner,:item,:created_at,:amount)", ["owner" => $owner, "item" => $item_id, "created_at" => $at, "amount" => $amount]);
            }
        }
    }
    private static function check_title(int $owner_id,string $title, int $item_id,array $errors ) : array {
        if (!self::unicite_title_for_owner($owner_id, $title, $item_id)) {
            $errors[] = Configuration::get("unicity_title");
        }
         if (strlen($title) == 0) {
            $errors[] = Configuration::get("title_required");
        }
        
        if (strlen($title) < Configuration::get("title_min_length") ||strlen($title) > Configuration::get("title_max_length") ) {
            $errors[] = Configuration::get("lenght_title");
        }
        return $errors; 

    }
    private static function check_description(string $description,array $errors) :  array {
        
        if ($description != null && strlen($description) < Configuration::get("description_min_lenght") ||strlen($description) > Configuration::get("description_max_lenght") ) {
            $errors[] = Configuration::get("description_lenght");
        }
        return $errors; 
    }
    private static function check_duration_days(string $duration_days,array $errors) : array {

        if (!preg_match("/^(?:[1-9]|[1-5][0-9]|60)$/", $duration_days)) {
            $errors[] = Configuration::get("duration_days");
        }
        return $errors ;
    }
    private static function check_starting_bid(string $starting_bid,array $errors) : array {
        if ($starting_bid != ""  &&  !preg_match("/^[1-9]\d*(?:[.,]\d{1,2})?$/", $starting_bid)) {
            $errors[] =Configuration::get("starting_bid");
        }
        return $errors ;
        
    }
    private static function check_buy_now_price(string $buy_now_price,string $starting_bid,string $saleprice ,array $errors,bool $buy) : array {
        if ($buy_now_price != "" &&   !preg_match("/^[1-9]\d*(?:[.,]\d{1,2})?$/", $buy_now_price)) {
            if($buy) {
                $errors[] = Configuration::get("buy_now_positif");
            }
        }
        if ( $saleprice != "" &&   !preg_match("/^[1-9]\d*(?:[.,]\d{1,2})?$/", $saleprice)) {
            if(!$buy) {
                $errors[] = Configuration::get("buy_now_positif");
            }
           
        }
        if ($starting_bid  == "" && $saleprice == "") {
            $errors[] = Configuration::get("option_required");
        }
        if ( $starting_bid  != "" && $buy_now_price != "" && $buy_now_price < $starting_bid && $saleprice == "") {
            if($buy) {
                 $errors[] =Configuration::get("buy_now_sup_starting_bid");
            }
        }
         if (($starting_bid  != "" && $saleprice != "") || ($saleprice != "" && $buy_now_price != "")) {
            $errors[] = Configuration::get("both");
         }
         return $errors ;

    }
    



    public static function validat_item(int $owner, array $info, array $errors): array
    {
        $errors['title']=self::check_title($owner,$info['title'],$info['id'],$errors['title']);
        $errors['description']=self::check_description($info['description'],$errors['description']);
        $errors['duration_days']=self::check_duration_days($info['duration_days'],$errors['duration_days']);
        $errors['starting_bid']=self::check_starting_bid($info['starting_bid'],$errors['starting_bid']);
        $errors['buy_now_price']=self::check_buy_now_price($info['buy_now_price'],$info['starting_bid'],$info['saleprice'],$errors['buy_now_price'],true);
        $errors['saleprice']=self::check_buy_now_price($info['buy_now_price'],$info['starting_bid'],$info['saleprice'],$errors['saleprice'],false);

        return $errors;
    }
    private static function unicite_title_for_owner(int $owner, string $title, int $id): bool
    {

        $query = self::execute("SELECT * from items i where i.owner = :owner and i.title = :title ", ["owner" => $owner, "title" => $title]);
        $query->fetchAll();
        if (($id == 0 && $query->rowCount() != 0) || ($id != 0 && $query->rowCount() > 1)) {
            return false;
        }
        return true;
    }
    public static function put_edition_item(int $owner, array $info): int
    {
        $current_date_time = AppTime::get_current_datetime();
        $id = $info['id'];
        if ($info['buy_now_price'] == "") {
            if ($info['starting_bid'] != "" && $info['starting_bid'] != 0) {
                $info['buy_now_price'] = null;
            } else {
                $info['buy_now_price'] = $info['saleprice'];
            }
        }

        $starting_bid = str_replace(',', '.', $info['starting_bid']);
        $buy_now_price = str_replace(',', '.', $info['buy_now_price']);
        if ($id == 0) {
            self::execute("INSERT INTO items (owner,title,description,duration_days,buy_now_price,starting_bid,created_at)  VALUE 
            (:owner,:title,:description,:duration_days,:buy_now_price,:starting_bid,:created_at) ", [
                'owner' => $owner,
                'title' => $info['title'],
                'description' => $info['description'],
                'duration_days' => $info['duration_days'],
                'starting_bid' => $starting_bid,
                'buy_now_price' => $buy_now_price,
                'created_at' => $current_date_time
            ]);
            $id_nv = self::lastInsertId();
            return $id_nv;
        } else {
            self::execute("UPDATE items SET title=:title,description=:description,buy_now_price=:buy_now_price,duration_days=:duration_days,
            starting_bid=:starting_bid WHERE id =:id ", [
                'title' => $info['title'],
                'description' => $info['description'],
                'duration_days' => $info['duration_days'],
                'starting_bid' => $starting_bid,
                'buy_now_price' => $buy_now_price,
                'id' => $id
            ]);
            return $id;
        }
    }
   
    public static function get_item_title(int $item_id): string
    {
        $query = self::execute(
            "SELECT title from items WHERE id = :item_id",
            ["item_id" => $item_id]
        );
        $result = $query->fetch();
        return $result["title"];
    }

    public function upload_image(string $picture_path): void
    {
        // on regarde quelle est la priorite de la derniere image de l'item
        Picture::upload_image($this->id, $picture_path);
    }


    public function delete_picture(int $priority): void
    {
        $image_to_delete = $this->get_picture($priority);
        $image_to_delete->delete();
    }

    public static function get_picture_path(int $item_id, int $priority): string
    {
        $sql = "SELECT picture_path FROM item_pictures WHERE item = :item_id AND priority = :priority";
        $query = self::execute($sql, ["item_id" => $item_id, "priority" => $priority]);
        $res = $query->fetch();
        return $res["picture_path"];
    }


    public static function cal_total_avg_nbrOfSales(array $salesItem): array
    {
        $total = 0.0;
        $nbrOfSales = 0;
        foreach ($salesItem as $sale) {
            $total = $total + $sale->get_max_bid();
            $nbrOfSales = $nbrOfSales + 1;
        }
        $info['total'] = (float)$total;
        $info['avg'] = $nbrOfSales == 0 ? 0 : $total / $nbrOfSales;
        $info['nbr'] = $nbrOfSales;
        return $info;
    }
    public static function find_loyer_bider(array $buyer, String $indice): ?array
    {
        if (empty($buyer)) {
            return [];
        }
        $buyrArray = [];
        foreach ($buyer  as $item) {
            if ($indice == 'pseudo') {
                $buyrArray[] = User::get_pseudo_by_id($item->owner);
            }
            if ($indice == 'buyer') {
                $buyrArray[] = User::get_pseudo_by_id($item->bids['0']->owner);
            }
        }

        $count = array_count_values($buyrArray);
        $max = max($count);
        $loyer_bider = array_keys($count, $max, true);
        return $loyer_bider;
    }

    public function closed_at() : string {
        if(count($this->bids)>0){
        return $this->buy_now_price<=$this->bids['0']->get_amount() ? $this->bids['0']->get_created_at() : $this->end_at();
        }
        return $this->end_at();
    }

    public function end_at(): string
    {
        $end_at = new DateTime($this->created_at);
        $end_at->modify('+' . $this->duration_days . 'days');

        return $end_at->format('Y-m-d H:i:s');
    }

    public function get_period(): string
    {
        $current_date_time = AppTime::get_current_datetime();
        $query = self::execute("SELECT  TIMESTAMPDIFF(DAY, :current_date_time, end_at) as d,
                                TIMESTAMPDIFF(HOUR, :current_date_time, end_at) % 24 h,
                                TIMESTAMPDIFF(MINUTE, :current_date_time, end_at) % 60 m
                                 FROM v_items_status vi WHERE vi.id= :id ", ["id" => $this->id, ":current_date_time" => $current_date_time]);
        $data = $query->fetch();
        $result = "";
        if ($data['d'] != 0) {
            $result = $data['d'] . 'd ' . $data['h'] . 'h';
        } else {
            $result = $data['h'] . 'h ' . $data['m'] . 'm';
        }

        return $result;
    }
    public function is_highest_bidder(int $user_id): bool
    {
        if (count($this->bids) == 0) {
            return false;
        }
        return $this->bids['0']->get_owner() == $user_id;
    }

    public function has_bidder(int $id) : bool {
        foreach ($this->bids as $bid){
            if ($bid->get_owner() == $id)
                return true;
        }
        return false;
    }
}
