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
 * &lt;img&gt; element.
 * 
 */
class Image extends Control
{
	/**
	 * @param string $src value for the src attribute
	 * @param string $title value for the title attribute
	 * @param string $border value for the border attribute
	 * @param string $style value for the style attribute
	 * @param string $id optional value for the id attribute
	 */
	function __initialize($src=null, $title="", $border="0", $style="", $id=false)
	{
		parent::__initialize("img");
		if( $src != null )
		{
			$this->src = $src;
			if($title == "")
			{
				// show filename (without extension) as alt
				$this->alt = basename($src,'.'.pathinfo($src, PATHINFO_EXTENSION));
			}
			else
			{
				$this->alt = $title;
				$this->title = $title;
			}
			$this->border = $border;

			if( $id )
				$this->id = $id;
				
			if( $style != "" )
				$this->style = $style;
		}
	}
}
