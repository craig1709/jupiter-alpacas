<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Default Kohana controller.
 */
class Products_Controller extends Template_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		
		$data['type'] = 'ALL';
		$this->template->header_2 = 'Products - ' . ucwords(strtolower($data['type']));
		$this->template->page_content = '

<div class="product_preview">
	<a href="/products/bags/">
		<img src="/images/timthumb.php5?src=/images/products/latte_bag.jpg&amp;h=120" alt="">
		Bags
	</a>
</div>

<div class="product_preview">
	<a href="/products/waistcoats/">
		<img src="/images/timthumb.php5?src=/images/products/cream_and_black_short_cardi_a.jpg&amp;h=120" alt="">
		Tops
	</a>
</div>

<div class="product_preview">
	<a href="/products/scarves/">
		<img src="/images/timthumb.php5?src=/images/products/cream_wide_scarf_a.jpg&amp;h=120" alt="">
		Scarves and Throws
	</a>
</div>';
		$this->template->page = 'garments';
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}
	
	public function bags()
	{
		$this->template->page_title = 'Jupiter Alpacas';
		$data['type'] = 'bags';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		
		$this->template->header_2 = 'Products - ' . ucwords(strtolower($data['type']));
		$this->template->page_content = new View('products', $data);
		$this->template->page = 'garments';
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}
	
	public function waistcoats()
	{
		$data['type'] = 'waistcoats';
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		
		$this->template->header_2 = 'Products - ' . ucwords(strtolower($data['type']));
		$this->template->page_content = new View('products', $data);
		$this->template->page = 'garments';
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}
	
	public function hats()
	{
		$data['type'] = 'hats';
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		
		$this->template->header_2 = 'Products - ' . ucwords(strtolower($data['type']));
		$this->template->page_content = new View('products', $data);
		$this->template->page = 'garments';
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}
	
	public function scarves()
	{
		$data['type'] = 'scarves';
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		$this->template->header_2 = 'Products - Scarves and Throws';
		$this->template->page_content = new View('products', $data);
		$this->template->page = 'garments';
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}
	
	public function offers()
	{
		$data['type'] = 'ALL';
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Luxury Garments and Accessories';
		$this->template->menu = array('Home' => '/', 'Overview' => '/garments/', 'Products' => '/products/', 'Your Basket' => '/cart/', 'Specialist & Breeder' => '/forsale/');
		$this->template->header_2 = 'Products - Special Offers';
		$this->template->page_content = new View('offers', $data);
		$this->template->page = 'garments';
		
		$this->template->show_sidebar = FALSE;
		$this->template->header_sidebar = '';
		$this->template->sidebar_content = '';
	}
	
	public function add($productid)
	{
		$curr_products = (!empty($_COOKIE['products'])) ? unserialize($_COOKIE['products']) : array();
		if (array_key_exists($productid, $curr_products)) {
			//the customer already has one of these
			$curr_products[$productid] = $curr_products[$productid]+1; //increase the quantity
			$products = $curr_products;
		} else {
			//the customer DOES NOT already have one of these
			$products = array_merge($curr_products, array($productid => 1)); //so add it to the basket
		}
		setcookie('products', serialize($products), 0, '/');
		url::redirect('/cart/');
	}
	
	public function remove($productid)
	{
		$curr_products = (!empty($_COOKIE['products'])) ? unserialize($_COOKIE['products']) : array();
		unset($curr_products[$productid]);
		$products = $curr_products;
		setcookie('products', serialize($products), 0, '/');
		url::redirect('cart/');
	}
	
	public function action()
	{
		$curr_products = unserialize($_COOKIE['products']);
		//print_r($_POST);
		foreach ($_POST as $key => $post) {
			if (substr($key, 0, 9) == 'quantity-') {
				$productid = substr($key, 9);
				//echo $productid . ' - ' . $post . '<br>';
				if ($post == 0) {
					//if the customer wants to remove this item
					unset($curr_products[$productid]);
				} else {
					$curr_products[$productid] = $post;
				}
			}
		}
		setcookie('products', serialize($curr_products), 0, '/');
		url::redirect('cart/');
	}

}