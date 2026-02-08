<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>🛒 <?= $user->get_pseudo() ?>'s items</title>
    <base href="<?= $web_root ?>" ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-navy">
    <header>
        <!-- Barre au dessus de la page -->
        <nav class="navbar bg-navy-gray fixed-top" data-bs-theme="dark">
            <div class="container-fluid justify-content-center">
                <span class="navbar-brand mb-0 h1 text-blue"">My items <i class=" bi bi-cart4 fs-4 text-white"></i></span>
            </div>
        </nav>
    </header>
    <main class="main-content-margins" style="margin-top: 100px;">
         <div class="active_items float-start">
        <?php if (count($active_items)) { ?>
                <h3 class="text-blue">Active items</h3>
                <!-- tableau contenant les cartes des items -->
                <div class="container mt-4 mb-5">
                    <div class="row text-white g-4">
                        <?php
                        foreach ($active_items as $item) { 
                            require 'item_card.php';
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>
            <div class="closed_unsold_items">
                <h3 class="text-blue"><?= (count($closed_unsold_items) > 0) ?  "Closed Unsold Items" : "" ?></h3>
                <!-- tableau contenant les cartes des items -->
                <div class="container mt-4 mb-5">
                    <div class="row text-white g-4">
                        <?php
                        foreach ($closed_unsold_items as $item) { 
                            require 'item_card.php';
                        }
                        ?>
                    </div>
                </div>
                <div class="sold_items">
                    <h3 class="text-blue"><?= count($sold_items) > 0 ? "Sold Items" : "" ?></h3>
                    <!-- tableau contenant les cartes des items -->
                    <div class="container mt-4" style="margin-bottom: 150px;">
                        <div class="row text-white g-4">
                            <?php
                            foreach ($sold_items as $item) { 
                                require 'item_card.php';
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