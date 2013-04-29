<?php

class Products extends ShopBase
{
	/**
	 * @attribute[RequestParam('error','string',false)]
	 */
	function Index($error)
	{
		if( $error )
			$this->content(uiMessage::Error($error));
		
		$ds = model_datasource('system');
		foreach( $ds->Query('products')->orderBy('title') as $prod )
		{
			$this->content( Template::Make('product_overview') )
				->set('title',$prod->title)
				->set('tagline',$prod->tagline)
				->set('image',resFile($prod->image)) // see config.php where we set up products images folder as resource folder
				->set('link',buildQuery('Products','Details',array('id'=>$prod->id)))
				;
		}
	}
	
	/**
	 * @attribute[RequestParam('id','int')]
	 */
	function Details($id)
	{
		$ds = model_datasource('system');
		$prod = $ds->Query('products')->eq('id',$id)->current();
		if( !$prod )
			redirect('Products','Index',array('error'=>'Product not found'));
		
		$this->content( Template::Make('product_details') )
			->set('title',$prod->title)
			->set('description',$prod->body)
			->set('image',resFile($prod->image)) // see config.php where we set up products images folder as resource folder
			->set('link',buildQuery('Basket','Add',array('id'=>$prod->id)))
			;
	}
}
