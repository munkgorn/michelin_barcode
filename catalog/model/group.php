<?php 
	class GroupModel extends db {
        public function findIdGroup($code) {
            $this->where('group_code', $code);
            $this->where('del',0);
            $query = $this->get('group');
            // echo $this->last_query();
            // echo '<br>';
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
            // $query = $this->get('group');
            $query = $this->get('history');
            return $query->num_rows > 0 ? $query->rows : false;
        }
        public function getGroupStatus($group_code) {
            $this->where('group_code', $group_code);
            $this->where('del',0);
            $this->where('remaining_qty', 0, '>=');
            $this->where('date_purchase', '0000-00-00', '!=');
			$query = $this->get('group');
			return isset($query->row['barcode_use']) ? (int)$query->row['barcode_use'] : false;
        }
        public function getGroups($filter=array()) {
            if (isset($filter['date_modify'])&&!empty($filter['date_modify'])) {
                $this->where('g.date_modify', $filter['date_modify'].'%', 'LIKE');
            }
            if (isset($filter['date_purchase'])&&!empty($filter['date_purchase'])) {
                $this->where('g.date_purchase', $filter['date_purchase']);
            }
            if (isset($filter['group_code'])&&!empty($filter['group_code'])) {
                $this->where('g2.group_code', $filter['group_code']);
            }
            // // print_r($filter);
            if (isset($filter['barcode_use'])&& (!empty($filter['barcode_use']) || $filter['barcode_use']==="0")) {
                $this->where('g2.barcode_use', $filter['barcode_use']);
            }
            if (isset($filter['has_remainingqty'])) {
                $this->where('g.barcode_qty', '0', '>=');
            }
            // $this->where('g.del',0);
            $this->group_by('g.id_group');
            $this->join('group g2','g.id_group = g2.id_group','LEFT');
            $this->join('user u','u.id_user = g.id_user','LEFT');
            $this->select('g.*, u.username, g2.group_code');
            $query = $this->get('history g');
            // echo $this->last_query();
            // echo '<br>';
            return $query->rows;
        }
        public function getGroupsOld($filter=array()) {
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
            $query = $this->get('hisoty g');
            // echo $this->last_query();
            // echo '<br>';
            return $query->rows;
        }
        public function getGroup($id) {
            $this->where('id_group', $id);
            $this->where('del', 0);
            $this->order_by('id_group', 'ASC');
            $query = $this->get('group');
            return $query->row;
        }
        public function changeStatus($idgroup, $status) {
            $this->where('id_group', $idgroup);
            $this->update('barcode', array('group_received' => 1));
            
            $this->where('id_group', $idgroup);
            $this->where('del',0);
            $response = $this->update('group', array('barcode_use'=>(int)$status, 'id_user'=>$_SESSION['id_user']));

            // $this->where('id_history', $id);
            // $this->where('id_group', $idgroup);
            // $this->update('history', array('barcode_use'=>(int)$status, 'date_modify'=>date('Y-m-d H:i:s')));


            return $response;
        }

        public function delGroup($id) {
            $this->where('id_group', $id);
            $query = $this->get('group');
            $group = $query->row;

            $group['start'] = (int)substr($group['start'], 3, 5);
            $group['default_start'] = (int)substr($group['default_start'], 3, 5);
            $group['default_end'] = (int)substr($group['default_end'], 3, 5);

            $num1 = $group['start'] - $group['remaining_qty'] + 1;
            if ($num1<$group['default_start']) {
                $num2 = $group['start'] - $group['default_start'];
                $num3 = $group['default_end'] - ($group['remaining_qty'] - $num2);
                $start = $num3 + 1;
            } else {
                $start = $group['start'] - $group['remaining_qty']; // find start number this purchase
                $end = $start + $group['remaining_qty'] - 1;
            }

            $update = array(
                'start' => $group['group_code'].sprintf('%05d',$start),
                'remaining_qty' => 0,
                'date_purchase' => NULL,
            );
            
            $this->where('id_group', $id);
            $result = $this->update('group', $update);
            
            $this->query("DELETE FROM ".PREFIX."barcode WHERE id_group = '".$id."' AND barcode_code >= '".$start."' AND barcode_code <= '".$end."'");

        }

        public function addDefaultGroup() {
            $query = $this->query("SELECT cb.* FROM mb_master_config_barcode cb LEFT JOIN mb_master_group g ON g.group_code = cb.`group` WHERE g.id_group is null;");
            $config_barcode = $query->rows;

            foreach ($config_barcode as $value) {
                $query2 = $this->query("SELECT * FROM mb_master_group WHERE group_code = '".$value['group']."' ");
                if ($query2->num_rows == 0) {
                    $value['start'] = (int)substr($value['start'], 3,5);
                    $this->query("INSERT INTO mb_master_group SET id_user = 1, group_code = '".$value['group']."', start = '".$value['start']."', end = '0', remaining_qty = '0', default_start = '".$value['start']."', default_end = '".$value['end']."', default_range = '".$value['total']."', barcode_use = '0', config_remaining = '', del = '0', date_added = '".date('Y-m-d')."', date_modify = '".date('Y-m-d')."' ");
                }
            }
        }

        public function updateDefaultStart() {
            $sql = 'UPDATE mb_master_group SET default_start = "00000", default_end = "99999", default_range = 10000;';
            return $this->query($sql);
        }



        public function createTableHistory()
        {
            $sql = "";
            $sql .= "CREATE TABLE `mb_master_history`  ( ";
                $sql .= "`id_history` int NOT NULL AUTO_INCREMENT, ";
                $sql .= "`id_user` int NULL, ";
                $sql .= "`id_group` int NULL, ";
                $sql .= "`barcode_start` int NULL, ";
                $sql .= "`barcode_end` int NULL, ";
                $sql .= "`barcode_qty` int NULL, ";
                $sql .= "`barcode_use` int NULL, ";
                $sql .= "`date_purchase` date NULL, ";
                $sql .= "`date_received` date NULL, ";
                $sql .= "`date_added` datetime(0) NULL, ";
                $sql .= "`date_modify` datetime(0) NULL, ";
                $sql .= "PRIMARY KEY (`id_history`), ";
                $sql .= "INDEX `id_group`(`id_group`) USING BTREE, ";
                $sql .= "INDEX `barcode_start`(`barcode_start`) USING BTREE, ";
                $sql .= "INDEX `barcode_end`(`barcode_end`) USING BTREE, ";
                $sql .= "INDEX `barcode_qty`(`barcode_qty`) USING BTREE, ";
                $sql .= "INDEX `date_purchase`(`date_purchase`) USING BTREE, ";
                $sql .= "INDEX `date_received`(`date_received`) USING BTREE ";
              $sql .= "); ";
            $this->query($sql);
        }

	}
?>