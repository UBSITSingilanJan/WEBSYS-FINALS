
<?php
    session_start();
    require_once "dbconn.php";

    if(isset($_SESSION["ROLE"])){
        if($_SESSION["ROLE"] == "admin"){
            header("Location: admin/index.php");
        } else {
            header("Location: customer/index.php");
        }
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login - Worker Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container min-vh-100 d-flex justify-content-center align-items-center">

        <div class="card shadow border-0 rounded-4" style="max-width: 420px; width: 100%;">

            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">
                    <h2 class="fw-bold">Welcome Back!</h2>
                    <p class="text-muted">Login to your account</p>
                </div>

                <form method="post" action="login.php">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control form-control-lg"
                            placeholder="Enter your email"
                            required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control form-control-lg"
                            placeholder="Enter your password"
                            required>
                    </div>
                    <div class="d-grid mb-3">
                        <button
                            type="submit"
                            name="login_btn"
                            class="btn btn-primary btn-lg">
                            Login
                        </button>
                    </div>
                    <div class="text-center">
                        <span class="text-muted">Don't have an account?</span>
                        <a href="register.php" class="text-decoration-none fw-semibold">
                            Register
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
