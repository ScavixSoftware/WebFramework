<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
 * Copyright (c) since 2013 Scavix Software Ltd. & Co. KG
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>
 *
 * @author PamConsult GmbH http://www.pamconsult.com <info@pamconsult.com>
 * @copyright 2007-2012 PamConsult GmbH
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
namespace ScavixWDF\Base;

use ScavixWDF\Localization\CultureInfo;

/**
 * Generic Argument wrapper class
 * 
 * Provides functionality to savely get values from the $_GET, $_POST,... superglobals.
 * Supports:
 * - GET
 * - POST
 * - COOKIE
 * - SERVER
 * - ENV
 * - SESSION
 * 
 * Use the shortcut methods if you want to query a specific (of the) variables (above).
 * Args::get(...), Args::post(...),...
 * You may use the Args::request method to query a mix of the superglobals defined by
 * the Args::setOrder() method (default is GPC -> Cookie overrides Post overrides Get). 
 * 
 * See method commets for details.
 */
class Args
{
	private static $_ignore_case = true;
	private static $_order = "GPC";
	private static $_buffer = array();
	private static $_ci = null;
	
	/**
	 * Returns a names variable
	 * 
	 * Queries the Superglobals for a variable named $name and optionally filters it
	 * for a specific type. Superglobals to be included in the query and their order
	 * may be given optionally.
	 * 
	 * For details on the possible values for $filter see Args::sanitize below.
	 * 
	 * @param string $name Name of the variable, if false a list of keys in the requested array is returned
	 * @param mixed $default Default value, null if none
	 * @param string $order Specific Superglobals order. NULL if default shall be used. See setOrder for details
	 * @param string|int $filter Specifies a filter to apply on the value. See description above
	 * @param mixed $filter_options Optional options for the $filter argument
	 * @return mixed Sanitized value or a list of requested array's keys
	 */
	public static function sanitized($name=false,$default=null,$order=null,$filter=null,$filter_options=null)
	{
		$order = is_null($order)?self::$_order:$order;
		if( !isset(self::$_buffer[$order]) )
		{
			self::$_buffer[$order] = array();
			for($i=0;$i<strlen($order);$i++)
			{
				switch( $order[$i] )
				{
					case "G": 
						self::$_buffer[$order] = array_merge(self::$_buffer[$order],self::$_ignore_case?array_change_key_case($_GET):$_GET);
						break;
					case "P": 
						self::$_buffer[$order] = array_merge(self::$_buffer[$order],self::$_ignore_case?array_change_key_case($_POST):$_POST); 
						break;
					case "C": 
						self::$_buffer[$order] = array_merge(self::$_buffer[$order],self::$_ignore_case?array_change_key_case($_COOKIE):$_COOKIE); 
						break;
					case "S": 
						self::$_buffer[$order] = array_merge(self::$_buffer[$order],self::$_ignore_case?array_change_key_case($_SERVER):$_SERVER); 
						break;
					case "E": 
						self::$_buffer[$order] = array_merge(self::$_buffer[$order],self::$_ignore_case?array_change_key_case($_ENV):$_ENV); 
						break;
					case "O": 
						self::$_buffer[$order] = array_merge(self::$_buffer[$order],self::$_ignore_case?array_change_key_case($_SESSION):$_SESSION); 
						break;
				}
			}
		}
		if( !$name )
			return array_keys(self::$_buffer[$order]);
		if( self::$_ignore_case )
			$name = strtolower($name);
		if( isset(self::$_buffer[$order][$name]) )
			return is_null($filter)?self::$_buffer[$order][$name]:self::sanitize(self::$_buffer[$order][$name],$filter,$filter_options);
		if( !is_null($default) )
			return is_null($filter)?$default:self::sanitize($default,$filter,$filter_options);
	}
	
	/**
	 * Sets ignore case flag.
	 * 
	 * If true, Args class will ignore the case of the argument names. If false will respect case.
	 * @param bool $ignore true|false
	 * @return void
	 */
	public static function setIgnoreCase($ignore)
	{
		if( self::$_ignore_case != $ignore )
			self::$_buffer = array();
		self::$_ignore_case = $ignore;
	}
	
	public static function clearBuffer()
	{
		self::$_buffer = array();
	}
	
