<?php 
	class MasterModel extends db {
		public function getOil($data=array()){
			$result = array();
			$sql = "SELECT * FROM oil_date WHERE DATE_FORMAT(update_date,'%Y-%m-%d') = '".$this->escape($data['date'])."'";
			$result = $this->query($sql)->row;
			return $result;
		}
		public function InsertOil($data=array()){
			$result = array();
			$data_date = array(
				'update_date' => $data['update_date'],
          	  	'remark'      => $data['remark']
			);
			$result_date = $this->insert('date',$data_date);
			foreach($data['items'] as $val){
				$data_detail = array(
					'id_oil'	=> $result_date,
					'name'     	=> $val['name'],
	            	'today'    	=> $val['today'],
	            	'tomorrow' 	=> $val['tomorrow'],
	             	'yesterday' => $val['yesterday'],
	            	'unit_th'  	=> $val['unit_th']
				);
				$result_detail = $this->insert('detail',$data_detail);
			}
			return $result;
		}
	}
?>