<!DOCTYPE html>
<html>
    <head>
         <meta charset="UTF-8">
        <title>🛒 Delete "<?= $title ?>" </title>
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
                   <i class="bi bi-trash3 " style="font-size: 100px;"></i>
                   <h1 >Are you sure ?</h1>
                </div>
                <div class="card-body bg-dark text-white text-center " style="padding: 15px;" >
                    <p >Are you really want to delete item <b style="text-decoration: solid;">"<?= $title ?>"</b> by <?= $owner ?> and all of its dependencies ?</p>
                    <p style="text-align: center;">This process cannot bo undone.</p>
                    <div class="row " style="padding: 35px;">
                        <div class="col">
                            <a class="btn bg-secondary text-light" href="item/open/<?= $id ?>/from-items<?= ($search_criteria == "" ? "" : "/" . $search_criteria) ?>">Cancel</a>
                        </div>
                        <div class="col">
                             <a class="btn bg-danger text-light" href="item/delete_item/<?= $id . ($search_criteria == "" ? "" : "/" . $search_criteria)?>">Delete</a>
                        </div>  
                    </div>
                </div>
            </div>
        </main>



     </body>