<?php
class HistoryModel extends db
{
 public function addHistory($data = array())
 {
  return $this->insert('history', $data);
 }

 public function editHistory($id, $data = array())
 {
  $this->where('del', 0);
  $this->where('id_history', $id);
  return $this->update('history', $data);
 }

 public function delHistory($id)
 {
  $this->where('id_history', $id);
  $this->update('history', array('del' => 1));
 }

 public function getHistories($data = array())
 {
  $this->where('del', 0);
  if (count($data) > 0) {
   foreach ($data as $key => $value) {
    $this->where($key, $value);
   }
  }
  $query = $this->get('history');
  return $query->num_rows > 0 ? $query->rows : false;
 }

 public function groupDateHistory()
 {
  $sql   = "SELECT date_purchase FROM mb_master_history WHERE del=0 GROUP BY date_purchase ORDER BY date_purchase DESC;";
  $query = $this->query($sql);
  return $query->rows;
 }

 public function groupGroup()
 {
  $sql   = "SELECT h.id_group, g.group_code FROM mb_master_history h LEFT JOIN mb_master_group g ON g.id_group=h.id_group WHERE h.del=0 GROUP BY h.id_group;";
  $query = $this->query($sql);
  return $query->rows;
 }
}
