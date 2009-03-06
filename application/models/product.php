<?php

class Product_Model extends Model
{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_productinfo($productid)
	{
		include('products_data/products.php');
		
		return $products[$productid];
	}
	
	public function get_all()
	{
		include('products_data/products.php');
		
		return $products;
	}

}

?>