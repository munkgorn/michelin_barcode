<?php 
	class SizeModel extends db {
		public function import($data=array()){
			$result = array();
			$file = $data['file'];
			$sql = "LOAD DATA LOCAL INFILE '" . DOCUMENT_ROOT . $file . "' INTO TABLE ".PREFIX."product FIELDS TERMINATED BY ',' 
			LINES TERMINATED BY '\n' IGNORE 1 ROWS ( id_user,size_product_code, sum_product,date_wk,date_added,date_modify);";
			$this->query($sql);

			// Remove after fix code date input
			$date_now = date('Y-m-d H:i:s');
			$sql_update_date = "UPDATE ".PREFIX."product SET date_wk = '".$date_now."',date_added='".$date_now."',date_modify='".$date_now."' WHERE date_wk = '0000-00-00 00:00:00'";
			$this->query($sql_update_date);
			return $result;
		}

		public function getProduct($id) {
			$this->where('id_product', $id);
			$query = $this->get('product');
			return $query->row;
		}
	}
?>