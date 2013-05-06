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

class SampleShopOrder extends Model implements IShopOrder
{
	const UNKNOWN  = 0;
	const PENDING  = 10;
	const PAID     = 20;
	const FAILED   = 30;
	const REFUNDED = 40;
	
	public function GetTableName() { return 'orders'; }

	public function GetAddress()
	{
		$res = new ShopOrderAddress();
		$res->Firstname = $this->fname;
		$res->Lastname = $this->lname;
		$res->Address1 = $this->street;
		$res->Zip = $this->zip;
		$res->City = $this->city;
		$res->Email = $this->email;
		return $res;
	}

	public function GetCurrency() { return 'EUR'; }

	public function GetInvoiceId() { return "I".$this->id; }

	public function GetLocale() { return 'en-US'; }

	public function GetTotalPrice($price = false)
	{
		if( $price !== false )
			return $price * ( (1+$this->GetVatPercent()) / 100 );
		return $this->price_total * ( (1+$this->GetVatPercent()) / 100 );
	}

	public function GetTotalVat() { return $this->price_total * ($this->GetVatPercent()/100); }

	public function GetVatPercent() { return 19; }

	public function ListItems() { return SampleShopOrderItem::Make()->eq('order_id',$this->id)->orderBy('id'); }

	public function SetCurrency($currency_code) { /* we stay with EUR */ }

	public static function FromOrderId($order_id)
	{
		return SampleShopOrder::Make()->eq('id',$order_id)->current();
	}

	public function SetFailed($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::FAILED;
		$this->updated = $this->deleted = 'now()';
		$this->Save();
	}

	public function SetPaid($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::PAID;
		$this->updated = $this->completed = 'now()';
		$this->Save();
	}

	public function SetPending($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::PENDING;
		$this->updated = 'now()';
		$this->Save();
	}

	public function SetRefunded($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::REFUNDED;
		$this->updated = $this->deleted = 'now()';
		$this->Save();
	}

	public function DoAddVat() { return true; /* Let's assume normal VAT customers for now */ }
}