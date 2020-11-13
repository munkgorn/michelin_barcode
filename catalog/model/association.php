<?php
class AssociationModel extends db
{
    public function importCSV($path)
    {
        $result = array();

        $sql = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE " . PREFIX . "product FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n' IGNORE 1 ROWS ( id_user,size_product_code,sum_product,date_wk,date_added,date_modify);";
        $result = $this->query($sql);

        $date_now = date('Y-m-d H:i:s');
        $this->where('date_added', '0000-00-00 00:00:00');
        $this->update('product', array('date_wk' => $date_now, 'date_added' => $date_now, 'date_modify' => $date_now));

        // return $result;
        return $date_now;
    }
    public function validatedProductWithGroup($data = array())
    {
        $result = array();
        $group_code = (int) $data['id_group'];
        $id_user = $data['id_user'];
        $date_wk = $data['date_wk'];
        $id_product = $data['id_product'];

        // Find barcode start and end
        $config_barcode = $this->query("SELECT * FROM " . PREFIX . "config_barcode WHERE `group` = '" . $group_code . "'")->row;
        $size_info = $this->query("SELECT * FROM " . PREFIX . "product WHERE id_product = '" . $id_product . "' AND date_wk LIKE '" . $date_wk . "%'")->row;

        $id_group = 0;

        // Remove old data
        if (isset($size_info['id_group']) && $size_info['id_group'] > 0) {
            $group_old = $this->query("SELECT * FROM " . PREFIX . "group WHERE del=0 AND id_group = '" . $size_info['id_group'] . "'")->row['group_code'];
            $this->query("DELETE FROM " . PREFIX . "group WHERE group_code='$group_old' AND date_wk LIKE '" . $date_wk . "%'");
            $this->query("UPDATE " . PREFIX . "config_barcode SET remaining = remaining + '" . $size_info['sum_product'] . "' WHERE `group` = '" . $group_old . "'");
        }

        $start = 0;
        $end = 0;
        $start = (int) $config_barcode['now'];
        $end = (int) $start + (int) $size_info['sum_product'] - 1;

        $sql_check_have_group = "SELECT * FROM " . PREFIX . "group WHERE del=0 AND group_code = '" . $group_code . "' ";
        $result_query_check_have_group = $this->query($sql_check_have_group);
        $data_now = date('Y-m-d H:i:s');

        if ($result_query_check_have_group->num_rows == 0) { // Insert because this group is never used.
            $data_insert = array(
                'group_code' => $group_code,
                'id_user' => $id_user,
                'date_added' => $data_now,
                'start' => $start,
                'end' => 0,
                'default_start' => $config_barcode['start'],
                'default_end' => $config_barcode['end'],
                'default_range' => $config_barcode['total'],
                'remaining_qty' => 0,
            );
            $id_group = $this->insert('group', $data_insert);

        } else { // Get last id on this group
            $id_group = $result_query_check_have_group->row['id_group'];
        }

        $config_barcodes = $this->get('config_barcode');
        foreach ($config_barcodes->rows as $barcode) {
            $this->where('group_code', $barcode['group']);
            $group_info = $this->get('group');
            if ($group_info->num_rows == 0) {
                $insert = array(
                    'id_user' => $id_user,
                    'group_code' => $barcode['group'],
                    'start' => $barcode['start'],
                    'end' => 0,
                    'remaining_qty' => 0,
                    'default_start' => $barcode['start'],
                    'default_end' => $barcode['end'],
                    'default_range' => $barcode['total'],
                    'barcode_use' => 0,
                    'config_remaining' => $barcode['total'],
                    'del' => 0,
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_modify' => date('Y-m-d H:i:s'),
                );
                $this->insert('group', $insert);
            }
        }

        // Update qty
        $remaining = $this->query("SELECT * FROM " . PREFIX . "config_barcode WHERE `group` = '" . $group_code . "'")->row['remaining'];
        $this->query("UPDATE " . PREFIX . "group SET date_modify = '" . $data_now . "', config_remaining = '" . $remaining . "' WHERE del=0 AND id_group='" . $id_group . "';");

        // Update qty in setting
        $product_info = $this->query("SELECT sum_product FROM " . PREFIX . "product WHERE id_product = '" . $id_product . "' AND date_wk LIKE '" . $date_wk . "%'");
        $qty = $product_info->row['sum_product'];
        $this->query("UPDATE " . PREFIX . "config_barcode SET remaining = total-'" . $qty . "' WHERE `group` = '" . $group_code . "'");

        // Update import product
        $result = $this->query("UPDATE " . PREFIX . "product SET id_group = '" . $id_group . "' WHERE id_product = '" . $id_product . "' AND date_wk LIKE '" . $date_wk . "%'");
        return $result == 1 ? true : false;
    }
    public function checkValidatedDate($date)
    {
        $this->where('date_wk', $date . '%', 'LIKE');
        $this->where('id_group is not null', '', '');
        $this->select('count(id_group) as count_group');
        $result = $this->get('product');
        return $result->row['count_group'];
    }

