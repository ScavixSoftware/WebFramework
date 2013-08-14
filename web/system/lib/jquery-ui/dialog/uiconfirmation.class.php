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

use ScavixWDF\WdfException;

default_string("TITLE_CONFIRMATION", "Confirm");
default_string("TXT_CONFIRMATION", "Please confirm");

/**
 * Displays a confirmation dialog.
 * 
 * This is basically a normal modal <uiDialog> with a predefined set of buttons.
 */
class uiConfirmation extends uiDialog
{
	const OK_CANCEL = 1;
	const YES_NO = 2;
	var $Mode;
	
	/**
	 * Creates a new uiConfirmation object.
	 * 
	 * The $text_base argument in fact defines two texts in one (and assumes you are using translations!):
	 * It will be prefixed with 'TXT_' and 'TITLE_' and that two constants will be used.
	 * Sample: 'CONFIRMATION' will become 'TXT_CONFIRMATION' and 'TITLE_CONFIRMATION'.
	 * @param string $text_base base of confirmation text.
	 * @param string $ok_callback JS code to be executed when the positive button is clicked (OK, YES)
	 * @param int $button_mode uiConfirmation::OK_CANCEL or uiConfirmation::YES_NO
	 */
	function __initialize($text_base='CONFIRMATION',$ok_callback=false,$button_mode=self::OK_CANCEL)
	{
		$options = array(
			'autoOpen'=>true,
			'modal'=>true,
			'width'=>450,
			'height'=>300
		);
		
		$title = "TITLE_$text_base";
		$text  = "TXT_$text_base";
		
		parent::__initialize($title,$options);
		switch( $button_mode )
		{
			case self::OK_CANCEL:
				$this->AddButton(tds('BTN_OK','Ok'),$ok_callback);
				$this->AddCloseButton(tds('BTN_CANCEL','Cancel'));
				break;
			case self::YES_NO:
				$this->AddButton(tds('BTN_YES','Yes'),$ok_callback);
				$this->AddCloseButton(tds('BTN_NO','No'));
				break;
			default:
				WdfException::Raise("Wrong button_mode: $button_mode");
		}
		$this->Mode = $button_mode;
		$this->content($text);
	}
	
	/**
	 * Set the callback for the positive button.
	 * 
	 * @param string $action JS code to be executed when the positive button is clicked (OK, YES)
	 * @return uiConfirmation `$this`
	 */
	function SetOkCallback($action)
	{
		switch( $this->Mode )
		{
			case self::OK_CANCEL:
				$this->SetButton(tds('BTN_OK','Ok'),$action);
				break;
			case self::YES_NO:
				$this->SetButton(tds('BTN_YES','Yes'),$action);
				break;
		}
		return $this;
	}
}
