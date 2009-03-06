<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Date helper class.
 *
 * $Id: date.php 1970 2008-02-06 21:54:29Z Shadowhand $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class date_Core {

	/**
	 * Converts a UNIX timestamp to DOS format.
	 *
	 * @param   integer  UNIX timestamp
	 * @return  integer
	 */
	public static function unix2dos($timestamp = FALSE)
	{
		$timestamp = ($timestamp === FALSE) ? getdate() : getdate($timestamp);

		if ($timestamp['year'] < 1980)
		{
			return (1 << 21 | 1 << 16);
		}

		$timestamp['year'] -= 1980;

		// What voodoo is this? I have no idea... Geert can explain it though,
		// and that's good enough for me.
		return ($timestamp['year']    << 25 | $timestamp['mon']     << 21 |
		        $timestamp['mday']    << 16 | $timestamp['hours']   << 11 |
		        $timestamp['minutes'] << 5  | $timestamp['seconds'] >> 1);
	}

	/**
	 * Converts a DOS timestamp to UNIX format.
	 *
	 * @param   integer  DOS timestamp
	 * @return  integer
	 */
	public static function dos2unix($timestamp = FALSE)
	{
		$sec  = 2 * ($timestamp & 0x1f);
		$min  = ($timestamp >>  5) & 0x3f;
		$hrs  = ($timestamp >> 11) & 0x1f;
		$day  = ($timestamp >> 16) & 0x1f;
		$mon  = ($timestamp >> 21) & 0x0f;
		$year = ($timestamp >> 25) & 0x7f;

		return mktime($hrs, $min, $sec, $mon, $day, $year + 1980);
	}

	/**
	 * Returns the offset (in seconds) between two time zones.
	 * @see     http://php.net/timezones
	 *
	 * @param   string          timezone that to find the offset of
	 * @param   string|boolean  timezone used as the baseline
	 * @return  integer
	 */
	public static function offset($remote, $local = TRUE)
	{
		static $offsets;

		// Default values
		$remote = (string) $remote;
		$local  = ($local === TRUE) ? date_default_timezone_get() : (string) $local;

		// Cache key name
		$cache = $remote.$local;

		if (empty($offsets[$cache]))
		{
			// Create timezone objects
			$remote = new DateTimeZone($remote);
			$local  = new DateTimeZone($local);

			// Create date objects from timezones
			$time_there = new DateTime('now', $remote);
			$time_here  = new DateTime('now', $local);

			// Find the offset
			$offsets[$cache] = $remote->getOffset($time_there) - $local->getOffset($time_here);
		}

		return $offsets[$cache];
	}

	/**
	 * Number of seconds in a minute, incrementing by a step.
	 *
	 * @param   integer  amount to increment each step by, 1 to 30
	 * @param   integer  start value
	 * @param   integer  end value
	 * @return  array    A mirrored (foo => foo) array from 1-60.
	 */
	public static function seconds($step = 1, $start = 0, $end = 60)
	{
		// Always integer
		$step = (int) $step;

		$seconds = array();

		for ($i = $start; $i < $end; $i += $step)
		{
			$seconds[$i] = ($i < 10) ? '0'.$i : $i;
		}

		return $seconds;
	}

	/**
	 * Number of minutes in an hour, incrementing by a step.
	 *
	 * @param   integer  amount to increment each step by, 1 to 30
	 * @return  array    A mirrored (foo => foo) array from 1-60.
	 */
	public static function minutes($step = 5)
	{
		// Because there are the same number of minutes as seconds in this set,
		// we choose to re-use seconds(), rather than creating an entirely new
		// function. Shhhh, it's cheating! ;) There are several more of these
		// in the following methods.
		return date::seconds($step);
	}

	/**
	 * Number of hours in a day.
	 *
	 * @param   integer  amount to increment each step by
	 * @param   boolean  use 24-hour time
	 * @param   integer  the hour to start at
	 * @return  array    A mirrored (foo => foo) array from start-12 or start-23.
	 */
	public static function hours($step = 1, $long = FALSE, $start = NULL)
	{
		// Default values
		$step = (int) $step;
		$long = (bool) $long;
		$hours = array();

		// Set the default start if none was specified.
		if (is_null($start))
		{
			$start = ($long == FALSE) ? 1 : 0;
		}

		$hours = array();

		// 24-hour time has 24 hours, instead of 12
		$size = ($long == TRUE) ? 23 : 12;

		for ($i = $start; $i <= $size; $i += $step)
		{
			$hours[$i] = $i;
		}

		return $hours;
	}

	/**
	 * Returns AM or PM, based on a given hour.
	 *
	 * @param   integer  number of the hour
	 * @return  string
	 */
	public static function ampm($hour)
	{
		// Always integer
		$hour = (int) $hour;

		return ($hour > 11) ? 'PM' : 'AM';
	}

	/**
	 * Adjusts a non-24-hour number into a 24-hour number.
	 *
	 * @param   integer  hour to adjust
	 * @param   string   AM or PM
	 * @return  string
	 */
	public static function adjust($hour, $ampm)
	{
		$hour = (int) $hour;
		$ampm = strtolower($ampm);

		switch($ampm)
		{
			case 'am':
				if ($hour == 12)
					$hour = 0;
			break;
			case 'pm':
				if ($hour < 12)
					$hour += 12;
			break;
		}

		return sprintf('%02s', $hour);
	}

	/**
	 * Number of days in month.
	 *
	 * @param   integer  number of month
	 * @param   integer  number of year to check month, defaults to the current year
	 * @return  array    A mirrored (foo => foo) array of the days.
	 */
	public static function days($month, $year = FALSE)
	{
		static $months;

		// Always integers
		$month = (int) $month;
		$year  = (int) $year;

		// Use the current year by default
		$year  = ($year == FALSE) ? date('Y') : $year;

		// We use caching for months, because time functions are used
		if (empty($months[$year][$month]))
		{
			$months[$year][$month] = array();

			// Use date to find the number of days in the given month
			$total = date('t', mktime(1, 0, 0, $month, 1, $year)) + 1;

			for ($i = 1; $i < $total; $i++)
			{
				$months[$year][$month][$i] = $i;
			}
		}

		return $months[$year][$month];
	}

	/**
	 * Number of months in a year
	 *
	 * @return  array  A mirrored (foo => foo) array from 1-12.
	 */
	public static function months()
	{
		return date::hours();
	}

	/**
	 * Returns an array of years between a starting and ending year.
	 * Uses the current year +/- 5 as the max/min.
	 *
	 * @param   integer  starting year
	 * @param   integer  ending year
	 * @return  array
	 */
	public static function years($start = FALSE, $end = FALSE)
	{
		// Default values
		$start = ($start == FALSE) ? date('Y') - 5 : (int) $start;
		$end   = ($end   == FALSE) ? date('Y') + 5 : (int) $end;

		$years = array();

		// Add one, so that "less than" works
		$end += 1;

		for ($i = $start; $i < $end; $i++)
		{
			$years[$i] = $i;
		}

		return $years;
	}

	/**
	 * Returns time difference between two timestamps, in human readable format.
	 *
	 * @param   integer       timestamp
	 * @param   integer       timestamp, defaults to the current time
	 * @param   string        formatting string
	 * @return  string|array
	 */
	public static function timespan($time1, $time2 = FALSE, $output = 'years,months,weeks,days,hours,minutes,seconds')
	{
		// Default values
		$time1  = max(0, (int) $time1);
		$time2  = ($time2 === FALSE) ? time() : max(0, (int) $time2);

		// Calculate timespan (seconds)
		$timespan = abs($time1 - $time2);

		// Array with the output formats
		$output = preg_split('/[\s,]+/', strtolower((string) $output));
		$output = array_combine($output, $output);

		// Array of diff values
		$timediff = array();

		// Years ago
		if (isset($output['years']))
		{
			// 60 * 60 * 24 * 365
			$year = 31536000;
			$timediff['years'] = (int) floor($timespan / $year);
			$timespan -= $timediff['years'] * $year;
		}

		// Months ago
		if (isset($output['months']))
		{
			// 60 * 60 * 24 * 30
			$month = 2592000;
			$timediff['months'] = (int) floor($timespan / $month);
			$timespan -= $timediff['months'] * $month;
		}

		// Weeks ago
		if (isset($output['weeks']))
		{
			// 60 * 60 * 24 * 7
			$week = 604800;
			$timediff['weeks'] = (int) floor($timespan / $week);
			$timespan -= $timediff['weeks'] * $week;
		}

		// Days ago
		if (isset($output['days']))
		{
			// 60 * 60 * 24
			$day = 86400;
			$timediff['days'] = (int) floor($timespan / $day);
			$timespan -= $timediff['days'] * $day;
		}

		// Hours ago
		if (isset($output['hours']))
		{
			// 60 * 60
			$hour = 3600;
			$timediff['hours'] = (int) floor($timespan / $hour);
			$timespan -= $timediff['hours'] * $hour;
		}

		// Minutes ago
		if (isset($output['minutes']))
		{
			// 60
			$minute = 60;
			$timediff['minutes'] = (int) floor($timespan / $minute);
			$timespan -= $timediff['minutes'] * $minute;
		}

		// Seconds ago
		if (isset($output['seconds']))
		{
			$timediff['seconds'] = $timespan;
		}

		// Invalid output formats string
		if (empty($timediff))
			return FALSE;

		// If only one output format was asked, don't put it in an array
		if (count($timediff) == 1)
			return current($timediff);

		// Return array
		return $timediff;
	}

} // End date