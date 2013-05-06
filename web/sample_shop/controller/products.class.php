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
