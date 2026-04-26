<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>🛒 Manage Images - <?= $item->get_title() ?></title>
    <base href="<?= $web_root ?>" ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="lib/jquery-ui.min.css">
    <script src="lib/jquery-ui.min.js" type="text/javascript"></script>
    <script src="lib/jquery.ui.touch-punch.js" type="text/javascript"></script>
    <script>

    /**
     * Comment ca va marcher le drag and drop
     * 
     * Pour implement jquery ui, il faut definir un parent et ses enfants
     * dans mon cas le parent est le form et chaque enfant est le figure
     * 
     * 
     */

    let image_container;
    const item_id = <?= $item->get_id() ?>;

    $(async function() {
        image_container = $("#image_container");

        image_container.sortable({ // on dit que notre form est sortable
            items: "figure", // que chaque element sortable est de type figure
            update: async function(event, ui) { // methode a activer apres le drop
                // chaque photo conservera son ancienne priorite,
                // on recupere une liste de priorite des images, ou l'index + 1 est la nouvelle priorite et array[index] et l'ancienne
                let sorted = image_container.sortable("toArray", {attribute: "data-id"});
                await swap_image_priority(item_id, sorted); // on envoie le tableau des anciennes priorite dans le nouvel ordre
            }
        });
    });

    async function swap_image_priority(item_id, new_order){
        // on va faire une requete post
        await $.post("item/swap_image_priority_service", 
            {
                item_id : item_id,
                order: new_order
            },
            function(response){
                console.log(response);
            }
        )
    }




    </script>

</head>

<body class="bg-navy" data-bs-theme="dark">
    <header>
        <!-- Barre au dessus de la page -->
        <nav class="navbar bg-navy-gray" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand text-blue" href="item/open/<?= $item->get_id() ?>/from-items<?= $search_criteria == "" ? "" : "/".$search_criteria ?>"><i class="bi bi-arrow-left"></i> </a>
                <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue"">Manage images <i class=" bi bi-cart4 fs-4 text-white"></i></span>
            </div>
        </nav>
    </header>
    <main class="container  text-white" style="margin-bottom: 150px;">
        <h2 class="mt-4 mb-4">Manage images for "<?= $item->get_title() ?>"</h2>
        <!-- Partie input d'images !-->
        <div class="card text-white">
            <div class="card-header bg-navy-gray ">
                Add New Images
            </div>
            <div class="card-body text-white" style="background-color: #27272a;">
                <h6 class="card-title fw-bold">Select Images</h6>
                <form method='post' action='item/manage_images/<?= $item->get_id() . ($search_criteria == "" ? "" : "/" . $search_criteria)?>' enctype='multipart/form-data'>
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
                <?php if ($item->get_pictures() == []) { ?>
                    <div class="container bg-dark text-white-50" style="border: 1px solid #495057; border-radius: 5px; padding-top: 10px;">
                        <p>No images yet for this item. Upload some images above to get started.</p>
                    </div>
                <?php } else { ?>
                    <form id="image_container" method="post" action='item/manage_images/<?= $item->get_id() . ($search_criteria == "" ? "" : "/" . $search_criteria) ?>'>
                        <?php foreach ($item->get_pictures() as $picture) { ?>
                            <figure class='d-inline-block' style="margin: 10px;" data-id="<?= $picture->get_priority() ?>">
                                <img src='<?= $picture->get_thumbnail_path() ?>' class="rounded float-start" witdh="100" height="100" style="margin-bottom: 8px;">
                                <figcaption style="text-align: center;">
                                    <button type="submit" class="btn btn-secondary" name="move" value="<?= -1 * $picture->get_priority() ?>" <?= $picture->get_priority() == 1 ? "disabled" : "" ?>><i class="bi bi-arrow-left"></i></button>
                                    <button type="submit" class="btn btn-secondary" name="move" value="<?= $picture->get_priority() ?>" <?= $picture->get_priority() == $item->get_number_of_pictures() ? "disabled" : "" ?>><i class="bi bi-arrow-right"></i></button>
                                    <button type="submit" class="btn btn-danger" name="delete" value="<?= $picture->get_priority() ?>"><i class="bi bi-x-lg"></i></button>
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