<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>🛒 Browse items</title>
    <base href="<?= $web_root ?>">
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
                <span class="navbar-brand mb-0 h1 text-blue">Browse <i class="bi bi-cart4 fs-4 text-white"></i></span>
            </div>
        </nav>
    </header>
    <main style="margin-top: 100px; margin-bottom: 200px;">
        <?php if ($user != false) { ?>
            <div class="items_participated_in container">
                <h3 class="text-blue"> <?= (count($items_participated_in) > 0)  ? "Items I\n'm Participating In" : "" ?> </h3>

                <!-- tableau contenant les cartes des items -->
                <div class="mt-4 mb-5">
                    <div class="row text-white g-4">
                        <?php
                        foreach ($items_participated_in as $item) {
                            require 'item_card.php';
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="other-available-items container">
            <h3 class="text-blue"><?= ($user == false) ? "" : "Other" ?> Available Items</h3>

            <!-- tableau contenant les cartes des items -->
            <div class="mt-4">
                <div class="row text-white g-4">
                    <?php
                    foreach ($other_available_items as $item) {
                        require 'item_card.php';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <?php require 'navbar_user.php'; ?>
    </footer>
</body>

</html>