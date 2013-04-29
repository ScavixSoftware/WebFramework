<?
require_once(__DIR__."/../system/system.php");

switchToDev();
system_init('blog');

if( isset($_GET['clear']) )
{
    cache_clear();
	$_SESSION = array();	
}

system_execute();
