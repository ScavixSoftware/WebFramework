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
namespace ScavixWDF\JQueryUI\Dialog;

use ScavixWDF\Controls\Table\Table;

default_string('TITLE_DIALOG', 'Dialog');

/**
 * Dialog that displayd data in two columns.
 * 
 * This is handy for option dialogs with label-input pairs.
 */
class uiTableLayoutDialog extends uiDialog
{
	private $_table;
	
	/**
	 * @param string $title Dialog title
	 * @param array $options Options as in <uiDialog>
	 */
	function __initialize($title="TITLE_DIALOG", $options=array())
	{
		parent::__initialize($title,$options);
		$this->_table = $this->content( new Table() );
	}
	
	/**
	 * Adds a line to the diaklog.
	 * 
	 * @param mixed $label Label for the first column
	 * @param mixed $control Control for the second column
	 * @return uiTableLayoutDialog `$this`
	 */
	function AddLine($label, $control=false)
	{
		if( $control )
			$this->_table->NewRow(array($label,$control));
		else
			$this->_table->NewRow()->NewCell($label)->colspan = "2";
		return $this;
	}
}
