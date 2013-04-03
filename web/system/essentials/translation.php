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
 * Initializes the translation essential.
 * 
 * @return void
 */
function translation_init()
{
	global $CONFIG;
	$GLOBALS['__unknown_constants'] = array();
	$GLOBALS['__translate_functions'] = array();

	if( !isset($CONFIG['translation']['data_path']) )
	{
		//log_warn('Please define $CONFIG["translation"]["data_path"]');
		$CONFIG['translation']['data_path'] = __DIR__.'/UNDEFINED/';
	}

    if( isset($CONFIG['translation']['sync']['provider']) && $CONFIG['translation']['sync']['provider'] )
    {
        if( !isset($CONFIG['translation']['sync']['datasource']) )
            $CONFIG['translation']['sync']['datasource'] = 'internal';
        
        $CONFIG['class_path']['system'][] = __DIR__.'/translation/';
        $CONFIG['class_path']['system'][] = __DIR__.'/translation/'.strtolower($CONFIG['translation']['sync']['provider']).'/';
    }
    else
        $CONFIG['translation']['sync']['datasource'] = false;
    
	if( !isset($CONFIG['translation']['searchpatterns']) )
		$CONFIG['translation']['searchpatterns'] = array();

	if( !isset($CONFIG['translation']['minlangtransrate']) )
		$CONFIG['translation']['minlangtransrate'] = 0.75;

	if( !isset($CONFIG['localization']['default_language']) )
		$CONFIG['localization']['default_language'] = "en";

	if( !isset($CONFIG['translation']['detect_ci_callback']) )
		$CONFIG['translation']['detect_ci_callback'] = false;
	
	if( !isset($CONFIG['translation']['default_strings']) )
		$CONFIG['translation']['default_strings'] = array();

	$CONFIG['translation']['searchpatterns'] = array_merge(
		$CONFIG['translation']['searchpatterns'],
		array("WINDOW_","TITLE_","BTN_","TXT_","ERR_")
	);

	// build reg pattern once:
	$reg = array();
	foreach( $CONFIG['translation']['searchpatterns'] as $pat )
		$reg[] = '('.$pat.'[a-zA-Z0-9_-]+)(\[[^\]]+\])*';
	$reg = "/".implode("|",$reg)."/";
	$GLOBALS['__translate_regpattern'] = $reg;
    
    system_ensure_path_ending($CONFIG['translation']['data_path']);
}

/**
 * @internal Includes the translation files
 */
function translation_do_includes()
{
	global $CONFIG;

	if( file_exists($CONFIG['translation']['data_path'].$GLOBALS['current_language'].".inc.php") )
	{
		include($CONFIG['translation']['data_path'].$GLOBALS['current_language'].".inc.php");
		$GLOBALS['translation']['included_language'] = $GLOBALS['current_language'];
	}
	else
	{
		$GLOBALS['translation']['included_language'] = $CONFIG['localization']['default_language'];
		if( file_exists($CONFIG['translation']['data_path'].$CONFIG['localization']['default_language'].".inc.php") )
		{
			include($CONFIG['translation']['data_path'].$CONFIG['localization']['default_language'].".inc.php");
		}
		else
		{
			log_warn("No translations found!",$CONFIG['translation']['data_path'].$CONFIG['localization']['default_language'].".inc.php");
			$GLOBALS['translation']['properties'] = array();
			$GLOBALS['translation']['strings'] = array();
		}
	}
	
	// remove those default strings that are now defined
	$CONFIG['translation']['default_strings'] = array_diff_key($CONFIG['translation']['default_strings'],$GLOBALS['translation']['strings']);
}

/**
 * Adds a custom translation function.
 * 
 * Use this to add your own placeholder replacer function. Should accept a single argument which contains
 * the string and return the ready string.
 * <code php>
 * function my_trans($text)
 * {
 *     return str_replace('{username}',"It's Me, Mario!",$text);
 * }
 * translation_add_function('my_trans');
 * </code>
 * @param string $func Name of translation function
 * @return void
 */
function translation_add_function($func)
{
	$GLOBALS['__translate_functions'][] = $func;
}

/**
 * @internal <preg_replace_callback> handler
 */
