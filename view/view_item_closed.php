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
            <!-- Barre  -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark"> <div class="container-fluid justify-content-center text-blue text-decoration-bold fw-bold fs-4 "> Sales 🛒 </div> </nav>
    </header>
     <body>
        <main class="container-sm mt-3 text-white text-bold  " style="margin:0;max-width: 100%;">
            <div class="container mb-3" style="max-width: 100%;">
                <div class="row ">
                    <div class="col "  >
                    <h5>  Completed Sales </h5>
                    <p class="card-text small">A snapshot of the deals you've wrapped up.</p>
                    </div>
                <div class=" col-1 small text-bg-success text-center h-25 " style="border-radius:25px;height:auto;width: auto;"><?= $info['nbr'] ?> sales</div>    
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 mb-3 mb-sm-0 ">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="text-light">TOTAL REVENUE</h1>
                            <p><?= $info['total'] ?></p>
                            <p>Across <?= $info['nbr'] ?> sales</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-3 mb-sm-0 ">    
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="text-light">Average Ticket</h1>
                            <p><?= $info['avg'] ?></p>
                            <p>Across <?= $info['nbr'] ?> sales</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-3 mb-sm-0 ">   
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="text-light">Loyal Bider</h1>
                            <p>Marc</p>
                            <p>Across 2 sales</p>
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
                                    <a href="item/open/<?= $item['id']?>/from-closed" style="text-decoration: none;">
                                        <div class="card border-0" style="width: 18rem;">
                                            <div class="card-head">    
                                            <img src='<?=Item::get_picture_path($item['id'],1)?>' class="card-img-top" alt="image item">
                                            <div class="card-img-overlay">
                                                <div class="float-end d-flex flex-column gap-2 h-50">
                                                    <?php 
                                                        if ($item['is_auction'] == 1){?>
                                                            <span class="badge rounded-pill" style="background-color: #f59e0c ; color: white;"><i class="ti ti-gavel"></i> Auction</span>
                                                    <?php  }
                                                        if ($item['is_direct_sale'] == 1 OR $item['has_buy_now'] == 1){?>
                                                            <span class="badge rounded-pill text-bg-primary mb-auto"><i class="bi bi-bag"></i> Buy Now</span>
                                                        <?php } ?>    
                                                            <span class="badge rounded-pill text-bg-dark mt-auto"><i class="bi bi-images"></i> <?= $item['number_of_pictures'] ?> images</span>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="card-body bg-dark text-white">
                                                <p class="card-text fw-bolder mb-0"><?=$item['title']?></p>
                                                <p class="card-text fw-light fs-6">By <?= $item['pseudo']?></p>
                                                <div class="container">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <?php if ($item['buy_now_price'] == NULL){?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #f59e0c;">€ <?= $item['starting_bid']?></p>
                                                            <?php }else{?>
                                                                <p class="card-text fw-bolder fs-5" style="color: #397deb;">€ <?= $item['buy_now_price']?></p>
                                                            <?php }?>
                                                        </div>
                                                        <div class="col-md-6 text-end">
                                                            <div class="d-flex flex-column">
                                                                <span class="card-text fw-light fs-6 mb-0 text-muted">Current bid</span>
                                                                <span class="card-text fs-6" style="color: green;">€ <?= $item['amount'] ?><span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="card-text text-tertiary  fw-bold"><i class="bi bi-clock"></i> Closed</span>
                                            </div>
                                            <div class="card-footer text-light " style="background-color: #1e1231;" >
                                                <p><span><i></i>Final price € <?= $item["amount"] ?> </span></p>
                                                <p><span><i></i><?= $item["pseudo"] ?></span></p>
                                                <p><span><i class="bi bi-clock fs-3"></i>Closed on </span></p>

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