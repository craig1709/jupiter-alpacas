<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Default Kohana controller.
 */
class Welcome_Controller extends Controller {

	public function index()
	{
		$view = new View('welcome');
		$view->render(TRUE);
	}

}