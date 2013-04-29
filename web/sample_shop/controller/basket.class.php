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
			$price_total = 0;
			foreach( $_SESSION['basket'] as $id=>$amount )
			{
				$ds = model_datasource('system');
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
}
