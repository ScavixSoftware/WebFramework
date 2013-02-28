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

$.fn.table = function()
{
	return this.each( function()
	{
		var self = this, current_row;

		var actions = $('.ui-table-actions .ui-icon',self);
		if( actions.length > 0 )
		{
			var w = 0;
			$('.ui-table-actions > div',self)
				.hover( function(){ $(this).toggleClass('ui-state-hover'); } )
				.each(function(){ w+=$(this).width(); });

			$('.ui-table-actions .ui-icon',self)
				.click(function()
				{
					wdf.post(self.attr('id')+'/OnActionClicked',{action:$(this).data('action'),row:current_row.attr('id')});
				});

			$('.ui-table-actions',self).width(w);

			var on = function()
			{
				if( $('.ui-table-actions.sorting',self).length>0 )
					return;
				current_row = $(this); 
				$('.ui-table-actions',self).show()
					.position({my:'right center',at:'right-1 center',of:current_row});
			};
			var off = function(){ $('.ui-table-actions',self).hide(); };

			$('.tbody .tr',self).bind('mouseenter click',on);
			$('.caption, .thead, .tfoot',self).bind('mouseenter',off);
			self.bind('mouseleave',off);

			$('.tbody .tr .td:last-child',self).css('padding-right',w)
		}
	});
};

})(jQuery);
