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

use ScavixWDF\Localization\Localization;

/**
 * Initializes the localization essential.
 * 
 * @return void
 */
function localization_init()
{
	global $CONFIG;

	$p = __DIR__.'/localization/';
	$CONFIG['class_path']['system'][] = $p;
	
	if( !isset($CONFIG['localization']['default_culture']) )
		$CONFIG['localization']['default_culture'] = 'en-US';

	if( !isset($CONFIG['localization']['default_timezone']) )
		$CONFIG['localization']['default_timezone'] = "Europe/Berlin";
	
	if( !isset($CONFIG['localization']['detection_order']) )
		$CONFIG['localization']['detection_order'] = array(Localization::USE_BROWSER,Localization::USE_IP);

	$GLOBALS['arBufferedCultures'] = array();
	require_once($p.'cultureinfo.inc.php');
}

/**
 * Returns whether given value is a valid float value or not
 *
 * @param string $value floatnumber to be checked
 * @return bool true if valid
 */
function localized_to_float_number($value)
{
	$ci = Localization::getCultureInfo();
	$number =  str_replace($ci->NumberFormat->GroupSeparator,"",$value);
	if( $ci->NumberFormat->DecimalSeparator != '.' )
		$number =  str_replace($ci->NumberFormat->DecimalSeparator,".",$number);

	if( !is_float(floatval($number)) || !is_numeric($number) )
		return false;

	return $number;
}
