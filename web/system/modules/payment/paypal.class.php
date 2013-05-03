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
 * PayPal payment provider.
 * 
 */
class PayPal extends PaymentProvider
{
	public $type = PaymentProvider::PROCESSOR_PAYPAL;
	public $type_name = "paypal";
	
	function __construct()
	{
		global $CONFIG;
		parent::__construct();
		if(!isset($CONFIG["payment"]) || !isset($CONFIG["payment"]["paypal"]))
			WdfException::Raise("PayPal payment provider not configured");
		
		$this->small_image = resFile("payment/paypal.gif");
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
			case 'CZK':
			case 'DKK':
			case 'EUR':
			case 'HKD':
			case 'HUF':
			case 'ILS':
			case 'MXN':
			case 'NOK':
			case 'NZD':
			case 'PHP':
			case 'PLN':
			case 'GBP':
			case 'SGD':
			case 'SEK':
			case 'CHF':
			case 'TWD':
			case 'THB':
			case 'TRY':
			case 'USD':
				return $currency;
		}
		log_warn("PayPal: Invalid currency '$currency'. Falling back to EUR");
		return 'EUR';
	}
	
	/**
	 * @override
	 */
	public function StartCheckout(IShopOrder $order)
	{
		global $CONFIG;
		
		$invoice_id = false;
		if( $tmp = $order->GetInvoiceId() )
			$invoice_id = $tmp;
		
		$this->SetVar("cmd", "_cart");
		$this->SetVar("upload", "1");
		$order_currency = $this->EnsureCurrency($order);
		$order->SetCurrency($order_currency);
		$this->SetVar("currency_code", $order_currency);
		$this->SetVar("charset", "utf-8");
//		set language of paypal UI:
//		$this->SetVar("lc", );
		
		if($CONFIG["payment"]["paypal"]["use_sandbox"] == true)
		{
			$this->SetVar("sandbox", "1");
			$checkoutUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		}
		else
			$checkoutUrl = "https://www.paypal.com/cgi-bin/webscr";
		
		$this->SetVar('business', $CONFIG["payment"]["paypal"]["paypal_id"]);
		$this->SetVar('custom', $CONFIG["payment"]["paypal"]["custom"]);
		
		$this->SetVar('return', $CONFIG["payment"]["paypal"]["returnurl"].($invoice_id ? (strpos($CONFIG["payment"]["paypal"]["returnurl"], "?") === false ? "?" : "&")."order_id=$invoice_id" : ""));
		$this->SetVar('cancel_return', $CONFIG["payment"]["paypal"]["cancelreturnurl"].($invoice_id ? (strpos($CONFIG["payment"]["paypal"]["cancelreturnurl"], "?") === false ? "?" : "&")."order_id=$invoice_id" : ""));
		$params = array("provider" => "paypal");
		$notify_url = buildQuery("Shopping", "PPNotification", $params, $CONFIG["payment"]["paypal"]["ipnurl"]);
		$this->SetVar('notify_url', $notify_url);
		
		// customer details
		$address = $order->GetAddress();
		if( $address->Firstname ) $this->SetVar('first_name',$address->Firstname);
		if( $address->Lastname ) $this->SetVar('last_name',$address->Lastname);
		if( $address->Email ) $this->SetVar('email',$address->Email);
		if( $address->Address1 ) $this->SetVar('address1',$address->Address1);
		if( $address->Address2 ) $this->SetVar('address2',$address->Address2);
		if( $address->Country ) $this->SetVar('country',$address->Country);
		if( $address->State ) $this->SetVar('state',$address->State);
		if( $address->Zip ) $this->SetVar('zip',$address->Zip);
		if( $address->City ) $this->SetVar('city',$address->City);
		// tell paypal to use this entered address:
		$this->SetVar('address_override', 1);
		$this->SetVar('bn', "ScendixSoftware_BuyNow_".$CONFIG["payment"]["paypal"]["custom"]."_DE");
		// do not let users add notes in paypal:
		$this->SetVar('no_note', 1);
		
		/* Return method. The  FORM METHOD used to send data to the 
		URL specified by the  return variable. 
		Allowable values are: 
		0 – all shopping cart payments use the GET  method 
		1 – the buyer’s browser is re directed to the return URL 
		by using the GET  method, but no payment variables are 
		included 
		2 – the buyer’s browser is re directed to the return URL 
		by using the POST method, and all payment variables are 
		included */
		$this->SetVar('rm', 1);
		
		if( $invoice_id ) 
			$this->SetVar('invoice', $invoice_id);
		
		$items = $order->ListItems();
		if( count($items) > 0 )
		{
			$i = 1;
			foreach( $items as $item )
			{
				$price = $item->GetAmount();
				$this->SetVar("item_name_$i",$item->GetName());
				$this->SetVar("amount_$i", round($item->GetAmount($order_currency), 2));
				if($order->DoAddVat())
					$this->SetVar("tax_$i", round($item->GetAmount($order_currency) * ($CONFIG['model']['vat_percent']/100), 2));
				$this->SetVar("quantity_$i", 1);
				
				$i++;
			}
		}
		
		$this->SetVar("tax_cart", round($order->GetTotalVat(), 2));

//		log_debug($this->data);
		return $this->CheckoutForm($checkoutUrl);
	}
	
	/**
	 * Verify that the IPN is a valid IPN call from PayPal
	 */
	private function CheckIPNCall(IShopOrder $order, $ipndata)
	{
		global $CONFIG;
		$paypal_url = ($CONFIG["payment"]["paypal"]["use_sandbox"] ? "www.sandbox.paypal.com" : "www.paypal.com");
		
		// check if the money really was sent to us
		if($ipndata["receiver_email"] != $CONFIG["payment"]["paypal"]["paypal_id"])
		{
			log_error("Wrong recipient PayPal email in IPN call: ".$ipndata["receiver_email"]." instead of ".$CONFIG["payment"]["paypal"]["paypal_id"]);
			return false;
		}
		
		// check if the order amount does match (10% currency difference is ok)
		if($order->amount_currency > ($ipndata["payment_gross"] * 1.1) )
		{
			log_error("Wrong order payment amount in PayPal IPN call: ".$ipndata["payment_gross"]." instead of ".$order->amount_currency);
			return false;
		}
		
		$header = ""; 
		// Read the post from PayPal and add 'cmd' 
		$req = 'cmd=_notify-validate'; 
		if(function_exists('get_magic_quotes_gpc')) 
			$get_magic_quotes_exists = true;
		
		foreach ($ipndata as $key => $value) 
		{  
			// Handle escape characters, which depends on setting of magic quotes 
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
				$value = urlencode(stripslashes($value)); 
			else
				$value = urlencode($value); 
		 
			$req .= "&$key=$value"; 
		} 
		// Post back to PayPal to validate 
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n"; 
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n"; 
		
		$fp = fsockopen("ssl://" . $paypal_url, 443, $errno, $errstr, 30);

		// Process validation from PayPal 
		if(!$fp)
		{ 
			// HTTP ERROR
			log_error("Unable to verify PayPal IPN call");
			return false;
		}
		else
		{
			// NO HTTP ERROR 
			fputs ($fp, $header . $req); 
			while (!feof($fp)) 
			{
				$res = fgets ($fp, 1024); 
				if(strcmp($res, "VERIFIED") == 0) 
				{
					return true;
					break;
				}
				else if(strcmp($res, "INVALID") == 0) 
				{
					// log for manual investigation
					log_error("PayPal IPN Verification failed: ".$res." ".$paypal_url."?".$req);
//					mail("<somevalidmailaddress>", "Invalid PayPal IPN Request #$order_id", "Order #$order_id returned $res in PayPal recheck:\r\n\r\n".var_export($_REQUEST, true));
					break;
				}
			}
		}
		fclose($fp);		
		return false;
	}
	
	/**
	 * @override
	 */
	public function HandleIPN($ipndata)
	{
		global $CONFIG;
		// strip off the prefix (needed to have unique invoice_ids at paypal):
		$order_id = $ipndata["invoice"];
		if(starts_with($order_id, $CONFIG["invoices"]["invoice_id_prefix"]))
			$order_id = trim(str_replace($CONFIG["invoices"]["invoice_id_prefix"], "", $order_id));
		$payment_status = strtolower($ipndata["payment_status"]);
		$transaction_id = $ipndata["txn_id"];

		$order = $this->LoadOrder($order_id);
		if( !$order )
		{
			// order not found
			return "Order id $order_id not found";
		}
		
		if(!$this->CheckIPNCall($order, $ipndata))
			return "Invalid IPN parameters";

		switch($payment_status)
		{
			case "pending":
				$order->SetPending(PaymentProvider::PROCESSOR_PAYPAL, $transaction_id, $ipndata["pending_reason"]);
				break;

			case "completed":
				$order->SetPaid(PaymentProvider::PROCESSOR_PAYPAL, $transaction_id);
				break;

			case "failed":
				$order->SetFailed(PaymentProvider::PROCESSOR_PAYPAL, $transaction_id);
				break;

			case "refunded":
				$order->SetRefunded(PaymentProvider::PROCESSOR_PAYPAL, $transaction_id);
				break;

			default:
				return "Unkown payment status: $payment_status";
				break;
		}
		
		return true;
	}
}
