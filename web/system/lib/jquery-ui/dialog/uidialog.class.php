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
namespace ScavixWDF\JQueryUI\Dialog;

use ScavixWDF\JQueryUI\uiControl;

default_string('TITLE_DIALOG', 'Dialog');

/**
 * Wraps a jQueryUI Dialog
 * 
 * See http://jqueryui.com/dialog/
 */
class uiDialog extends uiControl
{
	protected $Buttons = array();
	protected $CloseButton = null;
	var $CloseButtonAction = null;

	/**
	 * @param string $title The dialogs title
	 * @param array $options See http://api.jqueryui.com/dialog/
	 */
	function __initialize($title="TITLE_DIALOG", $options=array())
	{
		parent::__initialize("div");
		$this->title = $title;
		$tit_script = $this->title?"":"$(this).parent().find('.ui-dialog-titlebar').hide();";

		$this->Options = array_merge(array(
				'autoOpen'=>true,
				'modal'=>true,
				'resizable'=>false,
				'draggable'=>false,
				'width'=>350,
				'height'=>150,
				'open'=>"function(){ $(this).parent().find('.ui-button').button('enable');$tit_script }",
			),$options);
		
		$rem = system_is_ajax_call()?".remove()":'';
		$this->CloseButtonAction = "function(){ $('#{$this->id}').dialog('close')$rem; }";
		
		$this->InitFunctionName = false;
	}

	/**
	 * @override
	 */
	function PreRender($args=array())
	{
		if( count($args) > 0 )
		{
			$controller = &$args[0];
			// just to render close button with the right id
			if( !is_null($this->CloseButton) )
			{
				$temp = array( $this->CloseButton => $this->CloseButtonAction );
				$this->Buttons = array_merge($this->Buttons, $temp);
			}
			
			$rem = system_is_ajax_call()?".remove()":'';
			$close_action = "$('#{$this->id}').dialog('close')$rem;";
			
			foreach( $this->Buttons as $label=>$action )
			{
				if( !starts_with($action, '[jscode]') && !starts_with($action, 'function') )
					$action = "function(){ $action }";
				$this->Buttons[$label] = str_replace("{close_action}", $close_action, $action);
			}
						
			$this->Options['buttons'] = $this->Buttons;
			$tmp = $this->_script;
			$this->_script = array();
			$this->script("try{ $('#{$this->id}').dialog(".system_to_json($this->Options)."); }catch(ex){ wdf.debug(ex); }");
			$this->script("$('#{$this->id}').parent().find('.ui-dialog-buttonpane .ui-button').click(function(){ $(this).parent().find('.ui-button').button().prop('disabled', true).addClass( 'ui-state-disabled' ); });");
			$this->_script = array_merge($this->_script,$tmp);

			foreach( $this->_script as $s )
			{
				$controller->addDocReady($s);
			}
		}
		return parent::PreRender($args);
	}
	
	/**
	 * Adds a button to the dialog.
	 * 
	 * @param string $label Button text
	 * @param string $action JS code for button click event
	 * @return uiDialog `$this`
	 */
	function AddButton($label,$action)
	{
		if( !starts_with($action, '[jscode]') && !starts_with($action, 'function') )
			$action = "function(){ $action }";
		$this->Buttons[$label] = $action;
		return $this;
	}
	
	/**
	 * @shortcut <uiDialog::AddButton>
	 */
	function SetButton($label,$action)
	{
		return $this->AddButton($label, $action);
	}

	/**
	 * Adds a close button.
	 * 
	 * @param string $label Close button text
	 * @param string $action Action to be performed on click, defaults to the standard close action
	 * @return uiDialog `$this`
	 */
	function AddCloseButton($label, $action = false)
	{
		$this->CloseButton = $label;
		if($action !== false)
			$this->CloseButtonAction = $action;
		return $this;
	}
}
