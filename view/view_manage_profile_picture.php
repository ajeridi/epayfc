<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>🛒 Manage Profile Picture</title>
        <base href="<?= $web_root ?>"?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body class="bg-navy" data-bs-theme="dark">
        <header>
            <!-- Barre au dessus de la page -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark">
                <div class="container-fluid">
                    <a class="navbar-brand text-blue" href="user/profile"><i class="bi bi-arrow-left"></i> </a>
                    <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue">Manage profile picture <i class="bi bi-cart4 fs-4 text-white"></i></span>
                </div>
            </nav>
        </header>
        <main class="container mb-3" style="margin-bottom: 100px;">
            <!-- Information utilisateur !-->
            <div class="container mt-3">
                <div class="card mb-3 bg-dark">
                    <div class="card-header bg-primary fw-bold " style="--bs-bg-opacity: .10 ;" >Current Profile Picture</div>
                    
                    <div class="card-body container-fluid justify-content-center text-white text-center ">
                        <img src="<?= $user->get_picture_path() ?>"  class="avatar rounded-circle"> 
                        <h5 class="card-text "><?= $user->get_full_name() ?></p>
                        <p class="card-text small ">@ <?= $user->get_pseudo() ?></p>
                    </div>
                </div>    
                <div class="card mb-3 text-white" >
                <div class="card-header bg-navy-gray ">
                    Upload New Picture
                </div>
                <div class="card-body text-white text-bg-dark">
                    <h6 class="card-title fw-bold">Select Images *</h6>
                    <form method='post' action='user/manage_profile_picture' enctype='multipart/form-data'>
                        <input type='file' class='form-control text-bg-dark' name='image' accept="image/x-png, image/gif, image/jpeg, .jpg, image/webp">
                        <p class="card-text text-secondary">Supported formats:JPG, PNG, GIF, WebP. This well replace your current profile.</p>
                        <input type='submit' class="btn btn-primary" value='Upload Images'>
                    </form><br>
                    <div class="text-danger">
                        <?php if (count($errors) > 0) {?>
                            <ul>
                                <?php foreach ($errors as $error){ ?>
                                    <li><?= $error ?></li>
                                <?php }
                            }?>
                            </ul>
                    </div>
                </div>
                </div>
                <?php if($user->get_picture_path()){?>
             <form method="post" action="user/manage_profile_picture" >
                <div class="card text-white text-bg-dark">
                    <div class="card-header bg-navy-gray ">
                        Remove Current Picture
                    </div>
                    <div class="card-body  text-white text-bg-dark">
                        <div class=" bg-warning border  mb-3 " style="--bs-bg-opacity: .10;" >
                            <p class=" text-warning " style="padding-top:10px;padding-left:10px;">This will permenenly delete your current profile picture.You can upload a new one anytime</p>
                        </div>
    
                        <button class="btn btn-danger"  type="submit"  name="delete">Delete Picture</button>
                    </div>
                    
                </div>
             </form>   
                <?php } ?>
            </div><br><br>
                </div>
            </div>
        </main>
    </body>   