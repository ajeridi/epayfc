<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Browse items</title>
        <base href="<?= $web_root ?>"?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body class="bg-navy">
        <header>
            <!-- Barre au dessus de la page -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark">
                <div class="container-fluid justify-content-center">
                    <span class="navbar-brand mb-0 h1 text-blue"">Manage images <i class="bi bi-cart4 fs-4 text-white"></i></span>
                </div>
            </nav>
        </header>
        <main class="container  text-white">
            <h2 class="mt-4 mb-4">Manage images for "Item name"</h2>
            
            <div class="card text-white" style="max-width: 70rem;">
                <div class="card-header bg-navy-gray ">
                    Add New Images
                </div>
                <div class="card-body text-white text-bg-dark">
                    <h6 class="card-title fw-bold">Select Images</h6>
                    <div class="input-group">
                        <input type="file" class="form-control text-bg-dark" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                    </div>
                    <p class="card-text text-secondary">You can select multiple images (JPG, PNG, GIF, WebP). Images will be added to the end of your current list.</p>
                    <a href="#" class="btn btn-primary">Upload Images</a>
                </div>
            </div>


        </main>
        <footer> 
            <?php require 'navbar_user.php'; ?>
        </footer>
    </body>
</html>