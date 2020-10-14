<?php 
	class AssociationModel extends db {
        
        public function getDateWK() {
            $this->select('CAST(date_wk as DATE) as date_wk');
            $this->group_by('date_wk');
            $this->order_by('date_wk', 'DESC');
            $query = $this->get('product');
            return $query->rows;
        }
        public function getProducts($date_wk) {
            $this->select('p.id_product, p.size_product_code as size, p.sum_product as sum_prod, g.group_code');
            $this->where("p.date_wk ", $date_wk.'%','LIKE');
            $this->order_by('ABS(p.size_product_code)', 'ASC');
            $this->join('group g','g.id_group=p.id_group','LEFT');
            $query = $this->get('product p');
            return $query->rows;
        }
        public function getDateLastWeek() {
            $this->select('CAST(date_wk as DATE) as date_wk');
            $this->group_by('date_wk');
            $this->order_by('date_wk', 'DESC');
            $this->limit(1,1);
            $query = $this->get('product p');
            return !empty($query->row['date_wk']) ? $query->row['date_wk'] : false;
        }
        public function getGroupLastWeek($size, $date_lastwk) {
            if ($date_lastwk!=false) {
                $this->where('p.id_group is not null','','');
                $this->where('p.date_wk', $date_lastwk.'%', 'LIKE');
                $this->where('p.size_product_code', $size);
                $this->where('g.del',0);
                $this->where('g.date_added<=DATE_ADD(CURDATE(),INTERVAL-3 DAY)','','');
                $this->select('g.group_code');
                $this->join('group g','g.id_group=p.id_group','LEFT');
                $query = $this->get('product p');
                return !empty($query->row['group_code']) ? $query->row['group_code'] : '';
            } else {
                return '';
            }
        }
        public function getRemainingByGroup($group_code) {
            $this->where('g.group_code', $group_code);
            $this->where('g.barcode_use', 1);
            $this->where('b.barcode_status', 1);
            $this->where('g.del',0);
            $this->select('if (count(b.id_barcode)>0, g.remaining_qty-count(b.id_barcode), g.remaining_qty) as remaining_qty');
            $this->join('barcode b','b.id_group=g.id_group');
            $query = $this->get('group g');
            return !empty($query->row['remaining_qty']) ? $query->row['remaining_qty'] : '';
        }
        public function getRelationshipBySize($size) {
            $this->where('cr.size', $size);
            $this->where('cr.`group` is not null','','');
            $this->where('g.date_added <= DATE_ADD(CURDATE(),INTERVAL-3 DAY)','','');
            $this->where('g.del',0);
            $this->select('cr.`group`');
            $this->join('group g','g.group_code = cr.`group`','LEFT');
            $query = $this->get('config_relationship cr');
            return !empty($query->row['group']) ? $query->row['group'] : '';
        }
        public function getFreeGroup() {
            $this->where('g.id_group is null','','');
            $this->where('cr.id is null','','');
            // $this->where('g.del',0);
            $this->select('cb.`group`');
            $this->join('group g','g.group_code=cb.`group`','LEFT');
            $this->join('config_relationship cr','cr.`group` = cb.`group`','LEFT');
            $query = $this->get('config_barcode cb');
            return $query->rows;
        }

    }
?>