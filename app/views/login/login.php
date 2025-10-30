<?php
require_once CONFIG_PATH . '/config.php';



if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$flash = $_SESSION['flash'] ?? null;
// prepare BASE url for assets + form action
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AutoNexus - Sign In</title>

    <!-- If your folder name is literally "assests", keep it as-is -->
    <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/app/views/login/assests/css/styles.css" />
</head>
<body>
    <?php if ($flash !== null): ?>
        <script>
            alert(<?= json_encode($flash) ?>);
        </script>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="container">
        <!-- Left side with car image -->
        <div class="left-panel">
            <div class="car-image">
                <img src="<?= htmlspecialchars($base) ?>/app/views/login/assests/car-image.jpg" alt="AutoNexus Car" />
            </div>
        </div>

        <!-- Right side with login form -->
        <div class="right-panel">
            <div class="login-container">
                <!-- Logo and brand -->
                <div class="brand">
                    <img src="<?= htmlspecialchars($base) ?>/app/views/login/assests/autonexus-logo.jpg" alt="AutoNexus Logo" class="brand-logo" />
                    <span class="brand-name">AutoNexus</span>
                </div>

                <!-- Login form -->
                <div class="form-section">
                    <h1>Sign in to your account</h1>

                    <!-- Post to the router endpoint, not db_login.php -->
                    <form class="login-form" id="loginForm" method="post" action="<?= htmlspecialchars($base . '/login', ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-group">
                            <label for="email">Email/Username</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="your@email.com"
                                required
                            />
                        </div>

                        <div class="form-group">
                            <div class="password-label">
                                <label for="password">Password</label>
                                <a href="#" class="forgot-password">Forgot password?</a>
                            </div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                            />
                        </div>

                        <button type="submit" class="login-btn">Login</button>
                    </form>

                    <div class="signup-link">
                        <span>Don't have an account?</span>
                        <a href="<?= htmlspecialchars($base) ?>/register" class="signup-btn">Sign up</a>
                        <!-- ^ adjust if your register route/file differs -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= htmlspecialchars($base) ?>/app/views/login/assests/js/script.js"></script>
</body>
</html>
