<?php  
/**
 * Controller [ReplaceController]
 */
class [ReplaceController]Controller extends Controller
{

	public function index() 
	{
		$data = array();

		if($this->hasSession('success')) {
			$data['success'] = $this->getSession('success');
			$this->rmSession('success');
		}
		if($this->hasSession('error')) {
			$data['error'] = $this->getSession('error');
			$this->rmSession('error');
		}

		$filter = array();
		$[mgs] = $this->model('[mgs]');
		$data['results'] = $[mgs]->getLists($filter);

		$data['link_add'] = route('[mgs]/add');

		$this->view('[mgs]/list', $data);
	}

	public function add()
	{
		$data = array();
		$data['result'] = array();

		$[mgs] = $this->model('[mgs]');
		if (method_post()) {
			$result = $[mgs]->add($_POST);
			if ($result>0) {
				$this->setSession('success', 'Add Success');
			} else {
				$this->setSession('error', 'Fail Add');
			}
			redirect('[mgs]');
			exit();	
		}

		$data['action'] = route('[mgs]/add');
		
		$this->view('[mgs]/form', $data);		
	}

	public function edit()
	{
		$data = array();

		$id = get('id');
		$[mgs] = $this->model('[mgs]');

		if (method_post()) {
			$result = $[mgs]->edit($_POST, $id);
			if ($result>0) {
				$this->setSession('success', 'Edit Success');
			} else {
				$this->setSession('error', 'Fail Edit');
			}
			redirect('[mgs]');
			exit();	
		}

		$data['result'] = $[mgs]->getList($id);
		$data['action'] = route('[mgs]/edit');
		
		$this->view('[mgs]/form', $data);	
	}

	public function del() 
	{
		$id = get('id');
		$[mgs] = $this->model('[mgs]');
		$result = $[mgs]->del($id);
		if ($result==1) {
			$this->setSession('success', 'Del Success');
		} else {
			$this->setSession('error', 'Fail Del');
		}
		redirect('[mgs]');
		exit();
	}
}
?>