function __translate_callback($matches)
{
	global $__unknown_constants;

	$mod = array_pop($matches);
	$val = array_pop($matches);
	$as_attribute = false;
	$do_js = false;
	$unbuffered = false;
	switch( $mod )
	{
		case '[NT]':
			return $val;
		case '[NC]':
			$unbuffered = true;
			break;
		case '[JS]':
			$do_js = true;
			break;
		case '[AT]':
			$as_attribute = true;
			break;
		default:
			if( preg_match('/^\[.*\]$/', $mod) )
				log_debug("Unknown translation modifier: $mod");
			else
				$val = $mod;
			break;
	}

	if( isset($__unknown_constants["k".$val]) )
		return $val."?";
	$trans = getString($val,null,$unbuffered);
	if( isset($__unknown_constants["k".$val]) )
		return $trans;

	if( $do_js )
		return substr(json_encode($trans),1,-1);
	if( $as_attribute )
		return htmlentities($trans,ENT_QUOTES,'UTF-8',false);
	return $trans;
}

function __translate_sort_constants($a,$b)
{
	$la = strlen($a);
	$lb = strlen($b);
	return ($la==$lb)?0:(($la<$lb)?1:-1);
}

function __translate($text)
{
	global $CONFIG, $__unknown_constants;
	
	// TODO: reactivate loop regarding unknown constants and thos that shall not be translated
	//while( preg_match($GLOBALS['__translate_regpattern'], $text) )
	{
		if(!is_string($text))
			return $text;

		$text = preg_replace_callback(
			$GLOBALS['__translate_regpattern'],
			'__translate_callback',
			$text
		);
	}
	
	if( ends_with($text, '[NT]') )
		$text = substr($text, 0, -4);

	if( count($__unknown_constants) > 0 )
    {
        if( $CONFIG['translation']['sync']['datasource'] )
        {
            $ds = model_datasource($CONFIG['translation']['sync']['datasource']);
			$ds->ExecuteSql("CREATE TABLE IF NOT EXISTS wdf_unknown_strings (
				term VARCHAR(255) NOT NULL,
				last_hit DATETIME NOT NULL,
				hits INT DEFAULT 0,
				default_val TEXT,
				PRIMARY KEY (term))");
			
            $now = $ds->Driver->Now();
            $sql1 = "INSERT OR IGNORE INTO wdf_unknown_strings(term,last_hit,hits,default_val)VALUES(?,$now,0,?);";
            $sql2 = "UPDATE wdf_unknown_strings SET last_hit=$now, hits=hits+1 WHERE term=?;";
            foreach( $__unknown_constants as $uc )
            {
				$def = cfg_getd('translation','default_strings',$uc,'');
                $ds->Execute($sql1,array($uc,$def));
                $ds->Execute($sql2,$uc);
            }
        }
        else
            log_debug("Unknown text constants: ".render_var(array_values($__unknown_constants)));
    }

	return $text;
}

function __noTranslate_callback($matches)
{
	global $__unknown_constants;

	$mod = array_pop($matches);
	$val = array_pop($matches);
	if( $mod != "[NT]" )
		return $mod."[NT]";
	return $val."[NT]";
}

/**
 * Ensures that a specific content remains untranslated.
 * 
 * This may be useful when automatic translation would match one of you texts.
 * Also very good to prevent user-input from beeing translated!
 * @param string $content Content to remain untranslated
 * @return string Returns the string containing attributes to ensure it will not be translated (NT)
 */
function noTranslate($content)
{
	$res = preg_replace_callback(
		$GLOBALS['__translate_regpattern'],
		'__noTranslate_callback',
		$content
	);
	return $res;
}

/**
 * Detects the users supposed language.
 * 
 * Uses <Localization::detectCulture> to detect the users language.
 * @return string ISO2 code of detected language
 */
function detect_language()
{
	global $CONFIG;
	if( !$CONFIG['translation']['detect_ci_callback'] )
	{
		$ci = Localization::detectCulture();
		$ci = $ci->ResolveToLanguage();
	}
	else
		$ci = $CONFIG['translation']['detect_ci_callback']();

	$GLOBALS['current_language'] = ($ci instanceof CultureInfo)? $ci->Iso2 : $ci;
	return $GLOBALS['current_language'];
}

/**
 * Sets the language and return the current one.
 * 
 * @param mixed $code_or_ci Culture code or <CultureInfo>
 * @return string the previously set language
 */
function translation_set_language($code_or_ci)
{
	if( !isset($GLOBALS['current_language']) )
		detect_language();
	$res = $GLOBALS['current_language'];
	if( $code_or_ci instanceof CultureInfo )
		$GLOBALS['current_language'] = $code_or_ci->ResolveToLanguage()->Code;
	else
		$GLOBALS['current_language'] = $code_or_ci;
	return $res;
}

/**
 * Like <getString>(), but for a specific language.
 * 
 * @param string $lang Language to get string for
 * @param string $constant String to translate
 * @param array $arreplace Array with replacement data
 * @param bool $unbuffered If true skips buffering
 * @return string The translated string
 */
