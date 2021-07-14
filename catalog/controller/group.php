<?php
class GroupController extends Controller
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

  $group = $this->model('group');

  $data['title'] = "Barcode Reception";
  $style         = array('assets/home.css');
  $data['style'] = $style;

  $filter_date           = (get('date') ? get('date') : '');
  $data['filter_date']   = $filter_date;
  $filter_group          = get('group');
  $data['filter_group']  = $filter_group;
  $filter_status         = get('status');
  $data['filter_status'] = $filter_status;

  $data_select = array(
   'date' => $filter_date,
  );
//   $data['date_group'] = $group->getDateGroup();
  $history            = $this->model('history');
  $data['date_group'] = $history->groupDateHistory();

  // group for filter
  $data['groups'] = $history->groupGroup();
//   $lists          = $group->getGroups();
  //   foreach ($lists as $key => $value) {
  //    $data['groups'][] = $value['group_code'];
  //   }

  // data list
  $data['lists'] = array();
  // if (method_post()) {
  $data['lists'] = $this->getLists();
  // }

  $url = '';
  $url .= !empty($filter_date) ? "&date=$filter_date" : '';
  $url .= !empty($filter_group) ? "&group=$filter_group" : '';
  $url .= !empty($filter_status) ? "&status=$filter_status" : '';
  $data['action']              = route('group');
  $data['action_checkbox']     = route('group/checkall', $url);
  $data['link_clear']          = route('group');
  $data['link_changestatus']   = route('group/change', $url);
  $data['link_del']            = route('group/delGroup', $url);
  $data['action_import_excel'] = '';
  // $data['export_excel'] = route('export/group&date='.$filter_date.'&group='.$filter_group.'&status='.$filter_status);
  $data['export_excel'] = route('export/group&date=' . $filter_date . '&group=' . $filter_group . '&status=' . $filter_status);

  $data['success'] = $this->hasSession('success') ? $this->getSession('success') : '';
  $this->rmSession('success');
  $data['error'] = $this->hasSession('error') ? $this->getSession('error') : '';
  $this->rmSession('error');
  // $barcode = $this->model('barcode');
  //$data['list_group'] = $barcode->getListGroup($data_select);

  $data['textalert']              = $this->hasSession('textalert') ? $this->getSession('textalert') : false;
  $data['confirm_remove_barcode'] = route('barcode/confirm_remove' . (get('date') ? '&date=' . get('date') : ''));

  $this->view('group/index', $data);
 }

 public function getLists()
 {
  $data  = array();
  $group = $this->model('group');

  switch (get('status')) {
   case 'waiting':$status = 0;
    break;
   case 'received':$status = 1;
    break;
   default:$status = false;
    break;
  }
//   // echo $status;
  //   $filter = array(
  //    'date_purchase'    => !empty(get('date')) ? get('date') : '',
  //    'group_code'       => get('group'),
  //    // 'barcode_use' => $status,
  //     'has_remainingqty' => true,
  //   );
  //   if ($status !== false) {
  //    $filter['barcode_use'] = "$status";
  //   }
  //   $data = $group->getGroups($filter);
  $history = $this->model('history');
  $filter  = array();
  if (!empty($_GET['date'])) {$filter['date_purchase'] = trim($_GET['date']);}
  if (!empty($_GET['group'])) {$filter['id_group'] = trim($_GET['group']);}
  if ($status !== false) {$filter['barcode_use'] = (int)$status;}
  $data = $history->getHistories($filter);

  if ($data != false && count($data) > 0) {
   foreach ($data as $key => $value) {
    if (!empty($value['id_group'])) {$data[$key]['group_code'] = $group->findCode($value['id_group']);}
    if (!empty($value['id_user'])) {
     $user_info              = $this->model('user')->findUser($value['id_user']);
     $data[$key]['username'] = $user_info['username'];
    }
   }
  }

  return $data;
 }

 public function change()
 {
  $filter_date           = (get('date') ? get('date') : '');
  $data['filter_date']   = $filter_date;
  $filter_group          = get('group');
  $data['filter_group']  = $filter_group;
  $filter_status         = get('status');
  $data['filter_status'] = $filter_status;

  $id      = get('id');
  $idgroup = get('idgroup');
  $status  = 1; // this id is `Receive` status

  $group   = $this->model('group');
  $history = $this->model('history');
//   $result  = $group->changeStatus($id, $idgroup, $status);
  $result = $group->changeStatus($idgroup, $status);
  if ($result) {
   //   $barcode = $this->model('barcode');
   $filter = array(
    'id_group'    => $idgroup,
    'barcode_use' => 0,
   );
   $historyInfo = $history->getHistories($filter);
   if ($historyInfo != false && count($historyInfo) >= 1) {
    $historyInfo = $historyInfo[0];
    $history->editHistory($historyInfo['id_history'], array('barcode_use' => 1, 'date_received' => date('Y-m-d H:i:s')));
   }
   //   $barcode->editHistory();
   $this->setSession('success', 'Change status to receive success.');
  } else {
   $this->setSession('error', 'Cant change status something has wrong.');
  }
  $url = '';
  $url .= !empty($filter_date) ? "&date=$filter_date" : '';
  $url .= !empty($filter_group) ? "&group=$filter_group" : '';
  $url .= !empty($filter_status) ? "&status=$filter_status" : '';

  $group_info = $group->getGroup($idgroup);

  // $this->redirect('group'.$url );
  $this->setSession('redirect', 'group');
  $this->model('config')->getConfig('load_freegroup', 1);
  $this->redirect('loading/rangeall&round=1&status=1&flag=0&group=' . $group_info['id_group'] . '&max=' . $group_info['id_group'] . '&redirect=loading');
 }

 public function checkall()
 {

  if (!isset($_POST['checkbox']) || count($_POST['checkbox']) <= 0) {
   $this->setSession('error', 'Not found checkbox');
   $this->redirect('group');
   exit();
  }

  $filter_date           = (get('date') ? get('date') : '');
  $data['filter_date']   = $filter_date;
  $filter_group          = get('group');
  $data['filter_group']  = $filter_group;
  $filter_status         = get('status');
  $data['filter_status'] = $filter_status;

  $success    = array();
  $error      = array();
  $checkbox   = post('checkbox');
  $link_group = array();
  $history    = $this->model('history');
  foreach ($checkbox as $key => $value) {
   $id     = $value;
   $status = 1; // this id is `Receive` status

   $group      = $this->model('group');
   $group_info = $group->getGroup($id);
   if (!in_array($group_info['id_group'], $link_group)) {
    $link_group[] = $group_info['id_group'];
   }

   $result = $group->changeStatus($id, $status);

   if ($result) {
    $filter = array(
     'id_group'    => $id,
     'barcode_use' => 0,
    );

    $historyInfo = $history->getHistories($filter);

    if ($historyInfo != false && count($historyInfo) >= 1) {
     $historyInfo = $historyInfo[0];
     $history->editHistory($historyInfo['id_history'], array('barcode_use' => 1, 'date_received' => date('Y-m-d H:i:s')) );
    }
    $success[] = $group->findCode($id);
   } else {
    $error[] = $group->findCode($id);
   }
  }

  if (count($success) > 0) {
   $this->setSession('success', 'Received group : ' . implode(',', $success) . ' successful');
  }
  if (count($error) > 0) {
   $this->setSession('error', 'Fail group : ' . implode(',', $error) . ' ');
  }

  $url = '';
  $url .= !empty($filter_date) ? "&date=$filter_date" : '';
  $url .= !empty($filter_group) ? "&group=$filter_group" : '';
  $url .= !empty($filter_status) ? "&status=$filter_status" : '';

  // $this->redirect('group'.$url );
  $this->setSession('redirect', 'group');
  $this->model('config')->setConfig('load_freegroup', 1);
  $this->redirect('loading/rangeall&round=1&status=1&flag=0&group=' . $link_group[0] . '&max=' . $link_group[count($link_group) - 1] . '&redirect=loading');
 }

 public function delGroup()
 {

    $filter_date           = (get('date') ? get('date') : '');
    $data['filter_date']   = $filter_date;
    $filter_group          = get('group');
    $data['filter_group']  = $filter_group;
    $filter_status         = get('status');
    $data['filter_status'] = $filter_status;

    $group = $this->model('group');
    $group->delGroup($_GET['idgroup']);

    $history = $this->model('history');
    $history->delHistory($_GET['id']);

    $url = '';
    $url .= !empty($filter_date) ? "&date=$filter_date" : '';
    $url .= !empty($filter_group) ? "&group=$filter_group" : '';
    $url .= !empty($filter_status) ? "&status=$filter_status" : '';

    $this->redirect('group' . $url);
 }

 public function updateDefaultStart()
 {
  $group = $this->model('group');
  echo $group->updateDefaultStart();
 }
 public function addDefaultGroup()
 {
  $group = $this->model('group');
  echo $group->addDefaultGroup();
 }

}
