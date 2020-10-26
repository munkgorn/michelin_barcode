<?php 
	require_once DOCUMENT_ROOT.'/system/lib/simplexlsx-master/src/SimpleXLSX.php';
	class BarcodeController extends Controller {
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
	    
			$data['title'] = "List Barcode";
			$style = array(
				'assets/home.css'
			);
			$data['style'] 	= $style;


			$barcode = $this->model('barcode');

			// default data
			$data['date'] = (get('date')?get('date'):'');
			$data_select = array(
				'date' => $data['date']
			);
			$data['getImportBarcode'] = array();
			$barcodes = $barcode->getBarcode($data_select);


			$data['getImportBarcode'] = $this->calcurateBarcode($data['date']);
			// echo '<pre>';
			// print_r($data['getImportBarcode']);
			// echo '</pre>';


			// $config = $this->model('config');
			// $default_number_maximum_alert = $config->getConfig('config_maximum_alert'); // ? ค่าที่ตั้งไว้ว่าเกินเท่าไหร่ให้ alert
			// return $this->calcurateDiffernce($list1, $list2, $default_number_maximum_alert);
			// foreach ($barcodes as $barcode) {

			// }

			$data['nums_row']	= $barcode->getNumsBarcode($data_select);


			$data['action'] = route('barcode/listGroup');
			$data['action_import'] = route('barcode/importUseBarcode');
			$data['export_excel'] = route('export/barcode&date='.$data['date']);
			$data['action_addmenual'] = route('barcode/addmenual&date='.$data['date']);
			$data['action_import_excel'] = route('barcode');
			
			$data['groups'] = $barcode->getGroupInBarcode($data['date']);

			// modal
			$data['textalert'] = $this->hasSession('textalert') ? $this->getSession('textalert') : false;
			$data['confirm_remove_barcode'] = route('barcode/confirm_remove'.(get('date')?'&date='.get('date'):''));
			
			$data['success'] = $this->hasSession('success') ? $this->getSession('success') : ''; $this->rmSession('success');
			$data['error'] = $this->hasSession('error') ? $this->getSession('error') : ''; $this->rmSession('error');

			$this->view('barcode/list',$data);
		}
		public function addmenual() {
			$group = $this->model('group');
			$barcode = $this->model('barcode');

			$prefix = post('barcode_prefix');
			$start = $prefix.sprintf('%05d', post('barcode_code_start'));
			$end = $prefix.sprintf('%05d', post('barcode_code_end'));
			if ($end>$start) {
				$range = array();
				for ($i=$start; $i<=$end; $i++) {
					$range[] = $i;	
					
				}
			} else {
				$this->setSession('error', 'End Barcode is heighter more than Start Barcode');
				$this->redirect('barcode&date='.get('date'));
			}

			$result = $barcode->updateBarcodeUse($range);
			if ($result) {
				$this->setSession('success', 'Success add range barcode is used <b>'.$start.' - '.$end.'</b>');
			} else {
				$this->setSession('error', 'Cannot add range barcode');
			}
			$this->redirect('barcode&date='.get('date'));
		}
		public function unconfirmImportBarcode() {
			$this->rmSession('barcodealert');
			$this->rmSession('textalert');
			$this->redirect('barcode');
		}
	    public function association() {
			$data = array();
			
			if (method_post()) {
				$updateWkMapping = array();
				$barcode = $this->model('barcode');
				$id_group = post("id_group");

				$check = post('checkbox');
				$checked = array();
				foreach ($check as $idg => $value) {
					$checked[] = $idg;
				}

				$freegroup = $this->jsonFreeGroup(false);
				$jsonfreegroup = json_decode($freegroup, true);
				$freegroup = json_decode($jsonfreegroup[0], true);
				$i=0;

				foreach ($id_group as $key => $value) {
					if (in_array($key, $checked)) {
						if (!empty($value)) {
							$insert = array(
								'date_wk' => post('date_wk'),
								'id_user' => $this->getSession('id_user'),
								'id_group' => $value,
								'id_product' => $key
							);
							$updateWkMapping[] = $barcode->updateWkMapping($insert)['result'];
						} else {
							$size = $this->model('size');
							$association = $this->model('association');
							$group = $this->model('group');
							$product = $size->getProduct($key);
							$date_lastweek = $association->getDateLastWeek();
	
							$last_week = ($date_lastweek!=false) ? $association->getGroupLastWeek($product['size_product_code'], $date_lastweek) : '';
							$remaining_qty = !empty($last_week) ? $association->getRemainingByGroup($last_week) : 0;
	
							$relation_group = $association->getRelationshipBySize($product['size_product_code']);
	
							$propose = '';
							$propose_remaining_qty = '';
							$message = '';
	
							if ($remaining_qty>=$product['sum_product']) {
								$propose = $last_week;
								$propose_remaining_qty = $remaining_qty;
								$message = 'Last Weeek' ;
							} else if (!empty($relation_group)) {
								$qty = $association->getRemainingByGroup($relation_group);
								if ($propose_remaining_qty>=$product['sum_product']) {
									$propose = $relation_group;
									$propose_remaining_qty = $qty;
									$message = 'Relationship';
								}
							} else {
								$propose = (int)$freegroup[$i];
								$i++;
							}
	
							// $id_group = $group->findIdGroup($propose);
	
							if (!empty($id_group)) {
								$insert = array(
									'date_wk' => post('date_wk'),
									'id_user' => $this->getSession('id_user'),
									'id_group' => $propose, // ! this group code 
									'id_product' => $product['id_product']
								);
								$updateWkMapping[] = $barcode->updateWkMapping($insert)['result'];
							}
							
							
						}
					}
				}
				$this->redirect('barcode/association&date_wk='.post('date_wk'));
			}
			
			$association = $this->model('association');
			$data['date_wk'] = get('date_wk');
			$data['listDateWK'] = $association->getDateWK();

			

			$data['list'] = array();
			$lists = $association->getProducts($data['date_wk']);
			$date_lastweek = $association->getDateLastWeek();
			$thisFirstWeek = ($date_lastweek==false) ? true : false;
			foreach ($lists as $key => $value) {
				$last_week = ($date_lastweek!=false) ? $association->getGroupLastWeek($value['size'], $date_lastweek) : '';
				$remaining_qty = !empty($last_week) ? $association->getRemainingByGroup($last_week) : 0;

				$relation_group = $association->getRelationshipBySize($value['size']);

				$propose = '';
				$propose_remaining_qty = '';
				$message = '';

				if ($remaining_qty>=$value['sum_prod']) {
					$propose = $last_week;
					$propose_remaining_qty = $remaining_qty;
					$message = 'Last Weeek' ;
				} else if (!empty($relation_group)) {
					$qty = $association->getRemainingByGroup($relation_group);
					if ($propose_remaining_qty>=$value['sum_prod']) {
						$propose = $relation_group;
						$propose_remaining_qty = $qty;
						$message = 'Relationship';
					}
				}
				

				$text = '';
				// if ($last_week!=$propose) {
					$text = $message;
				// }

				

				$data['list'][] = array(
					'id_product' => $value['id_product'],
					'size' => $value['size'],
					'sum_prod' => $value['sum_prod'],
					'last_wk0' => $last_week,
					'remaining_qty' => number_format((int)$remaining_qty,0),
					'propose' => $propose,
					'propose_remaining_qty' => number_format((int)$propose_remaining_qty,0),
					'message' => $text,
					'save' => $value['group_code']
				);
			}
			
			$data['export_excel'] = route('export/association&date='.$data['date_wk']);
			$data['action_import'] = route('barcode/importAssociation');
			$data['action_validate'] = route('barcode/association');

	    	$this->view('barcode/association',$data);
		}
		public function jsonFreeGroup($header=true) {
			$json = array();

			if (!file_exists(DOCUMENT_ROOT . 'uploads/freegroup.json')) {
				$this->generateJsonFreeGroup();
			}

			$file_handle = fopen(DOCUMENT_ROOT . 'uploads/freegroup.json', "r");
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
		public function importAssociation() {
			if(method_post()){
				$dir = 'uploads/association/';
				$path = DOCUMENT_ROOT . $dir;
				$path_csv = DOCUMENT_ROOT . $dir;

				$file = $_FILES['excel_input'];
				
				$fileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
				$newname = 'import_association_'.date('YmdHis');
				$file_csv = 'CSV_'.$newname.'.csv';
				$newname .= '.'.$fileType;
				$acceptFileType  = array('xlsx');
				// check folder upload
				if (!file_exists($path)) {
					$oldmask = umask(0);
					mkdir($path, 0777);
					umask($oldmask);
					// exit('not found folder '.$path);
				}
				// check file
				if ($file['error']==0 && in_array($fileType, $acceptFileType)) {
					if (upload($file, $path, $newname)) {
						$date = (post('date')?post('date'):date('Y-m-d'));
						$id_user = $this->getSession('id_user');
						$results = readExcel($dir.$newname); // read excel to csv
						$csv_file = $path_csv.$file_csv;
						$fp = fopen($csv_file, 'w');
						$barcode_use = array();
						foreach ($results as $key => $result) {
							
							$insert = array(
								$id_user,
								$result[0],
								$result[1],
								'0000-00-00 00:00:00',
								'0000-00-00 00:00:00',
								'0000-00-00 00:00:00'
							);
							fputcsv($fp, $insert,',',chr(0));
						}	
						fclose($fp);

						$barcode = $this->model('barcode');
						$last_date = $barcode->import_product($csv_file);
						$split = explode(' ',$last_date);
						// print_r($result_import_barcode_csv);
						$this->redirect('barcode/association&date_wk='.$split[0]);
					}
				}

				$this->generateJsonFreeGroup();
			}
			
				
	    		// $path = 'uploads/import_group_barcode_xlsx/';
	    		// $path_csv = 'uploads/convert_xlsx_barcode_csv/';
	    		// $file_name = date('YmdHis');
	    		// $name = $file_name.'_'.$file['name'];
	    		// $full_path = $path.$name;
	    		// $result_upload = upload($file,$path,$name);
	    		// $id_user = $this->getSession('id_user');
	    		// $date = (post('date')?post('date'):date('Y-m-d'));
	    		// if($result_upload){
		    	// 	// read xlsx
		    	// 	if ( $xlsx = SimpleXLSX::parse($full_path) ) {
				// 		$result_xlsx = $xlsx->rows();
				// 		$result = $result_xlsx;
				// 		// convert to csv file 
				// 		$csv_file = $path_csv.$file_name.'.csv';
				// 		$fp = fopen($csv_file, 'w');
				// 		// $i=0;
				// 		foreach ($result_xlsx as $fields) {
				// 			$temp_array = array();
				// 			$temp_array[0] = $id_user;   // id_user
				// 			$temp_array[1] = $fields[0]; // barcode_prefix
				// 			$temp_array[2] = $fields[1]; // barcode_code

				// 			// remove last value date of array
				// 			$data_now = date('Y-m-d H:i:s');
				// 			// add column barcode use
				// 			$temp_array[3] = 0; // barcode_status
				// 			// convert format date 
				// 			$date = date_f($date,'Y-m-d H:i:s');
				// 			$temp_array[4] = 0; // barcode_flag
				// 			// add date_added 
				// 			$temp_array[5] = $data_now; // date_added
				// 			// add date_modify
				// 			$temp_array[6] = $data_now; // date_modify
				// 			fputcsv($fp, $temp_array,',',chr(0));
				// 		}
				// 		fclose($fp);
				// 		// import CSV to database 

				// 		$barcode = $this->model('barcode');
				// 		$data_barcode = array(
				// 			'full_name' => $csv_file
				// 		);
				// 		$result_import_barcode_csv = $barcode->import_barcode($data_barcode);
				// 		// $result = array(
				// 		// 	'empty_date' 	=> $count_empty_date,
				// 		// 	'fail'			=> $count_empty_date,
				// 		// 	'success'		=> $count_success,
				// 		// 	'total'			=> $count_empty_date+$count_success
				// 		// );
				// 	} else {
				// 		$this->json(SimpleXLSX::parseError());
				// 	}
		    	// }
	    		// $this->json($data);
	    		// exit();
		}
	    public function add_row_barcode(){
	    	$data = array();
	    	if(method_get()){
	    		$data['date_wk'] = get('date_wk');
		    	$barcode = $this->model('barcode');
		    	$array_insert = array(
					'size_product_code' => get('add_size'),
					'sum_product' => get('add_sum_prod'),
					'date_wk'	=> get('date_wk')
				);
		    	$data['listDateWK'] = $barcode->addRowBarcode($array_insert);
	    	}
	    	$this->json($data);
	    }
	    public function export_excel_association(){
	    	$data['date_wk'] = (get('date_wk')?get('date_wk'):'');
	    	$barcode = $this->model('barcode');
	    	$data_select_date_wk = array(
	    		'date' => $data['date_wk']
	    	);
	    	$data['listPrefixBarcode'] = $barcode->listPrefixBarcode($data_select_date_wk);
	    	$temp = array();
	    	foreach($data['listPrefixBarcode'] as $val){
	    		$temp[] = array(
	    			$val['size_product_code'],
	    			$val['sum_product'],
	    			$val['group_code'],
	    			$val['remaining_qty'],
	    		);
	    	}

	    	$filename = "xls";
		    // $temp = $data['getBarcode'];
		    //header info for browser
		    header("Content-Type: application/xls");    
		    header("Content-Disposition: attachment; filename=$filename.xls");  
		    header("Pragma: no-cache"); 
		    header("Expires: 0");
		    /*******Start of Formatting for Excel*******/   
		    //define separator (defines columns in excel & tabs in word)
		    $sep = "\t"; //tabbed character
		    //start of printing column names as names of MySQL fields
		    // for ($i = 0; $i < mysql_num_fields($result); $i++) {
		    // echo mysql_field_name($result,$i) . "\t";
		    // }
		    print("\n");    
		    //end of printing column names  
		    //start while loop to get data
		    foreach( $temp as $row ){
		        $schema_insert = "";
		        for($j=0; $j<count($row);$j++){
		            if(!isset($row[$j])){
		                $schema_insert .= "NULL".$sep;
		            }
		            else if($row[$j] != ""){
		                $schema_insert .= "$row[$j]".$sep;
		            }
		            else{
		                $schema_insert .= "".$sep;
		            }
		        }
		        $schema_insert = str_replace($sep."$", "", $schema_insert);
		        $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
		        $schema_insert .= "\t";
		        print(trim($schema_insert));
		        print "\n";
		    }
	    }
	    public function export_excel(){
	    	$data['date'] = (get('date')?get('date'):'');
	    	$data_select = array(
	    		'date' => $data['date']
	    	);
	    	$barcode = $this->model('barcode');
	    	$data['getBarcode'] = $barcode->getExcelBarcode($data_select);

	    	$filename = "xls";
		    $temp = $data['getBarcode'];
		    //header info for browser
		    header("Content-Type: application/xls");    
		    header("Content-Disposition: attachment; filename=$filename.xls");  
		    header("Pragma: no-cache"); 
		    header("Expires: 0");
		    $sep = "\t";
		    print("\n");
		    foreach( $temp as $row ){
		        $schema_insert = "";
		        for($j=0; $j<count($row);$j++){
		            if(!isset($row[$j])){
		                $schema_insert .= "NULL".$sep;
		            }
		            else if($row[$j] != ""){
		                $schema_insert .= "$row[$j]".$sep;
		            }
		            else{
		                $schema_insert .= "".$sep;
		            }
		        }
		        $schema_insert = str_replace($sep."$", "", $schema_insert);
		        $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
		        $schema_insert .= "\t";
		        print(trim($schema_insert));
		        print "\n";
		    }
	    }
	    public function updateWkMapping(){
			$data = array();
			$data['date_wk'] = get('date_wk');
			$data['group'] = get('group');
			$data['size'] = get('size');

			$data['id_user'] = $this->getSession('id_user');
			$barcode = $this->model('barcode');
			$data_selct = array(
				'group' => $data['group'],
				'date_wk' => $data['date_wk'],
				'size' => $data['size'],
				'id_user' => $data['id_user']
			);
			$updateWkMapping = $barcode->updateWkMapping($data_selct);
			$this->json($data);
		}
	    public function deleteGroup(){
	    	$data = array();
	    	$barcode = $this->model('barcode');
	    	if(method_post()){
		    	$select = array(
		    		'id_group' => post('id_group')
		    	);
		    	$data['list_group'] = $barcode->deleteGroup($select);
		    }
	    	$this->json($data);
	    }
	   
	    public function export_excel_group(){
	    	$data['date'] = (get('date')?get('date'):'');
	    	$data_select = array(
	    		'date' => $data['date']
	    	);
	    	$barcode = $this->model('barcode');
	    	$data['getBarcode'] = $barcode->getExcelBarcode($data_select);

	    	$filename = "xls";
		    $temp = $data['getBarcode'];
		    //header info for browser
		    header("Content-Type: application/xls");    
		    header("Content-Disposition: attachment; filename=$filename.xls");  
		    header("Pragma: no-cache"); 
		    header("Expires: 0");
		    /*******Start of Formatting for Excel*******/   
		    //define separator (defines columns in excel & tabs in word)
		    $sep = "\t"; //tabbed character
		    //start of printing column names as names of MySQL fields
		    // for ($i = 0; $i < mysql_num_fields($result); $i++) {
		    // echo mysql_field_name($result,$i) . "\t";
		    // }
		    print("\n");    
		    //end of printing column names  
		    //start while loop to get data
		    foreach( $temp as $row ){
		        $schema_insert = "";
		        for($j=0; $j<count($row);$j++){
		            if(!isset($row[$j])){
		                $schema_insert .= "NULL".$sep;
		            }
		            else if($row[$j] != ""){
		                $schema_insert .= "$row[$j]".$sep;
		            }
		            else{
		                $schema_insert .= "".$sep;
		            }
		        }
		        $schema_insert = str_replace($sep."$", "", $schema_insert);
		        $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
		        $schema_insert .= "\t";
		        print(trim($schema_insert));
		        print "\n";
		    }
	    }
	    public function export_excel_range_barcode(){
	    	$filename = "xls";
		    // $temp = $data['getBarcode'];

	    	$barcode = $this->model('barcode');
	    	// $path = 'catalog/view/theme/pdf/PPDOrder';

	    	$data['start_group'] = get('start_group');
	    	$data['end_group'] = get('end_group');

	    	$data_select = array(
	    		'start_group'	=> $data['start_group'],
				'end_group'		=> $data['end_group']
	    	);

	    	$resultMapping = $barcode->getMapping($data_select);
	    	$html_data = array();
	    	$html_data[] = array(
	    		'No.',
	    		'Start',
				'End',
				'Count.',
	    	);
	    	foreach($resultMapping as $val){
	    		$html_data[] = array(
	    			$val['group_code'],
	    			sprintf('%06d', $val['start']),
	    			sprintf('%06d', $val['end']),
	    			$val['remaining_qty']
	    		);
	    	}

		    //header info for browser
		    header("Content-Type: application/xls");    
		    header("Content-Disposition: attachment; filename=$filename.xls");  
		    header("Pragma: no-cache"); 
		    header("Expires: 0");
		    /*******Start of Formatting for Excel*******/   
		    //define separator (defines columns in excel & tabs in word)
		    $sep = "\t"; //tabbed character
		    //start of printing column names as names of MySQL fields
		    // for ($i = 0; $i < mysql_num_fields($result); $i++) {
		    // echo mysql_field_name($result,$i) . "\t";
		    // }
		    print("\n");    
		    //end of printing column names  
		    //start while loop to get data
		    foreach( $html_data as $row ){
		        $schema_insert = "";
		        for($j=0; $j<count($row);$j++){
		            if(!isset($row[$j])){
		                $schema_insert .= "NULL".$sep;
		            }
		            else if($row[$j] != ""){
		                $schema_insert .= "$row[$j]".$sep;
		            }
		            else{
		                $schema_insert .= "".$sep;
		            }
		        }
		        $schema_insert = str_replace($sep."$", "", $schema_insert);
		        $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
		        $schema_insert .= "\t";
		        print(trim($schema_insert));
		        print "\n";
		    }
		}
		public function importUseBarcode() {
			if(method_post()){
				$dir = 'uploads/import_cutbarcode/';
				$path = DOCUMENT_ROOT . $dir;
				$path_csv = DOCUMENT_ROOT . $dir;

				$file = $_FILES['import_file'];
				
				$fileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
				$newname = 'import_barcode_'.date('YmdHis');
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
						$barcode_use = array();
						$date = (post('date')?post('date'):date('Y-m-d'));
						$id_user = $this->getSession('id_user');
						$results = readExcel($dir.$newname, 0); // read excel to csv
						$csv_file = $path_csv.$file_csv;
						$fp = fopen($csv_file, 'w');
						
						foreach ($results as $key => $result) {
							if ($key!=0) { $barcode_use[] = $result[0]; }
							$insert = array(
								$id_user,
								$result[0],
								$date.' 00:00:00',
								'0000-00-00 00:00:00',
								'0000-00-00 00:00:00'
							);
							fputcsv($fp, $insert,',',chr(0));
						}	
						fclose($fp);
						$barcode = $this->model('barcode');
						$result_import_barcode_csv = $barcode->import_range_barcode($path_csv.$file_csv, $date);

						$result_updatebarcode = $barcode->updateBarcodeUse($barcode_use); // ? update ใน barcode ว่า ใช้เลขไหนไปบ้าง
						$barcode_alert = $this->calcurateBarcode(); // ? ต้องเช็คเลขที่ไม่ถูกใช้งาน ให้ Flag ทิ้ง

						// echo '<pre>';
						// print_r($barcode_alert);
						// echo '</pre>';
						// exit();
						$textalert = array();
						foreach ($barcode_alert as $alert) {
							$textalert[] = $alert['name'];
						}
						$textalert = implode(',<br>' ,$textalert);
						if (!empty($textalert)) {
							$this->setSession('textalert', $textalert);
							$this->setSession('barcodealert', $barcode_alert);
						}
						$this->redirect('barcode');
						// $last_date = $barcode->import_product($csv_file);
						// $split = explode(' ',$last_date);
						// print_r($result_import_barcode_csv);
						// $this->redirect('barcode/association&date_wk='.$split[0]);
					}
				}

				$this->generateJsonFreeGroup();
	    	}
		}
	    public function listGroup() {
			$data = array();
			
		    	$data['title'] = "List Barcode";
		    	$style = array(
		    		'assets/home.css'
		    	);
		    	$data['style'] 	= $style;
		    	$data['date'] = (get('date')?get('date'):'');
		    	$data_select = array(
		    		'date' => $data['date']
		    	);
				$data['action'] = route('barcode/listGroup'.(get('date')?'&date='.get('date'):''));
				$data['action_import'] = route('barcode/listGroup'.(get('date')?'&date='.get('date'):''));
				$data['action_import_excel'] = '';
		    	$barcode = $this->model('barcode');
				$data['list_group'] = $barcode->getListGroup($data_select);

				$data['textalert'] = $this->hasSession('textalert') ? $this->getSession('textalert') : false;
				$data['confirm_remove_barcode'] = route('barcode/confirm_remove'.(get('date')?'&date='.get('date'):''));

				

		    	// $data['action_import_excel'] = route('listGroup');
	 	    	$this->view('barcode/listGroup',$data);
	 	    
		}
		public function confirm_remove() {
			if ($this->hasSession('textalert')) {
				/*
				Array
				(
					[0] => Array
						(
							[name] => 10100036 - 10100044
							[barcodes] => Array
								(
									[0] => 10100004
									[1] => 10100005
									[2] => 10100006
									[3] => 10100007
									[4] => 10100008
									[5] => 10100036
									[6] => 10100037
									[7] => 10100038
									[8] => 10100039
									[9] => 10100040
									[10] => 10100041
									[11] => 10100042
									[12] => 10100043
									[13] => 10100044
								)

							[count] => 9
						)

				)*/
				$alert = $this->getSession('barcodealert'); // array('name'=>'100-105, 110,112', 'barcodes' => array(100,101,102,103,105,110,111,112), 'count'=>8);
				$barcode_flagnotuse = array();
				foreach ($alert as $value) {
					foreach ($value['barcodes'] as $barcode) {
						$barcode_flagnotuse[] = $barcode;
					}
				}
				//$barcode_notuse = explode(',', $this->getSession('textalert')); // ? in session is 100-105, 110-112
				$barcode = $this->model('barcode');
				$barcode->updateFlagBarcode($barcode_flagnotuse);

				$this->rmSession('barcodealert');
				$this->rmSession('textalert');

				$this->setSession('success', 'success remove some barcode is not use');
				
				
				$this->redirect('barcode'.(get('date')?'&date='.get('date'):''));
			}
		}
	    public function PPDOrder(){
	    	$barcode = $this->model('barcode');
	    	$path = 'catalog/view/theme/pdf/PPDOrder';

	    	$data['start_group'] = get('start_group');
	    	$data['end_group'] = get('end_group');

	    	$data_select = array(
	    		'start_group'	=> $data['start_group'],
				'end_group'		=> $data['end_group']
	    	);

	    	$resultMapping = $barcode->getMapping($data_select);
	    	$html_data = array();
	    	$html_data[] = array(
	    		'No.',
	    		'Start',
				'End',
				'Count.',
	    	);
	    	foreach($resultMapping as $val){
	    		$html_data[] = array(
	    			$val['group_code'],
	    			sprintf('%06d', $val['start']),
	    			sprintf('%06d', $val['end']),
	    			$val['remaining_qty']
	    		);
	    	}
	    	$html_data[] = array(
	    		'No.',
	    		'Start',
				'End',
				'Count.',
	    	);
	    	$style = "width: 180px;";
	    	$temp_array_data = data_to_row_html($html_data,$style);
	    	$replace = array(
				'{$row_data}' => $temp_array_data
			);
	    	$html = $this->getHtmlFilePDF($path,$replace);

	    	$path_dir = DOCUMENT_ROOT.'uploads/PPDOrder/';
	    	if (!file_exists($path_dir)) {
			    mkdir($path_dir, 0777, true);
			}
			$file_name = 'PPDOrder_'.date('Y_m_d_His');
			$path = DOCUMENT_ROOT.'uploads/PPDOrder/'.$file_name.'.pdf';
	    	$data_pdf = array(
	    		'file_name' => $file_name,
	    		'path' 		=> $path
	    	);
	    	$result_pdf = $this->downloadPdf($html,$data_pdf);
	    	header("Content-type:application/pdf");
			header("Content-Disposition:attachment;filename=".$file_name);
			readfile($path);
		}
		
		public function changeStatus() {
			$id = get('id');
			$status_id = get('status');
			$barcode = $this->model('barcode');
			$barcode->updateBarcodeStatus($id, $status_id);
			$this->redirect('barcode/listgroup'.(get('date')?'&date='.get('date'):''));
		}

		public function calcurateBarcode($date_wk='') {
			$input=array();
			if (!empty($date_wk)) { $input['date_wk'] = $date_wk; }
			$barcode = $this->model('barcode');

			$list1 = array();
			$list2 = array();
			
			$input = array(
				'date_wk' => $date_wk,
				'barcode_use' => 1
			);
			$listbarcode = $barcode->getListBarcode($input); // ? ที่จองในระบบทั้งหมด
			foreach ($listbarcode as $key => $value) {
				$list1[] = (int)$value['barcode_code'];
			}

			$input = array(
				'date_wk' => $date_wk,
				'barcode_use' => 1,
				'barcode_status' => '0'
			);
			$listbarcode = $barcode->getListBarcode($input); // ? ที่ใช้ไปแล้ว
			foreach ($listbarcode as $key => $value) {
				$list2[] = (int)$value['barcode_code'];
			}

			// echo '<pre>';
			// print_r($list1);
			// print_r($list2);
			// echo '</pre>';

			// ? get default alert
			$config = $this->model('config');
			$default_number_maximum_alert = $config->getConfig('config_maximum_alert'); // ? ค่าที่ตั้งไว้ว่าเกินเท่าไหร่ให้ alert
			return $this->calcurateDiffernce($list1, $list2, $default_number_maximum_alert);
		}

		public function calcurateDiffernce($list1, $list2, $default_number_maximum_alert) {
			sort($list1);
			sort($list2);
			$arr_diff = array_diff($list1, $list2); // ? ได้อาเรย์ ส่วนต่างที่ไม่เหมือนกัน
			$list_notfound = array_values($arr_diff); // ? reset key array

			$count = 0;
			$first = '';
			$end = '';
			$group = '';
			$save = array();
			$diff = array();
			foreach ($list_notfound as $key => $value) {
				if (isset($list_notfound[$key+1]) && $list_notfound[$key+1] == $value+1) { // ? ในกรณีที่ คียอันถัดไป เท่า ค่า+1 แสดงว่า ส่วนต่างที่ไม่มีนี้กำลังเรียง
					if (empty($first)) {
						$save = array();
						$first = sprintf('%08d',$value);
						$group = substr(sprintf('%08d', $value), 0 , 3);
					}
					$count++; // ? เริ่มนับจำนวนส่วนต่าง
				} else {
					if (empty($end)) {
						$end = sprintf('%08d',$value);
						$save[] = sprintf('%08d',$value);
					}
					$diff[] = array(
						'group' => $group,
						'name' => "$first - $end",
						'barcodes' => $save, 
						'count' => $count + 1 //  ? จำนวนระยะห่างที่หายไป +1 นับตัวแรกด้วย
					);
					$first = '';
					$end = '';
					$count = 0;
					$group = '';
				}
				if ($count>0) {
					$save[] = $value;
				}
			}

			$text = array();
			foreach ($diff as $key => $value) {
				if ($value['count'] >= $default_number_maximum_alert) {
					$text[] = $value;
				}
			}

			return $text;
		}
		public function test() {

			$array1 = array(10100000, 10100001, 10100002, 10100003, 10100004, 10100005, 10100006, 10100007, 10100008, 10100009, 10100010, 10100011, 10100012, 10100013, 10100014, 10100015, 10100016, 10100017, 10100018, 10100019, 10100020, 10100021, 10100022, 10100023, 10100024, 10100025, 10100026, 10100027, 10100028, 10100029, 10100030, 10100031, 10100032, 10100033, 10100034, 10100035, 10100036, 10100037, 10100038, 10100039, 10100040, 10100041, 10100042, 10100043, 10100044, 10100045, 10100046, 10100047, 10100048, 10100049, 10100050, 10100051, 10100052, 10100053, 10100054, 10100055, 10100056, 10100057, 10100058, 10100059, 10100060, 10100061, 10100062, 10100063, 10100064, 10100065, 10100066, 10100067, 10100068, 10100069, 10100070, 10100071, 10100072, 10100073, 10100074, 10100075, 10100076, 10100077, 10100078, 10100079, 10100080, 10100081, 10100082, 10100083, 10100084, 10100085, 10100086, 10100087, 10100088, 10100089, 10100090, 10100091, 10100092, 10100093, 10100094, 10100095, 10100096, 10100097, 10100098, 10100099);
			$array2 = array(10100000, 10100009, 10100010, 10100011, 10100012, 10100013, 10100014, 10100015, 10100016, 10100017, 10100018, 10100019, 10100020, 10100021, 10100022, 10100023, 10100024, 10100025, 10100026, 10100027, 10100028, 10100029, 10100030, 10100031, 10100032, 10100033, 10100034, 10100035, 10100036, 10100037, 10100038, 10100039, 10100040, 10100041, 10100042, 10100043, 10100044, 10100045, 10100046, 10100047, 10100048, 10100049, 10100050, 10100051, 10100052, 10100053, 10100054, 10100055, 10100056, 10100057, 10100058, 10100059, 10100060, 10100061, 10100062, 10100063, 10100064, 10100065, 10100066, 10100067, 10100068, 10100069, 10100070, 10100071, 10100072, 10100073, 10100074, 10100075, 10100076, 10100077, 10100078, 10100079, 10100080, 10100081, 10100082, 10100083, 10100084, 10100085, 10100086, 10100087, 10100088, 10100089, 10100090, 10100091, 10100092, 10100093, 10100094, 10100095, 10100096, 10100097, 10100098, 10100099);

			$array_diff = array_diff($array1, $array2);
			$array_notfound = array_values($array_diff); // reset key

			echo '<pre>';
			print_r($array_notfound);
			echo '</pre>';
			
			$count = 0;
			$first = '';
			$end = '';
			foreach ($array_notfound as $key => $value) {
				if (isset($array_notfound[$key+1]) && $array_notfound[$key+1] == $value+1) {
					if (empty($first)) {
						$first = $value;
					}
					$count++;
				} else {
					if (empty($end)) {
						$end = $value;
					}
					$diff[] = array(
						'name' => "$first - $end",
						'count' => $count + 1 // +1 นับตัวแรกด้วย
					);
					$first = '';
					$end = '';
					$count = 0;
				}
			}
			echo '<pre>';
			print_r($diff);
			echo '</pre>';
		}
	}
?>