<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) since 2012 Scavix Software Ltd. & Co. KG
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
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
use ScavixWDF\WdfException;

if( !defined('FRAMEWORK_LOADED') || FRAMEWORK_LOADED != 'uSI7hcKMQgPaPKAQDXg5' ) die('');

define("HOOK_POST_INIT",1);
define("HOOK_POST_INITSESSION",2);
define("HOOK_PRE_CONSTRUCT",9);
define("HOOK_PRE_EXECUTE",3);
define("HOOK_PRE_RENDER",8);
define("HOOK_POST_EXECUTE",4);
define("HOOK_PRE_FINISH",5);
define("HOOK_POST_MODULE_INIT",6);
define("HOOK_PING_RECIEVED",7);
define("HOOK_ARGUMENTS_PARSED",300);
define("HOOK_SYSTEM_DIE",999);

/**
 * Some quick markers to be able to switch application behaviour.
 * Typical code sits in config.php (that's why this block is defined here)
 * and looks like this:
 * 
 */
define("ENVIRONMENT_DEV",'dev');
define("ENVIRONMENT_BETA",'beta');
define("ENVIRONMENT_SANDBOX",'sandbox');
define("ENVIRONMENT_LIVE",'live');
if( !isset($_ENV['CURRENT_ENVIRONMENT']) )
    $_ENV['CURRENT_ENVIRONMENT'] = ENVIRONMENT_LIVE;

/**
 * Sets the environment
 * 
 * Possible values are ENVIRONMENT_DEV, ENVIRONMENT_BETA, ENVIRONMENT_SANDBOX or ENVIRONMENT_LIVE
 * @param string $value The new value
 * @return void
 */
function setEnvironment($value){ $_ENV['CURRENT_ENVIRONMENT'] = $value; }

/**
 * Returns the currently set environment
 * 
 * Possible values are ENVIRONMENT_DEV, ENVIRONMENT_BETA, ENVIRONMENT_SANDBOX or ENVIRONMENT_LIVE
 * @return string The current environment
 */
function getEnvironment(){ return $_ENV['CURRENT_ENVIRONMENT']; }

/**
 * Shortcut for <setEnvironment>(ENVIRONMENT_DEV);
 * 
 * see there for more details
 * @return void
 */
function switchToDev(){ $_ENV['CURRENT_ENVIRONMENT'] = ENVIRONMENT_DEV; }
/**
 * Shortcut for <setEnvironment>(ENVIRONMENT_BETA);
 * 
 * see there for more details
 * @return void
 */
function switchToBeta(){ $_ENV['CURRENT_ENVIRONMENT'] = ENVIRONMENT_BETA; }
/**
 * Shortcut for setEnvironment(ENVIRONMENT_SANDBOX);
 * 
 * see there for more details
 * @return void
 */
function switchToSandbox(){ $_ENV['CURRENT_ENVIRONMENT'] = ENVIRONMENT_SANDBOX; }
/**
 * Shortcut for setEnvironment(ENVIRONMENT_LIVE);
 * 
 * see there for more details
 * @return void
 */
function switchToLive(){ $_ENV['CURRENT_ENVIRONMENT'] = ENVIRONMENT_LIVE; }
/**
 * Checks current environment
 * 
 * Checks if current environment is ENVIRONMENT_DEV
 * @return bool true or false
 */
function isDev(){ return $_ENV['CURRENT_ENVIRONMENT'] == ENVIRONMENT_DEV; }
/**
 * Checks current environment
 * 
 * Checks if current environment is ENVIRONMENT_BETA
 * @return bool true or false
 */
function isBeta(){ return $_ENV['CURRENT_ENVIRONMENT'] == ENVIRONMENT_BETA; }
/**
 * Checks current environment
 * 
 * Checks if current environment is ENVIRONMENT_SANDBOX
 * @return bool true or false
 */
function isSandbox(){ return $_ENV['CURRENT_ENVIRONMENT'] == ENVIRONMENT_SANDBOX; }
/**
 * Checks current environment
 * 
 * Checks if current environment is ENVIRONMENT_LIVE
 * @return bool true or false
 */
function isLive(){ return $_ENV['CURRENT_ENVIRONMENT'] == ENVIRONMENT_LIVE; }
/**
 * Checks current environment
 * 
 * Checks if current environment is not ENVIRONMENT_LIVE
 * @return bool true or false
 */
function isNotLive(){ return $_ENV['CURRENT_ENVIRONMENT'] != ENVIRONMENT_LIVE; }
/**
 * Checks current environment
 * 
 * Checks if current environment is ENVIRONMENT_DEV or ENVIRONMENT_BETA
 * @return bool true or false
 */
function isDevOrBeta(){ return $_ENV['CURRENT_ENVIRONMENT'] == ENVIRONMENT_DEV || $_ENV['CURRENT_ENVIRONMENT'] == ENVIRONMENT_BETA; }

