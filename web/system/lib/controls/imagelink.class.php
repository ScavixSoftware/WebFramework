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
 * &lt;a&gt; element containing an &lt;img&gt;
 */
class ImageLink extends Control
{
	/**
	 * @param string $src value for the src attribute
	 * @param string $title value for the title attribute
	 * @param string $link value for the href attribute
	 * @param string $margin Value for the margin (css)
	 */
	function __initialize($src, $title, $link="", $margin="")
	{
		parent::__initialize('a');
		$this->href = $link;
		$this->css("text-decoration","none");
		if( $margin != "" )
			$this->css("margin","$margin");

		$this->content(new Image($src,$title,0));
	}

	/**
	 * Gets the inner image.
	 * 
	 * @return Image The inner image control
	 */
	function GetImage()
	{
		return $this->_content[0];
	}
}
