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
 * Payment providers must extend this class.
 * 
 */
abstract class PaymentProvider
{
	public $title = "";
	public $type = null;
	public $type_name = null;
	public $small_image = null;
	protected $data = array();
	
	const PROCESSOR_INTERNAL	= 0; //"internal";
	const PROCESSOR_PAYPAL		= 1; //"paypal";
	const PROCESSOR_GATE2SHOP	= 2; //"gate2shop";
	const PROCESSOR_TESTING		= 3; //"test";
	
	protected function LoadOrder($order_id)
	{
		return call_user_func("{$GLOBALS['CONFIG']["payment"]["order_model"]}::FromOrderId",$order_id);
	}
	
	/**
	 * Sets data for the PaymentProvider.
	 * 
	 * @param string $name Argument name
	 * @param mixed $value Argument value
	 * @return PaymentProvider `$this`
	 */
	public function SetVar($name,$value)
	{
		$this->data[$name] = $value;
		return $this;
	}
	
	function __construct()
	{
		$this->title = "TXT_PAYMENTPROVIDER_".strtoupper(get_class($this));
	}
	
	/**
	 * Possibility to disable/enable the payment provider list
	 * @return type 
	 */
	public function IsAvailable()
	{
		return true;
	}
	
	/**
	 * Handle the IPN (called DMN at g2s, ...) call from the payment provider
	 * @param type $ipndata Array with IPN data (i.e. POST data) from payment provider
	 * @return bool|string True if everything went well, errormessage as string otherwise 
	 */
	public function HandleIPN($ipndata)
	{
		return true;
	}
	
	/**
	 * Ensure a valid processor_id
	 * @param string $processor_id One of PROCESSOR_PAYPAL or PROCESSOR_GATE2SHOP.
	 * @return string One of PROCESSOR_PAYPAL or PROCESSOR_GATE2SHOP.
	 */
	static function SanitizePaymentProcessorId($processor_id)
	{
		switch($processor_id)
		{
			case self::PROCESSOR_INTERNAL:
			case self::PROCESSOR_GATE2SHOP:
			case self::PROCESSOR_PAYPAL:
			case self::PROCESSOR_TESTING:
				return $processor_id;
				
			default:
				return self::PROCESSOR_PAYPAL;
		}
	}	
	
	protected function Redirect($url)
	{
		$q = array();
		foreach( $this->data as $k=>$v )
			$q[] = "$k=".urldecode($v);
		log_debug(get_class($this)."::Redirect -> $url?$q",$url,$q,$this->data);
		redirect("$url?$q");
	}
	
	protected function CheckoutForm($url)
	{
		$form = new Form();
		$form->action = $url;
		$form->method = 'post';
		$form->class = 'nocsrf';
		foreach( $this->data as $k=>$v )
			$form->AddHidden($k,$v);
		$form->script("$('#{$form->id}').submit();");
		return $form;
	}
	
	/**
	 * Starts the checkout process
	 * 
	 * @param IShopOrder $order The order to start checkout for
	 * @return Form Must return a <Form> control 
	 */
	abstract public function StartCheckout(IShopOrder $order);
	
	/**
	 * Correct the status from the arguments passed by the PP.
	 * 
	 * @param string $status status passed by PP
	 * @param array $ipndata data from the PP
	 * @return string the status 
	 */
	public function SanitizeStatusFromPP($status, $ipndata)
	{
		return $status;
	}
	
	/**
	 * Process the user returning from the PP.
	 * 
	 * @param mixed $ipndata Data returned from PP
	 * @return bool currently always true
	 */
	public function HandleReturnFromPP($ipndata) 
	{
		return true;
	}
}

/**
 * Order <Model>s must implement this interface.
 */
interface IShopOrder
{
	/**
	 * Creates an instance from an order id.
	 * @return IShopOrder The new/loaded order <Model>
	 */
	static function FromOrderId($order_id);
	
	/**
	 * Gets the invoice ID.
	 * @return mixed Invoice identifier
	 */
	function GetInvoiceId();
	
	/**
	 * Gets the currency code.
	 * @return string A valid currency code
	 */
	function GetCurrency();
	
	/**
	 * Sets the currency
	 * @param string $currency_code A valid currency code
	 * @return void
	 */
	function SetCurrency($currency_code);
	
	/**
	 * Gets the order culture code.
	 * 
	 * See <CultureInfo>
	 * @return string Valid culture code
	 */
	function GetLocale();
	
	/**
	 * Returns all items.
	 * 
	 * @return array A list of <IShopOrderItem> objects
	 */
	function ListItems();
	
	/**
	 * Gets the orders address.
	 * @return ShopOrderAddress The order address
	 */
	function GetAddress();
	
    /**
	 * Return the total price incl. VAT (if VAT applies for the given country). 
	 * @param float $price The price without VAT.
	 * @return float Price including VAT (if VAT applies for the country).
	 */
	function GetTotalPrice($price = false);
    
    /**
	 * Return the total VAT (if VAT applies for the given country). 
	 * @return float VAT in order currency
	 */
	function GetTotalVat();
    
    /**
	 * Return the total VAT percent (if VAT applies for the given country). 
	 * @return float VAT percent
	 */
	function GetVatPercent();
}

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

