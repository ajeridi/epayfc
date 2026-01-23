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
    <body>
        <main class="container mt-3" style="margin-bottom: 100px;">
            <!-- Information utilisateur !-->
            <div class="container mb-3">
                <div class="card text-center bg-dark">
                    <div class="card-header">
                    </div>
                    <div class="card-body container-fluid justify-content-center text-white">
                        <img src="<?= $info['picture_path'] ?>"  class="avatar rounded-circle"> 
                        <h5 class="card-text"><?= $info['full_name'] ?></p>
                        <p class="card-text small">@<?= $info['pseudo'] ?></p>
                        <p class="card-text small"><?= $info['email'] ?></p>
                    </div>
                 
                </div>
            </div>
            <!-- My activities !-->
            <div class="container mb-3">
                <div class="card bg-dark" >
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">My activities</div>
                    <div class="card-body">
                        <!-- Sales!-->
                        <nav class="navbar mb-3" style="border: 1px solid blue; border-radius: 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom" href="#">   
                                    <i class="bi bi-receipt-cutoff fs-3"></i>
                                    <div style="margin-left: 10px;">
                                        <p class="mb-0 fs-6 fw-bold" >Sales</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Items you have sold</p>
                                    </div>
                                </a>
                            </div>
                        </nav>
                        <!-- Purchases !-->
                        <nav class="navbar" style="border: 1px solid blue; border-radius : 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom" href="#">   
                                    <i class="bi bi-cart3 fs-3"></i>
                                    <div style="margin-left: 10px;">
                                        <p class="mb-0 fs-6 fw-bold" >Purchases</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Items you have purchased</p>
                                    </div>
                                </a>
                            </div>
                        </nav>
                    </div>
                </div> 
            </div>        
            <!-- Account Settings !-->
            <div class="container mb-3">
                <div class="card bg-dark" >
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">Account Settings</div>
                    <div class="card-body">
                        <!-- Edit Profile !-->
                        <nav class="navbar mb-3" style="border: 1px solid blue; border-radius: 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom" href="#">   
                                    <i class="bi bi-person-gear fs-3"></i>
                                    <div style="margin-left: 10px;">
                                        <p class="mb-0 fs-6 fw-bold" >Edit Profile</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Update your personal informations</p>
                                    </div>
                                </a>
                            </div>
                        </nav>
                        <!-- Password !-->
                        <nav class="navbar mb-3" style="border: 1px solid blue; border-radius : 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom" href="#">   
                                    <i class="bi bi-key fs-3"></i>
                                    <div style="margin-left: 10px;">
                                        <p class="mb-0 fs-6 fw-bold" >Password</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Update your account security</p>
                                    </div>
                                </a>
                            </div>
                        </nav>
                        <!-- Profile Picture !-->
                        <nav class="navbar mb-3" style="border: 1px solid blue; border-radius : 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-primary d-flex align-items-bottom" href="#">   
                                    <i class="bi bi-person-bounding-box fs-3"></i>
                                    <div style="margin-left: 10px;">
                                        <p class="mb-0 fs-6 fw-bold" >Profile Picture</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Upload or change your profile picture</p>
                                    </div>
                                </a>
                            </div>
                        </nav>
                        <!-- Logout !-->
                        <nav class="navbar mb-3" style="border: 1px solid red; border-radius : 5px;">
                            <div class="container-fluid">
                                <a class="navbar-brand text-danger d-flex align-items-bottom" href="#">   
                                    <i class="bi bi-box-arrow-left fs-3"></i>
                                    <div style="margin-left: 10px;">
                                        <p class="mb-0 fs-6 fw-bold" >Logout</p>
                                        <p class="text-secondary fw-bold" style="font-size: small;">Sign out of your accout</p>
                                    </div>
                                </a>
                            </div>
                        </nav>
                    </div>
                </div> 
            </div>        
        </main>
        <footer>  
            <?php require 'navbar_user.php'; ?>
        </footer>
    </body>
</html>