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
namespace ScavixWDF\Controls\Form;

/**
 * Represents a checkbox.
 * 
 */
class CheckBox extends Input
{
	var $Label = false;
	
	/**
	 * @param string $name The name
	 */
    function __initialize($name=false)
	{
		parent::__initialize();
		$this->setType("checkbox")->setName($name)->setValue(1);
	}
	
	/**
	 * Creates a label element for this checkbox.
	 * 
	 * Note that this only ensures that the label is correctly assigne to this checkbox.
	 * It will not add it somewhere!
	 * @param string $text Text for the label
	 * @return Label The created label element
	 */
	function CreateLabel($text)
	{
		$this->Label = new Label($text,$this->id);
		return $this->Label;
	}
}
