<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Default Kohana controller.
 */
class Services_Controller extends Template_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Specialist and Breeder';
		$this->template->menu = array('Home' => '/', 'Alpacas for Sale' => '/forsale/', 'Gallery' => '/gallery/', 'Services' => '/services/', 'Garments & Accessories' => '/garments/');
		$this->template->header_2 = 'Services and Advice';
		$this->template->page_content = new View('services');
		
		$this->template->show_sidebar = TRUE;
		$this->template->header_sidebar = 'Current Alpacas';
		$this->template->sidebar_content = new View('alpacas_sidebar');
	}

}