<!DOCTYPE html>
<html lang="en">
     <head>
        <meta charset="UTF-8">
        <title>🛒 <?= $item['title'] ?></title>
        <base href="<?= $web_root ?>">
         <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
         <header>
            <!-- Barre au dessus de la page -->
            <nav class="navbar bg-navy-gray fixed-top" data-bs-theme="dark" >
                <div class="container-fluid">
                    <a class="navbar-brand text-blue" href="item/<?= $from ?>"><i class="bi bi-arrow-left"></i> </a>
                    <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue">Open <i class=" bi bi-cart4 fs-4 text-white"></i></span>
                </div>
            </nav>
        </header>
    <body class="app-bg page-item-open">             
    <main class="container-fluid px-3 page-wrap" style="margin-top: 100px;">
      <br>
      <div class="row g-3 align-items-start">

        <!-- GAUCHE -->
        <div class="col-12 col-lg-8">

          <!-- Card image + infos -->
          <div class="card card-dark border-0 overflow-hidden">
            <div class="ratio ratio-16x9 bg-black-30">
              <img <?= ($item['picture_path']=="") ? "src=uploads/items/test.jpg" :   "src=".$item['picture_path'] ?>  class="w-100 h-100 object-fit-cover" alt="item image">
            </div>

            <div class="card-body">
              <div class="d-flex justify-content-between gap-3">
                <div>
                  <div class="h5 mb-1"><?= $item['title'] ?></div>
                  <div class="text-muted small mb-2"><?= $item['description'] ?></div>

                  <div class="text-muted small">
                    <div><span class="label">Start:</span> <?= $item['created_at'] ?></div>
                    <div><span class="label">Ends:</span> <?= $item['end_at'] ?></div>
                  </div>
                </div>

                <span class="badge badge-pill">Auction</span>
              </div>
            </div>
          </div>

          <!-- Additional Images  -->
          <div class="mt-3">
            <div class="section-title mb-2">Additional Images</div>

            <div class="thumbs d-flex gap-2 flex-wrap">
              <?php
                foreach ($pictures as $i => $p) { ?>
                  <a class="thumb <?= $i === 0 ? 'active' : '' ?>"  href="item/open/<?= $item['id']?>/from-browse/<?= $p['priority'] ?>">
                    <img src="<?= $p['picture_path'] ?>" alt="thumb">
                  </a>
              <?php } ?>
            </div>
          </div>

          <!-- Bid History -->
          <div class="card card-dark border-0 mt-3">
            <div class="d-flex align-items-center justify-content-between px-3 pt-3">
              <div class="section-title mb-0">Bid History</div>
          
              <span class="badge badge-dark"><?= $item['bid_count']?> entries</span>
            </div>

            <div class="list-group list-group-flush list-dark mt-3">
              <?php foreach ($bids as $bid){ ?>
                <div class="list-group-item">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="fw-semibold"><?= $bid['pseudo'] ?></div>
                      <div class="small text-muted"><?= $bid['created_at'] ?></div>
                    </div>
                    <div class="price-green fw-semibold">€ <?= $bid['amount'] ?></div>
                  </div>
                </div>
              <?php  } ?>
            </div>

            <div class="p-2"></div>
          </div>

        </div>

        <!-- DROITE -->
        <div class="col-12 col-lg-4 ">

          <div class="card card-dark border-0">
            <div class="card-body">
              <div class="section-title mb-3">Pricing</div>
              <?php if($item['is_direct_sale']==0) {?>
              <div class="d-flex justify-content-between small mb-0">
                <span class="text-muted">Current Bid</span>
                <span class="price-green fw-semibold">
                  <?php if($item['max_bid']==NULL){?>
                  <i class="bi bi-currency-euro"></i><?= $item['starting_bid'] ?>
                </span>
              </div>
                <div class="d-flex justify-content-between small mb-2">
                <span class="text-muted">starting at € <?= $item['starting_bid'] ?> </span>    
                </div>
                  <?php }else {?>
                    <i class="bi bi-currency-euro"></i><?= $item['max_bid'] ?>
                </span>
              </div>       
                    <?php } ?> 
                <?php } ?>           
              <div class="d-flex justify-content-between small mb-3">
                
                <?php if($item['has_buy_now']==1){ ?>
                <span class="text-muted">Buy Now</span>
                <span class="fw-semibold">
                  <i class="bi bi-currency-euro"></i><?= $item['buy_now_price'] ?>
                </span>
                <?php } ?>    
              </div>
            
              <hr class="hr-soft">
              <fieldset <?php if($user == false || $user->pseudo == $item['pseudo'] ){?> disabled <?php } ?>>
              <?php  if($item['is_direct_sale']==0) {?>
              <?php if($is_open){?>
              <form method="post" action="item/bid/<?= $item['id']?>">
                <div class="input-group input-dark mb-3">
                  <span class="input-group-text">€</span>
                  <input
                    type="text"
                    name="min_bid"
                    class="form-control"
                    value="<?= ($item['max_bid'] === null) ? (($item['starting_bid'] != 0) ? $item['starting_bid'] : 1) : ($item['max_bid'] + 1) ?>"
                    required
                  >
                </div>

                <div class="d-grid gap-2">
                  <button class="btn btn-success" type="submit">Place Bid</button>
                  <?php if($item['has_buy_now']==1){ ?>
                  <a class="btn btn-outline-primary  " href="item/buy/<?= $item['id'] ?>">
                    Buy Now at € <?= $item['buy_now_price'] ?>
                  </a>
                  <?php } ?>
                </div>
              </form>
              <?php } ?>
              <?php } ?>
              <?php  if($item['is_direct_sale']==1 && $is_open ) {?>
                <div class="d-grid gap-2">
                <a class="btn btn-outline-success" href="item/buy/<?= $item['id'] ?>">
                    Buy Now 
                  </a>
                </div>
              </fieldset>   
              <?php }if(!$is_open){
                if($item['bid_count']!=0){
                if($user->id===$bids['0']['owner']){?>
                <p> Congratulation! You purchased this item for € <?= $item['max_bid'] ?> </p>
                <?php }else {?>
                <p><?= $bids['0']['pseudo'] ?> Won this item for € <?= $item['max_bid'] ?></p> 
              <?php } }  else {?>
                <p>This listing ended whitout a buyer</p>
                <?php } }?> 

                <?php if($user == true && $user->pseudo === $item['pseudo'] && $is_open){?>
                <p>You cannot bid on your own listing</p>
                <?php } ?> 
                <?php if($user == false && $is_open){?>
                <p>Option not available for guests</p>
                <?php } ?> 

            </div>
          </div>

          <div class="card card-dark border-0 mt-3">
            <div class="card-body">
              <div class="section-title mb-3">Seller Information</div>

              <div class="d-flex align-items-center gap-3">
                <div class="avatar rounded-circle"><img src="<?= $item['user_path'] ?>"  class="w-100"></div>
                <div>
                  <div class="fw-semibold"><?= $item['pseudo'] ?></div>
                  <div class="text-muted small"><?= $item['role'] ?></div>
                </div>
              </div>
            </div>
          </div>
          <?php if($user == true && $user->pseudo === $item['pseudo']  && $is_open){?>
          <div class="card card-dark border-0 mt-3">
            <div class="card-body">
              <div class="section-title mb-3">Manage Your Item</div>
                <div class="row g-3">
                <div class="d-grid gap-2">  
                  <a class="btn btn-outline-success <?php if($item['bid_count']!=0){?>disabled <?php } ?> "  href="item/edit/<?= $item['id']?>">
                    Edit item Details
                    </a>
                </div>
                <div class="d-grid gap-2">    
                    <a class="btn btn-outline-info" href="item/manage_images/<?= $item["id"] ?>">
                      Manage Images
                    </a>
                </div>
                <div class="d-grid gap-2">    
                    <a class="btn btn-outline-danger" href="">
                      Delete Item 
                    </a>
                </div>    
              </div>
            </div>
          </div>
          <?php } ?>

        </div>

      </div>
    </main>
</body>    
</html>      