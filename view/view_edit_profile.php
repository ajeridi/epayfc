<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title> 🛒 Edit Profile</title>
    <base href="<?= $web_root ?>" ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-navy" data-bs-theme="dark">
    <form method="post" action="user/edit_profile">
        <header>
            <!-- Barre au dessus de la page -->
            <nav class="navbar bg-navy-gray" data-bs-theme="dark">
                <div class="container-fluid">
                    <a class="navbar-brand text-blue" href="user/profile"><i class="bi bi-arrow-left"></i> </a>
                    <span class="navbar-brand position-absolute top-50 start-50 translate-middle h1 text-blue"">Edit Profile <i class=" bi bi-cart4 fs-4 text-white"></i></span>
                    <button type="submit" name="saveProfile" value="true" class="btn navbar-brand text-blue position-absolute top-50 end-0 translate-middle-y" href="user/profile"><i class="bi bi-floppy2-fill"></i></a>
                </div>
            </nav>
        </header>
        <main class="container mt-3" style="margin-bottom: 100px;">
            <!--  Personal information !-->
            <div class="container mb-3">
                <div class="card">
                    <div class="card-header bg-primary" style="--bs-bg-opacity: .10">Personal Information</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="inputFullName" class="form-label">Full Name *</label>
                            <input type="text" name="fullname" class="form-control" id="inputFullName" aria-describedby="fullNameHelp" value="<?= isset($newUser->full_name) ? $newUser->full_name : $user->full_name ?>">
                            <?php
                            if (isset($errors["fullname"])) { ?>
                                <ul class="text-danger">
                                    <?php foreach ($errors["fullname"] as $error) { ?>
                                        <li><?= $error ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <div id="fullNameHelp" class="form-text text-secondary ">Your complete name as it should appear on your profile</div>
                        </div>
                        <div class="mb-3">
                            <label for="inputUsername" class="form-label">Username *</label>
                            <input type="text" name="username" class="form-control" id="inputUsername" aria-describedby="usernameHelp" value="<?= isset($newUser->pseudo) ? $newUser->pseudo : $user->pseudo ?>">
                            <?php
                            if (isset($errors["username"])) { ?>
                                <ul class="text-danger">
                                    <?php foreach ($errors["username"] as $error) { ?>
                                        <li><?= $error ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <div id="usernameHelp" class="form-text text-secondary ">This will be your public display name on the platform</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Contact & Payment !-->
            <div class="container mb-3">
                <div class="card">
                    <div class="card-header bg-primary" style="--bs-bg-opacity: .10">Contact & Payment</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="inputEmail" class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" id="inputEmail" aria-describedby="emailHelp" value="<?= isset($newUser->email) ? $newUser->email : $user->email ?>">
                            <?php
                            if (isset($errors["email"])) { ?>
                                <ul class="text-danger">
                                    <?php foreach ($errors["email"] as $error) { ?>
                                        <li><?= $error ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <div id="emailHelp" class="form-text text-secondary ">We'll use this email to send you notifications about your sales and purchases</div>
                        </div>
                        <div class="mb-3">
                            <label for="inputIBAN" class="form-label"><span>IBAN</span> <span class="text-secondary">(Optional)</span></label>
                            <input type="text" name="iban" placeholder="BE99 9999 9999 9999" class="form-control" id="inputIBAN" aria-describedby="IBANHelp" value="<?= isset($newUser->iban) ? $newUser->iban : $user->iban ?>">
                            <?php
                            if (isset($errors["iban"])) { ?>
                                <ul class="text-danger">
                                    <?php foreach ($errors["iban"] as $error) { ?>
                                        <li><?= $error ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <div id="IBANHelp" class="form-text text-secondary ">For receiving payments from your sales. Leave empty if you don't want to receive payments directly.</div>
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