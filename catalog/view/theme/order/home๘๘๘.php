test lap
<div class="container">
	<div class="row vh-100 d-flex justify-content-center">
		<div class="col-12 align-self-center">
			<div class="row">
				<div class="col-lg-5 mx-auto">
					<div class="card">
						<div class="card-body p-0 auth-header-box">
							<div class="text-center p-3">
								<a href="index.html" class="logo logo-admin">
									<img src="assets/images/logo-sm.png" height="50" alt="logo" class="auth-logo">
								</a>
								<h4 class="mt-3 mb-1 font-weight-semibold text-white font-18">Let's Get Started Dastyle</h4>
								<p class="text-muted mb-0">Sign in to continue to Dastyle.</p>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav-border nav nav-pills" role="tablist">
								<li class="nav-item">
									<a class="nav-link active font-weight-semibold" data-toggle="tab" href="#LogIn_Tab" role="tab">Log In</a>
								</li>
							</ul>
							<!-- Tab panes -->
							<div class="tab-content">
								<div class="tab-pane active p-3 pt-3" id="LogIn_Tab" role="tabpanel">
									<form class="form-horizontal auth-form my-4" action="<?php echo $action; ?>" method="POST">
										<div class="form-group">
											<label for="username">Username</label>
											<div class="input-group mb-3">
												<input type="text" class="form-control" name="username" id="username" placeholder="Enter username">
											</div>
										</div>
										<!--end form-group-->
										<div class="form-group">
											<label for="userpassword">Password</label>
											<div class="input-group mb-3">
												<input type="password" class="form-control" name="password" id="userpassword" placeholder="Enter password">
											</div>
										</div>
										<!--end form-group-->
										<div class="form-group mb-0 row">
											<div class="col-12 mt-2">
												<!-- <button class="btn btn-primary btn-block waves-effect waves-light" type="submit">Log In <i class="fas fa-sign-in-alt ml-1">
												</i>
												</button> -->
												<button class="btn btn-primary btn-block waves-effect waves-light" type="submit">Log In <i class="fas fa-sign-in-alt ml-1">
												</i>
												</button>
											</div>
											<!--end col-->
										</div>
										<!--end form-group-->
									</form>
									<!--end form-->
									
								</div>
							</div>
							
						</div>
			</div>
			<!--end card-body-->
			<div class="card-body bg-light-alt text-center">
				<span class="text-muted d-none d-sm-inline-block">Power by Friendlysoftpro © 2020</span>
			</div>
		</div>
		<!--end card-->
	</div>
	<!--end col-->
</div>
<!--end row-->
</div>
<!--end col-->
</div>
<!--end row-->
</div>
<!--end container-->
<!-- End Log In page -->