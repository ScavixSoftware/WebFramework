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
 * Represents an (un-)ordered list in HTML (ul/ol)
 * 
 * Usefule if you want to bind AJAX to navigation which is often created as list.
 */
class HtmlList extends Control
{
	var $items = array();
	
	/**
	 * @param string $id Id to be set
	 * @param string $listType Type of list (ol|ul)
	 */
	function __initialize($id = "",$listType = "ul")
	{
		if($listType != "ul" || $listType != "ol")
			$listType = "ul";
		
		parent::__initialize($listType);
		
		if(!empty($id))
			$this->id = $id;
		
		store_object($this);
	}
	
	/**
	 * Adds an item to the list.
	 * 
	 * @param mixed $content Content to be added
	 * @param string $id Optional id for the created item
	 * @return HtmlListItem Created item or null if `is_empty($content)`
	 */
	function &AddItem($content,$id = "")
	{
		if(empty($content))
			return null;
		
		$item = new HtmlListItem($content,$id);
		$this->items[] = $item;
		$this->content($item);
		
		return $item;
	}
	
	
}
