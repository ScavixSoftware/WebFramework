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
var _fcChartStack = {};

function initFusionChart(settings)
{
	if( !settings.debug )
		settings.debug = 0;

	var fcid = settings.chartid + "_chart";
	if( _fcChartStack[fcid] )
		return;
	_fcChartStack[fcid] = true;
//	FusionCharts.setCurrentRenderer('javascript');
	
	var fc = new FusionCharts(settings.swfurl, fcid, settings.width, settings.height, settings.debug, 0);
	if( settings.data )
	{
		fc.setDataXML(settings.data);
		fc.setTransparent(true);
		fc.render(settings.chartid);
	}
	else if( settings.dataurl )
	{
		$.post(settings.dataurl,{},function(d)
		{
			fc.setDataXML(d);
			fc.setTransparent(true);
			fc.render(settings.chartid);
		});
	}
	else
		wdf.debug(fcid+": Missing data or dataurl setting");		
}