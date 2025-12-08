<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>🛒 Log In</title>
        <base href="<?= $web_root ?>"/>
        <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
       <div class="login">

    <div class="w-100" style="max-width: 480px;">

        <!-- Logo + titre -->
        <div class="text-center mb-4 text-light">
            <div class="fs-1 mb-2">🛒EPayFC</div>
            
        </div>

        <!-- Carte de login -->
        <div class="card shadow login-card border-0 text-light mb-4">
            <div class="card-body p-4">

                <h1 class="h4 text-center mb-4">Sign in</h1>

                <!-- Formulaire de connexion -->
                <form method="post" action="user/login">

                    <!-- Email -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-0 text-secondary">
                                &#128104;
                            </span>
                            <input
                                type="email"
                                name="email"
                                class="form-control bg-dark text-light border-0"
                                placeholder="Mail"
                                required
                            >
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-0 text-secondary">
                                &#128273;
                            </span>
                            <input
                                type="password"
                                name="password"
                                class="form-control bg-dark text-light border-0"
                                placeholder="******"
                                required
                            >
                        </div>
                    </div>

                    <!-- Bouton Login -->
                    <div class="d-grid mb-3">
                        <button class="btn btn-primary fw-semibold" type="submit">
                            Login
                        </button>
                    </div>

                    <!-- Bouton Continue as guest -->
                    <div class="d-grid mb-3">
                        <a href="item" class="btn btn-secondary">
                            Continue as guest
                        </a>
                    </div>

                </form>
                
               
                            <p class="text-center mt-2 mb-0">
                                <a href="user/signup" class="link-light text-decoration-underline">New here ? Click here to subscribe ! </a>
                            </p>    
                    </div>
                    <?php if(count($errors)!=0): ?>
                        <div >
                            <p>Please correct the folowing error(s):</p>
                            <ul>
                                <?php foreach($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach;?>    
                            </ul>
                        </div>
                 <?php endif;?>    
                    <hr>
                    <div class="text-center mb-4">
                        <h2 class="h6 text-warning fw-bold mb-3">For Debug Purpose</h2>

                        <p><a href="item" class="text-decoration-none  text-light">Lgin as <strong>bverhaegen@epfc.eu</strong></a></p>
                        <p><a href="item" class="text-decoration-none  text-light">Lgin as <strong>mamichel@epfc.eu</strong></a></p>
                        <p><a href="item" class="text-decoration-none text-light">Lgin as <strong>quhouben@epfc.eu</strong></a></p>
                        <p><a href="item" class="text-decoration-none  text-light">Lgin as <strong>xapigeolet@epfc.eu</strong></a></p>
                        <p><a href="setup/restore" class="text-decoration-none text-success">Restore original data</a></p>
                        <p><a href="setup/backup" class="text-decoration-none text-warning">Backup personal data</a></p>
                        <p><a href="setup/restore" class="text-decoration-none text-warning">Restore personal data</a></p>

                    </div>
                </div>
            </div>
        </div>
    </body>
</html>