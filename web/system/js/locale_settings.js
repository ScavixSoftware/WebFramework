/**
 * Scavix Web Development Framework
 *
 * Copyright (c) since 2012 Scavix Software Ltd. & Co. KG
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
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
var Locale_Settings_Query = function(elem,method)
{
	return $(elem).data('controller') + method;
};

var Locale_AppendRegionToLanguage = function(language)
{
	var languageExceptions = [
		"zh-CHS",
		"zh-CHT"
	];
	
	return $.inArray(language,languageExceptions) == -1;
}

var Locale_Settings_Init = function()
{
	Locale_Settings_Init = function(){};
	var q = Locale_Settings_Query;
	
	$('select[data-role="language"]').change(function()
	{
		var lang = $(this);
		$('select[data-role="region"]').each( function()
		{
			var reg = $(this);
			$.post(q(this,'ListOptions'),{language:lang.val()},function(d){reg.html(d).change();} );
		});
	});
	
	$('select[data-role="region"]').change(function()
	{
		var reg = $(this);
		var cc =  $('select[data-role="language"]').val();
		if (Locale_AppendRegionToLanguage(cc))
			cc += "-"+reg.val();
		$('select[data-role="datetimeformat"]').each( function()
		{
			var dtf = $(this);
			$.post(q(this,'ListOptions'),{culture_code:cc},function(d){dtf.html(d).change();} );
		});
	});
	
	$('select[data-role="currency"]').change(function()
	{
		var cur = $(this);
		$('select[data-role="currenyformat"]').each( function()
		{
			var fmt = $(this);
			$.post(q(this,'ListOptions'),{currency:cur.val()},function(d){fmt.html(d);} );
		});
	});
	
	$('select[data-role="datetimeformat"]').change(function()
	{
		Locale_Settings_RefreshTFEX_Sample($('input[data-role="timeformatex"]'));
	});
	
	$('select[data-role="timezone"]').change(function()
	{
		Locale_Settings_RefreshTFEX_Sample($('input[data-role="timeformatex"]:checked'));
	});
	
	$('input[data-role="timeformatex"]').change(function()
	{
		Locale_Settings_RefreshTFEX_Sample($(this));
	});
};

var Locale_Settings_Data = function()
{
	var data =
	{
		language: $('select[data-role="language"]').val(),
		region: $('select[data-role="region"]').val(),
		currency: $('select[data-role="currency"]').val(),
		timezone: $('select[data-role="timezone"]').val(),
		cur_format: $('select[data-role="currenyformat"]').val(),
		dt_format: $('select[data-role="datetimeformat"]').val(),
		append_tz: $('input[data-role="timeformatex"]:checked').val()
	};
	return data;
}

var Locale_Settings_RefreshTFEX_Sample = function(tfex)
{
	var tz = $('select[data-role="timezone"]').val();
	var dtf = $('select[data-role="datetimeformat"]').val();
	var cc = $('select[data-role="language"]').val();
	if (Locale_AppendRegionToLanguage(cc))
		cc += "-"+$('select[data-role="region"]').val();
	$(tfex).each( function() 
	{
		var current = $(this);
		$.post(Locale_Settings_Query(this,'RefreshSample'),{append_timezone:current.is(':checked'),timezone:tz,dtf:dtf,culture_code:cc},function(d)
		{
			$('label[for="'+current.attr('id')+'"] > span').replaceWith(d);
		});
	});
};