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
 * This is an &lt;input type=button/&gt;.
 * 
 */
class Button extends Input
{
	/**
	 * Creates a Button.
	 * 
	 * Note that you can safely ignore all but the $label argument if your new button
	 * shall not redirect elsewhere on click.
	 * @param string $label Label text
	 * @param string $controller Controller for click redirect
	 * @param string $event Event for click redirect
	 * @param mixed $data Data for click redirect
	 */
	function __initialize( $label, $controller="", $event="", $data="")
	{
		parent::__initialize();
		$this->setType("button")->setValue($label);

		if( $controller != "" && strpos($controller,"$") === false && strpos($controller,"?") === false )
			$query = "wdf.redirect('".buildQuery($controller,$event,$data)."')";
		else
			$query = $controller;

		if( $query != "" )
			$this->onclick = $query;
	}
}
