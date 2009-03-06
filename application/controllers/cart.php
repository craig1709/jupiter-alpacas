<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Default Kohana controller.
 */
class Cart_Controller extends Template_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		$this->template->header_2 = 'Your Shopping Basket';
		$this->template->page_content = new View('cart');
		$this->template->page_content->action = '/products/action/';
		$this->template->page_content->buttons = '<input type="submit" value="Update"> <input type="button" value="Checkout" onclick="document.location=\'/cart/confirm/\'">';
		$this->template->page = 'garments';
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}
	
	public function thankyou()
	{
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		$this->template->header_2 = 'Thankyou';
		$this->template->page_content = new View('thankyou');
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}
	
	public function confirm()
	{
		
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		$this->template->header_2 = 'Checkout Confirmation';
		$this->template->page_content = new View('cart_confirm');
		$this->template->page_content->action = 'https://www.paypal.com/cgi-bin/webscr';
		$this->template->page_content->buttons = '<input type="button" value="Modify" onclick="document.location=\'/cart/\'"> <input type="submit" name="action" value="Confirm Checkout">';
		$this->template->page = 'garments';
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}
}