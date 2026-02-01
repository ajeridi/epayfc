<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Browse items</title>
        <base href="<?= $web_root ?>"?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body class="bg-navy">
        <header>
            <!-- Barre au dessus de la page -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark">
                <div class="container-fluid justify-content-center">
                    <span class="navbar-brand mb-0 h1 text-blue"">My items <i class="bi bi-cart4 fs-4 text-white"></i></span>
                </div>
            </nav>
        </header>
        <main class="main-content-margins">
            <div class="active_items float-start">
                <h3 class="text-blue"><?= count($active_items)>0 ?  "Active items" : "" ?></h3>

                <!-- tableau contenant les cartes des items -->
                <div class="container mt-4 mb-5">
                    <div class="row text-white g-3">
                        <!-- chaque colonne a une largeur de 3 car 12 max par row, donc pour afficher 4 items, faut 12/4 = 3 par items -->
                        <?php
                        foreach ($active_items as $item) { 
                            $current_dates=AppTime::get_current_datetime();
                            $current_date= new DateTime($current_dates);
                            $durations=new DateInterval("P{$item->duration_days}D");
                            $datedebut=new DateTime($item->created_at);?>
                                <div class="col-3">
                                    <a href="item/open/<?= $item->id?>/from-items" style="text-decoration: none;">
                                        <div class="card" style="width: 18rem;">    
                                            <img <?= ($item->get_path()==null) ? "src=uploads/items/default.jpg" :   "src=".ImageUtils::get_thumbnail_path($item->get_path())?> class="card-img-top" alt="image item">
                                            <div class="card-img-overlay">
                                                <div class="float-end d-flex flex-column gap-2 h-50">
                                                    <?php 
                                                        if ($item->starting_bid != null){?>
                                                            <span class="badge rounded-pill" style="background-color: #f59e0c ; color: white;"><i class="ti ti-gavel"></i> Auction</span>
                                                    <?php  }
                                                        if ($item->starting_bid == null OR $item->buy_now_price != null){?>
                                                            <span class="badge rounded-pill text-bg-primary mb-auto"><i class="bi bi-bag"></i> Buy Now</span>
                                                        <?php } 
                                                        if ($item->get_nomber_of_pictures() > 1) {
                                                        ?>    
                                                            <span class="badge rounded-pill text-bg-dark mt-auto"><i class="bi bi-images"></i> <?= $item->get_nomber_of_pictures() ?> images</span>
                                                        <?php }?>    
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text fw-bolder mb-0"><?=$item->title?></p>
                                                <p class="card-text fw-light fs-6">By <?= User::get_full_name_by_id($item->owner)?></p>
                                                <div class="container">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <?php if ($item->buy_now_price == NULL){?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #f59e0c;">€ <?= number_format($item->starting_bid, 2, ",", " ")?></p>
                                                            <?php }else{?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #397deb;">€ <?= number_format($item->buy_now_price, 2, ",", " ")?></p>
                                                            <?php }?>
                                                        </div>
                                                        <?php if ($item->get_max_bid() != NULL) { ?>
                                                            <div class="col-md-6 text-end">
                                                                <div class="d-flex flex-column">
                                                                    <span class="card-text fw-light fs-6 mb-0 text-muted">Current bid</span>
                                                                    <span class="card-text fs-6" style="color: green;">€ <?= number_format($item->get_max_bid(), 2, ",", " ") ?><span>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <span class="card-text text-success fw-bold"><i class="bi bi-clock"></i> <?php echo $current_date->diff($datedebut->add($durations))->format('%dd  %hh') ?> left</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php 
                    }
                    ?>
                </div>
            </div>

            <div class="closed_unsold_items">
                <h3 class="text-blue"><?= (count($closed_unsold_items) >0) ?  "Closed Unsold Items":"" ?></h3>

                <!-- tableau contenant les cartes des items -->
                <div class="container mt-4 mb-5">
                    <div class="row text-white g-3">
                        <!-- chaque colonne a une largeur de 3 car 12 max par row, donc pour afficher 4 items, faut 12/4 = 3 par items -->
                        <?php
                        foreach ($closed_unsold_items as $item) { ?>
                                <div class="col-3">
                                    <a href="item/open/<?= $item->id?>/from-items" style="text-decoration: none;">
                                        <div class="card" style="width: 18rem;">    
                                            <img <?= ($item->get_path()==null) ? "src=uploads/items/default.jpg" :   "src=".ImageUtils::get_thumbnail_path($item->get_path(), 2, ",", " ")?> class="card-img-top" alt="image item">
                                            <div class="card-img-overlay">
                                                <div class="float-end d-flex flex-column gap-2 h-50">
                                                    <?php 
                                                        if ($item->starting_bid != null){?>
                                                            <span class="badge" style="background-color: #f59e0c ; color: white;"><i class="ti ti-gavel"></i> Auction</span>
                                                    <?php } 
                                                        if ($item->buy_now_price != null){?>
                                                            <span class="badge text-bg-primary mb-auto"><i class="bi bi-bag"></i> Buy Now</span>
                                                        <?php } 
                                                        if ($item->get_nomber_of_pictures() > 1) {
                                                        ?>    
                                                            <span class="badge rounded-pill text-bg-dark mt-auto"><i class="bi bi-images"></i> <?= $item->get_nomber_of_pictures() ?> images</span>
                                                        <?php }?>    
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text fw-bolder mb-0"><?=$item->title?></p>
                                                <p class="card-text fw-light fs-6">By <?= User::get_full_name_by_id($item->owner)?></p>
                                                <div class="container">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <?php if ($item->buy_now_price == NULL){?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #f59e0c;">€ <?= number_format($item->starting_bid, 2, ",", " ")?></p>
                                                            <?php }else{?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #397deb;">€ <?= number_format($item->buy_now_price, 2, ",", " ")?></p>
                                                            <?php }?>
                                                        </div>
                                                        <?php if ($item->get_max_bid() != NULL) { ?>
                                                            <div class="col-md-6 text-end">
                                                                <div class="d-flex flex-column">
                                                                    <span class="card-text fw-light fs-6 mb-0 text-muted">Current bid</span>
                                                                    <span class="card-text fs-6" style="color: green;">€ <?= number_format($item->get_max_bid(), 2, ",", " ") ?><span>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <span class="card-text text-secondary fw-bold"><i class="bi bi-clock"></i> Closed</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php 
                    }
                    ?>
                </div>
            </div>
            

            <div class="sold_items">
                <h3 class="text-blue"><?= count($sold_items)>0 ? "Sold Items" : "" ?></h3>

                <!-- tableau contenant les cartes des items -->
                <div class="container mt-4" style="margin-bottom: 150px;">
                    <div class="row text-white g-3">
                        <!-- chaque colonne a une largeur de 3 car 12 max par row, donc pour afficher 4 items, faut 12/4 = 3 par items -->
                        <?php
                        foreach ($sold_items as $item) { ?>
                                <div class="col-3">
                                    <a href="item/open/<?= $item->id?>/from-items" style="text-decoration: none;">
                                        <div class="card" style="width: 18rem;">    
                                            <img <?= ($item->get_path()=="") ? "src=uploads/items/default.jpg" :   "src=".ImageUtils::get_thumbnail_path($item->get_path())?> class="card-img-top" alt="image item">
                                            <div class="card-img-overlay">
                                                <div class="float-end d-flex flex-column gap-2 h-50">
                                                    <?php 
                                                        if ($item->starting_bid != null){?>
                                                            <span class="badge" style="background-color: #f59e0c ; color: white;"><i class="ti ti-gavel"></i> Auction</span>
                                                    <?php } 
                                                        if ($item->buy_now_price != null){?>
                                                            <span class="badge text-bg-primary mb-auto"><i class="bi bi-bag"></i> Buy Now</span>
                                                        <?php } 
                                                        if ($item->get_nomber_of_pictures() > 1) {
                                                        ?>    
                                                            <span class="badge rounded-pill text-bg-dark mt-auto"><i class="bi bi-images"></i> <?= $item->get_nomber_of_pictures()?> images</span>
                                                        <?php }?>    
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text fw-bolder mb-0"><?=$item->title?></p>
                                                <p class="card-text fw-light fs-6">By <?= User::get_full_name_by_id($item->owner)?></p>
                                                <div class="container">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <?php if ($item->buy_now_price == null){?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #f59e0c;">€ <?= number_format($item->starting_bid, 2, ",", " ")?></p>
                                                            <?php }else{?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #397deb;">€ <?= number_format($item->buy_now_price, 2, ",", " ")?></p>
                                                            <?php }?>
                                                        </div>
                                                        <?php if ($item->get_max_bid() != NULL && $item->starting_bid != null) { ?>
                                                            <div class="col-md-6 text-end">
                                                                <div class="d-flex flex-column">
                                                                    <span class="card-text fw-light fs-6 mb-0 text-muted">Current bid</span>
                                                                    <span class="card-text fs-6" style="color: green;">€ <?= number_format($item->get_max_bid(), 2, ",", " ") ?><span>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <span class="card-text text-secondary fw-bold"><i class="bi bi-clock"></i> Closed</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php 
                    }
                    ?>
                </div>
            </div>
        </main>
        <footer> 
            <?php require 'navbar_user.php'; ?>
        </footer>
    </body>
</html>