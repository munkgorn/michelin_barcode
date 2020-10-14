<?php 
	class PurchaseModel extends db {
        public function getStartDateOfYearAgo() {
            $this->where('config_key', 'config_date_year');
            $query = $this->get('config');
            $dayofyear = $query->row['config_value'];

            $this->where('config_key', 'config_date_size');
            $query = $this->get('config');
            $beforeusesize = $query->row['config_value'];

            $sql = "SELECT date_added FROM ".PREFIX."barcode WHERE date_added BETWEEN STR_TO_DATE(DATE_ADD(CURDATE(),INTERVAL-".$dayofyear." DAY),'%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(DATE_ADD(CURDATE(),INTERVAL-".$beforeusesize." DAY),'%Y-%m-%d %H:%i:%s') GROUP BY date_added ORDER BY date_added ASC LIMIT 0,1;";
            $query = $this->query($sql);
            return isset($query->row['date_added']) ? $query->row['date_added'] : '';
        }

        public function getEndDateOfYearAgo() {
            $this->where('config_key', 'config_date_year');
            $query = $this->get('config');
            $dayofyear = $query->row['config_value'];

            $this->where('config_key', 'config_date_size');
            $query = $this->get('config');
            $beforeusesize = $query->row['config_value'];

            $sql = "SELECT date_added FROM ".PREFIX."barcode WHERE date_added BETWEEN STR_TO_DATE(DATE_ADD(CURDATE(),INTERVAL-".$dayofyear." DAY),'%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(DATE_ADD(CURDATE(),INTERVAL-".$beforeusesize." DAY),'%Y-%m-%d %H:%i:%s') GROUP BY date_added ORDER BY date_added DESC LIMIT 0,1;";
            $query = $this->query($sql);
            return isset($query->row['date_added']) ? $query->row['date_added'] : '';
        }

        public function getStartBarcodeOfYearAgo($group) {
            $this->where('config_key', 'config_date_year');
            $query = $this->get('config');
            $dayofyear = $query->row['config_value'];

            $this->where('config_key', 'config_date_size');
            $query = $this->get('config');
            $beforeusesize = $query->row['config_value'];

            $sql = "SELECT barcode_code FROM ".PREFIX."barcode WHERE date_added BETWEEN STR_TO_DATE(DATE_ADD(CURDATE(),INTERVAL-".$dayofyear." DAY),'%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(DATE_ADD(CURDATE(),INTERVAL-".$beforeusesize." DAY),'%Y-%m-%d %H:%i:%s') AND barcode_prefix=".$group." GROUP BY date_added,barcode_code ORDER BY date_added ASC,barcode_code ASC LIMIT 0,1;";
            $query = $this->query($sql);
            return isset($query->row['barcode_code']) ? $query->row['barcode_code'] : '';
        }

        public function getEndBarcodeOfYearAgo($group) {
            $this->where('config_key', 'config_date_year');
            $query = $this->get('config');
            $dayofyear = $query->row['config_value'];

            $this->where('config_key', 'config_date_size');
            $query = $this->get('config');
            $beforeusesize = $query->row['config_value'];

            $sql = "SELECT barcode_code FROM ".PREFIX."barcode WHERE date_added BETWEEN STR_TO_DATE(DATE_ADD(CURDATE(),INTERVAL-".$dayofyear." DAY),'%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(DATE_ADD(CURDATE(),INTERVAL-".$beforeusesize." DAY),'%Y-%m-%d %H:%i:%s') AND barcode_prefix=".$group." GROUP BY date_added,barcode_code ORDER BY date_added DESC,barcode_code DESC LIMIT 0,1;";
            $query = $this->query($sql);
            return isset($query->row['barcode_code']) ? $query->row['barcode_code'] : '';
        }

        public function getPurchases($filter=array()) {
            // SELECT group_code, `start` as barcode_start, '00000000' as barcode_end, default_start, default_end, default_range FROM mb_master_group ORDER BY group_code
            if (count($filter)>0) {
                if (isset($filter['start_group'])&&isset($filter['end_group'])) {
                    $this->where("group_code BETWEEN '".$filter['start_group']."' AND '".$filter['end_group']."'",'','');
                } else {
                    foreach ($filter as $key => $value) {
                        $this->where($key, $value);
                    }
                }
            }
            $this->select("group_code, `start` as barcode_start, '' as barcode_end, default_start, default_end, default_range, remaining_qty ");
            $this->order_by('group_code','ASC');
            $query = $this->get('group');
            return $query->rows;
        }
	}
?>