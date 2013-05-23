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

/**
 * Represents an order item in the database.
 * 
 * In fact nothing more than implementations for the inherited Model 
 * and the implemented IShopOrderItem interface.
 * See https://github.com/ScavixSoftware/WebFramework/wiki/classes_modules_payment#wiki-97745ff2e14aebb2225c7647a8a059bc
 */
class SampleShopOrderItem extends Model implements IShopOrderItem
{
	/**
	 * Returns the table name.
	 * See https://github.com/ScavixSoftware/WebFramework/wiki/classes_essentials_model_model.class#gettablename
	 */
	public function GetTableName() { return 'items'; }

	/**
	 * Gets the price per item converted into the requested currency.
	 * @param string $currency Currency code
	 * @return float The price per item converted into $currency
	 */
	public function GetAmount($currency) { return $this->price; }

	/**
	 * Gets the discount.
	 * @return float The discount
	 */
	public function GetDiscount() { return 0; }

	/**
	 * Gets the handling cost.
	 * @return float Cost for handling
	 */
	public function GetHandling() { return 0; }

	/**
	 * Gets the items name.
	 * @return string The item name
	 */
	public function GetName() { return $this->title; }

	/**
	 * Gets the quantity.
	 * @return float The quantity
	 */
	public function GetQuantity() { return $this->amount; }

	/**
	 * Gets the shipping cost.
	 * @return float Cost for shipping
	 */
	public function GetShipping() { return 0; }
}