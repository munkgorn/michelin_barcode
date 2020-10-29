
<?php 
	class ImportModel extends db {
        public function getTables() {
            $query = $this->query('SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = "fsoftpro_barcode" AND TABLE_TYPE = "BASE TABLE"');
            return $query->rows;
        }

        public function getColumns($table) {
            $query = $this->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'fsoftpro_barcode' AND TABLE_NAME = '" . $table .  "';");
            return $query->rows;
        }

        public function querySql($sql) {
            $query = $this->query($sql);
        }

        public function insertBarcode($barcode, $date) {
            $this->query("INSERT INTO mb_master_barcode SET id_user = 2, id_group = 1, barcode_prefix=33,barcode_code='".$barcode."',barcode_status=1,date_added='".$date."',date_modify='".$date."'");
        }

        public function loadCSVGroup($path) {
			$sql = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE ".PREFIX."group FIELDS TERMINATED BY ',' 
			LINES TERMINATED BY '\n' ( id_user, group_code, start, default_start, default_end, default_range, date_added, date_modify, barcode_use);";
			$result = $this->query($sql);
        }

        public function loadCSVBarcode($path) {
			$sql = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE ".PREFIX."barcode FIELDS TERMINATED BY ',' 
			LINES TERMINATED BY '\n' ( id_user,id_group,barcode_prefix,barcode_code,date_added,date_modify);";
			$result = $this->query($sql);
        }
    }