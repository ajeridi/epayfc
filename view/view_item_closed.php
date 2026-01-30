
<!DOCTYPE html>
<html>
    <head>
         <meta charset="UTF-8">
        <title>🛒 Settings </title>
        <base href="<?= $web_root ?>">
        <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <header>
            <nav class="navbar bg-navy-gray" data-bs-theme="dark">
                <div class="container-fluid">
                    <a class="navbar-brand text-blue" href="user/profile"><i class="bi bi-arrow-left"></i> </a>
                    <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue"><?= $_GET['action'] ?><i class=" bi bi-cart4 fs-4 text-white"></i></span>
                </div>
            </nav>
        </header>
     <body>
        <main class="container-sm mt-4 text-white text-bold mt-5  " style="margin:0;max-width: 100%;">
            <div class="container mb-3" style=" margin-top:25px;max-width: 100%;">
                <div class="row ">
                    <div class="col "  >
                    <h2> <?= ($_GET['action']=="sales") ? "Completed Sales" : "My Purchases" ?>  </h2>          
                    <p class="card-text small"><?= ($_GET['action']=="sales") ? "A snapshot of the deals you've wrapped up." : "Track the gear you've successfully secured." ?></p>
                    </div>
                <div class=" col-1 small text-bg-<?= ($_GET['action']=="sales") ?"success":"primary"?> text-center h-25 " style="border-radius:25px;height:auto;width: auto;"><?=  $info['nbr'] ?> <?= ($_GET['action']=="sales") ? "sales": "purchases" ?></div>    
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 mb-3 mb-sm-0 ">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="text-light"><?= ($_GET['action']=="sales") ? "TOTAL REVENUE" : "TOTAL SPENT" ?></h1>
                            <p>€ <?=number_format($info['total'],2,',',' ')?></p>
                            <p>Across <?=($_GET['action']=="sales") ? $info['nbr']." sales": "pas encore" ?> </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-3 mb-sm-0 ">    
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="text-light">Average Ticket</h1>
                            <p>€ <?= number_format($info['avg'],2,',',' ') ?></p>
                            <p><?=($_GET['action']=="sales") ? "Median buyer appetite indicator": "Helps you plan future bids" ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-3 mb-sm-0 ">   
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="text-light"><?=($_GET['action']=="sales") ? "LOYAL BIDER" : "TOP SELLER" ?></h1>
                            <p><?= $nom ?></p>
                            <p><?=($_GET['action']=="sales") ? "Most recurring winning bidder" : "Who you've bought from the most" ?></p>
                        </div>
                    </div>
                </div>
            </div>
           <div class="container mt-4 mb-1 " style="margin:0;max-width: 100%;">
                    <div class="row text-white ">
                        <!-- chaque colonne a une largeur de 3 car 12 max par row, donc pour afficher 4 items, faut 12/4 = 3 par items -->
                        <?php
                        foreach ($salesItem as $item) { ?>
                                <div class="col-2  " >
                                    <a href="item/open/<?= $item->id?>/from-<?= $_GET['action'] ?>" style="text-decoration: none;">
                                        <div class="card border-0" style="width: 18rem;">
                                            <div class="card-head">    
                                            <img src='<?=$item->get_path()?>' class="card-img-top" alt="image item">
                                            <div class="card-img-overlay">
                                                <?php if ($_GET['action']=="purchases") { ?>
                                                            <span class="badge rounded-pill text-bg-success"><i class="bi bi-trophy"></i> Highest Bidder</span>
                                                <?php }?>
                                                <div class="float-end d-flex flex-column gap-2 h-50">
                                                    <?php 
                                                        if ($item->starting_bid != null){?>
                                                            <span class="badge rounded-pill" style="background-color: #f59e0c ; color: white;"><i class="ti ti-gavel"></i> Auction</span>
                                                    <?php  }
                                                        if ($item->starting_bid != null OR $item->buy_now_price != null){?>
                                                            <span class="badge rounded-pill text-bg-primary mb-auto"><i class="bi bi-bag"></i> Buy Now</span>
                                                        <?php } ?>    
                                                            <span class="badge rounded-pill text-bg-dark mt-auto"><i class="bi bi-images"></i> <?= $item->get_nomber_of_pictures() ?> images</span>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="card-body bg-dark text-white">
                                                <p class="card-text fw-bolder mb-0"><?=$item->title?></p>
                                                <p class="card-text fw-light fs-6">By <?= User::get_pseudo_by_id($item->owner)?></p>
                                                <div class="container">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <?php if ($item->buy_now_price == NULL){?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #f59e0c;">€ <?= $item->starting_bid?></p>
                                                            <?php }else{?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #397deb;">€ <?= $item->buy_now_price?></p>
                                                            <?php }?>
                                                        </div>
                                                        <div class="col-md-6 text-end">
                                                            <div class="d-flex flex-column">
                                                                <span class="card-text fw-light fs-6 mb-0 text-muted">Current bid</span>
                                                                <span class="card-text fs-6" style="color: green;">€ <?= number_format($item->get_max_bid(),2,',',' ') ?><span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="card-text text-tertiary  fw-bold"><i class="bi bi-clock"></i> Closed</span>
                                            </div>
                                            <div class="card-footer text-light " style="background-color: #1e1231;" >
                                             <?php if($_GET['action']=="sales"){?>  
                                               <p><span><i class="bi bi-currency-dollar fs-5"> <?php } else {?><p><span><i class="bi bi-tag fs-5"><?php } ?> </i><?= ($_GET['action']=="sales") ? "Final price € ".number_format($item->get_max_bid(),2,',',' ') : "Paid ".number_format($item->get_max_bid(),2,',',' ') ?> </span></p>
                                              <?php if($_GET['action']=="sales") {?>  <p><span><i class="bi bi-trophy"></i> <?= $item["buyer"] ?></span></p>
                                                <?php } ?>
                                                
                                                <p><span><i class="bi bi-clock fs-5"></i> Closed on <?= $item->created_at ?> </span></p>

                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php 
                    }
                    ?>
                </div> 
             
            
        </main>
     </body>




</html>    