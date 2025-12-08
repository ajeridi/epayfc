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
                    <span class="navbar-brand mb-0 h1 text-blue"">Browse <i class="bi bi-cart4 fs-4 text-white"></i></span>
                </div>
            </nav>
        </header>
        <main class="main-content-margins">
            <div class="items_participated_in float-start">
                <h3 class="text-blue">Items I'm Participating In</h3>

                <!-- tableau contenant les cartes des items -->
                <div class="container mt-4 mb-5">
                    <div class="row text-white g-3">
                        <!-- chaque colonne a une largeur de 3 car 12 max par row, donc pour afficher 4 items, faut 12/4 = 3 par items -->
                        <?php
                        foreach ($items_participated_in as $item) { ?>
                                <div class="col-3">
                                    <div class="card" style="width: 18rem;">    
                                        <img src='<?=$item['picture_path']?>' class="card-img-top" alt="image item">
                                        <div class="card-img-overlay">
                                            <span class="badge text-bg-secondary"><i class="ti ti-gavel"></i> Bidder</span>
                                            <div class="float-end d-flex flex-column gap-2 h-50">
                                                <?php 
                                                    if ($item['is_auction'] == 1){?>
                                                        <span class="badge" style="background-color: #f59e0c ; color: white;"><i class="ti ti-gavel"></i> Auction</span>
                                                <?php  }
                                                    if ($item['is_direct_sale'] == 1){?>
                                                        <span class="badge text-bg-primary mb-auto"><i class="bi bi-bag"></i> Buy Now</span>
                                                    <?php } ?>    
                                                        <span class="badge text-bg-dark mt-auto"><i class="bi bi-images"></i> <?= $item['number_of_pictures'] ?> images</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text fw-bolder mb-0"><?=$item['title']?></p>
                                            <p class="card-text fw-light fs-6">By <?= $item['owner_full_name']?></p>
                                            <?php if ($item['max_bid'] == NULL){?>
                                                <p class="card-text fw-bolder fs-5"><?= $item['starting_bid']?></p>
                                            <?php }else{?>
                                                <p class="card-text fw-bolder fs-5"><?= $item['starting_bid']?></p>
                                            <?php }?>

                                        </div>
                                    </div>
                                </div>
                            <?php 
                    }
                    ?>
                </div>
            </div>

            <div class="other-available-items">
                <h3 class="text-blue">Other Available Items</h3>

                <!-- tableau contenant les cartes des items -->
                <div class="container mt-4 mb-5">
                    <div class="row text-white g-3">
                        <!-- chaque colonne a une largeur de 3 car 12 max par row, donc pour afficher 4 items, faut 12/4 = 3 par items -->
                        <?php
                        foreach ($other_available_items as $item) { ?>
                                <div class="col-3">
                                    <div class="card" style="width: 18rem;">    
                                        <img src='<?=$item['picture_path']?>' class="card-img-top" alt="image item">
                                        <div class="card-img-overlay">
                                            <div class="float-end d-flex flex-column gap-2 h-50">
                                                <?php 
                                                    if ($item['is_auction'] == 1){?>
                                                        <span class="badge" style="background-color: #f59e0c ; color: white;"><i class="ti ti-gavel"></i> Auction</span>
                                                <?php } 
                                                    if ($item['is_direct_sale'] == 1){?>
                                                        <span class="badge text-bg-primary mb-auto"><i class="bi bi-bag"></i> Buy Now</span>
                                                <?php } ?>
                                                        <span class="badge text-bg-dark mt-auto"><i class="bi bi-images"></i> <?= $item['number_of_pictures'] ?> images</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text fw-bolder mb-0"><?=$item['title']?></p>
                                            <p class="card-text fw-light fs-6">By <?= $item['owner_full_name']?></p>
                                            <?php if ($item['max_bid'] == NULL){?>
                                                <p class="card-text fw-bolder fs-5"><?= $item['starting_bid']?></p>
                                            <?php }else{?>
                                                <p class="card-text fw-bolder fs-5"><?= $item['starting_bid']?></p>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                    }
                    ?>
                </div>
            </div>
        </main>

    </body>
</html>