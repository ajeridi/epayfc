<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>🛒 <?= $user->get_pseudo() ?>'s items</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="css/style.css">


    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script src="scripts/item_card.js" type="text/javascript"></script>
    <script src="scripts/search_bar_category_filter.js" type="text/javascript"></script>
    <script src="scripts/helper.js" type="text/javascript"></script>
    <script>
        let search_box, search_input, category_list;
        let active_items = <?= json_encode($active_items) ?>;
        let closed_unsold_items = <?= json_encode($closed_unsold_items) ?>;
        let sold_items = <?= json_encode($sold_items) ?>;
        let active_items_container, closed_unsold_items_container, sold_items_container;
        let user = <?= json_encode($user) ?>;
        let from = <?= json_encode($from) ?>;
        let search_criteria = <?= json_encode($search_criteria) ?>;
        let selected_category = <?= json_encode($selected_category) ?>;
        let categories = <?= json_encode($categories) ?>;

        $(async function() {
            search_box = $("#search"); // on prend la reference vers l'element qui va contenir la search bar
            createSearchBar(); // on cree la searchbar d'abord, puis apres on prend la ref vers le input
            search_input = $("#search_input");
            createCategoryFilter(); // creation du filtre de categorie
            category_list = $("#category_list");

            active_items_container = $("#active_items");
            closed_unsold_items_container = $("#closed_unsold_items");
            sold_items_container = $("#sold_items");

            if (search_criteria != "") {
                search_input.val(search_criteria);
            }

            if (selected_category != "") {
                category_list.val(selected_category);
            }

            // creation des carte des items
            await displayAllItemCategories(search_input.val(), category_list.val());

            search_input.on('input', async function() {
                await displayAllItemCategories(search_input.val(), category_list.val());
            })

            //filtrage lors de la selection d'une categorie
            category_list.on('change', function() {
                displayAllItemCategories(search_input.val(), category_list.val());
            })
        });

        async function displayAllItemCategories(search_criteria, selected_category) {
            await displayItems(search_criteria, selected_category, active_items, active_items_container);
            await displayItems(search_criteria, selected_category, closed_unsold_items, closed_unsold_items_container);
            await displayItems(search_criteria, selected_category, sold_items, sold_items_container);
        }

        async function displayItems(search_criteria, selected_category, items, container) {
            container.html("");
            for (let item of items) { // for respecte mieux le await que le forEach
                if ((search_criteria === "" || item.title.toLowerCase().includes(search_criteria.toLowerCase()) || item.description.toLowerCase().includes(search_criteria.toLowerCase()) || item.pseudo.toLowerCase().includes(search_criteria.toLowerCase())) &&
                    hasCategory(item, selected_category)) {
                    let card = await createItemCard(item);
                    container.append(card);
                }
            }
            if (container.html() === "") {
                container.html('<p>No items found</p>');
            }
        }
    </script>
</head>

<body class="bg-navy">
    <header>
        <!-- Barre au dessus de la page -->
        <nav class="navbar bg-navy-gray fixed-top" data-bs-theme="dark">
            <div class="container-fluid justify-content-center">
                <span class="navbar-brand mb-0 h1 text-blue">My items <i class=" bi bi-cart4 fs-4 text-white"></i></span>
            </div>
        </nav>
    </header>
    <main style="margin-top: 100px; margin-bottom: 200px;">

        <!-- Barre de recherche -->
        <div id="search" class="input-group mb-3 w-75 container" data-bs-theme="dark"></div>

        <div class="active_items container">
            <?php if (count($active_items)) { ?>
                <h3 class="text-blue">Active items</h3>
                <!-- tableau contenant les cartes des items -->
                <div class="mt-4 mb-5">
                    <div id="active_items" class="row text-white g-4">
                        <?php
                        foreach ($active_items as $item) {
                            require 'item_card.php';
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="closed_unsold_items container">
            <h3 class="text-blue"><?= (count($closed_unsold_items) > 0) ?  "Closed Unsold Items" : "" ?></h3>
            <!-- tableau contenant les cartes des items -->
            <div class="mt-4 mb-5">
                <div id="closed_unsold_items" class="row text-white g-4">
                    <?php
                    foreach ($closed_unsold_items as $item) {
                        require 'item_card.php';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="sold_items container">
            <h3 class="text-blue"><?= count($sold_items) > 0 ? "Sold Items" : "" ?></h3>
            <!-- tableau contenant les cartes des items -->
            <div class="mt-4">
                <div id="sold_items" class="row text-white g-4">
                    <?php
                    foreach ($sold_items as $item) {
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