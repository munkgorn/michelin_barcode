<?php 
class AssociationController extends Controller {

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
        $association = $this->model('association');
	
        $data['date_wk'] = get('date_wk');
        $data['listDateWK'] = $association->getDateWK();

        $data['list'] = array();
        if (!empty($data['date_wk'])) {
            $data['list'] = $this->getLists($data['date_wk']);

            $checkValidated = $association->checkValidatedDate($data['date_wk']);
            $data['hasValidated'] = $checkValidated>0 ? true : false;
        } else {
            if (isset($_GET['date_wk'])) {
                $this->setSession('error', 'Not found date');
            }
        }

        $data['success'] = $this->hasSession('success') ? $this->getSession('success') : ''; $this->rmSession('success');
        $data['error'] = $this->hasSession('error') ? $this->getSession('error') : ''; $this->rmSession('error');
        
        $data['export_excel'] = route('export/association&date='.$data['date_wk']);
        $data['action_import'] = route('association/import');
        $data['action'] = route('association/validated');
        $data['action_search'] = route('association');
        $data['action_addmenual'] = route('association/validatedMenual');

        $this->view('association/index',$data);
    }

    public function getLists($date_wk) {
        $data = array();

        $association = $this->model('association');
        $data['list'] = array();

        if (empty($date_wk)) {
            $this->setSession('error', 'Not found date WK');
            $this->redirect('association');
        }


        $lists = $association->getProducts($date_wk);
        $date_lastweek = $association->getDateLastWeek();
        $thisFirstWeek = ($date_lastweek==false) ? true : false;
        foreach ($lists as $key => $value) {
            $last_week = ($date_lastweek!=false) ? $association->getGroupLastWeek($value['size'], $date_lastweek) : '';
            $remaining_qty = 0;
            if (!empty($last_week)) {
                $groupReceived = $association->getGroupReceived($last_week);
                $barcodeUse = $association->getBarcodeUse($last_week);
                if ($groupReceived!=false) {
                    if ($barcodeUse==0) {
                        $remaining_qty = $groupReceived;
                    } else {
                        $remaining_qty = $groupReceived - $barcodeUse;
                    }
                }
            }
            // $remaining_qty = !empty($last_week) ? $association->getRemainingByGroup($last_week) : 0;

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

            $text = $message;
            $data['list'][] = array(
                'id_product' => $value['id_product'],
                'size' => $value['size'],
                'sum_prod' => $value['sum_prod'],
                'last_wk0' => !empty($last_week) ? sprintf('%03d', $last_week) : '',
                'remaining_qty' => number_format((int)$remaining_qty,0),
                'propose' => !empty($propose) ? sprintf('%03d', $propose) : '',
                'propose_remaining_qty' => number_format((int)$propose_remaining_qty,0),
                'message' => $text,
                'save' => !empty($value['group_code']) ? sprintf('%03d',$value['group_code']) : ''
            );
        }
        return $data['list'];
    }

    public function import() {
        if(method_post()){
            $association = $this->model('association');

            $date_wk = '';

            $dir = 'uploads/association/';
            $path = DOCUMENT_ROOT . $dir;
            $path_csv = DOCUMENT_ROOT . $dir;

            $file = $_FILES['excel_input'];
            $fileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
            $newname = 'import_association_'.date('YmdHis');
            $file_csv = 'CSV_'.$newname.'.csv';
            $newname .= '.'.$fileType;
            $acceptFileType  = array('xlsx'); // Accept file type

            // Check path and create folder
            if (!file_exists($path)) {
                $oldmask = umask(0);
                mkdir($path, 0777);
                umask($oldmask);
            }
            // Check file upload
            if ($file['error']==0 && in_array($fileType, $acceptFileType)) {
                if (upload($file, $path, $newname)) {
                    $date = (post('date')?post('date'):date('Y-m-d'));
                    $id_user = $this->getSession('id_user');

                    // Read excel and write file to csv, because csv is speed query
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

                    // Query insert all row in csv
                    $last_date = $association->importCSV($csv_file);

                    $split = explode(' ',$last_date);
                    if (!empty($split[0])) {
                        $date_wk = $split[0];
                        $this->setSession('success','Import association success');
                    } else {
                        $this->setSession('error', 'Fail import association');
                    }
                    
                }
            }

            $this->generateJsonFreeGroup();
            $this->redirect('association&date_wk='.$date_wk);
        } else {
            $this->setSession('error', 'Not found post');
            $this->redirect('association&date_wk='.get('date_wk'));
        }
    }

    public function validated() {
        if (method_post()) {
            $barcode = $this->model('barcode');
            $size = $this->model('size');
            $association = $this->model('association');
            $group = $this->model('group');

            $resultMapping = array();
            $id_group = post("id_group");

            // Get checkbox
            $check = post('checkbox');
            $checked = array();
            foreach ($check as $idg => $value) {
                $checked[] = $idg;
            }

            // Create json file and get data
            $freegroup = $this->jsonFreeGroup(false);
            $jsonfreegroup = json_decode($freegroup, true);
            $freegroup = json_decode($jsonfreegroup[0], true);

            $i=0;
            foreach ($id_group as $key => $value) {
                if (in_array($key, $checked)) { // Insert with checkbox is checked only
                    
                    if (!empty($value)) { // Menual add validated
                        $insert = array(
                            'date_wk' => post('date_wk'),
                            'id_user' => $this->getSession('id_user'),
                            'id_group' => $value,
                            'id_product' => $key
                        );
                        $resultMapping[] = $association->validatedProductWithGroup($insert);

                    } else { // Auto add validated
                        $product_info = $size->getProduct($key);
                        $date_lastweek = $association->getDateLastWeek();

                        // Get last week if have product_info setting
                        $last_week = ($date_lastweek!=false) ? $association->getGroupLastWeek($product_info['size_product_code'], $date_lastweek) : '';
                        $remaining_qty = !empty($last_week) ? $association->getRemainingByGroup($last_week) : 0;

                        // Find relation group
                        $relation_group = $association->getRelationshipBySize($product_info['size_product_code']);

                        $propose = '';
                        $propose_remaining_qty = '';
                        $message = '';

                        if ($remaining_qty>=$product_info['sum_product']) { // If can use old group on last week
                            $propose = $last_week;
                            $propose_remaining_qty = $remaining_qty;
                            $message = 'Last Weeek' ;

                        } else if (!empty($relation_group)) { // If have relation in condition "not use on 3 day"
                            $qty = $association->getRemainingByGroup($relation_group);
                            if ($propose_remaining_qty>=$product_info['sum_product']) {
                                $propose = $relation_group;
                                $propose_remaining_qty = $qty;
                                $message = 'Relationship';
                            }

                        } else { // Use free group in json file
                            $propose = (int)$freegroup[$i];
                            $i++;
                        }

                        if (!empty($propose)) {
                            $insert = array(
                                'date_wk' => post('date_wk'),
                                'id_user' => $this->getSession('id_user'),
                                'id_group' => $propose, // this group code 
                                'id_product' => $product_info['id_product']
                            );
                            $resultMapping[] = $association->validatedProductWithGroup($insert);
                        }
                        
                        
                    }
                }
            }
            // $this->redirect('barcode/association&date_wk='.post('date_wk'));
            if (in_array(false, $resultMapping)) {
                $this->setSession('error', 'Fail some group cannot validated');
            } else {
                $this->generateJsonFreeGroup();
                $this->setSession('success', 'Successfil validated group');
            }
        } else {
            $this->setSession('error', 'Not found post');
        }
        $this->redirect('association&date_wk='.post('date_wk'));
    }

    public function validatedMenual() {
        if (method_post()) {
            $association = $this->model('association');

            $insert = array(
                'id_user' => $this->getSession('id_user'),
                'id_group' => null,
                'date_wk' => post('date_wk').' 00:00:00',
                'size_product_code' => post('size_product_code'),
                'sum_product' => post('sum_product'),
                'date_added' => date('Y-m-d H:i:s'),
                'date_modify' => date('Y-m-d H:i:s')
            );
            $result = $association->addProduct($insert);
            if ($result>0) {
                $this->setSession('success', 'Success add menual product');
            } else {
                $this->setSession('error', 'Fail add menual');
            }
        } 
        $this->redirect('association&date_wk='.post('date_wk'));
    }


    // JSON FILE
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
    // JSON FILE
}