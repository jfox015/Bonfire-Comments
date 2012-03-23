<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Comments extends Front_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		
	}

	//--------------------------------------------------------------------
	
	public function index()
	{
		Template::render();
	}
}

// End main module class