
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
     <form method="post" action="item/edit/<?=($action=="add") ? "": $info['id']  ?>">  
           <header>
         
            <nav class="navbar bg-navy-gray fixed-top" data-bs-theme="dark">
                <div class="container-fluid">
                    <a class="navbar-brand text-blue" href="item/<?=($action=="add") ? "browse" : "open/".$info['id']?>/from-items"><i class="bi bi-arrow-left"></i> </a>
                    <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue">Edit Item<i class=" bi bi-cart4 fs-4 text-white"></i></span>
                    <button type="submit" name="saveProfile" value="true" class="btn navbar-brand text-blue position-absolute top-50 end-0 translate-middle-y" ><i class="bi bi-floppy2-fill"></i></a>
                </div>
            </nav>
        </header>
    <body >
        <main class="container mb-3"  style="margin-top: 100px;" >
    

            <div class="container fw-bold ">
                <div class= "card bg-dark mb-3  border-0 overflow-hidden">
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Basic Information</div>
                    <div class="card-body text-light border-0 ">
                        
                            <div class="mb-3">
                                <label for="itemtitle" class="form-label" >Item Title *</label>
                                <input type="text" class="form-label d-block  text-bg-dark"  style ="padding-right:80%" id="title" name="title" <?= ($action=="add") ? "placeholder=\"EX:Iphon 13 Pro Max 256GB\"" : "value=\"".$info['title']."\"" ?>>
                                <div class="errors">
                                <?php if(count($errors['title'])){?>
                                   <ul>
                                    <?php
                                    foreach($errors['title'] as $str){?>
                                        <li><?= $str ?></li>

                                       <?php } 
                                  } ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-label d-block  text-bg-dark" id="description" name="description" style ="padding-right:80%" <?= ($action=="add") ? "placeholder=\"Describe your item in detail...\"  rows=\"3\">" : "rows=\"3\">".$info['description']?></textarea>
                                 <div class="errors">
                                <?php if(count($errors['description'])){?>
                                   <ul>
                                    <?php
                                    foreach($errors['description'] as $str){?>
                                        <li><?= $str ?></li>

                                       <?php } 
                                  } ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="saledays" class="form-label">Sale Duration (days)*</label>
                                <input type="text" class="form-label d-block  text-bg-dark" style ="padding-right:80%" id="duration_days" name="duration_days" <?=($action=="add") ? "value=\"7\"" : "value=".$info['duration_days'] ?>>
                                <div class="errors">
                                <?php if(count($errors['duration_days'])){?>
                                   <ul>
                                    <?php
                                    foreach($errors['duration_days'] as $str){?>
                                        <li><?= $str ?></li>

                                       <?php } 
                                  } ?>
                                    </ul>
                                </div>

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
                                        <input type="text" class="form-label d-block  text-bg-dark" style ="padding-right:80%" id="starting_bid" name="starting_bid" <?= ($action=="add" || $info['starting_bid']==null) ? "placeholder=\"e.g. 50\"": "value=".$info['starting_bid'] ?>>
                                         <div class="errors">
                                            <?php if(count($errors['starting_bid'])){?>
                                            <ul>
                                                <?php
                                                foreach($errors['starting_bid'] as $str){?>
                                                    <li><?= $str ?></li>

                                                <?php } 
                                            } ?>
                                                </ul>
                                </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="purchaseprice" class="form-label text-white">Instant Purchase Price</label><a class="text-light">(optional)</a>
                                        <input type="text" class="form-label d-block text-bg-dark" style ="padding-right:80%" id="buy_now_price" name="buy_now_price" <?= ($action=="add" || $info['buy_now_price']==null) ? "placeholder=\"e.g. 200\"": "value=".$info['buy_now_price']?> >
                                         <div class="errors">
                                            <?php if(count($errors['buy_now_price'])){?>
                                            <ul>
                                                <?php
                                                foreach($errors['buy_now_price'] as $str){?>
                                                    <li><?= $str ?></li>

                                                <?php } 
                                            } ?>
                                                </ul>
                                         </div>
                                    </div>
                                
                            </div>
                        </div>
                        <div class="card bg-dark mb-3 border-success overflow-hidden">
                            <div class="card-header border-1 border-success bg-succes text-success" style="--bs-bg-opacity: .1"> Option 2: Direct Sale</div>
                            <div class="card-body border-0">
                               
                                    <div class="mb-3">
                                        <label for="saleprice" class="form-label text-white">Sale Price</label>
                                        <input type="text" class="form-label d-block text-bg-dark" style ="padding-right:80%" id="saleprice" name="saleprice" <?= ($action=="add" || $info['saleprice']==null) ? "placeholder=\"e.g. 150\"": "value=".$info['saleprice'] ?>>
                                         <div class="errors">
                                                <?php if(count($errors['saleprice'])){?>
                                                <ul>
                                                    <?php
                                                    foreach($errors['saleprice'] as $str){?>
                                                        <li><?= $str ?></li>

                                                    <?php } 
                                                } ?>
                                                    </ul>
                                        </div>
                                    </div>
                               
                            </div>
                        </div>
                    </div>


                </div>

            </div>  
           
          </form> 
        </main>
    </body>   


</html>