<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Allows a template to be automatically loaded and displayed. Display can be
 * dynamically turned off in the controller methods, and the template file
 * can be overloaded.
 *
 * To use it, declare your controller to extend this class:
 * `class Your_Controller extends Template_Controller`
 *
 * $Id: template.php 1911 2008-02-04 16:13:16Z PugFish $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Template_Controller extends Controller {

	// Template view name
	protected $template = 'template';

	// Default to no auto-rendering
	protected $auto_render = TRUE;

	/**
	 * Template loading and setup routine.
	 */
	public function __construct()
	{
		parent::__construct();

		// Load the template
		$this->template = new View($this->template);

		if ($this->auto_render === TRUE)
		{
			// Display the template immediately after the controller method
			Event::add('system.post_controller', array($this, '_display'));
		}
	}

	/**
	 * Display the loaded template.
	 */
	public function _display()
	{
		if ($this->auto_render === TRUE)
		{
			// Render the template when the class is destroyed
			$this->template->render(TRUE);
		}
	}

} // End Template_Controller