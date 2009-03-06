<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Default Kohana controller.
 */
class Gallery_Controller extends Template_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
	
		$gallery = new Gallery_Model;
		$images = $gallery->get_images();
		
		$imagesHtml = "\r\n<ul id=\"gallery\">\r\n";
		foreach($images as $image)
		{
			$caption = substr($image, 0, strrpos($image, '.'));
			$caption = str_replace($caption, '_', ' ');
			$imagesHtml .= "\r\n" . '<li><a class="thickbox" href="/images/gallery/' . $image . '" title="' . $caption .'"><img src="/images/timthumb.php5?src=' . urlencode('/images/gallery/' . $image) . '&amp;w=160" alt=""></a></li>';
		}
		$imagesHtml .= "\r\n</ul>";
		
		$data['images'] = $imagesHtml;
	
		$this->template->page_title = 'Jupiter Alpacas';
		$this->template->header_1 = 'Specialist and Breeder';
		$this->template->menu = array('Home' => '/', 'Alpacas for Sale' => '/forsale/', 'Gallery' => '/gallery/', 'Services' => '/services/', 'Garments & Accessories' => '/garments/');
		$this->template->header_2 = 'Gallery';
		$this->template->page_content = new View('gallery', $data);
		
		$this->template->show_sidebar = TRUE;
		$this->template->header_sidebar = 'Current Alpacas';
		$this->template->sidebar_content = new View('alpacas_sidebar');
	}

}