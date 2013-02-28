<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
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
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
 
/**
 * Wraps a simplyscroll object
 * 
 * See:
 * http://logicbox.net/jquery/simplyscroll
 * http://logicbox.net/blog/simplyscroll-jquery-plugin
 * http://plugins.jquery.com/project/simplyScroll
 */
class SimplyScroll extends Control
{
	var $Options = array();

	/**
	 * @param type $options SimplyScroll options (see http://logicbox.net/jquery/simplyscroll)
	 */
	function __initialize($options=array())
	{
		parent::__initialize("ul");
		if( !isset($options['autoMode']) ) $options['autoMode'] = 'loop';

		$this->Options = $options;
		$options = system_to_json($this->Options);
		$code = "$('#{self}').simplyScroll($options);";
		$this->script($code);
	}

	/**
	 * Adds an image to the scroller.
	 * 
	 * @param string $src Image src
	 * @param string $clicktarget Controller (or id) to redirect to when image is clicked
	 * @param string $title Image alt text
	 * @return SimplyScroll `$this`
	 */
	function AddImage($src,$clicktarget=false,$title=false)
	{
		$img = new Image($src);
		if( $title )
			$img->alt = $title;
		if( $clicktarget )
		{
			$img->onclick = "wdf.redirect('$clicktarget');";
			$img->css("cursor", 'pointer');
		}
		$this->content("<li>".$img->WdfRender()."</li>");
		return $this;
	}
}