function getStringLang($lang,$constant,$arreplace = null, $unbuffered = false)
{
	$mem = isset($GLOBALS['current_language'])?$GLOBALS['current_language']:false;
	if(!is_null($lang) && (trim($lang) != ""))
		$GLOBALS['current_language'] = $lang;
	$res = getString($constant, $arreplace,$unbuffered);
	if( $mem )
		$GLOBALS['current_language'] = $mem;
	return $res;
}

/**
 * @shortcut for <getString>($constant, $arreplace, $unbuffered, $encoding)
 */
function _text($constant, $arreplace = null, $unbuffered = false, $encoding = null)
{
	return getString($constant,$arreplace,$unbuffered,$encoding);
}

/**
 * @shortcut for <getStringOrig>($constant, $arreplace, $unbuffered, $encoding)
 */
function getString($constant, $arreplace = null, $unbuffered = false, $encoding = null)
{
	if( !$arreplace )
		return getStringOrig($constant,$arreplace,$unbuffered,$encoding);
	$n = array();
	foreach( $arreplace as $k=>$v )
		if( $k[0] == '{' ) $n[$k] = $v; else $n['{'.$k.'}'] = $v;
	return getStringOrig($constant,$n,$unbuffered,$encoding);
}

/**
 * Returns a localized string from the current user's language. 
 * 
 * Replaces all placeholders in string from arreplace i.e. TXT_TEST => "this is a {tt}" with arreplace = aray("{tt}" => "test") => returns "this is a test"
 * Buffers all strings on first access of this function.
 * @param string $constant Text constant. i.e. TXT_...
 * @param array $arreplace Replacement array
 * @param bool $unbuffered Reload from session instead of from cache buffer of current script
 * @param string $encoding E.g. cp1252. Default "null" => UTF-8 will be returned
 * @return string Translated string
 */
function getStringOrig($constant, $arreplace = null, $unbuffered = false, $encoding = null)
{
	global $CONFIG;
	
	// common 'ensure includes'-block. repeated multiple times in this file for performance reasons
	if( !isset($GLOBALS['current_language']) )
		detect_language();
	if( !isset($GLOBALS['translation']['included_language']) || $GLOBALS['translation']['included_language'] != $GLOBALS['current_language'] )
		translation_do_includes();
	
	if( !$unbuffered )
	{
		$key = "lang_{$GLOBALS['translation']['included_language']}_$constant".md5($constant.serialize($arreplace).$GLOBALS['current_language'].$encoding);
		$res = cache_get($key);
        if( $res !== false )
			return $res;
	}
	
	$GLOBALS['translation']['skip_buffering_once'] = false;
	if( isset($GLOBALS['translation']['strings'][$constant]) )
    {
        $res = $GLOBALS['translation']['strings'][$constant];
        $res = ReplaceVariables($res, $arreplace);
    }
    else 
    {
		// may be one of the system default strings
		$def = cfg_get('translation','default_strings',$constant);
		if( $def )
		{
			$res = ReplaceVariables($def, $arreplace);
			$GLOBALS['translation']['skip_buffering_once'] = true;
			$GLOBALS['__unknown_constants']["k".$constant] = $constant;
		}
		else
		{
			// $constant is not really a constant, but just a string, so we just need to replace the vars in there
			$res = ReplaceVariables($constant, $arreplace);
			if($res == $constant)
			{
				// if still the same, constant is unknown
				$res = htmlspecialchars($constant)."?";
				$GLOBALS['translation']['skip_buffering_once'] = true;
				$GLOBALS['__unknown_constants']["k".$constant] = $constant;
			}
		}
    }

	if(!is_null($encoding))
        $res = iconv("UTF-8", $encoding."//IGNORE", $res);
	
	if( !$GLOBALS['translation']['skip_buffering_once'] && preg_match_all($GLOBALS['__translate_regpattern'], $res, $m) )
		$res = __translate($res);
	
	if( isset($key) && !$GLOBALS['translation']['skip_buffering_once'] )
		cache_set($key,$res);

	return $res;
}

/**
 * @shortcut for <getString> but ensuring that it is escaped for use in JS
 */
function getJsString($constant, $arreplace = null, $unbuffered = false, $encoding = null)
{
	$res = getString($constant, $arreplace, $unbuffered, $encoding);
	return substr(json_encode($res),1,-1);
}

/**
 * @internal Replaces variables in strings
 */
function ReplaceVariables($text, $arreplace = null)
{
	if(!is_null($arreplace))
		$text = str_replace(array_keys($arreplace), array_values($arreplace), $text);
	foreach( $GLOBALS['__translate_functions'] as &$func )
		$text = call_user_func($func, $text);
	return $text;
}

