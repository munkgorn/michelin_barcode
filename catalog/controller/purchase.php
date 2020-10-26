<?php 
	class PurchaseController extends Controller {
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
			$barcode = $this->model('barcode');
			$config = $this->model('config');
	    	$data['start_group'] = get('start_group');
			$data['end_group'] = get('end_group');
			
	    	if(method_post()){
	    		$id_user = $this->getSession('id_user');
	    		$qty = post('qty');
	    		$data_post = array(
	    			'qty' => $qty,
	    			'id_user' => $id_user,
	    		);
	    		$barcode->updateGroupCreateBarcode($data_post);
	    		$data['start_group'] 	= post('start_group');
	    		$data['end_group'] 		= post('end_group');
	    		// $this->redirect('purchase&start_group='.$data['start_group'].'&end_group='.$data['end_group'].'&result=success');
			}
			
	    	$data['result'] = get('result');
	    	$data['title'] = "List Purchase";
	    	$style = array(
	    		'assets/home.css'
	    	);
	    	$data['style'] 	= $style;
	    	$data['date_wk'] = date('Y-m-d');
	    	$data_select_date_wk = array(
	    		'date' => $data['date_wk']
	    	);
	    	// $data['listPrefixBarcode'] = $barcode->listPrefixBarcode($data_select_date_wk);
			$data['result_group'] = array();
			$data['result_group'] = $config->getBarcodes();

			if (count($data['result_group'])==0) {
				$this->setSession('error', 'Cannot go page "new barcode ordering", please import association and validated.');
				$this->redirect('association');
			}

			$data['end_group'] = end($data['result_group'])['group'];
	    	$data['action'] = route('purchase');
			$data['action_import_excel'] = route('listGroup');
			$data['export_excel'] = route('export/pattern&start_group='.$data['start_group'].'&end_group='.$data['end_group']);
			$data['action_ajax'] = route('purchase/ajax&start_group='.$data['start_group'].'&end_group='.$data['end_group']);
			$data['date'] = (get('date')?get('date'):'');
			
			$purchase = $this->model('purchase');
			$group = $this->model('group');

			if (empty($data['start_group']) || empty($data['end_group'])) {
				// $this->setSession('error', 'Not found');
				// $this->redirect('association');
			}

			// Get List
			$filter = array(
				'start_group' => $data['start_group'],
				'end_group' => $data['end_group']
			);
			$mapping = $purchase->getPurchases($filter);
			$data['getMapping'] = array();
			if ($mapping!=false) {
				foreach ($mapping as $key => $value) {
					$value['barcode_start_year'] = $purchase->getStartBarcodeOfYearAgo($value['group_code']);
					$value['barcode_end_year'] = $purchase->getEndBarcodeOfYearAgo($value['group_code']);
					$barcode_use = $group->getGroupStatus($value['group_code']);
					$value['status'] = $barcode_use==="1" ? '<span class="text-primary">Recived</span>' : ($barcode_use==="0" ? '<span class="text-danger">Waiting</span>' : '');
					$value['status_id'] = $barcode_use;
	
					$data['getMapping'][] = $value;
				}
			}
			
			// 3 year ago
			$data['date_first_3_year'] = date('Y-m-d', strtotime($purchase->getStartDateOfYearAgo()));
			$data['date_lasted_order'] = date('Y-m-d', strtotime($purchase->getEndDateOfYearAgo()));

 	    	$this->view('purchase/list',$data);
		}
		public function ajax() {
			$post = post();
			
			$update = array(
				'change_qty' => $post['change_qty'],
				'change_end' => $post['change_end']
			);
			$purchase = $this->model('purchase');
			
			$result = $purchase->updatePurchase($post['group_code'], $update);
			if ($result) {
				echo 'success';
			} else {
				echo 'fail';
			}
		}
	    public function updateDefaultGroup(){
			$data = array();
			$barcode = $this->model('barcode');
			$value = get('value');
			$id_group = get('id_group');
			$type = get('type');
			$data_select = array(
				'value' => $value,
				'id_group' => $id_group,
				'type' => $type
			);
			$result = $barcode->updateDefaultGroup($data_select);
			$this->json($result);
		}
	    public function add() {
	    	$data = array();
	    	$this->view('purchase/form',$data);
	    }
	    public function edit() {
	    	$data = array();
	    	$this->view('purchase/form',$data);
	    }
	    public function delete() {
	    	$data = array();
	    	$this->view('purchase/form',$data);
	    }

	}
?>