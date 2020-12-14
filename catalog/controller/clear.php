<?php 
	class ClearController extends Controller {

        public function index() {
            $data = array();
            date_default_timezone_set('Asia/Bangkok');

            $association = $this->model('association');
            $data['dateass'] = $association->getDateWK();

            $data['success'] = $this->hasSession('success') ? $this->getSession('success') : ''; $this->rmSession('success');
            $data['error'] = $this->hasSession('error') ? $this->getSession('error') : ''; $this->rmSession('error');

 	    	$this->view('clear/index',$data);
        }

        public function removeBarcode() {
            if (isset($_POST['date'])&&!empty($_POST['date'])&&strtotime($_POST['date'])<=strtotime('+1 day')) {
                $date = $_POST['date'];
                $barcode = $this->model('barcode');
                $result = $barcode->clearBarcode($date);
                if ($result) {
                    $this->setSession('success','Success remove barcode before date '.$_POST['date']);
                } else {
                    $this->setSession('error', 'Fail cannot remove barcode before date'.$_POST['date']);
                }
            } else {
                $this->setSession('error', 'Fail cannot remove barcode before date'.$_POST['date']);
            }
            redirect('clear');
            
        }
        public function removeAssociation() {
            if (isset($_POST['association'])&&!empty($_POST['association'])) {
                $date = $_POST['association'];
                $association = $this->model('association');
                if ($date=='all') {
                    $result = $association->clearAllAssociation($date);
                } else {
                    $result = $association->clearAssociation($date);
                }
                
                if ($result) {
                    $this->setSession('success','Success remove association date '.$date);
                } else {
                    $this->setSession('error', 'Fail cannot remove association date'.$date);
                }
            } else {
                $this->setSession('error', 'Fail cannot remove association date'.$date);
            }
            redirect('clear');
        }
        public function removeFile() {
            $text = array();
            $dir = DOCUMENT_ROOT.'uploads/';
            $scan = scandir($dir);
            foreach ($scan as $value) {
                if (!in_array($value,array('.','..','.DS_Store'))) {
                    if (is_file($dir.$value)) {
                        $ex = explode('.', $value);
                        if (end($ex)!='json') {
                            $path = $dir.$value;
                            $text[] = 'Remove '.$path.'<br>';
                            unlink($path);
                        }
                    } else {
                        $scan2 = scandir($dir.$value);
                        foreach ($scan2 as $value2) {
                            if (!in_array($value2,array('.','..','.DS_Store'))) {
                                if (is_file($dir.$value.'/'.$value2)) {
                                    $ex = explode('.', $value2);
                                    if (end($ex)!='json'&&in_array(end($ex), array('csv','xls','xlsx'))) {
                                        $path2=$dir.$value.'/'.$value2;
                                        $text[] = 'Remove '.$path2.'<br>';
                                        unlink($path2);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // $this->json($text);
            $this->setSession('success', '<b>Success remove file</b><br>'.implode('', $text));
            redirect('clear');
        }
    }
?>