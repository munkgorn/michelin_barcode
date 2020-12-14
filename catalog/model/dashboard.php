<?php 
	class DashboardModel extends db {
        public function countGroup() {
            $this->group_by('group_code');
            $query = $this->get('group');
            return $query->num_rows;
        }
        public function countBarcode() {
            $this->where('barcode_status', 1);
            $query = $this->get('barcode');
            return $query->num_rows;
        }
        public function countBarcodeWaiting() {
            $this->where('barcode_status', 0);
            $query = $this->get('barcode');
            return $query->num_rows;
        }
        public function countBarcodeMissing() {
            $this->where('barcode_flag', 1);
            // $this->where('barcode_status',)
            $query = $this->get('barcode');
            return $query->num_rows;
        }
    }
?>