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
				$this->setSession('success', 'Purchase order successful');
	    		$this->redirect('purchase&start_group='.$data['start_group'].'&end_group='.$data['end_group'].'&validated=true');
			}

			$data['validated'] = isset($_GET['validated']) ? true : false;
			
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
			$data['result_group'] = array();
			$data['result_group'] = $config->getBarcodes();
			$data['end_group'] = isset($_GET['end_group']) ? get('end_group') : end($data['result_group'])['group'];
	    	$data['action'] = route('purchase');
			$data['action_import_excel'] = route('listGroup');
			$data['export_excel'] = route('export/pattern&start_group='.$data['start_group'].'&end_group='.$data['end_group']);
			$data['action_ajax'] = route('purchase/ajax&start_group='.$data['start_group'].'&end_group='.$data['end_group']);
			$data['date'] = (get('date')?get('date'):'');
			
			$purchase = $this->model('purchase');
			$group = $this->model('group');
			// Get List
			$filter = array(
				'start_group' => get('start_group'),
				'end_group' => get('end_group')
			);
			$mapping = $purchase->getPurchases($filter);
			$data['getMapping'] = array();
			if ($mapping!=false) {
				foreach ($mapping as $key => $value) {
					$barcode_use = $group->getGroupStatus($value['group_code']);
					$value['status'] = $barcode_use==="1" ? '' : ($barcode_use==="0" ? '<span class="text-danger">Waiting</span>' : '');
					$value['status_id'] = $barcode_use;
					$data['getMapping'][] = $value;
				}
			}

			$data['success'] = $this->hasSession('success') ? $this->getSession('success') : ''; $this->rmSession('success');
			$data['error'] = $this->hasSession('error') ? $this->getSession('error') : ''; $this->rmSession('error');
		

 	    	$this->view('purchase/list',$data);
		}
		public function ajax() {
			$post = post();
			
			$update = array(
				'change_qty' => $post['change_qty'],
				'change_end' => $post['change_end']
			);
			$purchase = $this->model('purchase');
			
			$result = $purchase->updatePurchase((int)$post['group_code'], $update);
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
		
		public function checkBarcodeUsed() {
			$data = array();

			$barcode = $this->model('barcode');
			$data = $barcode->checkBarcode(post('barcode'));
			$data = json_encode($data);

			$this->json($data);	
		}


		public function ajaxDefaultDate() {
			$data = array();
			$data = $this->jsonDefaultYear();
			$this->json($data);
		}
		public function jsonDefaultYear($header=true) {
			$json = array();
			if (!file_exists(DOCUMENT_ROOT . 'uploads/default_year.json')) {
				$this->generateJsonDefaultYear();
			}
			$file_handle = fopen(DOCUMENT_ROOT . 'uploads/default_year.json', "r");
			while(!feof($file_handle)){
				$line_of_text = fgets($file_handle);
				$json[] = $line_of_text;
			}
			fclose($file_handle);
			if ($header) {
				$this->json($json);
			} else {
				return json_encode($json);
			}
		}
		public function generateJsonDefaultYear() {
			$data = array();

			$purchase = $this->model('purchase');
			$config = $this->model('config');
			$dayofyear = $config->getConfig('config_date_year');
			$beforeusesize = $config->getConfig('config_date_size');

			$year = $purchase->getStartEndDateOfYearAgo(date('Y-m-d', strtotime('-'.$dayofyear.'day')), date('Y-m-d', strtotime('-'.$beforeusesize.'day')));
			$data['start'] = $year['date_start'];
			$data['end'] = $year['date_end'];

			// $data['start'] = $purchase->getStartDateOfYearAgo($dayofyear, $beforeusesize);
			// $data['end'] = $purchase->getEndDateOfYearAgo($dayofyear, $beforeusesize);

			$fp = fopen(DOCUMENT_ROOT . 'uploads/default_year.json', 'w');
			fwrite($fp, json_encode($data));
			fclose($fp);
			return $data;
		}


		public function ajaxGroupDefault() {
			$data = array();
			$data = $this->jsonGroupDefaultBarcode();
			$this->json($data);
		}
		public function jsonGroupDefaultBarcode($header=true) {
			$json = array();
			if (!file_exists(DOCUMENT_ROOT . 'uploads/default_purchase.json')) {
				$this->generateJsonDefaultBarcode();
			}
			$file_handle = fopen(DOCUMENT_ROOT . 'uploads/default_purchase.json', "r");
			while(!feof($file_handle)){
				$line_of_text = fgets($file_handle);
				$json[] = $line_of_text;
			}
			fclose($file_handle);
			if ($header) {
				$this->json($json);
			} else {
				return json_encode($json);
			}
		}
		public function generateJsonDefaultBarcode() {
			$data = array();
			$purchase = $this->model('purchase');
			$config = $this->model('config');

			$config = $this->model('config');
			$dayofyear = $config->getConfig('config_date_year');
			$beforeusesize = $config->getConfig('config_date_size');
			$results = $purchase->getBarcodeStartEndOfGroup(date('Y-m-d', strtotime('-'.$dayofyear.'day')), date('Y-m-d', strtotime('-'.$beforeusesize.'day')));
			foreach ($results as $value) {
				$data[$value['group']] = array(
					'start' => (int)$value['barcode_start']>0 ? sprintf('%08d', $value['barcode_start']) : '',
					'end' => (int)$value['barcode_end']>0 ? sprintf('%08d', $value['barcode_end']) : '',
				);
			}

			// $groups = $config->getBarcodes();
			// foreach ($groups as $value) {
			// 	$data[$value['group']] = array(
			// 		'start' => '',
			// 		'end' => '',
			// 	);
			// 	$year = $purchase->getBarcodeStartEndOfGroup($value['group'], date('Y-m-d', strtotime('-'.$dayofyear.'day')), date('Y-m-d', strtotime('-'.$beforeusesize.'day')));
			// 	$s = $year['barcode_start'];
			// 	$e = $year['barcode_end'];
			// 	// $s = $purchase->getStartBarcodeOfYearAgo($value['group']);
			// 	// $e = $purchase->getEndBarcodeOfYearAgo($value['group']);
			// 	$data[$value['group']]['start'] = !empty($s) ? sprintf('%08d', $s) : ''; 
			// 	$data[$value['group']]['end'] = !empty($e) ? sprintf('%08d', $e) : '';
			// }
			$fp = fopen(DOCUMENT_ROOT . 'uploads/default_purchase.json', 'w');
			fwrite($fp, json_encode($data));
			fclose($fp);
			return $data;
		}

	}
?>