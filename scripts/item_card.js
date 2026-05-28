// fonction qui recoit un item json et retourne une carte html de l'item
async function createItemCard(item) {
            let html = '<div class="col-auto">';
            let criteria = await $.getJSON("item/get_encoded_data_service/" + (search_input.val() == "" ? "-1" : search_input.val()) + "/" + category_list.val()); // encodage du critere de recherche et du filtre
            html += '<a href="item/open/' + item.id + (from == "" ? '' : '/' + from) + '/' + criteria.encoded_criteria + '" style="text-decoration: none;">';
            html += '<div class="card" style="width: 18rem;">';
            html += '<img ' + (!item.pictures || item.pictures.length === 0 || item.pictures[0].path === "" ? "src=uploads/items/default.jpg" : "src=" + item.pictures[0].thumbnail_path) + ' class="card-img-top" style="height: 175px; object-fit:cover;" alt="image item">';
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