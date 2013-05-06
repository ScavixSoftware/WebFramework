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
 * Payment provider for testing.
 * 
 */
class TestingPaymentProvider extends PaymentProvider
{
	public $type = PaymentProvider::PROCESSOR_TESTING;
	public $type_name = "testing";
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @override
	 */
	public function IsAvailable()
	{
		return isDevOrBeta();		// this one is only available on dev and beta
	}	
	
	/**
	 * @override Testing provider only sets order paid and forwards to dashboard
	 */
	public function StartCheckout(IShopOrder $order, $ok_url=false, $cancel_url=false)
	{
		$order->SetPaid(PaymentProvider::PROCESSOR_TESTING, -1);
		$order->Save();
		
		if( $ok_url )
		{
			$data = http_build_query(array("provider" => $this->type_name, "status" => "ok", "invoice_id" => $order->GetInvoiceId()));
			$ok_url .= (stripos($ok_url,'?')!==false?'&':'?').$data;
			redirect($ok_url);
		}

		return true;
	}
}
