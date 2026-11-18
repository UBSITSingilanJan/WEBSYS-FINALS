<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Barangay Portal - Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
	
	
	<link href="styles.css" rel="stylesheet">
</head>
<body class="bg-light">

	
	<div class="container min-vh-100 d-flex justify-content-center align-items-center">
		

		<div class="card border-0" style="max-width: 400px; width: 100%;">
			<div class="card-body p-4 p-md-5">

				<div class="text-center mb-4">
					<h2 class="fw-bold" style="color: #0b3d6b;">Welcome Back!</h2>
					<p class="text-muted">Honeymoon Holyghost Barangay</p>
				</div>

				<form method="post" action="login.php">
					<div class="mb-3">
						<label class="form-label fw-semibold">Email Address</label>
						<input type="email" name="email" class="form-control" placeholder="Enter your email" required>
					</div>
					
					<div class="mb-4">
						<label class="form-label fw-semibold">Password</label>
						<input type="password" name="password" class="form-control" placeholder="Enter your password" required>
					</div>
					
					<button name="login_btn" class="btn btn-primary w-100">Login to Portal</button>
					
					<div class="text-center mt-4">
						<a href="register.php" class="text-decoration-none fw-semibold">Create an account</a>
					</div>
				</form>

			</div>
		</div>
		
	</div>

</body>
</html>