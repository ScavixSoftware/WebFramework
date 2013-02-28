<?
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

/**
 * Base class for SessionHandlers.
 * 
 * Implements basic functionalities and defines some more as
 * abstract which must be implemented by inherited classes.
 */
abstract class SessionBase
{
	/**
	 * Implement to perform sanitations like checking if users IP has changed.
	 * 
	 * @return void
	 */
	abstract function Sanitize();
	
	/**
	 * Clears the complete object store.
	 * 
	 * @return void
	 */
	abstract function KillAll();
	
	/**
	 * Perform a session keep alive.
	 * 
	 * @param string $request_key Key in the REQUEST variable containing the request_id value
	 * @return void
	 */
	abstract function KeepAlive($request_key='PING');
	
	/**
	 * Store an object into the object store.
	 * 
	 * @param object $obj Object to be stored
	 * @param string $id Key to store object under (use <create_storage_id> if not given)
	 * @return void
	 */
	abstract function Store(&$obj,$id="");
	
	/**
	 * Removes an object from the object store.
	 * 
	 * @param string $id Key of the object to remove
	 * @return void
	 */
	abstract function Delete($id);
	
	/**
	 * Checks if an object exists in the object store.
	 * 
	 * @param string $id Key of the object to check for
	 * @return bool true or false
	 */
	abstract function Exists($id);
	
	/**
	 * Restores an object from the object store.
	 * 
	 * @param string $id Key of the object to restore
	 * @return object The restored object
	 */
	abstract function &Restore($id);

	/**
	 * Generates a secure session id
	 * 
	 * See http://www.php-security.org/2010/05/09/mops-submission-04-generating-unpredictable-session-ids-and-hashes/index.html#more-204
	 * @param int $maxLength Maximum length of resulting id
	 * @return string Session id
	 */
	function GenerateSessionId($maxLength = 32)
	{
		$entropy = '';

		// try ssl first
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			$entropy = openssl_random_pseudo_bytes(64, $strong);
			// skip ssl since it wasn't using the strong algo
			if($strong !== true)
				$entropy = '';
		}

		// add some basic mt_rand/uniqid combo
		$entropy .= uniqid(mt_rand(), true);

		// try to read from the unix RNG
		if (is_readable('/dev/urandom'))
		{
			$h = fopen('/dev/urandom', 'rb');
			$entropy .= fread($h, 64);
			fclose($h);
		}

		$hash = hash('whirlpool', $entropy);
		if ($maxLength) 
			return substr($hash, 0, $maxLength);
		
