<?php
class AssociationController extends Controller
{

    public function __construct()
    {
        if ($this->hasSession('id_user') == false) {
            $this->rmSession('id_user');
            $this->rmSession('username');
            $this->setSession('error', 'Please Login');
            $this->redirect('home');
        }
    }
    public function index()
    {
        $data = array();
        $association = $this->model('association');

        $data['date_wk'] = get('date_wk');
        $data['listDateWK'] = $association->getDateWK();

        $data['list'] = array();
        if (!empty($data['date_wk'])) {
            $data['list'] = $this->getLists($data['date_wk']);

            // $checkValidated = $association->checkValidatedDate($data['date_wk']);
            // $data['hasValidated'] = $checkValidated > 0 ? true : false;
        } else {
            if (isset($_GET['date_wk'])) {
                $this->setSession('error', 'Not found date');
            }
        }

        $data['success'] = $this->hasSession('success') ? $this->getSession('success') : '';
        $this->rmSession('success');
        $data['error'] = $this->hasSession('error') ? $this->getSession('error') : '';
        $this->rmSession('error');

        $data['export_excel'] = route('export/association&date=' . $data['date_wk']);
        $data['action_import'] = route('association/import');
        $data['action'] = route('association/validated');
        $data['action_search'] = route('association');
        $data['action_addmenual'] = route('association/validatedMenual');

        $this->view('association/index', $data);
    }
    public function ajaxCountBarcode() {
        $group_code = $_POST['group'];

        $json = $this->jsonFreeGroup(false);
        $list = json_decode($json,true);
        $list = json_decode($list[0], true);
        $count = '';
        foreach ($list as $value) {
            $group = $value['group'];
            $qty = $value['qty'];
            if ($group == $group_code) {
                $count = $qty;
            }
        }
        // echo $count;
        //$association = $this->model('association');
        //$json = $association->getNotUseBarcode($group_code);
        $this->json($json);
    }

    public function ajaxCountBarcodeNotuse() {
        $group_code = $_POST['group'];
        $association = $this->model('association');
        $remaining = $association->getRemaining($_POST['id_product']);
        if ($remaining==false) {
            $json = $association->getNotUseBarcode($group_code);
        } else {
            $json = $remaining;
        }
        $this->json($json);
    }

    public function ajaxCheckOldSync() {
        $association = $this->model('association');
        // Group barcode use with old association in config day
        $oldSync = $association->getOldSync(); 
        $beforeSync = array(); 
        foreach ($oldSync as $v) {
            $beforeSync[] = $v['group_code'];
        }
        $this->json($beforeSync);
    }

    public function ajaxSavePropose() {

        $idproduct = $_POST['id_product'];
        $data = array();
        $data['remaining_qty'] =  isset($_POST['remaining_qty'])&&!empty($_POST['remaining_qty']) ? $_POST['remaining_qty'] : '';
        $data['propose'] =  isset($_POST['propose'])&&!empty($_POST['propose']) ? $_POST['propose'] : '';
        $data['propose_remaining_qty'] =  isset($_POST['propose_remaining_qty'])&&!empty($_POST['propose_remaining_qty']) ? $_POST['propose_remaining_qty'] : '';
        $data['message'] =  isset($_POST['message'])&&!empty($_POST['message']) ? $_POST['message'] : '';
        $association = $this->model('association');
        $association->savePropose($idproduct, $data);
    }

