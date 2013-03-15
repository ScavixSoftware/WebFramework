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

$js_cookie_error = '<div style="display: block !important; font-weight:bold; text-align: center;"><br/>ERR_JAVASCRIPT_AND_COOKIES_REQUIRED</div>';
$render_noscript_block = isset($render_noscript_block)?$render_noscript_block:$GLOBALS['CONFIG']['system']['htmlpage']['render_noscript'];
$doctype = isset($doctype)?$doctype:$GLOBALS['CONFIG']['system']['htmlpage']['doctype'];

echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n$doctype\n";?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?=$title?></title>
<? if(isset($favicon) && $favicon) { ?>
	<link rel="shortcut icon" href="<?=$favicon?>" />
<? } ?>
	<? foreach($meta as $m) echo $m; ?>
	<? foreach($css as $c) echo $c; ?>
	<? foreach($js as $j) echo $j; ?>
	<? if(isset($inlineCSS)) echo $inlineCSS; ?>
<script type='text/javascript'>
$(function(){ 
	if( !navigator.cookieEnabled )
		return $('body').empty().append('<?=$js_cookie_error?>');
<? if( count($docready) > 0 ): ?>
	wdf.ready.add(function()
	{
	<?=implode("\n\t",$docready);?>
	});
<? endif; ?>
	<?=$wdf_init?>
});
	<?=implode("\n\t",$plaindocready)?>
</script>
</head>
<body<?=isset($isrtl)?"$isrtl":""?><?=isset($bodyClass)?" class='$bodyClass'":""?>>
<? if( $render_noscript_block ): ?>
<noscript>
	<style type="text/css">
		body>*:not(noscript) { display:none !important; }
	</style>
	<?=$js_cookie_error?>
</noscript>
<? endif; ?>
<?
if( isset($sub_template_content) )
	echo $sub_template_content;
else
	foreach($content as $c) echo $c;
?>
</body>
</html>
