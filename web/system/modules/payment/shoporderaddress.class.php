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
 * Prototype of an Address Model.
 * 
 * Your own address class must inherit from this. Usually this data is stored alongside the order itself
 * so a typical <IShopOrder::GetAddress> method would just create a new <ShopOrderAddress> object and assign all
 * properties from itself.
 * <code php>
 * class MyShopOrder implements IShopOrder
 * {
 *     public function GetAddress()
 *     {
 *         $res = new ShopOrderAddress();
 *         $res->Firstname = $this->fname;
 *         $res->Lastname = $this->lname;
 *         $res->Address1 = $this->street;
 *         $res->Zip = $this->zip;
 *         $res->City = $this->city;
 *         $res->Email = $this->email;
 *         return $res;
 *     }
 * 
 *     // more methods
 * }
 * </code>
 */
class ShopOrderAddress
{
	public $Firstname;
	public $Lastname;
	public $Companyname;
	public $Email;
	public $Address1;	
	public $Address2;
	public $Country;
	public $State;
	public $Zip;
	public $City;
	public $Phone1;
	public $Phone2;
	public $Phone3;
}