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
use ScavixWDF\Base\AjaxAction;
use ScavixWDF\Base\AjaxResponse;
use ScavixWDF\Base\Template;
use ScavixWDF\Controls\Form\Form;
use ScavixWDF\JQueryUI\Dialog\uiDialog;
use ScavixWDF\JQueryUI\uiButton;
use ScavixWDF\JQueryUI\uiDatabaseTable;
use ScavixWDF\JQueryUI\uiMessage;

class Admin extends ShopBase
{
	/**
	 * Checks if aa admin has logged in and redirects to login if not.
	 */
	private function _login()
	{
		// check only the fact that somebody logged in
		if( avail($_SESSION,'logged_in') )
			return true;

		// redirect to login. this terminates the script execution.
		redirect('Admin','Login');
	}

	/**
	 * @attribute[RequestParam('username','string',false)]
	 * @attribute[RequestParam('password','string',false)]
	 */
	function Login($username,$password)
	{
		// if credentials are given, try to log in
		if( $username && $password )
		{
			// see config.php for credentials
			if( $username==cfg_get('admin','username') && $password==cfg_get('admin','password') )
			{
				$_SESSION['logged_in'] = true; // check only the fact that somebody logged in
				redirect('Admin');
			}
			$this->content(uiMessage::Error("Unknown username/passsword"));
		}
		// putting it together as control here. other ways would be to create a new class
		// derived from Control or a Template (anonymous or with an own class)
		$form = $this->content(new Form());
		$form->content("Username:");
		$form->AddText('username', '');
		$form->content("<br/>Password:");
		$form->AddPassword('password', '');
		$form->AddSubmit("Login");
	}

	function Index()
	{
		$this->_login(); // require admin to be logged in

		// add products table and a button to create a new product
		$this->content("<h1>Products</h1>");
		$this->content(new uiDatabaseTable(model_datasource('system'),false,'products'))
			->AddPager(10)
			->AddRowAction('trash', 'Delete', $this, 'DelProduct');
		$this->content(uiButton::Textual('Add product'))->onclick = AjaxAction::Post('Admin', 'AddProduct');

		// add orders table
		$this->content("<h1>Orders</h1>");
		$this->content(new uiDatabaseTable(model_datasource('system'),false,'orders'))
			->AddPager(10)
			->OrderBy = 'id DESC';

		// add customers table
		$this->content("<h1>Customers</h1>");
		$this->content(new uiDatabaseTable(model_datasource('system'),false,'customers'))
			->AddPager(10)
			->OrderBy = 'id DESC';
	}

	/**
	 * @attribute[RequestParam('title','string',false)]
	 * @attribute[RequestParam('tagline','string',false)]
	 * @attribute[RequestParam('body','text',false)]
	 * @attribute[RequestParam('price','double',false)]
	 */
	function AddProduct($title,$tagline,$body,$price)
	{
		$this->_login(); // require admin to be logged in

		// This is a quite simple condition: You MUST provide each of the variables
		if( $title && $tagline && $body && $price )
		{
			// store the uploaded image if present
			if( isset($_FILES['image']) && $_FILES['image']['name'] )
			{
				$i = 1; $image = __DIR__.'/../images/'.$_FILES['image']['name'];
				while( file_exists($image) )
					$image = __DIR__.'/../images/'.($i++).'_'.$_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $image);
				$image = basename($image);
			}
			else
				$image = '';

			// store the new product into the database
			$ds = model_datasource('system');
			$ds->ExecuteSql("INSERT INTO products(title,tagline,body,image,price)VALUES(?,?,?,?,?)",
				array($title,$tagline,$body,$image,$price));

			redirect('Admin');
		}
		// create a dialog and put a template on it.
		$dlg = new uiDialog('Add product',array('width'=>600,'height'=>450));
		$dlg->content( Template::Make('admin_product_add') );
		$dlg->AddButton('Add product', "$('#frm_add_product').submit()"); // frm_add_product is defined in the template
		$dlg->AddCloseButton("Cancel");
		return $dlg;
	}

	/**
	 * @attribute[RequestParam('table','string',false)]
	 * @attribute[RequestParam('action','string',false)]
	 * @attribute[RequestParam('model','array',false)]
	 * @attribute[RequestParam('row','string',false)]
	 */
	function DelProduct($table,$action,$model,$row)
	{
		$this->_login(); // require admin to be logged in

		// we use the ajax confirm features of the framework which require some translated string, so we set them up here
		// normally we would start the sysadmin and create some, but for this sample we ignore that.
		default_string('TITLE_DELPRODUCT','Delete Product');
		default_string('TXT_DELPRODUCT','Do you really want to remove this product? This cannot be undone!');
		if( !AjaxAction::IsConfirmed('DELPRODUCT') )
			return AjaxAction::Confirm('DELPRODUCT', 'Admin', 'DelProduct', array('model'=>$model));

		// load and delete the product dataset
		$ds = model_datasource('system');
		$prod = $ds->Query('products')->eq('id',$model['id'])->current();
		$prod->Delete();

		// delete the image too if present
		if( $prod->image )
		{
			$image = __DIR__.'/../images/'.$prod->image;
			if( file_exists($image) )
				unlink($image);
		}
		return AjaxResponse::Redirect('Admin');
	}
}