/**
 * Sets a config value.
 * 
 * uses given arguments for key path like this:
 * <code php>
 * cfg_set('system','use_cfg','really',true);
 * // will set
 * $CONFIG['system']['use_cfg']['really'] = true;
 * </code>
 * measured performance agains direct assignment: it is about 5 times
 * slower on a Windows7 x64 system with 8GB RAM.
 * But for 1000 calls it just needs 5ms, so just leave me alone with that.
 * @return void
 */
function cfg_set()
{
	global $CONFIG;
	$args = func_get_args();
	switch( func_num_args() )
	{
		case 2: $CONFIG[$args[0]] = $args[1]; break;
		case 3: $CONFIG[$args[0]][$args[1]] = $args[2]; break;
		case 4: $CONFIG[$args[0]][$args[1]][$args[2]] = $args[3]; break;
		case 5: $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]] = $args[4]; break;
		case 6: $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]] = $args[5]; break;
		case 7: $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]] = $args[6]; break;
		default: WdfException::Raise("Illegal argument count: ".count($args));
	}
}

/**
 * Sets a config value only if it has not been set
 * 
 * See cfg_set() for usage and performance thoughts
 * @return void
 */
function cfg_setd()
{
	global $CONFIG;
	$args = func_get_args();
	switch( func_num_args() )
	{
		case 2: if( !isset($CONFIG[$args[0]]) ) $CONFIG[$args[0]] = $args[1]; break;
		case 3: if( !isset($CONFIG[$args[0]][$args[1]]) ) $CONFIG[$args[0]][$args[1]] = $args[2]; break;
		case 4: if( !isset($CONFIG[$args[0]][$args[1]][$args[2]]) ) $CONFIG[$args[0]][$args[1]][$args[2]] = $args[3]; break;
		case 5: if( !isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]]) ) $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]] = $args[4]; break;
		case 6: if( !isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]]) ) $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]] = $args[5]; break;
		case 7: if( !isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]]) ) $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]] = $args[6]; break;
		default: WdfException::Raise("Illegal argument count: ".count($args));
	}
}

/**
 * Adds an entry to a config value array
 * 
 * See cfg_set() for usage and performance thoughts
 * @return void
 */
function cfg_add()
{
	global $CONFIG;
	$args = func_get_args();
	switch( func_num_args() )
	{
		case 2: $CONFIG[$args[0]][] = $args[1]; break;
		case 3: $CONFIG[$args[0]][$args[1]][] = $args[2]; break;
		case 4: $CONFIG[$args[0]][$args[1]][$args[2]][] = $args[3]; break;
		case 5: $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][] = $args[4]; break;
		case 6: $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][] = $args[5]; break;
		case 7: $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]][] = $args[6]; break;
		default: WdfException::Raise("Illegal argument count: ".count($args));
	}
}

/**
 * Gets a config value.
 * 
 * See cfg_set() for usage and performance thoughts
 * @return mixed Config value
 */
function cfg_get()
{
	global $CONFIG;
	$args = func_get_args();
	switch( func_num_args() )
	{
		case 1: return isset($CONFIG[$args[0]])?$CONFIG[$args[0]]:false;
		case 2: return isset($CONFIG[$args[0]][$args[1]])?$CONFIG[$args[0]][$args[1]]:false;
		case 3: return isset($CONFIG[$args[0]][$args[1]][$args[2]])?$CONFIG[$args[0]][$args[1]][$args[2]]:false;
		case 4: return isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]])?$CONFIG[$args[0]][$args[1]][$args[2]][$args[3]]:false;
		case 5: return isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]])?$CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]]:false;
		case 6: return isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]])?$CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]]:false;
		default: WdfException::Raise("Illegal argument count: ".count($args));
	}
}

/**
 * Gets a config value and uses the last argument given as default if it is not set.
 * 
 * See cfg_set() for usage and performance thoughts
 * @return mixed Config value
 */
function cfg_getd()
{
	global $CONFIG;
	$args = func_get_args();
	switch( func_num_args() )
	{
        case 2: return isset($CONFIG[$args[0]])?$CONFIG[$args[0]]:$args[1];
		case 3: return isset($CONFIG[$args[0]][$args[1]])?$CONFIG[$args[0]][$args[1]]:$args[2];
		case 4: return isset($CONFIG[$args[0]][$args[1]][$args[2]])?$CONFIG[$args[0]][$args[1]][$args[2]]:$args[3];
		case 5: return isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]])?$CONFIG[$args[0]][$args[1]][$args[2]][$args[3]]:$args[4];
		case 6: return isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]])?$CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]]:$args[5];
		case 7: return isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]])?$CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]]:$args[6];
		default: WdfException::Raise("Illegal argument count: ".count($args));
	}
}

/**
 * Deletes a config value
 * 
 * See cfg_set() for usage and performance thoughts
 * @return void
 */
function cfg_del()
{
	global $CONFIG;
	$args = func_get_args();
	switch( func_num_args() )
	{
		case 1: unset($CONFIG[$args[0]]); break;
		case 2: unset($CONFIG[$args[0]][$args[1]]); break;
		case 3: unset($CONFIG[$args[0]][$args[1]][$args[2]]); break;
		case 4: unset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]]); break;
		case 5: unset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]]); break;
		case 6: unset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]]); break;
		default: WdfException::Raise("Illegal argument count: ".count($args));
	}
}