    public function ajaxCondition() {
        $association = $this->model('association');
        $config = $this->model('config');

        $size = $_POST['size'];
        $sumprod = str_replace(',','',$_POST['sum_prod']);
        $last_week = $_POST['last_wk'];
        $remaining_qty = $_POST['qty'];

        $pp = $association->getPropose($_POST['id_product']);
        if ($pp!==false) {
            $propose = $pp['propose'];
            $propose_remaining_qty = $pp['propose_remaining_qty'];
            $message = $pp['message'];

            $json = array(
                'size' => $size,
                'sum_prod' => $sumprod,
                'last_wk0' => !empty($last_week) ? sprintf('%03d', $last_week) : '',
                'remaining_qty' => number_format((int) round($remaining_qty,0), 0),
                'propose' => !empty(strip_tags($propose)) ? ($propose!=$last_week?''.sprintf('%03d', $propose).'':sprintf('%03d', $propose)) : '',
                'propose_remaining_qty' => round($propose_remaining_qty,0) > 0 ? ($propose!=$last_week?''.number_format((int) round($propose_remaining_qty,0), 0).'':number_format((int) round($propose_remaining_qty,0), 0)) : '',
                'message' => $message, 
            );
        } else {

            $notuse = array();

            $relation_group = $association->getRelationshipBySize($size, $sumprod);
            if ($sumprod>0) {
                if (!empty($relation_group['group']) && !empty($relation_group['qty'])) {
                    if (!in_array((int)$relation_group['group'],$notuse)) {
                        $notuse[] = (int)$relation_group['group'];
                    }
                }
                else if ($remaining_qty >= $sumprod) {
                    if (!in_array((int)$last_week,$notuse)) {
                        $notuse[] = (int)$last_week;
                    }
                }
            }

            // Config Relationship
            $config_relation = array();
            $temprelation = $config->getRelationship();
            foreach ($temprelation as $tr) {
                $config_relation[] = (int)$tr['group'];
            }

            // Group barcode use with old association in config day
            $oldSync = $association->getOldSync(); 
            $beforeSync = array(); 
            foreach ($oldSync as $v) {
                $beforeSync[] = $v['group_code'];
            }

            $propose = '';
            $propose_remaining_qty = '';
            $message = '';

            if ($sumprod>0) {
                
                if (!empty($relation_group['group']) && !empty($relation_group['qty'])) {
                    $propose = $relation_group['group'];
                    $propose_remaining_qty = $relation_group['qty'];
                    $message = '<span class="text-primary">Relationship</span>';
                    // unset($freegroup[(int)$relation_group['group']]);
                } 
                else if ($remaining_qty >= $sumprod) {
                    $propose = $last_week;
                    $propose_remaining_qty = $remaining_qty;
                    $message = 'Last Weeek';
                    // unset($freegroup[$last_week]);
                } 
                else {
                    $free = '';
                    $free_qty = '';
                }
            }

            if (empty($last_week)) {
                $propose = '';
                $propose_remaining_qty = '';
                $message = '';
            }


            $json = array(
                'size' => $size,
                'sum_prod' => $sumprod,
                'last_wk0' => !empty($last_week) ? sprintf('%03d', $last_week) : '',
                'remaining_qty' => number_format((int) round($remaining_qty,0), 0),
                'propose' => !empty(strip_tags($propose)) ? ($propose!=$last_week?''.sprintf('%03d', $propose).'':sprintf('%03d', $propose)) : '',
                'propose_remaining_qty' => round($propose_remaining_qty,0) > 0 ? ($propose!=$last_week?''.number_format((int) round($propose_remaining_qty,0), 0).'':number_format((int) round($propose_remaining_qty,0), 0)) : '',
                'message' => $message, 
            );

        }


        $this->json($json);

    }



