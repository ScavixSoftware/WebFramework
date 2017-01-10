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

/**
 * Wraps a jQueryUI Autocomplete
 * 
 * See http://jqueryui.com/autocomplete/
 * 
 * @attribute[Resource('jquery-ui/ui.autocomplete.ex.js')] 
 */
class uiAutocomplete extends uiControl
{
	protected $hidden;
	protected $ui;

	/**
	 * @param array $options See http://api.jqueryui.com/autocomplete/
	 */
	function __initialize($options=array())
	{		
		parent::__initialize("input");
		$this->type = "text";
		$this->Options = $options;
	}
	
	/**
	 * Sets an on change handler.
	 * 
	 * @param string $function JS Handler function
	 * @return uiAutocomplete `$this`
	 */
	function setOnChange($function)
	{
        if( !starts_iwith($function,'function'))
            $function = "function(event,ui){ $function }";
		//$this->hidden->onchange = $function;
        $this->opt('change',$function);
		return $this;
	}
    
    public function __sleep() 
    {
        return array('_storage_id');
    }
}
