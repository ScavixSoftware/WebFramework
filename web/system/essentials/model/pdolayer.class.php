<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
 * Copyright (c) since 2013 Scavix Software Ltd. & Co. KG
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
 * @author PamConsult GmbH http://www.pamconsult.com <info@pamconsult.com>
 * @copyright 2007-2012 PamConsult GmbH
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
namespace ScavixWDF\Model;

use PDO;

/**
 * Just the original slightly extended
 * 
 * We need some more functionalities, so extending PDO and using this when connecting to DB in <DataSource>.
 */
class PdoLayer extends PDO
{
    var $Driver = false;
    
	/**
	 * Overrides parent to perform preparations
	 * 
	 * For historical reasons we had some weird argument placeholders in various SQL queries.
	 * This is central point to replace them. Additionally polls drivers PreprocessSql method before
	 * passing flow to parents method.
	 * See <PDO::prepare>
	 * @param string $statement This must be a valid SQL statement for the target database server
	 * @param array $driver_options This array holds one or more key=>value pairs to set attribute values for the PDOStatement object that this method returns
	 * @return mixed PDOStatement (in our case ResultSet) or false
	 */
	function prepare($statement, $driver_options = null)
	{
		// remove the counter from ?0, ?,... so that they are simply ?,?,...
		$statement = preg_replace('/\?\d+/','?',$statement);
        
        // replace ifavail{a,b,c} with a when statement.
        // this is replacement for coalesce but not checking agains NULL but against NULL or empty strings
        $statement = preg_replace_callback('/ifavail{([^}]+)}/iU',function($m)
        {
            $r = array();
            foreach( explode(",",$m[1]) as $p )
                $r[] = "WHEN IFNULL($p,'')!='' THEN $p";
            return "CASE ".implode(" ",$r)." END";
        },$statement);
        
        if( $this->Driver )
            $statement = $this->Driver->PreprocessSql($statement);
		if( is_null($driver_options) )
			return parent::prepare($statement);
		return parent::prepare($statement, $driver_options);
	}
}
