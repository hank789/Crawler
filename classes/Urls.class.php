<?php 
	require_once("easyCRUD.class.php");

	class Urls  Extends Crud {
		
			# Your Table name 
			protected $table = 'urls';
			
			# Primary Key of the Table
			protected $pk	 = 'id';
			
			public function find_by_path($path) {
				$sql = "SELECT * FROM " . $this->table ." WHERE path= :path LIMIT 1";
				return $this->db->row($sql,array('path'=>$path));
			}
			
			public function find_by_post_date($date) {
				$sql = "SELECT * FROM " . $this->table ." WHERE link_post_date >= :link_post_date order by link_post_date asc";
				return $this->db->query($sql,array('link_post_date'=>$date));
			}
			
	}

?>