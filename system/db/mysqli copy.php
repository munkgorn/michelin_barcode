<?php
class db{
	public $db;
	private $sql;
	private $where;
	private $where_or;
	private $where_count;
	private $where_like;
	private $where_start;
	private $where_end;
	private $orderby;
	private $limit;
	private $join;
	private $prefix;
	private $select;
	private $lastquery;
	// public $check_open;
	function __construct(){
		$this->db = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_DB);
		$this->db->query('SET NAMES utf8');
		if (mysqli_connect_errno()) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    exit();
		}
		$this->db->set_charset("utf8");
		$this->db->query("SET SQL_MODE = ''");
		// $check_open = 1;

		$this->sql = "";
		$this->select = "*";
		$this->where = array();
		$this->where_or = array();
		$this->where_count = -1;
		$this->where_like = array();
		$this->where_start = -1;
		$this->where_end = -1;
		$this->orderby = "";
		$this->limit = "";
		$this->join = array();
		$this->prefix = PREFIX;
	}
	function __destruct(){
		// if($check_open==1){
			// $this->db->close();
		// }
    }
    public function escape($text_escape=''){
    	// var_dump($text_escape);
    	$return = '';
    	if(!is_array($text_escape)){
	    	$return = $this->db->real_escape_string($text_escape);
	    }
    	
    	return $return;
    }
    // public function real_escape_string($string){
    // 	return $this->db->real_escape_string($string);
    // }
    
    public function query($sql) {
    	// echo $sql;
		$query = $this->db->query($sql);
		if (!$this->db->errno) {
			if ($query instanceof \mysqli_result) {
				$data = array();

				while ($row = $query->fetch_assoc()) {
					$data[] = $row;
				}
				$result = new \stdClass();

				$query = $this->db->query("SELECT FOUND_ROWS()");

				$var = $query->fetch_row();
				$result->sql = $sql;
				if($var){
					$result->num_rows = $var[0];
				}else{
					$result->num_rows = 0;
				}
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;

				$query->close();

				return $result;
			} else {
				return true;
			}
		} else {
			// echo $this->db->error;
			trigger_error('<div style="position:fixed;width:100%;top:0px;background:#fff;left:0px;border:1px solid #3e3e3e;z-index:1;padding:10px;"><div><b>Error Database</b></div>'.$sql.'<br><b>Detail: <span style="color:red;">' . $this->db->error  . '</span></b></div>');
		}
		return $sql;
	}
	public function clean() {
		$this->sql = "";
		$this->select = "*";
		$this->where = array();
		$this->where_or = array();
		$this->where_count = -1;
		$this->where_like = array();
		$this->where_start = -1;
		$this->where_end = -1;
		$this->orderby = "";
		$this->limit = "";
		$this->join = array();
		$this->prefix = PREFIX;
	}
	public function set_prefix($name) {
		$this->prefix = $name;
	}
	public function order_by($sort, $type="asc") {
		$this->orderby = " ORDER BY $sort ".strtoupper($type);
	}
	public function limit($start,$limit) {
		$this->limit = " LIMIT $start,$limit";
	}
	public function where($name, $value, $type="=") {
		(int)$this->where_count++;
		$cut = "";
		if ($this->where_count>0) {
			$cut = "AND";
		}
		$g_start = ($this->where_start==$this->where_count) ? "(" : "";
		$this->where[] = " $cut $g_start $name $type '".$this->escape($value)."' ";
	}
	public function where_or($name, $value, $type="=") {
		(int)$this->where_count++;
		$cut = "";
		if ($this->where_count>0) {
			$cut = "OR";
		}
		$g_start = ($this->where_start==$this->where_count) ? "(" : "";
		$this->where[] = " $cut $g_start $name $type '".$this->escape($value)."' ";
	}
	public function where_like($name, $value, $type="AND") {
		(int)$this->where_count++;
		$cut = "";
		if ($this->where_count>0) {
			$cut = "$type";
		}
		$g_start = ($this->where_start==$this->where_count) ? "(" : "";
		$this->where[] = " $cut $g_start $name LIKE '".$this->escape($value)."'  ";
	}
	public function group_start() {
		$this->where_start = (int)$this->where_count+1;
	}
	public function group_end() {
		$this->where[] = ") ";
	}
	public function join($table, $condition, $type="LEFT") {
		$this->join[] = " $type JOIN ".$this->prefix."$table ON $condition";
	}
	public function select($select) {
		$this->select = $select;
	}
	public function get($table) {
		$sql = "SELECT $this->select FROM ".$this->prefix."$table";
		if (isset($this->join) && count($this->join)>0) {
			foreach ($this->join as $k => $j) {
				$sql .= $j;
			} 
		}
		if (count($this->where)>0||count($this->where_or)>0||count($this->like)>0) {
			$sql .= " WHERE";
		}

		if (!empty($this->where) && count($this->where)>0) {
			foreach ($this->where as $k => $w) {
				$sql .= " $w";
			}
		}

		if (isset($this->orderby) && !empty($this->orderby)) {
			$sql .= $this->orderby;
		}
		if (isset($this->limit) && !empty($this->limit)) {
			$sql .= $this->limit;
		}
		$result = $this->query($sql);
		$this->clean();
		$this->lastquery = $sql;
		return $result;
	}
	public function getdata($table,$where=NULL,$field=NULL,$order=NULL,$limit=NULL){
    	if($where!=NULL){$where="where ".$where;}
    	if($field==NULL){$field="*";}
    	if($order!=NULL){$order="order by $order";}
		if($limit!=NULL){$limit="limit $limit";}
    	$sql_txt = "select SQL_CALC_FOUND_ROWS ".$field." from ".PREFIX.$table." ".$where." ".$order." ".$limit;

    	$query = $this->db->query($sql_txt) or die("ERROR: ".$sql_txt);
    	if (!$this->db->errno) {
			if ($query instanceof \mysqli_result) {
				$data = array();

				while ($row = $query->fetch_assoc()) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$query = $this->db->query("SELECT FOUND_ROWS()");
				$var = $query->fetch_row();
				if($var){
					$result->num_rows = $var[0];
				}else{
					$result->num_rows = 0;
				}
				//$result->num_rows = $this->db->query("SELECT FOUND_ROWS()")->fetch_row()['0'];
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;

				$query->close();

				return $result;
			} else {
				return true;
			}
		} else {
			trigger_error('Error: ' . $this->db->error  . '<br />Error No: ' . $this->db->errno . '<br />' . $sql);
		}
		// echo "test";
		// var_dump($sql->rows); exit();
    	return $sql;
    }
    public function update($table,$input,$where=''){
    	$result = false;
		$update = 'update '.PREFIX.$table.' set';	
		$i=1;
		foreach($input as $key => $value){
			//$value = $this->db->real_escape_string($value);
			if($value==""){ $update .= " $key = NULL"; }else{
				$value = iconv(mb_detect_encoding($value, mb_detect_order(), true), "UTF-8", $value);
				$update .= " `$key` = '".$this->escape($value)."'";
			}
			if($i!=count($input)){ $update .= ","; }
			$i++;
		}
		if (!empty($this->where) && count($this->where)>0) {
			$update .= "WHERE ";
			foreach ($this->where as $k => $w) {
				$update .= " $w";
			}
		} else {
			$update .= " WHERE ".$where;
		}
		

		// echo $update;exit();

		$this->lastquery = $update;
		$query = $this->db->query($update) or die($this->db->error.'<br>'.$update);

		$fp = fopen(DOCUMENT_ROOT.'log/query_update.txt', 'a+');
		fwrite($fp, date('Y-m-d H:i:s').' : '.$update.PHP_EOL);
		fclose($fp);

		// echo $update;
		$result = ($query?true:false);
	    return $result;
	}
	public function insert($table,$input){
		$insert = 'insert into '.PREFIX.$table.' set';	
		$i=1;

		foreach($input as $key => $value){
			//$value = $this->db->real_escape_string($value);
			$insert .= " `$key` = '".$this->escape($value)."'";
			if($i!=count($input)){ $insert .= ","; }
			$i++;
		}
		$this->lastquery = $insert;
		$query = $this->db->query($insert) or die($this->db->error .'<br>'. $insert);

		$fp = fopen(DOCUMENT_ROOT.'log/query_insert.txt', 'a+');
		fwrite($fp, date('Y-m-d H:i:s').' : '.$insert.PHP_EOL);
		fclose($fp);

		if (!$this->db->errno) {
			$result = ($query?$this->getLastId():false);
		} else {
			trigger_error('Error: ' . $this->db->error  . '<br />Error No: ' . $this->db->errno . '<br />' . $insert);
		}

		$this->clean();
	    return $result;
	}
	public function delete($table,$where=''){
		if (!empty($where)||!empty($this->where)) {
			$result = false;
			if (!empty($where)) {
				$where = " WHERE $where ";
			} else if (!empty($this->where) && count($this->where)>0) {
				$where .= "WHERE ";
				foreach ($this->where as $k => $w) {
					$where .= " $w";
				}
			}
			$where = !empty($where) ? $where : $this->where;
			$delete = "DELETE FROM ".$this->prefix."$table $where";
			$this->lastquery = $delete;
			$query = $this->db->query($delete) or die("Error: ".$delete);
			if($query){ 	
	            $result = true;
	        }
			$this->clean();
	        return $result;
		}
		return false;
	}
	public function last_query() {
		return $this->lastquery;
	}
	public function getLastId() {
		return $this->db->insert_id;
	}
}
?>