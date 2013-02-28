<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
 * Copyright (c) since 2013 Scavix Software Ltd. & Co. KG
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
 * @author PamConsult GmbH http://www.pamconsult.com <info@pamconsult.com>
 * @copyright 2007-2012 PamConsult GmbH
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
 
/**
 * Represents a select element.
 * 
 */
class Select extends Control
{
	var $_first_option_value = false;
	var $_options = array();
	var $_current = false;

	/**
	 * @param string $name The name
	 */
    function __initialize($name=false)
	{
		parent::__initialize("select");
        if( $name )
            $this->name = $name;
	}
	
	/**
	 * Sets the current value.
	 * 
	 * @param mixed $value The current value
	 * @return Select `$this`
	 */
	function SetCurrentValue($value)
	{
		$this->_current = $value;
		return $this;
	}

	/**
	 * Creates an option element.
	 * 
	 * @param mixed $value The value
	 * @param mixed $label An optional label
	 * @param bool $selected True if selected (hint: use <Select::SetCurrentValue> instead of evaluating selected state for each option)
	 * @param string $htmlextra This is so dirty that i'd say: deprecated
	 * @return Select `$this`
	 */
	function AddOption($value, $label = "", $selected = false, $htmlextra = "")
	{
		$label = $label==""?$value:$label;
		$this->_options[$value] = $label;
		if( !$this->_first_option_value )
			$this->_first_option_value = $value;

		if( !$selected && $this->_current )
			$selected = $value == $this->_current;
		$selected = $selected?" selected='selected'":"";
		$opt = "<option value='$value'$selected".($htmlextra != "" ? " ".$htmlextra : "").">".htmlspecialchars($label)."</option>\r\n";
		$this->content($opt);
		return $this;
	}

	/**
	 * Creates an optgroup element.
	 * 
	 * @param string $label The label text
	 * @param bool $disabled True if disabled
	 * @return Select `$this`
	 */
	function AddGroup($label = "", $disabled = false)
	{
		$opt = "<optgroup label=\"".str_replace("\"", "&quot;", $label)."\"".($disabled ? "disabled=\"disabled\"" : "")."></optgroup>\r\n";
		$this->content($opt);
		return $this;
	}
}

