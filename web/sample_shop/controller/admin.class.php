<?php

class Admin extends ShopBase
{
	private function _login()
	{
		// check only the fact that somebody logged in
		if( $_SESSION['logged_in'] ) 
			return true;
		
		redirect('Admin','Login');
	}
	
	/**
	 * @attribute[RequestParam('username','string',false)]
	 * @attribute[RequestParam('password','string',false)]
	 */
	function Login($username,$password)
	{
		if( $username && $password )
		{
			// hardcoded credentials are okay for now
			if( $username=='admin' && $password=='admin')
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
		$this->_login();
		$this->content(new uiDatabaseTable(model_datasource('system'),false,'products'))
			->AddRowAction('trash', 'Delete', $this, 'DelProduct');
		$this->content(uiButton::Make('Add product'))->onclick = AjaxAction::Post('Admin', 'AddProduct');
	}
	
	/**
	 * @attribute[RequestParam('title','string',false)]
	 * @attribute[RequestParam('tagline','string',false)]
	 * @attribute[RequestParam('body','text',false)]
	 * @attribute[RequestParam('price','double',false)]
	 */
	function AddProduct($title,$tagline,$body,$price)
	{
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