/**
 * Checks if a config is set and throws an exception if not.
 * 
 * Last argument will be used as exception message.
 * See cfg_set() for usage and performance thoughts
 * @return void
 */
function cfg_check()
{
	global $CONFIG;
	$args = func_get_args();
	switch( func_num_args() )
	{
		case 2: if( !isset($CONFIG[$args[0]]) || !$CONFIG[$args[0]] ) WdfException::Raise($args[1]); break;
		case 3: if( !isset($CONFIG[$args[0]][$args[1]]) || !$CONFIG[$args[0]][$args[1]] ) WdfException::Raise($args[2]); break;
		case 4: if( !isset($CONFIG[$args[0]][$args[1]][$args[2]]) || $CONFIG[$args[0]][$args[1]][$args[2]] ) WdfException::Raise($args[3]); break;
		case 5: if( !isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]]) || $CONFIG[$args[0]][$args[1]][$args[2]][$args[3]] ) WdfException::Raise($args[4]); break;
		case 6: if( !isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]]) || !$CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]] ) WdfException::Raise($args[5]); break;
		case 7: if( !isset($CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]]) || !$CONFIG[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]] ) WdfException::Raise($args[6]); break;
		default: WdfException::Raise("Illegal argument count: ".count($args));
	}
}

/**
 * Sets the application version.
 * 
 * Use this when you update your app to a new version. It will create a new
 * nocache argument too so that all dependent files will be reloaded by your clients.
 * Will also affect minify module!
 * @param int $major Major version
 * @param int $minor Minor version
 * @param int $build Build number
 * @param string $codename Codename (like 'alpha' or 'woohoo-wdf')
 * @param string $nc_salt Optional string to salt the nocache argument
 * @return void
 */
function setAppVersion($major,$minor,$build,$codename="",$nc_salt=false)
{
	$major = intval($major);
	$minor = intval($minor);
	$build = intval($build);
	$GLOBALS['APP_VERSION'] = compact('major','minor','build','codename');
	$GLOBALS['APP_VERSION']['string'] = "$major.$minor.$build";
	if( $codename )
		$GLOBALS['APP_VERSION']['string'] .= " ($codename)";
	$GLOBALS['APP_VERSION']['nc'] = 'nc'.substr(preg_replace('/[^0-9]/', '', md5($GLOBALS['APP_VERSION']['string'].$nc_salt)), 0, 8);
}

/**
 * Gets the application version.
 * 
 * If key is given, returns that part only.
 * @param string $key 'major','minor','build' or 'codename'
 * @return mixed Version array or the requested part of it
 */
function getAppVersion($key=false)
{
	if( !isset($GLOBALS['APP_VERSION']) )
		setAppVersion (0, 0, 0, "default");
	
	if( $key && isset($GLOBALS['APP_VERSION'][$key]) )
		return $GLOBALS['APP_VERSION'][$key];
	return $GLOBALS['APP_VERSION'];
}

/**
 * Check if SSL is in use
 * 
 * Returns true when the current request is SSL secured, else false
 * @return bool true or false
 */
