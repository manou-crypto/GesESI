<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Login</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body class="login-page">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Side / Welcome Back -->
            <div class="col-md-6 login-left d-none d-md-block">
                <h1>BIENVENUE !</h1>
                <p>Connectez-vous pour accéder à votre espace ...</p>
                 <!--<div class="login-socials">
                    <a href="#"><i class="fab fa-facebook-f"></i></a> 
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>-->
            </div>

            <!-- Right Side / Login Form -->
            <div class="col-md-5 offset-md-1">
                <div class="login-right">
                    <h2 class="mb-4">Sign in</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo \App\Core\Security::escape($error); ?></div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>/index.php?url=auth/login" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        
                        <div class="mb-3">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="Email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Password" required>
                        </div>
                        
                        <div class="mb-4 form-check d-flex justify-content-between px-0">
                            <div>
                                <input type="checkbox" class="form-check-input" id="remember" style="margin-left: 0;">
                                <label class="form-check-label ms-4" for="remember">Remember Me</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">Sign in now</button>

                        <div class="mt-3 text-left">
                            <a href="#" class="text-white text-decoration-none" style="font-size: 0.9rem;">Lost your password?</a>
                        </div>
                    </form>
                    
                    <!--<div class="login-footer mt-5">
                        <p>By clicking on "Sign in now" you agree to<br>Terms of Service | Privacy Policy</p>
                    </div>-->
                </div>
            </div>
        </div>
    </div>
</body>
</html>
