<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Barangay Portal - Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
		}

		.top-bar {
			background-color: #0b3d6b; 
			color: white;
			padding: 10px 0;
			font-size: 14px;
			text-align: center;
			border-bottom: 4px solid #f1c40f; 
			flex-shrink: 0;
		}

		.login-card {
			width: 100%;
			max-width: 400px;
			margin: 40px auto;
			background: white;
			border-radius: 8px;
			border-top: 5px solid #0b3d6b; 
			box-shadow: 0 10px 25px rgba(0,0,0,0.05);
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
			width: 120px;
			height: 120px;
			object-fit: contain;
		}
	</style>
</head>
<body>

	<div class="top-bar">
		<strong>BENGUET</strong> | Baguio City
	</div>

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
				
				<div class="text-center">
					<a href="register.php" class="text-decoration-none" style="color: #0b3d6b;">Create an account</a>
				</div>
			</form>

		</div>
	</div>

</body>
</html>