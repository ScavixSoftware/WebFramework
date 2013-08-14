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

use ScavixWDF\Controls\Form\Form;
use ScavixWDF\WdfException;
 
/**
 * Initializes the payment module.
 * 
 * @return void
 */
function payment_init()
{
	global $CONFIG;
	$CONFIG['class_path']['system'][] = __DIR__.'/payment/';
	
	if( !isset($CONFIG["payment"]["order_model"]) || !$CONFIG["payment"]["order_model"] )
		WdfException::Raise('Please configure an order_model for the payment module');
}

/**
 * Returns a list of payment providers.
 * 
 * @return array List of <PaymentProvider> objects
 */
function payment_list_providers()
{
	$res = array();
	foreach( system_glob(__DIR__.'/payment/*.class.php') as $file )
	{
		$cn = basename($file,".class.php");
		$cn = new $cn();
		if( ($cn instanceof PaymentProvider) && $cn->IsAvailable() )
			$res[] = $cn;
	}
	return $res;
}
