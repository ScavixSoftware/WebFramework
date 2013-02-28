<?
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
default_string("TXT_UNKNOWN", 'Unknown');
	
/**
 * This is a container for UI elements.
 * 
 * May be deprecated, we used it in the past for widget based UI designs. 
 * It's kind of a dialog, but not exactly and contain clickable icons in the header.
 * @attribute[Resource('jquery-ui/ui.container.js')]
 * @attribute[Resource('jquery-ui/ui.container.css')]
 */
class uiContainer extends uiControl
{
	/**
	 * @param string $title Title for header section
	 * @param options $options
	 */
	function __initialize($title="TXT_UNKNOWN",$options=array())
	{
		parent::__initialize("div");
		$this->Options = $options;
		$this->title = $title;
	}
	
	/**
	 * @override
	 */
	function PreRender($args = array())
	{
		$this->script("$('#{self}').container(".system_to_json($this->Options).");");
		parent::PreRender($args);
	}

	/**
	 * Adds a button to the header section.
	 * 
	 * @param string $icon A valid <uiControl::Icon>
	 * @param string $function JS code to be executed on click
	 * @return uiContainer `$this`
	 */
	function AddButton($icon,$function)
	{
		if( isset($this->Options['buttons']) )
			$buttons = $this->Options['buttons'];
		else
			$buttons = array();

		$icon = self::Icon($icon);
		if( is_array($function))
			$buttons[$icon] = $function;
		else
			$buttons[$icon] = "[jscode]".$function;

		$this->Options['buttons'] = $buttons;
		return $this;
	}
}
