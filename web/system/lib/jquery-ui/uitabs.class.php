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
use ScavixWDF\Base\Renderable;

/**
 * Wraps jQueryUI Tabs
 * 
 * See http://jqueryui.com/tabs/
 */
class uiTabs extends uiControl
{
	var $list;

	/**
	 * @param array $options See http://api.jqueryui.com/tabs/
	 */
    function __initialize($options=array())
	{
		parent::__initialize('div');
		$this->Options = force_array($options);
		$this->list = $this->content(new Control('ul'));
	}

	/**
	 * Adds a new tab to the tabcontrol.
	 * 
	 * @param string $label Tab-Button label
	 * @param string $content Something to add as content
	 * @return Control A div container for the tabs content
	 */
	function AddTab($label, $content="")
	{
		$container = $this->content(new Control('div'));
		$container->content($content);
		$this->list->content( new Control('li') )->content("<a href='#{$container->id}'>$label</a>");
		return $container;
	}

	/**
	 * Adds contents to a tab.
	 * 
	 * This is just a not so nice altervative to method chaining:
	 * <code php>
	 * $tabControl->AddTab('My first tab');
	 * $tabControl->AddToTab(0,'my content');
	 * // is equivalent to 
	 * $tabControl->AddTab('My first tab')->content('my content');
	 * </code>
	 * @param Renderable $tab_or_index The tab itself or it's zero based index
	 * @param mixed $content The conten to be added
	 * @return uiTabs `$this`
	 */
	function AddToTab($tab_or_index,$content)
	{
		if( $tab_or_index instanceof Renderable )
		{
			foreach( $this->_content as $c )
				if( $c == $tab_or_index )
				{
					$c->content($content);
					break;
				}
		}
		else
			$this->_content[$tab_or_index+1]->content($content);
		return $this;
	}

	/**
	 * Sets the tab to be selected initially.
	 * 
	 * @param Renderable $tab_or_index The tab itself or it's zero based index
	 * @return uiTabs `$this`
	 */
	function SetSelected($tab_or_index)
	{
		if( $tab_or_index instanceof Renderable )
		{
			foreach( $this->_content as $c )
				if( $c == $tab_or_index )
				{
					$this->Options['selected'] = $c->id;
					break;
				}
		}
		else
			$this->Options['selected'] = $this->_content[$tab_or_index+1]->id;
		return $this;
	}
}
