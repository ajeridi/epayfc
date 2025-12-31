<!DOCTYPE html>
<html lang="en">
     <head>
        <meta charset="UTF-8">
        <title></title>
        <base href="<?= $web_root ?>">
         <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css?v=1">
    </head>
    <body class="app-bg">
         <header>
            <!-- Barre  -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark"> <div class="container-fluid justify-content-center text-blue text-decoration-bold fw-bold fs-4 "> Item open 🛒 </div> </nav>
           <main class="container-fluid px-3 bg-dark page-wrap">
  <div class="row g-3 align-items-start">

    <!-- GAUCHE -->
    <div class="col-12 col-lg-8">

      <!-- Card image + infos -->
      <div class="card card-dark border-0 overflow-hidden">
        <div class="ratio ratio-16x9 bg-black-30">
          <img src="<?= $item['picture_path'] ?>" class="w-100 h-100 object-fit-cover" alt="item image">
        </div>

        <div class="card-body">
          <div class="d-flex justify-content-between gap-3">
            <div>
              <div class="h5 mb-1"><?= $item['title'] ?></div>
              <div class="text-muted small mb-2"><?= $item['description'] ?></div>

              <div class="text-muted small">
                <div><span class="label">Start:</span> <?= $item['created_at'] ?></div>
                <div><span class="label">Ends:</span> <?= $item['end_at'] ?></div>
              </div>
            </div>

            <span class="badge badge-pill">Auction</span>
          </div>
        </div>
      </div>

      <!-- Additional Images (forme seulement) -->
      <div class="mt-3">
        <div class="section-title mb-2">Additional Images</div>

        <div class="thumbs d-flex gap-2 flex-wrap">
          <?php
            $pics = Item::get_other_pictures_by_id($item['id']);
            foreach ($pics as $i => $p) { ?>
              <a class="thumb <?= $i === 0 ? 'active' : '' ?>" href="#">
                <img src="<?= $p['picture_path'] ?>" alt="thumb">
              </a>
          <?php } ?>
        </div>
      </div>

      <!-- Bid History (forme seulement) -->
      <div class="card card-dark border-0 mt-3">
        <div class="d-flex align-items-center justify-content-between px-3 pt-3">
          <div class="section-title mb-0">Bid History</div>
          <?php
         $cmpt=0;
          foreach(Item::get_bids($item['id']) as $bids){
            ++$cmpt;
          }
          ?>
          <span class="badge badge-dark"><?= $cmpt?> entries</span>
        </div>

        <div class="list-group list-group-flush list-dark mt-3">
          <?php foreach(Item::get_bids($item['id']) as $bid) { ?>
            <div class="list-group-item">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="fw-semibold"><?= User::get_full_name_by_id($bid['owner'])['pseudo'] ?></div>
                  <div class="small text-muted"><?= $bid['created_at'] ?></div>
                </div>
                <div class="price-green fw-semibold">€ <?= $bid['amount'] ?></div>
              </div>
            </div>
          <?php } ?>
        </div>

        <div class="p-2"></div>
      </div>

    </div>

    <!-- DROITE -->
    <div class="col-12 col-lg-4">

      <div class="card card-dark border-0">
        <div class="card-body">
          <div class="section-title mb-3">Pricing</div>

          <div class="d-flex justify-content-between small mb-2">
            <span class="text-muted">Current Bid</span>
            <span class="price-green fw-semibold">
              <i class="bi bi-currency-euro"></i><?= $item['max_bid'] ?>
            </span>
          </div>

          <div class="d-flex justify-content-between small mb-3">
            <span class="text-muted">Buy Now</span>
            <span class="fw-semibold">
              <i class="bi bi-currency-euro"></i><?= $item['buy_now_price'] ?>
            </span>
          </div>

          <hr class="hr-soft">

          <form method="post" action="">
            <div class="input-group input-dark mb-3">
              <span class="input-group-text">€</span>
              <input
                type="text"
                name="min_bid"
                class="form-control"
                value="<?= ($item['max_bid'] === null) ? (($item['starting_bid'] != 0) ? $item['starting_bid'] : 1) : ($item['max_bid'] + 1) ?>"
                required
              >
            </div>

            <div class="d-grid gap-2">
              <button class="btn btn-success" type="submit">Place Bid</button>
              <a class="btn btn-outline-primary" href="#">
                Buy Now at € <?= $item['buy_now_price'] ?>
              </a>
            </div>
          </form>
        </div>
      </div>

      <div class="card card-dark border-0 mt-3">
        <div class="card-body">
          <div class="section-title mb-3">Seller Information</div>

          <div class="d-flex align-items-center gap-3">
            <div class="avatar"></div>
            <div>
              <div class="fw-semibold"><?= User::get_full_name_by_id($item['owner'])['pseudo'] ?></div>
              <div class="text-muted small"><?= User::get_full_name_by_id($item['owner'])['role'] ?></div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</main>
</header>
</body>    
</html>      