<!DOCTYPE html>
<html>
    <head>
         <meta charset="UTF-8">
        <title>🛒 Settings </title>
        <base href="<?= $web_root ?>">
        <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">

    </head>
     <header>
        <header>
            <!-- Barre  -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark"> <div class="container-fluid justify-content-center text-blue text-decoration-bold fw-bold fs-4 "> Profile 🛒 </div> </nav>
         </header>   

    </header>
    <body class="bg-navy"> 
        <main class="container mt-3" style="margin-bottom: 100px;">
            <!-- Information utilisateur !-->
            <div class="container mb-3">
                <div class="card text-center bg-dark">
                    <div class="card-body container-fluid justify-content-center text-white">
                        <img src="<?= $user->get_picture_path() ?>"  class="avatar rounded-circle"> 
                        <h5 class="card-text"><?= $user->get_full_name(); ?></p>
                        <p class="card-text small">@<?= $user->get_pseudo() ?></p>
                        <p class="card-text small"><?= $user->get_email()?></p>
                    </div>
                 
                </div>
            </div>
            <!-- My activities !-->
            <div class="container mb-3">
                <div class="card bg-dark" >
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">My activities</div>
                    <div class="card-body">
                        <!-- Sales!-->
                        <button class="btn btn-outline-light w-100 mb-3" style="border: 1px solid blue; border-radius: 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom pt-2" href="item/sales">   
                                    <i class="bi bi-receipt-cutoff fs-3"></i>
                                    <div style="margin-left: 15px; text-align:left;">
                                        <p class="mb-0 fs-6 fw-bold" >Sales</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Items you have sold</p>
                                    </div>
                                </a>
                            </div>
                        </button>
                        <!-- Purchases !-->
                        <button class="btn btn-outline-light w-100" style="border: 1px solid blue; border-radius : 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom pt-2" href="item/purchases">   
                                    <i class="bi bi-cart3 fs-3"></i>
                                    <div style="margin-left: 15px; text-align:left;">
                                        <p class="mb-0 fs-6 fw-bold" >Purchases</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Items you have purchased</p>
                                    </div>
                                </a>
                            </div>
                        </button>
                    </div>
                </div> 
            </div>        
            <!-- Account Settings !-->
            <div class="container mb-3">
                <div class="card bg-dark" >
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Account Settings</div>
                    <div class="card-body">
                        <!-- Edit Profile !-->
                        <button class="btn btn-outline-light w-100 mb-3" style="border: 1px solid blue; border-radius: 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom pt-2" href="user/edit_profile">   
                                    <i class="bi bi-person-gear fs-3"></i>
                                    <div style="margin-left: 15px; text-align:left;">
                                        <p class="mb-0 fs-6 fw-bold" >Edit Profile</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Update your personal informations</p>
                                    </div>
                                </a>
                            </div>
                        </button>
                        <!-- Password !-->
                        <button class="btn btn-outline-light w-100 mb-3" style="border: 1px solid blue; border-radius : 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom pt-2" href="user/change_password">   
                                    <i class="bi bi-key fs-3"></i>
                                    <div style="margin-left: 15px; text-align:left; ">
                                        <p class="mb-0 fs-6 fw-bold" >Password</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Update your account security</p>
                                    </div>
                                </a>
                            </div>
                        </button>
                        <!-- Profile Picture !-->
                        <button class="btn btn-outline-light w-100 mb-3" style="border: 1px solid blue; border-radius : 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom pt-2" href="user/manage_profile_picture">   
                                    <i class="bi bi-person-bounding-box fs-3"></i>
                                    <div style="margin-left: 15px; text-align: left;">
                                        <p class="mb-0 fs-6 fw-bold" >Profile Picture</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Upload or change your profile picture</p>
                                    </div>
                                </a>
                            </div>
                        </button>
                        <!-- Logout !-->
                         <form method="post" action="user/login">
                            <button type="submit" name="logout" value="true" class="btn btn-outline-light w-100 mb-3" style="border: 1px solid red; border-radius : 5px;">
                                <div class="container-fluid">
                                    <div class="navbar-brand text-danger d-flex align-items-bottom pt-2">   
                                        <i class="bi bi-box-arrow-left fs-3"></i>
                                        <div style="margin-left: 15px; text-align:left;">
                                            <p class="mb-0 fs-6 fw-bold" >Logout</p>
                                            <p class="text-secondary fw-bold" style="font-size: small;">Sign out of your account</p>
                                        </div>
                                    </div>
                                </div>
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