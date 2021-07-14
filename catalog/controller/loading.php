<?php
class LoadingController extends Controller
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

        if ($this->hasSession('id_user') == false) {
            $this->rmSession('id_user');
            $this->rmSession('username');
            $this->setSession('error', 'Please Login');
            $this->redirect('home');
            exit();
        }
        
        $this->trash();

        $data['redirect'] = isset($_GET['redirect']) ? $_GET['redirect'] : ($this->hasSession('redirect') ? $this->getSession('redirect') : 'dashboard');
        if ($data['redirect']=='loading') {
            $data['redirect']='dashboard';
        }

        $config = $this->model('config');
        $config->getConfig('load_freegroup');

        $data['loading'] = array();
        if ($config->getConfig('load_freegroup')==1) {
            $data['loading']['freegroup'] = array(
                'name' => 'Free Group',
                'url' => 'index.php?route=association/generateJsonFreeGroup',
            );
        }
        /*
        $data['loading']['count'] = array(
            'name' => 'Count barcode not used',
            'url' => 'index.php?route=association/generateJsonCountBarcode',
        );
        */
        if ($config->getConfig('load_year')==1) {
            $data['loading']['year'] = array(
                'name' => 'Default Year',
                'url' => 'index.php?route=purchase/generateJsonDefaultYear',
            );
        }
        // if ($config->getConfig('load_barcode')==1) {
        //     $data['loading']['barcode'] = array(
        //         'name' => 'Default group barcode all group start and end in 3 year',
        //         'url' => 'index.php?route=purchase/generateJsonDefaultBarcode',
        //     );
        // }   
        // if ($config->getConfig('load_date')==1) {
        //     $data['loading']['date'] = array(
        //         'name' => 'Default Group Date',
        //         'url' => 'index.php?route=barcode/generateJsonDateBarcode'
        //     );
        // }

        $this->view('loading/index', $data);
    }

    public function someone() {

        $key = explode(',',$_GET['key']);
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'association';

        $loading = array();
        $loading['freegroup'] = array(
            'name' => 'Free Group',
            'url' => 'index.php?route=association/generateJsonFreeGroup',
        );
        /*
        $loading['count'] = array(
            'name' => 'Count barcode not used',
            'url' => 'index.php?route=association/generateJsonCountBarcode',
        );
        */
        $loading['year'] = array(
            'name' => 'Default Year',
            'url' => 'index.php?route=purchase/generateJsonDefaultYear',
        );
        $loading['barcode'] = array(
            'name' => 'Default group barcode all group start and end in 3 year',
            'url' => 'index.php?route=purchase/generateJsonDefaultBarcode',
        );
        // $loading['date'] = array(
        //     'name' => 'Default Group Date',
        //     'url' => 'index.php?route=barcode/generateJsonDateBarcode'
        // );

        $data['loading'] = array();
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                if (isset($loading[$v])) {
                    $data['loading'][] = $loading[$v];
                }
                
            }
        } else {
            if (isset($loading[$key])) {
                $data['loading'] = $loading;
            }
        }

        

        $files = array(
            'freegroup' => 'freegroup.json',
            //'count' => 'countbarcode.json',
            'year' => 'default_year.json',
            'barcode' => 'default_purchase.json',
            'date' => 'default_datebarcode.json'
        );

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $file = isset($files[$v]) ? $files[$v] : '';
                if (!empty($file)) {
                    $path = DOCUMENT_ROOT . 'uploads/'. $file;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }
        } else {
            $file = isset($files[$key]) ? $files[$key] : '';
            if (!empty($file)) {
                $path = DOCUMENT_ROOT . 'uploads/'. $file;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
        

        if (!empty($redirect)) {
            // $this->redirect($redirect);
            $data['redirect'] = $redirect;
        }
        $this->view('loading/index', $data);

        

        
    }

    public function trash() 
    {
        $files = array(
            'freegroup.json',
            //'countbarcode.json',
            'default_year.json',
            'default_purchase.json',
            // 'default_datebarcode.json'
        );
        foreach ($files as $file) {
            $path = DOCUMENT_ROOT . 'uploads/'. $file;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    public function updateTable() {
        $range = $this->model('range');
        $range->createTable();

        // $range->updateTable();

        $this->model('config')->setConfig('load_freegroup', 0);
        $this->model('config')->setConfig('load_year', 0);
        $this->model('config')->setConfig('load_barcode', 0);
        $this->model('config')->setConfig('load_date', 0);
    }

    public function updateTable2() {
        $range = $this->model('range');
        // $range->createTable();

        $range->updateTable();

        $this->model('config')->setConfig('load_freegroup', 0);
        $this->model('config')->setConfig('load_year', 0);
        $this->model('config')->setConfig('load_barcode', 0);
        $this->model('config')->setConfig('load_date', 0);
    }

    public function range() {
        $data = array();
        
        if (method_post()) {
            $round = 1;
            $group = 12;
            $status = 1;
            $flag = 0;
        } else {
            $round = $_GET['round'];
            $group = $_GET['group'];
            $status = $_GET['status'];
            $flag = $_GET['flag'];  
        }
        

        $data['status'] = $status; // status = 1 is consum / 0 is remaining
        $data['round'] = $round;

        $barcode = $this->model('barcode');
        $list = $barcode->getBarcodeWithGroup($group, $status, $flag);
        $data['list'] = json_encode($list);

        $this->view('loading/range', $data);
    }

    public function showdb() {
        var_dump(DB_USER);
        var_dump(DB_PASS);
    }

    public function rangeall() {
        $data = array();
        
        if (method_post()) {
            $round = 1;
            $group = 12;
            $status = 1;
            $flag = 0;
        } else {
            $round = isset($_GET['round']) ? $_GET['round'] : 1;
            $group = isset($_GET['group']) ? $_GET['group'] : '';
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            $flag = isset($_GET['flag']) ? $_GET['flag'] : '';  
        }
        

        $data['status'] = (int)$status; // status = 1 is consum / 0 is remaining
        $data['round'] = $round;
        $data['group'] = $group;
        
        $config = $this->model('config');
        $bs = $config->getBarcodes();

        $start_barcode = (int)$bs[0]['id'];
        
        $now_barcode = (int)$group;
        
        $max_barcode = (int)$bs[count($bs)-1]['id'];
        
        
        $data['percent'] = (($now_barcode-$start_barcode) / ($max_barcode - $start_barcode)) * 100;
        

        $barcode = $this->model('barcode');
        $list = $barcode->getBarcodeWithIDGroup($group, $status, 0);
        
        $data['list'] = json_encode($list);

        $config = $this->model('config');
        // $config->setConfig('load_freegroup', 1);
        $config->setConfig('load_year', 1);
        // $config->setConfig('load_barcode', 1);
        $this->setSession('redirect', 'loading');

        $this->view('loading/rangeall', $data);
    }

    public function range_barcode() 
    {
        $data = array();
        $data['groups'] = array();
        $config = $this->model('config');
        $barcodes = $config->getBarcodes();
        foreach ($barcodes as $barcode) {
            $data['groups'][] = $barcode['group'];
        }

        $data['groups'] = json_encode($data['groups']);
        $this->view('loading/range_barcode', $data);
    }

    public function ajaxGetGroup() 
    {
        $group = $_POST['group'];
        $status = $_POST['status'];
        $barcode = $this->model('barcode');
        $list = $barcode->getBarcodeWithGroup($group, $status, 0);
        $this->json($list);
    }

    public function ajaxGetBarcodeWithGroup($group, $status) {
        $barcode = $this->model('barcode');
        $list = $barcode->getBarcodeWithGroup($group, $status, 0);
        // $data['list'] = json_encode($list);
        $this->json($list);
    }
}