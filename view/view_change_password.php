<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title> 🛒 Change Password</title>
    <base href="<?= $web_root ?>" ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-navy" data-bs-theme="dark">
    <form method="post" action="user/change_password">
        <header>
            <!-- Barre au dessus de la page -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark">
                <div class="container-fluid">
                    <a class="navbar-brand text-blue" href="user/profile"><i class="bi bi-arrow-left"></i> </a>
                    <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue">Change Password <i class=" bi bi-cart4 fs-4 text-white"></i></span>
                    <button type="submit" name="savePassword" value="true" class="btn navbar-brand text-blue position-absolute top-50 end-0 translate-middle-y"><i class="bi bi-floppy2-fill"></i></a>
                </div>
            </nav>
        </header>
        <main class="container mt-3" style="margin-bottom: 100px;">
            <!--  Current Password !-->
            <div class="container mb-3">
                <div class="card">
                    <div class="card-header bg-primary" style="--bs-bg-opacity: .10">Current Password</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="inputCurrentPassword" class="form-label">Current Password *</label>
                            <input type="password" name="currentPassword" class="form-control" id="inputCurrentPassword" aria-describedby="currentPasswordHelp" placeholder="Enter your current password">
                            <?php
                            if (isset($errors["currentPassword"]) && count($errors["currentPassword"]) != 0) { ?> <!-- ajout du count pour ne pas avoir d'espace inutile !-->
                                <ul class="text-danger">
                                    <?php foreach ($errors["currentPassword"] as $error) { ?>
                                        <li><?= $error ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <div id="currentPasswordHelp" class="form-text text-secondary ">Enter your current password to verify your identity</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Password !-->
            <div class="container mb-3">
                <div class="card">
                    <div class="card-header bg-primary" style="--bs-bg-opacity: .10">New Password</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="inputNewPassword" class="form-label">New Password *</label>
                            <input type="password" name="newPassword" class="form-control" id="inputNewPassword" aria-describedby="newPasswordHelp" placeholder="Enter your new password">
                            <?php
                            if (isset($errors["newPassword"]) && count($errors["newPassword"]) != 0) { ?> <!-- ajout du count pour ne pas avoir d'espace inutile !-->
                                <ul class="text-danger">
                                    <?php foreach ($errors["newPassword"] as $error) { ?>
                                        <li><?= $error ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <div id="newPasswordHelp" class="form-text text-secondary ">Password must be 8-16 characters long with uppercase, number and punctuation</div>
                        </div>
                        <div class="mb-3">
                            <label for="inputConfirmPassword" class="form-label">Confirm New Password *</label>
                            <input type="password" name="confirmPassword" placeholder="Confirm your new password" class="form-control" id="inputConfirmPassword" aria-describedby="confirmPasswordHelp" >
                            <?php
                            if (isset($errors["confirmPassword"]) && count($errors["confirmPassword"]) != 0) { ?> <!-- ajout du count pour ne pas avoir d'espace inutile !-->
                                <ul class="text-danger">
                                    <?php foreach ($errors["confirmPassword"] as $error) { ?>
                                        <li><?= $error ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <div id="confirmPasswordHelp" class="form-text text-secondary ">Re-enter your new password to confirm it matches</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </form>
    <footer>
        <?php require 'navbar_user.php'; ?>
    </footer>
</body>

</html>