<form method="post" action="<?=buildQuery('basket','StartCheckout')?>" enctype="multipart/form-data">
	<h2>Address:</h2>
	<table>
		<tr>
			<td>Firstname</td>
			<td><input type="text" name="fname" value=""/></td>
		</tr>
		<tr>
			<td>Lastname</td>
			<td><input type="text" name="lname" value=""/></td>
		</tr>
		<tr>
			<td>Street and number</td>
			<td><input type="text" name="street" value=""/></td>
		</tr>
		<tr>
			<td>ZIP and City</td>
			<td>
				<input type="text" name="zip" value=""/>
				<input type="text" name="city" value=""/>
			</td>
		</tr>
		<tr>
			<td>E-Mail address</td>
			<td><input type="text" name="email" value=""/></td>
		</tr>
	</table>
	<h2>Payment provider:</h2>
	<input id="rbPaypal" type="radio" name="provider" value="paypal"/><label for="rbPaypal">PayPal</label><br/>
	<input id="rbGate2Shop" type="radio" name="provider" value="gate2shop"/><label for="rbGate2Shop">Gate2Shop</label><br/>
	<input id="rbTesting" checked="checked" type="radio" name="provider" value="testingpaymentprovider"/><label for="rbTesting">Testing</label><br/>
	<input type="submit" value="Buy now"/>
</form>