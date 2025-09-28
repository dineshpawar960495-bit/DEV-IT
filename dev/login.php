<?php
session_start();

// If user is already logged in, redirect to home
if (isset($_SESSION['email'])) {
    header("Location: home.php");
    exit;
}

// Get and clear errors from session
$errors = $_SESSION['login_errors'] ?? [];
unset($_SESSION['login_errors']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DEV Project</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Login</h2>

            <!-- Show errors -->
            <?php if (!empty($errors)) { ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $err) { ?>
                        <p><?php echo htmlspecialchars($err); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>

            <form action="login-handler.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="login">Login</button>
                </div>
                <p class="text-center">
                    Not registered? <a href="signup.php">Signup here</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
