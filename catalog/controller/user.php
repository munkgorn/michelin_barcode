<?php 
	class UserController extends Controller {
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
	}
?>