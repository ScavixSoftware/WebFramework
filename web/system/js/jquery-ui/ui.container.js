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
(function($) {
}

$.widget("ui.container", {
	options: {
		left: 0,
		top: 0,
		width: 300,
		height: 'auto',
		closeable: true,
		collapsable: false,
		collapsed: false,
		draggable: true,
		resizable: true,
		container: 'body',
		grid: [1,1],
		dataUrl: false,
		dialogCss:
		{
			margin: 10
		},
		buttons: {}, // additional button (ex: {buttons:{'ui-icon-circle-triangle-n':function(){alert('clicked');}}})
		onResize: false,
		onDrag: false,
		onClose: false,
		onCollapsed: false,
		onOpened: false,
		onFocus: false
	},
	dlg_options: {
		autoOpen: true,
		buttons: {},
		closeOnEscape: false,
		closeText: 'close',
		dialogClass: '',
		draggable: false,
		hide: null,
		height: 'auto',
		maxHeight: false,
		maxWidth: false,
		minHeight: 150,
		minWidth: 150,
		modal: false,
		position: 'center',
		resizable: false,
		show: null,
		stack: true,
		title: '',
		width: 300,
		zIndex: 1
	},
	_init: function()
	{
		var self = this;
		this.dlg_options.position = [this.options.left,this.options.top];
		this.dlg_options.width = this.options.width;
		this.dlg_options.height = this.options.height;

		this.element.dialog(this.dlg_options)
			.parent('.ui-dialog')
			.find('.ui-dialog-titlebar')
				.css({padding:'2px 2px 2px 5px'})
				.end()
			.appendTo(this.options.container);

		// remove close btn (defined by dialog) when it's not needed
		if( !this.options.closeable )
		{
			this.element.parent('.ui-dialog')
				.find('.ui-icon-closethick').parent().remove();
		}
		else // else attach the close trigger if delegated
		{
			if( self.options.onClose )
				self.element.parent().bind('dialogclose',self.options.onClose);
			else
				self.element.parent().bind('dialogclose',function(){ $(this).remove(); });
		}

		// append collapse btn if needed
		if( this.options.collapsable )
		{
			var icon = this.options.collapsed?'ui-icon-circle-triangle-s':'ui-icon-circle-triangle-n';
			self._initBtn(icon,function()
			{
				// if NOT collapsed collapse now
				if( $('.ui-icon-circle-triangle-n',this).length > 0 )
				{
					$('.ui-icon-circle-triangle-n',this)
						.removeClass('ui-icon-circle-triangle-n')
						.addClass('ui-icon-circle-triangle-s');
					self.element.dialog('option', 'height', 'auto');
					self.element.hide('fast',function()
					{
						if( self.options.resizable )
							self.element.dialog('option', 'resizable', false);
					});

					if( self.options.onCollapsed )
						self.options.onCollapsed($(this).parent().parent());
				}
				else // else show
				{
					$('.ui-icon-circle-triangle-s',this)
						.removeClass('ui-icon-circle-triangle-s')
						.addClass('ui-icon-circle-triangle-n');
					self.element.dialog('option', 'height', 'auto');
					self.element.show('fast',function()
					{
						if( self.options.resizable )
							self.element.dialog('option', 'resizable', true);
					});

					// make sure contant AHAX loading occurs if initialized AND not already loaded
					if( self.options.dataUrl && !self.initiallyLoaded )
					{
						$.post(self.options.dataUrl,function(d)
						{
							wdf.debug("show: loading");
							self._loadAjaxContent();
						});
					}

					if( self.options.onOpened )
						self.options.onOpened($(this).parent().parent());
				}
			});
		}
		else // or set the collapsed state to false if no collapse btn there
			this.options.collapsed = false;

		// if shall be draggable initialize it
		if( this.options.draggable )
		{
			this.element.dialog('option', 'draggable', true);
			this.element.parent('.ui-dialog')
				.draggable("option","containment",'parent')
				.draggable("option","grid",this.options.grid);
			if( self.options.onDrag )
				self.element.parent().draggable('option','stop',self.options.onDrag);
		}

		// if shall be resizable initialize it
		if( this.options.resizable )
		{
			this.element.dialog('option', 'resizable', true);
			//this.element.parent('.ui-dialog').resizable("option","grid",this.options.grid);
			if( self.options.onResize )
				self.element.parent().resizable('option','stop',self.options.onResize);
		}

		if( self.options.onFocus )
			self.element.parent().bind('dialogfocus',self.options.onFocus);

		// initialize content AJAX loading
		if( this.options.dataUrl )
		{
			self.initialcontent = self.element.html();
			// if not collapsed initially load
			if( !this.options.collapsed )
			{
				self._loadAjaxContent();
			}
		}

		// if collapsed recalc size and disable resizing
		if( this.options.collapsed )
		{
			self.element.dialog('option', 'height', 'auto');
			self.element.css({display:'none'});
			self.element.dialog('option', 'resizable', false);
		}

		// add additional buttons
		if( self.options.buttons )
		{
			for(var icon in self.options.buttons)
			{
//				if( typeof(self.options.buttons[icon]) == 'string' )
//					eval('self.options.buttons[icon] = ' + self.options.buttons[icon] + ';');
				self._initBtn(icon,self.options.buttons[icon]);
			}
		}

		// apply additional css for the dialog
		if( self.options.dialogCss )
			self.element.parent('.ui-dialog').css(self.options.dialogCss);

		// fix some positioning issues
		self.element.parent('.ui-dialog').css({
			position:'absolute',
			float:'left',
			width: this.options.width,
			/* scendix: skip automatic repositioning of dialog. we know the pos! */
			top: this.options.top,
			left: this.options.left
		});
	},
	_loadAjaxContent: function()
	{
		var self = this;
		$.post(self.options.dataUrl,function(d)
		{
			self.element.html(d);
			self.initiallyLoaded = true;
			// add the reload button
			self._initBtn('ui-icon-refresh',function()
			{
				var btn = $(this).addClass("ui-state-highlight");
				self.element.html(self.initialcontent);
				$.post(self.options.dataUrl,function(d)
				{
					btn.removeClass("ui-state-highlight");
					self.element.html(d);
				});
				return false;
			});
		});
	},
	_initBtn: function(icon,click)
	{
		var self = this;
		
		if( self.element.parent('.ui-dialog').find('.'+icon).length > 0 )
			return;

		var tb = self.element.parent('.ui-dialog').find('.ui-dialog-titlebar');
		var right = (tb.find('.ui-icon').length) * 19;

		var btn = $('<a href="#"></a>')
			.addClass('ui-corner-all')
			.css({right:right+5})
			.attr('role', 'button')
			.hover(
				function(){btn.addClass('ui-state-hover');},
				function(){btn.removeClass('ui-state-hover');}
			)
			.focus(function(){btn.addClass('ui-state-focus');})
			.blur(function(){btn.removeClass('ui-state-focus');})
			.appendTo(tb);

		if( typeof(click) == 'function' )
			btn.click(click);
		else
		{
			var menu = $( "<ul></ul>" );
			for(var label in click)
			{
				if( typeof(click[label]) != 'function' )
				{
					wdf.debug('Wrong type for click function "'+label+'". Function expected!');
					continue;
				}
				var a = $('<a/>').html(label).css({whiteSpace:'nowrap'});
				$('<li/>').append(a).appendTo(menu).click(click[label]);
			}
			menu.menu()
				.css({float:'left',position:'absolute'}).hide()
				.removeClass('ui-corner-all')
				.find('*')
				.removeClass('ui-corner-all');

			self.element.parent().after(menu);


			btn.click( function()
			{
				menu.show();
				menu.position({
					my: "left top",
					at: "left bottom",
					of: $(this),
					collision: "none"
				}).zIndex( btn.zIndex() + 1 );

				setTimeout(function()
				{
					$(document).one('click', function()
					{
						menu.hide();
					});
				},1);
			});
		}
		
		$('<span></span>').text('').addClass("ui-icon").addClass(icon).appendTo(btn);
	}
});

$.extend($.ui.dialog.prototype,
{
	_origMakeDraggable: false,
	_newMakeDraggable: function()
	{
		var self = this,
			options = self.options,
			doc = $(document),
			heightBeforeDrag;
			
		self.uiDialog.draggable({
			cancel: '.ui-dialog-content, .ui-dialog-titlebar-close',
			handle: '.ui-dialog-titlebar',
			containment: 'parent',
			grid: options.grid,
			start: function(event) {
				heightBeforeDrag = options.height;
				$(this).height($(this).height()).addClass("ui-dialog-dragging");
				self._trigger('dragStart', event);
			},
			drag: function(event) {
				self._trigger('drag', event);
			},
			stop: function(event, ui) {
				options.position = [ui.position.left - doc.scrollLeft(),
					ui.position.top - doc.scrollTop()];
				$(this).removeClass("ui-dialog-dragging").height(heightBeforeDrag);
				self._trigger('dragStop', event);
				$.ui.dialog.overlay.resize();
			}
		});
	}
});

//$.ui.dialog.prototype._origMakeDraggable = $.ui.dialog.prototype._makeDraggable;
//$.ui.dialog.prototype._makeDraggable = $.ui.dialog.prototype._newMakeDraggable;

})(jQuery);
