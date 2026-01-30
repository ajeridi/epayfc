<?php 

class Bid extends Model {
    public int $owner ;
    public int $item;
    public string $created_at;
    public float $amount ;
    public function __construct(){}

    public static function get_item_bids(int $item_id) : array{
        $query=self::execute("SELECT * FROM bids b WHERE b.item = :item_id ORDER BY -b.created_at ",["item_id"=>$item_id]);
        return $query->fetchAll(PDO::FETCH_CLASS,Bid::class) ;
    }
   
}
?>