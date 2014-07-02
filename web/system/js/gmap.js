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
(function(win,$,undefined)
{
	win.wdf.gmap = 
	{
		markerClicked: $.Callbacks('unique'),
		
		geocoder: false,
		maps: {},
		markers: {},
		
		init: function(id,options)
		{
			$('#'+id).data('autoShowHints',options['autoShowHints']?true:false);
			if( options['autoShowHints'] )
				delete(options['autoShowHints']);
			this.maps[id] = new google.maps.Map($('#'+id).get(0),options);
		},
		
		addAddress: function(id, address, tooltip)
		{
			if( !this.geocoder )
				this.geocoder = new google.maps.Geocoder();
			
			var req = {address:address};
			this.geocoder.geocode(req, function(result)
			{
				if( result )
				{
					var i = result.length - 1;
					if(i >= 0)
					{
						var m = wdf.gmap.addMarker(id,
							result[i].geometry.location.lat(),
							result[i].geometry.location.lng(),
							{title:tooltip||result[i].formatted_address});
					}
					else if(req.address.indexOf(","))
						wdf.gmap.addAddress(id, req.address.substr(req.address.indexOf(",")+1));
					
					wdf.gmap.showAllMarkers(id);
				}
			});
		},
		
		addMarker: function(id,lat,lng,options)
		{
			if( !this.maps[id] )
				throw "gMap "+id+" not found";
			
			var pos = new google.maps.LatLng(lat,lng);
			var opts =
			{
				map: this.maps[id],
				position: pos
			};
			if( options )
				for(var p in options)
					if(p != "onclick")
						opts[p] = options[p];

			var marker = new google.maps.Marker(opts);
			google.maps.event.addListener(marker, 'click', function() { wdf.gmap.markerClicked.fire(id,marker); } );

			var oc = false;
			if( options )
			{
				for(var p in options)
				{
					if(p == "onclick")
					{
						google.maps.event.addListener(marker, 'click', function() { eval(options["onclick"]); } );
						oc = true;
						break;
					}
				}
			}
			
			if( !oc && marker.title )
			{
				var curmap = this.maps[id];
				var infowindow = new google.maps.InfoWindow({content: marker.title,closeBoxURL: ""});
				google.maps.event.addListener(marker, 'click', function(){ infowindow.open(curmap,marker); });
				if( $('#'+id).data('autoShowHints') )
					google.maps.event.addListenerOnce(curmap, 'idle', function()
					{
						new google.maps.event.trigger( marker, 'click' );
						$('.gm-style-iw').width($('.gm-style-iw').parent().width());
					});
			}
			
			if( !this.markers[id] )
				this.markers[id] = [];
			this.markers[id].push(marker);
		},
		
		showAllMarkers: function(id)
		{
			var bounds, multi = false;
			if( this.markers[id] && this.markers[id].length == 1 )
			{
				bounds = new google.maps.LatLngBounds(this.markers[id][0].position,this.markers[id][0].position);
				this.maps[id].setCenter(bounds.getCenter());
			}
			else if( this.markers[id] && this.markers[id].length > 1 )
			{
				bounds = new google.maps.LatLngBounds();
				for(var i=0; i<this.markers[id].length; i++)
				{
					if( bounds.contains(this.markers[id][i].position) )
						continue;
					bounds.extend(this.markers[id][i].position);
					if( i > 0 )
						multi = true;
				}
				if( multi )
				{
					this.maps[id].setCenter(bounds.getCenter());
					this.maps[id].fitBounds(bounds);
				}
			}
		}
	};
	
})(window,jQuery);
