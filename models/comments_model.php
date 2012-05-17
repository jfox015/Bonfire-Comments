<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
	Copyright (c) 2012 Jeff Fox

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

/*
	Class: Comments_model
*/

class Comments_model extends BF_Model 
{

	protected $table		= 'comments';
	protected $key			= 'id';
	protected $soft_deletes	= true;
	protected $date_format	= 'int';
	protected $set_created	= true;
	protected $set_modified = true;
	
	/*-----------------------------------------------
	/	PUBLIC FUNCTIONS
	/----------------------------------------------*/
	
	public function delete($thread_id = false) 
	{
		if ($thread_id === false)
		{
			$this->error = "No thread ID was received.";
			return false;
		}
		$this->db->where('id',$thread_id)->delete('comments_threads');
		return parent::delete($thread_id);
	}
	
	public function get_comment_count($thread_id = false) 
	{
		if ($thread_id === false)
		{
			$this->error = "No thread ID was received.";
			return false;
		}
		return $this->db->where('id',$thread_id)->count_all_results('comments');
	}

    public function modules_with_comments()
    {
        $modules = array();
        $this->db->select('module')->like('name','comments_enabled','before');
        $query = $this->db->get('settings');
        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                array_push($modules,$row->module);
            }
        }
        $query->free_result();
        return $modules;
    }

	public function new_comments_thread($module_name = false)
	{
        if ($module_name === false)
        {
            $this->error = "A module name is required to register a new comment thread.";
            return false;
        }

        $this->db->insert('comments_threads',array('created_on'=>time(),'module'=>$module_name));
		return $this->db->insert_id();
	}
	/*-----------------------------------------------
	/	PRIVATE FUNCTIONS
	/----------------------------------------------*/

}