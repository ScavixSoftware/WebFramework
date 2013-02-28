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
			'current_class': 'focused',
			'selectables'  : 'a, input, button'
		}, options);
		
		tenfoot_init();
		
		return this;
	};
	
	var tenfoot_init = function()
	{
		$(settings['selectables'])
			.each(function()
			{
				tenfoot_nearest('left' , $(this));
				tenfoot_nearest('right', $(this));
				tenfoot_nearest('up'   , $(this));
				tenfoot_nearest('down' , $(this));
			})
			.focus(function()
			{
				$('.'+settings['current_class']).removeClass(settings['current_class']);
				$(this).addClass(settings['current_class']);
			})
			.mouseover(function(){ $(this).focus(); })
			
		$(settings['selectables']+':focus').addClass(settings['current_class']);
			
		$(document).keydown( function(e)
		{
			var elem = $('.'+settings['current_class']);
			if( elem.length == 0 )
				return;
			switch( e.which )
			{
				case 37: if( elem.data('nav-left') ) elem.data('nav-left').focus(); break;
				case 39: if( elem.data('nav-right') ) elem.data('nav-right').focus(); break;
				case 38: if( elem.data('nav-up') ) elem.data('nav-up').focus(); break;
				case 40: if( elem.data('nav-down') ) elem.data('nav-down').focus(); break;
				
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
		if( elem.data(prop) ) return;
		
		var from = {}, off = elem.offset(), 
			min_dist = 999999,
			nearest = false;
			
		switch( direction )
		{
			case 'left':  from.x = off.left;                  from.y = off.top + elem.height()/2; break;
			case 'right': from.x = off.left + elem.width();   from.y = off.top + elem.height()/2; break;
			case 'up':    from.x = off.left + elem.width()/2; from.y = off.top;                   break;
			case 'down':  from.x = off.left + elem.width()/2; from.y = off.top + elem.height();   break;
		}
		
		$(settings['selectables']).not(elem).each(function()
		{
			var cur = $(this);
			off = cur.offset();
			switch( direction )
			{
				case 'left':  if( off.left + cur.width() > from.x ) return; break;
				case 'right': if( off.left < from.x ) return; break;
				case 'up':    if( off.top + cur.height() > from.y ) return; break;
				case 'down':  if( off.top < from.y ) return; break;
			}
			off.left += cur.width()/2;
			off.top  += cur.height()/2;
			
			var dx = off.left - from.x;
			var dy = off.top - from.y;
			var dist = Math.sqrt( (dx*dx) + (dy*dy) );
			if( dist < min_dist )
			{
				min_dist = dist;
				nearest = cur;
			}
		});
		
		if( nearest )
			elem.data(prop,nearest);
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
