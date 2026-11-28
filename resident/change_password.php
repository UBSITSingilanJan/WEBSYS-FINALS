<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'resident') {
    header("Location: ../index.php");
    exit();
}

require_once "../dbconn.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = is_string($_POST['new_password'] ?? null) ? $_POST['new_password'] : '';
    $confirmation = is_string($_POST['confirm_password'] ?? null) ? $_POST['confirm_password'] : '';

    if (strlen($password) < 8) {
        $error = 'Your new password must be at least 8 characters.';
    } elseif ($password !== $confirmation) {
        $error = 'The passwords do not match.';
    } else {
        $update = $pdo->prepare("UPDATE user SET password = :password, must_change_password = 0 WHERE user_id = :user_id AND role = 'resident'");
        $update->execute([
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':user_id' => (int)$_SESSION['user_id'],
        ]);

        if ($update->rowCount() === 1) {
            session_regenerate_id(true);
            $_SESSION = [];
            $_SESSION['password_changed_notice'] = 'Password changed successfully. Please log in again.';
            header("Location: ../index.php");
            exit();
        }

        $error = 'Unable to update your password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password</title>
    <link rel="stylesheet" href="../theme.css">
    <style>
        .password-card { max-width: 520px; margin: 40px auto; }
        .password-card label { display: block; margin: 14px 0 6px; }
        .password-card input { width: 100%; padding: 10px; border: 1px solid #bbb; border-radius: 5px; }
        .password-card button { margin-top: 18px; padding: 10px 16px; border: 0; border-radius: 5px; background: #0b3d6b; color: #fff; cursor: pointer; }
        .password-error { color: #b42318; }
    </style>
</head>
<body>
<div class="dashboard">
    <main class="main-content">
        <section class="panel password-card">
            <h2>Change your password</h2>
            <p>Your account was created by the barangay staff. You must set a new password before continuing.</p>
            <?php if ($error !== ''): ?><p class="password-error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
            <form method="post">
                <label for="new_password">New password</label>
                <input id="new_password" type="password" name="new_password" minlength="8" autocomplete="new-password" title="Password must be at least 8 characters." required>
                <label for="confirm_password">Confirm new password</label>
                <input id="confirm_password" type="password" name="confirm_password" minlength="8" autocomplete="new-password" title="Password must be at least 8 characters." required>
                <button type="submit">Update password</button>
            </form>
        </section>
    </main>
</div>
</body>
</html>
