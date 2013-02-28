<?
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
 
/**
 * Checks the remote browser.
 * 
 * If $version is given will check the major version too, if $gt_match is true, greater versions will match too.
 * Samples:
 * <code php>
 * browser_is('msie',7,false); // true for InternetExplorer 7
 * browser_is('msie',7,true); // true for InternetExplorer 7, 8, 9, ...
 * browser_is('msie'); // true for every InternetExplorer
 * </code>
 * @param string $id Browser id (msie, firefox,...)
 * @param int $version Major version to check
 * @param bool $gt_match If true greater versions match too
 * @return bool true or false
 */
function browser_is($id,$version=0,$gt_match=true)
{
	$bd = browserDetails();
	return $bd['browser_id'] == strtoupper($id) && (
		($gt_match && $bd['major_version']>=$version) || 
		(!$gt_match && $bd['major_version']==$version) );
}

/**
 * @shortcut <browser_is>('MSIE')
 */
function isIE(){ return browser_is('MSIE'); }

/**
 * @shortcut <browser_is>('MSIE',6,false)
 */
function isIE6(){ return browser_is('MSIE',6,false); }

/**
 * @shortcut <browser_is>('MSIE',7,false)
 */
function isIE7(){ return browser_is('MSIE',7,false); }

/**
 * @shortcut <browser_is>('MSIE',8,false)
 */
function isIE8(){ return browser_is('MSIE',8,false); }

/**
 * @shortcut <browser_is>('MSIE',9,false)
 */
function isIE9(){ return browser_is('MSIE',9,false); }

/**
 * @shortcut <browser_is>('MSIE',10,false)
 */
function isIE10(){ return browser_is('MSIE',10,false); }

/**
 * @shortcut <browser_is>('MSIE',7,true)
 */
function isMinIE7(){ return browser_is('MSIE',7,true); }

/**
 * @shortcut <browser_is>('MSIE',8,true)
 */
function isMinIE8(){ return browser_is('MSIE',8,true); }

/**
 * @shortcut <browser_is>('FIREFOX')
 */
function isFirefox(){ return browser_is('FIREFOX'); }

/**
 * @shortcut <browser_is>('FIREFOX',3,true)
 */
function isMinFirefox3(){ return browser_is('FIREFOX',3,true); }

/**
 * @internal Fetches all browser information from `$_SERVER['HTTP_USER_AGENT']`
 */
