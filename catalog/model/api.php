<?php
class ApiModel extends db
{
  public function createTableHistory($dbname='fsoftpro_barcode') {
    $sql = "CREATE TABLE IF NOT EXISTS `$dbname`.`mb_master_history` (";
    $sql .= "`id_history` INT NOT NULL AUTO_INCREMENT, ";
    $sql .= "`id_user` INT NULL, ";
    $sql .= "`id_group` INT NULL, ";
    $sql .= "`barcode_start` INT NULL, ";
    $sql .= "`barcode_end` INT NULL, ";
    $sql .= "`barcode_qty` INT NULL, ";
    $sql .= " `barcode_use` INT DEFAULT 0, ";
    $sql .= "`date_purchase` date NULL, ";
    $sql .= "`date_received` date NULL, ";
    $sql .= "`date_added` datetime (0) NULL, ";
    $sql .= "`date_modify` datetime (0) NULL, ";
    $sql .= "`del` INT DEFAULT 0, ";
    $sql .= "PRIMARY KEY (`id_history`), ";
    $sql .= "INDEX `id_group` (`id_group`) USING BTREE, ";
    $sql .= "INDEX `barcode_start` (`barcode_start`) USING BTREE, ";
    $sql .= "INDEX `barcode_end` (`barcode_end`) USING BTREE, ";
    $sql .= "INDEX `barcode_qty` (`barcode_qty`) USING BTREE, ";
    $sql .= "INDEX `date_purchase` (`date_purchase`) USING BTREE, ";
    $sql .= "INDEX `date_received` (`date_received`) USING BTREE) ";
    return $this->query($sql);
  }
  public function cleanTableHistory($dbname='fsoftpro_barcode') {
    $sql = "TRUNCATE `$dbname`.`mb_master_history";
    return $this->query($sql);
  }
  public function dumpDataGroupHistory($dbname='fsoftpro_barcode') {
    $sql = "SELECT * FROM `$dbname`.`mb_master_config_barcode`";
    $query = $this->query($sql);
    return $query->rows;
  }
  public function runDataGroupHistory($dbname='fsoftpro_barcode', $group=null) {
    $sql = "INSERT INTO `$dbname`.`mb_master_history` (id_user,id_group,barcode_start,barcode_end,barcode_qty,barcode_use,date_purchase,date_received,date_added,date_modify) SELECT 1 AS id_user,g.id_group,min(b.barcode_code) AS barcode_start,max(b.barcode_code) AS barcode_end,
    (max(b.barcode_code)-min(b.barcode_code))+1 AS barcode_qty,
     b.barcode_status as barcode_use, b.date_added AS date_purchase,
     b.date_modify as date_received,
     NOW() AS date_added,
     NOW() AS date_modify FROM `$dbname`.`mb_master_group` g 
     LEFT JOIN `$dbname`.`mb_master_barcode` b ON b.id_group=g.id_group WHERE g.group_code=$group  GROUP BY b.date_added ORDER BY g.group_code ASC,b.date_added ASC";
    return $this->query($sql);
  }
  
  public function cleanRemainingQtyProduct() {
    $sql = "UPDATE `mb_master_product` SET remaining_qty = null, propose=null, propose_remaining_qty=null, `message`=null;";
    return $this->query($sql);
  }
}