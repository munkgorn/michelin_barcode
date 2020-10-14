<?php 
	class HomeController extends Controller {
	    public function index() {
	    	$data = array();
	    	$data['action'] = route('home/login');
	    	$data['title'] = "Home";
	    	$style = array(
	    		'assets/home.css'
	    	);
	    	$data['style'] 	= $style;
 	    	$this->view('home',$data); 
	    }
	    public function login(){
	    	$data = array();
	    	$user = $this->model('user');
	    	$username = post('username');
	    	$password = post('password');
	    	$data_select = array(
	    		'username' => $username,
	    		'password' => $password
	    	);
	    	$result_login = $user->login($data_select);
	    	if($result_login){
	    		$this->setSession('id_user',$result_login['id_user']);
	    		$this->setSession('username',$result_login['username']);
	    		$this->redirect('dashboard');
	    	}else{
	    		$this->redirect('home&result=fail'); 
	    	}
		}
		
		public function logout() {
			$this->rmSession('id_user');
			$this->rmSession('username');
			$this->redirect('home/login');
		}
	}
?>