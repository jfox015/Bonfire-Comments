<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Comments_model
	
*/

class Comments_model extends BF_Model 
{

	protected $table		= 'comments';
	protected $key			= 'id';
	protected $soft_deletes	= true;
	protected $date_format	= 'int';
	protected $set_created	= false;
	protected $set_modified = false;
	
	/*-----------------------------------------------
	/	PUBLIC FUNCTIONS
	/----------------------------------------------*/
	public function new_comments_thread() 
	{
		$this->db->insert('comments_threads',array('created_on'=>time()));
		return $this->db->insert_id();
	}
	/*-----------------------------------------------
	/	PRIVATE FUNCTIONS
	/----------------------------------------------*/

}