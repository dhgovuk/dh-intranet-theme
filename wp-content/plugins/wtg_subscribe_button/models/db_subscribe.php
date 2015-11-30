<?php 

class db_subscribe {
	
	public $db;
	public $sql;
	public $result;

	function __construct($connector){

		$this->db = $connector;

	}

	public function add() {

		$this->result = $this->db->query($this->sql);	

		return $this;
	}

	public function get() {

		$this->result = $this->db->get_row($this->sql,ARRAY_A);	

		return $this;
	}

	public function getMeta($userid) {

		$this->sql = "SELECT * 
					FROM ".$this->db->prefix."usermeta 
					WHERE user_id = $userid AND meta_key = '_per_user_feeds_cats'";

		return $this;
	}

	public function createMeta($userid,$value) {

		$value = serialize($value);

		$this->sql = "INSERT INTO ".$this->db->prefix."usermeta
						(user_id,meta_key,meta_value)
						VALUES 
						($userid,'_per_user_feeds_cats','$value')";

		return $this;
	}

	public function updateMeta($userid,$value) {

		$value = serialize($value);

		$this->sql = "UPDATE ".$this->db->prefix."usermeta
						SET meta_value = '$value'
						WHERE user_id = $userid
						AND meta_key = '_per_user_feeds_cats'";

		return $this;

	}

	public function get_newsletter_cats($post)
	{
		$categories = get_the_category($post->ID);

		foreach ($categories as $key => $cat)
		{
			$flag = get_field('include_in_newsletter', $cat);

			if ($flag === 'never'||$flag === 'always')
			{
				unset($categories[$key]);
			}
		}

		return $categories;
	}

}

?>