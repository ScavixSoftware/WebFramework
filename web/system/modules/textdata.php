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

/**
 * Initializes the textdata module
 * 
 * This module provides functions for text-file processing
 * @return void
 */
function textdata_init()
{
}

/**
 * Parses CSV data into an array.
 * 
 * See http://www.php.net/manual/de/function.str-getcsv.php#95132
 * @param string $csv CSV data as string
 * @param string $delimiter CSV delimiter used
 * @param string $enclosure CSV enclosure string
 * @param string $escape CSV escape string
 * @param string $terminator CSV line terminator
 * @return array An array with one entry per line, each beeing an array of fields
 */
function csv_to_array($csv, $delimiter = ',', $enclosure = '"', $escape = '\\', $terminator = "\n")
{
    $result = array();
    $rows = explode($terminator,trim($csv));
    $names = str_getcsv(array_shift($rows),$delimiter,$enclosure,$escape);
    $nc = count($names);
    foreach( $rows as $row )
	{
        if( trim($row) )
		{
            $values = str_getcsv($row,$delimiter,$enclosure,$escape);
            if( !$values )
				$values = array_fill(0,$nc,null);
            $result[] = array_combine($names,$values);
        }
	}
	return $result;
}

/**
 * Extracts the header from a CSV data string
 * 
 * This is usually the first line which contains the field names.
 * @param string $csv CSV data as string
 * @param string $delimiter CSV delimiter used
 * @param string $enclosure CSV enclosure string
 * @param string $escape CSV escape string
 * @param string $terminator CSV line terminator
 * @return array An array with one entry per field
 */
function csv_header($csv, $delimiter = ',', $enclosure = '"', $escape = '\\', $terminator = "\n")
{
	$rows = explode($terminator,trim($csv));
    return str_getcsv(array_shift($rows),$delimiter,$enclosure,$escape);
}

/**
 * Tries to detect the CSV field delimiter
 * 
 * This may be one of: ';'  ','  '|'  '\t'
 * @param string $csv CSV data as string
 * @param string $terminator CSV line terminator
 * @return string The delimiter that seems to match best
 */
function csv_detect_delimiter($csv,$terminator = "\n")
{
	$rows = explode($terminator,trim($csv));
	$r = $rows[0];
	$counts = array();
	foreach( array(';',',','|',"\t") as $delim )
		$counts[count(explode($delim,$r))] = $delim;
	krsort($counts);
    return array_shift($counts);	
}

/**
 * Check if a file contains valid LDIF data.
 * 
 * @param string $file File path
 * @return bool true or false
 */
function ldif_valid($file)
{
	require_once(dirname(__FILE__).'/textdata/ldif2array.class.php');
	$ld = new ldif2array($file,true);
	foreach( $ld->entries as $entry )
		if( isset($entry['objectclass']) )
			return true;
	return false;
}

/**
 * Reads LDIF data from a file into an array.
 * 
 * Sample:
 * <code php>
 * $data_untouched = ldif_to_array($ldif_file);
 * 
 * $fieldmaps = array( 
 *     'mozillaAbPersonAlpha' => array(
 *         'cn'=>'name',
 *         'mail'=>'email',
 *         'homePhone'=>'phone_private'
 * ));
 * $data_mapped = ldif_to_array($ldif_file,$fieldmaps);
 * </code>
 * @param string $file File path
 * @param array $fieldmaps Defines a mapping from LDIF data to own data structures
 * @param bool $add_groups If true adds group information to resulting data
 * @return array Array of datasets
 */
function ldif_to_array($file, $fieldmaps = false, $add_groups = true)
{
	require_once(dirname(__FILE__).'/textdata/ldif2array.class.php');
	$ld = new ldif2array($file,true);
	if( !$fieldmaps )
		return $ld->entries;
	
	if( $add_groups )
	{
		$memberships = array();
		foreach( $ld->entries as $entry )
		{
			if( !isset($entry['objectclass']) || !in_array('groupOfNames', $entry['objectclass']) )
				continue;
			if( !$entry['member'] || count($entry['member'])==0 )
				continue;
			
			foreach( $entry['member'] as $dn )
			{
				if( !isset($memberships[$dn]) )
					$memberships[$dn] = array();
				$memberships[$dn][] = $entry['cn'];
			}
		}
	}
	
	$res = array();
	foreach( $ld->entries as $entry )
	{
		if( !isset($entry['objectclass']) )
			continue;
		foreach( $fieldmaps as $object_class=>$fieldmap )
		{
			if( !in_array($object_class, $entry['objectclass']) )
				continue;
			$r = array();
			foreach( $fieldmap as $k=>$v )
				if( isset($entry[$k]) )
					$r[$v] = trim($entry[$k]);
			if( count($r) > 0 )
			{
				if( $add_groups && isset($memberships[$entry['dn']]) )
					$r['groupMemberships'] = $memberships[$entry['dn']];
				$res[] = $r;
				break;
			}
		}
	}
	return $res;
}

/**
 * Checks if a file contains valid VCARD data.
 * 
 * @param string $file File path
 * @return bool true or false
 */
function vcard_valid($file)
{
	require_once(dirname(__FILE__).'/textdata/vcard_convert.php');
	$conv = new vcard_convert();
	$conv->fromFile($file);
	return isset($conv->cards) && (count($conv->cards) > 0);
}

/**
 * Reads VCARD data from a file into an array
 * 
 * <code php>
 * $data_untouched = vcard_to_array($vcard_file);
 * 
 * $fieldmap = array(
 *     'displayname'=>'name',
 *     'organization'=>'orga',
 *     'email'=>'login'
 * );
 * $data_mapped = vcard_to_array($vcard_file,$fieldmap); 
 * </code>
 * @param string $file File path
 * @param array $fieldmap Defines a mapping from VCARD data to own data structures
 * @return array Array of datasets
 */
function vcard_to_array($file, $fieldmap=false)
{
	require_once(dirname(__FILE__).'/textdata/vcard_convert.php');
	$conv = new vcard_convert();
	$conv->fromFile($file);
	
	$res = array();
	foreach( $conv->cards as $card )
	{
		$entry = (array)$card;
		foreach( $entry as $k=>$v )
		{
			if( is_array($v) )
			{
				foreach( $v as $kv=>$vv )
					$entry[$k.'_'.$kv] = $vv;
			}
		}			
		if( $fieldmap )
		{
			$r = array();
			foreach( $fieldmap as $k=>$v )
				if( isset($entry[$k]) )
					$r[$v] = trim($entry[$k]);
			if( count($r) > 0 )
				$res[] = $r;
		}
		else
			$res[] = $entry;
	}
	return $res;
}
