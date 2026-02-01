<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>🛒 Manage Images - <?= $item_title ?></title>
    <base href="<?= $web_root ?>" ?>
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
                <a class="navbar-brand text-blue" href="item/open/<?= $item_id ?>"><i class="bi bi-arrow-left"></i> </a>
                <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue"">Manage images <i class=" bi bi-cart4 fs-4 text-white"></i></span>
            </div>
        </nav>
    </header>
    <main class="container  text-white" style="margin-bottom: 150px;">
        <h2 class="mt-4 mb-4">Manage images for "<?= $item_title ?>"</h2>
        <!-- Partie input d'images !-->
        <div class="card text-white">
            <div class="card-header bg-navy-gray ">
                Add New Images
            </div>
            <div class="card-body text-white" style="background-color: #27272a;">
                <h6 class="card-title fw-bold">Select Images</h6>
                <form method='post' action='item/manage_images/<?= $item_id ?>' enctype='multipart/form-data'>
                    <input type='file' class='form-control text-bg-dark' name='image' accept="image/x-png, image/gif, image/jpeg, .jpg, image/webp">
                    <p class="card-text text-secondary">You can select multiple images (JPG, PNG, GIF, WebP). Images will be added to the end of your current list.</p>
                    <input type='submit' class="btn btn-primary" value='Upload Images'>
                </form><br>
                <div class="text-danger">
                    <?php if (count($errors) > 0) { ?>
                        <ul>
                            <?php foreach ($errors as $error) { ?>
                                <li><?= $error ?></li>
                        <?php }
                        } ?>
                        </ul>
                </div>
            </div>
        </div><br><br>

        <!-- Partie affichage des images!-->
        <div class="card text-white text-bg-dark">
            <div class="card-header bg-navy-gray ">
                Current Images
            </div>
            <div class="card-body" style="background-color: #27272a;">
                <?php if ($item_pictures == null) { ?>
                    <div class="container bg-dark text-white-50" style="border: 1px solid #495057; border-radius: 5px; padding-top: 10px;">
                        <p>No images yet for this item. Upload some images above to get started.</p>
                    </div>
                <?php } else { ?>
                    <form method="post" action='item/manage_images/<?= $item_id ?>'>
                        <?php foreach ($item_pictures as $row) { ?>
                            <figure class='d-inline-block' style="margin: 10px;">
                                <img src='<?= ImageUtils::get_thumbnail_path($row["picture_path"]) ?>' class="rounded float-start" witdh="100" height="100" style="margin-bottom: 8px;">
                                <figcaption style="text-align: center;">
                                    <button type="submit" class="btn btn-secondary" name="left" value="<?= $row["priority"] ?>" <?= $row["priority"] == 1 ? "disabled" : "" ?>><i class="bi bi-arrow-left"></i></button>
                                    <button type="submit" class="btn btn-secondary" name="right" value="<?= $row["priority"]  ?>" <?= $row["priority"] == count($item_pictures) ? "disabled" : "" ?>><i class="bi bi-arrow-right"></i></button>
                                    <button type="submit" class="btn btn-danger" name="delete" value="<?= $row["priority"] ?>"><i class="bi bi-x-lg"></i></button>
                                </figcaption>
                            </figure>

                    <?php }
                    } ?>
                    </form>
            </div>
        </div>
    </main>
    <footer>
        <?php require 'navbar_user.php'; ?>
    </footer>
</body>

</html>