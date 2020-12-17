<?php
class AssociationModel extends db
{
    public function checkProduct($size) {
        $this->where('size_product_code', $size);
        $query = $this->get('product');
        return $query->num_rows==1?true:false;
    }
    public function importCSV($path)
    {
        $result = array();

        $sql = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE " . PREFIX . "product FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' ( id_user,size_product_code,sum_product,date_wk,date_added,date_modify);";
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
        $group_code = (int)$data['id_group']; // groupcode
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

        if ($result_query_check_have_group->num_rows == 0 && (int)$group_code>0) { // Insert because this group is never used.==1?true:false;
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
            if ($group_info->num_rows == 0 && (int)$barcode['group']>0) {
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

    public function removeJunkSave($date_wk) {
        $this->where('date_wk', $date_wk);
        $update = array(
            'propose' => '',
            'propose_remaining_qty' => '',
            'message'=>'',
            'remaining_qty' => '',
        );
        $this->update('product', $update);
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

    

    public function getCountBarcode($group_code) {
        $sql = "SELECT count(b.id_barcode) AS countqty FROM mb_master_barcode b WHERE b.barcode_prefix=$group_code AND b.barcode_flag=0 AND b.barcode_status=0 AND b.group_received=1 GROUP BY b.id_group ORDER BY b.id_barcode,b.id_group,b.barcode_flag,b.barcode_status,b.group_received";
        $query = $this->query($sql);
        return $query->row['countqty'];
    }
    public function getProducts($date_wk)
    {
//         SELECT 
// p.size_product_code,
// p.sum_product,
// g.group_code,
// (select count(*) as qty from mb_master_barcode b WHERE b.barcode_prefix = g.group_code AND b.barcode_status = 0 AND b.group_received = 1 AND b.barcode_flag = 0) as qty
// FROM mb_master_product p
// LEFT JOIN mb_master_group g ON g.id_group = p.id_group
// WHERE p.date_wk = '2020-10-16'
        // $this->select('p.id_product, p.size_product_code as size, p.sum_product as sum_prod, g.group_code');
        // $this->where("p.date_wk ", $date_wk . '%', 'LIKE');
        // $this->order_by('ABS(p.size_product_code)', 'ASC');
        // $this->join('group g', 'g.id_group=p.id_group', 'LEFT');
        // $query = $this->get('product p');
        // $sql = "SELECT ";
        //     $sql .= "p.id_product, ";
        //     $sql .= "p.size_product_code AS size, ";
        //     $sql .= "p.sum_product AS sum_prod, ";
        //     $sql .= "g.group_code AS `last_week`, ";
        //     $sql .= "(SELECT group_code FROM mb_master_group g2 WHERE g2.id_group = p.id_group) as save ";
        //     // $sql .= "g2.group_code as save ";
        //     // $sql .= "-- ,(SELECT count(b.id_barcode) AS countqty FROM mb_master_barcode b WHERE b.id_group=g.id_group AND b.barcode_flag=0 AND b.barcode_status=0 AND b.group_received=1 GROUP BY b.id_group ORDER BY b.id_barcode,b.id_group,b.barcode_flag,b.barcode_status,b.group_received) AS qty ";
        // $sql .= "FROM ";
        // $sql .= "mb_master_product p ";
        //     $sql .= "INNER JOIN mb_master_product p2 ON p2.size_product_code = p.size_product_code ";
        //     $sql .= "LEFT JOIN mb_master_group g ON g.id_group = p2.id_group  ";
        //     // $sql .= "LEFT JOIN mb_master_group g2 ON g.id_group = p.id_group  ";
        //     $sql .= "WHERE ";
        //         $sql .= "p.date_wk = '$date_wk' ";
        //         $sql .= "AND p.id_product != p2.id_product ";
        // $sql .= "ORDER BY p.size_product_code ASC ";
        // echo $sql;
        // echo '<br>';

        //$query = $this->query("SELECT date_wk FROM mb_master_product WHERE date_wk < '$date_wk' GROUP BY date_wk ORDER BY date_wk DESC LIMIT 0,1");
        //$last_datewk = isset($query->row['date_wk']) ? $query->row['date_wk'] : '';
        $query = $this->query("SELECT * FROM mb_master_config WHERE config_key = 'config_lastweek';");
        $lastweekdate = $query->row['config_value'];

        $sql = "SELECT ";
        $sql .= "p.id_product, ";
        $sql .= "p.size_product_code AS size, ";
        $sql .= "p.sum_product AS sum_prod, ";
        // $sql .= "g.group_code AS `last_week`, ";
        $sql .= "(SELECT g.group_code FROM mb_master_product p2 LEFT JOIN mb_master_group g ON g.id_group = p2.id_group WHERE p2.size_product_code = p.size_product_code AND p2.id_product != p.id_product AND p2.id_group is not null AND p2.date_wk >= DATE_ADD( p.date_wk, INTERVAL - $lastweekdate DAY ) AND p2.date_wk <= p.date_wk  ORDER BY p2.id_product DESC LIMIT 0,1 ) as last_week, ";
        // $sql .= "(SELECT count(*) as qty FROM mb_master_barcode b WHERE b.id_group = g.id_group AND b.group_received=1 AND b.barcode_status=0 AND b.barcode_flag=0) as remaining_qty, ";
        $sql .= "(SELECT group_code FROM mb_master_group g2 WHERE g2.id_group = p.id_group) as save ";
        $sql .= "FROM mb_master_product p ";
        // $sql .= "LEFT JOIN mb_master_product p2 ON p2.size_product_code = p.size_product_code ";
        // $sql .= "LEFT JOIN mb_master_group g ON g.id_group = p2.id_group  ";
        $sql .= "WHERE ";
        $sql .= "p.date_wk = '$date_wk' ";
        // $sql .= "AND ( (p2.date_wk >= DATE_ADD( '$date_wk', INTERVAL - $lastweekdate DAY ) AND p2.id_product is not null AND p2.id_product != p.id_product) OR (p2.id_product is null) )  ";
        // $sql .= !empty($last_datewk) ?  "AND  " : "";
        // $sql .= !empty($last_datewk) ? "( " : "";
        //     $sql .= !empty($last_datewk) ? "(p.id_product != p2.id_product AND p2.date_wk = '$last_datewk') OR " : "";
        //     $sql .= !empty($last_datewk) ? "(p2.id_product is null) " : "";
        //     $sql .= !empty($last_datewk) ? ") " : "";
        $sql .= "GROUP BY p.size_product_code ";
        $sql .= "ORDER BY p.size_product_code ASC ";
        $query = $this->query($sql);
        // echo $sql;
        return $query->rows;
    }
    public function countAllBarcodeNotUsed() 
    {
        $sql = "SELECT * FROM (SELECT  ";
        $sql .= "g.group_code ";
        $sql .= ",(SELECT count(b.id_barcode) AS countqty FROM mb_master_barcode b WHERE b.id_group=g.id_group AND b.barcode_flag=0 AND b.barcode_status=0 AND b.group_received=1 GROUP BY b.id_group) AS qty ";
        $sql .= "FROM mb_master_group g ";
        $sql .= "WHERE g.barcode_use = 1 ) t WHERE t.qty is not null ";
        $query = $this->query($sql);
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
    public function getGroupLastWeek($size, $date_lastwk='')
    {
        // if ($date_lastwk != false) {
            // $this->where('p.id_group is not null', '', '');
            // $this->where('p.date_wk', $date_lastwk . '%', 'LIKE');
            // $this->where('p.size_product_code', $size);
            // $this->where('g.del', 0);
            // $this->where('g.date_added<=DATE_ADD(CURDATE(),INTERVAL-3 DAY)', '', '');
            // $this->select('g.group_code');
            // $this->join('group g', 'g.id_group=p.id_group', 'LEFT');
            // $query = $this->get('product p');
            // return !empty($query->row['group_code']) ? $query->row['group_code'] : '';
            $sql = "SELECT g.group_code FROM mb_master_product p  ";
            $sql .= "LEFT JOIN mb_master_group g ON g.id_group = p.id_group ";
            $sql .= "WHERE g.id_group is not null AND p.size_product_code = $size ";
            $sql .= "GROUP BY p.date_wk ORDER BY p.date_wk DESC LIMIT 1 ";
            $query = $this->query($sql);
            // echo $sql;
            // echo '<br>';
            return !empty($query->row['group_code']) ? $query->row['group_code'] : '';
        // } else {
            // return '';
        // }
    }
    public function getRemaining($idproduct) {
        $sql = "SELECT remaining_qty FROM mb_master_product WHERE id_product = $idproduct";
        $query = $this->query($sql);
        return $query->num_rows == 1&&!empty($query->row['remaining_qty']) ? $query->row['remaining_qty'] : false;
    }
    public function getPropose($idproduct) {
        $sql = "SELECT propose, propose_remaining_qty, `message` FROM mb_master_product WHERE id_product = $idproduct";
        $query = $this->query($sql);
        return $query->num_rows==1&&!empty($query->row['propose']) ? $query->row : false;
    }
    public function savePropose($idproduct , $data=array()) {
        if (
            isset($data['remaining_qty'])&&!empty($data['remaining_qty']) ||
            isset($data['propose'])&&!empty($data['propose']) ||
            isset($data['propose_remaining_qty'])&&!empty($data['propose_remaining_qty']) ||
            isset($data['message'])&&!empty($data['message']) 
        ) {
        $sql = "UPDATE mb_master_product SET ";
        $sql .= isset($data['remaining_qty'])&&!empty($data['remaining_qty']) ? "remaining_qty = '".$data['remaining_qty']."', " : "";
        $sql .= isset($data['propose'])&&!empty($data['propose']) ? "propose = '".$data['propose']."', " : "";
        $sql .= isset($data['propose_remaining_qty'])&&!empty($data['propose_remaining_qty']) ? "propose_remaining_qty = '".$data['propose_remaining_qty']."', " : "";
        $sql .= isset($data['message'])&&!empty($data['message']) ? "`message` = '".$data['message']."' " : "";
        $sql .= " WHERE id_product = ".(int)$idproduct;
        $query = $this->query($sql);
        }
        
    }
    public function getNotUseBarcode($group_code) {
        $sql = "SELECT count(*) as qty  FROM mb_master_barcode WHERE barcode_prefix = $group_code AND barcode_flag = 0 AND group_received = 1 AND barcode_status = 0 ";
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
        $sql .= "SELECT cr.size, cr.`group`, ";
        $sql .= "(SELECT count(b.id_barcode) as qty FROM mb_master_barcode b WHERE b.barcode_prefix = cr.`group` AND b.group_received = 1 AND b.barcode_status = 0 AND b.date_modify BETWEEN '$day2' AND '$day1' GROUP BY b.id_group, b.barcode_prefix ORDER BY b.id_barcode ASC,b.id_group ASC,b.date_modify DESC) as qty  ";
        $sql .= "FROM mb_master_config_relationship cr ";
        $sql .= "WHERE cr.size != '' AND cr.size is not null AND cr.size = '".$size."'";
        $sql .= ") t ";
        // $sql .= "WHERE t.qty is not null AND t.qty >= $sumprod LIMIT 0,1";
        $sql .= "WHERE 1 LIMIT 0,1";


        $query = $this->query($sql);
        return $query->row;
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
            $sql .= "(SELECT count(b.id_barcode) as qty FROM mb_master_barcode b WHERE b.barcode_prefix = g.group_code AND b.group_received = 1 AND b.barcode_flag = 0 AND b.barcode_status = 0 AND b.date_modify BETWEEN '$day2' AND '$day1' GROUP BY b.id_group, b.barcode_prefix ORDER BY b.id_barcode,b.id_group) as qty ";
            $sql .= "FROM mb_master_group g ";
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

    public function getOldSync() {
        $query = $this->query("SELECT config_value FROM mb_master_config WHERE config_key = 'config_date_size'");
        $config = $query->row['config_value'];
        $sql = "SELECT g.group_code FROM mb_master_product p LEFT JOIN mb_master_group g ON g.id_group=p.id_group WHERE p.date_modify>=DATE_ADD(CURDATE(),INTERVAL-$config DAY) AND p.date_modify AND g.group_code is not null ";
        // $sql = "SELECT g.group_code FROM mb_master_product p LEFT JOIN mb_master_group g ON g.id_group=p.id_group WHERE p.date_modify>=DATE_ADD('2020-10-16',INTERVAL-$config DAY) ";
        $query = $this->query($sql);
        return $query->rows;
    }

    public function clearAllAssociation() {
        return $this->query("TRUNCATE TABLE mb_master_product;");
    }

    public function clearAssociation($date) {
        return $this->query("DELETE FROM mb_master_product WHERE date_wk = '$date'");
    }

}
