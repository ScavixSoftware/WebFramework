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
 * This is a flashmovie wrapped by flowplayer.js
 * 
 * See http://flash.flowplayer.org/documentation/api/index.html
 * @attribute[Resource('flowplayer-3.1.4.min.js')]
 */
class FlashMovie extends Template
{
	/**
	 * @param string $id Player id
	 * @param string $src Source of the movie to play
	 * @param string $width HTML width value
	 * @param string $height HTML height value
	 * @param bool $autoplay True will automatically start playing
	 */
	function __initialize($id,$src,$width="230px",$height="",$autoplay=true)
	{
		parent::__initialize();
		$this->set("id",$id);
		$this->set("movie",$src);
		$this->set("width",$width);
		$this->set("height",$height);
		$this->set("autoplay",$autoplay);
	}
}
