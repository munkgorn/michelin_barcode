<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Michelin Barcode</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
		<meta content="" name="description">
		<meta content="" name="author">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

        <link rel="shortcut icon" href="assets/images/favicon.ico">
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
		<link rel="stylesheet" href="assets/css/icons.min.css" />
		<link rel="stylesheet" href="assets/css/metisMenu.min.css" />
		<link rel="stylesheet" href="assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" />
		<link rel="stylesheet" href="assets/plugins/animate/animate.css" />
		<link rel="stylesheet" href="assets/plugins/select2/select2.min.css" />
		<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css" />
		<link rel="stylesheet" href="assets/css/app.min.css" />
		
		<link rel="stylesheet" href="assets/plugins/sweet-alert2/sweetalert2.min.css" />
		<link rel="stylesheet" href="assets/fontawesome-free-5.15.0-web/css/all.min.css" />
		<link rel="stylesheet" href="assets/fontawesome-free-5.15.0-web/css/fontawesome.min.css" />

		<link rel="stylesheet" href="assets/css/custom.css">


		<script type="text/javascript" src="assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="assets/js/metismenu.min.js"></script>
		<!-- <script type="text/javascript" src="assets/js/waves.js"></script> -->
		<script type="text/javascript" src="assets/js/feather.min.js"></script>
		<script type="text/javascript" src="assets/js/simplebar.min.js"></script>
		<script type="text/javascript" src="assets/js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="assets/js/moment.js"></script>
		<script type="text/javascript" src="assets/plugins/daterangepicker/daterangepicker.js"></script>
		<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
		<script type="text/javascript" src="assets/plugins/sweet-alert2/sweetalert2.min.js"></script>


	</head>
	<body class="dark-sidenav">
		<?php if (get('route') != 'home' && get('route') != 'loading' && get('route') != 'loading/someone' && get('route') != '') {?>
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
						<a href="<?php echo route('association'); ?>" class="<?php echo $_GET['route'] == 'barcode/association' ? 'active' : ''; ?>">
							<i data-feather="link-2" class="align-self-center menu-icon"></i>
							<span>Barcode Association<br>จับคู่บาร์โค้ด</span>
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
								<a href="<?php echo route('purchase'); ?>" class="<?php echo $_GET['route'] == 'purchase' ? 'active' : ''; ?>">
									<i class="ti-control-record"></i>New Barcode Ordering<br>สั่งซื้อบาร์โค้ดสำหรับออเดอร์ถัดไป
								</a>
							</li>
							<li>
								<a href="<?php echo route('group'); ?>" class="<?php echo $_GET['route'] == 'group' ? 'active' : ''; ?>">
									<i class="ti-control-record"></i>Barcode Reception<br>บันทึกบาร์โค้ดที่ได้รับจริง
								</a>
							</li>

							<!-- <li>
								<a href="<?php echo route('barcode'); ?>" class="<?php echo $_GET['route'] == 'barcode' ? 'active' : ''; ?>">
									<i class="ti-control-record"></i>Ordering History<br>ประวัติการสั่งซื้อบาร์โค้ด
								</a>
							</li> -->
							<li>
								<a href="<?php echo route('barcode'); ?>" class="<?php echo $_GET['route'] == 'barcode' ? 'active' : ''; ?>">
									<i class="ti-control-record"></i>Recode Consumed Barcode<br>บันทึกบาร์โค้ดที่นำมาใช้งานแล้ว
								</a>
							</li>
							<li>
								<a href="<?php echo route('report'); ?>" class="<?php echo $_GET['route'] == 'report' ? 'active' : ''; ?>">
								<i class="ti-control-record"></i> Report remaining stock barcode<br>รายงานสต๊อกบาร์โค้ดที่เหลืออยู่
								</a>
							</li>
						</ul>
					</li>
					<?php if (isset($_SESSION['id_user_group']) && in_array($_SESSION['id_user_group'], array(1, 2))): ?>
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
								<a href="<?php echo route('user'); ?>" class="<?php echo $_GET['route'] == 'user' ? 'active' : ''; ?>">
									<i class="ti-control-record"></i> Manage Users (Add/Remove)<br>สร้าง/ลบ ผู้ใช้งาน
								</a>
							</li>
							<li>
								<a href="<?php echo route('setting&tab=config_barcode'); ?>" class="<?php echo $_GET['route'] == 'setting' && $_GET['tab'] == 'config_barcode' ? 'active' : ''; ?>">
									<i class="ti-control-record"></i> List of barcodes that plant can use<br>กลุ่มบาร์โค้ดทั้งหมดที่โรงงานนำมาใช้ได้
								</a>
							</li>
							<li>
								<a href="<?php echo route('setting&tab=config_relationship'); ?>" class="<?php echo $_GET['route'] == 'setting' && $_GET['tab'] == 'config_relationship' ? 'active' : ''; ?>">
									<i class="ti-control-record"></i> List of special barcodes for association<br>บาร์โค้ดพิเศษสำหรับใช้ในการจับคู่
								</a>
							</li>
							<li>
								<a href="<?php echo route('setting&tab=config_default'); ?>" class="<?php echo $_GET['route'] == 'setting' && $_GET['tab'] == 'config_default' ? 'active' : ''; ?>">
									<i class="ti-control-record"></i> Nb of days that barcode cannot be repeated<br>จำนวนวันที่ไม่อนุญาตให้ใช้เลขบาร์โค้ด
								</a>
							</li>

						</ul>
					</li>
					<?php endif;?>
					<?php if (isset($_SESSION['id_user_group']) && in_array($_SESSION['id_user_group'], array(1,2))): ?>
					<li>
						<a href="<?php echo route('clear');?>">
							<i data-feather="x-octagon" class="align-self-center menu-icon"></i>
							<span>Clear Data</span>
						</a>
					</li>
					<?php endif; ?>
					<?php if (isset($_SESSION['id_user_group']) && in_array($_SESSION['id_user_group'], array(1))): ?>
					<li>
						<a href="<?php echo route('import'); ?>">
							<i data-feather="tool" class="align-self-center menu-icon"></i>
							<span>Import Mockup Data</span>
						</a>
					</li>
					<?php endif;?>
					<hr class="hr-dashed hr-menu">
					<li>
						<a href="<?php echo route('user/resetPassword'); ?>" class="<?php echo $_GET['route'] == 'user/resetPassword' ? 'active' : ''; ?>">
						<i data-feather="edit-3" class="align-self-center menu-icon"></i>
							Reset password
						</a>
					</li>
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
		<?php }?>

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
				window.location.href ="<?php echo route('home/logout'); ?>"
			} else if (result.dismiss == 'cancel') {
				console.log('cancel');
			}
			})
		});
	</script>