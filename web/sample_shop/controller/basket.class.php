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

class Basket extends ShopBase
{
	/**
	 * Lists all items in the basket.
	 * @attribute[RequestParam('error','string',false)]
	 */
	function Index($error)
	{
		// display any given error message
		if( $error )
			$this->content(uiMessage::Error($error));
		
		// prepare basket variable
		if( !isset($_SESSION['basket']) )
			$_SESSION['basket'] = array();
		
		if( count($_SESSION['basket']) == 0 )
			$this->content(uiMessage::Hint('Basket is empty'));
		else
		{
			// list all items in the basket ...
			$ds = model_datasource('system');
			$price_total = 0;
			foreach( $_SESSION['basket'] as $id=>$amount )
			{
				$prod = $ds->Query('products')->eq('id',$id)->current();
				
				//... each using a template
				$this->content( Template::Make('product_basket') )
					->set('title',$prod->title)
					->set('amount',$amount)
					->set('price',$prod->price)
					->set('image',resFile($prod->image)) // see config.php where we set up products images folder as resource folder
					->set('add',buildQuery('Basket','Add',array('id'=>$prod->id)))
					->set('remove',buildQuery('Basket','Remove',array('id'=>$prod->id)))
					;
				$price_total += $amount * $prod->price;
			}
			// display total price and the button to go on
			$this->content("<div class='basket_total'>Total price: $price_total</div>");
			$this->content( uiButton::Make("Buy now") )->onclick = "location.href = '".buildQuery('Basket','BuyNow')."'";
		}
	}
	
	/**
	 * Adds a product to the basket.
	 * @attribute[RequestParam('id','int')]
	 */
	function Add($id)
	{
		// check if the product exists
		$ds = model_datasource('system');
		$prod = $ds->Query('products')->eq('id',$id)->current();
		if( !$prod )
			redirect('Basket','Index',array('error'=>'Product not found'));

		// increase the counter for this product
		if( !isset($_SESSION['basket'][$id]) )
			$_SESSION['basket'][$id] = 0;
		$_SESSION['basket'][$id]++;
		redirect('Basket','Index');
	}
	
	/**
	 * Removes an item from the basket.
	 * @attribute[RequestParam('id','int')]
	 */
	function Remove($id)
	{
		// check if the product exists
		$ds = model_datasource('system');
		$prod = $ds->Query('products')->eq('id',$id)->current();
		if( !$prod )
			redirect('Basket','Index',array('error'=>'Product not found'));
		
		// decrease the counter for this product
		if( isset($_SESSION['basket'][$id]) )
			$_SESSION['basket'][$id]--;
		// and unset if no more items left
		if( $_SESSION['basket'][$id] == 0 )
			unset($_SESSION['basket'][$id]);
		redirect('Basket','Index');
	}

	/**
	 * Entrypoint for the checkout process.
	 * 
	 * Requests customers address details and asks for payment processor.
	 */
	function BuyNow()
	{
		// displays the chechout form, which has all inputs for address on it
		$this->content( Template::Make('checkout_form') );
	}
	
	/**
	 * Persists current basket to the database and starts checkout process.
	 * @attribute[RequestParam('fname','string')]
	 * @attribute[RequestParam('lname','string')]
	 * @attribute[RequestParam('street','string')]
	 * @attribute[RequestParam('zip','string')]
	 * @attribute[RequestParam('city','string')]
	 * @attribute[RequestParam('email','string')]
	 * @attribute[RequestParam('provider','string')]
	 */
	function StartCheckout($fname,$lname,$street,$zip,$city,$email,$provider)
	{
		log_debug("StartCheckout($fname,$lname,$street,$zip,$city,$email,$provider)");
		
		if( !$fname || !$lname || !$street || !$zip || !$city || !$email )
			redirect('Basket','Index',array('error'=>'Missing some data'));
		
		// create a new customer. note that we do not check for existance or stuff.
		// this should be part of a real shop system!
		$cust = new SampleCustomer();
		$cust->fname = $fname;
		$cust->lname = $lname;
		$cust->street = $street;
		$cust->zip = $zip;
		$cust->city = $city;
		$cust->email = $email;
		$cust->price_total = 0;
		$cust->Save();

		// create a new order and assign the customer (from above)
		$order = new SampleShopOrder();
		$order->customer_id = $cust->id;
		$order->created = 'now()';
		$order->Save();
		
		// now loop thru the basket-items and add them to the order...
		$ds = model_datasource('system');
		foreach( $_SESSION['basket'] as $id=>$amount )
		{
			//... by creating a dataset for each item
			$prod = $ds->Query('products')->eq('id',$id)->current();
			$item = new SampleShopOrderItem();
			$item->order_id = $order->id;
			$item->price = $prod->price;
			$item->amount = $amount;
			$item->title = $prod->title;
			$item->tagline = $prod->tagline;
			$item->body = $prod->body;
			$item->Save();
			
			$order->price_total += $amount * $prod->price;
		}
		// save the order again to persist the total amount
		$order->Save();
		$_SESSION['basket'] = array();
		
		// finally start the checkout process using the given payment provider
		log_debug("Handing control over to payment provider '$provider'");
		$p = new $provider();
		$p->StartCheckout($order,buildQuery('Basket','PostPayment'));
	}
	
	/**
	 * This is the return URL for the payment provider.
	 * Will be called when payment raches a final state, so control is handed over to our 
	 * app again from the payment processor.
	 */
	function PostPayment()
	{
		// we just display the $_REQUEST data for now. in fact this is the point where some processing
		// should take place: send email to the team, that prepares the items for shipping, send email(s) to customer,...
		log_debug("PostPayment",$_REQUEST);
		$this->content("<h1>Payment processed</h1>");
		$this->content("Provider returned this data:<br/><pre>".render_var($_REQUEST)."</pre>");
	}
}
