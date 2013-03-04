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
 * Initializes the pear module.
 * 
 * Note: We do not deliver any PEAR files. Please set the path there manually: <cfg_set>('pear','include_path','/path/to/pear')* * 
 * @return void
 */
function pear_init()
{
	global $CONFIG;

	if( !isset($CONFIG['pear']['include_path']) )
		$CONFIG['pear']['include_path'] = __DIR__."/pear";

	$inc_path = explode(PATH_SEPARATOR,ini_get("include_path"));
	foreach( $inc_path as $i=>$p )
		if( preg_match('|/pear$|i',$p) || preg_match('|/pear/|i',$p) )
			unset($inc_path[$i]);
	$inc_path[] = $CONFIG['pear']['include_path'];
	ini_set("include_path", implode(PATH_SEPARATOR,$inc_path));

	require_once("PEAR.php");


	if( isset($CONFIG['pear']['modules']) && is_array($CONFIG['pear']['modules']) )
		foreach( $CONFIG['pear']['modules'] as $pear )
			require_once($pear);
}

/**
 * Loads a pear module.
 * 
 * Sample: `pear_load('HTTP/Request.php')`
 * @param string $module Path to module relative to the pear subdir
 * @return void
 */
function pear_load($module)
{
	require_once($module);
}
