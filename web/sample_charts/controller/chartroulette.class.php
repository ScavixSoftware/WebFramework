<?php
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

use ScavixWDF\Base\HtmlPage;
use ScavixWDF\Controls\ChartJS3;
use ScavixWDF\Google\GoogleVisualization;
use ScavixWDF\Google\gvBarChart;
//use ScavixWDF\Google\gvComboChart;
use ScavixWDF\Google\gvGeoChart;
use ScavixWDF\Google\gvPieChart;
use ScavixWDF\JQueryUI\uiTabs;
use ScavixWDF\Localization\Localization;
use ScavixWDF\Model\DataSource;

class ChartRoulette extends HtmlPage
{
	function __construct()
	{
		parent::__construct();
		if( $GLOBALS['CREATE_DATA'] )
		{
			$ds = model_datasource('system');

			$fn = array('John','Jane','Thomas','Marc','Jamie','Bob','Marie');
			$ln = array('Doe','Murphy','Anderson','Smith');
			$country_codes = array('DE','US','RU','FR','IT','SE');
			$ds->ExecuteSql("CREATE TABLE participants(name VARCHAR(50), country VARCHAR(5),age INTEGER,game_count INTEGER,PRIMARY KEY(name))");
			foreach( $fn as $f )
			{
				foreach( $ln as $l )
				{
					$cc = $country_codes[rand(0, count($country_codes)-1)];
					$ds->ExecuteSql("INSERT INTO participants(name,country,age,game_count)VALUES(?,?,?,?)",array("$f $l",$cc,rand(18,70),rand(0,100)));
				}
			}

			$nums = array();
			$ds->ExecuteSql("CREATE TABLE numbers(number INTEGER,hit_count INTEGER,PRIMARY KEY(number))");
			for($i=0; $i<=36; $i++)
			{
				$nums[$i] = 0;
				$ds->ExecuteSql("INSERT INTO numbers(number,hit_count)VALUES(?,?)",array($i,0));
			}
			for($i=0; $i<9999; $i++)
			{
				$rnd = rand(0,36);
				$nums[$rnd]++;
			}
			foreach( $nums as $i=>$c )
				$ds->ExecuteSql("UPDATE numbers SET hit_count=? WHERE number=?",array($c,$i));
		}
		GoogleVisualization::$DefaultDatasource = model_datasource('system');
	}

	/**
	 * @attribute[RequestParam('type','string',false)]
	 * @attribute[RequestParam('data','string',false)]
	 */
	function Index($type,$data)
	{
		$tabs = new uiTabs();
		$tabs->AddTab("Participants by game count", $this->ParticipantsGames());
		$tabs->AddTab("Participants by age", $this->ParticipantsAge());
		$tabs->AddTab("Participants countries", $this->ParticipantsCountries());

		$this->content($tabs);
	}

	function ParticipantsAge()
	{
        $data = DataSource::Get()->ExecuteSql("SELECT age, count(*) as cnt FROM participants GROUP BY age ORDER BY cnt DESC")
            ->Enumerate('cnt', false, 'age');
        $labels = array_map(function ($a) { return "$a years";},array_keys($data));
        $data = array_combine($labels, array_values($data));
        return ChartJS3::Pie("Participants by age", 400)
            ->setSeries(['age'=>"Age",'cnt'=>"Count"])
            ->setPieData($data);
	}

	function ParticipantsGames()
	{
        $data = DataSource::Get()->ExecuteSql("SELECT name, game_count FROM participants ORDER BY game_count DESC")
            ->Enumerate('game_count', false, 'name');
        return ChartJS3::Bar("Participants by game count", 400)
            ->setPieData($data)
            ->legend('display',false)
            ->opt('indexAxis','y');
	}

	function ParticipantsCountries()
	{
		$chart = new gvGeoChart();
		$chart->setTitle("Participants countries")
			->setDataHeader("Country","Participants")
			->setSize(800, 400)
			->opt('is3D',true);
		$country_names = Localization::get_country_names();
		foreach( DataSource::Get()->ExecuteSql("SELECT country, count(*) as cnt FROM participants GROUP BY country ORDER BY cnt DESC") as $row )
			$chart->addDataRow($country_names[$row['country']],intval($row['cnt']));

		return $chart;
	}
}