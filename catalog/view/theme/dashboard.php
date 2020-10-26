<div class="page-wrapper">
	<!-- Page Content-->
	<div class="page-content">
		<div class="container-fluid">
			<!-- Page-Title -->
			<div class="row">
				<div class="col-sm-12">
					<div class="page-title-box">
						<div class="row">
							<div class="col">
								<h4 class="page-title">Dashboard</h4>
							</div>
							<!--end col-->
							<div class="col-auto align-self-center">
							</div>
							<!--end col-->
						</div>
						<!--end row-->
					</div>
					<!--end page-title-box-->
				</div>
				<!--end col-->
			</div>
			<!--end row-->
			<!-- end page title end breadcrumb -->
			<div class="row mt-3">
                <div class="col-12">
                    <?php echo !empty($success) ? '<div class="alert alert-success" role="alert">'.$success.'</div>' : ''; ?>
                    <?php echo !empty($error) ? '<div class="alert alert-danger" role="alert">'.$error.'</div>' : ''; ?>
                </div>
            </div>
			<div class="row justify-content-center">
				<div class="col-md-6 col-lg-3">
					<div class="card report-card">
						<div class="card-body">
							<div class="row d-flex justify-content-center">
								<div class="col">
									<p class="text-dark mb-1 font-weight-semibold">Group Used</p>
									<h3 class="my-2"><?php echo number_format($group,0);?></h3>
								</div>
								<div class="col-auto align-self-center">
									<div class="report-main-icon bg-light-alt">
										<i class="fas fa-layer-group"></i>
									</div>
								</div>
							</div>
						</div>
						<!--end card-body-->
					</div>
					<!--end card-->
				</div>
				<!--end col-->
				<div class="col-md-6 col-lg-3">
					<div class="card report-card">
						<div class="card-body">
							<div class="row d-flex justify-content-center">
								<div class="col">
									<p class="text-dark mb-1 font-weight-semibold">Barcode purchased</p>
									<h3 class="my-2"><?php echo number_format($barcode,0);?></h3>
								</div>
								<div class="col-auto align-self-center">
									<div class="report-main-icon bg-light-alt">
										<i class="fas fa-barcode"></i>
									</div>
								</div>
							</div>
						</div>
						<!--end card-body-->
					</div>
					<!--end card-->
				</div>
				<!--end col-->
				<div class="col-md-6 col-lg-3">
					<div class="card report-card">
						<div class="card-body">
							<div class="row d-flex justify-content-center">
								<div class="col">
									<p class="text-dark mb-1 font-weight-semibold">Barcode Waiting</p>
									<h3 class="my-2"><?php echo number_format($waiting,0);?></h3>
								</div>
								<div class="col-auto align-self-center">
									<div class="report-main-icon bg-light-alt">
									<i class="fas fa-barcode"></i>
									</div>
								</div>
							</div>
						</div>
						<!--end card-body-->
					</div>
					<!--end card-->
				</div>
				<!--end col-->
				<div class="col-md-6 col-lg-3">
					<div class="card report-card">
						<div class="card-body">
							<div class="row d-flex justify-content-center">
								<div class="col">
									<p class="text-dark mb-1 font-weight-semibold">Barcode not used</p>
									<h3 class="my-2"><?php echo number_format($missing, 0);?></h3>
									</p>
								</div>
								<div class="col-auto align-self-center">
									<div class="report-main-icon bg-light-alt">
										<i class="fas fa-barcode"></i>
									</div>
								</div>
							</div>
						</div>
						<!--end card-body-->
					</div>
					<!--end card-->
				</div>
				<!--end col-->
			</div>
			<!--end row-->
			
		</footer>
		<!--end footer-->
	</div>
	<!-- end page content -->
</div>
<!-- end page-wrapper -->