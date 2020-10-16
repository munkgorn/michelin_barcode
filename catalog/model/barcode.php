<?php 
	class BarcodeModel extends db {
		public function getBarcode($data='') {
			if (!empty($data['date'])) {
				$this->where('b.date_modify', $data['date'].'%', 'LIKE');
			}
			$this->where('b.barcode_status', 1);
			$this->join('user u','u.id_user=b.id_user','LEFT');
			$this->select('b.barcode_prefix, b.barcode_code, b.date_modify as date_added, u.username');
			$query = $this->get('barcode b');
			return $query->rows;
		}
		public function addRowBarcode($data=array()){
			$result = array();
			$array_insert = array(
				'size_product_code' => $data['size_product_code'],
				'sum_product' => $data['sum_product'],
				'date_wk'	=> $data['date_wk']
			);
			$result_insert = $this->insert('product',$array_insert);
			return $result_insert;
		}
		public function deleteGroup($data = array()){
			$result = array();
			$id_group = (int)$data['id_group'];

			// $sql = "DELETE FROM ".PREFIX."group WHERE id_group = '".$id_group."'";
			// $result_delete = $this->query($sql);
			$this->where('id_group', $id_group);
			$this->update('group', array('del'=>1));

			// $sql = "DELETE FROM ".PREFIX."barcode WHERE id_group = '".$id_group."'";
			// $result_delete = $this->query($sql);
			$this->where('id_group', $id_group);
			$this->update('barcode', array('del'=>1));

			$result = array(
				'result' => 'success'
			);
			return $result;
		}
		public function getListImportBarcode($date = array()) {
			$sql = "SELECT * FROM ".PREFIX."import_barcode WHERE id>0 ";
			if (isset($data['date_wk'])&&!empty($data['date_wk'])) {
				$sql .= "AND data_wk LIKE '".$data['date_wk']."%'";
			}
			$query = $this->query($sql);
			return $query->rows;
		}
		public function getListBarcode($data = array()){
			$result = array();
			$date = isset($data['date'])&&!empty($data['date']) ? $data['date'] : '';
			$date_wk = isset($date['date_wk'])&&!empty($data['date_wk']) ? $data['date_wk'] : '';

			$sql = "SELECT *,".PREFIX."barcode.date_added AS date_added 
			FROM ".PREFIX."barcode 
			LEFT JOIN ".PREFIX."user ON ".PREFIX."barcode.id_user = ".PREFIX."user.id_user
			WHERE id_barcode > 0 ";
			$sql .= !empty($date) ? "AND DATE(".PREFIX."barcode.date_added) = '".$date."' " : ""; 
			$sql .= !empty($date_wk) ? "AND DATE(".PREFIX."barcode.date_wk) = '".$date_wk."' " : ""; 
			$result_group = $this->query($sql);
			$result = $result_group->rows;
			return $result;
		}
		public function getListGroup($data = array()){
			$result = array();
			$date = $data['date'];

			/*
			$sql = "SELECT *,".PREFIX."group.date_added AS date_added,".PREFIX."group.id_group
			FROM ".PREFIX."group 
			LEFT JOIN ".PREFIX."user ON ".PREFIX."group.id_user = ".PREFIX."user.id_user
			WHERE DATE(".PREFIX."group.date_added) = '".$date."' ORDER BY ABS(".PREFIX."group.group_code) ASC "; 
			*/
			$sql = "SELECT ";
			$sql .= "g.id_group, ";
			$sql .= "g.group_code, ";
			$sql .= "(SELECT b.barcode_code FROM mb_master_barcode b WHERE b.barcode_prefix=g.group_code ORDER BY b.barcode_code ASC LIMIT 0,1) AS `start`, ";
			$sql .= "(SELECT b.barcode_code FROM mb_master_barcode b WHERE b.barcode_prefix=g.group_code ORDER BY b.barcode_code DESC LIMIT 0,1) AS `end`, ";
			$sql .= "g.remaining_qty, ";
			$sql .= "g.date_added, ";
			$sql .= "u.username, ";
			$sql .= "g.barcode_use ";
			$sql .= "FROM ".PREFIX."group g ";
			$sql .= "LEFT JOIN ".PREFIX."user u ";
			$sql .= "ON u.id_user=g.id_user ";
			$sql .= "WHERE ";
			$sql .= "DATE(g.date_added)='".$date."' ";
			$sql .= "AND g.del=0 ";
			$sql .= "ORDER BY ABS(g.group_code) ASC ";
			$result_group = $this->query($sql);
			$result = $result_group->rows;
			return $result;
		}
		public function import_barcode($path, $date_wk){
			$result = array();
			
			$sql = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE ".PREFIX."barcode FIELDS TERMINATED BY ',' 
			LINES TERMINATED BY '\n' IGNORE 1 ROWS ( id_user,barcode_prefix,barcode_code,date_added,date_modify);";
			$result = $this->query($sql);

			$date_now = date('Y-m-d H:i:s');
			$this->where('date_added', '0000-00-00 00:00:00');
			$this->update('barcode', array('date_added'=>$date_now,'date_modify'=>$date_now,'date_wk'=>$date_wk));

			// return $result;
			return $date_now;
		}
		public function import_product($path){
			$result = array();
			
			$sql = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE ".PREFIX."product FIELDS TERMINATED BY ',' 
			LINES TERMINATED BY '\n' IGNORE 1 ROWS ( id_user,size_product_code,sum_product,date_wk,date_added,date_modify);";
			$result = $this->query($sql);

			$date_now = date('Y-m-d H:i:s');
			$this->where('date_added', '0000-00-00 00:00:00');
			$this->update('product', array('date_wk'=>$date_now,'date_added'=>$date_now,'date_modify'=>$date_now));

			// return $result;
			return $date_now;
		}
		public function updateBarcodeUse($barcodes=array()) {
			$sql = "UPDATE mb_master_barcode SET barcode_status = 1 WHERE barcode_code IN (".implode(',', $barcodes).")";
			$result = $this->query($sql);
			return $result;
		}
		public function import_range_barcode($path, $date_wk){
			$result = array();
			// $full_name = $data['full_name'];
			// $date = $data['date'];

			// $sql = "LOAD DATA LOCAL INFILE '" . $full_name . "' INTO TABLE ".PREFIX."group FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS ( id_user,group_code,start,end,remaining_qty);";
			$sql = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE ".PREFIX."import_barcode FIELDS TERMINATED BY ',' 
			LINES TERMINATED BY '\n' IGNORE 1 ROWS (`id_user`,`barcode`,`date_wk`,`date_added`,`date_modify`);";
			$this->query($sql);
			$date_now = date('Y-m-d H:i:s');
			$sql = "UPDATE ".PREFIX."import_barcode SET date_wk = '".$date_wk." 00:00:00', date_added = '".$date_now."', date_modify = '".$date_now."' WHERE date_added = '0000-00-00 00:00:00' AND date_modify = '0000-00-00 00:00:00'; ";
			$this->query($sql);
			// $sql_update_date = "UPDATE ".PREFIX."group SET 
			// date_wk = '".$date."',
			// date_added = '".$date."',
			// date_modify = '".$date."' 
			//  WHERE date_added = '0000-00-00 00:00:00' OR date_added IS NULL";
			// $this->query($sql_update_date);
			$result = array(
				'result' => 'success'
			);
			return $result;
		}
		public function updateFlagBarcode($barcode=array()) {
			$sql = "UPDATE ".PREFIX."barcode SET barcode_flag = 1, barcode_status = 1 WHERE barcode_code IN (".implode(',', $barcode).")";
			return $this->query($sql);
			
		}
		public function updateGroupCreateBarcode($data=array()){
			$result = array();
			$data_insert_barcode = array();
			foreach($data['qty'] as $group_code => $val){

				// check in group this day
				$this->where('group_code', $group_code);
				// $this->where('date_added', date('Y-m-d').'%', 'LIKE');
				$this->where('del', '0');
				$query = $this->get('group');

				// มีค่าเดิม ของวันนี้อยู่แล้ว ให้อัพเดท
				if ($query->num_rows==1) {  
					$oldgroup = $query->row;
					
					$this->where('id_group', $oldgroup['id_group']);
					$this->where('del', '0');
					$update = array(
						'id_user' => isset($data['user']) ? $data['user'] : 0,
						'remaining_qty' => $val,
						'start' => (int)$oldgroup['start']+(int)$val,
						'date_modify' => date('Y-m-d H:i:s'),
						'barcode_use' => "0"
					);
					$this->update('group', $update);

					$id_group = $oldgroup['id_group'];
					$start = $oldgroup['start'];
				}
				// ไม่เคยมีค่า ของวันนี้
				else {
					// หาค่า default barcode
					$this->where('`group`', $group_code);
					$query_configbarcode = $this->get('config_barcode');
					$config_barcode = $query_configbarcode->row;

					// หายอดคงเหลือ ของ วันก่อน
					// $this->group_by('date_added');
					// $this->order_by('date_added','DESC');
					// $this->where('group_code', $group_code);
					// $this->limit(1,1);
					// $this->select('*');
					// $findold = $this->get('group');
					$findold = $this->query("SELECT * FROM ".PREFIX."group WHERE group_code = '$group_code' GROUP BY date_added ORDER BY date_added DESC;");
					

					if ($findold->num_rows==1) {
						$old_group = $findold->row;
						$insert = array(
							'id_user' => isset($data['user']) ? $data['user'] : 0,
							'group_code' => $group_code,
							'start' => 0,
							'end' => 0,
							'remaining_qty' => null,
							'default_start' => $config_barcode['start'],
							'default_end' => $config_barcode['end'],
							'default_range' => $config_barcode['total'],
							'barcode_use' => 0,
							'config_remaining' => $config_barcode['remaining'], // ค่าคงเหลือเฉยๆ เก็บไว้ในระบบดูว่า ใช้ไปเท่าไหร่
							'date_added' => date('Y-m-d H:i:s'),
							'date_modify' => date('Y-m-d H:i:s')
						);
						$insert['start'] = $old_group['start']; // เลขล่าสุด จากครั้งที่แล้ว ไม่สนใจว่า barcode นั้น confirm การใช้ไปรึยัง
						$insert['config_remaining'] = $old_group['config_remaining'] - $old_group['remaining_qty']; // เอาเลข 

						$id_group = $this->insert('group', $insert);
						$start = $insert['start'];
					}

				}

				// $start = 0;
				// $end = 0;
				// $qty = $val;
				// $id_user = $data['id_user'];
				// // $group_code = $data['group_code'];
				// $date_now = date('Y-m-d H:i:s');
				// $id_group = 0;
				// $old_qty = 0;

				// $result_get_start_end = $this->query("SELECT * FROM ".PREFIX."group WHERE group_code = '".$group_code."'");
				// if($result_get_start_end->num_rows>0){
				// 	$start 	= $result_get_start_end->row['start'];
				// 	$end 	= $result_get_start_end->row['end'];
				// 	$id_group = $result_get_start_end->row['id_group'];
				// 	$old_qty = $result_get_start_end->row['remaining_qty'];
				// }
				// $updatestart = (int)$start+(int)$qty;
				// $updateend 	= 0;


				// $data_update_group_code = array(
				// 	'remaining_qty' => 	(int)$val + (int)$old_qty,
				// 	'start'			=> 	$updatestart,
				// 	'end'			=>	$updateend,
				// 	'date_added' => $date_now
				// );
				// $result_update = $this->update('group',$data_update_group_code,"group_code = '".$group_code."'");

				$date_now = date('Y-m-d H:i:s');
				$qty = $val;
				if (isset($start)&&isset($id_group)) {
					for($i=$start;$i<=((int)$start+(int)$qty-1);$i++){
						$data_insert_barcode[] = array(
							'id_user' => $data['id_user'],
							'id_group'=> $id_group,
							'barcode_prefix' => $group_code,
							'barcode_code'	=> $i,
							'barcode_status' => 0,
							'barcode_flag'	=>	0,
							'date_added' => $date_now,
							'date_modify' => $date_now
						);
						// $result_insert_barcode = $this->insert('barcode',$data_insert_barcode);
					}
				}
				
			}

			// save to csv and insert barcode 
			$path_file = DOCUMENT_ROOT.'uploads/import_barcode_csv/';
			$file_name = date('YmdHis').'.csv';
			$full_name = $path_file.$file_name;

			$fp = fopen($full_name, 'w');
			foreach ($data_insert_barcode as $fields) {
			    fputcsv($fp, $fields);
			}
			fclose($fp);

			$sql = "LOAD DATA LOCAL INFILE '" . $full_name . "' INTO TABLE ".PREFIX."barcode FIELDS TERMINATED BY ',' 
			LINES TERMINATED BY '\n'  ( id_user,id_group,barcode_prefix,barcode_code,barcode_status,barcode_flag,date_added,date_modify);";
			$this->query($sql);
			$date_now = date('Y-m-d H:i:s');
			$sql_update_date = "UPDATE ".PREFIX."barcode SET date_added = '".$date_now."', date_modify = '".$date_now."' WHERE date_added = '0000-00-00 00:00:00'";
			$this->query($sql_update_date);
			$result = array(
				'result' => 'success'
			);
			return $result;
		}
		public function updateDefaultGroup($data = array()){
			$result = false;
			$type = $data['type'];
			if (in_array($data['type'], array('default_start','default_end','default_range'))) {
				$this->where('id_group',$data['id_group']);
				$this->where('del',0);
				$result = $this->update('group', array($data['type']=>$data['value']));
			}
			
			return $result;

			// if($type=="default_start"){
			// 	$this->where('id_group',$data['id_group']);
			// 	$this->where('del',0);
			// 	$this->update('group', array($data['type']=>$data['value']));
			// 	// $sql = "UPDATE ".PREFIX."group SET ".$data['type']." = '".$data['value']."' 
			// 	// WHERE id_group='".$data['id_group']."'";
			// 	// $result_update_group = $this->query($sql);

			// }
			// if($type=="default_end"){
			// 	$sql = "UPDATE ".PREFIX."group SET ".$data['type']." = '".$data['value']."' 
			// 	WHERE id_group='".$data['id_group']."'";
			// 	$result_update_group = $this->query($sql);

			// }
			// if($type=="default_range"){
			// 	$sql = "UPDATE ".PREFIX."group SET ".$data['type']." = '".$data['value']."' 
			// 	WHERE id_group='".$data['id_group']."'";
			// 	$result_update_group = $this->query($sql);

			// }
			// return $result;
		}
		public function getgroup($data = array()){
			$result = array();
			$this->group_by('group_code');
			$this->order_by('ABS(group_code)','ASC');
			$this->where('del',0);
			$result_group = $this->get('group');
			// $sql = "SELECT * FROM ".PREFIX."group GROUP BY group_code ORDER BY ABS(group_code) ASC";
			// $result_group = $this->query($sql);
			$result = $result_group->rows;
			// $result = array(
			// 	'start_group' 	=> $result_group->row['group_code'],
			// 	'end_group' 	=> $result_group->row['end_group']
			// );
			return $result;
		}
		public function updateWkMapping( $data = array() ){
			$result = array();
			$group_code = $data['id_group'];
			$id_user = $data['id_user'];
			$date_wk = $data['date_wk'];
			$id_product = $data['id_product'];

		

			// ? Find barcode start and end
			$config_barcode = $this->query("SELECT * FROM ".PREFIX."config_barcode WHERE `group` = '".$group_code."'")->row;
			$size_info = $this->query("SELECT * FROM ".PREFIX."product WHERE id_product = '".$id_product."' AND date_wk LIKE '".$date_wk."%'")->row;

			$id_group = 0;

			// ? Return qty in config
			if (isset($size_info['id_group'])&&$size_info['id_group']>0) { // ? กรณีมีค่าเก่าอยู่แล้ว
				// ? remove old 
				$group_old = $this->query("SELECT * FROM ".PREFIX."group WHERE del=0 AND id_group = '".$size_info['id_group']."'")->row['group_code'];
				// $this->query("UPDATE ".PREFIX."group SET del=1 WHERE group_code = '".$group_old."' AND date_wk LIKE '".$date_wk."%'");
				$this->query("DELETE FROM ".PREFIX."group WHERE group_code='$group_old' AND date_wk LIKE '".$date_wk."%'");
				$this->query("UPDATE ".PREFIX."config_barcode SET remaining = remaining + '".$size_info['sum_product']."' WHERE `group` = '".$group_old."'");
				// $update = array(
				// 	'date_modify' => date('Y-m-d H:i:s')
				// );
				// $this->where('id_group', $size_info['id_group']);
				// $this->update('group', $update);
				// $id_group = $size_info['id_group'];
			// } else {
			}

				$start = 0;
				$end = 0;
				$start = (int)$config_barcode['now'];
				$end = (int)$start + (int)$size_info['sum_product'] - 1;

				// ที่เอาออกเพราะ group จะจับรวมเป็นอันเดียว
				// $sql_check_have_group = "SELECT * FROM ".PREFIX."group WHERE group_code = '".$group_code."' AND date_wk LIKE '".$date_wk."%'";
				
				$sql_check_have_group = "SELECT * FROM ".PREFIX."group WHERE del=0 AND group_code = '".$group_code."' ";
				$result_query_check_have_group = $this->query($sql_check_have_group);
				$data_now = date('Y-m-d H:i:s');
				// if not have group
				if($result_query_check_have_group->num_rows == 0 ){
					$data_insert = array(
						'group_code' 	=> $group_code,
						'id_user'		=> $id_user,
						// 'date_wk'		=> $date_wk,
						'date_added'	=> $data_now,
						// 'barcode_use'	=> null,
						'start'			=> $start,
						'end'			=> 0,
						'default_start'	=> $config_barcode['start'],
						'default_end'	=> $config_barcode['end'],
						'default_range'	=> $config_barcode['total'],
						'remaining_qty' => 0
					);
					$id_group = $this->insert('group',$data_insert);
				}else{
					// $update = array();
					$id_group = $result_query_check_have_group->row['id_group'];
				}

			// }
			
			
			
			$this->query("UPDATE ".PREFIX."product SET id_group = '".$id_group."' WHERE id_product = '".$id_product."' AND date_wk LIKE '".$date_wk."%'");

			$remaining = $this->query("SELECT * FROM ".PREFIX."config_barcode WHERE `group` = '".$group_code."'")->row['remaining'];
			$this->query("UPDATE ".PREFIX."group SET date_modify = '".$data_now."', config_remaining = '".$remaining."' WHERE del=0 AND id_group='".$id_group."';");

			$result = array(
				'result' => 'success'
			);


			// update remaining
			$product_info = $this->query("SELECT sum_product FROM ".PREFIX."product WHERE id_product = '".$id_product."' AND date_wk LIKE '".$date_wk."%'");
			$qty = $product_info->row['sum_product'];
			// ? cut stock
			$this->query("UPDATE ".PREFIX."config_barcode SET remaining = total-'".$qty."' WHERE `group` = '".$group_code."'");
			// $this->where('`group`',$group);
			// $this->update('config_barcode', array('remaining'=>'total-'.$qty));

			return $result;
		}
		public function listDateWK( $data = array() ){
			$result = array();
			$sql = "SELECT *,DATE(date_wk) AS date_wk  FROM ".PREFIX."product GROUP BY DATE(date_wk)";
			$result_query = $this->query($sql);
			$result = $result_query->rows;
			return $result;
		}
		public function findPropose($id_product, $date) {

			// ! GET จำนวนวันที่สามารถใช้ group ที่ใช้ไปแล้วได้
			$this->where('config_key','config_date_size');
			$this->select('config_value');
			$query = $this->get('config');
			$config_date_size = $query->row['config_value']; 

			$sql = "SELECT ";
			$sql .= "p.id_product as id_product, ";
			$sql .= "g.group_code as this_group, ";
			$sql .= "g.remaining_qty as this_remaining, ";
			$sql .= "p.size_product_code as size, ";
			$sql .= "p.sum_product as sum_prod, ";
			$sql .= "(SELECT g2.group_code FROM mb_master_product p2 LEFT JOIN mb_master_group g2 ON g2.id_group=p2.id_group WHERE g2.del=0 AND p2.date_wk=(SELECT p3.date_wk FROM mb_master_product p3 GROUP BY p3.date_wk ORDER BY p3.date_wk DESC LIMIT 1,1) AND p2.size_product_code=p.size_product_code) AS last_wk0, ";
			$sql .= "(SELECT g2.remaining_qty-count(b2.id_barcode) AS remaining_qty FROM mb_master_product p2 LEFT JOIN mb_master_group g2 ON g2.id_group=p2.id_group LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=g2.group_code WHERE g2.del=0 AND p2.date_wk=(SELECT p3.date_wk FROM mb_master_product p3 GROUP BY p3.date_wk ORDER BY p3.date_wk DESC LIMIT 1,1) AND p2.size_product_code=p.size_product_code AND b2.barcode_status=1) AS remaining_qty, ";
			
			$sql .= "(SELECT cr2.`group` FROM mb_master_config_relationship cr2 LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=cr2.`group` WHERE cr2.size=p.size_product_code AND cr2.del=0 AND ((b2.date_added IS NOT NULL AND b2.date_added<=CONCAT(DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY),' 23:59:59')) OR (b2.date_added IS NULL)) GROUP BY b2.barcode_prefix) as condition_relationship_group, ";
			// $sql .= "(SELECT cr2.`group` FROM mb_master_config_relationship cr2 LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=cr2.`group` WHERE cr2.size=p.size_product_code AND cr2.del=0 AND b2.date_added<=CONCAT(DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY),' 23:59:59') GROUP BY b2.barcode_prefix) as condition_relationship_group, ";
			$sql .= "(SELECT g2.remaining_qty-count(b2.id_barcode) FROM mb_master_config_relationship cr2 LEFT JOIN mb_master_group g2 ON g2.group_code=cr2.`group` LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=cr2.`group` WHERE g2.del=0 AND cr2.size=p.size_product_code AND g2.date_wk=(SELECT p3.date_wk FROM mb_master_product p3 GROUP BY p3.date_wk ORDER BY p3.date_wk DESC LIMIT 1,1) AND cr2.del=0 AND b2.barcode_status=1 AND b2.date_added<=CONCAT(DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY),' 23:59:59')) as condition_relation_remaining, ";
			
			$sql .= "(SELECT cb2.`group` AS propose_wk0 FROM mb_master_config_barcode cb2 LEFT JOIN mb_master_group g2 ON g2.group_code=cb2.`group` LEFT JOIN mb_master_config_relationship r2 ON r2.`group`=cb2.`group` WHERE g2.del=0 AND g2.id_group IS NULL AND r2.id IS NULL LIMIT 0,1) as condition_notuse_group, ";
			$sql .= "0 as condition_notuse_remaining, ";
			
			$sql .= "(SELECT cb2.`group` AS propose_wk0 FROM mb_master_config_barcode cb2 LEFT JOIN mb_master_group g2 ON g2.group_code=cb2.`group` WHERE g2.del=0 AND g2.id_group IS NOT NULL AND g2.date_wk<=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) AND cb2.remaining > p.sum_product LIMIT 0,1) as condition_groupoldday_group, ";
			$sql .= "(SELECT cb2.remaining AS remaining_wk0 FROM mb_master_config_barcode cb2 LEFT JOIN mb_master_group g2 ON g2.group_code=cb2.`group` WHERE g2.del=0 AND g2.id_group IS NOT NULL AND g2.date_wk<=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) AND cb2.remaining > p.sum_product LIMIT 0,1) as condition_groupoldday_remaining ";
			
			$sql .= "FROM ";
			$sql .= "mb_master_product p ";
			$sql .= "LEFT JOIN mb_master_group g ON g.id_group = p.id_group ";
			$sql .= "LEFT JOIN mb_master_config_barcode b ON b.`group` = g.group_code  ";
			$sql .= "WHERE ";
			$sql .= "p.id_product = '".$id_product."' ";
			$sql .= "AND g.del=0 ";
			$sql .= "AND p.date_wk LIKE '".$date."%' ";
		
			$sql .= "ORDER BY ABS( p.size_product_code ) ASC ";
			
			$result = $this->query($sql)->row; 
			return $result;
		}
		public function listPrefixBarcode( $data = array() ){
			$result = array();
			$date = $data['date'];

			// ! GET จำนวนวันที่สามารถใช้ group ที่ใช้ไปแล้วได้
			$this->where('config_key','config_date_size');
			$this->select('config_value');
			$query = $this->get('config');
			$config_date_size = $query->row['config_value']; 


				// $sql = "SELECT  ";
				// $sql .= "group_code, ";
				// $sql .= "group_code_remaining_qty, ";
				// $sql .= "size, ";
				// $sql .= "sum_prod, ";
				// $sql .= "last_wk0, ";
				// $sql .= "remaining_qty, ";
				// $sql .= "IF (remaining_qty>sum_prod, last_wk0, '') as propose_wk0, ";
				// $sql .= "IF (remaining_qty>sum_prod, remaining_qty, '') as remaining_qty_wk0, ";
				// $sql .= "relation_group, ";
				// $sql .= "relation_remaining, ";
				// $sql .= "condition_groupnotuse_group, ";
				// $sql .= "condition_groupnotuse_remaining, ";
				// $sql .= "condition_groupoldday_group, ";
				// $sql .= "condition_groupoldday_remaining ";
				// $sql .= "FROM ( ";
				// 	$sql .= "SELECT ";
				// 		$sql .= "g.group_code AS group_code, ";
				// 		$sql .= "g.config_remaining AS group_code_remaining_qty, ";
				// 		$sql .= "p.size_product_code AS size, ";
				// 		$sql .= "p.sum_product AS sum_prod, ";
				// 		$sql .= "(SELECT g2.group_code FROM mb_master_product p2 LEFT JOIN mb_master_group g2 ON g2.id_group=p2.id_group WHERE p2.date_wk=(SELECT p3.date_wk FROM mb_master_product p3 GROUP BY p3.date_wk ORDER BY p3.date_wk DESC LIMIT 1,1) AND p2.size_product_code=p.size_product_code) AS last_wk0, ";
				// 		//$sql .= "(SELECT cb3.remaining FROM mb_master_product p3 LEFT JOIN mb_master_group g3 ON g3.id_group=p3.id_group LEFT JOIN mb_master_config_barcode cb3 ON cb3.GROUP=g3.group_code WHERE p3.size_product_code=p.size_product_code GROUP BY p3.date_wk ORDER BY p3.date_wk DESC LIMIT 1,1) as remaining_qty, ";
				// 		// ! ต้องเป็นจำนวน Barcode ที่ Receive แล้วลบกับจำนวนที่ Import Barcode ใช้ไปแล้ว จะได้จำนวนที่เหลือ
				// 		$sql .= "(SELECT g3.remaining_qty-count(b3.id_barcode) AS remaining_qty FROM mb_master_group g3 LEFT JOIN mb_master_barcode b3 ON g3.group_code=b3.barcode_prefix WHERE g3.group_code=g.group_code AND b3.barcode_status=1 AND g3.date_wk LIKE '".$date."%') as remaining_qty, ";

				// 		// ? Relationship
				// 		$sql .= "(SELECT cr.`group` FROM mb_master_config_relationship cr WHERE cr.size = p.size_product_code AND cr.del = 0) as relation_group, ";
				// 		$sql .= "(SELECT cb6.remaining FROM mb_master_config_relationship cr2 LEFT JOIN mb_master_config_barcode cb6 ON cb6.`group` = cr2.`group` WHERE cr2.size = p.size_product_code  AND cr2.del = 0) as relation_remaining, ";
						
				// 		// ? Recommend Group not use
				// 		$sql .= "(SELECT cb4.`group` AS propose_wk0 FROM mb_master_config_barcode cb4 LEFT JOIN mb_master_group g4 ON g4.group_code=cb4.`group` WHERE g4.id_group IS NULL LIMIT 0,1) as condition_groupnotuse_group, ";
				// 		$sql .= "(SELECT cb4.remaining as remaining_wk0 FROM mb_master_config_barcode cb4 LEFT JOIN mb_master_group g4 ON g4.group_code=cb4.`group` WHERE g4.id_group IS NULL LIMIT 0,1) as condition_groupnotuse_remaining, ";

				// 		// ? Recommend Old goup used but more than 3 day
				// 		$sql .= "(SELECT cb5.`group` AS propose_wk0 FROM mb_master_config_barcode cb5 LEFT JOIN mb_master_group g5 ON g5.group_code=cb5.`group` WHERE g5.id_group IS NOT NULL AND g5.date_wk<=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) AND cb5.remaining > p.sum_product LIMIT 0,1) as condition_groupoldday_group, ";
				// 		$sql .= "(SELECT cb5.remaining AS remaining_wk0 FROM mb_master_config_barcode cb5 LEFT JOIN mb_master_group g5 ON g5.group_code=cb5.`group` WHERE g5.id_group IS NOT NULL AND g5.date_wk<=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) AND cb5.remaining > p.sum_product LIMIT 0,1) as condition_groupoldday_remaining ";
				// 	$sql .= "FROM mb_master_product p ";
				// 		$sql .= "LEFT JOIN mb_master_group g ON g.id_group = p.id_group ";
				// 		$sql .= "LEFT JOIN mb_master_config_barcode b ON b.`group` = g.group_code  ";
				// 	$sql .= "WHERE p.date_wk LIKE '".$date."%'  ";
				// 	$sql .= "ORDER BY ";
				// 		$sql .= "ABS( p.size_product_code ) ASC ";
				// $sql .= ") t";

				$sql = "SELECT ";
					$sql .= "p.id_product as id_product, ";
					$sql .= "g.group_code as this_group, ";
					$sql .= "g.remaining_qty as this_remaining, ";
					$sql .= "p.size_product_code as size, ";
					$sql .= "p.sum_product as sum_prod, ";
					$sql .= "(SELECT g2.group_code FROM mb_master_product p2 LEFT JOIN mb_master_group g2 ON g2.id_group=p2.id_group WHERE g2.del=0 AND p2.date_wk=(SELECT p3.date_wk FROM mb_master_product p3 GROUP BY p3.date_wk ORDER BY p3.date_wk DESC LIMIT 1,1) AND p2.date_wk NOT LIKE '".$date."%' AND p2.size_product_code=p.size_product_code) AS last_wk0, ";
					$sql .= "(SELECT g2.remaining_qty-count(b2.id_barcode) AS remaining_qty FROM mb_master_product p2 LEFT JOIN mb_master_group g2 ON g2.id_group=p2.id_group LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=g2.group_code WHERE g2.del=0 AND p2.date_wk=(SELECT p3.date_wk FROM mb_master_product p3 GROUP BY p3.date_wk ORDER BY p3.date_wk DESC LIMIT 1,1) AND p2.date_wk NOT LIKE '".$date."%' AND p2.size_product_code=p.size_product_code AND b2.barcode_status=1) AS remaining_qty, ";
					
					$sql .= "(SELECT cr2.`group` FROM mb_master_config_relationship cr2 LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=cr2.`group` WHERE cr2.size=p.size_product_code AND cr2.del=0 AND ((b2.date_added IS NOT NULL AND b2.date_added<=CONCAT(DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY),' 23:59:59')) OR (b2.date_added IS NULL)) GROUP BY b2.barcode_prefix) as condition_relationship_group, ";
					// $sql .= "(SELECT cr2.`group` FROM mb_master_config_relationship cr2 LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=cr2.`group` WHERE cr2.size=p.size_product_code AND cr2.del=0 AND b2.date_added<=CONCAT(DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY),' 23:59:59') GROUP BY b2.barcode_prefix) as condition_relationship_group, ";
					$sql .= "(SELECT g2.remaining_qty-count(b2.id_barcode) FROM mb_master_config_relationship cr2 LEFT JOIN mb_master_group g2 ON g2.group_code=cr2.`group` LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=cr2.`group` WHERE g2.del=0 AND cr2.size=p.size_product_code AND g2.date_wk=(SELECT p3.date_wk FROM mb_master_product p3 GROUP BY p3.date_wk ORDER BY p3.date_wk DESC LIMIT 1,1) AND cr2.del=0 AND b2.barcode_status=1 AND b2.date_added<=CONCAT(DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY),' 23:59:59')) as condition_relation_remaining, ";
					
					$sql .= "(SELECT cb2.`group` AS propose_wk0 FROM mb_master_config_barcode cb2 LEFT JOIN mb_master_group g2 ON g2.group_code=cb2.`group` LEFT JOIN mb_master_config_relationship r2 ON r2.`group`=cb2.`group` WHERE g2.del=0 AND g2.id_group IS NULL AND r2.id IS NULL LIMIT 0,1) as condition_notuse_group, ";
					$sql .= "0 as condition_notuse_remaining, ";
					
					$sql .= "(SELECT cb2.`group` AS propose_wk0 FROM mb_master_config_barcode cb2 LEFT JOIN mb_master_group g2 ON g2.group_code=cb2.`group` WHERE g2.del=0 AND g2.id_group IS NOT NULL AND g2.date_wk<=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) AND cb2.remaining > p.sum_product LIMIT 0,1) as condition_groupoldday_group, ";
					$sql .= "(SELECT cb2.remaining AS remaining_wk0 FROM mb_master_config_barcode cb2 LEFT JOIN mb_master_group g2 ON g2.group_code=cb2.`group` WHERE g2.del=0 AND g2.id_group IS NOT NULL AND g2.date_wk<=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) AND cb2.remaining > p.sum_product LIMIT 0,1) as condition_groupoldday_remaining, ";

					$sql .= "(SELECT b2.barcode_code FROM mb_master_product p2 LEFT JOIN mb_master_group g2 ON g2.id_group=p2.id_group LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=g2.group_code WHERE g2.del=0 AND p2.id_group IS NOT NULL AND p2.size_product_code=p.size_product_code AND p2.date_wk LIKE '".$date."%' AND b2.date_added>=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) ORDER BY b2.barcode_code ASC LIMIT 0,1) as start_of_year, ";
					$sql .= "(SELECT b2.date_added FROM mb_master_product p2 LEFT JOIN mb_master_group g2 ON g2.id_group=p2.id_group LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=g2.group_code WHERE g2.del=0 AND p2.id_group IS NOT NULL AND p2.size_product_code=p.size_product_code AND p2.date_wk LIKE '".$date."%' AND b2.date_added>=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) ORDER BY b2.barcode_code ASC LIMIT 0,1) as startdate_of_year, ";
					$sql .= "(SELECT b2.barcode_code FROM mb_master_product p2 LEFT JOIN mb_master_group g2 ON g2.id_group=p2.id_group LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=g2.group_code WHERE g2.del=0 AND p2.id_group IS NOT NULL AND p2.size_product_code=p.size_product_code AND p2.date_wk LIKE '".$date."%' AND b2.date_added>=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) ORDER BY b2.barcode_code DESC LIMIT 0,1) as end_of_year, ";
					$sql .= "(SELECT b2.date_added FROM mb_master_product p2 LEFT JOIN mb_master_group g2 ON g2.id_group=p2.id_group LEFT JOIN mb_master_barcode b2 ON b2.barcode_prefix=g2.group_code WHERE g2.del=0 AND p2.id_group IS NOT NULL AND p2.size_product_code=p.size_product_code AND p2.date_wk LIKE '".$date."%' AND b2.date_added>=DATE_ADD(CURDATE(),INTERVAL-".$config_date_size." DAY) ORDER BY b2.barcode_code DESC LIMIT 0,1) as enddate_of_Year ";
					
					$sql .= "FROM ";
					$sql .= "mb_master_product p ";
					$sql .= "LEFT JOIN mb_master_group g ON g.id_group = p.id_group ";
					$sql .= "LEFT JOIN mb_master_config_barcode b ON b.`group` = g.group_code  ";
					$sql .= "WHERE ";
					$sql .= "p.date_wk LIKE '".$date."%' ";
					$sql .= "AND g.del=0 ";
				
					$sql .= "ORDER BY ABS( p.size_product_code ) ASC ";
			
			// echo $sql;
			$result = $this->query($sql)->rows; 
			

			return $result;
		}
		public function getMapping($data = array()){
			$result = array();
			$start_group = $data['start_group'];
			$end_group = $data['end_group'];

			$sql = "SELECT * FROM mb_master_group WHERE del=0 AND group_code BETWEEN '".$start_group."' AND '".$end_group."' ORDER BY ABS(group_code) ASC";
			// echo $sql;
			// exit();
			$result_query = $this->query($sql);
			$result = $result_query->rows;
			// foreach($result_query->rows as $val){
			// 	$result[$val['size']] = $val['group_code'];
			// }
			return $result;
		}
		// public function getBarcode($data = array()){
		// 	$result = array();
		// 	$date = $data['date'];
		// 	$sql = "SELECT *,mb_master_barcode.date_added AS date_added FROM mb_master_barcode 
		// 	LEFT JOIN mb_master_user ON mb_master_barcode.id_user = mb_master_user.id_user 
		// 	WHERE DATE(mb_master_barcode.date_added) = '".$date."' limit 0,10";
		// 	$result_query = $this->query($sql);
		// 	return $result_query->rows;
		// }
		public function getNumsBarcode($data = array()){
			$result = array();
			$date = $data['date'];
			$sql = "SELECT *,mb_master_barcode.date_added AS date_added FROM mb_master_barcode 
			LEFT JOIN mb_master_user ON mb_master_barcode.id_user = mb_master_user.id_user 
			WHERE DATE(mb_master_barcode.date_added) = '".$date."'";
			$result_query = $this->query($sql);
			return $result_query->num_rows;
		}
		public function getExcelBarcode($data = array()){
			$result = array();
			$date = $data['date'];
			$sql = "SELECT barcode_prefix,barcode_code,mb_master_barcode.date_added AS date_added,username FROM mb_master_barcode 
			LEFT JOIN mb_master_user ON mb_master_barcode.id_user = mb_master_user.id_user 
			WHERE DATE(mb_master_barcode.date_added) = '".$date."'";
			$result_query = $this->query($sql);
			$header = array(
				'Prefix',
				'Barcode',
				'Date added',
				'Create by'
			);
			$result[] = $header;
			foreach($result_query->rows as $val){
				// $result[] = $val;
				$temp = array();
				foreach($val as $v){
					$temp[] = $v; 
				}
				$result[] = $temp;
			}
			return $result;
		}
		public function getExcelListGroup($data = array()){
			$result = array();
			$date = $data['date'];

			$sql = "SELECT ".PREFIX."group.group_code,".PREFIX."group.start,".PREFIX."group.end,".PREFIX."group.remaining_qty 
			FROM ".PREFIX."group 
			LEFT JOIN ".PREFIX."user ON ".PREFIX."group.id_user = ".PREFIX."user.id_user
			WHERE group.del=0 AND DATE(".PREFIX."group.date_added) = '".$date."'"; 
			$result_query = $this->query($sql);
			$header = array(
				'Prefix',
				'Barcode',
				'Date added',
				'Create by'
			);
			$result[] = $header;
			foreach($result_query->rows as $val){
				// $result[] = $val;
				$temp = array();
				foreach($val as $v){
					$temp[] = $v; 
				}
				$result[] = $temp;
			}
			return $result;
		}

		public function updateBarcodeStatus($id, $status_id) {
			$this->where('id_group', $id);
			$this->where('del',0);
			$result = $this->update('group', array('barcode_use'=>$status_id));
			return $result;
		}

		public function getGroupInBarcode($date="") {
			$this->where('date_added', $date.'%', 'LIKE');
			$this->group_by('barcode_prefix');
			$this->select('barcode_prefix as `group`');
			$query = $this->get('barcode');
			return $query->rows;
		}
		




		
	}
?>