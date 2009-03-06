<?php

class Gallery_Model extends Model
{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_images()
	{
		$dir = scandir('images/gallery/');
		array_shift($dir); //remove ./
		array_shift($dir); //remove ../
		
		return $dir;
	}

}

?>