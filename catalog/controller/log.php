<?php 
	class LogController extends Controller {
	    public function index() {
	    	$data = array();
	    	$data['title'] = "Log";
	    	$style = array(
	    		// 'assets/home.css'
	    	);
	    	$data['style'] 	= $style;

 	    	$this->view('log/home',$data);
	    }
	}
?>