<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>🛒 <?= $action ?> </title>
    <base href="<?= $web_root ?>">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="scripts/item_add_edit.js" type="text/javascript"></script>
    <script>
        const info = <?= json_encode($info) ?>;
    </script>

</head>

<body style="padding-bottom: 200px;">

    <!-- Modal -->
    <div class="modal fade" id="myModal" data-bs-theme="dark" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Unsaved changes</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You have unsaved changes. Are you sure you want to leave anyway ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="leave_button" type="button" class="btn btn-primary">Leave</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale pour les categories -->
    <div class="modal fade" id="categoriesModal" data-bs-theme="dark" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Category Limit</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You cannot select more than  3 categories.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- SI nouveau item, donc id = 0, le submit se fait vers item/add, sinon vers item/edit/id[/search_criteria]-->
    <header>
        <nav class="navbar bg-navy-gray fixed-top" data-bs-theme="dark">
            <div class="container-fluid">
                <!-- SI nouveau item, donc id = 0, redirection vers item/browse, sinon redirection vers item/open/id/from-items[/search_criteria] -->
                <a id="back_link" class="navbar-brand text-blue" href="<?= ($info['id'] == 0) ? "item/browse" :  $back_url_to_open ?>"><i class="bi bi-arrow-left"></i> </a>
                <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue"><?= $action ?> <i class=" bi bi-cart4 fs-4 text-white"></i></span>
                <button id="save_btn" type="submit" form="myForm" name="saveProfile" value="true" class="btn navbar-brand text-blue position-absolute top-50 end-0 translate-middle-y"><i class="bi bi-floppy2-fill"></i></button>
            </div>
        </nav>
    </header>
    <main class="container mb-3" style="margin-top: 100px;">
        <form id="myForm" method="post" action="item/<?= ($info['id'] == 0) ? "add" : "edit/" . $info['id'] . ($search_criteria == "" ? "" : "/" . $search_criteria) ?>">
            <div class="container fw-bold ">
                <div class="card bg-dark mb-3  border-0 overflow-hidden">
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Basic Information</div>
                    <div class="card-body text-light border-0 ">

                        <div class="mb-3">
                            <label for="itemtitle" class="form-label">Item Title *</label>
                            <input type="text" class="form-control d-block  text-bg-dark" style="padding-right:80%" id="itemtitle" name="title" <?= ($info['title'] == null) ? "placeholder=\"EX:Iphon 13 Pro Max 256GB\"" : "value=\"" . $info['title'] . "\"" ?>>
                            <div id="errTitle" class="errors">
                                <?php if (count($errors['title'])) { ?>
                                    <ul>
                                        <?php
                                        foreach ($errors['title'] as $str) { ?>
                                            <li><?= $str ?></li>

                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control d-block  text-bg-dark" id="description" name="description" style="padding-right:80%" <?= ($info['description'] == null) ? "placeholder=\"Describe your item in detail...\"  rows=\"3\">" : "rows=\"3\">" . $info['description'] ?></textarea>
                                 <div id="errDescription" class="errors">
                                    <?php if (count($errors['description'])) { ?>
                                    <ul>
                                        <?php
                                        foreach ($errors['description'] as $str) { ?>
                                            <li><?= $str ?></li>

                                        <?php } ?>
                                    </ul>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="duration_days" class="form-label">Sale Duration (days)*</label>
                                <input  type="text" class="form-control d-block  text-bg-dark" style ="padding-right:80%" id="duration_days" name="duration_days" <?= ($info['duration_days'] == 0) ? "value=\"7\"" : "value=" . $info['duration_days'] ?>>
                                <div id="errDuration" class="errors">
                                    <?php if (count($errors['duration_days'])) { ?>
                                    <ul>
                                            <?php
                                            foreach ($errors['duration_days'] as $str) { ?>
                                                <li><?= $str ?></li>

                                            <?php } ?>
                                        </ul>
                                    <?php } ?>
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
                                        <label for="starting_bid" class="form-label text-white">Starting Bid</label>
                                        <input id="starting_bid" type="text" class="form-control d-block  text-bg-dark" style ="padding-right:80%"  name="starting_bid" <?= ($info['starting_bid'] == null) ? "placeholder=\"e.g. 50.00\"" : "value=" . $info['starting_bid'] ?>>
                                         <div id="errStartingBid" class="errors">
                                            <?php if (count($errors['starting_bid'])) { ?>
                                                <ul>
                                                    <?php
                                                    foreach ($errors['starting_bid'] as $str) { ?>
                                                        <li><?= $str ?></li>

                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="buy_now_price" class="form-label text-white">Instant Purchase Price</label><a class="text-light">(optional)</a>
                                        <input  type="text" class="form-control d-block text-bg-dark" style ="padding-right:80%" id="buy_now_price" name="buy_now_price" <?= ($info['buy_now_price'] == null) ? "placeholder=\"e.g. 200.00\"" : "value=" . $info['buy_now_price'] ?> >
                                         <div id="errBuyNow" class="errors">
                                            <?php if (count($errors['buy_now_price'])) { ?>
                                                <ul>
                                                    <?php
                                                    foreach ($errors['buy_now_price'] as $str) { ?>
                                                        <li><?= $str ?></li>

                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                         </div>
                                    </div>
                                
                            </div>
                        </div>
                        <div class="card bg-dark mb-3 border-success overflow-hidden">
                            <div class="card-header border-1 border-success bg-succes text-success" style="--bs-bg-opacity: .1"> Option 2: Direct Sale</div>
                            <div class="card-body border-0">
                               
                                <div class="mb-3">
                                    <label for="saleprice" class="form-label text-white">Sale Price</label>
                                    <input  type="text" class="form-control d-block text-bg-dark" style ="padding-right:80%" id="saleprice" name="saleprice" <?= ($info['saleprice'] == null) ? "placeholder=\"e.g. 150.00\"" : "value=" . $info['saleprice'] ?>>
                                    <div id="errDirectSale" class="errors">
                                        <?php if (count($errors['saleprice'])) { ?>
                                        <ul>
                                            <?php
                                            foreach ($errors['saleprice'] as $str) { ?>
                                                <li class="ms-0"><?= $str ?></li>
                                            <?php } ?>
                                        </ul>
                                        <?php } ?>
                                    </div>
                                </div>     
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Partie categories -->
            <div class="container fw-bold ">
                <div class="card bg-dark mb-3  border-0 overflow-hidden">
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Categories</div>
                    <div class="card-body text-light border-0 ">
                        <div class="mb-3">
                            <p>You can select up to 3 categories.</p>
                            <?php 
                                foreach($categories as $category){ // https://wastedpotential.com/html-multi-checkbox-set-the-correct-way-to-group-checkboxes/ , https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/input/checkbox#using_checkbox_inputs
                                    $checked = in_array($category->get_id(), $info["categories"] ?? []); // regarde si l'item appartient a la categorie, pour check deja le checkbox
                                    echo '<input type="checkbox" name="chk_group[]" value="'.$category->get_id().'"'. ($checked ? 'checked' : '') .'/> '. $category->get_name().'<br />';
                                }
                            ?>

                            <div id="errCategories" class="errors">
                                <!-- Affichage des erreurs -->
                                <?php if (count($errors['categories'])) { ?> 
                                <ul class="errors">
                                    <?php
                                    foreach ($errors['categories'] as $str) { ?>
                                        <li><?= $str ?></li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                            </div>
                    </div>    
                </div>
            </div>  
        </form> 
    </main>
    <footer>
        <?php require 'navbar_user.php'; ?>
    </footer>
</body>   
</html>