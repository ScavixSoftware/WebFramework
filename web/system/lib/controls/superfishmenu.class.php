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
 * Wraps a superfish menu control
 * 
 * See http://users.tpg.com.au/j_birch/plugins/superfish/
 */
class SuperfishMenu extends Control
{
    function __initialize()
	{
		parent::__initialize("ul");
		$this->class = "sf-menu sf-navbar";
		$this->script('$("#'.$this->id.'").superfish();');
	}
	
	/**
	 * Adds an item
	 * 
	 * New item will be top-level navigation item
	 * @param string $label Item label
	 * @param mixed $controller Controller object or id
	 * @param string $event Handler method
	 * @param mixed $data Array or urlencoded string containing additional data to be passed
	 * @return SuperfishMenuItem The created item
	 */
	function AddItem($label,$controller,$event="",$data=array())
	{
		$item = new SuperfishMenuItem($label,$controller,$event,$data);
		$this->content($item);
		return $item;
	}
	
	/**
	 * Gets all top-level items.
	 * 
	 * @return array List of <SuperfishMenuItem> objects
	 */
	function GetItems()
	{
		return $this->_content;
	}
	
	/**
	 * Adds a new item to an external URL
	 * 
	 * Use this if you want something outide your app to be referenced.
	 * @param string $label Item label
	 * @param string $href URL to be redirected to
	 * @return SuperfishMenuItem The created item
	 */
	function AddExternalItem($label,$href)
	{
		return $this->AddItem($label,$href,'$is_link$');
	}
	
	/**
	 * @override
	 */
	function WdfRender()
	{
		if( count($this->_content) > 0 )
			$this->content('<li class="closer"></li>');
		return parent::WdfRender();
	}
	
	/**
	 * Tries to figure out the item that must have the focus
	 * 
	 * Will then set it active and return
	 * @return void
	 */
	function DetectSelected()
	{
		$controller = strtolower(current_controller());
		$event = current_event(true);
		$data = md5(render_var($_GET));
		
		for( $level=1; $level<=6; $level++ )
		{
			foreach( $this->GetItems() as $item )
			{
				if( $level < 4 )
					foreach( $item->GetItems() as $sub )
					{
						if( $sub->controller == $controller && 
							($level > 2 || $sub->event == $event) && 
							($level > 1 || $sub->data == $data) )
						{
							$sub->SetSelected();
							$item->SetSelected();
							return;
						}
					}
				else
					if( $item->controller == $controller && 
						($level > 5 || $item->event == $event) && 
						($level > 4 || $item->data == $data) )
					{
						$item->SetSelected();
						return;
					}
			}
		}
	}
}

/**
 * Represents a menu item in a <SuperfishMenu>.
 * 
 * May also be a subitem to another <SuperfishMenuItem>
 */
class SuperfishMenuItem extends Control
{
	var $is_parent = true;
	var $sub = false;
	var $controller = false;
	var $event = false;
	var $data = false;
	
	/**
	 * @param string $label Item label
	 * @param mixed $controller Controller object or id
	 * @param string $event Handler method
	 * @param mixed $data Array or urlencoded string containing additional data to be passed
	 */
	function __initialize($label,$controller,$event="",$data=array())
	{
		parent::__initialize("li");
		$this->class = "";
		if( $event == '$is_link$' )
			$this->content(new Anchor($controller, $label));
		else
			$this->content(new Anchor(buildQuery($controller,$event,$data), $label));
		
		$this->controller = strtolower($controller);
		if( $event ) $this->event = strtolower($event);
		$this->data = md5(render_var($data));
	}
	
	/**
	 * Adds a subitem.
	 * 
	 * @param string $label Item label
	 * @param mixed $controller Controller object or id
	 * @param string $event Handler method
	 * @param mixed $data Array or urlencoded string containing additional data to be passed
	 * @return SuperfishMenuItem The created item
	 */
	function AddItem($label,$controller,$event="",$data=array())
	{
		if( !$this->sub )
		{
			$this->sub = $this->content(new Control('ul'));
			$this->sub->class = "subnavi";
		}
		$item = new SuperfishMenuItem($label,$controller,$event,$data);
		$item->is_parent = false;
		$this->sub->content($item);
		return $item;
	}
	
	/**
	 * Adds a subitem to an external URL
	 * 
	 * Use this if you want something outide your app to be referenced.
	 * @param string $label Item label
	 * @param string $href URL to be redirected to
	 * @return SuperfishMenuItem The created item
	 */
	function AddExternalItem($label,$href)
	{
		return $this->AddItem($label,$href,'$is_link$');
	}
	
	/**
	 * Gets all subitems.
	 * 
	 * @return array List of <SuperfishMenuItem> objects
	 */
	function GetItems()
	{
		if( $this->sub )
			return $this->sub->_content;
		return array();
	}
	
	/**
	 * Sets this item selected
	 * 
	 * This means that it will be shown as the current one.
	 * Note that there may be two selected items: one top-level, and one of it's subitems
	 * @return void
	 */
	function SetSelected()
	{
		if( $this->is_parent )
			$this->addClass('current');
		else
			$this->addClass('current_page_item');
	}
	
	/**
	 * @override
	 */
	function WdfRender()
	{
		if( $this->sub )
			$this->sub->content('<li class="page_item"><span class="subclsr">&nbsp;</span></li>');
		return parent::WdfRender();
	}
}
