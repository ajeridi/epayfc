<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>🛒 <?= $item->get_title() ?> </title>
  <base href="<?= $web_root ?>">
  <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="css/style.css">
  <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="lib/jquery-ui.min.css">
  <script src="lib/jquery-ui.min.js" type="text/javascript"></script>
  <script src="lib/jquery.ui.touch-punch.js" type="text/javascript"></script>
  <script>
    let carousel; // https://www.w3schools.com/bootstrap/bootstrap_ref_js_carousel.asp
    // https://getbootstrap.com/docs/5.3/components/carousel/#via-javascript

    const item_id = <?= $item->get_id() ?>;

    let pictures;

    $(async function() {

      carousel = $("#carousel");

      carousel.addClass("carousel slide");

      pictures = await $.getJSON("item/get_item_pictures_service/" + item_id);

      // creer le carousel
      makeCarousel();
      // on dit que pour chaque thumbnail, on se met sur l'element du carousel ayant un id priorite - 1
      $("a.thumb").click(function(e) { // on assigne un event au click des <a class="thumb">
        let index = $(this).data("priority"); // on cherche leur index (priorite - 1), attention a bien utiliser this et non l'ensemble des <a class"thumb">
        carousel.carousel(index - 1); // on navigue dans le carousel vers l'index;
        $("a.thumb").removeClass("active"); // on enleve le highlight sur tous les <a>
        $(this).addClass("active"); // et on highlight le thumbnail clique
        e.preventDefault();
      })
    });

    function makeCarousel() {
      carousel.html("");
      let html = "";
      html += '<div class="carousel-inner"> ';
      
      if (pictures.length == 0) {
          html += '<div class="carousel-item active"> ';
            html += '<img src="uploads/items/default.jpg" class="d-block w-100 alt=image">';
          html += '</div> ';
      }
      else {
      for (let i = 0; i < pictures.length; i++) {
        if (i === 0) {
          html += '<div class="carousel-item active"> ';
            html += '<img src="' + pictures[0].path + '" class="d-block w-100" alt="image"> ';
          html += '</div> ';
        } else {
          html += '<div class="carousel-item"> ';
          html += '<img src="' + pictures[i].path + '" class="d-block w-100" alt="image">';
          html += '</div>';
        }
      }
      html += '  <button id="left_btn" class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev" data-bs-theme="dark">';
      html += '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
      html += '<span class="visually-hidden">Previous</span>';
      html += '</button> ';
      html += '<button id="right_btn" class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next" data-bs-theme="dark">'
      html += '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
      html += '<span class="visually-hidden">Next</span>';
      html += '</button>';
    }

      carousel.append(html);

      // creer les action de carousel
      $("#left_btn").click(function() {
        carousel.carousel("prev");
      });

      $("#right_btn").click(function() {
        carousel.carousel("next");
      });
    }
  </script>


</head>

