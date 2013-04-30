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