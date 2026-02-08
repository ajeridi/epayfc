<div class="col-auto">
    <a href="item/open/<?= $item->id ?>/from-browse" style="text-decoration: none;">
        <div class="card" style="width: 18rem;">
            <img <?= ($item->get_path() == "") ? "src=uploads/items/default.jpg" :   "src=" . $item->get_thumbnail_path() ?> class="card-img-top" style="height: 175px; object-fit:cover;" alt="image item">
            <div class="card-img-overlay">

                <?php if ($user != false) {
                    if ($item->is_highest_bidder($user->get_id())) { ?>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-trophy"></i> Highest Bidder</span>
                    <?php } else if ($item->has_bidder($user->get_id())) { ?>
                        <span class="badge rounded-pill text-bg-secondary"><i class="ti ti-gavel"></i> Bidder</span>
                <?php }
                } ?>
                <div class="float-end d-flex flex-column gap-2" style="height: 150px;">
                    <?php
                    if ($item->starting_bid != null) { ?>
                        <span class="badge rounded-pill" style="background-color: #f59e0c ; color: white;"><i class="ti ti-gavel"></i> Auction</span>
                    <?php  }
                    if ($item->starting_bid == null or $item->buy_now_price !== null) { ?>
                        <span class="badge rounded-pill text-bg-primary mb-auto"><i class="bi bi-bag"></i> Buy Now</span>
                    <?php } ?>
                    <?php if ($item->get_number_of_pictures() > 1) {
                    ?>
                        <span class="badge rounded-pill text-bg-dark mt-auto"><i class="bi bi-images"></i> <?= $item->get_number_of_pictures() ?> images</span>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text fw-bolder mb-0"><?= $item->title ?></p>
                <p class="card-text fw-light fs-6">By <?= $item->pseudo ?></p>
                <div class="row align-items-center mb-2">
                    <div class="col-md-6 text-start">
                        <?php if ($item->buy_now_price == NULL) { ?>
                            <p class="card-text fw-bolder fs-5" style="color: #f59e0c;">€ <?= number_format($item->starting_bid, 2, ",", " ") ?></p>
                        <?php } else { ?>
                            <p class="card-text fw-bolder fs-5" style="color: #397deb;">€ <?= number_format($item->buy_now_price, 2, ",", " ") ?></p>
                        <?php } ?>
                    </div>
                    <?php if ($item->get_max_bid() != NULL) { ?>
                        <div class="col-md-6 text-end">
                            <div class="d-flex flex-column">
                                <span class="card-text fw-light fs-6 mb-0 text-muted">Current bid</span>
                                <span class="card-text fs-6" style="color: green;">€ <?= number_format($item->bids['0']->get_amount(), 2, ",", " ") ?></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php if ($item->is_open_item()) { ?>
                    <span class="card-text <?= (str_contains($item->get_period(), 'd')) ? "text-success" : ((str_starts_with($item->get_period(), '0h')) ? "text-danger" : "text-warning") ?> fw-bold"><i class="bi bi-clock"></i> <?= $item->get_period() ?> left</span>
                <?php } else { ?>
                    <span class="card-text text-secondary fw-bold"><i class="bi bi-clock"></i> Closed</span>
                <?php } ?>
            </div>
        </div>
    </a>
</div>