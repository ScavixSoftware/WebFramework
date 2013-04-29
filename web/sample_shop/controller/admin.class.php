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
		
		$tab = $this->content(new uiDatabaseTable(model_datasource('system'),false,'products'));
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
		log_debug("AddProduct($title,$tagline,$body,$price)",$_FILES);
		if( $title && $tagline && $body && $price )
		{
			if( isset($_FILES['image']) )
			{
				$image = tempnam(__DIR__.'/../images/', 'upload_');
				move_uploaded_file($_FILES['image']['tempnam'], $image);
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
}
