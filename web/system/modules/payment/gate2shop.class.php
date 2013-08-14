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

use ScavixWDF\Localization\CultureInfo;
use ScavixWDF\Localization\Localization;
use ScavixWDF\WdfException;

/**
 * Gate2Shop payment provider.
 * 
 */
class Gate2Shop extends PaymentProvider
{
	public $type = PaymentProvider::PROCESSOR_GATE2SHOP;
	public $type_name = "gate2shop";
	
	function __construct()
	{
		global $CONFIG;
		parent::__construct();
		
		if( !isset($CONFIG["payment"]["gate2shop"]["merchant_id"]) )
			WdfException::Raise("Gate2Shop: Missing merchant_id");

		if( !isset($CONFIG["payment"]["gate2shop"]["merchant_site_id"]) )
			WdfException::Raise("Gate2Shop: Missing merchant_site_id");
		
		if( !isset($CONFIG["payment"]["gate2shop"]["secret_key"]) )
			WdfException::Raise("Gate2Shop: Missing secret_key");
		
		$this->small_image = resFile("payment/gate2shop.png");
	}
	
	private function EnsureCurrency($order)
	{
		$currency = $order->GetCurrency();
		if( $currency instanceof CultureInfo )
		{
			if( $currency->IsNeutral() )
				$currency = $currency->DefaultRegion()->DefaultCulture();
			$currency = $currency->CurrencyFormat->Code;
		}
		$currency = strtoupper("$currency");
		switch( $currency )
		{
			case 'AUD':
			case 'CAD':
			case 'CHF':
			case 'DKK':
			case 'EUR':
			case 'GBP':
			case 'NOK':
			case 'SEK':
			case 'USD':
			case 'RMB':
			case 'YEN':
				return $currency;
			case 'JPY':
				return 'YEN';
			case 'CNY':
				return 'RMB';
		}
		log_warn("Gate2Shop: Invalid currency '$currency'. Falling back to EUR");
		return 'EUR';
	}
	
	private function SanitizeLocale($order)
	{
		$locale = $order->GetLocale();
		if( !$locale ) return false;
		
		if( is_string($locale) )
		{
			if( $tmp = Localization::getCultureInfo($locale) )
				$locale = $tmp;
		}
		if( $locale instanceof CultureInfo )
		{
			if( !$locale->IsNeutral() )
				$locale = $locale->ResolveToLanguage();
			$locale = $locale->Iso2;
		}
		$locale = strtolower($locale);
		switch( $locale )
		{
			case 'en': return 'en_US';
			case 'it': return 'it_IT';
			case 'es': return 'es_ES';
			case 'fr': return 'fr_FR';
			case 'iw': return 'iw_IL';
			case 'de': return 'de_DE';
			case 'ar': return 'ar_AA';
			case 'ru': return 'ru_RU';
			case 'nl': return 'nl_NL';
			case 'bg': return 'bg_BG';
			case 'ja': return 'ja_JP';
			case 'tr': return 'tr_TR';
			case 'pt': return 'pt_BR';
			case 'zh': return 'zh_CN';
			case 'lt': return 'lt_LT';
			case 'sv': return 'sv_SE';
			case 'sl': return 'sl_SL';
			case 'da': return 'da_DK';
			case 'pl': return 'pl_PL';
		}
		log_warn("Gate2Shop: Invalid locale '$locale'. Skipping argument");
		return false;
	}
	
	private function CalcChecksum()
	{
		global $CONFIG;
		$content = $CONFIG["payment"]["gate2shop"]["secret_key"]
			.$this->data['merchant_id']
			.$this->data['currency']
			.$this->data['total_amount'];
		
		for($i=1; $i<=$this->data['numberofitems']; $i++ )
		{
			$content .= $this->data["item_name_$i"]
				.$this->data["item_amount_$i"]
				.$this->data["item_quantity_$i"];
		}
		
		$content .= $this->data['time_stamp'];
		return md5($content);
	}
	
