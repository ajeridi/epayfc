<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Browse items</title>
        <base href="<?= $web_root ?>"?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body class="bg-navy">
        <header>
            <!-- Barre au dessus de la page -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark">
                <div class="container-fluid justify-content-center">
                    <span class="navbar-brand mb-0 h1 text-blue"">Browse <i class="bi bi-cart4 fs-4 text-white"></i></span>
                </div>
            </nav>
        </header>
        <main class="main-content-margins">
            <div class="items_participated_in">
                <h3 class="text-blue">Items I'm Participating In</h3>
            </div>

            <div class="other_available_items">
                <h3 class="text-blue">Other Available Items</h3>
            </div>


        </main>

    </body>
</html>