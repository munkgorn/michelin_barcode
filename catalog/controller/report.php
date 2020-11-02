<?php 
	class ReportController extends Controller {
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
	    
			$data['title'] = "Report remaining stock barcode";
			$style = array(
				'assets/home.css'
			);
			$data['style'] 	= $style;

			
			// $data['barcodes'] = $this->calcurateBarcode();

			$data['action'] = '';
			$data['export_excel'] = route('export/report');
			
			$data['success'] = $this->hasSession('success') ? $this->getSession('success') : ''; $this->rmSession('success');
			$data['error'] = $this->hasSession('error') ? $this->getSession('error') : ''; $this->rmSession('error');

			$this->view('report/index',$data);
		}
		
		public function calcurateBarcode() {
			$input=array();
			$barcode = $this->model('barcode');

			$list1 = array();
			$list2 = array();

			$listbarcode = $barcode->getListBarcode(); // ? ที่จองในระบบ
			foreach ($listbarcode as $key => $value) {
				$list1[] = (int)$value['barcode_code'];
			}


			// $listimport = $barcode->getListImportBarcode($input); // ? ที่ Import เข้ามา
			$filter = array('barcode_status'=>1);
			$listbarcode2 = $barcode->getListBarcode($filter); // ? ที่ใช้ไปแล้ว 
			foreach ($listbarcode2 as $key => $value) {
				$list2[] = (int)$value['barcode_code'];
			}

			// ? get default alert
			$config = $this->model('config');
			$default_number_maximum_alert = $config->getConfig('config_maximum_alert'); // ? ค่าที่ตั้งไว้ว่าเกินเท่าไหร่ให้ alert
			return $this->calcurateDiffernce($list1, $list2, $default_number_maximum_alert);
		}

		public function calcurateDiffernce($list1, $list2, $default_number_maximum_alert) {
			sort($list1);
			sort($list2);
			$arr_diff = array_diff($list1, $list2); // ? ได้อาเรย์ ส่วนต่างที่ไม่เหมือนกัน
			$list_notfound = array_values($arr_diff); // ? reset key array

			$count = 0;
			$first = '';
			$end = '';
			$save = array();
			$group = '';
			foreach ($list_notfound as $key => $value) {
				if (isset($list_notfound[$key+1]) && $list_notfound[$key+1] == $value+1) { // ? ในกรณีที่ คียอันถัดไป เท่า ค่า+1 แสดงว่า ส่วนต่างที่ไม่มีนี้กำลังเรียง
					if (empty($first)) {
						$save = array();
						$first = sprintf('%08d',$value);
						if (strlen($value)==8) {
							$group = substr($value, 0, 3);
						} else if (strlen($value)==7) {
							$group = sprintf('%03d', substr($value, 0, 2));
						} else if (strlen($value)==6) {
							$group = sprintf('%03d', substr($value, 0, 1));
						}
					}
					$count++; // ? เริ่มนับจำนวนส่วนต่าง
				} else {
					if (empty($end)) {
						$end = sprintf('%08d',$value);
						$save[] = sprintf('%08d',$value);
					}
					$diff[] = array(
						'name' => "$first - $end",
						'group' => $group,
						// 'barcodes' => $save, 
						'count' => $count + 1 //  ? จำนวนระยะห่างที่หายไป +1 นับตัวแรกด้วย
					);
					$first = '';
					$end = '';
					$count = 0;
				}
				if ($count>0) {
					$save[] = $value;
				}
			}

			$text = array();
			foreach ($diff as $key => $value) {
				if ($value['count'] >= $default_number_maximum_alert) {
					$text[] = $value;
				}
			}

			return $text;
		}
    }

?>