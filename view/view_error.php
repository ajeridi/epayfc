<!DOCTYPE html>
<html>
    <head>
         <meta charset="UTF-8">
        <title>🛒 Error </title>
        <base href="<?= $web_root ?>">
        <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
     <body class="container">
         <main>
            <div class="container " style="max-width: 350px;margin-top:100px">
                <div class="card bg-dark text-danger text-bold text-center border-0 overflow-hidden" >
                   <i class="bi bi-bug-fill " style="font-size: 100px;"></i>
                   <h1 >Nice Try </h1>
                </div>
                <div class="card-body bg-dark text-white text-center " style="padding: 15px;" >
                    <p >Are you really want to do this ? <b style="text-decoration: solid;text-decoration-color: red;"><?= $error ?></b>  Abdelmouine et Aymen thank you -_-</p>
                    <p style="text-align: center;"></p>
                    <div class="row " style="padding: 35px;">
                        <div class="col">
                            <a class="btn bg-secondary text-light" href="item/browse">Browse</a>
                        </div>
             
                    </div>
                </div>
            </div>
        </main>