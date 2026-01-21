<!DOCTYPE html>
<html>
    <head>
         <meta charset="UTF-8">
        <title>🛒 Edit item </title>
        <base href="<?= $web_root ?>">
        <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">

    </head>
    <header>
        <header>
            <!-- Barre  -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark"> <div class="container-fluid justify-content-center text-blue text-decoration-bold fw-bold fs-4 "> Edit item 🛒 </div> </nav>
         </header>   

    </header>
    <body >
        <main class="container mb-3" >
            <br>
          <form method="post" action="item/edit_item/<?= $info['id'] ?>">  
            <div class="container fw-bold ">
                <div class= "card bg-dark mb-3  border-0 overflow-hidden">
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Basic Information</div>
                    <div class="card-body text-light border-0 ">
                        
                            <div class="mb-3">
                                <label for="itemtitle" class="form-label" >Item Title *</label>
                                <input type="text" class="form-control d-block  text-bg-dark"  style ="padding-right:80%" id="title" name="title"  value="<?= $info['title'] ?>">
                                <div class="errors">
                                <?php if(count($errors)){?>
                                   <ul>
                                    <?php
                                    foreach($errors as $str){?>
                                        <li><?= $str ?></li>

                                       <?php } 
                                  } ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-label d-block  text-bg-dark" id="description" name="description" style ="padding-right:80%"   rows="3"><?= $info['description'] ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="saledays" class="form-label">Sale Duration (days)*</label>
                                <input type="text" class="form-label d-block  text-bg-dark" style ="padding-right:80%" id="duration_days" name="duration_days" value=<?= $info['duration_days'] ?>>
                            </div>
                        
                    </div>    
                </div>
            </div>  
            <div class="container fw-bold">
                <div class="card bg-dark mb-3  border-0 overflow-hidden" >
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Sale Type</div>
                    <div class="card-body text-white border-0 ">
                        <div class="card bg-dark mb-3  border-1 border-primary overflow-hidden" >
                            <div class="card-header border-1 border-primary bg-primary text-white" style="--bs-bg-opacity: .1">Option 1:Auction</div>
                            <div class="card-body  border-0  ">
                               
                                    <div class="mb-3">
                                        <label for="startingbid" class="form-label text-white">Starting Bid</label>
                                        <input type="text" class="form-label d-block  text-bg-dark" style ="padding-right:80%" id="starting_bid" name="starting_bid"  value="<?= $info['starting_bid'] ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="purchaseprice" class="form-label text-white">Instant Purchase Price</label><a class="text-light">(optional)</a>
                                        <input type="text" class="form-label d-block text-bg-dark" style ="padding-right:80%" id="buy_now_price" name="buy_now_price" <?= ($info['starting_bid'] == 0) ? "placeholder=".$info['buy_now_price'] : "value=".$info['buy_now_price'] ?> ?> >
                                    </div>
                                
                            </div>
                        </div>
                        <div class="card bg-dark mb-3 border-success overflow-hidden">
                            <div class="card-header border-1 border-success bg-succes text-success" style="--bs-bg-opacity: .1"> Option 2: Direct Sale</div>
                            <div class="card-body border-0">
                               
                                    <div class="mb-3">
                                        <label for="saleprice" class="form-label text-white">Sale Price</label>
                                        <input type="text" class="form-label d-block text-bg-dark" style ="padding-right:80%" id="saleprice" name="saleprice" <?= ($info['starting_bid'] == 0) ? "value=".$info['buy_now_price'] : "placeholder=".$info['buy_now_price'] ?>>
                                    </div>
                               
                            </div>
                        </div>
                    </div>


                </div>

            </div>  
            <button type="submit">valider</button>
          </form> 
        </main>
    </body>   


</html>