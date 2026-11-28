<?php
require_once "dbconn.php";
require_once "classes/users.php";
require_once "classes/resident_profile.php";

$conn = new User($pdo);
$error = null;
$profileValues = $_POST;
$email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';

if (isset($_POST['register_btn'])) {
    $profileResult = ResidentProfile::from_post($_POST);
    $profileValues = $profileResult['data'] ? array_merge($_POST, $profileResult['data']) : $_POST;
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $passwordConfirm = is_string($_POST['password_confirm'] ?? null) ? $_POST['password_confirm'] : '';

    if ($profileResult['error'] !== null) {
        $error = $profileResult['error'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $conn->insert_data($profileResult['data'], $email, $password);
            header("Location: index.php");
            exit();
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $error = 'An account with that email address already exists.';
            } else {
                throw $exception;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resident Registration</title>
    <link rel="stylesheet" href="application/application_style.css">
</head>
<body class="resident-form-body">
<main class="resident-form-page">
    <header class="resident-form-header">
        <p class="resident-form-kicker">HONEYMOON HOLYGHOST BARANGAY</p>
        <h1>Create a Resident Account</h1>
        <p>Complete your resident profile and account information to register.</p>
    </header>

    <?php if ($error !== null): ?>
        <p class="application-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form class="resident-form-card" method="post">
        <?php require "application/resident_profile_fields.php"; ?>

        <section class="application-form-section" aria-labelledby="account-heading">
            <h4 id="account-heading">Account Information</h4>
            <div class="application-fields-grid">
                <label>Email address *
                    <input class="form-control" type="email" name="email" maxlength="100" autocomplete="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <label>Password *
                    <input class="form-control" type="password" name="password" minlength="8" autocomplete="new-password" required>
                </label>
                <label>Confirm password *
                    <input class="form-control" type="password" name="password_confirm" minlength="8" autocomplete="new-password" required>
                </label>
            </div>
        </section>

        <div class="application-actions">
            <a class="btn-secondary" href="index.php">Cancel</a>
            <button class="btn-primary" type="submit" name="register_btn">Create Account</button>
        </div>
    </form>
</main>
</body>
</html>