/**
 * Returns a list of all languages that have enough translated strings to be usable.
 * 
 * @param int $min_percent_translated Specifies how many percent must be translated for a language to be 'available'
 * @return array Array of language codes
 */
function getAvailableLanguages( $min_percent_translated=false )
{
	global $CONFIG;
	
	// common 'ensure includes'-block. repeated multiple times in this file for performance reasons
	if( !isset($GLOBALS['current_language']) )
		detect_language();
	if( !isset($GLOBALS['translation']['included_language']) || $GLOBALS['translation']['included_language'] != $GLOBALS['current_language'] )
		translation_do_includes();
	
	if( $min_percent_translated === false )
		$min_percent_translated = $CONFIG['translation']['minlangtransrate'];
	elseif( $min_percent_translated > 1 )
		$min_percent_translated /= 100;
	
	$key = "getAvailableLanguages_".$min_percent_translated;
	if(isset($GLOBALS[$key]))
		return $GLOBALS[$key];
	
	$res = array();
	foreach( $GLOBALS['translation']['properties'] as $lang=>$data )
		if( $data['percentage_empty'] < 1 - $min_percent_translated )
			$res[] = $lang;
	$GLOBALS[$key] = $res;
	return $res;
}

/**
 * Checks if there are translations for the given culture.
 * 
 * @param string $cultureCode Culture code to check for
 * @return bool true or false
 */
function checkForExistingLanguage($cultureCode)
{
	$key = "existing_language_check_".$cultureCode;
	if(isset($GLOBALS[$key]))
		return $GLOBALS[$key];
	$arr_lang = array_flip(getAvailableLanguages());
	if( isset($arr_lang[$cultureCode]) )
	{
		$GLOBALS[$key] = $cultureCode;
		return $cultureCode;
	}
	$parentCulture = substr($cultureCode,0,2); // this may match for many, but not for chinese! so fall back below
	if( isset($arr_lang[$parentCulture]) )
	{
		$GLOBALS[$key] = $parentCulture;
		return $parentCulture;
	}
	
	$ci = Localization::getCultureInfo($cultureCode); // this is fallback for above, clean implementation
	if($ci !== false)
	{
		$ci = $ci->ResolveToLanguage();
		if( isset($arr_lang[$ci->Code]) )
		{
			$GLOBALS[$key] = $ci->Code;
			return $ci->Code;
		}
	}
	
	$GLOBALS[$key] = false;
	return false;
}

/**
 * Returns a list of all known constants.
 * 
 * @return array List of all constants
 */
function translation_known_constants()
{
	global $CONFIG;
	
    $res = cache_get('translation_known_constants');
	if( $res )
		return $res;
	
	if( !isset($GLOBALS['translation']['known_constants']) )
	{
		// common 'ensure includes'-block. repeated multiple times in this file for performance reasons
		if( !isset($GLOBALS['current_language']) )
			detect_language();
		if( !isset($GLOBALS['translation']['included_language']) || $GLOBALS['translation']['included_language'] != $GLOBALS['current_language'] )
			translation_do_includes();
		$GLOBALS['translation']['known_constants'] = array_keys($GLOBALS['translation']['strings']);
	}
	
	cache_set('translation_known_constants',$GLOBALS['translation']['known_constants']);
	return $GLOBALS['translation']['known_constants'];
}

/**
 * @internal Skips buffering for the current call
 */
function translation_skip_buffering()
{
	$GLOBALS['translation']['skip_buffering_once'] = true;
}

/**
 * Checks if a string constant exists.
 * 
 * You can use this to test if a string is a translation constant too.
 * @param string $constant Constant to check for existance
 * @return bool true or false
 */
function translation_string_exists($constant)
{
	$known = translation_known_constants();
	return in_array($constant, $known);
}

/**
 * @internal Ensures that a string will not be translated
 */
function translation_ensure_nt($text_potentially_named_like_a_constant)
{
	if( !translation_string_exists($text_potentially_named_like_a_constant) )
		return $text_potentially_named_like_a_constant;
	return $text_potentially_named_like_a_constant."[NT]";
}

/**
 * 'Registers' a string in the translation system with a default value.
 * 
 * This is used in WDF when components require user-interaction without forcing the implementor to
 * create 100ths of strings as the first he must do.
 * @param string $constant Constant name
 * @param string $text The defauilt text
 * @return string The $constant value
 */
function default_string($constant,$text)
{
	cfg_set('translation','default_strings',$constant,$text);
	return $constant;
}

/**
 * @shortcut for <default_string>($constant, $text)
 */
function tds($constant,$text){ return default_string($constant, $text); }
