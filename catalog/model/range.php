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
            echo $sql;
            $this->query($sql);
              
            $sql = "ALTER TABLE `mb_master_barcode_range` ";
            $sql .= "ADD PRIMARY KEY (`id`); ";
            $this->query($sql);
              
            $sql = "ALTER TABLE `mb_master_barcode_range` ";
            $sql .= "MODIFY `id` int(11) NOT NULL AUTO_INCREMENT; ";
            $sql .= "COMMIT; ";
            $this->query($sql);
        }
		public function findIdByGroup($group, $status=1) {
            $this->select('id');
            $this->where('barcode_status', $status);
            $this->where('group_code', $group);
            $query = $this->get('barcode_range');
			return $query->rows;
        }

        public function findGroup($group, $status=1) {
            $this->where('barcode_status', $status);
            $query = $this->get('barcode_range');
			return $query->rows;
        }
        
        public function addRange($data) {
            $sql = "INSERT INTO mb_master_barcode_range (`round`,`group_code`,`barcode_start`,`barcode_end`,`barcode_qty`,`barcode_status`) VALUES ";
            $values = array();
            foreach ($data as $value) {
                $values[] = " ('".$value['round']."','".$value['group_code']."','".$value['barcode_start']."','".$value['barcode_end']."','".$value['barcode_qty']."','".$value['barcode_status']."') ";
            }
            $sql .= implode(',', $values);
            return $this->query($sql);
        }

        public function delRange($id, $status='') {
            $sql = "DELETE FROM mb_master_barcode_range WHERE id IN (".$id.") ";
            if (!empty($status)) {
                $sql .= "AND barcode_status = ".(int)$status." ";
            }
            return $this->query($sql);
        }
    }

?>