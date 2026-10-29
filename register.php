<?php
session_start();
require_once __DIR__ . '/db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($name === "" || $email === "" || $phone === "" || $password === "") {
        $error = "Please fill in all fields.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }
    elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Please enter a valid 10-digit phone number.";
    }
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    }
    elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    }
    elseif (mongoFindOne("users", ["email" => $email])) {
        $error = "An account with this email already exists.";
    }
    else {

        mongoInsert("users", [
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "role" => "user",
            "created_at" => date("Y-m-d H:i:s")
        ]);

        $success = "Account created successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Create Account - MediBook</title>

<style>

/* =========================
   BASIC RESET
========================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {

    font-family: Arial, Helvetica, sans-serif;

    min-height: 100vh;

    background:
        radial-gradient(
            circle at 10% 20%,
            rgba(32, 180, 166, 0.14),
            transparent 25%
        ),
        radial-gradient(
            circle at 90% 80%,
            rgba(100, 110, 230, 0.10),
            transparent 25%
        ),
        linear-gradient(
            135deg,
            #effdfb,
            #f5f7ff
        );

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 35px 15px;

    color: #101828;
}


/* =========================
   REGISTER CARD
========================= */

.register-card {

    width: 440px;

    background: rgba(255, 255, 255, 0.96);

    border-radius: 24px;

    padding: 38px 40px;

    box-shadow:
        0 20px 55px rgba(31, 48, 80, 0.12),
        0 5px 20px rgba(31, 48, 80, 0.05);

    border: 1px solid rgba(220, 228, 240, 0.8);
}


/* =========================
   LOGO
========================= */

.logo {

    text-align: center;

    font-size: 25px;

    font-weight: 800;

    margin-bottom: 35px;

    color: #101828;
}

.logo-icon {

    color: #655ee8;

    margin-right: 5px;
}

.logo .medi {

    color: #138f88;
}

.logo .book {

    color: #101828;
}


/* =========================
   HEADING
========================= */

.eyebrow {

    color: #087e78;

    font-size: 13px;

    font-weight: 800;

    letter-spacing: 2px;

    margin-bottom: 15px;
}

h1 {

    font-size: 32px;

    line-height: 1.15;

    margin-bottom: 10px;

    color: #101828;
}

.subtitle {

    color: #667085;

    font-size: 15px;

    margin-bottom: 28px;
}


/* =========================
   FORM
========================= */

.form-group {

    margin-bottom: 18px;
}

.form-group label {

    display: block;

    font-size: 14px;

    font-weight: 700;

    color: #101828;

    margin-bottom: 8px;
}


/* =========================
   INPUT
========================= */

.input-box {

    position: relative;
}

.input-icon {

    position: absolute;

    left: 16px;

    top: 50%;

    transform: translateY(-50%);

    font-size: 17px;

    color: #98a2b3;
}

.input-box input {

    width: 100%;

    height: 48px;

    border: 1px solid #d6dfeb;

    border-radius: 11px;

    background: #ffffff;

    padding: 0 15px 0 46px;

    font-size: 14px;

    color: #101828;

    outline: none;

    transition: 0.2s;
}

.input-box input::placeholder {

    color: #98a2b3;
}

.input-box input:focus {

    border-color: #159d94;

    box-shadow:
        0 0 0 3px rgba(21, 157, 148, 0.10);
}


/* =========================
   PASSWORD NOTE
========================= */

.password-note {

    font-size: 11px;

    color: #98a2b3;

    margin-top: 6px;
}


/* =========================
   CREATE BUTTON
========================= */

.create-btn {

    width: 100%;

    height: 49px;

    border: none;

    border-radius: 11px;

    background: linear-gradient(
        90deg,
        #168f87,
        #20a995
    );

    color: white;

    font-size: 14px;

    font-weight: 800;

    cursor: pointer;

    margin-top: 5px;

    box-shadow:
        0 10px 22px rgba(22, 143, 135, 0.20);

    transition: 0.2s;
}

.create-btn:hover {

    transform: translateY(-1px);

    box-shadow:
        0 13px 28px rgba(22, 143, 135, 0.28);
}


/* =========================
   OR DIVIDER
========================= */

.divider {

    display: flex;

    align-items: center;

    gap: 15px;

    margin: 25px 0 20px;

    color: #98a2b3;

    font-size: 12px;
}

.divider::before,
.divider::after {

    content: "";

    flex: 1;

    height: 1px;

    background: #e5eaf1;
}


/* =========================
   LOGIN BOX
========================= */

.login-box {

    background: #f5f8fc;

    border-radius: 11px;

    padding: 17px;

    text-align: center;

    color: #667085;

    font-size: 13px;
}

.login-box a {

    display: block;

    margin-top: 7px;

    color: #087e78;

    font-weight: 800;

    text-decoration: none;

    font-size: 14px;
}

.login-box a:hover {

    text-decoration: underline;
}


/* =========================
   ERROR
========================= */

.error {

    background: #fff1f1;

    color: #d92d20;

    border: 1px solid #fecdca;

    padding: 11px 13px;

    border-radius: 9px;

    font-size: 13px;

    margin-bottom: 18px;
}


/* =========================
   SUCCESS
========================= */

.success {

    background: #ecfdf3;

    color: #027a48;

    border: 1px solid #abefc6;

    padding: 12px;

    border-radius: 9px;

    text-align: center;

    font-size: 14px;

    margin-bottom: 18px;
}

.login-button {

    display: block;

    width: 100%;

    text-align: center;

    background: #159d94;

    color: white;

    padding: 13px;

    border-radius: 10px;

    text-decoration: none;

    font-weight: 700;
}


/* =========================
   MOBILE
========================= */

@media (max-width: 500px) {

    .register-card {

        width: 100%;

        padding: 30px 24px;

        border-radius: 20px;
    }

    h1 {

        font-size: 27px;
    }
}

</style>

</head>

<body>


<div class="register-card">


    <!-- LOGO -->

    <div class="logo">

        <span class="logo-icon">🩺</span>

        <span class="medi">Medi</span><span class="book">Book</span>

    </div>


    <!-- HEADING -->

    <div class="eyebrow">

        JOIN MEDIBOOK

    </div>

    <h1>

        Create Your Account

    </h1>

    <p class="subtitle">

        Fill in your details to get started

    </p>


    <!-- ERROR -->

    <?php if ($error): ?>

        <div class="error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- SUCCESS -->

    <?php if ($success): ?>

        <div class="success">

            <?php echo htmlspecialchars($success); ?>

        </div>

        <a href="login.php" class="login-button">

            Login to MediBook

        </a>


    <?php else: ?>


    <!-- FORM -->

    <form method="POST">


        <!-- NAME -->

        <div class="form-group">

            <label>

                Full Name

            </label>

            <div class="input-box">

                <span class="input-icon">👤</span>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter your full name"
                    required
                >

            </div>

        </div>


        <!-- EMAIL -->

        <div class="form-group">

            <label>

                Email

            </label>

            <div class="input-box">

                <span class="input-icon">✉</span>

                <input
                    type="email"
                    name="email"
                    placeholder="you@example.com"
                    required
                >

            </div>

        </div>


        <!-- PHONE -->

        <div class="form-group">

            <label>

                Phone Number

            </label>

            <div class="input-box">

                <span class="input-icon">☎</span>

                <input
                    type="tel"
                    name="phone"
                    placeholder="Enter your phone number"
                    maxlength="10"
                    required
                >

            </div>

        </div>


        <!-- PASSWORD -->

        <div class="form-group">

            <label>

                Password

            </label>

            <div class="input-box">

                <span class="input-icon">🔒</span>

                <input
                    type="password"
                    name="password"
                    placeholder="Create a password"
                    minlength="6"
                    required
                >

            </div>

            <div class="password-note">

                At least 6 characters

            </div>

        </div>


        <!-- CONFIRM PASSWORD -->

        <div class="form-group">

            <label>

                Confirm Password

            </label>

            <div class="input-box">

                <span class="input-icon">🔒</span>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    minlength="6"
                    required
                >

            </div>

        </div>


        <!-- BUTTON -->

        <button
            type="submit"
            class="create-btn"
        >

            Create Account

        </button>


    </form>


    <!-- DIVIDER -->

    <div class="divider">

        OR

    </div>


    <!-- LOGIN -->

    <div class="login-box">

        Already have an account?

        <a href="login.php">

            Login to MediBook

        </a>

    </div>


    <?php endif; ?>


</div>


</body>

</html>