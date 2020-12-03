<?php
class ReportController extends Controller
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

        $data['title'] = "Report remaining stock barcode";
        $style = array(
            'assets/home.css',
        );
        $data['style'] = $style;

        $data['action'] = '';
        $data['export_excel'] = route('export/report');

        $data['success'] = $this->hasSession('success') ? $this->getSession('success') : '';
        $this->rmSession('success');
        $data['error'] = $this->hasSession('error') ? $this->getSession('error') : '';
        $this->rmSession('error');

        $this->view('report/index', $data);
    }

    public function all() 
    {
        $data = array();

        $this->view('report/all', $data);
    }

    public function saveJson()
    {
        // $memory = $this->model('memory');
        // $decode = json_decode($_POST['data'], true);
        // $start = ''; $end = '';
        // if (isset($decode['range'])) {
        //     $ex = explode('-', $decode['range']);
        //     $start = trim($ex[0]);
        //     $end = trim($ex[1]);
        // }
        // $save = array(
        //     'group' => $decode['group'],
        //     'barcode_start' => $start,
        //     'barcode_end' => $end,
        //     'total' => $_POST['qty'],
        //     'type' => 2 // 1 use , 2 notuse
        // );
        // // print_r($save);
        // $memory->saveRange($save);

        $json = $_POST['data'];
        $fp = fopen(DOCUMENT_ROOT . 'uploads/reportall.json', 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);
        return $json;
    }

}
