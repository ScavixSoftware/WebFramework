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
			'current_class'  : 'focused',
			'selectables'    : 'a, input, button',
			'container_class': 'body, .tenfoot_container'
		}, options);
		
		tenfoot_init();
		
		return this;
	};
	
	// We use this instead of focus() as empty elements like <a class='something'></a> will not trigger focus events
	$.fn.setCurrent = function()
	{
		var elem = this;
		return this.each(function()
		{
			$('.'+settings['current_class']).removeClass(settings['current_class']);
			elem.addClass(settings['current_class']).focus().offsetParent().data('tenfoot_current',elem);
		});
	}
	
	var tenfoot_init = function()
	{
		var keyNav = function(elem,dir)
		{
			var nearest = elem.data('nav-'+dir);
			if( nearest )
			{
				if( nearest.is('.tenfoot_element_container') )
					nearest = tenfoot_calc_nearest(elem,dir,settings['selectables'],nearest);
				if( nearest )
					nearest.setCurrent();
			}
		};
		
		$(settings['selectables']).offsetParent().addClass('tenfoot_element_container');
		$(settings['selectables'])
			.each(function()
			{
				tenfoot_nearest('left' , $(this));
				tenfoot_nearest('right', $(this));
				tenfoot_nearest('up'   , $(this));
				tenfoot_nearest('down' , $(this));
			})
			.mouseover(function(){ $(this).setCurrent(); })
			.first().setCurrent();
			
		$(document).keydown( function(e)
		{
			var elem = $('.'+settings['current_class']);
			if( elem.length == 0 )
				return;

			switch( e.which )
			{
				case 37: keyNav(elem,'left'); break;
				case 39: keyNav(elem,'right'); break;
				case 38: keyNav(elem,'up'); break;
				case 40: keyNav(elem,'down'); break;
				
				/* ENTER and SPACE */
				case 13:
				case 32:
					// trigger click for a elements without href attribute
					if( elem.is('a') && !elem.attr('href') )
						elem.click();
					break;
				default: wdf.debug('key: '+e.which); break;
			}
		});
	};
	
	var tenfoot_nearest = function(direction,elem)
	{
		var prop = 'nav-'+direction;
		var predef = elem.data(prop);
		if( predef )
		{
			if( typeof predef == "string" )
				elem.data(prop,$('#'+predef));
			return;
		}
		
		var nearest = tenfoot_calc_nearest(elem,direction,settings['selectables'],elem.offsetParent());
		if( !nearest )
			nearest = tenfoot_calc_nearest(elem,direction,'.tenfoot_element_container',$('body'));
		if( nearest )
			elem.data(prop,nearest);
	};
	
	var tenfoot_calc_nearest = function(elem,direction,selector,parent)
	{
		var from = {}, off = elem.offset(), min_dist = 999999, nearest = false;
			
		switch( direction )
		{
			case 'left':  from.x = off.left;                  from.y = off.top + elem.height()/2; break;
			case 'right': from.x = off.left + elem.width();   from.y = off.top + elem.height()/2; break;
			case 'up':    from.x = off.left + elem.width()/2; from.y = off.top;                   break;
			case 'down':  from.x = off.left + elem.width()/2; from.y = off.top + elem.height();   break;
		}

		$(selector,parent).not(elem).not(elem.offsetParent()).each(function()
		{
			var cur = $(this);
			var off = cur.offset();
			switch( direction )
			{
				case 'left':  if( off.left + cur.width() > from.x ) return; break;
				case 'right': if( off.left < from.x ) return; break;
				case 'up':    if( off.top + cur.height() > from.y ) return; break;
				case 'down':  if( off.top < from.y ) return; break;
			}

			var dist = function(dx,dy)
			{
				dx = off.left + dx - from.x;
				dy = off.top + dy - from.y;
				var dist = Math.sqrt( (dx*dx) + (dy*dy) );
				if( dist < min_dist )
				{
					min_dist = dist;
					nearest = cur;
				}
			};
			dist(0,0); dist(cur.width(),0); dist(cur.width(),cur.height()); dist(0,cur.height());
		});
		return nearest;
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
