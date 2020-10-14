<?php 
	class GroupModel extends db {
        public function findIdGroup($code) {
            $this->where('group_code', $code);
            $this->where('del',0);
            $query = $this->get('group');
            // echo $this->last_query();
            return !empty($query->row['id_group']) ? $query->row['id_group'] : '';
        }
        public function findCode($id) {
            $this->where('id_group',$id);
            $this->where('del',0);
            $query = $this->get('group');
            return !empty($query->row['group_code']) ? $query->row['group_code'] : '';
        }
        public function getGroupStatus($group_code) {
            $this->where('group_code', $group_code);
            $this->where('del',0);
            $this->where('remaining_qty', 0, '>=');
			$query = $this->get('group');
			return isset($query->row['barcode_use']) ? $query->row['barcode_use'] : false;
        }
        public function getGroups($filter=array()) {
            if (isset($filter['date_modify'])) {
                $this->where('g.date_modify', $filter['date_modify'].'%', 'LIKE');
            }
            if (isset($filter['group_code'])&&!empty($filter['group_code'])) {
                $this->where('g.group_code', $filter['group_code']);
            }
            if (isset($filter['barcode_use'])&&$filter['barcode_use']>=0) {
                $this->where('g.barcode_use', (int)$filter['barcode_use']);
            }
            if (isset($filter['has_remainingqty'])) {
                $this->where('remaining_qty', '0', '>=');
            }
            $this->where('g.del',0);
            $this->group_by('g.group_code');
            $this->join('user u','u.id_user = g.id_user','LEFT');
            $this->select('g.*, u.username');
            $query = $this->get('group g');
            // echo $this->last_query();
            return $query->rows;
        }

        public function changeStatus($id, $status) {
            $this->where('id_group', $id);
            $this->where('del',0);
            return $this->update('group', array('barcode_use'=>(int)$status));
        }

        public function delGroup($id) {
            $this->where('id_group', $id);
            $query = $this->get('group');
            $group = $query->row;

            $start = $group['start']-$group['remaining_qty']; // find start number this purchase
            $end = $group['start']-1; // -1 because this number for next purcharse

            $update = array(
                // 'del'=>1  // เอาออกเพราะว่า group เกิดมาตั้งแต่ association แล้ว ดังนั้นใช้วิธีคืนค่าแทน
                'start' => $start,
                'remaining_qty' => 0
            );
            $this->where('id_group', $id);
            $result = $this->update('group', $update);
 
            // if ($result) {
                $this->query("DELETE FROM ".PREFIX."barcode WHERE id_group = '".$id."' AND barcode_code >= '".$start."' AND barcode_code <= '".$end."'");
            // }

        }
	}
?>