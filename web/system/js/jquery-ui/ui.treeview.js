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
(function( $, undefined )
{

$.widget( "ui.treeview",
{
	tree: false,
	loader: 0,

	options:
	{
		url: false,
		multiOpen: false,
		nodeSelected: function(event, node){}
	},

	_initSubtree: function( root )
	{
		root.addClass('ui-treeview-node ui-treeview-closed ui-treeview-expandable');

		var sub = root.children('ul');
		sub.remove();
//		if( sub.length > 0 )
//			root.addClass('ui-treeview-expandable');

		var content = (root.children('.icon').length>0)?root.children('.icon'):$("<span class='icon'/>").click( function(){return false;} ).append(root.html());
		var hit = (root.children('.hit').length>0)?root.children('.hit'):$("<span class='hit'/>").append('&nbsp;');


		content.bind('click.treeview',function(){$(this).parent().click();});
		root.empty().append(hit).append(content).append(sub);
		sub.hide();

		root.children('.icon').hover(
			function(){$(this).addClass('ui-treeview-hover');},
			function(){$(this).removeClass('ui-treeview-hover');}
		);

		if( root.hasClass('expanded') )
		{
			root.removeClass('expanded');
			tree._setState(root,true);
		}

		return root;
	},

	_setState: function(root, open, skip_siblings)
	{		
		if( open )
		{
			if( root.children('ul').length > 0 )
				root.addClass('ui-treeview-collapsable');

			root.removeClass('ui-treeview-expandable ui-treeview-closed')
				.addClass('ui-treeview-expanded').find('ul').show();
		}
		else
		{
			if( root.children('ul').length > 0 )
				root.addClass('ui-treeview-expandable');

			root.removeClass('ui-treeview-collapsable ui-treeview-expanded')
				.addClass('ui-treeview-closed').find('ul').hide();

			root.find('li').each( function()
			{
				if( $(this).children('ul').length > 0 )
					$(this).addClass('ui-treeview-expandable');

				$(this).removeClass('ui-treeview-collapsable ui-treeview-expanded')
					.addClass('ui-treeview-closed').find('ul').hide();
			});
		}
		// close siblings that have no children or if multiOpen is not allowed, close all siblings
		if( !skip_siblings )
		{
			root.siblings().each( function()
			{
				if( !tree.options.multiOpen || $(this).children('ul').length == 0 )
					tree._setState($(this),false,true);
			});
		}
	},

	_create: function()
	{
		tree = this;

		this.element.addClass( "ui-treeview" )
			.find('li').each(function()
			{
				tree._initSubtree($(this));
			});

		this.element.find('li').on('click.treeview',function(event)
		{
			var node = $(this);
			var selected = tree.element.find('.ui-treeview-selected').removeClass('ui-treeview-selected');
			node.addClass('ui-treeview-selected');

			if( node.children('ul').length > 0 )
			{
				if( node.children('ul').css('display') != 'none' )
				{
					// close only if node was clicked twice (selected previously)
					if( selected.attr('id') == node.attr('id') )
						tree._setState(node,false);
				}
				else
					tree._setState(node,true);
				tree._trigger('nodeSelected',event,node);
			}
			else if( tree.options.url )
			{
				if( node.hasClass('ui-treeview-empty') )
				{
					tree._setState(node,true);
					tree._trigger('nodeSelected',event,node);
				}
				else
				{
					node.addClass('ui-treeview-loading');
					$.post(tree.options.url,{root:node.attr('id')},function(d)
					{
						node.removeClass('ui-treeview-loading');
						if( d == 'NONE' )
						{
							node.removeClass('ui-treeview-expandable ui-treeview-collapsable')
								.addClass('ui-treeview-empty');
						}
						else
						{
							node.append(d);
							node.children('ul').children('li').each( function()
							{
								tree._initSubtree($(this));
								tree._setState($(this),false);
							});
						}
						tree._setState(node,true);
						tree._trigger('nodeSelected',event,node);
					});
				}
			}
			return false;
		});
	},

	destroy: function()
	{
		tree.element.removeClass( "ui-treeview" );
		tree.element.find('li').die('click.treeview');

		$.Widget.prototype.destroy.apply( this, arguments );
	}
});

})( jQuery );
