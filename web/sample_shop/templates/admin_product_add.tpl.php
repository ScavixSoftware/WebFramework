<?
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
<form id="frm_add_product" method="post" action="<?=buildQuery('Admin','AddProduct')?>" enctype="multipart/form-data">
	<table>
		<tr>
			<td>Title</td>
			<td><input type="text" name="title" value=""/></td>
		</tr>
		<tr>
			<td>Tagline</td>
			<td><input type="text" name="tagline" value=""/></td>
		</tr>
		<tr>
			<td>Description</td>
			<td><textarea name="body"></textarea></td>
		</tr>
		<tr>
			<td>Image</td>
			<td><input type="file" name="image"/></td>
		</tr>
		<tr>
			<td>Price</td>
			<td><input type="text" name="price" value=""/></td>
		</tr>
	</table>
</form>