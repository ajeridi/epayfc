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

    <script src="lib\jquery-4.0.0.min.js" type="text/javascript"></script>
    <script>
        let search_box, search_input;
        let items_participated_in = <?= json_encode($items_participated_in) ?>;
        let other_available_items = <?= json_encode($other_available_items) ?>;
        let items_participated_in_container, other_available_items_container;
        let user = <?= json_encode($user) ?>;
        let from = <?= json_encode($from) ?>;

        $(function() {
            search_box = $("#search"); // on prend la reference vers l'element qui va contenir la search bar
            createSearchBar(); // on cree la searchbar d'abord, puis apres on prend la ref vers le input
            search_input = $("#search_input");
            items_participated_in_container = $("#items_participated_in");
            other_available_items_container = $("#other_available_items");
            // creation des carte des items
            displayItems(search_input.val());


            search_input.on('input', async function() {
                displayItems(search_input.val());
            })
        });

        async function displayItems(search_criteria) {
            items_participated_in_container.html("");
            other_available_items_container.html("");
            displayItemsParticipatedIn(search_criteria);
            displayOtherAvailableItems(search_criteria);
        }

        function createSearchBar() {
            let html = '<span class="input-group-text" id="searchHelp"><i class="bi bi-search"></i></span><input id="search_input" type="text" class="form-control d-block" name="search" placeholder="Search items..." aria-label="Search" aria-describedby="searchHelp" value="">';
            search_box.html(html);
        }

        async function displayItemsParticipatedIn(search_criteria) {
            // va display les carte des items
            for (let item of items_participated_in) { // for respecte mieux le await que le forEach
                if (search_criteria === "" || item.title.toLowerCase().includes(search_criteria.toLowerCase()) || item.description.toLowerCase().includes(search_criteria.toLowerCase()) || item.pseudo.toLowerCase().includes(search_criteria.toLowerCase())) {
                    let card = await createItemCard(item);
                    items_participated_in_container.append(card);
                }
            }
        }

        async function displayOtherAvailableItems(search_criteria) {
            for (let item of other_available_items) {
                if (search_criteria === "" || item.title.toLowerCase().includes(search_criteria.toLowerCase()) || item.description.toLowerCase().includes(search_criteria.toLowerCase()) || item.pseudo.toLowerCase().includes(search_criteria.toLowerCase())) {
                    let card = await createItemCard(item);
                    other_available_items_container.append(card);
                }
            }
        }

        // ca affichait promise parce que je ne respectais pas les await


        // fonction qui recoit un item json et retourne une carte html de l'item
        async function createItemCard(item) {

            let html = '<div class="col-auto">';
            html += '<a href="item/open/' + item.id + (from == "" ? '' : '/' + from) + '" style="text-decoration: none;">';
            html += '<div class="card" style="width: 18rem;">';
            html += '<img ' + (!item.pictures || item.pictures.length === 0 || item.pictures[0].path === "" ? "src=uploads/items/default.jpg" : "src=" + item.pictures[0].path) + ' class="card-img-top" style="height: 175px; object-fit:cover;" alt="image item">';
            html += '<div class="card-img-overlay">';
            if (user != false) {
                let is_highest_bidder = await $.get("item/is_highest_bidder_service/" + item.id + "/" + user.id);
                let has_bidder = await $.get("item/has_bidder_service/" + item.id + "/" + user.id);
                if (is_highest_bidder) {
                    html += '<span class="badge rounded-pill text-bg-success"><i class="bi bi-trophy"></i> Highest Bidder</span>';
                } else if (has_bidder) {
                    html += '<span class="badge rounded-pill text-bg-secondary"><i class="ti ti-gavel"></i> Bidder</span>';
                }
            }
            html += '<div class="float-end d-flex flex-column gap-2" style="height: 150px;">';
            if (item.starting_bid != null) {
                html += '<span class="badge rounded-pill" style="background-color: #f59e0c ; color: white;"><i class="ti ti-gavel"></i> Auction</span>';
            }
            if (item.starting_bid == null || item.buy_now_price != null) {
                html += '<span class="badge rounded-pill text-bg-primary mb-auto"><i class="bi bi-bag"></i> Buy Now</span>';
            }
            if (item.pictures && item.pictures.length > 1) {
                html += '<span class="badge rounded-pill text-bg-dark mt-auto"><i class="bi bi-images"></i> ' + item.pictures.length + ' images</span>';
            }
            html += '</div></div>';
            html += '<div class="card-body">';
            html += '<p class="card-text fw-bolder mb-0">' + item.title + '</p>';
            html += '<p class="card-text fw-light fs-6">By ' + item.pseudo + '</p>';
            html += '<div class="row align-items-center mb-2">';
            html += '<div class="col-md-6 text-start">';
            if (item.buy_now_price == null) {
                html += '<p class="card-text fw-bolder fs-5" style="color: #f59e0c;">€ ' + doubleToStringFormatted(item.starting_bid) + '</p>';
            } else {
                html += '<p class="card-text fw-bolder fs-5" style="color: #397deb;">€ ' + doubleToStringFormatted(item.buy_now_price) + '</p>';
            }
            html += '</div>';
            if (item.bids && item.bids.length > 0 && item.bids[0].amount != null) {
                let max_bid = item.bids[0].amount;
                html += '<div class="col-md-6 text-end"><div class="d-flex flex-column">';
                html += '<span class="card-text fw-light fs-6 mb-0 text-muted">Current bid</span>';
                html += '<span class="card-text fs-6" style="color: green;">€ ' + doubleToStringFormatted(max_bid) + '</span>';
                html += '</div></div>'
            }
            html += '</div>';
            let is_open_item = await $.get("item/is_open_item_service/" + item.id);
            if (is_open_item) {
                let item_period = await $.get("item/get_item_period_service/" + item.id);
                html += '<span class="card-text ' + (item_period.includes('d') ? "text-success" : ((item_period.startsWith('0h')) ? "text-danger" : "text-warning")) + ' fw-bold"><i class="bi bi-clock"></i> ' + item_period + ' left</span>';
            } else {
                html += '<span class="card-text text-secondary fw-bold"><i class="bi bi-clock"></i> Closed</span>';
            }
            html += '</div>'; // card-body
            html += '</div>'; // card
            html += '</a>';
            html += '</div>'; // col-auto
            return html;
        }


        function doubleToStringFormatted(number) {
            return number.toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
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

        <!-- Barre de recherche -->
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