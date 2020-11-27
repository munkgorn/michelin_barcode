<?php 
	class GroupModel extends db {
        public function findIdGroup($code) {
            $this->where('group_code', $code);
            $this->where('del',0);
            $query = $this->get('group');
            // echo $this->last_query();
            return !empty($query->row['id_group']) ? $query->row['id_group'] : '';
        }
        public function findCode($id) {
            $this->where('id_group',$id);
            $this->where('del',0);
            $query = $this->get('group');
            return !empty($query->row['group_code']) ? $query->row['group_code'] : '';
        }
        public function getDateGroup() {
            $this->select('date_purchase');
            $this->where('date_purchase is not null','','');
            $this->group_by('date_purchase');
            $this->order_by('date_purchase', 'DESC');
            $query = $this->get('group');
            return $query->num_rows > 0 ? $query->rows : false;
        }
        public function getGroupStatus($group_code) {
            $this->where('group_code', $group_code);
            $this->where('del',0);
            $this->where('remaining_qty', 0, '>=');
			$query = $this->get('group');
			return isset($query->row['barcode_use']) ? $query->row['barcode_use'] : false;
        }
        public function getGroups($filter=array()) {
            if (isset($filter['date_modify'])&&!empty($filter['date_modify'])) {
                $this->where('g.date_modify', $filter['date_modify'].'%', 'LIKE');
            }
            if (isset($filter['date_purchase'])&&!empty($filter['date_purchase'])) {
                $this->where('g.date_purchase', $filter['date_purchase']);
            }
            if (isset($filter['group_code'])&&!empty($filter['group_code'])) {
                $this->where('g.group_code', $filter['group_code']);
            }
            // print_r($filter);
            if (isset($filter['barcode_use'])&& (!empty($filter['barcode_use']) || $filter['barcode_use']==="0")) {
                $this->where('g.barcode_use', $filter['barcode_use']);
            }
            if (isset($filter['has_remainingqty'])) {
                $this->where('remaining_qty', '0', '>=');
            }
            $this->where('g.del',0);
            // $this->where('g.start != g.default_start','','');
            // $this->where('remaining_qty', 0, '>');
            $this->group_by('g.group_code');
            $this->join('user u','u.id_user = g.id_user','LEFT');
            $this->select('g.*, u.username');
            $query = $this->get('group g');
            // echo $this->last_query();
            // echo '<br>';
            return $query->rows;
        }

        public function changeStatus($id, $status) {
            $this->where('id_group', $id);
            $this->update('barcode', array('group_received' => 1));
            
            $this->where('id_group', $id);
            $this->where('del',0);
            return $this->update('group', array('barcode_use'=>(int)$status, 'id_user'=>$_SESSION['id_user']));
        }

        public function delGroup($id) {
            $this->where('id_group', $id);
            $query = $this->get('group');
            $group = $query->row;

            $num1 = $group['start'] - $group['remaining_qty'] + 1;
            if ($num1<$group['default_start']) {
                $num2 = $group['start'] - $group['default_start'];
                $num3 = $group['default_end'] - ($group['remaining_qty'] - $num2);
                $start = $num3 + 1;
                // echo 'con1';
            } else {
                $start = $group['start'] - $group['remaining_qty']; // find start number this purchase
                $end = $start + $group['remaining_qty'] - 1;
                // echo 'con2';
            }

            // echo $start.' '.$end;
            // exit();
            
            // $end = $group['remaining_qty'] - 1 ; // -1 because this number for next purcharse

            $update = array(
                // 'del'=>1  // เอาออกเพราะว่า group เกิดมาตั้งแต่ association แล้ว ดังนั้นใช้วิธีคืนค่าแทน
                'start' => $start,
                'remaining_qty' => 0,
                'date_purchase' => NULL,
            );
            $this->where('id_group', $id);
            $result = $this->update('group', $update);
 
            // echo "DELETE FROM ".PREFIX."barcode WHERE id_group = '".$id."' AND barcode_code >= '".$start."' AND barcode_code <= '".$end."'";
            // if ($result) {
                $this->query("DELETE FROM ".PREFIX."barcode WHERE id_group = '".$id."' AND barcode_code >= '".$start."' AND barcode_code <= '".$end."'");
            // }

        }

        public function addDefaultGroup() {
            $query = $this->query("SELECT cb.* FROM mb_master_config_barcode cb LEFT JOIN mb_master_group g ON g.group_code = cb.`group` WHERE g.id_group is null;");
            $config_barcode = $query->rows;

            foreach ($config_barcode as $value) {
                $this->query("INSERT INTO mb_master_group SET id_user = 1, group_code = '".$value['group']."', start = '".$value['start']."', end = '0', remaining_qty = '0', default_start = '".$value['start']."', default_end = '".$value['end']."', default_range = '".$value['total']."', barcode_use = '0', config_remaining = '', del = '0', date_added = '".date('Y-m-d')."', date_modify = '".date('Y-m-d')."' ");
            }
        }

	}
?>