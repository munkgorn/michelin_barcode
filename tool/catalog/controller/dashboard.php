<?php 
	class DashboardController extends Controller {
	    public function index() {
	    	$data = array();
	    	$dashboard = $this->model('dashboard');
	    	$shop = $this->model('shop');
	    	$girl = $this->model('girl');
	    	$calendar = $this->model('calendar');
	    	$data['dashboard_data'] = $dashboard->getTotalGirls();
	    	$time = time();
			$time_between = getBetweenDate($time);

			$time_yesterday = strtotime("-1 day",strtotime(date('Y-m-d')));
			$time_between_yesterday = getBetweenDateYeaterday($time_yesterday);
			// var_dump($time_between);exit();
			$data_get_working = array(
				'date_start' => $time_between['date_start'],
				'date_end' => $time_between['date_end']
			);
			$data_get_working_yesterday = array(
				'date_start' => $time_between_yesterday['date_start'],
				'date_end' => $time_between_yesterday['date_end']
			);

	    	$shop_all = $shop->getShop();
	    	$shop_girls = array();
	    	foreach($shop_all as $val){
	    		$data['dashboard_data']['girl_work_by_shop'][$val['shop_id']] = $girl->getCountGirlWoringByshop($val['shop_id'],$data_get_working);
	    	}
	    	$data['dashboard_data']['girls_register'] = $girl->getGirlsRegister($data_get_working);
	    	$data['list_shop'] = $shop->getShop();
	    	// $data['dashboard_data']['leave'] = 0;
	    	$data['dashboard_data']['dh'] = (int)$girl->getDH($data_get_working);
	    	$data['dashboard_data']['ot'] = (int)$girl->getOT($data_get_working);
	    	$data['dashboard_data']['mama'] = (int)$girl->getMamaWork($data_get_working);
	    	$data['dashboard_data']['emp'] = (int)$girl->getEmpWork($data_get_working);
	    	$data['dashboard_data']['guide'] = (int)$girl->getGuideWork($data_get_working);
	    	$data['dashboard_data']['back'] = (int)$girl->getGirlsBack($data_get_working);
	    	$data['dashboard_data']['total_girls'] = (int)$girl->getTotalGirlOnly($data_get_working);
	    	$data['all_data'] = $girl->getSelectData($data_get_working_yesterday);

	    	// var_dump($data['all_data']);
	    	// exit();

 	    	$data['title'] = "Dashboard";

 	    	$ranking = $this->model('ranking');
 	    	$reservation = $this->model('reservation');
 	    	$data_select = array(
 	    		'date_start' => date('Y-m-1'),
 	    		'date_end' => date('Y-m-t')
 	    	);
 	    	$data['ranking_work_in'] = $ranking->list_rank_work_in($data_select);
 	    	$data['ranking_pay_bar'] = $ranking->list_rank_paybar($data_select);
 	    	$data['ranking_drink'] = $ranking->list_rank_drink($data_select);
 	    	$data['avgLastToToday'] = $ranking->avgLastToToday($data_select);
 	    	$data_reserv = array(
 	    		'end' => 15,
 	    		'date' => date('Y-m-d')
 	    	);
 	    	$data['list_reservation'] = $reservation->listRervation($data_reserv);
 	    	$data['calendar'] = $calendar->show();
 	    	$this->view('dashboard/dashboard',$data);
	    }
	    public function graph(){
	    	$method = get('method');
	    	$result = array();
	    	
	    	// foreach($list_shop as $val){

	    	// }
	    	// echo get('shop_id');
	    	$data_select = array(
 	    		'date_start' => (get('date_start')?get('date_start'):date('Y-m-01')),
 	    		'date_end' => (get('date_end')?get('date_end'):date('Y-m-d')),
 	    		'shop_id' => (get('shop_id')?get('shop_id'):''),
 	    	);
	    	$ranking = $this->model('ranking');
	    	$result = $ranking->graph_work_in($data_select);
	    	// $result[] = array(
    		// 	''
    		// );
	    	// $result['']
	    	$this->json($result);
	    }
	    public function getGraphWeek(){
	    	$method = get('method');
	    	$result = array();
	    	
	    	// foreach($list_shop as $val){

	    	// }
	    	// echo get('shop_id');
	    	$data_select = array(
 	    		// 'date_start' => (get('date_start')?get('date_start'):date('Y-m-1')),
 	    		// 'date_end' => (get('date_end')?get('date_end'):date('Y-m-d')),
 	    		'shop_id' => (get('shop_id')?get('shop_id'):''),
 	    	);
	    	$ranking = $this->model('ranking');
	    	$result = $ranking->getGraphWeek($data_select);
	    	// $result[] = array(
    		// 	''
    		// );
	    	// $result['']
	    	$this->json($result);
	    }
	    public function getGraphDetailMonth(){
	    	$method = get('method');
	    	$result = array();
	    	$data_select = array(
 	    		// 'date_start' => (get('date_start')?get('date_start'):date('Y-m-1')),
 	    		// 'date_end' => (get('date_end')?get('date_end'):date('Y-m-d')),
 	    		'shop_id' => (get('shop_id')?get('shop_id'):''),
 	    	);
	    	$ranking = $this->model('ranking');
	    	$result = $ranking->getGraphDetailMonth($data_select);

	    	$this->json($result);
	    }
	    public function getGraphCustomerYear(){
	    	$method = get('method');
	    	$result = array();
	    	$data_select = array(
 	    		'date_start' => (get('date_start')?get('date_start'):date('Y-m-01')),
 	    		'date_end' => (get('date_end')?get('date_end'):date('Y-m-d')),
 	    		'shop_id' => (get('shop_id')?get('shop_id'):''),
 	    	);
	    	$ranking = $this->model('ranking');
	    	$result = $ranking->getGraphCustomerYear($data_select);
	    	
	    	$this->json($result);
	    }
	    public function getGraphCustomerFoodDrink(){
	    	$method = get('method');
	    	$result = array();
	    	$data_select = array(
 	    		'date_start' => (get('date_start')?get('date_start'):date('Y-m-01')),
 	    		'date_end' => (get('date_end')?get('date_end'):date('Y-m-d')),
 	    		'shop_id' => (get('shop_id')?get('shop_id'):''),
 	    	);
	    	$ranking = $this->model('ranking');
	    	$result = $ranking->getGraphCustomerFoodDrink($data_select);
	    	
	    	$this->json($result);
	    }
	    public function getGraphCustomerYearly(){
	    	$method = get('method');
	    	$result = array();
	    	$data_select = array(
 	    		'date_start' => (get('date_start')?get('date_start'):date('Y-m-01')),
 	    		'date_end' => (get('date_end')?get('date_end'):date('Y-m-d')),
 	    		'shop_id' => (get('shop_id')?get('shop_id'):''),
 	    	);
	    	$ranking = $this->model('ranking');
	    	$result = $ranking->getGraphCustomerYearly($data_select);
	    	
	    	$this->json($result);
	    }
	    public function getGraphCustomerVisit(){
	    	$method = get('method');
	    	$result = array();
	    	$data_select = array(
 	    		'date_start' => (get('date_start')?get('date_start'):date('Y-m-01')),
 	    		'date_end' => (get('date_end')?get('date_end'):date('Y-m-d')),
 	    		'shop_id' => (get('shop_id')?get('shop_id'):''),
 	    	);
	    	$ranking = $this->model('ranking');
	    	$result = $ranking->getGraphCustomerVisit($data_select);
	    	
	    	$this->json($result);
	    }
	}
?>