<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('admin_url'))
{
	/**
	 * Admin URL
	 *
	 * Create a local URL based on your basepath. Segments can be passed via the
	 * first parameter either as a string or an array.
	 *
	 * @param	string	$uri
	 * @param	string	$protocol
	 * @return	string
	 */
	function admin_url($uri = '', $protocol = NULL)
	{
		return get_instance()->config->admin_url($uri, $protocol);
	}
}