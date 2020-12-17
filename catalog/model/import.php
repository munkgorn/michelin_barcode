
<?php 
	class ImportModel extends db {

        public function removeTable($table) {
            return $this->query("truncate table $table");
        }

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
			LINES TERMINATED BY '\n' ( id_user, group_code, start, date_purchase, date_added, date_modify, barcode_use, remaining_qty);";
            $result = $this->query($sql);
            
            // update default start default end in group table for speed query
            $sql = "UPDATE mb_master_group g ";
            $sql .= "LEFT JOIN mb_master_config_barcode b ON b.`group` = g.group_code ";
            $sql .= "SET ";
            $sql .= "g.default_start = b.`start`, ";
            $sql .= "g.default_end = b.`end`, ";
            $sql .= "g.default_range = b.`total` ";
            $sql .= "WHERE g.default_start = 0 AND g.default_end = 0 AND g.default_range = 0 ";
            $this->query($sql);

            // update barcode now for ready next purchase condition is standby start+1 
            $sql = "UPDATE mb_master_group g ";
            $sql .= "SET g.`start` = (CASE WHEN g.`start` + 1 > g.default_end THEN g.default_start ELSE g.`start`+1 END) ";
            $this->query($sql);

            return $result;
        }

        public function loadCSVBarcode($path) {
			$sql = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE ".PREFIX."barcode FIELDS TERMINATED BY ',' 
			LINES TERMINATED BY '\n' ( id_user,id_group,barcode_prefix,barcode_code,barcode_status,group_received,date_added,date_modify);";
			$result = $this->query($sql);
        }

        public function loadCSVAssociation($path) {
            $sql = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE ".PREFIX."product FIELDS TERMINATED BY ',' 
            LINES TERMINATED BY '\n' IGNORE 1 ROWS ( id_user,size_product_code,sum_product,product_name,date_wk);";
            $result = $this->query($sql);

            // $this->query("UPDATE ".PREFIX."product p LEFT JOIN mb_master_group g ON g.`group_code`=p.product_name SET p.date_added = p.date_wk, p.date_modify = p.date_wk, p.id_group = g.id_group, p.product_name = null WHERE g.id_group > 0 AND p.product_name is not null");
            $this->query("UPDATE mb_master_product p LEFT JOIN mb_master_group g ON g.`group_code`=p.product_name SET p.date_added=p.date_wk,p.date_modify=p.date_wk,p.id_group=g.id_group,p.product_name=NULL WHERE p.product_name IS NOT NULL");
            return $result;
        }
    }