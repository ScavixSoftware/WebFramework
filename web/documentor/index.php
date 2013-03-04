<?
require_once(__DIR__."/../system/system.php");

switchToDev();
setAppVersion(0, 0, 1);
system_init('documentor');

if( isset($_GET['clear']) )
{
    cache_clear();
	$_SESSION = array();	
}

system_execute();
