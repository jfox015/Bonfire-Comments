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

class Comments extends Front_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->model('comments_model');
		$this->lang->load('comments');
	}

	//--------------------------------------------------------------------
	
	public function index()
	{
		Template::render();
	}

	//--------------------------------------------------------------------

	/*
		Method:
			ajax_add() 
			
		Accepts a comment submission via Ajax .post() and returns a JSON reponse
		object.
		
		Parameters:
			thread_id	 - Comment thread id
			comment_txt	 - Comment text content
			author_id	 - Comment author user ID
			
		Return:
			JSON response object
	*/
	public function ajax_add() 
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		if ($this->input->post('items'))
		{
			$items = json_decode($this->input->post('items'));
			$data = array('thread_id'		=> $items->thread_id,
						  'comment'	 		=> (isset($items->comment_txt)) ? html_entity_decode(urldecode($items->comment_txt)) : '',
						  'created_by'	 	=> (isset($items->author_id)) ? $items->author_id : 0,
						  'anonymous_email' => (isset($items->anonymous_email)) ? $items->anonymous_email : '',
						  'status_id' 		=> 1
			);
			if ($data['created_by'] == 0 && empty($data['anonymous_email']))
			{
				$error = true;
				$status = lang('cm_identifier_err');
			}
			else
			{
				if (!empty($data['anonymous_email'])) {
					$this->load->helper('email');
					if (!valid_email($data['anonymous_email']))
					{
						$error = true;
						$status = lang('cm_email_err');
					}
				}
				// EDIT - 0.2 - Check if comments require approval and if not, auto approve it
				$settings = $this->settings_model->select('name,value')->find_all_by('module', 'comments');
				if (!isset($settings['comments.require_approval']) || (isset($settings['comments.require_approval']) && $settings['comments.require_approval'] == 0))
				{
					$data['status_id'] = 2;
				}
				if (!$error)
				{
					$this->comments_model->insert($data);
				}
			}
			$json_out['result']['items'] = $this->resolve_thread_data($this->comments_model->find_all_by('thread_id',$items->thread_id));
		}
		else
		{
			$error = true;
			$status = "Post Data was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));
	}

	//--------------------------------------------------------------------

	/*
		Method:
			ajax_get() 
			
		Returns a JSON object array of comment items for the given thread.
		
		Parameters:
			thread_id	 - Comment thread id
			
		Return:
			JSON response object
	*/
	public function ajax_get() 
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		$thread_id = $this->uri->segment(3);
		
		if (isset($thread_id) && !empty($thread_id)) 
		{
			$json_out['result']['items'] = $this->resolve_thread_data($this->comments_model->find_all_by('thread_id',$thread_id));
		}
		else
		{
			$error = true;
			$status = "Thread ID was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));
	}
	
	//--------------------------------------------------------------------

	/**
		purge_thread().
			
		Removes a thread and comments from the db.
		
		@param	$thread_id	int		Comment thread id
		@return				boolean	TRUE on success, FALSE on error
			
	*/
	public function purge_thread($thread_id = false) 
	{
		if ($thread_id === false) 
		{
			return false;
		}
		$this->comments_model->delete($thread_id);
	}
		
	//--------------------------------------------------------------------

	/**
		count_comments().
			
		Retusn a count of the number of comments for the passed thread_id.
		
		@param	$thread_id	int		Comment thread id
		@return				boolean	TRUE on success, FALSE on error
			
	*/
	public function count_comments($thread_id = false) 
	{
		if ($thread_id === false) 
		{
			return false;
		}
		return $this->comments_model->get_comment_count($thread_id);
	}
	
	//--------------------------------------------------------------------

	/*
		Method:
			thread_view() 
			
		Return a comment thread without an HTML submission form. This function will 
		normally be called via the modules::run() method passing in the thread_id
		
		Parameters:
			$thread_id	 - Comment thread id
			
		Return:
			HTML comments View content
			
	*/
	public function thread_view($thread_id = false) 
	{
		if ($thread_id === false) 
		{
			return false;
		}
		$this->comments_model->where('status_id', 2); // filter only approved
		$thread = $this->resolve_thread_data($this->comments_model->find_all_by('thread_id',$thread_id));
		$html_out = $this->load->view('thread_view',array('comments'=>$thread), true);
		Assets::add_js($this->load->view('thread_view_js',array('thread_id'=>$thread_id), true),'inline');
		return $html_out;
	}

	//--------------------------------------------------------------------

	/*
		Method:
			thread_view_with_form() 
			
		Draws a comment thread with HTML submission form. This function will 
		normally be called via the modules::run() method passing in the thread_id
		
		Parameters:
			$thread_id	 - Comment thread id
			
		Return:
			HTML comments View content
			
	*/
	public function thread_view_with_form($thread_id = false) 
	{
		if ($thread_id === false) 
		{
			return false;
		}
		$html_out = $this->thread_view($thread_id);
		
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'comments');
		// acessing userdata cookie
		$cookie = unserialize($this->input->cookie($this->config->item('sess_cookie_name')));
		$logged_in = isset ($cookie['logged_in']);
		unset ($cookie);
		$user_id = (isset($this->current_user)) ? $this->current_user->id : 0;
		$anonymous = (!$logged_in && $settings['comments.anonymous_comments'] == 1) ? 'true' : 'false';
		
		if ($logged_in || (!$logged_in && $anonymous == 'true'))
		{
			$html_out .= $this->load->view('form',array('anonymous'=>$anonymous), true);
			Assets::add_js($this->load->view('form_js',array('thread_id'=>$thread_id,'user_id'=>$user_id, 'anonymous'=>$anonymous), true),'inline');
			//$html_out .= $this->load->view('form_js',array('thread_id'=>$thread_id,'user_id'=>$user_id, 'anonymous'=>$anonymous), true);
		}
		return $html_out;
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

	//--------------------------------------------------------------------

	/*
		Method:
			resolve_thread_data() 
			
		Converts author id and created_on date values to readable content
		
		Parameters:
			$thread	 - Array of comment thread objects
			
		Return:
			Updated thread object with additional values
			
	*/
	private function resolve_thread_data($thread) 
	{
		if (!isset($this->author_model)) 
		{
			$this->load->model('news/author_model');
		}
		if (isset($thread) && is_array($thread) && count($thread)) {
			foreach($thread as $comment) {
				if (isset($comment->created_by) && $comment->created_by != 0)
				{
					$comment->creator = $this->author_model->find_author($comment->created_by);
				}
				else if (isset($comment->anonymous_email) && !empty($comment->anonymous_email))
				{
					$comment->creator = $comment->anonymous_email;
				}
				else
				{
					$comment->creator = lang('submitted_by_unknown');
				}
				$comment->created = date($this->config->item('log_date_format'),$comment->created_on);
			}
		}
		return $thread;
	}
}

// End main module class