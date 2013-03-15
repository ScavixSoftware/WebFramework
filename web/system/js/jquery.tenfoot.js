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
(function( $ ){

	var settings = {};
	
	$.tenfoot = function( options )
	{  
		settings = $.extend( {
			current_class  : 'focused',
			selectables    : 'a, input, button',
			container_class: 'body, .tenfoot_container',
			onselect       : false,
			client         : 'browser'
		}, options);
		
		window.start = new Date().getTime();
		window.track = function(text)
		{
			var d = new Date().getTime();
			text = "["+(d-window.start)+"ms] "+text;
//			wdf.debug(text);
		}
		tenfoot_init_keys();
		tenfoot_init();
		return this;
	};
	
	// We use this instead of focus() as empty elements like <a class='something'></a> will not trigger focus events
	$.fn.setCurrent = function()
	{
		var elem = this;
		return this.each(function()
		{
			$('.'+settings.current_class).removeClass(settings.current_class);
			elem.addClass(settings.current_class).focus();
			
			if( settings.client != 'browser' )
			{
				var rect = function(elem,scroll)
				{
					var res = elem.position();
					res.width = elem.width();
					res.height= elem.height();
					if( scroll ) { res.left=elem.scrollLeft(); res.top=elem.scrollTop() }
					res.right = res.left + res.width;
					res.bottom = res.top + res.height;
					return res;
				}
				var op = elem.parents('.tenfoot_element_container'),
					re = rect(elem), 
					rp = rect(op,true);

				if( re.top < rp.top )
					op.scrollTop(re.top);
				else if( re.bottom > rp.bottom )
					op.scrollTop( rp.top + elem.outerHeight(true) );

				if( re.left < rp.left )
					op.scrollTop(re.left);
				else if( re.right > rp.right )
					op.scrollTop( rp.left + elem.outerWidth(true) );
			}
			if( settings.onselect )
				settings.onselect(elem);
		});
	};
	
	var tenfoot_init = function()
	{
		window.track("tenfoot 0");
		var keyNav = function(elem,dir)
		{
			var nearest = tenfoot_nearest(elem,dir,elem.parents('.tenfoot_element_container'));
			if( nearest )
				nearest.setCurrent();
		};
		
		window.track("tenfoot 1");
		$(settings.selectables).mouseover(function(){ $(this).setCurrent(); })
			.offsetParent().addClass('tenfoot_element_container');
		$(settings.selectables)
//			.each(function()
//			{
//				var elem = $(this), cont = elem.parents('.tenfoot_element_container');
//				//window.track('start searching for '+$(this).attr('id'));
//				tenfoot_nearest(elem, 'left' , cont);
//				tenfoot_nearest(elem, 'right', cont);
//				tenfoot_nearest(elem, 'up'   , cont);
//				tenfoot_nearest(elem, 'down' , cont);
//			})
		window.track("tenfoot 2");
		
		$(document)
			.keydown(function(e)
			{
				switch( e.which )
				{
					case settings.keys.left : 
					case settings.keys.right: 
					case settings.keys.up   : 
					case settings.keys.down : 
						$(document).one('keypress',function(e){ e.preventDefault(); });
						e.preventDefault();
						break;
				}
			});
			
		window.track("tenfoot 3");
		$(document).keyup( function(e)
		{
			var elem = $('.'+settings.current_class);
			if( elem.length == 0 )
				return;
			
			switch( e.which )
			{
				case settings.keys.left : e.preventDefault(); keyNav(elem,'left');  break;
				case settings.keys.right: e.preventDefault(); keyNav(elem,'right'); break;
				case settings.keys.up   : e.preventDefault(); keyNav(elem,'up');    break;
				case settings.keys.down : e.preventDefault(); keyNav(elem,'down');  break;
				
				case settings.keys.back:
					break;
				
				case settings.keys.enter:
				case settings.keys.space:
					// trigger click for a elements without href attribute
					if( elem.is('a') && !elem.attr('href') )
						elem.click();
					break;
				default: wdf.debug('unhandled key: '+e.which); break;
			}
		});
		window.track("tenfoot 4");
		
		setTimeout(function(){ if( $('.'+settings.current_class).length==0 ) $(settings.selectables).first().setCurrent(); },3000);
	};
	
	var tenfoot_nearest = function(elem,direction,container)
	{
		var prop = 'nav-'+direction;
		var predef = elem.data(prop);
		if( predef )
		{
			if( typeof predef == "string" )
			{
				if( predef == 'none' )
					return false;
				predef = $('#'+predef);
				if( predef.is('.tenfoot_element_container') )
					predef = tenfoot_nearest_from_candidates(elem,direction,$(settings.selectables,predef) );
				if( predef )
					elem.data(prop,predef);
			}
			return predef;
		}
		
		var nearest = tenfoot_nearest_from_candidates(elem,direction,$(settings.selectables,container));
		if( !nearest )
			nearest = tenfoot_nearest_from_candidates(elem,direction,$('.tenfoot_element_container').not(container).find(settings.selectables) );
		else if( nearest.is('.tenfoot_element_container') )
			nearest = tenfoot_nearest_from_candidates(elem,direction,$(settings.selectables,nearest) );
		
		if( nearest )
			elem.data(prop,nearest);
		else
			elem.data(prop,'none');
		return nearest;
	};
	
	var tenfoot_nearest_from_candidates = function(elem,direction,candidates)
	{
		window.track("tenfoot_nearest_from_candidates "+elem.attr('id')+" "+direction);
		var from = elem.offset(), min_dist = 999999, nearest = false;
			
		switch( direction )
		{
			case 'left':  from.top += elem.height()/2; break;
			case 'right': from.left += elem.width(); from.top += elem.height()/2; break;
			case 'up':    from.left += elem.width()/2; break;
			case 'down':  from.left += elem.width()/2; from.top += elem.height(); break;
		}

		candidates.not(elem).each(function()
		{
			var cur = $(this), off=cur.offset(), curw=cur.width(), curh=cur.height();
			switch( direction )
			{
				case 'left':  if( off.left + curw > from.left ) return; break;
				case 'right': if( off.left < from.left ) return; break;
				case 'up':    if( off.top + curh > from.top ) return; break;
				case 'down':  if( off.top < from.top ) return; break;
			}

			var dist = function(dx,dy)
			{
				dx = off.left + dx - from.left;
				dy = off.top + dy - from.top;
				var dist = Math.sqrt( (dx*dx) + (dy*dy) );
				if( dist < min_dist )
				{
					min_dist = dist;
					nearest = cur;
				}
			};
			dist(0,0); dist(curw,0); dist(curw,curh); dist(0,curh);
		});
		window.track("tenfoot_nearest_from_candidates 2");
		return nearest;
	};
	
	var tenfoot_init_keys = function()
	{
		switch( settings.client )
		{
			case 'nettv':
				settings.keys = {
					left : 132, right: 133,
					up   : 130, down : 131,
					enter: 13 , space: 32 ,
					back : 8
				};
				break;
			default:
				settings.keys = {
					left : 37, right: 39,
					up   : 38, down : 40,
					enter: 13, space: 32,
					back : 8
				};
				break;
		}
	};
	
	/* some 10foot related helper functions */
	
	$.nextOption = function( select_selector )
	{  
		var elem = $(select_selector);
		var opt  = $(select_selector+' option[value="'+elem.val()+'"]');
		var tobe = opt.next().val() || $(select_selector+' option:first').val();
		return elem.val(tobe).change();
	};
	
	$.prevOption = function( select_selector )
	{  
		var elem = $(select_selector);
		var opt  = $(select_selector+' option[value="'+elem.val()+'"]');
		var tobe = opt.prev().val() || $(select_selector+' option:last').val();
		return elem.val(tobe).change();
	};
	
	$.selectedLabel = function( select_selector )
	{  
		return $(select_selector+' option[value="'+$(select_selector).val()+'"]').html();
	};
	
})( jQuery );