    public function getDateWK()
    {
        $this->select('CAST(date_wk as DATE) as date_wk');
        $this->group_by('date_wk');
        $this->order_by('date_wk', 'DESC');
        $query = $this->get('product');
        return $query->rows;
    }
    public function addProduct($data = array())
    {
        return $this->insert('product', $data);
    }
    public function getProducts($date_wk)
    {
        $this->select('p.id_product, p.size_product_code as size, p.sum_product as sum_prod, g.group_code');
        $this->where("p.date_wk ", $date_wk . '%', 'LIKE');
        $this->order_by('ABS(p.size_product_code)', 'ASC');
        $this->join('group g', 'g.id_group=p.id_group', 'LEFT');
        $query = $this->get('product p');
        return $query->rows;
    }
    public function getDateLastWeek()
    {
        $this->select('CAST(date_wk as DATE) as date_wk');
        $this->group_by('date_wk');
        $this->order_by('date_wk', 'DESC');
        $this->limit(1, 1);
        $query = $this->get('product p');
        return !empty($query->row['date_wk']) ? $query->row['date_wk'] : false;
    }
    public function getGroupLastWeek($size, $date_lastwk)
    {
        if ($date_lastwk != false) {
            $this->where('p.id_group is not null', '', '');
            $this->where('p.date_wk', $date_lastwk . '%', 'LIKE');
            $this->where('p.size_product_code', $size);
            $this->where('g.del', 0);
            $this->where('g.date_added<=DATE_ADD(CURDATE(),INTERVAL-3 DAY)', '', '');
            $this->select('g.group_code');
            $this->join('group g', 'g.id_group=p.id_group', 'LEFT');
            $query = $this->get('product p');
            return !empty($query->row['group_code']) ? $query->row['group_code'] : '';
        } else {
            return '';
        }
    }
    public function getNotUseBarcode($group_code) {
        $sql = "SELECT count(*) as qty  FROM mb_master_barcode WHERE barcode_prefix = $group_code AND group_received = 1 AND barcode_status = 0 ";
        $query = $this->query($sql);
        return $query->row['qty'];
    }
    public function getGroupReceived($group_code)
    {
        $this->select('remaining_qty as barcode_received');
        $this->where('group_code', $group_code);
        $this->where('barcode_use', 1);
        $this->where('del', 0);
        $query = $this->get('group');
        if ($group_code == 250) {
            // echo $this->last_query();
        }
        return $query->num_rows > 0 ? $query->row['barcode_received'] : false;
    }
    public function getBarcodeUse($group_code)
    {
        // $this->select('count(b.id_barcode) as barcode');
        // $this->join('barcode b', 'b.id_group = g.id_group', 'LEFT');
        // $this->where('g.group_code', $group_code);
        // $this->where('g.barcode_use', 1);
        // $this->where('b.barcode_status', 1);
        // $this->where('g.del', 0);
        // $query = $this->get('group g');
        
        return $query->row['barcode'];
    }
    public function getRemainingByGroup($group_code)
    {
        $this->where('g.group_code', $group_code);
        $this->where('g.barcode_use', 1);
        // $this->where('b.barcode_status', 1);
        $this->where('g.del', 0);
        $this->select('if (count( b.id_barcode )> 0, g.remaining_qty-count(b.id_barcode), g.remaining_qty) as remaining_qty');
        $this->join('barcode b', 'b.id_group=g.id_group');
        $query = $this->get('group g');
        // echo $this->last_query();
        return !empty($query->row['remaining_qty']) ? $query->row['remaining_qty'] : '';
    }
    public function getRelationshipBySize($size, $sumprod=0)
    {
        $day1 = $this->query("SELECT config_value FROM mb_master_config WHERE config_key = 'config_date_size';")->row['config_value'];
        $day1 = date('Y-m-d', strtotime('-'.$day1.'day'));

        $day2 = $this->query("SELECT config_value FROM mb_master_config WHERE config_key = 'config_date_year';")->row['config_value'];
        $day2 = date('Y-m-d', strtotime('-'.$day2.'day'));

        $sql = "SELECT * FROM  ";
        $sql .= "(  ";
        $sql .= "SELECT cr.`group`, ";
        $sql .= "(SELECT count(b.id_barcode) as qty FROM mb_master_barcode b WHERE b.barcode_prefix = cr.`group` AND b.group_received = 1 AND b.barcode_status = 0 AND b.date_modify BETWEEN '$day2' AND '$day1' GROUP BY b.id_group, b.barcode_prefix ORDER BY b.id_barcode ASC,b.id_group ASC,b.date_modify DESC) as qty  ";
        $sql .= "FROM mb_master_config_relationship cr ";
        $sql .= "WHERE cr.size != '' AND cr.size is not null AND cr.size = $size";
        $sql .= ") t WHERE t.qty is not null AND t.qty >= $sumprod LIMIT 0,1";

        $query = $this->query($sql);
        return $query->row;

        // $this->where('cr.size', $size);
        // $this->where('cr.`group` is not null', '', '');
        // $this->where("g.date_purchase <= '".$day1."'", '', '');
        // $this->where('g.del', 0);
        // $this->select('cr.`group`');
        // $this->join('group g', 'g.group_code = cr.`group`', 'LEFT');
        // $query = $this->get('config_relationship cr');
        // return !empty($query->row['group']) ? $query->row['group'] : '';
    }
    public function getFreeGroup() {
        $day1 = $this->query("SELECT config_value FROM mb_master_config WHERE config_key = 'config_date_size';")->row['config_value'];
        $day1 = date('Y-m-d', strtotime('-'.$day1.'day'));

        $day2 = $this->query("SELECT config_value FROM mb_master_config WHERE config_key = 'config_date_year';")->row['config_value'];
        $day2 = date('Y-m-d', strtotime('-'.$day2.'day'));

        // $sqlbarcode = "SELECT count(b.id_barcode) as qty FROM mb_master_barcode b WHERE b.barcode_prefix = 192 AND group_received = 1 AND barcode_status = 0 AND b.date_modify BETWEEN '$day2' AND '$day1' GROUP BY b.id_group, b.barcode_prefix ORDER BY b.id_barcode ASC,b.id_group ASC,b.date_modify DESC";

        $sql = "SELECT * FROM ( ";
            $sql .= "SELECT  ";
            $sql .= "g.group_code as `group`, ";
            $sql .= "(SELECT count(b.id_barcode) as qty FROM mb_master_barcode b WHERE b.barcode_prefix = g.group_code AND group_received = 1 AND barcode_status = 0 AND b.date_modify BETWEEN '$day2' AND '$day1' GROUP BY b.id_group, b.barcode_prefix ORDER BY b.id_barcode,b.id_group) as qty ";
            $sql .= "FROM mb_master_group g ";
            $sql .= "WHERE ";
            $sql .= "g.barcode_use = 1 ";
        $sql .= ") t ";
        $sql .= "WHERE t.qty is not null ";
        // echo $sql;

        $query = $this->query($sql);
        return $query->rows;
    }
    public function getFreeGroup2()
    {
        // $this->where('g.id_group is null','','');
        $this->where('cr.id is null', '', '');
        $this->where('b.id_barcode is null', '', '');
        // $this->where('g.del',0);
        // $this->select('cb.`group`');
        $this->select('LPAD(cb.`group`, 3, "0")  as `group`');
        $this->join('group g', 'g.group_code=cb.`group`', 'LEFT');
        $this->join('barcode b', 'b.id_group = g.id_group', 'LEFT');
        $this->join('config_relationship cr', 'cr.`group` = cb.`group`', 'LEFT');
        $this->order_by('ABS(cb.`group`)', 'ASC');
        $query = $this->get('config_barcode cb');
        return $query->rows;
    }

}
