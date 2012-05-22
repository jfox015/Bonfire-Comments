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

class Content extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('comments_model');
		$this->lang->load('comments');
        $this->auth->restrict('Comments.Content.View');

    }

	//--------------------------------------------------------------------
	
    public function index()
    {


		$modules = $this->comments_model->modules_with_comments();
        Template::set('modules', $modules);
				
        $offset = $this->uri->segment(5);

        // Do we have any actions?
        if ($action = $this->input->post('submit'))
        {
            $checked = $this->input->post('checked');

            switch(strtolower($action))
            {
                case 'approve':
                    $this->change_status($checked, 2);
                    break;
                case 'flag':
                    $this->change_status($checked, 3);
                    break;
                case 'mark as spam':
                    $this->change_status($checked, 4);
                    break;
                case 'reject':
                    $this->change_status($checked, 5);
                    break;
                case 'delete':
                    $this->delete($checked);
                    break;
            }
        }

        $where = array();
        $dbprefix = $this->db->dbprefix;

        // Filters
		$filter = $this->input->get('filter');
        switch($filter)
        {
            case 'submitted':
                $where['comments.status_id'] = 1;
                break;
            case 'flagged':
				$where['comments.status_id'] = 3;
                break;
            case 'spam':
                $where['comments.status_id'] = 4;
                break;
            case 'rejected':
				$where['comments.status_id'] = 5;
                break;
            case 'deleted':
                $where['comments.deleted'] = 1;
                break;
            case 'module':
                $module_name = (string)$this->input->get('module_name');
                $where['comments_threads.module'] = $module_name;
                foreach ($modules as $module)
                {
                    if ($module == $module_name)
                    {
                        Template::set('filter_module', $module_name);
                        break;
                    }
                }
                break;
            default:
                $where['comments.status_id'] = 2;
                $this->comments_model->where('comments.deleted', 0);
                break;
        }

        $this->load->helper('ui/ui');
        $this->load->helper('news/author');

		$this->comments_model->select('comments.*, comments_threads.module, list_comments_status.name as status_name');
		$this->comments_model->limit($this->limit, $offset)->where($where);
		$this->comments_model->join('comments_threads','comments_threads.id = comments.thread_id');
		$this->comments_model->join('list_comments_status','list_comments_status.id = comments.status_id');
		Template::set('comments', $this->comments_model->find_all());

        // Pagination
        $this->load->library('pagination');

        $this->comments_model->where($where);
        $this->comments_model->join('comments_threads','comments_threads.id = comments.thread_id');
        $total_comments = $this->comments_model->count_all();
		
        $this->pager['base_url'] = site_url(SITE_AREA .'/content/comments/index');
        $this->pager['total_rows'] = $total_comments;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 5;

        $this->pagination->initialize($this->pager);

		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set('toolbar_title', lang('cm_custom_header'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function delete($items)
	{
		if (empty($items))
		{
			$item_id = $this->uri->segment(5);

			if(!empty($item_id))
			{
				$items = array($item_id);
			}
		}

		$error = false;
		if (!empty($items))
		{
			$this->auth->restrict('Comments.Content.Moderate');

			foreach ($items as $id)
			{
				$item = $this->comments_model->find($id);

				if (isset($item))
				{
					if ($this->comments_model->delete($id))
					{
						$this->load->model('activities/Activity_model', 'activity_model');

						$this->activity_model->log_activity($this->current_user->id, lang('us_log_delete') . ' comment id: '.$id, 'comments');
						
						if (in_array('comments',module_list(true))) {
							modules::run('comments/purge_thread',$item->comments_thread_id);
						}
			
					} else {
						Template::set_message(lang('us_action_not_deleted'). $this->comments_model->error, 'error');
						$error = true;
					}
				}
				else 
				{
					Template::set_message(lang('us_no_comments'), 'error');
					$error = true;
				}
			}
		}
		else 
		{
				Template::set_message(lang('us_empty_id'), 'error');
				$error = true;
						
		}
		if (!$error) {
			Template::set_message('The comments selected were successfully deleted.', 'success');
		}
		redirect(SITE_AREA .'/content/comments');
	}

	//--------------------------------------------------------------------
	//	!PRIVATE FUNCTIONS
	//--------------------------------------------------------------------

	//--------------------------------------------------------------------

	private function change_status($items=false, $status_id = 1)
	{
		if (!$items)
		{
			return;
		}
		$this->auth->restrict('Comments.Content.Moderate');
		
		foreach ($items as $item_id)
		{
			$this->comments_model->update($item_id, array('status_id' => $status_id));
		}
	}
	
}
// End main module class