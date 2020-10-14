<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- <title><?php echo $title;?></title> -->
  <link href="assets/boostrap_jquery/css/bootstrap.css" rel="stylesheet" >
  <link href="assets/css/sidebar.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <link href="assets/fontawesome/css/fontawesome.css" rel="stylesheet">
  <?php if(isset($style)){ 
      foreach ($style as $key => $value) { ?>
    <link rel="stylesheet" href="<?php echo $value;?>">
  <?php } } ?>
  <script src="assets/boostrap_jquery/js/jquery.js"></script>
  <script src="assets/boostrap_jquery/js/popper.js"></script>
  <script src="assets/boostrap_jquery/js/bootstrap.js"></script>
  <script src="assets/js/main.js"></script>

  <!-- Custom styles for this template -->
  <script src="assets/fontawesome/js/all.js"></script>
  <?php 
  if(isset($script)){
  foreach ($script as $key => $value) { ?>
    <script src="<?php echo $value;?>"></script>
  <?php } } ?>
</head>
<body class="<?php echo (isset($class_body)?$class_body:'animsition site-navbar-small dashboard mm-wrapper site-menubar-fold site-navbar dashboard'); ?>">
<!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]--> 
  <div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="menu-left" id="sidebar-wrapper">
      <div class="sidebar-heading text-theme text-center"><img src="../assets/image/logo.png" alt="" width="100"> <br> RFQ Store</div>
      <div class="list-group menu-list list-group-flush">
        <a href="<?php echo route('home'); ?>" class="list-group-item list-group-item-action"> Home</a>
        <a href="<?php echo route('create/indexModel'); ?>" id="model" class="list-group-item list-group-item-action"> Create Model</a>
        <a href="<?php echo route('create/indexController'); ?>" id="controller" class="list-group-item list-group-item-action"> Create Controller</a>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <nav class="navbar navbar-expand-lg navbar-light">
        <a id="menu-toggle" href="#" style="color: #999;"><i class="fas fa-bars fa-2x"></i></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            <li class="nav-item active">
              <a class="nav-link" href="#">Home</a>
            </li>
          </ul>
        </div>
      </nav>