    public function getLists($date_wk = '')
    {
        $data = array();

        $association = $this->model('association');
        $config = $this->model('config');
        $data['list'] = array();

        if (empty($date_wk)) {
            $this->setSession('error', 'Not found date WK');
            $this->redirect('association');
            exit();
        }

        // Free Group
        $free_group = $this->jsonFreeGroup(false);
        $temp_freegroup = json_decode($free_group, true);
        $temp_freegroup = json_decode($temp_freegroup[0], true);
        $freegroup = array();
        foreach ($temp_freegroup as $v) {
            $freegroup[$v['group']] = $v['qty'];
        }

        // getgroup Config Relationship
        $config_relation = array();
        $temprelation = $config->getRelationship();
        foreach ($temprelation as $tr) {
            $config_relation[] = (int)$tr['group'];
        }

        $oldSync = $association->getOldSync(); // Group barcode use with old association in config day
        $beforeSync = array(); 
        foreach ($oldSync as $v) {
            $beforeSync[] = $v['group_code'];
        }

        $lists = $association->getProducts($date_wk);
        $notuse = array();
        foreach ($lists as $key => $value) {
            $last_week = $value['last_week'];
            // $remaining_qty = (int)$value['remaining_qty'];
            $remaining_qty = 0;

            $relation_group = $association->getRelationshipBySize($value['size'], $value['sum_prod']);
            $lists[$key]['relation_group'] = $relation_group;

            // if ($value['sum_prod']>0) {
            //     if (!empty($relation_group['group']) && !empty($relation_group['qty'])) {
            //         if (!in_array((int)$relation_group['group'],$notuse)) {
            //             $notuse[] = (int)$relation_group['group'];
            //         }
            //     }
            //     else if ($remaining_qty >= $value['sum_prod']) {
            //         if (!in_array((int)$last_week,$notuse)) {
            //             $notuse[] = (int)$last_week;
            //         }
            //     }
            // }
        }

        foreach ($lists as $key => $value) {
            $last_week = $value['last_week'];
            $remaining_qty = 0;

            $relation_group = $value['relation_group'];

            $propose = '';
            $propose_remaining_qty = '';
            $message = '';

            if (!empty($relation_group['size'])&&$relation_group['size']==$value['size']) {
                $message = '<span class="text-primary">Relationship</span>';
            }

            if ($value['sum_prod']>0) {
                if (!empty($relation_group['group'])) {
                    if (!empty($relation_group['qty'])) {
                        $propose = $relation_group['group'];
                        $propose_remaining_qty = $relation_group['qty'];
                        $message = '<span class="text-primary">Relationship</span>';
                        unset($freegroup[(int)$relation_group['group']]);
                    }
                } 
                else if ($remaining_qty >= $value['sum_prod']) {
                    $propose = $last_week;
                    $propose_remaining_qty = $remaining_qty;
                    $message = 'Last Weeek';
                    unset($freegroup[$last_week]);
                } 
                else {
                    $free = '';
                    $free_qty = '';

                    // if (count($freegroup)>0) {
                    //     foreach ($freegroup as $keyfirst => $fgqty) {
                    //         if (!in_array($keyfirst,$beforeSync)) {
                    //             if ($fgqty>=$value['sum_prod'] && !in_array($keyfirst, $config_relation) && !in_array($keyfirst, $notuse) ) {
                    //                 $free = $keyfirst;
                    //                 $free_qty = $freegroup[$keyfirst];   
                    //                 unset($freegroup[$keyfirst]); 
                    //                 // break;
                    //             }
                    //         }
                    //     }
                    // }
                    // if (!empty($free)&&!empty($free_qty)) {
                    //     $propose = $free;
                    //     $propose_remaining_qty = $free_qty;
                    //     $message = !empty($free) ? '<span class="text-danger">Free Group</span>' : '';
                    // }
                }
            }

            if (empty($last_week)) {
                $propose = '';
                $propose_remaining_qty = '';
                // $message = '';
            }

            $text = $message;
            $data['list'][] = array(
                'id_product' => $value['id_product'],
                'size' => $value['size'],
                'sum_prod' => $value['sum_prod'],
                'last_wk0' => !empty($last_week) ? sprintf('%03d', $last_week) : '',
                'remaining_qty' => number_format((int) round($remaining_qty,0), 0),
                'propose' => !empty(strip_tags($propose)) ? ($propose!=$last_week?'<span class="text-danger">'.sprintf('%03d', $propose).'</span>':sprintf('%03d', $propose)) : '',
                'propose_remaining_qty' => round($propose_remaining_qty,0) > 0 ? ($propose!=$last_week?'<span class="text-danger">'.number_format((int) round($propose_remaining_qty,0), 0).'</span>':number_format((int) round($propose_remaining_qty,0), 0)) : '',
                'message' => $text,
                'plain_message' => strip_tags($text),
                'save' => !empty($value['save']) ? sprintf('%03d', $value['save']) : '',
            );
            // exit();
        }
        return $data['list'];
    }

    public function import()
    {
        if (method_post()) {
            $association = $this->model('association');

            $date_wk = '';

            

            $dir = 'uploads/association/';
            $path = DOCUMENT_ROOT . $dir;
            $path_csv = DOCUMENT_ROOT . $dir;

            if (!file_exists($path)) {
                $oldmask = umask(0);
                mkdir($path, 0777);
                umask($oldmask);
            }

            $file = $_FILES['excel_input'];
            $fileType = strtolower(pathinfo(basename($file["name"]), PATHINFO_EXTENSION));
            $newname = 'import_association_' . date('YmdHis');
            $file_csv = 'CSV_' . $newname . '.csv';
            $newname .= '.' . $fileType;
            $acceptFileType = array('xlsx'); // Accept file type

            // Check path and create folder
            if (!file_exists($path)) {
                $oldmask = umask(0);
                mkdir($path, 0777);
                umask($oldmask);
            }
            // Check file upload
            if ($file['error'] == 0 && in_array($fileType, $acceptFileType)) {
                if (upload($file, $path, $newname)) {
                    $date = (post('date') ? post('date') : date('Y-m-d'));
                    $id_user = $this->getSession('id_user');

                    // Read excel and write file to csv, because csv is speed query
                    $results = readExcel($dir . $newname); // read excel to csv
                    $csv_file = $path_csv . $file_csv;
                    $fp = fopen($csv_file, 'w');
                    $barcode_use = array();
                    foreach ($results as $key => $result) {
                        $insert = array(
                            $id_user,
                            $result[0],
                            $result[1],
                            '0000-00-00 00:00:00',
                            '0000-00-00 00:00:00',
                            '0000-00-00 00:00:00',
                        );
                        fputcsv($fp, $insert, ',', chr(0));
                    }
                    fclose($fp);

                    // Query insert all row in csv
                    $last_date = $association->importCSV($csv_file);

                    $split = explode(' ', $last_date);
                    if (!empty($split[0])) {
                        $date_wk = $split[0];
                        $this->setSession('success', 'Import association success');
                    } else {
                        $this->setSession('error', 'Fail import association');
                    }

                }
            }

            // $this->generateJsonFreeGroup();
            $this->redirect('loading/someone&key=freegroup,year,barcode&redirect=association');
            $this->redirect('association&date_wk=' . $date_wk);
        } else {
            $this->setSession('error', 'Not found post');
            $this->redirect('association&date_wk=' . get('date_wk'));
        }
    }

