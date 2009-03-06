<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Number helper class.
 *
 * $Id: num.php 1710 2008-01-14 01:12:01Z PugFish $
 *
 * @package    Number Helper
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class num_Core {

	/**
	 * Round a number to the nearest nth
	 *
	 * @param   integer  number to round
	 * @param   integer  number to round to
	 * @return  integer
	 */
	public static function round($number, $nearest = 5)
	{
		return round($number / $nearest) * $nearest;
	}

}