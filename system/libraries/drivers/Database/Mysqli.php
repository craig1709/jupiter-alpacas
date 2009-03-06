<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQLi Database Driver
 *
 * $Id: Mysqli.php 1931 2008-02-05 22:56:30Z PugFish $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Database_Mysqli_Driver extends Database_Mysql_Driver {

	// Database connection link
	protected $link;
	protected $db_config;
	protected $statements = array();

	/**
	 * Sets the config for the class.
	 *
	 * @param  array  database configuration
	 */
	public function __construct($config)
	{
		$this->db_config = $config;

		Log::add('debug', 'MySQLi Database Driver Initialized');
	}

	/**
	 * Closes the database connection.
	 */
	public function __destruct()
	{
		is_object($this->link) and $this->link->close();
	}

	public function connect()
	{
		// Check if link already exists
		if (is_object($this->link))
			return $this->link;

		// Import the connect variables
		extract($this->db_config['connection']);

		// Build the connection info
		$host = (isset($host)) ? $host : $socket;

		// Make the connection and select the database
		if ($this->link = new mysqli($host, $user, $pass, $database))
		{
			if ($charset = $this->db_config['character_set'])
			{
				$this->set_charset($charset);
			}

			// Clear password after successful connect
			$this->config['connection']['pass'] = NULL;

			return $this->link;
		}

		return FALSE;
	}

	public function query($sql)
	{
		// Only cache if it's turned on, and only cache if it's not a write statement
		if ($this->db_config['cache'] AND ! preg_match('#\b(?:INSERT|UPDATE|REPLACE|SET)\b#i', $sql))
		{
			$hash = $this->query_hash($sql);

			if ( ! isset(self::$query_cache[$hash]))
			{
				// Set the cached object
				self::$query_cache[$hash] = new Kohana_Mysqli_Result($this->link, $this->db_config['object'], $sql);
			}

			// Return the cached query
			return self::$query_cache[$hash];
		}

		return new Kohana_Mysqli_Result($this->link, $this->db_config['object'], $sql);
	}

	public function set_charset($charset)
	{
		if ($this->link->set_charset($charset) === FALSE)
			throw new Kohana_Database_Exception('database.error', $this->show_error());
	}

	public function stmt_prepare($sql = '')
	{
		is_object($this->link) or $this->connect();
		return new Kohana_Mysqli_Statement($sql, $this->link);
	}

	public function escape_str($str)
	{
		is_object($this->link) or $this->connect();

		return $this->link->real_escape_string($str);
	}

	public function show_error()
	{
		return $this->link->error;
	}

	public function field_data($table)
	{
		$query  = $this->link->query('SHOW COLUMNS FROM '.$this->escape_table($table));

		$table  = array();
		while ($row = $query->fetch_object())
		{
			$table[] = $row;
		}

		return $table;
	}

} // End Database_Mysqli_Driver Class

/**
 * MySQLi result.
 */
class Kohana_Mysqli_Result implements Database_Result, ArrayAccess, Iterator, Countable {

	// Result resource
	protected $result = NULL;
	protected $link = NULL;

	// Total rows and current row
	protected $total_rows  = FALSE;
	protected $current_row = FALSE;

	// Insert id
	protected $insert_id = FALSE;

	// Data fetching types
	protected $fetch_type  = 'mysqli_fetch_object';
	protected $return_type = MYSQLI_ASSOC;

	/**
	 * Sets up the result variables.
	 *
	 * @param  object    database link
	 * @param  boolean   return objects or arrays
	 * @param  string    SQL query that was run
	 */
	public function __construct($link, $object = TRUE, $sql)
	{
		$this->link = $link;

		if ( ! $this->link->multi_query($sql))
		{
			// SQL error
			throw new Kohana_Database_Exception('database.error', $this->link->error.' - '.$sql);
		}
		else
		{
			$this->result = $this->link->store_result();

			// If the query is an object, it was a SELECT, SHOW, DESCRIBE, EXPLAIN query
			if (is_object($this->result))
			{
				$this->current_row = 0;
				$this->total_rows  = $this->result->num_rows;
				$this->fetch_type = ($object === TRUE) ? 'fetch_object' : 'fetch_array';
			}
			elseif ($this->link->error)
			{
				// SQL error
				throw new Kohana_Database_Exception('database.error', $this->link->error.' - '.$sql);
			}
			else
			{
				// Its an DELETE, INSERT, REPLACE, or UPDATE query
				$this->insert_id  = $this->link->insert_id;
				$this->total_rows = $this->link->affected_rows;
			}
		}

		// Set result type
		$this->result($object);
	}

	/**
	 * Magic __destruct function, frees the result.
	 */
	public function __destruct()
	{
		if (is_object($this->result))
		{
			$this->result->free_result();

			// this is kinda useless, but needs to be done to avoid the "Commands out of sync; you
			// can't run this command now" error. Basically, we get all results after the first one
			// (the one we actually need) and free them.
			if (is_resource($this->link) AND $this->link->more_results())
			{
				do
				{
					if ($result = $this->link->store_result())
					{
						$result->free_result();
					}
				} while ($this->link->next_result());
			}
		}
	}

