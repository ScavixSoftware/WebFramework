<?php
require_once(__DIR__."/../system/system.php");

system_init('documentor');

switchToDev();
setAppVersion(0, 0, 1);
ScavixWDF\Model\DataSource::SetDefault('system');

if( isset($_GET['clear']) )
{
    cache_clear();
	$_SESSION = array();	
}

system_execute();
