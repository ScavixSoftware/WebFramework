<?

// Pages are PHP classes extending HtmlPage 
$CONFIG['system']['default_page']  = "Products";
// Events are mapped to PHP class methods
$CONFIG['system']['default_event'] = "Index";

// Application specific classpath
classpath_add(__DIR__.'/controller');
classpath_add(__DIR__.'/templates');

// Database connection, a DSN passed to the PDO constructor
$CONFIG['model']['system']['connection_string'] = "sqlite:../shop.db";

// Logger Config
ini_set("error_log", __DIR__.'/../logs/shop_php_error.log');
$CONFIG['system']['logging'] = array
(
	'human_readable' => array
	(
		'path' => __DIR__.'/../logs/',
		'filename_pattern' => 'shop_wdf.log',
		'log_severity' => true,
		'max_filesize' => 10*1024*1024,
		'keep_for_days' => 5,
		'max_trace_depth' => 16,
	),
	'full_trace' => array
	(
		'class' => 'TraceLogger',
		'path' => __DIR__.'/../logs/',
		'filename_pattern' => 'shop_wdf.trace',
		'log_severity' => true,
		'max_trace_depth' => 10,
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

// this is products image folder. 
// setting it up as resource folder allows us to simply use resFile() to reference a products image.
$CONFIG['resources'][] = array
(
	'ext' => 'png|jpg|jpeg|gif',
	'path' => realpath(__DIR__.'/images/'),
	'url' => 'images/',
	'append_nc' => true,
);

// some essentials
$CONFIG['system']['modules'] = array();
date_default_timezone_set("Europe/Berlin");
