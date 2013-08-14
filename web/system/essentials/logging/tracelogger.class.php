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

/**
 * Generates a machine readable log.
 * 
 * Use WdfTracer to read these files.
 * https://github.com/ScavixSoftware/WebFramework/tree/master/tools
 * In fact writes a JSON encoded object per line that contains full tracing information.
 */
class TraceLogger extends Logger
{
	/**
	 * Writes a log entry.
	 * 
	 * @param string $severity Severity
	 * @param bool $log_trace Ignored, will log trace always
	 * @param_array mixed $a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8,$a9,$a10 Data to be logged
	 * @return void
	 */
    public function write($severity=false,$log_trace=false,$a1=null,$a2=null,$a3=null,$a4=null,$a5=null,$a6=null,$a7=null,$a8=null,$a9=null,$a10=null)
	{
		$content = $this->prepare($severity,true,$a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8,$a9,$a10);
		if( !$content ) return;
		$content = $content->serialize();
		file_put_contents($this->filename, "$content\n", FILE_APPEND);
	}
}