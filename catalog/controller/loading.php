<?php
class LoadingController extends Controller
{
    public function index()
    {
        $data = array();
        
        $this->trash();

        $data['redirect'] = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard';

        $data['loading'] = array();
        $data['loading']['freegroup'] = array(
            'name' => 'Free Group',
            'url' => 'index.php?route=association/generateJsonFreeGroup',
        );
        /*
        $data['loading']['count'] = array(
            'name' => 'Count barcode not used',
            'url' => 'index.php?route=association/generateJsonCountBarcode',
        );
        */
        $data['loading']['year'] = array(
            'name' => 'Default Year',
            'url' => 'index.php?route=purchase/generateJsonDefaultYear',
        );
        $data['loading']['barcode'] = array(
            'name' => 'Default group barcode all group start and end in 3 year',
            'url' => 'index.php?route=purchase/generateJsonDefaultBarcode',
        );
        $data['loading']['date'] = array(
            'name' => 'Default Group Date',
            'url' => 'index.php?route=barcode/generateJsonDateBarcode'
        );

        $this->view('loading/index', $data);
    }

    public function someone() {

        $key = explode(',',$_GET['key']);
        $redirect = $_GET['redirect'];

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
        $loading['date'] = array(
            'name' => 'Default Group Date',
            'url' => 'index.php?route=barcode/generateJsonDateBarcode'
        );

        $data['loading'] = array();
        if (isset($loading[$key])) {
            $data['loading'] = $loading;
        }

        $files = array(
            'freegroup' => 'freegroup.json',
            //'count' => 'countbarcode.json',
            'year' => 'default_year.json',
            'barcode' => 'default_purchase.json',
            'date' => 'default_datebarcode.json'
        );

        $file = isset($files[$key]) ? $files[$key] : '';
        if (!empty($file)) {
            $path = DOCUMENT_ROOT . 'uploads/'. $file;
            if (file_exists($path)) {
                unlink($path);
            }
        }

        if (!empty($redirect)) {
            $this->redirect($redirect);
        } else {
            $this->view('loading/index', $data);
        }

        

        
    }

    public function trash() 
    {
        $files = array(
            'freegroup.json',
            //'countbarcode.json',
            'default_year.json',
            'default_purchase.json',
            'default_datebarcode.json'
        );
        foreach ($files as $file) {
            $path = DOCUMENT_ROOT . 'uploads/'. $file;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}