<?php 
	class ConfigModel extends db {
        // master config zone
        public function getConfig($key) {
            $this->where('config_key', $key);
            $query = $this->get('config');
            return $query->num_rows == 1 ? $query->row['config_value'] : false;
        }
        public function setConfig($key, $value) {
            $this->where('config_key', $key);
            $query = $this->get('config');
            if ($query->num_rows == 1){
                $this->where('config_key', $key);
                if ($this->update('config', array('config_value'=>$value,'date_modify'=>date('Y-m-d H:i:s')))==1) {
                    return true;
                }
            } else {
                if ($this->insert('config', array('config_key'=>$key, 'config_value'=>$value,'date_added'=>date('Y-m-d H:i:s')))>0) {
                    return true;
                }
            }
            return false;
        }
        // master config zone

        // Config Relationship
        public function getRelationship() {
            return $this->get('config_relationship')->rows;
        }
        // Config Relationship

        // Config status
        public function getStatus($id=0) {
            if ($id>0) {
                $this->where('id', $id);
            }
            $this->where('del', 0);
            $query = $this->get('config_status');
            return $id>0 ? $query->row : $query->rows;
        }
        public function addStatus($data) {
            return $this->insert('config_status', $data);
        }
        public function editStatus($id, $data) {
            $this->where('id', $id);
            $this->where('del', 0);
            return $this->update('config_status', $data);
        }
        public function delStatus($id) {
            $this->where('id', $id);
            return $this->update('config_status', array('del'=>1));
        }
        // Config status

        // Config Relationship
        public function importRelationship($path_file){
            $this->query("TRUNCATE mb_master_config_relationship;");
			$sql = "LOAD DATA LOCAL INFILE '" . $path_file . "' INTO TABLE ".PREFIX."config_relationship FIELDS TERMINATED BY ',' 
			LINES TERMINATED BY '\n' IGNORE 1 ROWS ( `group`,`size`,`comment`,`date_added`,`date_modify`)";
            $result = $this->query($sql);
            $this->where('date_added', '0000-00-00 00:00:00');
            $this->where('date_modify', '0000-00-00 00:00:00');
            $this->update('config_relationship', array('date_added'=>date('Y-m-d H:i:s'),'date_modify'=>date('Y-m-d H:i:s')));
            return $result;
        }
        public function getLastupdateRelationship() {
            $this->select('date_added');
            $this->group_by('date_added');
            $this->order_by('date_added','desc');
            $query = $this->get('config_relationship');
            return isset($query->row['date_added']) ? $query->row['date_added'] : false;
        }
        // Config Relationship

        // Config Barcode Zone
        public function importBarcode($path_file){
            $this->query("TRUNCATE mb_master_config_barcode;");
			$sql = "LOAD DATA LOCAL INFILE '" . $path_file . "' INTO TABLE ".PREFIX."config_barcode FIELDS TERMINATED BY ',' 
            LINES TERMINATED BY '\n' IGNORE 1 ROWS ( `group`,`start`,`end`,`total`,`remaining`,`now`)";
            $result = $this->query($sql);

            $this->where('date_added', '0000-00-00 00:00:00');
            $this->update('config_barcode', array('date_added'=>date('Y-m-d H:i:s')));

            $this->where('date_added is null', '', '');
            $this->update('config_barcode', array('date_added'=>date('Y-m-d H:i:s')));
            return $result;
		}
        public function getBarcodes($filter=array()) {
            if (count($filter)>0) {
                foreach ($filter as $key => $value) {
                    $this->where($key, $value);
                }
            }

            $query = $this->get('config_barcode');
            return $query->rows;
        }
        public function getBarcodeByPrefix($prefix) {
            $this->where('`group`', $prefix);
            $query = $this->get('config_barcode');
            return $query->num_rows == 1 ? $query->row : false;
        }
        public function getBarcode($id) {
            $this->where('id', $id);
            $query = $this->get('config_barcode');
            return $query->row;
        }
        public function getLastupdateBarcode() {
            $this->select('date_added');
            $this->group_by('date_added');
            $this->order_by('date_added', 'desc');
            $query = $this->get('config_barcode');
            return isset($query->row['date_added']) ? $query->row['date_added'] : false;
        }
        public function addBarcode($data=array()) {
            return $this->insert('config_barcode', $data);
        }
        public function editBarcode($id, $data=array()) {
            $this->where('id', $id);
            return $this->update('config_barcode', $data);
        }
        // Config Barcode Zone
	}
?>