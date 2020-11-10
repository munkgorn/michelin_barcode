<?php
class LoadingController extends Controller
{
    public function index()
    {
        $data = array();
        
        $this->trash();

        $data['redirect'] = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard';

        $data['loading'] = array();
        // $data['loading'][] = array(
        //     'name' => 'Free Group',
        //     'url' => 'index.php?route=association/generateJsonFreeGroup',
        // );
        $data['loading'][] = array(
            'name' => 'Default Year',
            'url' => 'index.php?route=purchase/generateJsonDefaultYear',
        );
        $data['loading'][] = array(
            'name' => 'Default group barcode all group start and end in 3 year',
            'url' => 'index.php?route=purchase/generateJsonDefaultBarcode',
        );
        $data['loading'][] = array(
            'name' => 'Default Group Date',
            'url' => 'index.php?route=barcode/generateJsonDateBarcode'
        );

        $this->view('loading/index', $data);
    }

    public function trash() 
    {
        $files = array(
            'default_year.json',
            'default_purchase.json',
            // 'freegroup.json',
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