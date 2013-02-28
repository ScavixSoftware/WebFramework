<?
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

if( !defined('FRAMEWORK_LOADED') || FRAMEWORK_LOADED != 'uSI7hcKMQgPaPKAQDXg5' ) die('');

/**
 * We use this to test access to controllers.
 * All controllers must implement this interface
 */
interface ICallable {}


/**
 * Transparently wraps Exceptions thus providing a way to catch them easily while still having the original
 * Exception information.
 * 
 * Using static <WdfException::Raise>() method you can pass in multiple arguments. WDF will try to detect
 * if there's an exception object given and use it (the first one detected) as inner exception object.
 * <code php>
 * WdfException::Raise('My simple test');
 * WdfException::Raise('My simple test2',$obj_to_debug_1,'and',$obj_to_debug_2);
 * try{ $i=42/0; }catch(Exception $ex){ WdfException::Raise('That was stupid!',$ex); }
 * <code>
 */
class WdfException extends Exception
{
	private function ex()
	{
		$inner = $this->getPrevious();
		return $inner?$inner:$this;
	}
	
	/**
	 * Use this to throw exceptions the easy way.
	 * 
	 * Can be used from derivered classes too like this:
	 * <code php>
	 * ToDoException::Raise('implement myclass->mymethod()');
	 * </code>
	 * @return void
	 */
	public static function Raise()
	{
		$msgs = array();
		$inner_exception = false;
		foreach( func_get_args() as $m )
		{
			if( !$inner_exception && ($m instanceof Exception) )
				$inner_exception = $m;
			else 
				$msgs[] = logging_render_var($m);
		}
		$message = implode("\t",$msgs);
		
		$classname = get_called_class();
		if( $inner_exception )
			throw new $classname($message,$inner_exception->getCode(),$inner_exception);
		else
			throw new $classname($message);
	}
	
	/**
	 * Use this to easily log an exception the nice way.
	 * 
	 * Ensures that all your exceptions are logged the same way, so they are easily readable.
	 * sample: 
	 * <code php>
	 * try{
	 *  some code
	 * }catch(Exception $ex){ WdfException::Log("Weird:",$ex); }
	 * </code>
	 * Note that Raise method will log automatically, so this is mainly useful when silently catching exceptions.
	 * @return void
	 */
	public static function Log()
	{
		call_user_func_array('log_error', func_get_args());
	}
	
	/**
	 * Returns exception message.
	 * 
	 * Check if there's an inner exception and combines this and that messages into one if so.
	 * @return string Combined message
	 */
	public function getMessageEx()
	{
		$inner = $this->getPrevious();
		return $this->getMessage().($inner?"\nOriginal message: ".$inner->getMessage():'');
	}
	
	/**
	 * Calls this or the inner exceptions getFile() method.
	 * 
	 * See http://www.php.net/manual/en/exception.getfile.php
	 * @return string Returns the filename in which the exception was created
	 */
	public function getFileEx(){ return $this->ex()->getFile(); }
	
	/**
	 * Calls this or the inner exceptions getCode() method.
	 * 
	 * See http://www.php.net/manual/en/exception.getcode.php
	 * @return string Returns the exception code as integer
	 */
	public function getCodeEx(){ return $this->ex()->getCode(); }
	
	/**
	 * Calls this or the inner exceptions getLine() method.
	 * 
	 * See http://www.php.net/manual/en/exception.getline.php
	 * @return string Returns the line number where the exception was created
	 */
	public function getLineEx(){ return $this->ex()->getLine(); }
	
	/**
	 * Calls this or the inner exceptions getTrace() method.
	 * 
	 * See http://www.php.net/manual/en/exception.gettrace.php
	 * @return string Returns the Exception stack trace as an array
	 */
	public function getTraceEx(){ return $this->ex()->getTrace(); }
}

/**
 * Thrown when something still needs investigation
 * 
 * We use this like this: `ToDoException::Raise('Not yet implemented')`
 */
class ToDoException extends WdfException {}

/**
 * Thrown from all database related system parts
 * 
 * All code in the model essential (essentials/model.php + essentials/model/*) use this instead of WdfException.
 * Just to have everyting nicely wrapped.
 */
class WdfDbException extends WdfException {}