	/**
	 * Sets the default superglobal query range.
	 * 
	 * Supported values and their meanings:
	 * G - GET
	 * P - POST
	 * C - COOKIE
	 * S - SERVER
	 * E - ENV
	 * O - SESSION (use O to have the S for PHP compatibility as it means SERVER there)
	 * 
	 * Note: GPC is default. This will use Cookie before Post before Get, so order is reversed!
	 * 
	 * @param string $order Order string (sample: GPCSEO)
	 * @return void
	 */
	public static function setOrder($order)
	{
		self::$_order = $order;
	}
	
	/**
	 * Sets a CultureInfo object.
	 * 
	 * This is optional but will allow you to parse inputs correctly in a given
	 * Cultures format. So if the user enters a number like this
	 * 1,042.23 (one thousant fourty two point twenty three) this is US formatting
	 * and can pe parsed successfully to 1042.23 if you provide an en-US culture.
	 * If not it will result in an invalid float value.
	 * @param CultureInfo $cultureInfo Ci to set
	 * @return void
	 */
	public static function setCultureInfo($cultureInfo)
	{
		self::$_ci = $cultureInfo;
	}
	
	/**
	 * Performs value sanitation.
	 * 
	 * Sanitizes a value with a given filter.
	 * Valid values for $filter are all PHP defined FILTER_SANITIZE_* constants or a string value.
	 * If $filter is one of those constants, $filter_options apply as in PHP documentation.
	 * See http://www.php.net/manual/en/filter.filters.sanitize.php for details on that.
	 * If $filter is a string see code below for details.
	 * Mentionable here:
	 * - 'array': Will treat value as an array. You may provide filter_options to
	 *            be a string (like 'int') defining a type for all array elements or
	 *            to be an array that should contain the same keys (count and name) as the
	 *            value and each filter_option[key] value defines another type.
	 *            Sample: $filter='array', $filter_options=array('int','bool','string')
	 * - 'object': Will treat the value as object and simply return it if it is one.
	 *             May also be a string defining the storage_id of an object in object store.
	 *             In that case restore_object($value) will be returned.
	 * 
	 * Another note: sanitize will fill the log with messages severity WARN if something unexpected
	 * happen. This is especially the case when default values are returned for invalid inputs.
	 * So have an eye on the logs!
	 * 
	 * @param mixed $value The value to be sanitized
	 * @param string|int $filter Type of filter
	 * @param mixed $filter_options Optional options for the filter
	 * @return mixed The sanitized value
	 */
	public static function sanitize($value,$filter,$filter_options=null)
	{
		if( is_string($filter) )
		{
			$filter = strtoupper($filter);
			switch( $filter )
			{
				case 'STRING': 
				case 'TEXT': 
				case 'STRIPPED':
				case 'VARCHAR':
					return filter_var($value,FILTER_SANITIZE_STRING);
				case 'URL': 
				case 'URI': 
					return filter_var($value,FILTER_SANITIZE_URL);
				case 'MAIL': 
				case 'EMAIL': 
					$value = filter_var($value,FILTER_SANITIZE_EMAIL);
					if( !preg_match("/^[a-zA-Z0-9,!#\$%&'\*\+\/=\?\^_`\{\|}~-]+(\.[a-zA-Z0-9,!#\$%&'\*\+\/=\?\^_`\{\|}~-]+)*@[a-zA-Z0-9-]+(\.[a-z0-9-]+)*\.([a-zA-Z]{2,})$/", $value) )
					{
						if( is_null($filter_options) || $filter_options=false )
						{
							log_warn("Invalid eMail address '$value'. Retuning empty string");
							return "";
						}
					}
					return $value;
				case 'INT': 
				case 'INTEGER': 
					if( intval($value)."" == "$value" )
						return intval($value);
					log_warn("Value '$value' is no valid '$filter'. Returning 0");
					return 0;
				case 'BOOL':
				case 'BOOLEAN':
					if( is_string($value) )
						if( $value == '' || $value == '0' || strtolower($value) == "false" )
							return false;
						else
							return true;
					return $value == true;
				case 'FLOAT':
				case 'DOUBLE':
					if( is_string($value) && !is_null(self::$_ci) )
						return self::$_ci->NumberFormat->StrToNumber($value);
					log_warn("No CultureInfo specified for '$filter'. Returning doubleval($value)");
					return doubleval($value);
				case 'CURRENCY':
					if( is_string($value) && !is_null(self::$_ci) )
						return self::$_ci->CurrencyFormat->StrToCurrencyValue($value);
					log_warn("No CultureInfo specified for '$filter'. Returning doubleval($value)");
					return doubleval($value);
				case 'ARRAY':
					if( is_array($value) )
					{
						if( !is_null($filter_options) )
						{
							if( is_string($filter_options) )
							{
								foreach( $value as $k=>$v )
									$value[$k] = self::sanitize($v,"$filter_options");
								return $value;
							}
							if( is_array($filter_options) )
							{
								foreach( $value as $k=>$v )
									if( isset($filter_options[$k]) )
										$value[$k] = self::sanitize($v,$filter_options[$k].'');
									else
										log_warn("Array elements filter not given for key '$k'. Leaving value unfiltered");
								return $value;
							}
						}
						return $value;
					}
					log_warn("Value is no array. Returning empty array");
					return array();
				case 'OBJECT':
					if( is_string($value) && in_object_storage($value) )
						return restore_object($value);
					if( is_object($value) )
						return $value;
					log_warn("Value is not an object nor in session storage. Returning NULL");
					return null;
			}
			log_warn("Unknown filter '$filter'. Returning unsanitized value '$value'");
			return $value;
		}
		return filter_var($value,$filter,$filter_options);
	}
	
