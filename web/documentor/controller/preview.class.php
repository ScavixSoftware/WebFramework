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

use ScavixWDF\Base\HtmlPage;

/**
 * @attribute[Resource('marked.js')]
 */
class Preview extends HtmlPage
{
	function Init()
	{
		$this->Linked(false);
	}
	
	/**
	 * @attribute[RequestParam('f','string')]
	 */
	function Linked($f)
	{
		if( $f )
			$md = file_get_contents(__DIR__."/out/$f.md");
		else 
			$md = "# Scavix WDF Home
- [Alphabetical function listing](functions)
- [Alphabetical class listing](classes)
- [Inheritance tree](inheritance)
- [Interfaces](interfaces)
- [Folder tree](foldertree)
- [Namespace tree](namespacetree)";
		
		$q = buildQuery('Preview','Linked');
		$s  = "$('.markdown-body').html(marked(".json_encode($md)."));";
		$s .= "$('.markdown-body a[id]').each(function(){ $(this).attr('id','wiki-'+$(this).attr('id')); });";
		$s .= "$('.markdown-body a[href]').each(function(){ if( $(this).attr('href').match(/^http/)) return; $(this).attr('href','$q?f='+$(this).attr('href')); });";
		$s .= "$('.markdown-body a[id='+location.hash.substr(1)+']').get(0).scrollIntoView();";
		
		$this->addDocReady("$s");
	}
}
