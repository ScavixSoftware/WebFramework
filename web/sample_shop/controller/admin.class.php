<?php

class Admin extends ShopBase
{
	private function _login()
	{
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
			if( $username=='admin' && $password=='admin')
			{
				$_SESSION['logged_in'] = true;
				redirect('Admin');
			}
			$this->content(uiMessage::Error("Unknown username/passsword"));
		}
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
		if( $title && $tagline && $body && $price )
		{
			if( isset($_FILES['image']) && $_FILES['image']['name'] )
			{
				$i = 1;
				$image = __DIR__.'/../images/'.$_FILES['image']['name'];
				while( file_exists($image) )
					$image = __DIR__.'/../images/'.($i++).'_'.$_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $image);
				$image = basename($image);
			}
			else $image='';
			$ds = model_datasource('system');
			$ds->ExecuteSql("INSERT INTO products(title,tagline,body,image,price)VALUES(?,?,?,?,?)",
				array($title,$tagline,$body,$image,$price));
			
			redirect('Admin');
		}
		$dlg = new uiDialog('Add product',array('width'=>600,'height'=>450));
		$dlg->content( Template::Make('admin_product_add') );
		$dlg->AddButton('Add product', "$('#frm_add_product').submit()");
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
		if( !AjaxAction::IsConfirmed('DELPRODUCT') )
			return AjaxAction::Confirm('DELPRODUCT', 'Admin', 'DelProduct', array('model'=>$model));
		$ds = model_datasource('system');
		$prod = $ds->Query('products')->eq('id',$model['id'])->current();
		$prod->Delete();
		
		if( $prod->image )
		{
			$image = __DIR__.'/../images/'.$prod->image;
			if( file_exists($image) )
				unlink($image);
		}
		return AjaxResponse::Redirect('Admin');
	}
}
