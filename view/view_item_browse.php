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

    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script src="scripts/item_card.js" type="text/javascript"></script>
    <script src="scripts/search_bar_category_filter.js" type="text/javascript"></script>
    <script src="scripts/helper.js" type="text/javascript"></script>
    <script>
        let search_box, search_input, category_list;
        let items_participated_in = <?= json_encode($items_participated_in) ?>;
        let other_available_items = <?= json_encode($other_available_items) ?>;
        let items_participated_in_container, other_available_items_container;
        let user = <?= json_encode($user) ?>;
        let from = <?= json_encode($from) ?>;
        let search_criteria = <?= json_encode($search_criteria) ?>;
        let selected_category = <?= json_encode($selected_category) ?>;
        let categories = <?= json_encode($categories) ?>;

        $(function() {
            search_box = $("#search"); // on prend la reference vers l'element qui va contenir la search bar
            createSearchBar(); // on cree la searchbar d'abord, puis apres on prend la ref vers le input
            search_input = $("#search_input");
            createCategoryFilter(); // creation du filtre de categorie
            category_list = $("#category_list");

            items_participated_in_container = $("#items_participated_in");
            other_available_items_container = $("#other_available_items");
            

            // si critere de recherche existant, on remplit le champs
            if (search_criteria != ""){ // si pas de critere de recherche , search_criteria vaut "" si premiere fois sur browse ou -1, si navigation a eu lieu
                search_input.val(search_criteria);
            }
            
            if (selected_category != ""){
                category_list.val(selected_category);
            }
            
            // creation des carte des items
            displayItems(search_input.val(), category_list.val());


            // filtrage lors du input dans la barre de recherche
            search_input.on('input', async function() {
                displayItems(search_input.val(), category_list.val());
            })

            //filtrage lors de la selection d'une categorie
            category_list.on('change', function(){
                displayItems(search_input.val(), category_list.val());
            })
        });

        async function displayItems(search_criteria, selected_category) {
            items_participated_in_container.html("");
            other_available_items_container.html("");
            displayItemsParticipatedIn(search_criteria, selected_category);
            displayOtherAvailableItems(search_criteria, selected_category);
        }

        async function displayItemsParticipatedIn(search_criteria, selected_category) {
            // va display les carte des items
            for (let item of items_participated_in) { // for respecte mieux le await que le forEach
                if ((search_criteria === "" || item.title.toLowerCase().includes(search_criteria.toLowerCase()) || item.description.toLowerCase().includes(search_criteria.toLowerCase()) || item.pseudo.toLowerCase().includes(search_criteria.toLowerCase()))
                && hasCategory(item, selected_category)) {
                    let card = await createItemCard(item);
                    items_participated_in_container.append(card);
                }
            }
            if (items_participated_in_container.html() === ""){
                items_participated_in_container.html('<p>No items found</p>');
            }
        }

        async function displayOtherAvailableItems(search_criteria, selected_category) {
            for (let item of other_available_items) {
                if ((search_criteria === "" || item.title.toLowerCase().includes(search_criteria.toLowerCase()) || item.description.toLowerCase().includes(search_criteria.toLowerCase()) || item.pseudo.toLowerCase().includes(search_criteria.toLowerCase()))
                    && hasCategory(item, selected_category)) {
                    let card = await createItemCard(item);
                    other_available_items_container.append(card);
                }
            }
            if (other_available_items_container.html() === ""){
                other_available_items_container.html('<p>No items found</p>');
            }
        }

        

        // ca affichait promise parce que je ne respectais pas les await (remarque)
        
    </script>
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

        <!-- Barre de recherche  et filtre de categories-->
        <div id="search" class="input-group mb-3 w-75 container" data-bs-theme="dark">
        </div>

        <?php if ($user != false) { ?>
            <div class="items_participated_in container">
                <h3 class="text-blue"> <?= (count($items_participated_in) > 0)  ? "Items I\n'm Participating In" : "" ?> </h3>

                <!-- tableau contenant les cartes des items -->
                <div class="mt-4 mb-5">
                    <div id="items_participated_in" class="row text-white g-4">
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
                <div id="other_available_items" class="row text-white g-4">
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