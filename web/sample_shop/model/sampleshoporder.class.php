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
use ScavixWDF\Model\Model;
use ScavixWDF\Payment\IShopOrder;
use ScavixWDF\Payment\ShopOrderAddress;

/**
 * Represents an order in the database.
 * 
 * In fact nothing more than implementations for the inherited Model 
 * and the implemented IShopOrder interface.
 * See https://github.com/ScavixSoftware/WebFramework/wiki/classes_modules_payment#wiki-1c67f96d00c3c22f1ab9002cd0e3acbb
 * More logic would go into the Set* methods to handle different order states.
 * For our sample we just set the states in the DB.
 */
class SampleShopOrder extends Model implements IShopOrder
{
	const UNKNOWN  = 0;
	const PENDING  = 10;
	const PAID     = 20;
	const FAILED   = 30;
	const REFUNDED = 40;
	
	/**
	 * Returns the table name.
	 * See https://github.com/ScavixSoftware/WebFramework/wiki/classes_essentials_model_model.class#gettablename
	 */
	public function GetTableName() { return 'orders'; }

	/**
	 * Gets the orders address.
	 * @return ShopOrderAddress The order address
	 */
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

	/**
	 * Gets the currency code.
	 * @return string A valid currency code
	 */
	public function GetCurrency() { return 'EUR'; }

	/**
	 * Gets the invoice ID.
	 * @return mixed Invoice identifier
	 */
	public function GetInvoiceId() { return "I".$this->id; }

	/**
	 * Gets the order culture code.
	 * 
	 * See <CultureInfo>
	 * @return string Valid culture code
	 */
	public function GetLocale() { return 'en-US'; }

    /**
	 * Return the total price incl. VAT (if VAT applies for the given country). 
	 * @param float $price The price without VAT.
	 * @return float Price including VAT (if VAT applies for the country).
	 */
	public function GetTotalPrice($price = false)
	{
		if( $price !== false )
			return $price * ( (1+$this->GetVatPercent()) / 100 );
		return $this->price_total * ( (1+$this->GetVatPercent()) / 100 );
	}

    /**
	 * Return the total VAT (if VAT applies for the given country). 
	 * @return float VAT in order currency
	 */
	public function GetTotalVat() { return $this->price_total * ($this->GetVatPercent()/100); }

    /**
	 * Return the total VAT percent (if VAT applies for the given country). 
	 * @return float VAT percent
	 */
	public function GetVatPercent() { return 19; }

	/**
	 * Returns all items.
	 * 
	 * @return array A list of <IShopOrderItem> objects
	 */
	public function ListItems() { return SampleShopOrderItem::Make()->eq('order_id',$this->id)->orderBy('id'); }

	/**
	 * Sets the currency
	 * @param string $currency_code A valid currency code
	 * @return void
	 */
	public function SetCurrency($currency_code) { /* we stay with EUR */ }

	/**
	 * Creates an instance from an order id.
	 * @return IShopOrder The new/loaded order <Model>
	 */
	public static function FromOrderId($order_id)
	{
		return SampleShopOrder::Make()->eq('id',$order_id)->current();
	}

	/**
	 * Called when the order has failed.
	 * 
	 * This is a callback from the payment processor. Will be called when there was an error in the payment process.
	 * This can be synchronous (when cutsomer aborts in then initial payment ui) or asynchronous when something goes wrong
	 * later in the payment processors processes.
	 * @param int $payment_provider_type Provider type identifier (<PaymentProvider>::PROCESSOR_PAYPAL, <PaymentProvider>::PROCESSOR_GATE2SHOP, ...)
	 * @param mixed $transaction_id Transaction identifier (from the payment provider)
	 * @param string $statusmsg An optional status message
	 * @return void
	 */
	public function SetFailed($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::FAILED;
		$this->updated = $this->deleted = 'now()';
		$this->Save();
	}

	/**
	 * Called when the order has been paid.
	 * 
	 * This is a callback from the payment processor. Will be called when the customer has paid the order.
	 * @param int $payment_provider_type Provider type identifier (<PaymentProvider>::PROCESSOR_PAYPAL, <PaymentProvider>::PROCESSOR_GATE2SHOP, ...)
	 * @param mixed $transaction_id Transaction identifier (from the payment provider)
	 * @param string $statusmsg An optional status message
	 * @return void
	 */
	public function SetPaid($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::PAID;
		$this->updated = $this->completed = 'now()';
		$this->Save();
	}

	/**
	 * Called when the order has reached pending state.
	 * 
	 * This is a callback from the payment processor. Will be called when the customer has paid the order but the
	 * payment has not yet been finished/approved by the provider.
	 * @param int $payment_provider_type Provider type identifier (<PaymentProvider>::PROCESSOR_PAYPAL, <PaymentProvider>::PROCESSOR_GATE2SHOP, ...)
	 * @param mixed $transaction_id Transaction identifier (from the payment provider)
	 * @param string $statusmsg An optional status message
	 * @return void
	 */
	public function SetPending($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::PENDING;
		$this->updated = 'now()';
		$this->Save();
	}

	/**
	 * Called when the order has been refunded.
	 * 
	 * This is a callback from the payment processor. Will be called when the payment was refunded for any reason.
	 * This can be reasons from the provider and/or from the customer (when he cancels the payment later).
	 * @param int $payment_provider_type Provider type identifier (<PaymentProvider>::PROCESSOR_PAYPAL, <PaymentProvider>::PROCESSOR_GATE2SHOP, ...)
	 * @param mixed $transaction_id Transaction identifier (from the payment provider)
	 * @param string $statusmsg An optional status message
	 * @return void
	 */
	public function SetRefunded($payment_provider_type, $transaction_id, $statusmsg = false)
	{
		$this->status = self::REFUNDED;
		$this->updated = $this->deleted = 'now()';
		$this->Save();
	}

	/**
	 * Checks if VAT needs to be paid.
	 * @return boolean true or false
	 */
	public function DoAddVat() { return true; /* Let's assume normal VAT customers for now */ }
}