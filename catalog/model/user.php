<?php 
	class UserModel extends db {
		public function getUser($data=array()){
			$result = array();
			$id_user = (int)$data['id_user'];
			$sql = "SELECT * FROM ".PREFIX."user WHERE id_user = '".$id_user."'";
			$result_user = $this->query($sql);
			if($result_user->num_rows){
				$result = $result_user->row;
			}
			// var_dump($result);exit();
			return $result;
		}
		public function login($data=array()){
			$result = array();
			$username 	= $this->escape(pure_text($data['username']));
			$password 	= $this->escape($data['password']);
			
			$sql = "SELECT * FROM ".PREFIX."user WHERE `username` = '".$username."' AND `password` = '".md5($password)."' limit 0,1";
			
			$result_user = $this->query($sql); 
			if($result_user->num_rows > 0){
				$result = array(
					'username' 	=> $result_user->row['username'],
					'id_user' 	=> $result_user->row['id_user']
				);
			}
			return $result;
		}
		public function listUser($data=array()){
			$result = array();
			$sql = "SELECT * FROM ".PREFIX."user LEFT JOIN ".PREFIX."user_group ON ".PREFIX."user.id_user_group = ".PREFIX."user_group.id_user_group";
			$result_user = $this->query($sql); 
			if($result_user->num_rows > 0){
				$result = $result_user->rows;
			}
			return $result;
		}
		public function listUserGroup($data=array()){
			$result = array();
			$sql = "SELECT * FROM ".PREFIX."user_group";
			$result_user = $this->query($sql); 
			if($result_user->num_rows > 0){
				$result = $result_user->rows;
			}
			return $result;
		}
		public function add($data=array()){
			$password = $data['password'];
			$id_user = $data['id_user'];
			$data_user = array(
    			'username'		=> $data['username'],
				'id_user_group'	=> $data['id_user_group']
    		);
    		if(!empty($password)){
				$data_user['password'] = md5($password);
			}
    		$id_user = $this->insert('user',$data_user);
    		return $id_user;
		}
		public function edit($data=array()){
			$password = $data['password'];
			$id_user = $data['id_user'];
			$data_user = array(
    			'username'		=> $data['username'],
				'id_user_group'	=> $data['id_user_group']
    		);
    		if(!empty($password)){
				$data_user['password'] = md5($password);
			}
    		$this->update('user',$data_user,"id_user = '".$id_user."'");
		}
		public function register($data=array()){
			// $result = array();
			// $email 			= !empty($data['user_email']) ? $this->escape($data['user_email']) : '' ;
			// $user_password 	= !empty($data['user_password']) ? $this->escape($data['user_password']) : '' ;
			// $user_name 		= !empty($data['user_name']) ? $this->escape($data['user_name']) : '' ;
			// $user_lastname 	= !empty($data['user_lastname']) ? $this->escape($data['user_lastname']) : '' ;
			// $id_user_fb 	= !empty($data['id_user_fb']) ? $this->escape($data['id_user_fb']) : '' ;
			// $user_phone		= !empty($data['user_phone']) ? $this->escape($data['user_phone']) : '' ;

			// if(!empty($id_user_fb)){
			// 	$sql_check_dupplicate_if_fb = "SELECT * FROM com_user WHERE id_user_fb = '".$id_user_fb."'";
			// 	$query_check = $this->query($sql_check_dupplicate_if_fb);
			// 	if($query_check->num_rows > 0){
			// 		$result['status'] 	= 'success';
			// 		$result['desc'] 	= 'ไม่สมัครใหม่ เนื่องจากมี email ในระบบแล้ว';
			// 		return $result;
			// 	}
			// }
			// if(!empty($email)){
			// 	$sql_check_dupplicate_email = "SELECT * FROM com_user WHERE user_email = '".$email."'";
			// 	$query_check = $this->query($sql_check_dupplicate_email);
			// 	if($query_check->num_rows == 0){
			// 		$data_insert_user = array(
			// 			'user_email' 		=> $email,
			// 			'user_password' 	=> md5($user_password),
			// 			'user_name'			=> $user_name,
			// 			'user_lastname'		=> $user_lastname,
			// 			'user_key'			=> rand(10000,99999),
			// 			'user_date_create'	=> date('Y-m-d H:i:s'),
			// 			'id_user_fb'		=>	$id_user_fb,
			// 			'user_phone'		=> 	$user_phone
			// 		);
			// 		$id_user = $this->insert('user',$data_insert_user);
			// 		$result['status'] 	= 'success';
			// 		$result['desc'] 	= '';
			// 		return $result;
			// 	}else{
			// 		$result['status'] 	= 'fail';
			// 		$result['desc']		= 'Email นี้มีอยู่ในระบบแล้ว';
			// 		return $result;
			// 	}
			// }else{
			// 	$result['status'] 	= 'fail';
			// 	$result['desc']		= 'Email เป็นค่าว่าง';
			// 	return $result;
			// }
		}
		public function findEamil($email) {
			$this->where('user_email', $email);
			$result = $this->get('user');
			return $result->num_rows;
		}
	}
?>