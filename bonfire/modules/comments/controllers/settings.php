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

class Settings extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();

        $this->auth->restrict('Comments.Settings.View');

        if (!class_exists('Activity_model'))
        {
            $this->load->model('activities/Activity_model', 'activity_model', true);
        }
		$this->lang->load('comments');

	}
	
	//--------------------------------------------------------------------

	public function _remap($method) 
	{ 
		if (method_exists($this, $method))
		{
			$this->$method();
		}
	}
    //--------------------------------------------------------------------

    public function index()
    {
        if ($this->input->post('submit'))
        {
            if ($this->save_settings())
            {
                Template::set_message(lang('mod_settings_saved'), 'success');
                redirect(SITE_AREA .'/settings/comments');
            } else
            {
                Template::set_message(lang('md_settings_error'), 'error');
            }
        }
        // Read our current settings
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'comments');
		Template::set('settings', $settings);
		if (!isset($this->role_model)) 
		{
			$this->load->model('roles/role_model');
		}
		$roles = array();
		$tmpRoles = $this->role_model->select('role_id, role_name, default')->where('deleted', 0)->find_all();
		if (isset($tmpRoles) && is_array($tmpRoles) && count($tmpRoles))
		{
			foreach($tmpRoles as $role) 
			{
				$roles = $roles + array($role->role_id => $role->role_name);
			}
		}
		Template::set('roles',$roles);
		
        Template::set('toolbar_title', lang('mod_settings_title'));
        Template::set_view('comments/settings/index');
        Template::render();
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // !PRIVATE METHODS
    //--------------------------------------------------------------------

    private function save_settings()
    {

		$this->load->library('form_validation');

        $this->form_validation->set_rules('anonymous_comments', lang('cm_anonymous_comments'), 'numeric|strip_tags|trim|xss_clean');
        
        if ($this->form_validation->run() === false)
        {
            return false;
        }

		$data = array(
            array('name' => 'comments.anonymous_comments', 'value' => $this->input->post('anonymous_comments')),

        );
        //destroy the saved update message in case they changed update preferences.
        /*if ($this->cache->get('update_message'))
        {
            if (!is_writeable(FCPATH.APPPATH.'cache/'))
            {
                $this->cache->delete('update_message');
            }
        }*/

        // Log the activity
        $this->activity_model->log_activity($this->auth->user_id(), lang('mod_settings_saved').': ' . $this->input->ip_address(), 'comments');

        // save the settings to the DB
        $updated = $this->settings_model->update_batch($data, 'name');

        return $updated;

	}
}

// End Settings Class