	/**
	 * @override
	 */
	public function StartCheckout(IShopOrder $order, $ok_url=false, $cancel_url=false)
	{
		global $CONFIG;
		
		if( $ok_url )
			log_info('Gate2Shop does not allow to pass a return URL, so ignoring it.');
		
		// merchant details
		$this->SetVar('merchant_id', $CONFIG["payment"]["gate2shop"]["merchant_id"]);
		$this->SetVar('merchant_site_id', $CONFIG["payment"]["gate2shop"]["merchant_site_id"]);
		
		// merchant site details
		if( isset($CONFIG["payment"]["gate2shop"]["customSiteName"]) ) 
			$this->SetVar('customSiteName', $CONFIG["payment"]["gate2shop"]["customSiteName"]);
		
		$order_currency = $this->EnsureCurrency($order);
		
		// item details
		$items = $order->ListItems();
		if( count($items) > 0 )
		{
			$this->SetVar('numberofitems',count($items));
			$i = 1;
			$total_amount = 0;
			foreach( $items as $item )
			{
				$this->SetVar("item_name_$i",$item->GetName());
				$this->SetVar("item_number_$i",$i);
				$this->SetVar("item_amount_$i", round($item->GetAmount($order_currency), 2));
				$this->SetVar("item_quantity_$i",$item->GetQuantity());
				if( $tmp = $item->GetShipping() ) $this->SetVar("item_shipping_$i",$tmp);
				if( $tmp = $item->GetHandling() ) $this->SetVar("item_handling_$i",$tmp);
				if( $tmp = $item->GetDiscount() ) $this->SetVar("item_discount_$i",$tmp);
				
				$i++;
				$total_amount += round($order->GetTotalPrice($item->GetAmount($order_currency)), 2);
			}
			$this->SetVar('total_amount', round($total_amount, 2));
			$this->SetVar('total_tax', $order->GetVatPercent());
		}
		
		// customer details
		$address = $order->GetAddress();
		if( $address->Firstname ) $this->SetVar('first_name', substr($address->Firstname, 0, 30));
		if( $address->Lastname ) $this->SetVar('last_name', substr($address->Lastname, 0, 40));
		if( $address->Email ) $this->SetVar('email', substr($address->Email, 0, 100));
		if( $address->Address1 ) $this->SetVar('address1', substr($address->Address1, 0, 60));
		if( $address->Address2 ) $this->SetVar('address2', substr($address->Address2, 0, 60));
		if( $address->Country ) $this->SetVar('country',$address->Country);
		if( $address->State ) $this->SetVar('state',$address->State);
		if( $address->Zip ) $this->SetVar('zip', substr($address->Zip, 0, 10));
		if( $address->City ) $this->SetVar('city', substr($address->City, 0, 30));
		if( $address->Phone1 ) $this->SetVar('phone1',$address->Phone1);
		if( $address->Phone2 ) $this->SetVar('phone2',$address->Phone2);
		if( $address->Phone3 ) $this->SetVar('phone3',$address->Phone3);
		
		// other parameters
		$this->SetVar('version', '3.0.0');
		$this->SetVar('currency', $order_currency );
		$this->SetVar('time_stamp', gmdate('Y-m-d H:i:s'));
		
		if( $tmp = $order->GetInvoiceId() ) 
		{
			$this->SetVar('invoice_id', $tmp);
			$this->SetVar('merchant_unique_id', $tmp);		// invoice_id is not shown in g2s admin. but this one is...
		}
		if( $tmp = $this->SanitizeLocale($order) ) $this->SetVar('merchantLocale',$tmp);
		
		// finally create the checksum
		$this->SetVar('checksum', $this->CalcChecksum());
		$checkouturl = "https://secure.gate2shop.com/ppp/purchase.do";
		return $this->CheckoutForm($checkouturl);
	}
	
/**
	 * Verify that the IPN is a valid IPN call from G2S
	 */
	private function CheckIPNCall(IShopOrder $order, $ipndata)
	{
		global $CONFIG;
		foreach(array("PPP_TransactionID", "ppp_status", "responsechecksum") as $k => $v)
		{
			if(!isset($ipndata[$v]))
			{
				log_error("Gate2Shop: Missing IPN parameter: $v");
				return false;
			}
		}
		
		// check checksum
		$checksum = md5($CONFIG["payment"]["gate2shop"]["secret_key"].$ipndata["ppp_status"].$ipndata["PPP_TransactionID"]);
//		log_debug($checksum." -> ".$CONFIG["payment"]["gate2shop"]["secret_key"]."-".$ipndata["ppp_status"]."-".$ipndata["PPP_TransactionID"]);
		if($checksum != $ipndata["responsechecksum"])
		{
			log_error("Gate2Shop: Checksum don't consist with response");
			return false;
		}
		
		return true;
	}
	
	/**
	 * @override
	 */
	public function HandleIPN($ipndata)
	{
		global $CONFIG;
		$order_id = $ipndata["invoice_id"];
		if(starts_with($order_id, $CONFIG["invoices"]["invoice_id_prefix"]))
			$order_id = trim(str_replace($CONFIG["invoices"]["invoice_id_prefix"], "", $order_id));		
		
		$order = $this->LoadOrder($order_id);
		if( !$order )
			return "Order id $order_id not found";   // order not found
		
		if(!$this->CheckIPNCall($order, $ipndata))
			return "Invalid IPN parameters";
		
		return $this->HandlePayment($order, $ipndata);
	}
	
