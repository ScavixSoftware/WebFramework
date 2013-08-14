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
namespace ScavixWDF\Payment;

/**
 * Order items <Model>s must implement this.
 */
interface IShopOrderItem
{
	/**
	 * Gets the items name.
	 * @return string The item name
	 */
	function GetName();
	
	/**
	 * Gets the price per item converted into the requested currency.
	 * @param string $currency Currency code
	 * @return float The price per item converted into $currency
	 */
	function GetAmount($currency);
	
	/**
	 * Gets the shipping cost.
	 * @return float Cost for shipping
	 */
	function GetShipping();
	
	/**
	 * Gets the handling cost.
	 * @return float Cost for handling
	 */
	function GetHandling();
	
	/**
	 * Gets the discount.
	 * @return float The discount
	 */
	function GetDiscount();
	
	/**
	 * Gets the quantity.
	 * @return float The quantity
	 */
	function GetQuantity();
}