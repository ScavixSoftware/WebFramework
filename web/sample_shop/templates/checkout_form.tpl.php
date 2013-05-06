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