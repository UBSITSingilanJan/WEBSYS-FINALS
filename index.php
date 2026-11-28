<?php
session_start();
$passwordChangedNotice = $_SESSION['password_changed_notice'] ?? '';
unset($_SESSION['password_changed_notice']);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Barangay Portal - Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="theme.css">
	<style>
		html, body {
			height: 100%;
			margin: 0;
		}

		body {
			background-color: #f4f7f6; 
			display: flex;
			flex-direction: column;
			min-height: 100vh;
			overflow: hidden;
		}

		.container {
			display: flex;
			align-items: center;
			justify-content: center;
			min-height: 100vh;
			padding: 16px;
			box-sizing: border-box;
		}

		.login-card {
			width: 100%;
			max-width: 400px;
			margin: 0;
			background: white;
			border-radius: 8px;
			border-top: 5px solid #0b3d6b; 
			box-shadow: 0 10px 25px rgba(0,0,0,0.05);
			max-height: 100%;
			overflow-y: auto;
		}

		.btn-custom {
			background: #0b3d6b;
			color: white;
			font-weight: 600;
		}
		.btn-custom:hover {
			background: #072a4d;
			color: white;
		}

		.login-card img {
			width: 100px;
			height: 100px;
			object-fit: contain;
		}

		@media (max-height: 700px) {
			.login-card img {
				width: 70px;
				height: 70px;
			}
			.login-card {
				padding: 16px !important;
			}
			.login-card h5 {
				font-size: 15px;
			}
			.login-card .mb-4 {
				margin-bottom: 12px !important;
			}
		}

		@media (max-height: 560px) {
			.login-card img {
				width: 50px;
				height: 50px;
			}
			.login-card p {
				display: none;
			}
			.login-card h5 {
				font-size: 14px;
				margin-top: 8px !important;
			}
		}
	</style>
</head>
<body>

	<?php if ($passwordChangedNotice !== ''): ?>
		<div class="alert alert-success" role="alert" style="position:fixed; top:16px; left:50%; transform:translateX(-50%); z-index:1050; width:min(92%, 520px); text-align:center;">
			<?= htmlspecialchars($passwordChangedNotice, ENT_QUOTES, 'UTF-8') ?>
		</div>
	<?php endif; ?>

	<div class="container">
		<div class="card login-card p-4">
			
			<div class="text-center mb-4">
				<img src="logo.jpg" alt="Barangay Logo">
				<h5 class="mt-3" style="color: #0b3d6b; font-weight: bold;">HONEYMOON HOLYGHOST BARANGAY</h5>
				<p class="text-muted" style="font-size: 14px;">Resident Portal Login</p>
			</div>

			<form method="post" action="login.php">
				<div class="mb-3">
					<label for="email" class="form-label text-muted" style="font-size: 14px;">Email Address</label>
					<input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
				</div>
				<div class="mb-4">
					<label for="password" class="form-label text-muted" style="font-size: 14px;">Password</label>
					<input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
				</div>
				
				<button name="login_btn" class="btn btn-custom w-100 mb-3">Login to Portal</button>
			</form>

		</div>
	</div>

</body>
</html>