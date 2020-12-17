<?php 
	class SettingController extends Controller {
		public function __construct() {
			if ($this->hasSession('id_user')==false) {
				$this->rmSession('id_user');
				$this->rmSession('username');
				$this->setSession('error', 'Please Login');
				$this->redirect('home');
			} 

			if ($this->hasSession('id_user_group')) {
				if (!in_array($this->getSession('id_user_group'), array(1,2))) {
					$this->setSession('error', 'Permission fail');
					$this->redirect('dashboard');
				}
			}
		}
	    public function index() {
	    	$data = array();
	    	$data['title'] = "Setting";
	    	$style = array(
	    		'assets/home.css'
	    	);
	    	$data['style'] 	= $style;

	    	$data['date_wk'] = get('date_wk');
	    	$barcode = $this->model('barcode');
	    	$data['listDateWK'] = $barcode->listDateWK();
	    	$data_select_date_wk = array(
	    		'date' => ''
	    	);
			// $data['listPrefixBarcode'] = $barcode->listPrefixBarcode($data_select_date_wk);

			$data['tab'] = isset($_GET['tab']) ? get('tab') : 'config_default';

			$data['action_default'] = route('setting/configdefault');
			$data['action_relationship'] = route('setting/relationship');
			$data['action_importrelationship'] = route('setting/importrelationship');
			$data['action_barcode'] = route('setting/barcode');
			$data['action_status'] = route('setting/status');
			$data['action_addstatus'] = route('setting/addStatus');
			
			$config = $this->model('config');
			// tab default
			$data['config_date_size'] = $config->getConfig('config_date_size')!==false ? $config->getConfig('config_date_size') : 0;
			$data['config_date_year'] = $config->getConfig('config_date_year')!==false ? $config->getConfig('config_date_year') : 0;
			$data['config_maximum_alert'] = $config->getConfig('config_maximum_alert')!==false ? $config->getConfig('config_maximum_alert') : 0;
			$data['config_lastweek'] = $config->getCOnfig('config_lastweek')!==false ? $config->getConfig('config_lastweek') : 0;

			// tab relationship
			$data['relationships'] = $config->getRelationship();
			$data['lastupdate_relationship'] = $config->getLastupdateRelationship();
			
			// tab barcode
			$data['barcodes'] = $config->getBarcodes();
			$data['lastupdate_barcode'] = $config->getLastupdateBarcode();

			// tab status
			$data['status'] = $config->getStatus();

			
			$data['success'] = $this->hasSession('success') ? $this->getSession('success') : ''; $this->rmSession('success');
			$data['error'] = $this->hasSession('error') ? $this->getSession('error') : ''; $this->rmSession('error');

 	    	$this->view('setting/home',$data);
		}

		// submit
		public function relationship() {
		}
		public function importrelationship() {
			if (method_post()) {
				$dir = 'uploads/config/';
				$path = DOCUMENT_ROOT . $dir;
				$path_csv = DOCUMENT_ROOT . $dir;

				if (!file_exists($path)) {
					$oldmask = umask(0);
					mkdir($path, 0777);
					umask($oldmask);
				}

				$file = $_FILES['import_file'];
				
				$fileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
				$newname = 'import_relationship_'.date('YmdHis');
				$file_csv = 'CSV_'.$newname.'.csv';
				$newname .= '.'.$fileType;
				$acceptFileType  = array('xlsx');

				// check folder upload
				if (!file_exists($path)) {
					$oldmask = umask(0);
					mkdir($path, 0777);
					umask($oldmask);
				}

				// check file
				if ($file['error']==0 && in_array($fileType, $acceptFileType)) {
					if (upload($file, $path, $newname)) {
						// Read file to insert database
						$config = $this->model('config');
						$results = readExcel($dir.$newname, 0);

						$csv_file = $path_csv.$file_csv;
						$fp = fopen($csv_file, 'w');
						foreach ($results as $key => $result) {
							$rowcsv = array(
								$result[0],
								$result[1],
								$result[2],
								'0000-00-00 00:00:00',
								'0000-00-00 00:00:00'
							);
							fputcsv($fp, $rowcsv,',',chr(0));
						}	

						fclose($fp);
						$config->importRelationship($csv_file);
						// $this->generateJsonFreeGroup();

						$this->setSession('success', 'Import file config relationship successful.');
					} else {
						$this->setSession('error', 'Fail improt file config relationship, something has wrong');
					}
					
				}
			} else {
				$this->setSession('error', 'Not found submit');
			}
			$this->redirect('setting&tab=config_relationship');
		}
		public function addStatus() {
			if (method_post()) {
				$config = $this->model('config');
				if ( $config->addStatus(post()) ) {
					$this->setSession('success','Add Status success');
				} else {
					$this->setSession('error', 'Add Status fail something has wrong');
				}
			} else {
				$this->setSession('error', 'Not found submit');
			}
			$this->redirect('setting&tab=config_status');
		}
		public function delStatus() {
			$config = $this->model('config');
			$id = get('id');
			if ($config->delStatus($id)) {
				$this->setSession('success', 'Remove Status success');
			} else {
				 $this->setSession('error', 'Remove status fail something has wrong');
			}
			$this->redirect('setting&tab=config_status');
		}
		public function configdefault() {
			$result = array();
			if (method_post()) {
				$config = $this->model('config');
				foreach (post() as $key => $value) {
					$result[] = $config->setConfig($key, $value);
				}
			}
			if (in_array(false, $result)) {
				$this->setSession('error', 'fail save config');
			} else {
				$this->setSession('success', 'Success save config');
			}
			$this->redirect('setting');
		}
		public function barcode() {
			if (method_post()) {
				$dir = 'uploads/config/';
				$path = DOCUMENT_ROOT . $dir;
				$path_csv = DOCUMENT_ROOT . $dir;

				$file = $_FILES['import_file'];
				
				$fileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
				$newname = 'import_configbarcode';
				$file_csv = 'csv_'.$newname.'.csv';
				$newname .= '.'.$fileType;
				$acceptFileType  = array('xlsx');

				// check folder upload
				if (!file_exists($path)) {
					$oldmask = umask(0);
					mkdir($path, 0777);
					umask($oldmask);
				}

				// check file
				if ($file['error']==0 && in_array($fileType, $acceptFileType)) {
					if (upload($file, $path, $newname)) {
						// Read file to insert database
						$config_barcode = $this->model('config');
						$results = readExcel($dir.$newname, 0);

						$csv_file = $path_csv.$file_csv;
						$fp = fopen($csv_file, 'w');
						foreach ($results as $key => $result) {
							$rowcsv = array(
								sprintf('%03d',$result[0]),
								sprintf('%08d',$result[1]),
								sprintf('%08d',$result[2]),
								$result[3],
								$result[3],
								(int)$result[1],
							);
							fputcsv($fp, $rowcsv);
						}	
						fclose($fp);
						$config_barcode->importBarcode($path_csv.$file_csv);

						// $this->generateJsonFreeGroup();

						$this->setSession('success', 'Import file config barcode successful.');
					} else {
						$this->setSession('error', 'Fail improt file config barcode, something has wrong');
					}
					
				}
			} else {
				$this->setSession('error', 'Not found submit');
			}
			$this->redirect('setting&tab=config_barcode');
		}





		public function generateJsonFreeGroup() {
			$association = $this->model('association');
			$lists = $association->getFreeGroup();
			$json = array();
			foreach ($lists as $value) {
				$json[] = $value['group'];
			}
			$fp = fopen(DOCUMENT_ROOT . 'uploads/freegroup.json', 'w');
			fwrite($fp, json_encode($json));
			fclose($fp);
			return $json;
		}


		
	}

?>