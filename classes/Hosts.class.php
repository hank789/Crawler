<?php 
	require_once("easyCRUD.class.php");

	class Hosts  Extends Crud {
		
			# Your Table name 
			protected $table = 'hosts';
			
			# Primary Key of the Table
			protected $pk	 = 'id';
			
			public function find_by_host($host,$https,$port) {
				$sql = "SELECT * FROM " . $this->table ." WHERE host= :host and https= :https and port= :port LIMIT 1";
				return $this->db->row($sql,array('host'=>$host,'https'=>$https,'port'=>$port));
			}
	}

?>