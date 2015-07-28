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
namespace ScavixWDF\JQueryUI;

use ScavixWDF\Base\Control;

/**
 * This is a custom select.
 * 
 * See http://www.filamentgroup.com/lab/jquery_ui_selectmenu_an_aria_accessible_plugin_for_styling_a_html_select/
 * this component is only very few tested
 * known issue: weird effects in uiDialog
 */
class uiSelectMenu extends uiControl
{
	var $_icons = array();

	/**
	 * @param array $options See http://www.filamentgroup.com/lab/jquery_ui_selectmenu_an_aria_accessible_plugin_for_styling_a_html_select/
	 */
    function __initialize($options = array())
	{
		parent::__initialize("select");
	}

	private function addIcon($path)
	{
		$key = "sm_icon_".count($this->_icons);
		$this->_icons[$key] = $path;
		return $key;
	}

	/**
	 * Adds an option to the selectmenu.
	 * 
	 * @param string $name The label
	 * @param mixed $value The value
	 * @param string $icon An image as icon
	 * @return void
	 */
	public function AddOption($name,$value,$icon=false)
	{
		$opt = new Control("option");
		$opt->value = $value;
		$opt->content($name);
		if( $icon )
			$opt->class = $this->addIcon($icon);
		$this->content($opt);
	}

	/**
	 * Defines the selected option.
	 * 
	 * @param mixed $value The selected value
	 * @return void
	 */
	public function SetSelected($value)
	{
		foreach( $this->_content as &$opt )
			if( $opt->value == $value )
			{
				$opt->selected = "selected";
				break;
			}
	}
}
