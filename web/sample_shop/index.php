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

require_once(__DIR__."/../system/system.php");

switchToDev();
system_init('shop');

if( isset($_GET['clear']) )
{
    cache_clear();
	$_SESSION = array();	
}

/**
 * Creates a all tables and some sample data.
 */
function ensure_db()
{
	$flds = array();
	foreach( array('email','fname','lname','street','zip','city') as $fld )
		$flds[] = "$fld VARCHAR(255)";
	$flds = implode(",", $flds);
	
	$ds = model_datasource('system');
	$ds->ExecuteSql("CREATE TABLE IF NOT EXISTS products(id INTEGER,title VARCHAR(50),tagline VARCHAR(100),body TEXT,image VARCHAR(50),price DOUBLE,PRIMARY KEY(id))");
	$ds->ExecuteSql("CREATE TABLE IF NOT EXISTS orders(id INTEGER,customer_id INTEGER,status INTEGER,created DATETIME,updated DATETIME,completed DATETIME,deleted DATETIME,price_total DOUBLE,PRIMARY KEY(id))");
	$ds->ExecuteSql("CREATE TABLE IF NOT EXISTS items(id INTEGER,order_id INTEGER,title VARCHAR(50),tagline VARCHAR(100),body TEXT,price DOUBLE,amount DOUBLE,PRIMARY KEY(id))");
	$ds->ExecuteSql("CREATE TABLE IF NOT EXISTS customers(id INTEGER,$flds,PRIMARY KEY(id))");
	
	if( $ds->ExecuteScalar("SELECT count(*) FROM products") == 0 )
	{
		$ds->ExecuteSql("INSERT INTO products(title,tagline,body,image,price)VALUES(?,?,?,?,?)",
			array('Product 1','This is short desc for product 1','Here we go with an real description that can be long and will only be displayed on products details page','product1.png',11.99));
		$ds->ExecuteSql("INSERT INTO products(title,tagline,body,image,price)VALUES(?,?,?,?,?)",
			array('Product 2','Product 2 has a tagline too','But we will go with a short description','product2.png',9.85));
		$ds->ExecuteSql("INSERT INTO products(title,tagline,body,image,price)VALUES(?,?,?,?,?)",
			array('Product 3','Product 3 tagline: we need that for listings','No desc here too as this is demo data','product3.png',1.99));
	}
}
// Not nice to call that every time, but we will go for it in our sample.
ensure_db();

system_execute();
