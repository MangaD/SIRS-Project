<!DOCTYPE html>

<html lang="en">
	<head>
		<title>Home</title>

		<link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />

		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!--Bootsrap 4 CDN-->
		<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
			integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
			crossorigin="anonymous"> -->

		<!--Fontawesome CDN-->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css"
			integrity="sha384-KA6wR/X5RY4zFAHpv/CnoG2UW1uogYfdnP67Uv7eULvTveboZJg0qUpmJZb5VqzN"
			crossorigin="anonymous" />

		<!--Custom styles-->
		<!--
			Bootstrap downloaded from https://bootswatch.com/
			Superhero theme
		-->
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/login.css">

		<script src="js/login.js"></script>

	</head>
	<body>

		<!-- Template from: https://bootsnipp.com/snippets/vl4R7 -->
		<div class="container">

			<h1>Smartphone as a security token</h1>

			<div class="container">
				<div class="d-flex justify-content-center h-100">

					<div class="card">
						<div class="card-header">
							<h3>Sign In</h3>
						</div>
						<div class="card-body">
							<form>
								<div class="input-group form-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fas fa-user"></i></span>
									</div>
									<input type="text" class="form-control"
										placeholder="username">
									
								</div>
								<div class="input-group form-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fas fa-key"></i></span>
									</div>
									<input type="password" class="form-control"
										placeholder="password">
								</div>
								<div class="row align-items-center remember">
									<input type="checkbox">Remember Me
								</div>
								<div class="form-group">
									<input type="submit" value="Login"
										class="btn float-right login_btn"
										onclick="sendMessage('login');">
								</div>
							</form>
						</div>
						<div class="card-footer">
							<div class="d-flex justify-content-center links">
								Don't have an account?<a href="#">Sign Up</a>
							</div>
							<div class="d-flex justify-content-center">
								<a href="#">Forgot your password?</a>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>


		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
			integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
			crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
			integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
			crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
			integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
			crossorigin="anonymous"></script>

		<div id="particles-js"></div>
		<script src="js/particles.min.js"></script>
		<script>
			particlesJS.load('particles-js', 'js/particles.json', function() {
				//console.log('callback - particles.js config loaded');
			});
		</script>
	</body>
</html>
