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
            <nav class="navbar bg-navy-gray" data-bs-theme="dark"> <div class="container-fluid justify-content-center text-blue text-decoration-bold fw-bold fs-4 "> Edit item 🛒 </div> </nav>
         </header>   

    </header>
    <body>
        <main class="container mb-3">
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
            <div class="container mb-3">
                <div class="card bg-dark  border-0 " >
                    <div class="card-header bg-primary text-white  " style="--bs-bg-opacity: .10">My activities</div>
                    <div class="card-body  text-white  ">
                        <div class="mb-3">
                        <a class="btn btn-outline-primary " style="width:max" href="">
                            <div>Sales</div>
                            <div>items you have solde</div>
                        </a>
                        </div>
                        <div class="mb-3">
                        <a class="btn btn-outline-primary " style="box-sizing:border-box" href="">
                            <div>Purchases</div>
                            <div>items you have purchased</div>
                        </a>
                        </div>
                        
                    </div>
                </div> 
            </div>        
        </main>
    </body>
</html>