<?php 
	class RangeModel extends db {

        public function createTable() {
            $sql = "CREATE TABLE `fsoftpro_barcode`.`mb_master_barcode_range` ( ";
            $sql .= "`id` int(11) NOT NULL, ";
            $sql .= "`round` int(11) DEFAULT NULL, ";
            $sql .= "`group_code` int(11) DEFAULT NULL, ";
            $sql .= "`barcode_start` int(11) DEFAULT NULL, ";
            $sql .= "`barcode_end` int(11) DEFAULT NULL, ";
            $sql .= "`barcode_qty` int(11) DEFAULT NULL, ";
            $sql .= "`barcode_status` int(11) DEFAULT NULL ";
            $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; ";
            $this->query($sql);
              
            $sql = "ALTER TABLE `mb_master_barcode_range` ";
            $sql .= "ADD PRIMARY KEY (`id`); ";
            $this->query($sql);
              
            $sql = "ALTER TABLE `mb_master_barcode_range` ";
            $sql .= "MODIFY `id` int(11) NOT NULL AUTO_INCREMENT; ";
            $this->query($sql);
            $sql = "COMMIT; ";
            $this->query($sql);
        }

        public function updateTable() {
            $sql = "ALTER TABLE mb_master_barcode_range ADD date_added date; ";
            $this->query($sql);
            $sql = "ALTER TABLE mb_master_barcode_range ADD date_modify date; ";
            $this->query($sql);
            $sql = "UPDATE mb_master_barcode_range r  ";
            $sql .= "left join mb_master_barcode b on b.barcode_code = r.barcode_start ";
            $sql .= "set r.date_added = b.date_added, r.date_modify = b.date_modify ";
            $this->query($sql);
        }
        


		public function findAllByGroup($group, $status=1) {
            $this->where('barcode_status', $status);
            if ($group!='all') {
                $this->where('group_code', (int)$group);
            }
            $query = $this->get('barcode_range');
			return $query->rows;
        }

		public function findIdByGroup($group, $status=1) {
            $this->select('id');
            $this->where('barcode_status', $status);
            $this->where('group_code', $group);
            $query = $this->get('barcode_range');
            // echo $this->last_query();
			return $query->rows;
        }

        public function getQtyLower($qty) 
        {
            $this->where('barcode_qty', $qty, '<');
            $this->get('barcode_range');
        } 
        public function findGroup($group, $status=1) {
            $this->where('barcode_status', $status);
            $this->where('group_code', $group);
            $query = $this->get('barcode_range');
			return $query->row;
        }

        public function addRange($data) {
            $sql = "INSERT INTO mb_master_barcode_range (`round`,`group_code`,`barcode_start`,`barcode_end`,`barcode_qty`,`barcode_status`,`date_added`,`date_modify`) VALUES ";
            $values = array();
            foreach ($data as $value) {
                $values[] = " ('".$value['round']."','".$value['group_code']."','".$value['barcode_start']."','".$value['barcode_end']."','".$value['barcode_qty']."','".$value['barcode_status']."', '".$value['date_added']."', '".$value['date_modify']."') ";
            }
            $sql .= implode(',', $values);
            $result = $this->query($sql);
            
            $max = $this->findLowerAndRemove();
            foreach ($data as $value) {
                if ((int)$value['barcode_qty'] < $max && $value['barcode_status'] == 0) { // only remaining
                    $this->removeLower($value['group_code'], $value['barcode_start'], $value['barcode_end'], $value['barcode_qty']);
                }
                // $this->updateDateRange($value['group_code']);
            }

            return $result;
        }

        public function updateDateRange($group=null) {
            $sql = "UPDATE mb_master_barcode_range r LEFT JOIN mb_master_barcode b ON b.barcode_code=r.barcode_start SET r.date_added=b.date_added,r.date_modify=b.date_modify ";
            $sql .= ($group>0) ? "WHERE r.group_code = ".$group : "";
            $this->query($sql);
        }

        public function removeLower($group, $start, $end, $qty) {
            $this->where('group_code', $group);
            $this->where('barcode_start', $start);
            $this->where('barcode_end', $end);
            $this->where('barcode_qty', $qty);
            $this->where('barcode_status', 0);
            $query = $this->get('barcode_range');
            $range = $query->row;

            for ( $i=$start; $i<=$end; $i++) {
                $sql = "UPDATE mb_master_barcode SET barcode_flag = 1 WHERE barcode_code = '".(int)$i."'";
                $this->query($sql);
            }

            $sql = "DELETE FROM mb_master_barcode_range WHERE id = '".$range['id']."';";
            $this->query($sql);

            
        }

        public function findLowerAndRemove() {
            $this->where('config_key', 'config_maximum_alert');
            $query = $this->get('config');
            return $query->row['config_value'];
        }

        public function delRange($id, $status='') {
            $sql = "DELETE FROM mb_master_barcode_range WHERE id IN (".$id.") ";
            if (!empty($status)) {
                $sql .= "AND barcode_status = ".(int)$status." ";
            }
            return $this->query($sql);
        }

        public function clearRange($group, $status) {
            $sql = "DELETE FROM mb_master_barcode_range WHERE `group_code` = '".$group."' AND barcode_status = '".$status."';";
            $query = $this->query($sql);
            return $query; 
        }
        
    }

?>