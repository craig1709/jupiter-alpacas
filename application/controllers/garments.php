<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Default Kohana controller.
 */
class Garments_Controller extends Template_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		$this->template->header_2 = 'About our Luxury Garments';
		$this->template->page_content = new View('garments');
		$this->template->page = 'garments';
		
		$this->template->show_sidebar = TRUE;
		$this->template->header_sidebar = 'Links';
		$this->template->sidebar_content = new View('links');
	}

}