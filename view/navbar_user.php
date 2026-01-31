<div class="fixed-bottom">
    <nav class="navbar justify-content-center bg-navy-gray text-blue small nav-fill pt-3">
        <a class="nav-link active text-center" aria-current="page" href="item/browse">
            <figure>
                <i class="bi bi-search"></i>
                <figcaption class="text-center">Browse</figcaption>
            </figure>
        </a>

        <?php if ($user != false) { ?>
            <a class="nav-link " href="item/list">
                <figure>
                    <i class="bi bi-house-door"></i>
                    <figcaption class="text-center">My items</figcaption>
                </figure>

            </a>
            <a class="nav-link" href="item/add">
                <figure>
                    <i class="bi bi-plus"></i>
                    <figcaption class="text-center">Add offer</figcaption>
                </figure>

            </a>
            <a class="nav-link" href="user/profile">
                <figure>
                    <i class="bi bi-gear"></i>
                    <figcaption class="text-center">Profile</figcaption>
                </figure>
            </a>
        <?php } else { ?>
            <a class="nav-link" href="user/signup">
                <figure>
                    <i class="bi bi-person-raised-hand"></i>
                    <figcaption class="text-center">Join us!</figcaption>
                </figure>
            </a>
        <?php } ?>

    </nav>
    <?php require 'navbar_datetime.php'; ?>
</div>