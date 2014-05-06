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
namespace ScavixWDF\Reflection;
use ScavixWDF\Localization\Localization;

/**
 * Allows to automatically pass REQUEST parameters to methods arguments.
 * 
 * <at>attribute[RequestParam('joe','string',false)]
 * in the doccomment will make the following usable:
 * function SomeMethod($joe){ log_debug($joe); }
 */
class RequestParamAttribute extends WdfAttribute
{
	var $Name = null;
	var $Type = null;
	var $Default = null;
	var $Filter = null;

	function __construct($name,$type=null,$default=null,$filter=null)
	{
		$this->Name = $name;
		$this->Type = $type;
		$this->Default = $default;
		$this->Filter = $filter;
		
		if( !is_null($this->Type) && is_null($this->Filter) )
		{
			switch( strtolower($this->Type) )
			{
				case 'string':
					$this->Filter = FILTER_SANITIZE_STRING;
					break;
			}
		}
	}

	/**
	 * Checks if the argument is optional
	 * 
	 * This is done by checking if there's a default value specified.
	 * @return bool true or false
	 */
	function IsOptional()
	{
		return isset($this->Default);
	}

	/**
	 * Checks a given array for data for this and updates another array accordingly
	 * 
	 * This is kind of internal, so will not be documented further. Only that it ensures typed data in the $args argument
	 * from the $data argument. We will most likely clean this procedure up in the future.
	 * @param array $data Combined request data
	 * @param array $args resulting typed values
	 * @return boolean|string true if everything went fine, an error string if not
	 */
	function UpdateArgs($data, &$args)
	{		
		global $CONFIG;
		if( $CONFIG['requestparam']['ignore_case'] )
		{
			$name = strtolower($this->Name);
			foreach( $data as $k=>$v )
			{
				unset($data[$k]);
				$data[strtolower($k)] = $v;
			}
		}
		else
			$name = $this->Name;

		if( isset($GLOBALS['routing_args']) && count($GLOBALS['routing_args'])>0 && !isset($data[$name]) )
			$data[$name] = array_shift($GLOBALS['routing_args']);

		if( !isset($data[$name]) )
		{
			if( !is_null($this->Default) )
			{
				$args[$this->Name] = $this->Default;
				return true;
			}
			$args[$this->Name] = null;
			return 'missing';
		}

		if( !isset($GLOBALS['request_param_detected_ci']) )
		{
			if( isset($CONFIG['requestparam']['ci_detection_func']) && function_exists($CONFIG['requestparam']['ci_detection_func']) )
				$GLOBALS['request_param_detected_ci'] = $CONFIG['requestparam']['ci_detection_func']();
			else
				$GLOBALS['request_param_detected_ci'] = Localization::detectCulture();
//			log_debug($CONFIG['requestparam']['ci_detection_func']);
		}
		$ci = $GLOBALS['request_param_detected_ci'];

		if( !is_null($this->Type) )
		{
			switch( strtolower($this->Type) )
			{
				case 'object':
					if( !in_object_storage($data[$name]) )
						return 'object not found';

					$args[$this->Name] = restore_object($data[$name]);
					return true;
				case 'array':
				case 'file':
					if( isset($data[$name]) && is_array($data[$name]) )
						$args[$this->Name] = $data[$name];
					return true;
				case 'string':
				case 'text':
					if( $this->Filter )
						$args[$this->Name] = filter_var($data[$name],$this->Filter,FILTER_FLAG_NO_ENCODE_QUOTES);
					else
						$args[$this->Name] = $data[$name];
					return true;
				case 'email':
					$args[$this->Name] = filter_var($data[$name],FILTER_SANITIZE_EMAIL);
					return true;
				case 'url':
				case 'uri':
					$args[$this->Name] = filter_var($data[$name],FILTER_SANITIZE_URL);
					return true;
				case 'int':
				case 'integer':
					if( intval($data[$name])."" != $data[$name] )
//						if( floatval($data[$name])."" != $data[$name] )
							return 'invalid int value';
					$args[$this->Name] = intval($data[$name]);
					return true;
				case 'float':
				case 'double':
				case 'currency':
					if( $data[$name]."" == "" && $this->IsOptional() )
					{
						$data[$name] = $this->Default;
						$args[$this->Name] = $this->Default;
						return true;
					}
					
//					if( isset($CONFIG['localization']['float_conversion']) )
//						$data[$name] = call_user_func($CONFIG['localization']['float_conversion'],$data[$name]);
//					else if( !is_float(floatval($data[$name])) )
//						$data[$name] = false;

					if( strtolower($this->Type) == 'currency' )
						$data[$name] = $ci->CurrencyFormat->StrToCurrencyValue($data[$name]);
					else
						$data[$name] = $ci->NumberFormat->StrToNumber($data[$name]);

					if( $data[$name] === false )
						return 'invalid float value';
					else
						$args[$this->Name] = $data[$name];
					return true;
				case 'bool':
				case 'boolean':
					if( $data[$name] == '' || $data[$name] == '0' || strtolower($data[$name]) == "false" )
						$args[$this->Name] = false;
					else
						$args[$this->Name] = true;
					return true;
			}
			return 'wrong type';
		}

		$args[$this->Name] = $data[$name];
		return true;
	}
}
