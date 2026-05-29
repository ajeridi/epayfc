<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>🛒 Manage categories</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
</head>
<body class="bg-navy">
    <header>
        <!-- Barre au dessus de la page -->
        <nav class="navbar bg-navy-gray fixed-top" data-bs-theme="dark">
            <div class="container-fluid justify-content-center">
                <span class="navbar-brand mb-0 h1 text-blue">Categories <i class="bi bi-cart4 fs-4 text-white"></i></span>
            </div>
        </nav>
    </header>
    <main style="margin-top: 100px; margin-bottom: 200px;" data-bs-theme="dark">
    
        <div class="container pt-2 pb-2" style="background-color: #26262a; border: 1px solid #26262a; border-radius: 10px;">
            <form method="post" action="category/manage">
                <?php foreach($categories as $category){ ?>
                    <div class="row mt-2 mb-2">
                        <div class="col-7">
                            <input type="text" class="form-control" name=<?= "category_".$category->get_id() ?> value="<?= $_POST['category_'.$category->get_id()] ?? $category->get_name() ?>">
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-secondary" name="up" value="<?= $category->get_id() ?>" <?= $category->get_priority() == 1 ? "disabled" : "" ?>>Up</i></button>
                            <button type="submit" class="btn btn-secondary" name="down" value="<?= $category->get_id() ?>" <?= $category->has_max_priority() ? "disabled" : "" ?>>Down</i></button>
                            <button type="submit" class="btn btn-success" name="save" value="<?= $category->get_id() ?>">Save</i></button>
                            <button type="submit" class="btn btn-danger" name="delete" value="<?= $category->get_id() ?>" <?= $category->is_empty() == false ? "disabled" : "" ?>>Delete</i></button>
                        </div>
                    </div>

                    <?php if (isset($errors["category_".$category->get_id()])) { ?> 
                                <p class="text-danger"><?= $errors["category_".$category->get_id()] ?></p>
                            <?php } ?>
                <?php } ?>
                <hr class="mt-4 mb-4" style="border: 1px solid #ccc;">
                <div class="row">
                    <div class="col-9">
                        <input type="text" class="form-control" name="category_0" value="<?= $post["category_". 0] ?? "" ?>" placeholder="Category name">
                    </div>
                    <div class="col-1">
                        <button type="submit" class="btn btn-primary" name="save" value="0"><i class="bi bi-plus"></i></button>
                    </div>
                </div>
                <!-- Erreur sur la nouvelle categorie -->
                <?php if (isset($errors["category_". 0])) { ?> 
                    <p class="text-danger"><?= $errors["category_". 0] ?></p>
                <?php } ?>
            </form>
        </div>

    </main>
    <footer>
        <?php require 'navbar_user.php'; ?>
    </footer>
</body>

</html>