    public function validated()
    {
        if (method_post()) {
            $barcode = $this->model('barcode');
            $size = $this->model('size');
            $association = $this->model('association');
            $group = $this->model('group');

            $resultMapping = array();
            $id_group = post("id_group");
            $propose = post("propose");

            // Get checkbox
            $check = post('checkbox');
            $checked = array();
            foreach ($check as $idg => $value) {
                $checked[] = $idg;
            }

            // Create json file and get data
            $free_group = $this->jsonFreeGroup(false);
            $temp_freegroup = json_decode($free_group, true);
            $temp_freegroup = json_decode($temp_freegroup[0], true);
            $freegroup = array();
            foreach ($temp_freegroup as $v) {
                $freegroup[$v['group']] = $v['qty'];
            }

            $i = 0;
            foreach ($id_group as $key => $value) {
                if (in_array($key, $checked)) { // Insert with checkbox is checked only

                    if (!empty($value)) { // Menual add validated
                        $insert = array(
                            'date_wk' => post('date_wk'),
                            'id_user' => $this->getSession('id_user'),
                            'id_group' => $value, // groupcode
                            'id_product' => $key,
                        );
                        $resultMapping[] = $association->validatedProductWithGroup($insert);
                        unset($freegroup[$value]);

                    } else { // Auto add validated

                        
                        // use value on input propose
                        $insert = array(
                            'date_wk' => post('date_wk'),
                            'id_user' => $this->getSession('id_user'),
                            'id_group' => $propose[$key], // this group code
                            'id_product' => $key,
                        );
                        $resultMapping[] = $association->validatedProductWithGroup($insert);
                        unset($freegroup[$propose[$key]]);

                    }
                }
            }
            if (in_array(false, $resultMapping)) {
                $this->setSession('error', 'Fail some group cannot validated');
            } else {
                //$this->generateJsonFreeGroup();
                $this->setSession('success', 'Successfil validated group');
                redirect('loading/someone','&key=freegroup&redirect=association');
            }
        } else {
            $this->setSession('error', 'Not found post');
        }
        $this->redirect('association&date_wk=' . post('date_wk'));
    }

    public function validatedMenual()
    {
        if (method_post()) {
            $association = $this->model('association');
            
            $check = $association->checkProduct(post('size_product_code'));
            if ($check) {
                $insert = array(
                    'id_user' => $this->getSession('id_user'),
                    'id_group' => null,
                    'date_wk' => post('date_wk') . ' 00:00:00',
                    'size_product_code' => post('size_product_code'),
                    'sum_product' => post('sum_product'),
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_modify' => date('Y-m-d H:i:s'),
                );
                $result = $association->addProduct($insert);
                if ($result > 0) {
                    $this->setSession('success', 'Success add menual product');
                } else {
                    $this->setSession('error', 'Fail add menual');
                }
            } else {
                $this->setSession('error', 'Fail add menual');
            }

            
        }
        $this->redirect('association&date_wk=' . post('date_wk'));
    }

    // JSON FILE
    public function jsonFreeGroup($header = true)
    {
        $json = array();
        if (!file_exists(DOCUMENT_ROOT . 'uploads/freegroup.json')) {
            $this->generateJsonFreeGroup();
        }
        $file_handle = fopen(DOCUMENT_ROOT . 'uploads/freegroup.json', "r");
        while (!feof($file_handle)) {
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
    public function generateJsonFreeGroup()
    {
        $association = $this->model('association');
        $lists = $association->getFreeGroup();
        $json = array();
        foreach ($lists as $value) {
            $json[] = $value;
        }
        $fp = fopen(DOCUMENT_ROOT . 'uploads/freegroup.json', 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);
        return $json;
    }
    // JSON FILE

    /*
    public function jsonCountBarcode($header = true) {
        $json = array();
        if (!file_exists(DOCUMENT_ROOT . 'uploads/countbarcode.json')) {
            $this->generateJsonCountBarcode();
        }
        $file_handle = fopen(DOCUMENT_ROOT . 'uploads/countbarcode.json', "r");
        while (!feof($file_handle)) {
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
    public function generateJsonCountBarcode()
    {
        $association = $this->model('association');
        $lists = $association->countAllBarcodeNotUsed();
        $json = array();
        foreach ($lists as $value) {
            $json[$value['group_code']] = $value['qty'];
        }
        $fp = fopen(DOCUMENT_ROOT . 'uploads/countbarcode.json', 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);
        return $json;
    }*/
}
