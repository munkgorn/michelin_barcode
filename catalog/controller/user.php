<?php 
	class UserController extends Controller {
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
	    	$data['title'] = "user";
	    	$user = $this->model('user');
	    	$listUser = $user->listUser();
	    	$data['listUser'] = $listUser;
 	    	$this->view('user/list',$data);
	    }
	    public function add() {
	    	$data = array();
	    	$data['user']['id_user_group'] = '';
	    	$data['user']['username'] = '';
	    	$data['action'] = route('user/add');
	    	$user = $this->model('user');
	    	if(method_post()){
	    		$data_user = array(
	    			'username'	=> post('username'),
					'password'	=> post('password'),
					'id_user_group'	=> post('id_user_group')
	    		);
	    		$id_user = $user->add($data_user);
	    		$this->redirect('user');
	    	}else{
		    	$data['title'] = "user";
		    	$listUserGroup = $user->listUserGroup();
		    	$data['listUserGroup'] = $listUserGroup;
	 	    	$this->view('user/form',$data);
	 	    }
	    }
	    public function edit() {
	    	$data = array();
	    	$data['action'] = route('user/edit');
	    	$user = $this->model('user');
	    	if(method_post()){
	    		$data_user = array(
	    			'id_user' => post('id_user'),
	    			'username'	=> post('username'),
					'password'	=> post('password'),
					'id_user_group'	=> post('id_user_group')
	    		);
	    		$user->edit($data_user);
	    		$this->redirect('user/edit&id_user='.post('id_user').'&result=success');
	    	}else{
	    		$data['result'] = get('result');
	    		$id_user = get('id_user');
	    		$data['id_user'] = $id_user;
		    	$data['title'] = "user";
		    	$listUserGroup = $user->listUserGroup();
		    	$data_user = array(
		    		'id_user' => $id_user
		    	);
		    	$data['user'] = $user->getUser($data_user);
		    	$data['listUserGroup'] = $listUserGroup;
	 	    	$this->view('user/form',$data);
	 	    }
		}
		public function del() {
			$user = $this->model('user');
			$id = get('id');
			$user->del($id);
			$this->redirect('user');
		}
	    public function group() {
	    	$data = array();
	    	$data['title'] = "user";
	    	$user = $this->model('user');
	    	$listUserGroup = $user->listUserGroup();
	    	$data['listUserGroup'] = $listUserGroup;
 	    	$this->view('user/listGroup',$data);
	    }public function addGroup() {
	    	$data = array();
	    	$data['title'] = "user";
	    	// $user = $this->model('user');
	    	// $listUserGroup = $user->listUserGroup();
	    	// $data['listUserGroup'] = $listUserGroup;
 	    	$this->view('user/formGroup',$data);
	    }
	    public function editGroup() {
	    	$data = array();
	    	$data['title'] = "user";
	    	// $user = $this->model('user');
	    	// $listUserGroup = $user->listUserGroup();
	    	// $data['listUserGroup'] = $listUserGroup;
 	    	$this->view('user/formGroup',$data);
	    }
	    public function deleteGroup(){
	    	// $data = array();
	    	// $data['result'] = 'success';
	    	$result = 'success';
	    	$this->redirect('user/group&result='.$result);
		}
		
		public function resetPassword() {
			$user = $this->model('user');

	    	$data = array();
	    	$data['title'] = "Reset password";
			$data['action'] = route('user/resetPassword');

			$data['id_user'] = $this->getSession('id_user');

			if (method_post()) {
				$data_user = array(
					'id_user' => post('id_user')
				);
				$user_info = $user->getUser($data_user);
				if (post('password') != post('confirm-password')) {
					$this->setSession('error', 'Password is not match');
					$this->redirect('user/resetPassword&fail');
					exit();
				}
				
				if (isset($user_info['id_user']) && $user_info['id_user'] == $this->getSession('id_user')) {
					$user_info['password'] = trim(post('password'));
					$result = $user->edit($user_info);
					if ($result) {
						$this->setSession('success','Update new password successfull');
					} else {
						$this->setSession('error','Cannot reset password, please contact to admin');
					}
					$this->redirect('user/resetPassword');
					exit();
				} else {
					$this->setSession('error', 'Not found id user please logout and login');
					$this->redirect('user/resetPassword');
					exit();
				}
			}
			
			$data['success'] = $this->hasSession('success') ? $this->getSession('success') : ''; $this->rmSession('success');
			$data['error'] = $this->hasSession('error') ? $this->getSession('error') : ''; $this->rmSession('error');
			

 	    	$this->view('user/reset',$data);
		}
	}
?>