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
?>
<div style="text-align: center; font-size: 18px; font-weight: bold">
	<a href="javascript: history.back();">Back</a>
	&nbsp;&nbsp;&nbsp;
	<a href="<?=buildQuery('DocMain')?>">Close preview</a>
</div>
<div style="text-align: center; font-size: 18px; font-weight: bold">
	<a href="<?=buildQuery('Preview','Linked','f=functions')?>">Functions</a>
	&nbsp;&nbsp;
	<a href="<?=buildQuery('Preview','Linked','f=classes')?>">Classes</a>
	&nbsp;&nbsp;
	<a href="<?=buildQuery('Preview','Linked','f=inheritance')?>">Inheritance</a>
	&nbsp;&nbsp;
	<a href="<?=buildQuery('Preview','Linked','f=interfaces')?>">Interfaces</a>
	&nbsp;&nbsp;
	<a href="<?=buildQuery('Preview','Linked','f=foldertree')?>">Folder tree</a>
	&nbsp;&nbsp;
	<a href="<?=buildQuery('Preview','Linked','f=namespacetree')?>">Namespace tree</a>
</div>
<div class="markdown-body">
<?php foreach($content as $c)echo$c;?>
</div>