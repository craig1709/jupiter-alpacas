<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Default Kohana controller.
 */
class Contact_Controller extends Template_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Specialist and Breeder';
		$this->template->menu = array('Home' => '/');
		$this->template->header_2 = 'Contact Us';
		$this->template->page_content = new View('contact');
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}

}