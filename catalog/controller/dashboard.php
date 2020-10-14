<?php 
	class DashboardController extends Controller {
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

 	    	$this->view('dashboard',$data); 
	    }
	}
?>