		return $hash;
	}

	function __construct($allow_regenerate_id=true)
	{
		global $CONFIG;

		if( (session_name() != $CONFIG['session']['session_name']) || (session_id() == "") )
		{
			session_name($CONFIG['session']['session_name']);

			if( isset($_REQUEST[$CONFIG['session']['session_name']]) )
			{
				$regen_needed = false;
				/**
				 * @todo The following code is superfluous if variables_order=EGPCS and session.use_only_cookies = Off
				 */
				// in case that there is a session id passed in the cookie and in the post, prefer the one in post:
				if(isset($_POST[$CONFIG['session']['session_name']]) && $_REQUEST[$CONFIG['session']['session_name']] != $_POST[$CONFIG['session']['session_name']])
				{
					$_REQUEST[$CONFIG['session']['session_name']] = $_COOKIE[$CONFIG['session']['session_name']] = $_POST[$CONFIG['session']['session_name']];
					$regen_needed = true;
				}
				
				// in case that there is a session id passed in the cookie and in the get, prefer the one in get,
				// but do not set the COOKIE to make multi-session handling possible
				if(isset($_GET[$CONFIG['session']['session_name']]) && $_REQUEST[$CONFIG['session']['session_name']] != $_GET[$CONFIG['session']['session_name']])
				{
					$_REQUEST[$CONFIG['session']['session_name']] = $_GET[$CONFIG['session']['session_name']];
					$regen_needed = true;
				}
				$sid = preg_replace("/[^0-9a-zA-Z]/", "", $_REQUEST[$CONFIG['session']['session_name']]);
				if($sid != "")
				{
					session_id($sid);
					$try = 0;
					while( (@session_start() === false) && ($try++ < 10) )
						usleep(200);
					if($try >= 10)
						trigger_error("session_start failed 10 times!", E_USER_ERROR);
					// generate a new session id if the passed one is not valid
					if( $regen_needed && $allow_regenerate_id )
						$this->RegenerateId(true);
				}
				else
				{
					session_id($this->GenerateSessionId());

					$try = 0;
					while( (@session_start() === false) && ($try++ < 10) )
						usleep(200);
					if($try >= 10)
						trigger_error("start_start failed 10 times!", E_USER_ERROR);
				}
			}
			else
			{
				try {
					$try = 0;
					while( (@session_start() === false) && ($try++ < 10) )
						usleep(200);
					if($try >= 10)
						trigger_error("start_start failed 10 times!", E_USER_ERROR);
				} catch(Exception $ex) {}
			}
		}
	}

	/**
	 * Regenerates the session id
	 * 
	 * See http://www.php.net/manual/en/function.session-regenerate-id.php
	 * @param bool $destroy_old_session Whether to delete the old associated session file or not
	 * @return bool true or false
	 */
	function RegenerateId($destroy_old_session = false)
	{
		$ret = @session_regenerate_id($destroy_old_session);
		session_write_close();
		return $ret;
	}

	/**
	 * Ensures object validity
	 * 
	 * Calls <store_object> for every <Renderable> object in the object store to ensure that the stored 
	 * objects really match the serialized ones. This is needed because fields/properties can change after
	 * the initial save and our caching will hide that from system.
	 * 
	 * No need to call this manually, WDF will do!
	 * @return void
	 */
	function Update()
	{
		global $CONFIG;
		$_SESSION[$CONFIG['session']['prefix']."session_lastaccess"] = time();
		foreach( $GLOBALS['object_storage'] as $id=>&$obj )
		{
			try
			{
				if( $obj instanceof Renderable )
					store_object($obj,$id);
			}
			catch(Exception $ex)
			{
				WdfException::Log("updating session storage for object $id [".get_class($obj)."]",$ex);
			}
		}
	}

	/**
	 * Returns a (new) request id
	 * 
	 * WDF creates a new ID for every request and passed it to every subsequent AJAX call.
	 * This method does the real magic and creates a new request id or returns the current.
	 * @return string A new request id or the current one
	 */
	function RequestId()
	{
		if( !isset($GLOBALS['session_request_id']) )
		{
			$p = current_controller();
			$e = current_event();
			$GLOBALS['session_request_id'] = md5($p.$e.microtime());
		}
		return $GLOBALS['session_request_id'];
	}

	/**
	 * Creates a object id
	 * 
	 * WDF will create IDs for <Renderable> objects automatically and ensures uniqueness for
	 * the whole session. This method creates such an id based on the given objects classname.
	 * It will store it to `$obj->_storage_id` and return it.
	 * @param object $obj Object which needs an id
	 * @return string The generated object id
	 */
	function CreateId(&$obj)
	{
		global $CONFIG;

		if( unserializer_active() )
		{
			log_trace("create_storage_id while unserializing object of type ".get_class($obj));
			$obj->_storage_id = "to_be_overwritten_by_unserializer";
			return $obj->_storage_id;
		}

		$cn = strtolower(get_class($obj));
		if( !isset($GLOBALS['object_ids'][$cn]) )
		{
			$i = 1;
			while(isset($_SESSION[$CONFIG['session']['prefix']."session"][$cn.$i]))
				$i++;
			$GLOBALS['object_ids'][$cn] = $i;
		}
		else
			$GLOBALS['object_ids'][$cn]++;

		$obj->_storage_id = $cn.$GLOBALS['object_ids'][$cn];

		if( session_id() )
			$_SESSION['object_id_storage'] = $GLOBALS['object_ids'];

		return $obj->_storage_id;
	}
}
