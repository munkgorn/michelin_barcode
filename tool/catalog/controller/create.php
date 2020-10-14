<?php 
	class CreateController extends Controller {
	    public function indexModel() {
	    	$data = array();
	    	$model = $this->model('model');
	    	$data['folder'] = $model->findPath();
	    	$data['table'] = $model->listTableDB();
	    	$this->view('create/indexModel',$data);
	    }
	    public function submitAddModel(){
	    	$data = array();
	    	$model = $this->model('model');
	    	$data_insert = array(
	    		'folder' 		=> post('folder'),
	    		'database_name' => post('database_name'),
	    		'model_name' 	=> post('model_name'),
	    		'chkAdd'		=> post('chkAdd'),
				'chkEdit'		=> post('chkEdit'),
				'chkDelete'		=> post('chkDelete'),
				'getList'		=> post('getList'),
				'getLists'		=> post('getLists')
	    	);
	    	$result = $model->addModel($data_insert);
	    	$this->json($result);
	    }
	    public function submitAddController(){
	    	$data = array();
	    	$model = $this->model('model');
	    	$data_insert = array(
	    		'controller_name' 	=> post('controller_name'),
	    		'model_name' 		=> post('model_name'),
	    		'chkView' 			=> post('chkView'),
	    		'chkSelect2' 		=> post('chkSelect2'),
	    	);
	    	$result = $model->addModel($data_insert);
	    	$this->json($result);
	    }
	    public function genarateModelName(){
	    	$database_name = $_GET['database_name'];
	    	$resul_explore = explode('_',$database_name);
	    	$sum_text = '';
	    	$i=0;
	    	foreach($resul_explore as $val){
	    		$txt_new = $val;
	    		if($i>0){
	    			$txt_new = ucfirst($val);
	    		}
	    		$i++;
	    		$sum_text .= $txt_new;
	    	}
	    	$this->json($sum_text);
	    }
	    public function indexController() {
	    	$data = array();
	    	$this->view('create/indexController',$data);
	    }
	}
?>