function browserDetails()
{
	global $__BROWSERINFO__CACHE;

	if( isset($__BROWSERINFO__CACHE) )
		return $__BROWSERINFO__CACHE;

	$agent = $_SERVER['HTTP_USER_AGENT'];

    // initialize properties
    $bd['platform'] = "Unknown";
    $bd['browser']  = "Unknown";
    $bd['version']  = "Unknown";
    $bd['agent']    = $agent;

    // find operating system
    if (false !== stripos("win", $agent))
        $bd['platform'] = "Windows";
    elseif (false !== stripos("mac", $agent))
        $bd['platform'] = "MacIntosh";
    elseif (false !== stripos("linux", $agent))
        $bd['platform'] = "Linux";
    elseif (false !== stripos("OS/2", $agent))
        $bd['platform'] = "OS/2";
    elseif (false !== stripos("BeOS", $agent))
        $bd['platform'] = "BeOS";

    // test for Opera
    if (false !== stripos("opera",$agent)){
        $val = stristr($agent, "opera");
        if (false !== stripos("/", $val)){
            $val = explode("/",$val);
            $bd['browser'] = $val[0];
            $val = explode(" ",$val[1]);
            $bd['version'] = $val[0];
        }else{
            $val = explode(" ",stristr($val,"opera"));
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];
        }

    // test for WebTV
    }elseif(false !== stripos("webtv",$agent)){
        $val = explode("/",stristr($agent,"webtv"));
        $bd['browser'] = $val[0];
        $bd['version'] = $val[1];

    // test for MS Internet Explorer version 1
    }elseif(false !== stripos("microsoft internet explorer", $agent)){
        $bd['browser'] = "MSIE";
        $bd['version'] = "1.0";
        $var = stristr($agent, "/");
        if (ereg("308|425|426|474|0b1", $var)){
            $bd['version'] = "1.5";
        }

    // test for NetPositive
    }elseif(false !== stripos("NetPositive", $agent)){
        $val = explode("/",stristr($agent,"NetPositive"));
        $bd['platform'] = "BeOS";
        $bd['browser'] = $val[0];
        $bd['version'] = $val[1];

    // test for MS Internet Explorer
    }elseif(false !== stripos("msie",$agent) && !false !== stripos("opera",$agent)){
        $val = explode(" ",stristr($agent,"msie"));
        $bd['browser'] = $val[0];
        $bd['version'] = $val[1];

    // test for MS Pocket Internet Explorer
    }elseif(false !== stripos("mspie",$agent) || false !== stripos('pocket', $agent)){
        $val = explode(" ",stristr($agent,"mspie"));
        $bd['browser'] = "MSPIE";
        $bd['platform'] = "WindowsCE";
        if (false !== stripos("mspie", $agent))
            $bd['version'] = $val[1];
        else {
            $val = explode("/",$agent);
            $bd['version'] = $val[1];
        }

    // test for Galeon
    }elseif(false !== stripos("galeon",$agent)){
        $val = explode(" ",stristr($agent,"galeon"));
        $val = explode("/",$val[0]);
        $bd['browser'] = $val[0];
        $bd['version'] = $val[1];

    // test for Konqueror
    }elseif(false !== stripos("Konqueror",$agent)){
        $val = explode(" ",stristr($agent,"Konqueror"));
        $val = explode("/",$val[0]);
        $bd['browser'] = $val[0];
        $bd['version'] = $val[1];

    // test for iCab
    }elseif(false !== stripos("icab",$agent)){
        $val = explode(" ",stristr($agent,"icab"));
        $bd['browser'] = $val[0];
        $bd['version'] = $val[1];

    // test for OmniWeb
    }elseif(false !== stripos("omniweb",$agent)){
        $val = explode("/",stristr($agent,"omniweb"));
        $bd['browser'] = $val[0];
        $bd['version'] = $val[1];

    // test for Phoenix
    }elseif(false !== stripos("Phoenix", $agent)){
        $bd['browser'] = "Phoenix";
        $val = explode("/", stristr($agent,"Phoenix/"));
        $bd['version'] = $val[1];

    // test for Firebird
    }elseif(false !== stripos("firebird", $agent)){
        $bd['browser']="Firebird";
        $val = stristr($agent, "Firebird");
        $val = explode("/",$val);
        $bd['version'] = $val[1];

    // test for Firefox
    }elseif(false !== stripos("Firefox", $agent)){
        $bd['browser']="Firefox";
        $val = stristr($agent, "Firefox");
        $val = explode("/",$val);
        $bd['version'] = $val[1];

  // test for Mozilla Alpha/Beta Versions
    }elseif(false !== stripos("mozilla",$agent) &&
        preg_match("/rv:[0-9].[0-9][a-b]/",$agent) && !false !== stripos("netscape",$agent)){
        $bd['browser'] = "Mozilla";
        $val = explode(" ",stristr($agent,"rv:"));
        preg_match("/rv:[0-9].[0-9][a-b]/",$agent,$val);
        $bd['version'] = str_replace("rv:","",$val[0]);

    // test for Mozilla Stable Versions
    }elseif(false !== stripos("mozilla",$agent) &&
        preg_match("/rv:[0-9]\.[0-9]/",$agent) && !false !== stripos("netscape",$agent)){
        $bd['browser'] = "Mozilla";
        $val = explode(" ",stristr($agent,"rv:"));
        preg_match("/rv:[0-9]\.[0-9]\.[0-9]/",$agent,$val);
        $bd['version'] = str_replace("rv:","",$val[0]);

    // test for Lynx & Amaya
    }elseif(false !== stripos("libwww", $agent)){
        if (false !== stripos("amaya", $agent)){
            $val = explode("/",stristr($agent,"amaya"));
            $bd['browser'] = "Amaya";
            $val = explode(" ", $val[1]);
            $bd['version'] = $val[0];
        } else {
            $val = explode("/",$agent);
            $bd['browser'] = "Lynx";
            $bd['version'] = $val[1];
        }

    // test for Safari
    }elseif(false !== stripos("safari", $agent)){
        $bd['browser'] = "Safari";
        $bd['version'] = "";

    // remaining two tests are for Netscape
    }elseif(false !== stripos("netscape",$agent)){
        $val = explode(" ",stristr($agent,"netscape"));
        $val = explode("/",$val[0]);
        $bd['browser'] = $val[0];
        $bd['version'] = $val[1];
    }elseif(false !== stripos("mozilla",$agent) && !preg_match("/rv:[0-9]\.[0-9]\.[0-9]/",$agent)){
        $val = explode(" ",stristr($agent,"mozilla"));
        $val = explode("/",$val[0]);
        $bd['browser'] = "Netscape";
        $bd['version'] = $val[1];
    }

    // clean up extraneous garbage that may be in the name
    $bd['browser'] = preg_replace("/[^a-z,A-Z]/", "", $bd['browser']);
    // clean up extraneous garbage that may be in the version
    $bd['version'] = preg_replace("/[^0-9,.,a-z,A-Z]/", "", $bd['version']);

    // check for AOL
    if (false !== stripos("AOL", $agent)){
        $var = stristr($agent, "AOL");
        $var = explode(" ", $var);
        $bd['aol'] = ereg_replace("[^0-9,.,a-z,A-Z]", "", $var[1]);
    }

	$bd['browser_id'] = strtoupper($bd['browser']);
	$bd['major_version'] = intval($bd['version']);
	$__BROWSERINFO__CACHE = $bd;
    return $bd;
}
