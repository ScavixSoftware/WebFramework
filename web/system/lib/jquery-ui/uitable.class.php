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

use ScavixWDF\Controls\Table\Table;

/**
 * Wrapper class to ensure jQueryUI is loaded.
 * 
 * @attribute[Resource('jquery-ui/jquery-ui.js')] 
 * @attribute[Resource('jquery-ui/jquery-ui.css')] 
 */
class uiTable extends Table
{	
	/**
	 * @override
	 */
	function WdfRender()
	{
		$this->_ensureCaptionObject();
		$this->addClass('ui-widget ui-widget-content ui-corner-all');
		if( $this->header ) $this->header->addClass('ui-widget-header');
		if( $this->Caption ) $this->Caption->addClass('ui-widget-header');
		if( $this->footer ) $this->footer->addClass('ui-widget-content');
		return parent::WdfRender();
	}
}
