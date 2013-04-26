<?

// Defaults
$CONFIG['system']['default_page']  = "DocMain";
$CONFIG['system']['default_event'] = "Init";

// Classpath
classpath_add(__DIR__.'/controller');
classpath_add(__DIR__.'/templates');

// Database
$CONFIG['model']['system']['connection_string'] = "sqlite:doc.db";

// Logger Config
ini_set("error_log", __DIR__.'/../logs/doc_php_error.log');
$CONFIG['system']['logging'] = array
(
	'full_trace' => array
	(
		'class' => 'TraceLogger',
		'path' => __DIR__.'/../logs/',
		'filename_pattern' => 'doc_wdf.trace',
		'log_severity' => true,
		'max_trace_depth' => 2,
		'max_filesize' => 10*1024*1024,
		'keep_for_days' => 4,
	),
);

// Resources config
$CONFIG['resources'][] = array
(
	'ext' => 'js|css|png|jpg|jpeg|gif|htc|ico',
	'path' => realpath(__DIR__.'/res/'),
	'url' => 'res/',
	'append_nc' => true,
);
// If you put WDF into a separate folder next to the app (like here), that folder must be externaly accessible.
// So maybe you'll have to set up a subdomain for it and set that to 'resources_system_url_root'.
// For now we just rely on the built in router that will output the resource contents via readfile().
$CONFIG['resources_system_url_root'] = false;
//$CONFIG['resources_system_url_root'] = 'http://wdf.domain.com/'; // <- sample

// some essentials
$CONFIG['system']['modules'] = array();
date_default_timezone_set("Europe/Berlin");

$CONFIG['system']['admin']['enabled']  = true;
$CONFIG['system']['admin']['username'] = 'admin';
$CONFIG['system']['admin']['password'] = 'admin';
