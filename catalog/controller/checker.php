<?php 
class CheckerController extends Controller {
    public function __construct() {
    }

    public function index() {
        $data = array();
        $this->view('barcode/import', $data);
    }
    
}