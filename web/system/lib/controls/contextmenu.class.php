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
namespace ScavixWDF\Controls;

use ScavixWDF\Base\Control;

/**
 * Wraps a context menu.
 * 
 * @attribute[Resource('jquery.contextmenu.r2.js')]
 */
class ContextMenu extends Control
{
	public $_content = array();
    private $_triggers = array();
    private $_bindings = array();
    private $_menuitems = array();
    private $_leftclick = false;
    private $_defaults = array(
        'menuStyle'=>"listStyle: 'none',padding: '1px', margin: '0px', backgroundColor: '#fff', border: '1px solid #999'",
        'itemStyle'=>"margin: '0px', color: '#000', display: 'block', cursor: 'default', padding: '3px', border: '1px solid #fff', backgroundColor: 'transparent'",
        'itemHoverStyle'=>"border: '1px solid #0a246a', backgroundColor: '#b6bdd2'"
    );
	
    function __initialize()
	{
		parent::__initialize("div");
	}

	/**
	 * @override Prepares JS init code
	 */
	public function WdfRender()
	{
        $script = "";
        $trigger = implode(",",$this->_triggers);

        if( count($this->_defaults) > 0 )
            $script .= "$.contextMenu.defaults({".$this->CreateDefaults()."});";

        if( count($this->_triggers) > 0 )
            $script .= "$('".$trigger."').contextMenu('{$this->id}',{";
        else
            $script .= "$(document).contextMenu('{$this->id}',{";

        if( count($this->_bindings) > 0 )
            $script .= $this->CreateBindings();

        $script .= "});";

        if( $trigger && $this->_leftclick )
		    $script .= "$('$trigger').click(function(e){ e.type = 'contextmenu'; $(this).trigger(e); });";
		
        $script .= ";";
        $this->script($script);
        $this->_content[] = $this->CreateUL();

        return parent::WdfRender();
	}

    /**
     * Add menuitem to contextmenu 
	 * 
	 * $img and title are optional to be able to choose which one is needed. 
	 * $id is needed to bind functions to menuitems via AddBinding
     * @param string $id Elements id
     * @param string $bindingfunction JS function to bind to click
     * @param string $title Item title
     * @param string $img Item icon#
	 * @return void
     */
    public function AddMenuItem($id,$bindingfunction="",$title="",$img="")
	{
		$this->_menuitems[] = "<li id='".$id."'>".$img." ".$title."</li>";
        $this->_bindings["$id"] = $bindingfunction;
	}

    /**
	 * Specifies a context menu trigger
	 * 
     * Define one or more jquery-selectors for elements which are uses
     * as triggers for the contextmenu if no trigger is defined the
     * document-selector will be used
     * @param string $triggerselector Valid jQuery selector
	 * @return void
     */
    public function AddTrigger($triggerselector)
    {
        $this->_triggers[] = $triggerselector;
    }

    /**
     * To change on of the following defaults
     *
     *  <b>menuStyle</b>
     *      An object containing styleName:value pairs for styling the containing <ul> menu.
     *  <b>itemStyle</b>
     *      An object containing styleName:value pairs for styling the <li> elements.
     *  <b>itemHoverStyle</b>
     *      An object containing styleName:value pairs for styling the hover behaviour of <li> elements.
     *  <b>shadow</b>
     *      Boolean: display a basic drop shadow on the menu.
     *      Defaults to true
     *  <b>eventPosX</b>
     *      Allows you to define which click event is used to determine where to place the menu. There are
     *      possibly times (particularly in IE6) where you will need to set this to "clientX".
     *      Defaults to: 'pageX'
     *  <b>eventPosY</b>
     *      Allows you to define which click event is used to determine where to place the menu. There are
     *      possibly times (particularly in IE6) where you will need to set this to "clientY".
     *      Defaults to: 'pageY'
     *  <b>onContextMenu(event)</b>
     *      A custom event function which runs before the context menu is displayed. If the function returns
     *      false the menu is not displayed. This allows you to attach the context menu to a large block
     *      element (or the entire document) and then filter on right click whether or not the context menu
     *      should be shown.
     *  <b>onShowMenu(event, menu)</b>
     *      A custom event function which runs before the menu is displayed. It is passed a reference to
     *      the menu element and allows you to manipulate the output before the menu is shown. This allows
     *      you to hide/show options or anything else you can think of before showing the context menu to
     *      the user. This function must return the menu.
     * @param string $defaultname Key to be set
     * @param string $value The new value
	 * @return void
     */
    public function AddDefault($defaultname,$value)
    {
        $this->_defaults["$defaultname"] = $value;
    }

    /**
     * If called the left-click is also bound.
	 * 
	 * It only works if the menu has one or more triggers
	 * @return void
     */
    public function BindLeftClick()
    {
        $this->_leftclick = true;
    }

    private function CreateUL()
    {
        $menucontent = "<ul>";

        foreach($this->_menuitems as $menuitem)
            $menucontent .= $menuitem;

        $menucontent .= "</ul>";

        return $menucontent;
    }

    private function CreateBindings()
    {
        $bindings = " bindings: {";
        foreach($this->_bindings as $bindingsid => $function)
            $bindings .= "'".$bindingsid."': function(t){ ".$function." },";

        $bindings = rtrim($bindings,",")."}";

        return $bindings;
    }

    private function CreateDefaults()
    {
        $defaults = "";
        
        foreach($this->_defaults as $defaultid => $value)
            $defaults .= "'".$defaultid."': { ".$value." },";

        $defaults = rtrim($defaults,",");

        return $defaults;
    }
}