function isSSL()
{
	return (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) || (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https");
}

/**
 * Returns current URL scheme
 * 
 * That is one of http, https, http:// or https:// 
 * @param bool $append_slashes If true appends '//' to the result
 * @return string The current scheme
 */
function urlScheme($append_slashes=false)
{
	if( $append_slashes )
		return isSSL()?"https://":"http://";
	return isSSL()?"https":"http";
}

/**
 * Ensures that the given path ends with a directory separator
 * 
 * As Windows works fine with '/' and all others use '/' we just use that instead
 * of `DIRECTORY_SEPARATOR`. That one actually makes problems in some cases.
 * @param string $path Path to be checked
 * @param bool $make_realpath If true calls realpath() on the `$path`
 * @return void
 */
function system_ensure_path_ending(&$path, $make_realpath=false)
{
	if( $make_realpath )
	{
		$p = realpath($path);
		if( $p ) $path = $p;
	}
    if( !ends_with($path, '/') )
        $path .= '/';
}

/**
 * Checks if a string starts with another one.
 * 
 * Shortcut for the lazy ones: `return strpos($string,$start) === 0`
 * You may also call this function with more parameters. In that case will check if
 * $string starts with any of the given strings: `$hit = starts_with('hello world','wow','rl','hello');`
 * @param string $string String to check
 * @param string $start The start to be checked
 * @return bool true or false
 */
function starts_with($string,$start)
{
	if( func_num_args() > 2 )
	{
		$args = func_get_args();
		array_shift($args);
		foreach( $args as $start )
			if( strpos($string,$start) === 0 )
				return true;
		return false;
	}
	return strpos($string,$start) === 0;
}

/**
 * @shortcut <starts_with>() but ignoring the case
 */
function starts_iwith($string,$start)
{
	if( func_num_args() > 2 )
	{
		$args = func_get_args();
		array_shift($args);
		foreach( $args as $start )
			if( stripos($string,$start) === 0 )
				return true;
		return false;
	}
	return stripos($string,$start) === 0;
}

/**
 * Checks if a string ends with another one.
 * 
 * Shortcut for the lazy ones: `return substr($string,strlen($string)-strlen($end)) == $end`
 * You may also call this function with more parameters. In that case will check if
 * $string ends with any of the given strings: `$hit = ends_with('hello world','wow','rl','ld');`
 * @param string $string String to check
 * @param string $end The end to be checked
 * @return bool true or false
 */
function ends_with($string,$end)
{
	if( func_num_args() > 2 )
	{
		$args = func_get_args();
		array_shift($args);
		foreach( $args as $end )
			if( substr($string,strlen($string)-strlen($end)) == $end )
				return true;
		return false;
	}
	return substr($string,strlen($string)-strlen($end)) == $end;
}

/**
 * Checks if a string ends with another one.
 * 
 * Same as <ends_with> but ignores case.
 * @param string $string String to check
 * @param string $end The end to be checked
 * @return bool true or false
 */
function ends_iwith($string,$end)
{
	$end = strtolower($end);
	if( func_num_args() > 2 )
	{
		$args = func_get_args();
		array_shift($args);
		foreach( $args as $end )
			if( strtolower(substr($string,strlen($string)-strlen($end))) == $end )
				return true;
		return false;
	}
	return strtolower(substr($string,strlen($string)-strlen($end))) == $end;
}

/**
 * Tests if the first given argument is one of the others.
 * 
 * Use like this: `is_in('nice','Hello','nice','World')`
 * This is a shortcut for `in_array('nice',array('Hello','nice','World'))`.
 * @return bool true or false
 */
function is_in()
{
	$args = func_get_args();
	$needle = array_shift($args);
	return in_array($needle,$args);
}

/**
 * Tests if the first given argument contains one of the others.
 * 
 * First argument may be an array or a string.
 * If array, all entries will be checked for equality with at least one of the other given arguments.
 * If string, <contains> performs a <stripos> check with each other given argument and returns true if at least one matched.
 * Use like this: 
 * <code php>
 * contains(array('Hello','nice','World'),'some','other','nice','words'); // true
 * contains('Hello nice World','some','other','nice','words'); // true
 * contains('Hello nice World','some','other','words'); // false
 * </code>
 * @return bool true or false
 */
function contains()
{
	$args = func_get_args();
	$array = array_shift($args);
	if( is_array($array) )
	{
		foreach( $args as $a )
			if( in_array($a,$array) )
				return true;
		return false;
	}
	if( is_string($array) )
	{
		foreach( $args as $a )
			if( stripos($array,$a) !== false )
				return true;
		return false;
	}
	WdfException::Raise('First argument needs to be of type array or string');
}

/**
 * Returns array value at key if it exists, else default is returned.
 * 
 * This is shortcut for `$val = (array_key_exists($key,$array) && $array[$key])?$array[$key]:$default;`
 * @param array $array The source array
 * @param mixed $key The key to be checked
 * @param mixed $default Default value to return if array does not contain key
 * @return mixed Result or `$default`
 */
function array_val($array,$key,$default=null)
{
	if( array_key_exists($key, $array) )
		return $array[$key];
	return $default;
}

/**
 * Checks if an array contains key and if the value is needle
 * 
 * This is shortcut for
 * <code php>
 * if( array_key_exists($key,$array) && $array[$key]==$needle  ) 
 *     ...;
 * </code>
 * @param array $array The source array
 * @param mixed $key The key to be checked
 * @param mixed $needle The value to check against
 * @return bool true or false
 */
function array_val_is($array,$key,$needle)
{
	if( array_key_exists($key, $array) )
		return $array[$key] == $needle;
	return false;
}

/**
 * Tests if 'we are' currently handling an ajax request
 * 
 * This is done by checking the `$_SERVER` variable and the request_id.
 * We set the request_id in plain requests in the SESSION and add it to AJAX requests so we can compare those two here.
 * @return bool true or false
 */
function system_is_ajax_call()
{
    if( php_sapi_name() == "cli" )
        $GLOBALS['result_of_system_is_ajax_call'] = false;
	if( !isset($GLOBALS['result_of_system_is_ajax_call']) )
	{
		$GLOBALS['result_of_system_is_ajax_call'] = strtolower(array_val($_SERVER, 'HTTP_X_REQUESTED_WITH', '')) == 'xmlhttprequest';
		if( !$GLOBALS['result_of_system_is_ajax_call'] )
		{
			if( !isset($_REQUEST['request_id']) || !isset($_SESSION['request_id']) )
			{
				unset($GLOBALS['result_of_system_is_ajax_call']);
				return false;
			}
			$GLOBALS['result_of_system_is_ajax_call'] = $_REQUEST['request_id'] == $_SESSION['request_id'];
		}
	}
	return $GLOBALS['result_of_system_is_ajax_call'];
}

/**
 * Strips given tags from string
 * 
 * See http://www.php.net/manual/en/function.strip-tags.php#93567
 * @param string $str String to strip
 * @param array $tags Tags to be stripped
 * @return string cleaned up string
 */
function strip_only(&$str, $tags)
{
	if(isset($str) && is_array($str))
		return $str;
    if(!is_array($tags))
	{
        $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
        if(end($tags) == '') array_pop($tags);
    }

	$size = sizeof($tags);
	$keys = array_keys($tags);
	for ($i=0; $i<$size; $i++)
	{
		$tag = $tags[$keys[$i]];
		if(isset($tag) && is_array($tag))
			$str = strip_only($str, $tag);
		else
		{
			if(stripos($str, $tag) !== false)
				$str = preg_replace('#</?'.$tag.'[^>]*>#is', '', $str);
		}
	}
	return $str;
}

/**
 * Returns the ordinal number for a char
 * 
 * Code 'stolen' from php.net ;)
 * The following uniord function is simpler and more efficient than any of the ones suggested without
 * depending on mbstring or iconv.
 * It's also more validating (code points above U+10FFFF are invalid; sequences starting with 0xC0 and 0xC1 are
 * invalid overlong encodings of characters below U+0080),
 * though not entirely validating, so it still assumes proper input.
 * See http://de3.php.net/manual/en/function.ord.php#77905
 * @param char $c Character to get ORD of
 * @return int The ORD code
 */
function uniord($c)
{
	$h = ord($c{0});
	if ($h <= 0x7F) {
		return $h;
	} else if ($h < 0xC2) {
		return false;
	} else if ($h <= 0xDF) {
		return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
	} else if ($h <= 0xEF) {
		return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
								 | (ord($c{2}) & 0x3F);
	} else if ($h <= 0xF4) {
		return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
								 | (ord($c{2}) & 0x3F) << 6
								 | (ord($c{3}) & 0x3F);
	} else {
		return false;
	}
}

/**
 * Here's a PHP function which does just that when given a UTF-8 encoded string.
 * 
 * It's probably not the best way to do it, but it works:
 * See http://www.iamcal.com/understanding-bidirectional-text/
 * Uncommented PDF correction because it's too weak and kills some currency symbols in CurrencyFormat::Format
 * @param string $data String to be cleaned up
 * @return string Cleaned up string
 */
function unicode_cleanup_rtl($data)
{
	#
	# LRE - U+202A - 0xE2 0x80 0xAA
	# RLE - U+202B - 0xE2 0x80 0xAB
	# LRO - U+202D - 0xE2 0x80 0xAD
	# RLO - U+202E - 0xE2 0x80 0xAE
	#
	# PDF - U+202C - 0xE2 0x80 0xAC
	#

	$explicits	= '\xE2\x80\xAA|\xE2\x80\xAB|\xE2\x80\xAD|\xE2\x80\xAE';
//	$pdf		= '\xE2\x80\xAC';

	preg_match_all("!$explicits!",	$data, $m1, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
	//preg_match_all("!$pdf!", 	$data, $m2, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
	$m2 = array();

	if (count($m1) || count($m2)){

		$p = array();
		foreach ($m1 as $m){ $p[$m[0][1]] = 'push'; }
		foreach ($m2 as $m){ $p[$m[0][1]] = 'pop'; }
		ksort($p);

		$offset = 0;
		$stack = 0;
		foreach ($p as $pos => $type){

			if ($type == 'push'){
				$stack++;
			}else{
				if ($stack){
					$stack--;
				}else{
					# we have a pop without a push - remove it
					$data = substr($data, 0, $pos-$offset)
						.substr($data, $pos+3-$offset);
					$offset += 3;
				}
			}
		}

		# now add some pops if your stack is bigger than 0
		for ($i=0; $i<$stack; $i++){
			$data .= "\xE2\x80\xAC";
		}

		return $data;
	}

	return $data;
}

/**
 * Cleans an UTF8 string
 * 
 * See http://stackoverflow.com/a/3742879
 * @param string $str String to clean
 * @return string The clean string
 */
function utf8_clean($str)
{
    return iconv('UTF-8', 'UTF-8//IGNORE', $str);
}

/**
 * Return the client's IP address
 * 
 * Quite some logic to get that behind load-balancers and some proxies, but
 * works fine now ;)
 * @return string IP address
 */
function get_ip_address()
{
//	if( isDev() )
//		return "66.135.205.14";	// US (ebay.com)
//		return "46.122.252.60"; // ljubljana
//		return "190.172.82.24"; // argentinia?
//		return "84.154.26.132"; // probably invalid ip from munich
//		return "203.208.37.104"; // google.cn
//		return "62.215.83.54";	// kuwait
//		return "41.250.146.224";	// Morocco (rtl!)
//		return "66.135.205.14";	// US (ebay.com)
//		return "121.243.179.122";	// india
//		return "109.253.21.90";	// invalid (user says UK)
//		return "82.53.187.74";	// IT
//		return "190.172.82.24";	// AR
//		return "99.230.167.125";	// CA
//		return "95.220.134.145";	// N/A
//		return "194.126.108.2";	// Tallinn/Estonia (Skype static IP)

	global $DETECTED_CLIENT_IP;

	if( isset($DETECTED_CLIENT_IP) )
		return $DETECTED_CLIENT_IP;

	$proxy_headers = array(
		'HTTP_VIA',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_FORWARDED',
		'HTTP_CLIENT_IP',
		'HTTP_FORWARDED_FOR_IP',
		'VIA',
		'X_FORWARDED_FOR',
		'FORWARDED_FOR',
		'X_FORWARDED',
		'FORWARDED',
		'CLIENT_IP',
		'FORWARDED_FOR_IP',
		'HTTP_PROXY_CONNECTION',
		'REMOTE_ADDR' // REMOTE_ADDR must be last -> fallback
	);

	foreach( $proxy_headers as $ph )
	{
		if( !empty($_SERVER) && isset($_SERVER[$ph]) )
		{
			$DETECTED_CLIENT_IP = $_SERVER[$ph];
			break;
		}
		elseif( !empty($_ENV) && isset($_ENV[$ph]) )
		{
			$DETECTED_CLIENT_IP = $_ENV[$ph];
			break;
		}
		elseif( @getenv($ph) )
		{
			$DETECTED_CLIENT_IP = getenv($ph);
			break;
		}
	}

	if( !isset($DETECTED_CLIENT_IP) )
		return false;

	$is_ip = preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/',$DETECTED_CLIENT_IP,$regs);
	if( $is_ip && (count($regs) > 0) )
		$DETECTED_CLIENT_IP = $regs[1];
	return $DETECTED_CLIENT_IP;
}

/**
 * Add a path to the classpath for autoloading classes
 * 
 * You can add complete trees with this when letting $recursive be true.
 * @param string $path folder name
 * @param bool $recursive add subfolders too?
 * @param string $part INTERNAL, let default to false
 * @return void
 */
function classpath_add($path, $recursive=true, $part=false)
{
	global $CONFIG;
	system_ensure_path_ending($path,true);
	if( !$part )
		$part = $CONFIG['system']['application_name'];
	$CONFIG['class_path'][$part][] = $path;
	if( !in_array($part, $CONFIG['class_path']['order']) )
		$CONFIG['class_path']['order'][] = $part;
			
	if( $recursive && is_dir($path) )
	{
		foreach( system_glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT) as $sub )
			classpath_add($sub, true, $part);
	}
}

/**
 * Find pathnames matching a pattern.
 * 
 * glob() cant be used directly in foreach when [open_basedir](http://www.php.net/manual/en/ini.core.php#ini.open-basedir) is set.
 * See https://bugs.php.net/bug.php?id=47358 and <glob>
 * @param string $pattern The pattern. No tilde expansion or parameter substitution is done.
 * @param int $flags Valid flags: see <glob>
 * @return array An array containing the matched files/directories, an empty array if no file matched
 */
function system_glob($pattern, $flags = 0)
{
    $ret = glob($pattern, $flags);
    if( $ret === false )
        return array();
    return $ret;
}

/**
 * Lists all files of a directory recursively.
 * 
 * Note that default pattern in *.* thus only listing files with a dot in the name.
 * If you change that to '*' everything will be returned.
 * We use *.* a common filter for all files (yes, we know that this is wrong).
 * @param string $directory Directory name
 * @param string $pattern Filename pattern
 * @return array Listing of all files
 */
function system_glob_rec($directory='',$pattern='*.*')
{
	system_ensure_path_ending($directory);
	$paths = system_glob($directory.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
	if( $pattern )
	{
		$files = system_glob($directory.$pattern);
		foreach($paths as $path) { $files = array_merge($files,system_glob_rec($path,$pattern)); }
	}
	else
	{
		$files = $paths;
		foreach($paths as $path) { $files = array_merge($files,system_glob_rec($path,$pattern)); }
	}
	return $files;
}

/**
 * Checks if WDF_FEATURES_REWRITE is on
 * 
 * You can set it in .htaccess with `SetEnv WDF_FEATURES_REWRITE on`
 * Note that this check is case sensitive, so 'on' really means 'on' and not 'On' or '1'!
 * @return bool true or false
 */
function can_rewrite(){ return array_val_is($_SERVER,'WDF_FEATURES_REWRITE','on'); }

/**
 * Checks if WDF_FEATURES_NOCACHE is on
 * 
 * You can set it in .htaccess with `SetEnv WDF_FEATURES_NOCACHE on`
 * Note that this check is case sensitive, so 'on' really means 'on' and not 'On' or '1'!
 * @return bool true or false
 */
function can_nocache(){ return array_val_is($_SERVER,'WDF_FEATURES_NOCACHE','on'); }

/**
 * Natural sorts an array by it's keys.
 * 
 * This is a slightly modified version of one found in the PHP documentation.
 * See http://www.php.net/manual/en/function.ksort.php#54319
 * @param array $array Array to be sorted
 * @return void
 */
function natksort(&$array)
{
	$new_array = array();
	$keys = array_keys($array);
	natcasesort($keys);
	foreach ($keys as $k)
		$new_array[$k] = $array[$k];
	$array = $new_array;
}

/**
 * Wraps something into an array if needed.
 * 
 * If fact does this: `return is_array($data)?$data:array($data);`
 * Note that for `is_null($data)` force_array will return an empty `array()`
 * @param mixed $data Anything you want to be an array if it is not aready
 * @return array The resulting array
 */
function force_array($data)
{
	if( is_null($data) )
		return array();
	return is_array($data)?$data:array($data);
}

/**
 * Casts an object to another type.
 * 
 * There are situations where PHP provides you with stdClasses where you want your own type.
 * This function casts any object into another one:
 * <code php>
 * class SomeClass { var $someProperty; }
 * class SomeOtherClass { }
 * $std = json_decode('{"someProperty":"someValue"}');
 * $typed = castObject($std,'SomeClass');
 * $othertyped = castObject($typed,'SomeOtherClass');
 * </code>
 * See stackoverflow: [Convert/cast an stdClass object to another class](http://stackoverflow.com/questions/3243900/convert-cast-an-stdclass-object-to-another-class)
 * @param object $instance Object of any type
 * @param string $className Classname of the type you want
 * @return object Typed object
 */
function castObject($instance, $className)
{
    $res = unserialize(sprintf(
        'O:%d:"%s"%s',
        strlen($className),
        $className,
        strstr(strstr(serialize($instance), '"'), ':')
    ));
	return $res;
}

/**
 * Returns the classname for the given object.
 * 
 * This function ignores all namespace stuff and only return the good old classname.
 * Not sure if we will need it for a longer time, but in fact it IS needed for namespace redesign.
 * @param object $object The object to get the classname from
 * @param bool $lower_case What do you think?
 * @return string Simplified classname
 */
function get_class_simple($object, $lower_case=false)
{
    $array = explode('\\',get_class($object));
    $res = $array[count($array)-1];
	return $lower_case?strtolower($res):$res;
}

/**
 * Checks if an array is associative.
 * 
 * Stolen from [stackoverflow.com](http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-sequential/4254008#4254008)
 * @param array $array Input array
 * @return bool true if $array contains at least one key that is not numeric
 */
function is_assoc($array)
{
  return (bool)count( array_filter(array_keys($array), 'is_string') );
}

/**
 * Returns the first property of an object that is not null.
 * 
 * Requires at least two parameters. The first must be an object or array to check.
 * All others represent property/key names that shall be checked for existance.
 * <code php>
 * $data = array('name'=>'helloworld','display_name'=>'Hello World!');
 * $result = ifnull($data,'email','username','display_name','name');
 * // $result is now "Hello World!"
 * </code>
 * @return mixed The first non-null value or null of none found
 */
function ifnull()
{
	$args = func_get_args();
	$data = array_shift($args);
	
	if( count($args) == 0 )
		ScavixWDF\WdfException::Raise("ifnull needs at least one argument");
	
	if( is_array($data) )
		$data = (object)$data;
	if( !is_object($data) )
	{
		foreach( func_get_args() as $arg )
			if( $arg !== null )
				return $arg;
		return null;
	}
	foreach( $args as $n )
		if( isset($data->$n) && $data->$n !== null )
			return $data->$n;
	return null;
}

/**
 * Shorthand IF function.
 * 
 * This function is something similar to the ?: syntax for IF control structures.
 * Complicated to explain, here's a sample:
 * <code php>
 * $a = true ? 1 : true ? 2 : 3;    // -> 2
 * $a = true ? 1 : (true ? 2 : 3);  // -> 1
 * $b = sif(true,1,sif(true,2,3));  // -> 1
 * </code>
 * So we use <sif> to get readable code in a one-liner.
 * @param bool $condition Condition to check
 * @param mixed $true_value Returnvalue if $condition is true
 * @param mixed $false_value Returnvalue if $condition is false
 * @return mixed $true_value or $false_value
 */
function sif($condition,$true_value,$false_value)
{
	return $condition?$true_value:$false_value;
}

/**
 * Returns true if an object's/array's property/key is set.
 * 
 * <avail> is a shorthand function to recursively check if an object property or array key is present and set.
 * It needs at least two arguments: The object/array to check and a property/key to check. If you want to check
 * more deeply just add more arguments.
 * In fact using avail is equivalent to using <isset> and `== true`.
 * See this sample and you will understand:
 * <code php>
 * $o = new stdClass();
 * $o->attributes = new stdClass();
 * $o->attributes->url = 'http://www.scavix.com';
 * $a = array();
 * $a['system']['atad'] = 'wrong order';
 * if( avail($o,'attributes','url') )
 *     log_debug("URL",$o->attributes->url);
 * if( isset($o) && is_object($o->attributes) && isset($o->attributes->url) && $o->attributes->url )
 *     log_debug("URL",$o->attributes->url);
 * if( avail($a,'system','data') )
 *     log_debug("SysData",$a['system']['data']);
 * if( isset($a) && is_array($a['system']) && isset($a['system']['data']) && $a['system']['data'] )
 *     log_debug("SysData",$a['system']['data']);
 * </code>
 * @return boolean True if the requested data is available, else false
 */
function avail()
{
	$args = func_get_args();
	if( count($args) < 2 )
		ScavixWDF\WdfException::Raise("avail needs at least two arguments");
	
	$ar = array_shift($args);
	if( !is_array($ar) && !is_object($ar) )
		return false;
	$ar = (array)$ar;
	$l = array_pop($args);
	foreach( $args as $a )
	{
		if( !isset($ar[$a]) )
			return false;
		if( !is_array($ar[$a]) && !is_object($ar[$a]) )
			return false;
		$ar = (array)$ar[$a];
	}
	if( !isset($ar[$l]) )
		return false;
	return $ar[$l] == true; // note the weak comparision!
}

/**
 * Returns the first property of an object that is available.
 * 
 * See <ifnull> for a detailed description as this works the same way.
 * Difference is that this only checks against null but also if a value is set (weak comparison against false).
 * @return mixed The first set value or null of none found
 */
function ifavail()
{
	$args = func_get_args();
	$data = array_shift($args);
	
	if( count($args) == 0 )
		ScavixWDF\WdfException::Raise("ifavail needs at one argument");
	
	if( is_array($data) )
		$data = (object)$data;
	if( !is_object($data) )
	{
		foreach( func_get_args() as $arg )
			if( $arg )
				return $arg;
		return null;
	}
	foreach( $args as $n )
		if( avail($data,$n) )
			return $data->$n;
	return null;
}

/**
 * @shortcut <array_values> on a multidimentional array
 */
function array_values_rec($array,$max_depth=false,$cur_depth=1)
{
	if( $max_depth !== false && $cur_depth>$max_depth )
		return $array;
	$res = array();
	foreach( $array as $v )
	{
		if( is_array($v) )
			$v = array_values_rec($v,$max_depth,$cur_depth+1);
		$res[] = $v;
	}
	return $res;
}

if( !function_exists('idn_to_utf8') )
{
    /**
     * @internal Use own implementation if missing
     */
    class IDN {
        // adapt bias for punycode algorithm
        private static function punyAdapt(
            $delta,
            $numpoints,
            $firsttime
        ) {
            $delta = $firsttime ? $delta / 700 : $delta / 2;
            $delta += $delta / $numpoints;
            for ($k = 0; $delta > 455; $k += 36)
                $delta = intval($delta / 35);
            return $k + (36 * $delta) / ($delta + 38);
        }

        // translate character to punycode number
        private static function decodeDigit($cp) {
            $cp = strtolower($cp);
            if ($cp >= 'a' && $cp <= 'z')
                return ord($cp) - ord('a');
            elseif ($cp >= '0' && $cp <= '9')
                return ord($cp) - ord('0')+26;
        }

        // make utf8 string from unicode codepoint number
        private static function utf8($cp) {
            if ($cp < 128) return chr($cp);
            if ($cp < 2048)
                return chr(192+($cp >> 6)).chr(128+($cp & 63));
            if ($cp < 65536) return
                chr(224+($cp >> 12)).
                chr(128+(($cp >> 6) & 63)).
                chr(128+($cp & 63));
            if ($cp < 2097152) return
                chr(240+($cp >> 18)).
                chr(128+(($cp >> 12) & 63)).
                chr(128+(($cp >> 6) & 63)).
                chr(128+($cp & 63));
            // it should never get here
        }

        // main decoding function
        private static function decodePart($input) {
            if (substr($input,0,4) != "xn--") // prefix check...
                return $input;
            $input = substr($input,4); // discard prefix
            $a = explode("-",$input);
            if (count($a) > 1) {
                $input = str_split(array_pop($a));
                $output = str_split(implode("-",$a));
            } else {
                $output = array();
                $input = str_split($input);
            }
            $n = 128; $i = 0; $bias = 72; // init punycode vars
            while (!empty($input)) {
                $oldi = $i;
                $w = 1;
                for ($k = 36;;$k += 36) {
                    $digit = IDN::decodeDigit(array_shift($input));
                    $i += $digit * $w;
                    if ($k <= $bias) $t = 1;
                    elseif ($k >= $bias + 26) $t = 26;
                    else $t = $k - $bias;
                    if ($digit < $t) break;
                    $w *= intval(36 - $t);
                }
                $bias = IDN::punyAdapt(
                    $i-$oldi,
                    count($output)+1,
                    $oldi == 0
                );
                $n += intval($i / (count($output) + 1));
                $i %= count($output) + 1;
                array_splice($output,$i,0,array(IDN::utf8($n)));
                $i++;
            }
            return implode("",$output);
        }

        public static function decodeIDN($name) {
            // split it, parse it and put it back together
            return
                implode(
                    ".",
                    array_map("IDN::decodePart",explode(".",$name))
                );
        }
    }
    
    /**
     * @internal Use own implementation if missing
     */
    function idn_to_utf8($domain) { return IDN::decodeIDN($domain); }
}