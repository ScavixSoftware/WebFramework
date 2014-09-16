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
namespace ScavixWDF\Logging;
use \stdClass;

/**
 * Represents a logfile entry.
 * 
 * We use this class to collect information before logging them.
 * It allows to create murch more detailed logs as the PHP standart allows.
 */
class LogEntry
{
    public $datetime;
    public $categories;
    public $severity;
    public $trace;
    public $message;
    
    function __construct($severity,$categories,$trace,$message,$max_trace_depth)
    {
        $this->datetime = time();
        $this->categories = $categories;
        $this->severity = $severity;
        $this->trace = $trace?$this->cleanupTrace($trace,$max_trace_depth):false;
        $this->message = substr($message,0,1024*50);
    }
	
	private function cleanupTrace($stacktrace,$max_trace_depth)
	{
		$args = array();
		$info = array();
		$stack = array();
		$stcnt = count($stacktrace);
		foreach($stacktrace as $i=>$t0)
		{
			if( isset($t0['file']) )
			{
				if( ends_with($t0['file'],"/essentials/logging/logger.class.php") ||
					ends_with($t0['file'],"/essentials/logging/tracelogger.class.php") ||
					ends_with($t0['file'],"/essentials/logging/logentry.php") ||
					ends_with($t0['file'],"/essentials/logging.php") )
					continue;
				$t0['location'] = $t0['file'].":".$t0['line'];
			}
			else
				$t0['location'] = "*UNKNOWN*";
			
			foreach( $t0['args'] as $ai=>$a)
			{
				if( !is_object($a) && !is_array($a) )
					continue;
				$index = array_search($a, $args, true);
				if( $index !== false )
				{
					$t0['args'][$ai] = $info[$index];
					continue;
				}
				$args[] = $a;
				$info[] = "*SEE ARG ".$t0['function']."[$ai]*";
			}
			
			$stack[] = $t0;
			if( count($stack) == $max_trace_depth )
				break;
		}
		
		if( $stack[count($stack)-1]['function'] == 'system_execute' )
			array_pop($stack);
		if( $stack[count($stack)-1]['function'] == 'system_invoke_request' )
			array_pop($stack);
		if( $stack[count($stack)-1]['function'] == 'call_user_func_array' )
			array_pop($stack);
		
		return $stack;
	}
    
    private function parseTrace($stacktrace)
	{
		$stack = array();
		
		foreach( $stacktrace as $t0 )
		{
			if( isset($t0['class']) && isset($t0['type']) )
				$function = $t0['class'].$t0['type'].$t0['function'];
			else
				$function = $t0['function'];
			
			if( isset($t0['location']))
				$stack[] = sprintf("+ %s(...) [in %s]",$function,$t0['location']);
			else
				$stack[] = sprintf("+ %s(...)",$function);
		}
		return implode("\n",$stack);
	}
    
	/**
	 * @internal Creates a human readable representation of this <LogEntry>
	 */
    public function toReadable()
    {
        $content = date("[Y-m-d H:i:s.m]",$this->datetime);
		$content .= " [{$this->severity}]";
		$content .= " (".implode(",",$this->categories).")";
		$content .= "\t{$this->message}";
		if( $this->trace )
			$content .= "\n".$this->parseTrace($this->trace);
        return $content;
    }
	
	/**
	 * @internal Creates a machine readable representation of this <LogEntry>
	 */
	function serialize()
	{
		$res = new stdClass();
		$res->dt = date("c",$this->datetime);
		$res->cat = array();
		foreach( array_values($this->categories) as $v )
			$res->cat[] = utf8_encode($v);
		$res->sev = utf8_encode($this->severity);
		$res->msg = utf8_encode($this->message);
		$res->trace = $this->trace;
		$res = @json_encode($res);
		return $res;
	}
}