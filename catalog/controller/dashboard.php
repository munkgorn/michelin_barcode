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
			// $data['group'] = $dashboard->countGroup();
			// $data['barcode'] = $dashboard->countBarcode();
			// $data['waiting'] = $dashboard->countBarcodeWaiting();
			// $data['missing'] = $dashboard->countBarcodeMissing();

			$data['group'] = 0;
			$data['barcode'] = 0;
			$data['waiting'] = 0;
			$data['missing'] = 0;

			$data['success'] = $this->hasSession('success') ? $this->getSession('success') : ''; $this->rmSession('success');
			$data['error'] = $this->hasSession('error') ? $this->getSession('error') : ''; $this->rmSession('error');

 	    	$this->view('dashboard',$data); 
	    }
	}
?>