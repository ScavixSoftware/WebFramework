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
wdf.ready.add(function()
{
    $('div.navigation a[href="'+document.location.href+'"]').addClass("current");
    
    $('table.new_string input.create').click( function()
    { 
        var term = $(this).data('term');
        var text = encodeURIComponent($('textarea.'+term).val()||'');
        wdf.controller.post('CreateString',{term:term,text:text},function()
        {
			$('table.'+term).fadeOut( function(){ $('table.'+term).remove(); } );
        });
    });
    
    $('table.new_string input.delete').click( function()
    { 
        var term = $(this).data('term');
        wdf.controller.post('DeleteString',{term:term},function()
        {
            $('table.'+term).fadeOut( function(){ $('table.'+term).remove(); } );
        });
    });
	
	$('.translations input.save').click( function()
    { 
		var btn = $(this).attr('disabled',true);
		var lang = $('.translations').data('lang');
        var term = btn.data('term');
		var text = encodeURIComponent($('textarea.'+term).val()||'');
        wdf.controller.post('SaveString',{lang:lang,term:term,text:text},function()
		{
			btn.val('Saved').addClass('ok');
			setTimeout(function(){ btn.removeAttr('disabled').val('Save').removeClass('ok err'); },2000);
		});
    });
	
	$('.translations .rename').click( function()
    { 
        wdf.controller.post('Rename',{term:$(this).data('term')});
    });
	
	$('.translations .remove').click( function()
    { 
        wdf.controller.post('Remove',{term:$(this).data('term')});
    });
	
	wdf.exception.add( function(msg){ alert(msg); } );
});