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

use ScavixWDF\Session\Serializer;
use ScavixWDF\WdfException;

require_once(__DIR__.'/session/serializer.class.php');

/**
 * Initializes the session essential.
 * 
 * @return void
 */
function session_init()
{
	global $CONFIG;

	$CONFIG['class_path']['system'][]   = __DIR__.'/session/';
	$GLOBALS['object_storage'] = array();

	if( !isset($CONFIG['session']['session_name']) )
		$CONFIG['session']['session_name'] = isset($CONFIG['system']['application_name'])?$CONFIG['system']['application_name']:'WDF_SESSION';

	if( !isset($CONFIG['session']['datasource']) )
		$CONFIG['session']['datasource'] = 'internal';

	if( !isset($CONFIG['session']['table']) )
		$CONFIG['session']['table'] = 'sessions';

	if( !isset($CONFIG['session']['prefix']) )
		$CONFIG['session']['prefix'] = '';

	if( !isset($CONFIG['session']['lifetime']) )
		$CONFIG['session']['lifetime'] = '10';

	// Bind sessions to one ip address
	if( !isset($CONFIG['session']['iplock']))
		$CONFIG['session']['iplock'] = false;

	// Classname of the Session Handler
	if( !isset($CONFIG['session']['handler']))
		$CONFIG['session']['handler'] = 'PhpSession';
    
	if( !isset($CONFIG['session']['object_store']))
		$CONFIG['session']['object_store'] = 'SessionStore';
}

/**
 * @internal Starts the session handler.
 */
function session_run()
{
	global $CONFIG;
	// check for backwards compatibility
	if( isset($CONFIG['session']['usephpsession']))
	{
		if( ($CONFIG['session']['usephpsession'] && $CONFIG['session']['handler'] != "PhpSession") ||
			(!$CONFIG['session']['usephpsession'] && $CONFIG['session']['handler'] == "PhpSession") )
			WdfException::Raise('Do not use $CONFIG[\'session\'][\'usephpsession\'] anymore! See session_init() for details.');
	}
    
	$CONFIG['session']['handler'] = fq_class_name($CONFIG['session']['handler']);
	$CONFIG['session']['object_store'] = fq_class_name($CONFIG['session']['object_store']);
    
	$GLOBALS['fw_session_handler'] = new $CONFIG['session']['handler']();
    $GLOBALS['fw_session_handler']->store = 
        $GLOBALS['fw_object_store'] = 
        new $CONFIG['session']['object_store']();
    
    if( !isset($_SESSION[$GLOBALS['CONFIG']['session']['prefix']."object_access"]) )
        $_SESSION[$GLOBALS['CONFIG']['session']['prefix']."object_access"] = array();
}

/**
 * Checks if the unserializer is doing something.
 * 
 * @return bool true if running, else false
 */
function unserializer_active()
{
	return isset($GLOBALS['unserializing_level']) && $GLOBALS['unserializing_level'] > 0;
}

/**
 * Tests two objects for equality.
 * 
 * Checks reference-equality or storage_id equality (if storage_id is set)
 * @param object $o1 First object to compare
 * @param object $o2 Second object to compare
 * @param bool $compare_classes Seems to be deprecated
 * @return bool true if eual, else false
 */
function equals(&$o1, &$o2, $compare_classes=true)
{
	if($o1 === $o2)
		return true;
	
	if( $compare_classes )
	{
		$iso1 = is_object($o1);
		$iso2 = is_object($o2);
		if(( !$iso1 && $iso2 ) || ( $iso1 && !$iso2 ))
			return false;
		if( !$iso1 && !$iso2 )
			return ($o1 === $o2);
	}
	
	if( ($o1 instanceof Closure) || !($o2 instanceof Closure) )
		return false;
	if( !($o1 instanceof Closure) && ($o2 instanceof Closure) )
		return false;
	if( ($o1 instanceof Closure) && ($o2 instanceof Closure) && $o1==$o2 )
		return false;
	
	return (
		isset($o1->_storage_id) &&
		isset($o2->_storage_id) &&
		$o1->_storage_id == $o2->_storage_id
	);
}

/**
 * @shortcut <SessionBase::Sanitize>
 */
function session_sanitize()
{
	return $GLOBALS['fw_session_handler']->Sanitize();
}

/**
 */
function session_kill_all()
{
	$GLOBALS['fw_session_handler']->KillAll();
}

function session_keep_alive($request_key='PING')
{
	return $GLOBALS['fw_session_handler']->KeepAlive($request_key);
}

function session_update($keep_alive=false)
{
    if( isset($GLOBALS['session_update_done']) )
        return;
    $GLOBALS['session_update_done'] = true;
    
    if( !system_is_ajax_call() )
        $GLOBALS['fw_object_store']->Cleanup();

    $GLOBALS['fw_object_store']->Update($keep_alive);
    return $GLOBALS['fw_session_handler']->Update();
}

/**
 * @shortcut <SessionBase::RequestId>
 */
function request_id()
{
	return $GLOBALS['fw_session_handler']->RequestId();
}

/**
 * @shortcut <SessionBase::Store>
 */
function store_object(&$obj,$id="")
{
    if( !isset($GLOBALS['fw_object_store']) )
		return false;
	$res = $GLOBALS['fw_object_store']->Store($obj,$id);
    return $res;
}

/**
 * @shortcut <SessionBase::Delete>
 */
function delete_object($id)
{
    if( !isset($GLOBALS['fw_object_store']) )
		return false;
	return $GLOBALS['fw_object_store']->Delete($id);
}

/**
 * @shortcut <SessionBase::Exists>
 */
function in_object_storage($id)
{
	if( !isset($GLOBALS['fw_object_store']) )
		return false;
	return $GLOBALS['fw_object_store']->Exists($id);
}

/**
 * @shortcut <SessionBase::Restore>
 */
function &restore_object($id)
{
	$res = $GLOBALS['fw_object_store']->Restore($id);
    return $res;
}

/**
 * @shortcut <SessionBase::CreateId>
 */
function create_storage_id(&$obj)
{
	if( isset($GLOBALS['fw_object_store']) && is_object($GLOBALS['fw_object_store']) )
		return $GLOBALS['fw_object_store']->CreateId($obj);
	return false;
}

/**
 * @shortcut <SessionBase::RegenerateId>
 */
function regenerate_session_id()
{
	return $GLOBALS['fw_session_handler']->RegenerateId();
}

/**
 * @shortcut <SessionBase::GenerateSessionId>
 */
function generate_session_id()
{
	return $GLOBALS['fw_session_handler']->GenerateSessionId();
}

/**
 * @shortcut <Serializer::Serialize>
 */
function session_serialize($value)
{
	$s = new Serializer();
	return $s->Serialize($value);
}

/**
 * @shortcut <Serializer::Unserialize>
 */
function session_unserialize($value)
{
	$s = new Serializer();
	$res = $s->Unserialize($value);
	return $res;
}
