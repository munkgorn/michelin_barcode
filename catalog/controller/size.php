<?php 
	require_once DOCUMENT_ROOT.'/system/lib/simplexlsx-master/src/SimpleXLSX.php';
	class SizeController extends Controller {
	    public function index() {
	    	$data = array();
	    	$result = array();
	    	$id_user = $this->getSession('id_user');
	    	$count_empty_date = 0;
	    	$count_success = 0;
	    	if(!empty($id_user)){
	    		if(isset($_FILES['excel_input'])){
			    	$file = $_FILES['excel_input'];
			    	$path = 'uploads/import_xlsx_size/';
			    	$path_csv = 'uploads/convert_xlsx_size_csv/';
			    	$name = time().'_'.rand().'_'.pure_text($file['name']);
			    	$full_path = $path.$name;
			    	$result_upload = upload($file,$path,$name);
			    	$file_csv = time();
			    	if($result_upload){
			    		// read xlsx
			    		if ( $xlsx = SimpleXLSX::parse($full_path) ) {
							$result_xlsx = $xlsx->rows();
							$result = $result_xlsx;
							// convert to csv file
							$csv_file = $path_csv.$file_csv.'.csv';
							$fp = fopen($csv_file, 'w');
							// $i=0;
							foreach ($result_xlsx as $fields) {
								$temp_array = array();
								// $temp_array = $fields;
								// $this->json($temp_array);exit();
								// add id_user
								$temp_array[0] = $id_user;
								$temp_array[1] = $fields[0];
								$temp_array[2] = $fields[1];
								// array_push($temp_array, $id_user);
								// array_push($temp_array, $fields[0]);
								// array_push($temp_array, $fields[1]);
								// get value array ( date Wk)
								$date = $fields[2];
								// remove last value date of array
								// array_pop($fields);
								if(!empty($date)){
									$data_now = date('Y-m-d H:i:s');
									// add column barcode use
									$temp_array[3] = 0;
									// array_push($temp_array, 0);
									// convert format date 
									$date = date_f($date,'Y-m-d H:i:s');
									// array_push($temp_array, $date);
									$temp_array[4] = $date;
									// add date_added 
									$temp_array[5] = $data_now;
									// array_push($temp_array, $data_now);
									// add date_modify
									$temp_array[6] = $data_now;
									// array_push($temp_array, $data_now);
									// $this->json($temp_array);exit();
									fputcsv($fp, $temp_array,',',chr(0));
									$count_success++;
								}else{
									$count_empty_date++;
								}
							}
							fclose($fp);
							// import CSV to database 
							$size = $this->model('size');
							$data_size = array(
								'file' => $csv_file,
								'id_user' => $id_user
							);
							$result_import_size_csv = $size->import($data_size);
							$result = array(
								'empty_date' 	=> $count_empty_date,
								'fail'			=> $count_empty_date,
								'success'		=> $count_success,
								'total'			=> $count_empty_date+$count_success
							);
						} else {
							$this->json(SimpleXLSX::parseError());
						}
			    	}
			    }else{
			    	$result = $_FILES;
			    }
	    	}
	    	
	    	$this->json($result);
	    }
	}
?>