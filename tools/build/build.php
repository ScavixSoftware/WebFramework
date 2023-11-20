<?php
$dstPath = realpath(__DIR__."/../");
$srcRoot = realpath(__DIR__.'/../../web/system');

array_shift($argv);
$tag = array_shift($argv)?:'';
$master = trim(explode(":",@file_get_contents("$srcRoot/.git/HEAD"),2)[1]);
$branch = explode("/",$master);
$branch = $branch[count($branch)-1];
$master = "$srcRoot/.git/$master";
$version = trim(@file_get_contents($master));
$creation = @filemtime($master);

$pharname = "scavix-wdf";
if( $tag )
	$pharname .= "-{$tag}";
$pharname .= '.phar';

echo "Building '$pharname'...\n";

$conf = strtolower(array_pop($argv)?:'');
@mkdir($conf);

class StripFilesFilter extends RecursiveFilterIterator 
{
	public function accept():bool
	{
		return stripos($this->current()->getPathname(),'.git') === false;
    }
}

@unlink("$dstPath/$pharname");
$phar = new Phar("$dstPath/$pharname",0, "scavix-wdf.phar");

$objects = new RecursiveDirectoryIterator($srcRoot,FilesystemIterator::SKIP_DOTS);
$objects = new StripFilesFilter($objects);
$objects = new RecursiveIteratorIterator($objects);
$phar->buildFromIterator($objects,$srcRoot);
$phar->addFromString('VERSION',"$version\n$creation\n$branch");
//$phar->addFile(__DIR__.'/stub-cli.php', 'stub-cli.php');
$phar->setStub("#!/usr/bin/env php" .PHP_EOL.$phar->createDefaultStub('cli.php',''));
