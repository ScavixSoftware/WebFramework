<?
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
default_string('TXT_VERY_POOR', 'Poor');
default_string('TXT_NOT_THAT_BAD', 'Bad');
default_string('TXT_AVERAGE', 'Average');
default_string('TXT_GOOD', 'Good');
default_string('TXT_PERFECT', 'Perfect');
/**
 * Wraps a jQueryUI 'Star-Rating' control.
 * 
 * See http://plugins.jquery.com/project/Star_Rating_widget
 * @attribute[Resource('jquery-ui/ui.stars.js')]
 * @attribute[Resource('jquery-ui/ui.stars.css')]
 */
class uiStarSelect extends uiControl
{
	private $_value = 3;
	public $_content;

	var $_scale = array(
		1=>"TXT_VERY_POOR",
		2=>"TXT_NOT_THAT_BAD",
		3=>"TXT_AVERAGE",
		4=>"TXT_GOOD",
		5=>"TXT_PERFECT"
	);

	/**
	 * @param array $options See http://plugins.jquery.com/project/Star_Rating_widget
	 */
    function __initialize( $options=array() )
	{
		parent::__initialize("div");
		
		$this->Options = force_array($options);
		$this->Options['inputType'] = 'select';

		if( isset($this->Options['disabled']) )
			$this->Options['disabled'] = true;

		store_object($this);
	}

	/**
	 * @override
	 */
	public function PreRender($args = array())
	{
		if( isset($this->Options['captionEl']) )
		{
			$title = isset($this->Options['captionTitle'])?$this->Options['captionTitle'].": ":getString("TXT_RATING").": ";
			$labTitle = new Label($title);
			$labTitle->class = "userrating";

			$caption_element = "<span id='".$this->Options['captionEl']."'></span>";
			$caption = ", captionEl: $('#".$this->Options['captionEl']."')}";
			
			unset($this->Options['captionEl']);
			unset($this->Options['captionTitle']);

			$this->Options = system_to_json($this->Options);
			$this->Options = str_replace("}",$caption,$this->Options);

			$this->_content[] = $labTitle;
			$this->_content[] = $this->CreateSelect($this->id."_select");
			//$this->_content[] = "&nbsp;&nbsp;(".$caption_element.")";
		}
		else
		{
			$this->Options = system_to_json($this->Options);
			$this->_content[] = $this->CreateSelect($this->id."_select");
		}
		
		$script = "$('#{$this->id}').stars($this->Options);";
		$this->script($script);

		parent::PreRender($args);
	}

	private function CreateSelect($sel_name)
	{
		$Select = new Select($sel_name);
		foreach( $this->_scale as $val => $desc )
		{
			$selected = ($val==$this->_value)?true:false;
			$Select->AddOption( $val, $desc, $selected);
		}
		return $Select;
	}

	/**
	 * Sets the value.
	 * 
	 * @param mixed $value The new value
	 * @return void
	 */
	public function SetValue($value=false)
	{
		if( !$value )
			return;
		$this->_value = $value;
	}

	/**
	 * Sets the caption.
	 * 
	 * @param string $caption_title The label
	 * @return void
	 */
	public function SetCaption($caption_title=null)
	{
		$this->Options['captionEl'] = "stars-cap".$this->id;

		if( !is_null($caption_title) )
			$this->Options['captionTitle'] = $caption_title;
	}
}
