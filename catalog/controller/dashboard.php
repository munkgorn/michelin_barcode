<?php 
	class DashboardController extends Controller {
		public function __construct() {
			if ($this->hasSession('id_user')==false) {
				$this->rmSession('id_user');
				$this->rmSession('username');
				$this->setSession('error', 'Please Login');
				$this->redirect('home');
			} 
		}
	    public function index() {
	    	$data = array();
	    	// echo getSession('id_user').'<';exit();
	    	// $user = $this->call('User')->login();
	    	// $data['user'] = $user;
	    	$data = array();
	    	$data['title'] = "Dashboard";
	    	$style = array(
	    		// 'assets/home.css'
	    	);
			$data['style'] 	= $style;
			
			$dashboard = $this->model('dashboard');
			$data['group'] = $dashboard->countGroup();
			$data['barcode'] = $dashboard->countBarcode();
			$data['waiting'] = $dashboard->countBarcodeWaiting();
			$data['missing'] = $dashboard->countBarcodeMissing();

 	    	$this->view('dashboard',$data); 
	    }
	}
?>