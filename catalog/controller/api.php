<?php 
	class ApiController extends Controller {
		public function __construct() {
      // index.php?route=api/createTableHistory&dbname=fsoftpro_barcode
		}
    
		public function createTableHistory() {
      $api = $this->model('api');
      if ($api->createTableHistory(get('dbname'))) {
        redirect('api/cleanTableHistory', '&dbname='.get('dbname'));
      } else {
        echo 'Fail Create Table History';
      }
    }
    public function cleanTableHistory() {
      $api = $this->model('api');
      if ($api->cleanTableHistory(get('dbname'))) {
        redirect('api/dumpDataGroupHistory', '&dbname='.get('dbname'));
      } else {
        echo 'Fail TRUNCATE Table History';
      }
    }
    public function dumpDataGroupHistory() {
      $api = $this->model('api');
      $list = $api->dumpDataGroupHistory(get('dbname'));
      $groups = array();
      foreach ($list as $value) {
        $groups[] = (int)$value['group'];
      }
      $_SESSION['groups'] = $groups;
      redirect('api/runDataGroupHistory', '&dbname='.get('dbname').'&key=0');
    }
    public function runDataGroupHistory() {
      $data = array('dbname'=>get('dbname'));
      $api = $this->model('api');
      $key = get('key');
      $groups = $_SESSION['groups'];
      if (isset($groups[$key])) {
        $now = $groups[$key];
        
        $result = $api->runDataGroupHistory(get('dbname'), $now);
        $data['text'] = 'Run Group '.$now.' '.($result==1?'Success':'Fail');
        $data['percent'] = number_format(($key / count($_SESSION['groups'])) * 100, 2, '.', '');
        $groups = implode(',', $groups);
        // redirect('api/runDataGroupHistory', '&dbname='.get('dbname').'&key='.($key++));
        $data['key'] = $key+1;
        $data['result'] = $result;
        $this->view('api/history', $data);
      } else {
        echo 'SUCCESS';
      }
    }


    public function updateProduct() { // Clear all Value in column RemainingQTY table product because it's not used, we can find reamining qty on barcode_range
      $api = $this->model('api');
      $result = $api->cleanRemainingQtyProduct();
      echo $result==1 ? "<p>SUCCESS</p>":"FAIL";
      echo '<br><a href="index.php?route=clear">back</a>';
    }
  }