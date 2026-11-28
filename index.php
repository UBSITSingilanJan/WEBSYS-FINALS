<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Barangay Portal - Login</title>
	<link rel="stylesheet" href="theme.css">
</head>
<body class="login-page">

	<div class="container">
		<div class="login-card">
			
			<div class="login-header">
				<img src="logo.jpg" alt="Barangay Logo">
				<h1>HONEYMOON HOLYGHOST BARANGAY</h1>
				<p>Resident Portal Login</p>
			</div>

			<form method="post" action="login.php">
				<div class="login-field">
					<label for="email">Email Address</label>
					<input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
				</div>
				<div class="login-field">
					<label for="password">Password</label>
					<input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
				</div>
				
				<button name="login_btn" class="btn-custom">Login to Portal</button>
				
				</div>
			</form>

		</div>
	</div>

</body>
</html>