	public function result($object = TRUE, $type = MYSQLI_ASSOC)
	{
		$this->fetch_type = ((bool) $object) ? 'fetch_object' : 'fetch_array';

		// This check has to be outside the previous statement, because we do not
		// know the state of fetch_type when $object = NULL
		// NOTE - The class set by $type must be defined before fetching the result,
		// autoloading is disabled to save a lot of stupid overhead.
		if ($this->fetch_type == 'fetch_object')
		{
			$this->return_type = class_exists($type, FALSE) ? $type : 'stdClass';
		}
		else
		{
			$this->return_type = $type;
		}

		return $this;
	}

	public function result_array($object = NULL, $type = MYSQLI_ASSOC)
	{
		$rows = array();

		if (is_string($object))
		{
			$fetch = $object;
		}
		elseif (is_bool($object))
		{
			if ($object === TRUE)
			{
				$fetch = 'fetch_object';

				// NOTE - The class set by $type must be defined before fetching the result,
				// autoloading is disabled to save a lot of stupid overhead.
				$type = class_exists($type, FALSE) ? $type : 'stdClass';
			}
			else
			{
				$fetch = 'fetch_array';
			}
		}
		else
		{
			// Use the default config values
			$fetch = $this->fetch_type;

			if ($fetch == 'fetch_object')
			{
				$type = class_exists($type, FALSE) ? $type : 'stdClass';
			}
		}

		if ($this->result->num_rows)
		{
			// Reset the pointer location to make sure things work properly
			$this->result->data_seek(0);

			while ($row = $this->result->$fetch($type))
			{
				$rows[] = $row;
			}
		}

		return isset($rows) ? $rows : array();
	}

	public function insert_id()
	{
		return $this->insert_id;
	}

	public function list_fields()
	{
		$field_names = array();
		while ($field = $this->result->fetch_field())
		{
			$field_names[] = $field->name;
		}

		return $field_names;
	}
	// End Interface

	// Interface: Countable
	/**
	 * Counts the number of rows in the result set.
	 *
	 * @return  integer
	 */
	public function count()
	{
		return $this->total_rows;
	}
	// End Interface

	// Interface: ArrayAccess
	/**
	 * Determines if the requested offset of the result set exists.
	 *
	 * @param   integer  offset id
	 * @return  boolean
	 */
	public function offsetExists($offset)
	{
		if ($this->total_rows > 0)
		{
			$min = 0;
			$max = $this->total_rows - 1;

			return ($offset < $min OR $offset > $max) ? FALSE : TRUE;
		}

		return FALSE;
	}

	/**
	 * Retreives the requested query result offset.
	 *
	 * @param   integer  offset id
	 * @return  mixed
	 */
	public function offsetGet($offset)
	{
		// Check to see if the requested offset exists.
		if ( ! $this->offsetExists($offset))
			return FALSE;

		// Go to the offset
		$this->result->data_seek($offset);

		// Return the row
		$fetch = $this->fetch_type;
		return $this->result->$fetch($this->return_type);
	}

	/**
	 * Sets the offset with the provided value. Since you can't modify query result sets, this function just throws an exception.
	 *
	 * @param   integer  offset id
	 * @param   integer  value to set
	 * @throws  Kohana_Database_Exception
	 */
	public function offsetSet($offset, $value)
	{
		throw new Kohana_Database_Exception('database.result_read_only');
	}

	/**
	 * Unsets the offset. Since you can't modify query result sets, this function just throws an exception.
	 *
	 * @param   integer  offset id
	 * @throws  Kohana_Database_Exception
	 */
	public function offsetUnset($offset)
	{
		throw new Kohana_Database_Exception('database.result_read_only');
	}
	// End Interface

	// Interface: Iterator
	/**
	 * Retrieves the current result set row.
	 *
	 * @return  mixed
	 */
	public function current()
	{
		return $this->offsetGet($this->current_row);
	}

	/**
	 * Retreives the current row id.
	 *
	 * @return  integer
	 */
	public function key()
	{
		return $this->current_row;
	}

	/**
	 * Moves the result pointer ahead one step.
	 *
	 * @return  integer
	 */
	public function next()
	{
		return ++$this->current_row;
	}

	/**
	 * Moves the result pointer back one step.
	 *
	 * @return  integer
	 */
	public function prev()
	{
		return --$this->current_row;
	}

	/**
	 * Moves the result pointer to the beginning of the result set.
	 *
	 * @return  integer
	 */
	public function rewind()
	{
		return $this->current_row = 0;
	}

	/**
	 * Determines if the current result pointer is valid.
	 *
	 * @return  boolean
	 */
	public function valid()
	{
		return $this->offsetExists($this->current_row);
	}
	// End Interface

} // End Mysqli_Result Class

/**
 * MySQLi Prepared Statement (experimental)
 */
class Kohana_Mysqli_Statement {

	protected $link = NULL;
	protected $stmt;
	protected $var_names = array();
	protected $var_values = array();

	public function __construct($sql, $link)
	{
		$this->link = $link;

		$this->stmt = $this->link->prepare($sql);

		return $this;
	}

	public function __destruct()
	{
		$this->stmt->close();
	}

	// Sets the bind parameters
	public function bind_params($param_types, $params)
	{
		$this->var_names = array_keys($params);
		$this->var_values = array_values($params);
		call_user_func_array(array($this->stmt, 'bind_param'), array_merge($param_types, $var_names));

		return $this;
	}

	public function bind_result($params)
	{
		call_user_func_array(array($this->stmt, 'bind_result'), $params);
	}

	// Runs the statement
	public function execute()
	{
		foreach ($this->var_names as $key => $name)
		{
			$$name = $this->var_values[$key];
		}
		$this->stmt->execute();
		return $this->stmt;
	}
}