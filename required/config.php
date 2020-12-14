<?php
ini_set("memory_limit", "-1");
set_time_limit(0);

  	define('DEBUG_MODE',true);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


	// ini_set('display_errors', 0);
	// ini_set('display_startup_errors', 0);
	// error_reporting(0);
	

	$base = str_replace('required', '', __DIR__);
  	define('MURL','http://localhost/michelin_barcode/');
	// define('MURL','https://www.fsoftpro.com/dohung/');
	define('SOURCE_UPDATE', 'https://www.fsoftpro.com/production/michelin_barcode/update_source/Archive.zip');
	define('DOCUMENT_ROOT',$_SERVER['DOCUMENT_ROOT'].'/michelin_barcode/');
	define('ROW_IN_DOC','10');
	define('BYTE_PER_KB','1000');
	 
	define('app_id','166994808024757');
	define('app_secret','b0bf73fa492cfd8b4d0125eeda9d5e51');
	define('default_graph_version','v2.10');

	define('GOOGLE_CLIENT_ID', '310104410325-k5ufrsold5trpadn00c424vidtqc2lpt.apps.googleusercontent.com');
	define('GOOGLE_CLIENT_SECRET', 'k-mfqWUZaQoL5r-rpu9NM1fP');
	define('GOOGLE_REDIRECT_URL', MURL.'index.php?route=user/gmailCallback');

	define('DEFAULT_PAGE','home');
	define('WEB_NAME','');
	define('IMAGE',MURL.'uploads/');
	define('IMAGE_PHOTO',MURL.'uploads/photo/'); 
	define('NO_PHOTO',MURL.'uploads/no_photo.jpg');
	define('DB','mysqli');
	define('KEY', 'appcom@fsp88');
	
	// Config DB localhost
	define('PREFIX', 'mb_master_');
	define('DB_HOST','localhost');
	// define('DB_USER','fsoftpro_barcode');
	// define('DB_PASS','wSNZztqzI');
	define('DB_USER','root');
	define('DB_PASS','');
	define('DB_DB','fsoftpro_barcode');
	define('DATE_FORMAT','Y-m-d');
	// Production
	// define('PREFIX', 'dh_');
	// define('DB_HOST','localhost');
	// define('DB_USER','fsoftpro_dhpro');
	// define('DB_PASS','29bGG94RSg');
	// define('DB_DB','fsoftpro_dhpro');

	// System config 
	define('DEFAULT_LANGUAGE','1');
	define('DEFAULT_LIMIT_PAGE','10');

	// email ssl
	define('email_username','support@fsoftpro.com');
	define('email_password','fiverama2');
	define('email_host','smtp.gmail.com');
	define('email_port','465');
	define('email_send','support@fsoftpro.com');
	define('email_stmpsecure','ssl');

	// email tls
	// define('email_username','');
	// define('email_password','');
	// define('email_host','');
	// define('email_port','25');
	// define('email_send','');
	// define('email_stmpsecure','TLS');

	// use PHPMailer\PHPMailer\PHPMailer;
	// use PHPMailer\PHPMailer\Exception;

	// require DOCUMENT_ROOT.'system/lib/PHPMailer-master-7/src/Exception.php';
	// require DOCUMENT_ROOT.'system/lib/PHPMailer-master-7/src/PHPMailer.php';
	// require DOCUMENT_ROOT.'system/lib/PHPMailer-master-7/src/SMTP.php';
	// global	$mail ;
	// $mail = new PHPMailer(true); //New instance, with exceptions enabled



	// ? CONFIG DEFAULT PATTERN DATE IMPORT
	define('CONFIG_DEFAULT_DATE_1', 'dd-mm-yy');

	
?>