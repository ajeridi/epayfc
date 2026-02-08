<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>🛒 Settings </title>
    <base href="<?= $web_root ?>">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-navy" style="margin-top: 70px;">
    <header>
        <!-- Barre  -->
        <nav class="navbar bg-navy-gray fixed-top" data-bs-theme="dark">
            <div class="container-fluid justify-content-center text-blue text-decoration-bold fw-bold fs-4 "> Profile 🛒 </div>
        </nav>
    </header>
    <main class="container mt-3" style="margin-bottom: 150px;">
        <!-- Information utilisateur !-->
        <div class="container mb-3">
            <div class="card text-center bg-dark">
                <div class="card-body container-fluid justify-content-center text-white">
                    <img src="<?= $user->get_picture_path() == null ? 'https://cdn.jsdelivr.net/npm/bootstrap-icons/icons/person-circle.svg' : $user->get_picture_path() ?>" width="80" height="80" class="avatar rounded-circle" alt="profile_picture">
                    <h5 class="card-text"><?= $user->get_full_name(); ?></h5>
                    <h6 class="card-text text-secondary">@<?= $user->get_pseudo() ?></h6>
                    <h6 class="card-text text-secondary"><?= $user->get_email() ?></h6>
                </div>

            </div>
        </div>
        <!-- My activities !-->
        <div class="container mb-3">
            <div class="card bg-dark">
                <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">My activities</div>
                <div class="card-body">
                    <!-- Sales!-->
                    <a class="btn btn-outline-light w-100 mb-3  text-primary d-flex align-items-bottom pt-3" style="border: 1px solid blue; border-radius : 5px;" href="item/sales">
                        <span class="d-flex container">
                            <i class="bi bi-receipt-cutoff fs-3"></i>
                            <span class="d-flex flex-column mb-3" style="margin-left: 15px; text-align:left;">
                                <span class="mb-0 fs-6 fw-bold">Sales</span>
                                <span class="text-secondary fw-bold" style="font-size: small;">Items you have sold</span>
                            </span>
                        </span>
                    </a>
                    <!-- Purchases !-->
                    <a class="btn btn-outline-light w-100 mb-3  text-primary d-flex align-items-bottom pt-3" style="border: 1px solid blue; border-radius : 5px;" href="item/purchases">
                        <span class="d-flex container">
                            <i class="bi bi-cart3 fs-3"></i>
                            <span class="d-flex flex-column mb-3" style="margin-left: 15px; text-align:left;">
                                <span class="mb-0 fs-6 fw-bold">Purchases</span>
                                <span class="text-secondary fw-bold" style="font-size: small;">Items you have purchased</span>
                            </span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <!-- Account Settings !-->
        <div class="container mb-3">
            <div class="card bg-dark">
                <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Account Settings</div>
                <div class="card-body">
                    <!-- Edit Profile !-->
                    <a class="btn btn-outline-light w-100 mb-3  text-primary d-flex align-items-bottom pt-3" style="border: 1px solid blue; border-radius : 5px;" href="user/edit_profile">
                        <span class="d-flex container">
                            <i class="bi bi-person-gear fs-3"></i>
                            <span class="d-flex flex-column mb-3" style="margin-left: 15px; text-align:left;">
                                <span class="mb-0 fs-6 fw-bold">Edit Profile</span>
                                <span class="text-secondary fw-bold" style="font-size: small;">Update your personal informations</span>
                            </span>
                        </span>
                    </a>
                    <!-- Password !-->
                    <a class="btn btn-outline-light w-100 mb-3  text-primary d-flex align-items-bottom pt-3" style="border: 1px solid blue; border-radius : 5px;" href="user/change_password">
                        <span class="d-flex container">
                            <i class="bi bi-key fs-3"></i>
                            <span class="d-flex flex-column mb-3" style="margin-left: 15px; text-align:left; ">
                                <span class="mb-0 fs-6 fw-bold">Password</span>
                                <span class="text-secondary fw-bold" style="font-size: small;">Update your account security</span>
                            </span>
                        </span>
                    </a>
                    <!-- Profile Picture !-->
                    <a class="btn btn-outline-light w-100 mb-3  text-primary d-flex align-items-bottom pt-3" style="border: 1px solid blue; border-radius : 5px;" href="user/manage_profile_picture">
                        <span class="d-flex container">
                            <i class="bi bi-person-bounding-box fs-3"></i>
                            <span class="d-flex flex-column mb-3" style="margin-left: 15px; text-align: left;">
                                <span class="mb-0 fs-6 fw-bold">Profile Picture</span>
                                <span class="text-secondary fw-bold" style="font-size: small;">Upload or change your profile picture</span>
                            </span>
                        </span>
                    </a>
                    <!-- Logout !-->
                    <form method="post" action="user/login">
                        <button type="submit" name="logout" value="true" class="btn btn-outline-light w-100 mb-3 d-flex text-danger pt-2" style="border: 1px solid red; border-radius : 5px; padding-left: 1.3rem;">
                            <i class="bi bi-box-arrow-left fs-3" style="margin-right: 15px;"></i>
                            <span class="d-flex flex-column mb-3  text-start">
                                <span class="mb-0 fs-6 fw-bold">Logout</span>
                                <span class="text-secondary fw-bold" style="font-size: small;">Sign out of your account</span>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <?php require 'navbar_user.php'; ?>
    </footer>
</body>

</html>