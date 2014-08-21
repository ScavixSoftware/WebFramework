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

use ScavixWDF\Controls\Form\Label;
use ScavixWDF\Controls\Form\RadioButton;

/**
 * Wraps a button set.
 * 
 * See http://jqueryui.com/button/#radio
 */
class uiButtonSet extends uiControl
{
	var $buttons = array();

	function __initialize()
	{
		parent::__initialize("div");
	}

	/**
	 * @override
	 */
	function PreRender($args = array())
	{
		$btncnt = count($this->buttons);
		if($btncnt != 0)
		{
			for($index = 0; $index < $btncnt;$index++)
			{
				$label = $this->buttons[$index]['label'];
				$events = $this->buttons[$index]['events'];
				$this->content($this->buttons[$index]['button']);
//				$this->content("<label for='".$this->buttons[$index]['button']->id."'>".$label."</label>");
				$this->content($this->buttons[$index]['label']);
				if(isset($this->buttons[$index]['icon']))
					$this->script("$('#{$this->buttons[$index]['button']->id}').button({icons:{primary:'ui-icon-".$this->buttons[$index]['icon']."'}});");
				foreach($events as $event=>$function)
				{
					$this->script("$('#{$this->buttons[$index]['button']->id}').$event(function(){ $function }); ");
				}
			}
		}
		parent::PreRender($args);
	}

	/**
	 * Adds a button to the set.
	 * 
	 * @param string $label Button text
	 * @param string $click_event Click action (JS)
	 * @param string $icon A valid <uiControl::Icon>
	 * @return RadioButton The created button
	 */
	function &AddButton($label,$click_event = false,$icon = false)
	{
		$ctn = count($this->buttons);
		$btn = new RadioButton(false,"radio");
		$this->buttons[$ctn]["label"]  = new Label($label,$btn->id);
		$this->buttons[$ctn]['button'] = $btn;
		$this->buttons[$ctn]['events']['click'] = $click_event;
		
		if( $icon )
			$this->buttons[$ctn]["icon"] = self::Icon($icon);
			
		return $btn;
	}

	/**
	 * Assigns an event to a button.
	 * 
	 * @param string $button_id the target button id
	 * @param string $event a jQuery event e.g click
	 * @param string $function JS code
	 * @return uiButtonSet `$this`
	 */
	function AddEventToButton($button_id,$event,$function)
	{
		$btncnt = count($this->buttons);
		for($i = 0; $i < $btncnt; $i++)
		{
			if($this->buttons[$i]['button']->id == $button_id)
			{
				$this->buttons[$i]['events'][$event] = $function;
				break;
			}
		}
		return $this;
	}

	/**
	 * Change a buttons label.
	 * 
	 * @param string $button_id the target button id
	 * @param string $label the new label
	 * @return uiButtonSet `$this`
	 */
	function ChangeLabel($button_id,$label)
	{
		$btncnt = count($this->buttons);
		for($i = 0; $i < $btncnt;$i++)
		{
			if($this->buttons[$i]['button']->id == $button_id)
				$this->buttons[$i]['label'] = $label;
		}
		return $this;
	}
}
