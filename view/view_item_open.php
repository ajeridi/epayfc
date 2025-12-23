<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <base href="<?= $web_root ?>">
         <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
         <header>
            <!-- Barre  -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark">
                <div class="container-fluid justify-content-center text-blue text-decoration-bold fw-bold fs-4 "> Item open 🛒
                </div>
            </nav>
            <main class="main-content-margins ">
                <div class="row g-0">
                   
                        <div class="col-sm-6 col-md-8 card border-0 margin: 1rem" >
                            <img src='<?= $item['picture_path'] ?>' class="card-img-top"  alt="item image">
                            <div class="card-body bg-dark text-light ">
                                <p class="card-text fs-3 "><?= $item['title']?></p>
                                <p class="card-text opacity-75 "><?= $item['description']?></p>
                                <p class="card-text opacity-75 ">Start: <?= $item['created_at']?></p>
                                <p class="card-text opacity-75 ">Ends:<?= $item['end_at'] ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="bg-dark container-fluid margin: 1rem text-light ">
                                <p class="opacity-75">Pricing</p>
                                <div class="container ">
                                    <div class="row">
                                        <div class="col">
                                            <p>Current Bid</p>
                                        </div>
                                        <div class="col text-success">
                                            <p> <i class="bi bi-currency-euro"></i> <?=$item['max_bid'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="container ">
                                    <div class="row">
                                        <div class="col">
                                            <p>Buy Now</p>
                                        </div>
                                        <div class="col">
                                            <p><i class="bi bi-currency-euro"></i><?=$item['buy_now_price'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>    
                            <div class="mb-3">
                            <div class="input-group border-1">
                                <span class="input-group-text bg-dark border-1 text-light">
                                    &#x20ac;
                                </span>
                                <input
                                    type="text"
                                    name="min_bid"
                                    class="form-control  bg-dark text-light border-1"
                                    value="<?= $item['max_bid']+1 ?>"
                                    required
                                >
                            </div>
                            <div class="d-grid grap2">
                                <button class="btn btn-success border-1" type="submit">Place Bid</button>
                                <button class="btn text-primary border-primary" type="submit">Buy NOw at <?= $item['buy_now_price'] ?></button>
                            </div>
                            
                        </div>
                        <div class="bg-dark container-fluid margin: 1rem text-light ">
                              <p class="opacity-75">Seller Information</p>
                             <p> <?= $ $item['owner'] ?></p>
                           
                        </div>
                        
                 </div>
            </main>
        </header>
    </body>

</html>