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
 * Wrapper class to generate a report in the logs.
 * 
 * Use log_start_report function to generate a report.
 * Then add information and finally write it with log_report function.
 * 
 * Sample:
 * <code php>
 * $r = log_start_report('test-report');
 * $r->add("some var",$v);
 * $r->add("$c);
 * if( $user ) $r->add("User",$user);
 * log_report($r);
 * </code>
 */
class LogReport
{
	var $Name = "LogReport";
	var $Lines = array();
	
	public function __construct($name)
	{
		$this->Name = $name;
	}
	
	/**
	 * Adds information to the report.
	 * 
	 * @param_array mixed $a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8,$a9,$a10 Data to be logged
	 * @return void
	 */
    public function add($a1=null,$a2=null,$a3=null,$a4=null,$a5=null,$a6=null,$a7=null,$a8=null,$a9=null,$a10=null)
	{
		$l = array();
		for($i=1;$i<11;$i++)
		{
			$v = "a$i";
			if( $$v == null )
				continue;
			$l[$v] = $$v;
		}
		$this->Lines[] = $l;
	}
	
	/**
	 * Renders a report to a logfile.
	 * 
	 * Do not call directly but use a combination of <log_start_report> and <log_report> instead.
	 * @return void
	 */
	public function render()
	{
		$lines = array($this->Name." (".count($this->Lines)." lines):");
		foreach( $this->Lines as $i=>$l )
		{
			$line = array("[#".($i+1)."]");
			foreach( $l as $k=>$v )
				$line[] = logging_render_var($v);
			$lines[] = implode("\t",$line);
		}
		return implode("\n",$lines);
	}
}