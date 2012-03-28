<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_comments extends Migration {
	
	public function up() 
	{
		$prefix = $this->db->dbprefix;
		
		$data = array(
			'name'        => 'Comments.Settings.Manage' ,
			'description' => 'Manage OOTP Online Settings and Content' 
		);
		$this->db->insert("{$prefix}permissions", $data);
		
		$permission_id = $this->db->insert_id();
		
		$this->db->query("INSERT INTO {$prefix}role_permissions VALUES(1, ".$permission_id.")");
		
		// Comment Threads
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`created_on` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`deleted` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('comments_threads');
		
		$this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0)");
       
		// Comments
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`thread_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`comment` varchar(2000) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`created_on` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`created_by` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`modified_on` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`anonymous_email` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`deleted` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('comments');
		
	}
	
	//--------------------------------------------------------------------
	
	public function down() 
	{
		$prefix = $this->db->dbprefix;
		
		$query = $this->db->query("SELECT permission_id FROM {$prefix}permissions WHERE name = 'Comments.Settings.Manage'");
		foreach ($query->result_array() as $row)
		{
			$permission_id = $row['permission_id'];
			$this->db->query("DELETE FROM {$prefix}role_permissions WHERE permission_id='$permission_id';");
		}
		//delete the permission
		$this->db->query("DELETE FROM {$prefix}permissions WHERE (name = 'Comments.Settings.Manage')");
		
		$this->dbforge->drop_table('comments');
		$this->dbforge->drop_table('comments_threads');

	}
	
	//--------------------------------------------------------------------
	
}