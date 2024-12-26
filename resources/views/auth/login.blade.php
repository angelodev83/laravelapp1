<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="/images/mgmt88-logo.ico" sizes="32x32" />
	<link rel="icon" href="/images/mgmt88-logo.ico" sizes="192x192" />
	<link rel="apple-touch-icon" href="/images/mgmt88-logo.ico" />
	<!--plugins-->
	<link href="/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
	<link href="/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
	<!-- loader-->
	<link href="/assets/css/pace.min.css" rel="stylesheet" />
	<script src="/assets/js/pace.min.js"></script>
	<!-- Bootstrap CSS -->
	<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="/assets/css/bootstrap-extended.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="/assets/css/app.css" rel="stylesheet">
	<link href="/assets/css/icons.css" rel="stylesheet">
	<link href="/css/custom.css" rel="stylesheet" />
	<title>MGMT88 Portal</title>
	<style>
		/* login */

		body {
			background-image: url("/images/INTRANET bg.png");
			background-size: 100% 100%;
		}
		.card-body{
			/* width: 100px; */
			height: 450px;
		}

		.login-sub-title{
			font-size: 170%;
			margin-left: -10%;
			margin-bottom: -5%;
			color: #000000;
		}

		.login-div-logo{
			height: 150px;
		}

		.login-main-text-color{
			color: #5A566B;
		}

		
		/* CSS for Mobile devices */
		@media (max-width: 480px) {
		/* Insert your CSS rules for mobile devices here */
		}

		/* CSS for Tablet devices */
		@media (min-width: 481px) and (max-width: 1024px) {
		/* Insert your CSS rules for tablet devices here */
		/* login */
			body {
				background-image: url("/images/INTRANET bg.png");
				background-size: 100% 100%;
			}

			.card-body {
				/* width: 100px; */
				height: 450px;
			}

			.login-sub-title {
				font-size: 170%;
				margin-left: -10%;
				margin-bottom: -5%;
				color: #000000;
			}

			.login-div-logo {
				height: 100px;
				margin-left: 20%;
				display: block;
				/* margin-left: auto;
				margin-right: auto; */
				width: 50%;
			}

			.login-main-text-color {
				color: #5a566b;
			}
		}

	</style>
</head>

<body>
	<!--wrapper-->
	<div class="wrapper">
		<!-- <div class="authentication-header"></div> -->
		<div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
			<div class="container">
				<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
					<div class="col mx-auto">
						
						<div class="card rounded-4">
							<div class="card-body">
								<div class="p-4 rounded">
									<div class="text-center">
										<img src="/images/mgmt88-pharmacy-logo.png" width="230" class="img-fluid mb-3" alt="" />

										<h4 class="login-main-text-color">MGMT88 Intranet Portal</h4>
										<p class="login-main-text-color">Please sign-in your account</a>
										</p>
									</div>
									
									<div class="form-body">
										
                                         <form class="row g-3 login100-form validate-form login-main-text-color" method="POST" action="{{ route('login') }}">
                            				@csrf
											<div class="col-12">
												<label for="inputEmailAddress" class="form-label">Email</label>
												<input type="email" class="form-control" id="email" name="email" placeholder="Enter your Email">
												@error('email')
												<div class="p-2 border-2 border-danger rounded-2">
													<span class="text-danger">{{ $message }}</span>
												</div>
												@enderror
											</div>
											<div class="col-12">
												<label for="inputChoosePassword" class="form-label col-7">Password</label>
										
												<div class="input-group" id="show_hide_password">
													<input type="password" class="form-control border-end-0" id="password" name="password"  > 
												</div>
												@error('password')
												<div class="p-2 border-2 border-danger rounded-2">
													<span class="text-danger">{{ $message }}</span>
												</div>
												@enderror
											</div>
											
											
											
											<div class="col-12">
												<div class="d-grid">
													<button type="submit" class="btn btn-primary">Sign in</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--end row-->
			</div>
		</div>
	</div>
	<!--end wrapper-->
	<!-- Bootstrap JS -->
	<script src="/assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="/assets/js/jquery.min.js"></script>
	
</body>

</html>