<body class="app-bg page-item-open">
  <header>
    <!-- Barre au dessus de la page -->
    <nav class="navbar bg-navy-gray fixed-top" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand text-blue" href="<?= $back_url ?>"><i class="bi bi-arrow-left"></i> </a>
        <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue">Open <i class=" bi bi-cart4 fs-4 text-white"></i></span>
      </div>
    </nav>
  </header>

  <main class="container-fluid px-3 page-wrap" style="margin-top: 100px;">
    <br>
    <div class="row g-3 align-items-start">

      <!-- GAUCHE -->
      <div class="col-12 col-lg-8">

        <!-- Card image + infos -->
        <div class="card card-dark border-0 overflow-hidden">
          <div id="carousel" class="ratio ratio-16x9 bg-black-30">
            <img <?= ($item->get_path($priority) == "") ? "src=uploads/items/default.jpg" :   "src=" . $item->get_path($priority) ?> class="w-100 h-100 object-fit-cover" alt="item image">
          </div>

          <div class="card-body">
            <div class="d-flex justify-content-between gap-3">
              <div>
                <div class="h5 mb-1"><?= $item->get_title() ?></div>
                <div class="text-muted small mb-2"><?= $item->get_description() ?></div>
                <div class="text-muted small">
                  <div><span class="label">Start:</span> <?= $item->get_created_at() ?></div>
                  <?php
                  if ($item->is_open_item()) {
                  ?>
                    <div><span class="label">Ends:</span> <?= $item->end_at() ?></div>
                  <?php } else { ?>
                    <div><span class="label">Closed:</span> <?= $item->closed_at() ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php if ($item->get_starting_bid() != null) { ?>
                <span class="badge badge-pill">Auction</span>
              <?php } ?>
            </div>
          </div>
        </div>

        <!-- Additional Images  -->
        <div class="mt-3">
          <div class="section-title mb-2">Additional Images</div>

          <div class="thumbs d-flex gap-2 flex-wrap">
            <?php
            foreach ($pictures as $picture) { ?>
              <a data-priority="<?= $picture->get_priority() ?>" class="thumb <?= $picture->get_priority() == 1 ? 'active' : '' ?>" href="item/open/<?= $item->get_id() ?>/from-<?= $from ?>/<?= $picture->get_priority() ?>">
                <img src="<?= $picture->get_thumbnail_path() ?>" alt="thumb">
              </a>
            <?php } ?>
          </div>
        </div>

        <!-- Bid History -->
        <div class="card card-dark border-0 mt-3">
          <div class="d-flex align-items-center justify-content-between px-3 pt-3">
            <div class="section-title mb-0">Bid History</div>

            <span class="badge badge-dark"><?= count($item->get_bids()) ?> entries</span>
          </div>

          <div class="list-group list-group-flush list-dark mt-3">
            <?php foreach ($item->get_bids() as $bid) { ?>
              <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fw-semibold"><?= $bid->get_pseudo() ?></div>
                    <div class="small text-muted"><?= $bid->get_created_at() ?></div>
                  </div>
                  <div class="price-green fw-semibold">€ <?= number_format($bid->get_amount(), 2, ",", " ") ?></div>
                </div>
              </div>
            <?php  } ?>
          </div>

          <div class="p-2"></div>
        </div>

      </div>

      <!-- DROITE -->
      <div class="col-12 col-lg-4 ">

        <div class="card card-dark border-0">
          <div class="card-body">
            <div class="section-title mb-3">Pricing</div>
            <?php if ($item->get_starting_bid() != null) { ?>
              <div class="d-flex justify-content-between small mb-0">
                <span class="text-muted">Current Bid</span>
                <span class="price-green fw-semibold">
                  <?php if ($item->get_bids() == NULL) { ?>
                    <i class="bi bi-currency-euro"></i><?= number_format($item->get_starting_bid(), 2, ",", " ") ?>
                </span>
              </div>
              <div class="d-flex justify-content-between small mb-2">
                <span class="text-muted">starting at € <?= number_format($item->get_starting_bid(), 2, ",", " ") ?> </span>
              </div>
            <?php } else { ?>
              <i class="bi bi-currency-euro"></i><?= number_format($item->get_bids()['0']->get_amount(), 2, ",", " ") ?>
              </span>
          </div>
        <?php } ?>
      <?php } ?>
      <div class="d-flex justify-content-between small mb-3">

        <?php if ($item->get_buy_now_price() != null) { ?>
          <span class="text-muted">Buy Now</span>
          <span class="fw-semibold">
            <i class="bi bi-currency-euro"></i><?= number_format($item->get_buy_now_price(), 2, ",", " ") ?>
          </span>
        <?php } ?>
      </div>

      <hr class="hr-soft">
      <fieldset <?php if ($user == false || $user->get_id() == $item->get_owner()) { ?> disabled <?php } ?>>
        <?php if ($item->get_starting_bid() != null) { ?>
          <?php if ($item->is_open_item()) { ?>
            <form method="post" action="<?= $bids_url ?>">
              <div class="input-group input-dark mb-3">
                <span class="input-group-text">€</span>
                <input
                  type="text"
                  name="min_bid"
                  class="form-control"
                  value="<?= ($item->get_bids() == null) ?  $item->get_starting_bid() : $item->get_bids()['0']->get_amount() + 1 ?>"
                  required>
              </div>

              <div class="d-grid gap-2">
                <button class="btn btn-success" type="submit">Place Bid</button>
            </form>
            <?php if ($item->get_buy_now_price() != null && $item->get_starting_bid() != null) { ?>
              <button class="btn btn-outline-primary " type="submit" name="buy" value="<?= $item->get_buy_now_price() ?>">
                Buy Now at € <?= number_format($item->get_buy_now_price(), 2, ",", " ") ?>
              </button>
            <?php } ?>
        </div>
      <?php } ?>
    <?php } ?>
    <?php if ($item->get_starting_bid() == null && $item->is_open_item()) { ?>
      <form method="post" action="item/bid/<?= $item->get_id() ?>">
        <div class="d-grid gap-2">
          <button class="btn btn-outline-primary " type="submit" name="buy" value="<?= $item->get_buy_now_price() ?>">Buy Now</button>
        </div>
      </form>
      </fieldset>
      <?php }
    if (!$item->is_open_item()) {
      if (count($item->get_bids()) != 0) {
        if ($user->get_id() === $item->get_bids()['0']->owner) { ?>
          <p> Congratulation! You purchased this item for € <?= number_format($item->get_bids()['0']->get_amount(), 2, ",", " ") ?> </p>
        <?php } else { ?>
          <p><?= $item->get_bids()['0']->get_pseudo() ?> Won this item for € <?= number_format($item->get_bids()['0']->get_amount(), 2, ",", " ") ?></p>
        <?php }
      } else { ?>
        <p>This listing ended whitout a buyer</p>
    <?php }
    } ?>

    <?php if ($user == true && $user->get_pseudo() === $item->get_owner() && $item->is_open_item()) { ?>
      <p>You cannot bid on your own listing</p>
    <?php } ?>
    <?php if ($user == false && $item->is_open_item()) { ?>
      <p>Option not available for guests</p>
    <?php } ?>

      </div>
    </div>

    <div class="card card-dark border-0 mt-3">
      <div class="card-body">
        <div class="section-title mb-3">Seller Information</div>

        <div class="d-flex align-items-center gap-3">
          <div class="avatar rounded-circle"><img src="<?= $owner_picture ?>" class="w-100" alt="profile picture"></div>
          <div>
            <div class="fw-semibold"><?= $item->get_pseudo()  ?></div>
            <div class="text-muted small"><?= $owner_role ?></div>
          </div>
        </div>
      </div>
    </div>
    <?php if ($user == true && $user->get_id() === $item->get_owner()  && $item->is_open_item()) { ?>
      <div class="card card-dark border-0 mt-3">
        <div class="card-body">
          <div class="section-title mb-3">Manage Your Item</div>
          <div class="row g-3">
            <div class="d-grid gap-2">
              <a class="btn btn-outline-success <?php if (count($item->get_bids()) != 0) { ?>disabled <?php } ?> " href="<?= $edit_item_url ?>">
                Edit item Details
              </a>
            </div>
            <div class="d-grid gap-2">
              <a class="btn btn-outline-info" href="<?= $manage_images_url ?>">
                Manage Images
              </a>
            </div>
            <div class="d-grid gap-2">
              <a class="btn btn-outline-danger  <?php if (count($item->get_bids()) != 0) { ?>disabled <?php } ?> " href="item/delete/<?= $item->get_id() . ($search_criteria == "" ? "" : "/" . $search_criteria) ?>">
                Delete Item
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>

    </div>

    </div>
  </main>
</body>

</html>