<?php 
	class ContactModel extends db {
		public function save($data=array()) {
			return $this->insert('contact', $data);
		}
	}
?>