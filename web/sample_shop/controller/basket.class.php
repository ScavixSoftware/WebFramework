<?php

class Basket extends ShopBase
{
	/**
	 * @attribute[RequestParam('error','string',false)]
	 */
	function Index($error)
	{
		if( $error )
			$this->content(uiMessage::Error($error));
		
		if( !isset($_SESSION['basket']) )
			$_SESSION['basket'] = array();
		
		if( count($_SESSION['basket']) == 0 )
			$this->content(uiMessage::Hint('Basket is empty'));
		else
		{
			$ds = model_datasource('system');
			$price_total = 0;
			foreach( $_SESSION['basket'] as $id=>$amount )
			{
				$prod = $ds->Query('products')->eq('id',$id)->current();
				
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
			$this->content("<div class='basket_total'>Total price: $price_total</div>");
			$this->content( uiButton::Make("Buy now") )->onclick = "location.href = '".buildQuery('Basket','BuyNow')."'";
		}
	}
	
	/**
	 * @attribute[RequestParam('id','int')]
	 */
	function Add($id)
	{
		$ds = model_datasource('system');
		$prod = $ds->Query('products')->eq('id',$id)->current();
		if( !$prod )
			redirect('Basket','Index',array('error'=>'Product not found'));
		
		if( !isset($_SESSION['basket'][$id]) )
			$_SESSION['basket'][$id] = 0;
		$_SESSION['basket'][$id]++;
		redirect('Basket','Index');
	}
	
	/**
	 * @attribute[RequestParam('id','int')]
	 */
	function Remove($id)
	{
		$ds = model_datasource('system');
		$prod = $ds->Query('products')->eq('id',$id)->current();
		if( !$prod )
			redirect('Basket','Index',array('error'=>'Product not found'));
		
		if( isset($_SESSION['basket'][$id]) )
			$_SESSION['basket'][$id]--;
		if( $_SESSION['basket'][$id] == 0 )
			unset($_SESSION['basket'][$id]);
		redirect('Basket','Index');
	}
	
	function BuyNow()
	{
		$this->content( Template::Make('checkout_form') );
	}
	
	/**
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
		
		$cust = new SampleCustomer();
		$cust->fname = $fname;
		$cust->lname = $lname;
		$cust->street = $street;
		$cust->zip = $zip;
		$cust->city = $city;
		$cust->email = $email;
		$cust->price_total = 0;
		$cust->Save();

		$order = new SampleShopOrder();
		$order->customer_id = $cust->id;
		$order->created = 'now()';
		$order->Save();
		
		$ds = model_datasource('system');
		foreach( $_SESSION['basket'] as $id=>$amount )
		{
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
		$order->Save();
		$_SESSION['basket'] = array();
		
		log_debug("Handing control over to payment provider '$provider'");
		$p = new $provider();
		$p->StartCheckout($order,buildQuery('Basket','PostPayment'));
	}
	
	function PostPayment()
	{
		log_debug("PostPayment",$_REQUEST);
		$this->content("<h1>Payment processed</h1>");
		$this->content("Provider returned this data:<br/><pre>".render_var($_REQUEST)."</pre>");
	}
}
