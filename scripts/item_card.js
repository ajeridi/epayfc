// fonction qui recoit un item json et retourne une carte html de l'item
function createItemCard(item, encoded_criteria = "") {
    let html = '<div class="col-auto">';
    let linkCriteria = encoded_criteria ? '/' + encoded_criteria : '';
    html += '<a href="item/open/' + item.id + (from == "" ? '' : '/' + from) + linkCriteria + '" style="text-decoration: none;">';
    html += '<div class="card" style="width: 18rem;">';
    html += '<img ' + (!item.pictures || item.pictures.length === 0 || item.pictures[0].path === "" ? "src=uploads/items/default.jpg" : "src=" + item.pictures[0].thumbnail_path) + ' class="card-img-top" style="height: 175px; object-fit:cover;" alt="image item">';
    html += '<div class="card-img-overlay">';

    // Calcul 100% local en mémoire avec le user global et les bids déjà chargées
    if (user != false && item.bids && item.bids.length > 0) {
        let is_highest_bidder = (item.bids[0].owner == user.id);
        let has_bidder = item.bids.some(b => b.owner == user.id);

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
        html += '</div></div>';
    }
    html += '</div>';

    // Lecture directe des propriétés calculées côté PHP
    if (item.is_open) {
        let item_period = item.period || "";
        html += '<span class="card-text ' + (item_period.includes('d') ? "text-success" : ((item_period.startsWith('0h')) ? "text-danger" : "text-warning")) + ' fw-bold"><i class="bi bi-clock"></i> ' + item_period + ' left</span>';
    } else {
        html += '<span class="card-text text-secondary fw-bold"><i class="bi bi-clock"></i> Closed</span>';
    }
    html += '</div>';
    html += '</div>';
    html += '</a>';
    html += '</div>';
    return html;
}