	/**
	 * @shortcut To access ENV values.
	 */
	public static function env($name=false,$default=null,$filter=null,$filter_options=null)
	{
		return self::sanitized($name,$default,"E",$filter,$filter_options);
	}
	
	/**
	 * @shortcut To access GET values.
	 */
	public static function get($name=false,$default=null,$filter=null,$filter_options=null)
	{
		return self::sanitized($name,$default,"G",$filter,$filter_options);
	}
	
	/**
	 * @shortcut To access POST values.
	 */
	public static function post($name=false,$default=null,$filter=null,$filter_options=null)
	{
		return self::sanitized($name,$default,"P",$filter,$filter_options);
	}
	
	/**
	 * @shortcut To access COOKIE values.
	 */
	public static function cookie($name=false,$default=null,$filter=null,$filter_options=null)
	{
		return self::sanitized($name,$default,"C",$filter,$filter_options);
	}
	
	/**
	 * @shortcut To access SERVER values.
	 */
	public static function server($name=false,$default=null,$filter=null,$filter_options=null)
	{
		return self::sanitized($name,$default,"S",$filter,$filter_options);
	}
	
	/**
	 * @shortcut To access SESSION values.
	 */
	public static function session($name=false,$default=null,$filter=null,$filter_options=null)
	{
		return self::sanitized($name,$default,"O",$filter,$filter_options);
	}
	
	/**
	 * @shortcut To access REQUEST values.
	 * 
	 * Uses the default (or set via setOrder) superglobal query order.
	 */
	public static function request($name=false,$default=null,$filter=null,$filter_options=null)
	{
		return self::sanitized($name,$default,null,$filter,$filter_options);
	}
	
	/**
	 * Strips given tags from request data.
	 * 
	 * @return void
	 */
	public static function strip_tags()
	{
		$tags = cfg_getd('requestparam','tagstostrip',false);
		if( !$tags ) return;
		self::array_strip_tags($_GET);
		self::array_strip_tags($_POST);
		self::array_strip_tags($_COOKIE);
		$_REQUEST = array_merge($_GET, $_POST, $_COOKIE);
	}
	
   /**
	* @private
	* See http://www.php.net/manual/en/function.strip-tags.php#93567
	*/
   private static function array_strip_tags(&$params)
   {
	   $tags = cfg_getd('requestparam','tagstostrip',false);
	   if( !$tags )
		   return;

	   $size = sizeof($tags);
	   $keys = array_keys($tags);
	   $paramsize = sizeof($params);
	   $paramkeys = array_keys($params);

	   for ($j=0; $j<$paramsize; $j++)
	   {
		   for ($i=0; $i<$size; $i++)
		   {
			   $tag = $tags[$keys[$i]];
			   if(is_string($params[$paramkeys[$j]]))
			   {
				   if(stripos($params[$paramkeys[$j]], $tag) !== false)
					   $params[$paramkeys[$j]] = preg_replace('#</?'.$tag.'[^>]*>#is', '', $params[$paramkeys[$j]]);
			   }
			   elseif(is_array($params[$paramkeys[$j]]))
				   Args::array_strip_tags($params[$paramkeys[$j]]);
		   }
	   }
   }
}