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
namespace ScavixWDF\Base;

use ScavixWDF\JQueryUI\Dialog\uiConfirmation;
use ScavixWDF\WdfException;

/**
 * Helper class to build common JavaScript codes for usage in AJAX aware controls.
 */
class AjaxAction
{
	private static function _data($data)
	{
		if( $data )
		{
			if( is_string($data) )
				return "$data";
			if( is_array($data) || is_object($data) )
				return json_encode($data);
			WdfException::Raise("Invalid argmuent: 'data' should be string, array or object, but '".gettype($data)."' detected");
		}
		return '';
	}
	
	/**
	 * Creates a valid URL to a controler('s method).
	 * 
	 * @param mixed $controller Controller object, Classname or _storage_id of the controller
	 * @param string $event Optional method to be called
	 * @return string A valid URL for use in JavaScript wdf object
	 */
	public static function Url($controller,$event='')
	{
		if( $controller instanceof HtmlPage )
			$controller = log_return("Using classname instead of id to reference controller:",get_class($controller));
		return ($controller instanceof Renderable)?"{$controller->_storage_id}/$event":"$controller/$event/";
	}
	
	/**
	 * Creates a wdf.post call.
	 * 
	 * @param mixed $controller Controller to call
	 * @param string $event Method to call
	 * @param string|array $data Data to be posted
	 * @param string $callback JS callback method
	 * @return string Valid JS code performing wdf.post
	 */
	public static function Post($controller,$event='',$data='',$callback='')
	{
		$q = self::Url($controller,$event);
		$data = self::_data($data);
		if( $data ) $data = ",$data";
		if( $callback ) $callback = ",$callback";
		return "wdf.post('$q'$data$callback);";
	}
	
	/**
	 * High level confirm procedure.
	 * 
	 * This will return a standard confirmation dialog that will perform the specified action
	 * when OK is clicked. Will also set a session variable so that the OK action PHP side code
	 * can simply test with <AjaxAction::IsConfirmed>($text_base) if the confirmation was really shown and accepted by the user.
	 * @param string $text_base Text constants basename (like CONFIRMATION). Confirmation will need TITLE_$text_base and TXT_$text_base
	 * @param mixed $controller Controller for OK action
	 * @param string $event Method for OK action
	 * @param string|array $data Data for OK action
	 * @return uiConfirmation Dialog ready to be shown to the user
	 */
	public static function Confirm($text_base,$controller,$event='',$data='')
	{
		$dlg = new uiConfirmation($text_base);
		$q = self::Url($controller,$event);
		$data = self::_data($data);
		$data = "var d = ".($data?$data:'{}')."; for(var n in $('#{$dlg->id}').data()) if( typeof $('#{$dlg->id}').data(n) == 'string') d[n] = $('#{$dlg->id}').data(n); ";
		$action = "$data".AjaxAction::Post($controller,$event,'d',$dlg->CloseButtonAction);
		$dlg->SetOkCallback($action);
		$_SESSION['ajax_confirm'][$text_base] = md5(time());
		$dlg->setData('confirmed', $_SESSION['ajax_confirm'][$text_base]);
		return $dlg;
	}
	
	/**
	 * Checks if the user has seen and accepted a confirmation.
	 * 
	 * See <AjaxAction::Confirm>
	 * @param string $text_base Text base the user confirmed
	 * @return boolean True if user clicked OK
	 */
	public static function IsConfirmed($text_base)
	{
		if( isset($_SESSION['ajax_confirm'][$text_base]) && $_SESSION['ajax_confirm'][$text_base] == Args::request('confirmed',false) )
			return true;
		return false;
	}
}
