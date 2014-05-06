(function( $ )
{
	$.extend( $.ui.autocomplete.prototype,
	{
		_renderItem: function( ul, item)
		{
			var content = this.options.renderItem
				?this.options.renderItem.call(this,ul,item)
				:(item.html?item.html:(item.label?item.label:item.value));
			return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append($( "<a></a>" ).html(content))
					.appendTo( ul );
		}
	});
})(jQuery);