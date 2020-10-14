<?php
	class customerController extends Controller{
		public function home(){
			$customer = $this->model('customer');
			$data['list_customer'] = $customer->listCustomer();
			$this->view('customer/home',$data);
		}
		public function edit(){
			$this->view('customer/edit');
		}
	}  
?>