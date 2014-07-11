<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Feature_updates extends Migration {

    private $permission_array = array(
        'Comments.Content.View' => 'To view the comment content menu.',
        'Comments.Content.Moderate' => 'Moderate Comments Content.',
        'Comments.Settings.View' => 'View comments settings menu.',
    );
    public function up()
	{
		$prefix = $this->db->dbprefix;

        foreach ($this->permission_array as $name => $description)
        {
            $this->db->query("INSERT INTO {$prefix}permissions(name, description) VALUES('".$name."', '".$description."')");
            // give current role (or administrators if fresh install) full right to manage permissions
            $this->db->query("INSERT INTO {$prefix}role_permissions VALUES(1,".$this->db->insert_id().")");
        }
		$default_settings = "
			INSERT INTO `{$prefix}settings` (`name`, `module`, `value`) VALUES
			 ('comments.moderator_level', 'comments', '1'),
			 ('comments.require_approval', 'comments', '0');
		";
        $this->db->query($default_settings);

        $this->dbforge->add_column('comments_threads', array(
                'module'	=> array(
                'type'	=> 'VARCHAR',
                'constraint'	=> 255,
                'default'		=> ''
            )
        ));
		$this->dbforge->add_column('comments', array(
                'status_id'	=> array(
                'type'	=> 'int',
                'constraint'	=> 2,
                'default'		=> '0'
            )
        ));
		
		// Comments status List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_comments_status');

		$this->db->query("INSERT INTO {$prefix}list_comments_status VALUES(-1, 'Unknown')");
		$this->db->query("INSERT INTO {$prefix}list_comments_status VALUES(1, 'Submitted')");
		$this->db->query("INSERT INTO {$prefix}list_comments_status VALUES(2, 'Approved')");
		$this->db->query("INSERT INTO {$prefix}list_comments_status VALUES(3, 'Flagged')");
		$this->db->query("INSERT INTO {$prefix}list_comments_status VALUES(4, 'Spam')");
		$this->db->query("INSERT INTO {$prefix}list_comments_status VALUES(5, 'Rejected')");

    }
	
	//--------------------------------------------------------------------
	
	public function down() 
	{
        $prefix = $this->db->dbprefix;
        //delete the permission
        foreach ($this->permission_array as $name => $description)
        {
            $query = $this->db->query("SELECT permission_id FROM {$prefix}permissions WHERE name = '".$name."'");
            foreach ($query->result_array() as $row)
            {
                $permission_id = $row['permission_id'];
                $this->db->query("DELETE FROM {$prefix}role_permissions WHERE permission_id='$permission_id';");
            }
            //delete the role
            $this->db->query("DELETE FROM {$prefix}permissions WHERE (name = '".$name."')");
        }
        // remove the keys
		$this->db->query("DELETE FROM {$prefix}settings WHERE (name = 'comments.moderator_level' 
			OR name = 'require_approval'
		)");

        $this->dbforge->drop_column("comments_threads","module");
        $this->dbforge->drop_column("comments","status_id");
		$this->dbforge->drop_table('list_comments_status');
		
    }
	
	//--------------------------------------------------------------------
	
}