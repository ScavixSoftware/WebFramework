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
namespace ScavixWDF\JQueryUI;

use ScavixWDF\Base\Control;

/**
 * A TreeView control.
 * 
 * Nodes are represented by <uiTreeNode> objects
 * @attribute[Resource('jquery-ui/ui.treeview.js')]
 * @attribute[Resource('jquery-ui/ui.treeview.js')]
 */
class uiTreeView extends uiControl
{
	var $Url = false;
	var $NodeSelected = false;

	/**
	 * @param string $url Optional URL to load data from
	 * @param string $nodeSelected JS callback function to call when a node has been selected
	 */
    function __initialize($url=false,$nodeSelected=false)
	{
		parent::__initialize("");
		$this->Url = $url;
		$this->NodeSelected = $nodeSelected;
	}

	/**
	 * @override
	 */
	function  PreRender($args=array())
	{
		if( $this->Url || $this->NodeSelected )
		{
			$options = new StdClass();
			if( $this->Url )
				$options->url = $this->Url;
			if( $this->NodeSelected )
				$options->nodeSelected = $this->NodeSelected;
			$this->script("$('#".$this->id."').treeview(".system_to_json($options).");");
		}
		else
			$this->script("$('#".$this->id."').treeview({});");
		return parent::PreRender($args);
	}

	/**
	 * Adds a root node to the tree
	 * 
	 * Returns the node object so you may chain adding subnodes:
	 * <code php>
	 * $sub = uiTreeView::Make()->AddRootNode("Root1")->AddNode("Subnode 1");
	 * $sub->AddNode("Subnode 2);
	 * </code>
	 * @param type $text Label of the node
	 * @param type $class Optional css class
	 * @return uiTreeNode The new node
	 */
	function &AddRootNode($text,$class="folder")
	{
		$this->Tag = "ul";
		$res = new uiTreeNode($text);
		$this->content($res);
		return $res;
	}
}

/**
 * Represents a tree node in a <uiTreeView>.
 * 
 */
class uiTreeNode extends Control
{
	var $tree = false;
	var $text = false;
	var $hasChildren = false;
	var $expanded = "closed";
	var $children = false;

	/**
	 * No need to call this manually, use <uiTreeView::AddRootNode>() instead.
	 * @param string $text Node label text
	 */
	function __initialize($text)
	{
		parent::__initialize("li");
		$this->class = "ui-treeview-node";
		$this->content($text);
	}

	/**
	 * Adds a subnode.
	 * 
	 * This creates a new <uiTreeNode> object and returns it for method chaining:
	 * <code php>
	 * $sub = uiTreeView::Make()->AddRootNode("Root1")->AddNode("Subnode 1");
	 * $sub->AddNode("Subnode 2);
	 * </code>
	 * @param string $text Subnode label text
	 * @return uiTreeNode The created node
	 */
	function &AddNode($text)
	{
		if( !$this->tree )
		{
			$this->tree = new Control("ul");
			$this->content( $this->tree );
		}

		$res = new uiTreeNode($text);
		$this->tree->content($res);
		return $res;
	}
}
