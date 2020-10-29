<?php 
	class ImportController extends Controller {
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
        public function menual() {
            $import = $this->model('import');
            $date = '2019-05-23 00:00:00';
            for ($i=3360000; $i<=3399999; $i++) {
                $import->insertBarcode($i, $date);
            }
        }
	    public function index() {
            $data = array();

            $import = $this->model('import');

            $data['get_table'] = get('table');

            if (method_post()) {
                $dir = 'uploads/import/';
				$path = DOCUMENT_ROOT . $dir;
				$path_csv = DOCUMENT_ROOT . $dir;

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
                        $group = $this->model('group');
                        
                        $results = readExcel($dir.$newname, 0, 1);
                        $results2 = readExcel($dir.$newname, 0, 1);


                        $dir = 'uploads/import_cutbarcode/';
                        $path = DOCUMENT_ROOT . $dir;
                        $path_csv = DOCUMENT_ROOT . $dir;
                        $newname = 'import_defaultgroup_'.date('YmdHis');
                        $file_csv = 'CSV_'.$newname.'.csv';
                        $csv_file = $path_csv.$file_csv;
                        $col = array();
                        $row=0;
                        $fp = fopen($csv_file, 'w');
                        foreach ($results as $value) {
                            if ($row>0) {
                                $config_info = $config->getBarcodeByPrefix($value[0]);
                                $default_start = 0;
                                $default_end = 0;
                                $default_range = 0;
                                if ($config_info!=false) {
                                    $default_start = !empty($config_info['default_start']) ? $config_info['default_start'] : '';
                                    $default_end = !empty($config_info['default_end']) ? $config_info['default_end'] : '';
                                    $default_range = !empty($config_info['default_range']) ? $config_info['default_range'] : '';
                                }
                                
                                // $sql = "INSERT INTO mb_master_group SET ";
                                $insert = array(
                                    $this->getSession('id_user'),
                                    $value[0],
                                    $value[1],
                                    $default_start,
                                    $default_end,
                                    $default_range,
                                    $value[2].' 00:00:00',
                                    $value[2].' 00:00:00',
                                    $value[3],
                                );
                                fputcsv($fp, $insert,',',chr(0));
                            }
                            $row++;
                        }
                        fclose($fp);
                        $import->loadCSVGroup($csv_file);


                        $col = array();
                        $row=0;
                        $dir = 'uploads/import_cutbarcode/';
                        $path = DOCUMENT_ROOT . $dir;
                        $path_csv = DOCUMENT_ROOT . $dir;
                        $newname = 'import_defaultbarcode_'.date('YmdHis');
                        $file_csv = 'CSV_'.$newname.'.csv';
                        $csv_file = $path_csv.$file_csv;
                        $fp = fopen($csv_file, 'w');
                        foreach ($results2 as $value) {
                            if ($row>0) {
                                $id_group = $group->findIdGroup($value[0]);

                                for ($i=(int)$value[1]; $i<=$value[2]; $i++) {
                                    $insert = array(
                                        $this->getSession('id_user'),
                                        $id_group,
                                        $value[0],
                                        $i,
                                        $value[5],
                                        $value[4].' 00:00:00',
                                        $value[4].' 00:00:00',
                                    );
                                    fputcsv($fp, $insert,',',chr(0));
                                }
                            }
                            $row++;
                        }
                        fclose($fp);
                        $import->loadCSVBarcode($csv_file);

                        // print_r(post('column'));
                        // $post_column = post('column');

                        // $sqlCol = array();
                        // $sql = "";

                        // $row = 0;
                        // foreach ($results as $key => $value) {
                        //     if ($row>0) {
                        //         $sql = "INSERT INTO ".get('table')." SET ";
                        //         foreach ($value as $ke => $val) {
                        //             if (isset($post_column[$ke])) { 
                        //                 $sqlCol[$post_column[$ke]] = $val;
                        //             }
                        //         }

                        //         $temp = array();
                        //         foreach ($sqlCol as $col => $val) {
                        //             if (!empty($col)) {
                        //                 // if ($col=='date_added') {
                        //                 //     $tempdate = explode('/', $val);
                        //                 //     $val = $tempdate[2].'-'.sprintf('%02d', $tempdate[1]).'-'.sprintf('%02d', $tempdate[0]).' 00:00:00';
                        //                 // }
                        //                 $temp[] = "`$col` = '$val'";
                        //             }
                        //         }

                        //         $sql .= implode(', ', $temp);
                        //         $sql .= ";";

                        //         $import->querySql($sql);
                        //     }
                        //     $row++;
                        // }


                        

                        // print_r($sqlCol);
                        // echo $sql;

                        
                        // echo '<pre>';
                        // print_r($results);  
                        // echo '</pre>';
                        exit();

						// $csv_file = $path_csv.$file_csv;
						// $fp = fopen($csv_file, 'w');
						// foreach ($results as $key => $result) {
						// 	$rowcsv = array(
						// 		$result[0],
						// 		$result[1],
						// 		'0000-00-00 00:00:00',
						// 		'0000-00-00 00:00:00'
						// 	);
						// 	fputcsv($fp, $rowcsv,',',chr(0));
						// }	

						// fclose($fp);
					}
					
                }
            }
            
            $table = $import->getTables();
            $data['table'] = array();
            foreach ($table as $value) {
                $data['table'][] = $value['TABLE_NAME'];
            }

            $data['column'] = array();
            if (isset($_GET['table'])) {
                $column = $import->getColumns(get('table'));
                foreach ($column as $value) {
                    $data['column'][] = $value['COLUMN_NAME'];
                }
                
            }

 	    	$this->view('import/index',$data);
        }
    }