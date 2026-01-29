<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title> 🛒 Sign Up</title>
    <base href="<?= $web_root ?>" ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-navy" data-bs-theme="dark">
    <main class="container">
        <div class="position-absolute top-50 start-50 translate-middle" style="border: 1px solid #262e3c; border-radius: 5px; padding: 20px; background-color: #27272a; width: 350px;">
            <h4 class="text-white fw-bold text-center">Sign Up</h3>
                <hr>
                <form method="post" action="user/signup">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="emailHelp"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="Email" aria-label="Email" aria-describedby="emailHelp">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="fullNameHelp"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" name="fullname" placeholder="Full Name" aria-label="FullName" aria-describedby="fullNameHelp">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="pseudoHelp"><i class="bi bi-universal-access-circle"></i></i></span>
                        <input type="text" class="form-control" name="pseudo" placeholder="Pseudo" aria-label="Pseudo" aria-describedby="pseudoHelp">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="passwordHelp"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" aria-label="Password" aria-describedby="passwordHelp">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="confirmPasswordHelp"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm your password" aria-label="ConfirmPassword" aria-describedby="confirmPasswordHelp">
                    </div>


                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="submit">Sign Up</button>
                        <button class="btn btn-danger" type="button">Cancel</button>
                    </div>


                </form>
        </div>

    </main>
</body>

</html>