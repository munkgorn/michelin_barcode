<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Michelin Barcode</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
		<meta content="" name="description">
		<meta content="" name="author">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<!-- App favicon -->
		<link rel="shortcut icon" href="assets/images/favicon.ico">
		<!-- jvectormap -->
		<link href="assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">
		<!-- App css -->
		<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="assets/css/jquery-ui.min.css" rel="stylesheet">
		<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
		<link href="assets/css/metisMenu.min.css" rel="stylesheet" type="text/css">
		<link href="assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css">
		<link href="assets/plugins/select2/select2.min.css" rel="stylesheet" type="text/css">
		<link href="assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
		<link href="assets/plugins/animate/animate.css" rel="stylesheet" type="text/css">
		<link href="assets/css/app.min.css" rel="stylesheet" type="text/css">

		<link rel="stylesheet" href="assets/fontawesome-free-5.15.0-web/css/all.min.css" />
		<link rel="stylesheet" href="assets/fontawesome-free-5.15.0-web/css/fontawesome.min.css" />
		
		<link rel="stylesheet" href="assets/css/custom.css">

		<script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/bootstrap.bundle.min.js"></script>
		<script src="assets/js/metismenu.min.js"></script>
		<!-- <script src="assets/js/waves.js"></script> -->
		<script src="assets/js/feather.min.js"></script>
		<script src="assets/js/simplebar.min.js"></script>
		<script src="assets/js/jquery-ui.min.js"></script>
		<script src="assets/js/moment.js"></script>
		<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
		<script src="assets/plugins/select2/select2.min.js"></script>
		<script src="assets/plugins/sweet-alert2/sweetalert2.min.js"></script>

		<!-- <script src="assets/pages/jquery.forms-advanced.js"></script> -->
		
	</head>
	<body class="dark-sidenav">
		<?php if(get('route')!='home' AND get('route')!=''){ ?>
		<!-- Left Sidenav -->
		<div class="left-sidenav">
			<!-- LOGO -->
			<div class="brand">
				<a href="#" class="logo">
					<span class="text-info">
						Michelin
					</span>
				</a>
			</div>
			<!--end logo-->
			<div class="menu-content h-100" data-simplebar>
				<ul class="metismenu left-sidenav-menu">
					<li class="menu-label mt-0">Main</li>
					<li>
						<a href="<?php echo route('dashboard'); ?>">
							<i data-feather="home" class="align-self-center menu-icon"></i>
							<span>Dashboard</span>
						</a>
					</li>
					<li>
						<a href="<?php echo route('barcode/association'); ?>" class="<?php echo $_GET['route']=='barcode/association'?'active':'';?>">
							<i data-feather="link-2" class="align-self-center menu-icon"></i>
							<span>Barcode Association</span>
						</a>
					</li>
					<li id="barcode">
						<a href="javascript: void(0);">
							<i data-feather="grid" class="align-self-center menu-icon">
							</i>
							<span>Barcode Management</span>
							<span class="menu-arrow">
								<i class="mdi mdi-chevron-right"></i>
							</span>
						</a>
						<ul class="nav-second-level" aria-expanded="false">
							<li>
								<a href="<?php echo route('purchase'); ?>" class="<?php echo $_GET['route']=='purchase'?'active':'';?>">
									<i class="ti-control-record"></i>New Barcode Ordering
								</a>
							</li>
							<li>
								<a href="<?php echo route('group'); ?>" class="<?php echo $_GET['route']=='group'?'active':'';?>">
									<i class="ti-control-record"></i>Barcode Reception
								</a>
							</li>
							<li>
								<a href="<?php echo route('barcode'); ?>" class="<?php echo $_GET['route']=='barcode'?'active':'';?>">
									<i class="ti-control-record"></i>Recode Consumed Barcode
								</a>
							</li>
						</ul>
					</li>
					<!-- <li>
						<a href="javascript: void(0);">
							<i data-feather="grid" class="align-self-center menu-icon">
							</i>
							<span>Product</span>
							<span class="menu-arrow">
								<i class="mdi mdi-chevron-right"></i>
							</span>
						</a>
						<ul class="nav-second-level" aria-expanded="false">
							<li>
								<a href="#">
									<i class="ti-control-record"></i>Import product 
								</a>
							</li>
						</ul>
					</li> -->
					
					<li id="config">
						<a href="javascript: void(0);">
							<i data-feather="settings" class="align-self-center menu-icon">
							</i>
							<span>Configuration</span>
							<span class="menu-arrow">
								<i class="mdi mdi-chevron-right"></i>
							</span>
						</a>
						<ul class="nav-second-level" aria-expanded="false">
							<li>
								<a href="<?php echo route('user'); ?>" class="<?php echo $_GET['route']=='user'?'active':'';?>">
									<i class="ti-control-record"></i> Manage Users (Add/Remove) 
								</a>
							</li>
							<li>
								<a href="<?php echo route('setting&tab=config_barcode');?>" class="<?php echo $_GET['route']=='setting'&&$_GET['tab']=='config_barcode'?'active':'';?>">
									<i class="ti-control-record"></i> List of barcodes that plant can use
								</a>
							</li>
							<li>
								<a href="<?php echo route('setting&tab=config_relationship');?>" class="<?php echo $_GET['route']=='setting'&&$_GET['tab']=='config_relationship'?'active':'';?>">
									<i class="ti-control-record"></i> List of special barcodes for association
								</a>
							</li>
							<li>
								<a href="<?php echo route('setting&tab=config_default');?>" class="<?php echo $_GET['route']=='setting'&&$_GET['tab']=='config_default'?'active':'';?>">
									<i class="ti-control-record"></i> Nb of days that barcode cannot be repeated
								</a>
							</li>
							
						</ul>
					</li>
					<hr class="hr-dashed hr-menu">
					<li>
						<a type="button" id="sa-logout" href="#">
							<i data-feather="power" class="align-self-center menu-icon"></i>
							<span>Logout</span>
						</a>
					</li>
					
				</ul>
			</div>
		</div>
		<!-- end left-sidenav-->
	<?php } ?>

	<script>
		$("#sa-logout").click(function(){
			Swal.fire({
			title: 'Do you want to logout?',
			showDenyButton: true,
			showCancelButton: true,
			confirmButtonText: `Logout`,
			denyButtonText: `No`,
			}).then((result) => {
			if (result.value) {
				window.location.href ="<?php echo route('home/logout');?>"
			} else if (result.dismiss == 'cancel') {
				console.log('cancel');
			}
			})
		});
	</script>