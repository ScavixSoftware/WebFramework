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
namespace ScavixWDF\Controls\Form;

use ScavixWDF\Base\Control;

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
	 * Sets the name attribute.
	 * 
	 * @param string $name The type
	 * @return Select `$this`
	 */
	function setName($name)
	{
		if( $name )
			$this->name = $name;
		return $this;
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
        if(count($this->_content) > 0)
            $this->_setval($this->_content, $value);
		return $this;
	}
    
    /**
     * Set the option(s) as selected after the options have been added
     */
    private function _setval($children, $val)
    {
        foreach($children as $opt)
        {
            if(is_object($opt))
            {
                if(isset($opt->Tag) && ($opt->Tag == 'optgroup'))
                    $this->_setval($opt->_content, $val);
                if((is_array($val) && in_array($opt->value, $val)) || ($opt->value == $val) && ($opt->Tag != 'optgroup'))
                    $opt->attr('selected', 'selected');
                elseif(isset($opt->_attributes['selected']))
                    unset($opt->_attributes['selected']);
            }
        }
    }

	/**
	 * Creates an option element.
	 * 
	 * @param mixed $value The value
	 * @param mixed $label An optional label
	 * @param bool $selected True if selected (hint: use <Select::SetCurrentValue> instead of evaluating selected state for each option)
	 * @param Control $opt_group If given the option will be added to this optgroup element. Create one via <Select::CreateGroup>.
	 * @return Select `$this`
	 */
	function AddOption($value, $label = "", $selected = false, $opt_group=false)
	{
		$this->CreateOption($value,$label,$selected,$opt_group);
		return $this;
	}
	
	function CreateOption($value, $label="", $selected = false, $opt_group=false)
	{
		$label = $label==""?$value:$label;
		$this->_options[$value] = $label;
		if( !$this->_first_option_value )
			$this->_first_option_value = $value;

		if( !$selected && $this->_current !== false )
			$selected = $value == $this->_current;
		
		$opt = Control::Make('option')->append($label!==""?$label:$value);
		if( $selected )
			$opt->attr("selected","selected");
//		if( $value!=='' )
			$opt->attr("value",$value);
		
		if( $opt_group )
			return $opt_group->content($opt);
		return $this->content($opt);
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
//		$opt = "<optgroup label=\"".str_replace("\"", "&quot;", $label)."\"".($disabled ? "disabled=\"disabled\"" : "")."></optgroup>\r\n";
		$this->CreateGroup($label, $disabled);
		return $this;
	}
	
	/**
	 * Creates an optgroup element and returns it.
	 * 
	 * Same as <Select::AddGroup>, but return the OptGroup <Control> instead to `$this`.
	 * @param string $label The label text
	 * @param bool $disabled True if disabled
	 * @return Control OptGroup element
	 */
	function CreateGroup($label = "", $disabled = false)
	{
		$opt = new Control('optgroup');
		$opt->label = $label;
		if( $disabled )
			$opt->disabled = 'disabled';
		return $this->content($opt);
	}
	
	/**
	 * Creates a label element for this select.
	 * 
	 * Note that this only ensures that the label is correctly assigned to this select.
	 * It will not add it somewhere!
	 * @param string $text Text for the label
	 * @return Label The created label element
	 */
	function CreateLabel($text)
	{
		return new Label($text,$this->id);
	}
    
    static function Create($options,$name=false,$selected=false)
    {
        $res = new Select($name);
        if( $selected ) $res->SetCurrentValue($selected);
        foreach( $options as $k=>$v )
            $res->AddOption($k,$v);
        return $res;
    }
}
