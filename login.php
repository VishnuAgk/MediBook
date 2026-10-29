<?php
session_start();
require_once __DIR__ . '/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $user = mongoFindOne("users", ["email" => $email]);
    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = $user;
        header($user["role"] === "admin" ? "Location: admin.php" : "Location: index.php");
        exit;
    }
    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - MediBook</title><link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="logo center">🩺 <span>Medi</span>Book</div>
        <p class="eyebrow">WELCOME BACK</p>
        <h1>Login to MediBook</h1>
        <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="post">
            <label>Email<input type="email" name="email" placeholder="you@example.com" required></label>
            <label>Password<input type="password" name="password" placeholder="Enter password" required></label>
            <button class="btn-primary wide" type="submit">Login</button>
        </form>
        <div class="demo-login">
            <strong>Every step toward better health starts with care.</strong>
            <p style="text-align:center;margin-bottom:0;">
        Don't have an account?
        <a href="register.php"
           style="color:#ff4f8b; font-weight:700; text-decoration:none;">
            Create Account
        </a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
