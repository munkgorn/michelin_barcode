<?php 
	class GroupController extends Controller {
        public function __construct() {
			if ($this->hasSession('id_user')==false) {
				$this->rmSession('id_user');
				$this->rmSession('username');
				$this->setSession('error', 'Please Login');
				$this->redirect('home');
			} 
		}
        public function index() {
            $data = array();

            $group = $this->model('group');

            $data['title'] = "Barcode Reception";
            $style = array('assets/home.css');
            $data['style'] 	= $style;

            $filter_date = (get('date')?get('date'):'');
            $data['filter_date'] = $filter_date;
            $filter_group = get('group');
            $data['filter_group'] = $filter_group;
            $filter_status = get('status');
            $data['filter_status'] = $filter_status;
            
            $data_select = array(
                'date' => $filter_date
            );
            
            $data['date_group'] = $group->getDateGroup();
            
             // group for filter
            $data['groups'] = array();
            $lists = $group->getGroups();
            foreach ($lists as $key => $value) {
                $data['groups'][] = $value['group_code'];
            }

            // data list 
            $data['lists'] = array(); 
            // if (method_post()) {
                $data['lists'] = $this->getLists();
            // }


            $url = '';
            $url .= !empty($filter_date) ? "&date=$filter_date" : '';
            $url .= !empty($filter_group) ? "&group=$filter_group" : '';
            $url .= !empty($filter_status) ? "&status=$filter_status" : '';
            $data['action'] = route('group');
            $data['action_checkbox']  = route('group/checkall', $url);
            $data['link_clear'] = route('group');
            $data['link_changestatus'] = route('group/change', $url);
            $data['link_del'] = route('group/delGroup', $url);
            $data['action_import_excel'] = '';
            // $data['export_excel'] = route('export/group&date='.$filter_date.'&group='.$filter_group.'&status='.$filter_status);
            $data['export_excel'] = route('export/group&date='.$filter_date.'&group='.$filter_group.'&status='.$filter_status);

            $data['success'] = $this->hasSession('success') ? $this->getSession('success'): ''; $this->rmSession('success');
            $data['error'] = $this->hasSession('error') ? $this->getSession('error'): ''; $this->rmSession('error');
            // $barcode = $this->model('barcode');
            //$data['list_group'] = $barcode->getListGroup($data_select);

            $data['textalert'] = $this->hasSession('textalert') ? $this->getSession('textalert') : false;
            $data['confirm_remove_barcode'] = route('barcode/confirm_remove'.(get('date')?'&date='.get('date'):''));
            
            $this->view('group/index',$data);
        }

        public function getLists() {
            $data = array();
            $group = $this->model('group');
            
            switch (get('status')) {
                case 'waiting' : $status = 0; break;
                case 'received' : $status = 1; break;
                default : $status = false; break;
            }
            // echo $status;
            $filter = array(
                'date_purchase' => !empty(get('date')) ? get('date') : '',
                'group_code' => get('group'),
                // 'barcode_use' => $status,
                'has_remainingqty' => true
            );
            if ($status!==false) {
                $filter['barcode_use'] = "$status";
            }
            $data = $group->getGroups($filter);
            return $data;
        }

        public function change() {
            $filter_date = (get('date')?get('date'):'');
            $data['filter_date'] = $filter_date;
            $filter_group = get('group');
            $data['filter_group'] = $filter_group;
            $filter_status = get('status');
            $data['filter_status'] = $filter_status;

			$id = get('id');
            $status = 1; // this id is `Receive` status
            
            $group = $this->model('group');
            $result = $group->changeStatus($id, $status);
            if ($result) {
                $this->setSession('success','Change status to receive success.');
            } else {
                $this->setSession('error','Cant change status something has wrong.');
            }
            $url = '';
            $url .= !empty($filter_date) ? "&date=$filter_date" : '';
            $url .= !empty($filter_group) ? "&group=$filter_group" : '';
            $url .= !empty($filter_status) ? "&status=$filter_status" : '';

            $this->redirect('group'.$url );
        }

        public function checkall() {

            if (!isset($_POST['checkbox'])||count($_POST['checkbox'])<=0) {
                $this->setSession('error','Not found checkbox');
                $this->redirect('group');
                exit();
            }

            $filter_date = (get('date')?get('date'):'');
            $data['filter_date'] = $filter_date;
            $filter_group = get('group');
            $data['filter_group'] = $filter_group;
            $filter_status = get('status');
            $data['filter_status'] = $filter_status;

            
            $success = array();
            $error = array();
            $checkbox = post('checkbox');
            foreach ($checkbox as $key => $value) {
                $id = $value;
                $status = 1; // this id is `Receive` status
                
                $group = $this->model('group');
                $result = $group->changeStatus($id, $status);


                if ($result) {
                    $success[] = $group->findCode($id);
                } else {
                    $error[] = $group->findCode($id);
                }
            }
            
            if (count($success)>0) {
                $this->setSession('success', 'Received group : '.implode(',',$success).' successful');
            }
            if (count($error)>0) {
                $this->setSession('error', 'Fail group : '.implode(',',$error).' ');
            }

            

			
            $url = '';
            $url .= !empty($filter_date) ? "&date=$filter_date" : '';
            $url .= !empty($filter_group) ? "&group=$filter_group" : '';
            $url .= !empty($filter_status) ? "&status=$filter_status" : '';

            $this->redirect('group'.$url );
        }

        public function delGroup() {

            $filter_date = (get('date')?get('date'):'');
            $data['filter_date'] = $filter_date;
            $filter_group = get('group');
            $data['filter_group'] = $filter_group;
            $filter_status = get('status');
            $data['filter_status'] = $filter_status;

            $group = $this->model('group');
            $group->delGroup(get('id'));

            $url = '';
            $url .= !empty($filter_date) ? "&date=$filter_date" : '';
            $url .= !empty($filter_group) ? "&group=$filter_group" : '';
            $url .= !empty($filter_status) ? "&status=$filter_status" : '';

            $this->redirect('group'.$url );
        }
        
    }
?>