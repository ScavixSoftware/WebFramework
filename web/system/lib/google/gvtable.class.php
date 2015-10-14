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
namespace ScavixWDF\Google;

use ScavixWDF\Base\Control;

/**
 * A data table
 * 
 * See https://google-developers.appspot.com/chart/interactive/docs/gallery/table
 */
class gvTable extends GoogleVisualization
{
	/**
	 * @override
	 */
	function __initialize($options=array(),$query=false,$ds=false)
	{
		parent::__initialize('Table',$options,$query,$ds);
		$this->_loadPackage('table');
	}
		
	/**
	 * Sets an option.
	 * 
	 * Overrides parent to capture 'title' option as tables does not handle that.
	 * We will instead create a &ltdiv class='caption'/&gt; element with the title once the table has been rendered.
	 * @override
	 */
	function opt($name,$value=null)
	{
		if( $name == 'title' && $value )
		{
			$script = "$('#{self}').prepend( $('<div/>').addClass('caption').html(".json_encode($value).") );";
			$script = "if( $('#{self}').data('ready') ){ $script }else setTimeout(readyCb{self},100);";
			$script = "var readyCb{self} = function(){ $script }; readyCb{self}();";
			$this->script($script);
		}
		return parent::opt($name, $value);
	}
}