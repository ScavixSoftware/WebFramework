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
$.ajaxSetup({cache:false});

(function(win,$,undefined)
{
	win.wdf = 
	{
		/* see http://api.jquery.com/jQuery.Callbacks/ */
		ready: $.Callbacks('unique memory'),
		ajaxReady: $.Callbacks('unique'),  // fires without args, just notifier
		ajaxError: $.Callbacks('unique'),  // fires with args (XmlHttpRequest object, TextStatus, ResponseText)
		exception: $.Callbacks('unique'),  // fires with string arg containing the message
		
		arg: function (name)
		{
			return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
		},
		
		validateHref: function(href)
		{
			if( this.settings.rewrite || typeof href != 'string' || href.match(/\?wdf_route/) )
				return href;
			
			href = href.substr(this.settings.site_root.length);
			var parts = href.split("/");
			var url_path = this.settings.site_root + '?wdf_route='+encodeURIComponent((parts[0]||'')+"/"+(parts[1]||'')+"/");
			if( parts.length > 2 )
			{
				parts.shift(); parts.shift();
				url_path += "&"+parts.join("/");
			}
			return url_path;
		},
		
		setCallbackDefault: function(name, func)
		{
			wdf[name].empty().add( func );
			wdf[name]._add = wdf[name].add;
			wdf[name].add = function( fn ){ wdf[name].empty()._add(fn); wdf[name].add = wdf[name]._add; delete(wdf[name]._add); };
		},
		
		init: function(settings)
		{
			// prepare settings object
			settings.route = location.href.substr(settings.site_root.length);
			settings.rewrite = !settings.route.match(/^\?wdf_route/);
			settings.route = settings.rewrite
				?settings.route.split("/")
				:this.arg('wdf_route').split("/");
			settings.controller = settings.route[0] || '~';
			settings.method = settings.route[1] || '';
			settings.route = settings.rewrite
				?settings.controller+"/"+settings.method+"/"
				:'?wdf_route='+encodeURIComponent(this.arg('wdf_route'));
			settings.url_path = settings.site_root + settings.route;
			settings.focus_first_input = (settings.focus_first_input === undefined)?true:settings.focus_first_input;
			
			// Init
			this.settings = settings;
			this.request_id = settings.request_id;
			this.initLogging();
			this.initAjax();
			
			// Add some methods
			for(var method in {"get":1, "post":1})
			{
				this[ method ] = function( controller, data, callback )
				{
					var url = wdf.settings.site_root;
					if( typeof controller === "string"  )
						url += controller;
					else
						url += $(controller).attr('id')
					url = wdf.validateHref(url);
					return $[method](url, data, callback);
				};
			};
			
			// Shortcuts for current controller 
			this.controller = {};
			for(var method in {"get":1, "post":1})
			{
				this.controller[ method ] = function( handler, data, callback )
				{
					return wdf[method](wdf.settings.controller+'/'+handler,data,callback);
				};
			};

			// Focus the first visible input on the page (or after the hash)
			if( this.settings.focus_first_input )
			{
				if( location.hash && $('a[name="'+location.hash.replace(/#/,'')+'"]').length > 0 )
				{
					var anchor = $("a[name='"+location.hash.replace(/#/,'')+"']");
					var input = anchor.parentsUntil('*:has(input:text)').parent().find('input:text:first');
					if( input.length > 0 && anchor.position().top < input.position().top )
						input.select();
				}
				else
				{
					$('form').find('input[type="text"],input[type="email"],input[type="password"],textarea,select').filter(':visible:first').select();
				}
			}
			
			//win.onerror = function(a){ server_debug(a); };
			this.resetPing();
			this.setCallbackDefault('exception', function(msg){ alert(msg); });
			this.ready.fire();
		},
		
		resetPing: function()
		{
			if( wdf.ping_timer ) clearTimeout(wdf.ping_timer);
			wdf.ping_timer = setTimeout(function()
			{
				wdf.post('',{PING:wdf.request_id}, function(){ wdf.resetPing(); });
			},wdf.settings.ping_time || 60000);
		},

		reloadWithoutArgs: function()
		{
			this.redirect(this.settings.url_path);
		},
		
		redirect: function(href,data)
		{
			if( typeof href == 'object' )
			{
				data = href;
				href = this.settings.url_path;
			}
			href = this.validateHref(href);
			
			if( !href.match(/\/\//) )
				href = this.settings.site_root + href;
			
			if( typeof data == 'object' )
				href += (this.settings.rewrite?"?":"&")+$.param(data);
			
			if( location.href == href )
				location.reload();
			else
				location.href = href;
		},
		
		initAjax: function()
		{
			this.original_ajax = $.ajax;
			$.extend({
				ajax: function( s )
				{
					wdf.resetPing();
					if( !s.data )
						s.data = {};
					else if( $.isArray(s.data) )
					{
						var tmp = {};
						for(var i=0; i<s.data.length; i++)
							tmp[s.data[i].name] = s.data[i].value;
						s.data = tmp;
					}
					s.data.request_id = wdf.request_id;

					if( wdf.settings.session_name && wdf.settings.session_id )
					{
						if( s.url.indexOf('?')>=0 )
							s.url += "&"+wdf.settings.session_name+"="+wdf.settings.session_id;
						else
							s.url += "?"+wdf.settings.session_name+"="+wdf.settings.session_id;
					}

					if( s.dataType == 'json' || s.dataType == 'script' )
						return wdf.original_ajax(s);

					if( s.data  )
					{
						if( s.data.PING )
							return wdf.original_ajax(s);
						if( wdf.settings.log_to_server && typeof s.data[wdf.settings.log_to_server] != 'undefined' )
							return wdf.original_ajax(s);
					}

					if( s.success )
						s.original_success = s.success;
					s.original_dataType = s.dataType;
					s.dataType = 'json';

					s.success = function(json_result,status)
					{
						if( json_result )
						{
							var head = document.getElementsByTagName("head")[0];
							if( json_result.dep_css )
							{
								for( var i in json_result.dep_css )
								{
									var css = json_result.dep_css[i];
									if( $('link[href=\''+css+'\']').length == 0 )
									{
										var fileref = document.createElement("link")
										fileref.setAttribute("rel", "stylesheet");
										fileref.setAttribute("type", "text/css");
										fileref.setAttribute("href", css);
										head.appendChild(fileref);
									}
								}
							}

							if( json_result.dep_js )
							{
								for( var i in json_result.dep_js )
								{
									var js = json_result.dep_js[i];
									if( $('script[src=\''+js+'\']').length == 0 )
									{
										var script = document.createElement("script");
										script.setAttribute("type", "text/javascript");
										script.setAttribute("ajaxdelayload", "1");
										script.src = js;
										var jscallback = function() { this.setAttribute("ajaxdelayload", "0"); };
										if (script.addEventListener)
											script.addEventListener("load", jscallback, false);
										else
											script.onreadystatechange = function() {
												if ((this.readyState == "complete") || (this.readyState == "loaded"))
													jscallback.call(this);
											}
										head.appendChild(script);
									}
								}
							
							}
							
							if( json_result.error )
							{
								wdf.exception.fire(json_result.error);
								if( json_result.abort )
									return;
							}
							if( json_result.script )
							{
								$('body').append(json_result.script);
								if( json_result.abort )
									return;
							}
						}

						var param = json_result ? (json_result.html ? json_result.html : json_result) : null;
						if( s.original_success || param )
						{
							// async exec JS after all JS have been loaded
							var cbloaded = function()
							{
								if(($("script[ajaxdelayload='1']").length == 0)) 
								{
									if( s.original_success )
										s.original_success(param, status);
									else if( param )
										$('body').append(param);
									wdf.ajaxReady.fire();
								}
								else
									setTimeout(cbloaded, 10);
							}
							cbloaded();
						}
					};

					s.error = function(xhr, st, et)
					{
						// Mantis #6390: Sign up error with checkemail
						if( st=="error" && !et )
							return;
						wdf.error("ERROR calling " + s.url + ": " + st,xhr.responseText);
						wdf.ajaxError.fire(xhr,st,xhr.responseText);
					}

					return wdf.original_ajax(s);
				}
			});

			$(document).ajaxComplete( function(e, xhr)
			{
				if( xhr && xhr.responseText == "__SESSION_TIMEOUT__" )
					wdf.reloadWithoutArgs();
			});
		},
		
		initLogging: function()
		{
			var perform_logging = function(severity,data)
			{
				if( wdf.settings.log_to_console )
				{
					if( typeof console != 'undefined' && typeof console[severity] != 'undefined' )
						console[severity].apply(console,data);
				}

				if( wdf.settings.log_to_server )
				{
					var d = {sev:severity};
					d[wdf.settings.log_to_server] = [];
					for(var i=0; i<data.length; i++) 
						d[wdf.settings.log_to_server].push(data[i]);
					if( d[wdf.settings.log_to_server].length == 1 )
						d[wdf.settings.log_to_server] = d[wdf.settings.log_to_server][0]
					d[wdf.settings.log_to_server] = $.toJSON(d[wdf.settings.log_to_server]);
					//wdf.server_logger_entries.push(d);
					wdf.post('',d,function(){});
				}
			};
			this.log = function(){ perform_logging('log',arguments); };
			this.debug = function(){ perform_logging('debug',arguments); };
			this.warn = function(){ perform_logging('warn',arguments); };
			this.error = function(){ perform_logging('error',arguments); };
			this.info = function(){ perform_logging('info',arguments); };

//			wdf.server_logger_entries = [];
//			wdf.server_logger = function()
//			{
//				if( wdf.server_logger_entries.length > 0 )
//				{
//					var entry = wdf.server_logger_entries.shift();
//					wdf.post('',entry,function(){});
//				}
//				setTimeout(wdf.server_logger,100);
//			}
//			wdf.server_logger();
		}
	};
	
	if( typeof win.Debug != "function" )
	{
		win.Debug = function()
		{
			wdf.debug("Deprecated debug function called! Use wdf.debug() instead.");
		};
	}
	
	$.fn.enumAttr = function(attr_name)
	{
		var attr = []
		this.each( function(){ if( $(this).attr(attr_name) ) attr.push($(this).attr(attr_name)); } );
		return attr;
	};
	
	$.fn.overlay = function(method)
	{
		return this.each(function()
		{
			if( method == 'remove' )
			{
				$('.wdf_overlay, .wdf_overlay_anim',this).remove();
				return;
			}
			var elem = $(this), overlay = $('<div class="wdf_overlay"/>')
				.appendTo(elem).show()
				.sizeFrom(elem,0,$('.caption',elem).height())
				.css('position','absolute')
				.position({my:'left top',at:'left top',of:elem});
			$('<div class="wdf_overlay_anim"/>')
				.appendTo(elem).show()
				.sizeFrom(overlay)
				.css('position','absolute')
				.position({my:'left top',at:'left top',of:elem})
				.click( function(){ return false;} );
		});
	};
	
	$.fn.sizeFrom = function(elem,add_width,add_height)
	{
		return this.each(function()
		{
			$(this).width( elem.width() + (add_width||0) ).height( elem.height() + (add_height||0) )
		});
	};

})(window,jQuery);

