<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <base href="<?= $web_root ?>"/>
        <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
         <header>
            <!-- Barre  -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark">
                <div class="container-fluid justify-content-center"> Item open 🛒
                </div>
            </nav>
            <div class="card" style="width: 18rem;">
                <img src=<? $item->picture_path ?> class="card-img-top"  alt="item image">
                <div class="card-body">
                    <p class="card-text"><? $item->title?></p>
                    <p class="card-text"><? $item->description?></p>
                    <p class="card-text">Start: <? $item->created_at?></p>

                </div>
                <div class="fixed-bottom">
                    <h1>Additional Image </h1>

                </div> 
            </div>
        </header>
    </body>

</html>