	/**
	 * @override
	 */
	public function SanitizeStatusFromPP($status, $ipndata)
	{
		// g2s has some weird cancel return values from the PPP page when user clicks "cancel" (mantis #8199)
		if(($ipndata["status"] == "failure") && ($ipndata["ppp_status"] == "CANCEL"))
			return "cancel";
		return $status;
	}
	
	/**
	 * @override
	 */
	public function HandleReturnFromPP($ipndata)
	{
		global $CONFIG;
		foreach(array("totalAmount", "currency", "responseTimeStamp", "PPP_TransactionID", "Status", "productId", "advanceResponseChecksum") as $k => $v)
		{
			if(!isset($ipndata[$v]))
			{
				log_error("Gate2Shop: Missing IPN parameter: $v");
				return false;
			}
		}
		
		// check checksum
		$checksum = md5($CONFIG["payment"]["gate2shop"]["secret_key"].$ipndata["totalAmount"].$ipndata["currency"].$ipndata["responseTimeStamp"].$ipndata["PPP_TransactionID"].$ipndata["Status"].$ipndata["productId"]);
//		log_debug($checksum." -> ".$CONFIG["payment"]["gate2shop"]["secret_key"]."-".$ipndata["ppp_status"]."-".$ipndata["PPP_TransactionID"]);
		if($checksum != $ipndata["advanceResponseChecksum"])
		{
			log_error("Gate2Shop: Checksum don't consist with response");
			return false;
		}
		
		$order_id = $ipndata["invoice_id"];
		if(starts_with($order_id, $CONFIG["invoices"]["invoice_id_prefix"]))
			$order_id = trim(str_replace($CONFIG["invoices"]["invoice_id_prefix"], "", $order_id));		
		$ds = model_datasource('system');
		$order = $ds->CreateInstance("ShopOrder");
		if(!$order->Load("id=?", $order_id))
			return "Order id $order_id not found";   // order not found
		
		return $this->HandlePayment($order, $ipndata);
	}
	
	private function HandlePayment(IShopOrder $order, $ipndata)
	{
		$payment_status = strtolower($ipndata["ppp_status"]);
		$transaction_id = $ipndata["TransactionID"];		
		$statusmsg = false;
		
		if(strval($ipndata["ErrCode"])=='0' && strval($ipndata["ExErrCode"])=='-2')
		{
			$payment_status = "pending";
			$statusmsg = "Your order will be reviewed manually";
		}
		elseif(strval($ipndata["ErrCode"])!='0' || strval($ipndata["ExErrCode"])!='0' || ($ipndata["ppp_status"] == "FAIL"))
		{
			if(isset($ipndata["Error"]) && strlen($ipndata["Error"]))
				$statusmsg = "ErrCode: ".$ipndata["ErrCode"].", ExErrCode: ".$ipndata["ExErrCode"]." ".$ipndata["Reason"].", ".$ipndata["Error"];
			else
				$statusmsg = "ErrCode: ".$ipndata["ErrCode"].", ExErrCode: ".$ipndata["ExErrCode"].", "."Your transaction has been declined.";
			
			$payment_status = "failed";
		}

		if( isset($ipndata["transactionType"]) && (
			($ipndata["transactionType"] == "Credit") 
			|| ($ipndata["transactionType"] == "ChargeBack") 
			|| ($ipndata["transactionType"] == "Void") 
			|| (($ipndata["transactionType"] == "Modification") && ($ipndata["Status"] == "CANCELED"))
			))
		{
			$payment_status = "refunded";
		}
		
		switch($payment_status)
		{
			case "pending":
				$order->SetPending(PaymentProvider::PROCESSOR_GATE2SHOP, $transaction_id, $statusmsg);
				break;

			case "ok":
				$order->SetPaid(PaymentProvider::PROCESSOR_GATE2SHOP, $transaction_id, $statusmsg);
				break;
			
			case "failed":
				$order->SetFailed(PaymentProvider::PROCESSOR_GATE2SHOP, $transaction_id, $statusmsg);
				break;
			
			case "refunded":
				$order->SetRefunded(PaymentProvider::PROCESSOR_GATE2SHOP, $transaction_id, $statusmsg);
				break;

			default:
				return "Unkown payment status: $payment_status";
				break;
		}
		
		return true;
	}
	
}
