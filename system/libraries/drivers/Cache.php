<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cache driver interface
 */
interface Cache_Driver
{

	/**
	 * Set a cache item.
	 */
	public function set($id, $data, $tags, $expiration);

	/**
	 * Find all of the cache ids for a given tag.
	 */
	public function find($tag);

	/**
	 * Get a cache item.
	 */
	public function get($id);

	/**
	 * Delete cache items by id or tag.
	 */
	public function delete($id, $tag = FALSE);

	/**
	 * Deletes all expired cache items.
	 */
	public function delete_expired();

} // End Cache Driver