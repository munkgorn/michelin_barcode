<?php  
/**
 * Model [ReplaceClass]
 */
class [ReplaceClass]Model extends db
{
	public function getLists() 
	{
		$result = $this->get('[ReplaceDB]');
		return $result->rows;
	}

	public function getList($id)
	{
		$this->where('id', $id);
		$result = $this->get('[ReplaceDB]');
		return $result->row;
	}

	public function add($data=array())
	{
		return $this->insert('[ReplaceDB]', $data);
	}

	public function edit($data=array(), $id)
	{
		$this->where('id', $id);
		return $this->update('[ReplaceDB]', $data);
	}

	public function del($id)
	{
		$this->where('id', $id);
		return $this->delete('[ReplaceDB]');
	}
}
?>