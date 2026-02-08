<?php 

class Bid extends Model {
    
    public function __construct(public int $owner ,
    private int $item,
    private string $created_at,
    private float $amount,
    private string $pseudo
     ){
        $this->owner=$owner;
        $this->item=$item;
        $this->created_at=$created_at;
        $this->amount=$amount;
        $this->pseudo=$pseudo; 
    }


 

    public static function get_item_bids(int $item_id) : array {
        $query=self::execute("SELECT b.owner,b.item ,b.created_at,b.amount,u.pseudo FROM bids b join users u on b.owner = u.id  WHERE b.item = :item_id ORDER BY -b.created_at ",["item_id"=>$item_id]);
        $array=$query->fetchall();
        $bids=[];

        
            foreach($array as $bid){
                $bids[]=new Bid($bid['owner'],$bid['item'],$bid['created_at'],$bid['amount'],$bid['pseudo']); 
            }
            return $bids;
       
    }
    public function get_owner(): int {
        return $this->owner ; 
    }
    public function get_amount() : float {
        return $this->amount ;
    }
    public function get_item() : int {
        return $this->item; 
    }
    public function get_created_at() : string {
        return $this->created_at; 
    }
    public function get_pseudo() : string {
        return $this->pseudo;
    }
   
}
?>