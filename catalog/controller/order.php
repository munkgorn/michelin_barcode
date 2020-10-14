<?php 
	class OrderController extends Controller {
	    public function index() {
	    	$data = array();
	    	$data['title'] = "Order";
	    	$style = array(
	    		'assets/home.css'
	    	);
	    	$data['style'] 	= $style;

 	    	$this->view('order/home',$data);
	    }
	}
?>