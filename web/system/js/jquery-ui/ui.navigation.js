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
(function($)
{
	$.widget("ui.navigation",
	{
		_init: function()
		{
			var self = this;
			var parent = this.element.parent();

			// tries to get an id for the given uri
			var get_href_id = function(href)
			{
				var res = href;
				if( self.options.root_uri )
					res = res.split(self.options.root_uri);
				else
					res = res.split("?");
				return res[res.length-1];
			};

			// checks if the given uri matches the location.href uri
			var matches_current_page = function(href)
			{
				var aloc = get_href_id(href);
				if( aloc && loc && aloc == loc )
					return true;
				return false;
			};
			var loc = get_href_id(location.href);


			// prepare widget and loop through lis children to set their properties
			this.element.css({position:'absolute', zIndex:99999});
			this.element.addClass("ui-navigation ui-widget").children("li").each( function(index)
			{
				if( self.options.item_width )
					$(this).css({width:self.options.item_width});

				// detect if top-level item was clicked
				var cls;
				if( matches_current_page($('a:first',this).attr('href')) )
					cls = "ui-state-selected ui-state-active nav_borderless";
				else
					cls = "ui-state-default";

				// add classes and bind hovering handler
				$(this).addClass(cls).addClass("ui-corner-top")
					.bind('mouseenter.navigation',function()
					{
						$(this).addClass("ui-state-hover ui-corner-all")
							.removeClass('nav_borderless');
						$('ul:first',this).addClass("ui-state-hover").show('fast');
					})
					.bind('mouseleave.navigation',function()
					{
						$(this).removeClass("ui-state-hover ui-corner-all");
						if( $(this).is('.ui-state-selected') )
							$(this).addClass('nav_borderless');
						$('ul:first',this).hide('fast').removeClass("ui-state-hover");
					});

				// check if one of the subitems matches the current location
				$('ul',this).addClass("ui-subnavigation").find('li a').each( function()
				{
					if( matches_current_page($(this).attr('href')) )
					{
						$(this).parent().parent().parent()
							.addClass("ui-state-selected ui-state-active nav_borderless");
						return false;
					}
				});
			});

			// try to find the default link and mark it active
			if( this.element.find(".ui-state-active").length == 0 )
				this.element.find("a[rel=default]").parent()
					.addClass("ui-state-selected ui-state-active nav_borderless");

			// disable all links without href
			this.element.find('a[href=]')
				.css({cursor:'default',textDecoration:'line-through'})
				.click(function(){return false});
		},
		value: function()
		{
			return this.options.value;
		},
		destroy: function()
		{
			this.element.unbind(".navigation").removeData("navigation");
		}
	});

	$.extend($.ui.navigation,
	{
		version: "0.0.1",
		getter: "value",
		defaults:
		{
			root_uri:'', // http://domain.tld/rootpath/
			item_width:130 // fixed top-level item width
		}
	});

})(jQuery);
