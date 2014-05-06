<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
 * Copyright (c) since 2013 Scavix Software Ltd. & Co. KG
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
 * @author PamConsult GmbH http://www.pamconsult.com <info@pamconsult.com>
 * @copyright 2007-2012 PamConsult GmbH
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
namespace ScavixWDF\Localization;

/**
 * @internal 
 */
function internal_getCultureInfo($cultureCode)
{
	switch( strtolower( str_replace("_", "-", $cultureCode)) )
	{
		case 'en-us':
			$ci = new CultureInfo('en-US','en','en','English (United States)','English (United States)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','USD','$%v','-$%v','US Dollar','US Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('US');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'de-de':
			$ci = new CultureInfo('de-DE','de','de','German (Germany)','Deutsch (Deutschland)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mrz','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez','');
			$dtf->GenitiveMonthNames = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->DayNames = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
			$dtf->ShortDayNames = array('So','Mo','Di','Mi','Do','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','Euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('DE');
			$ci->Parent = internal_getLanguage('de');
			return $ci;
		case 'it-it':
			$ci = new CultureInfo('it-IT','it','it','Italian (Italy)','Italiano (Italia)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('gennaio','febbraio','marzo','aprile','maggio','giugno','luglio','agosto','settembre','ottobre','novembre','dicembre','');
			$dtf->ShortMonthNames = array('gen','feb','mar','apr','mag','giu','lug','ago','set','ott','nov','dic','');
			$dtf->GenitiveMonthNames = array('gennaio','febbraio','marzo','aprile','maggio','giugno','luglio','agosto','settembre','ottobre','novembre','dicembre','');
			$dtf->DayNames = array('domenica','lunedì','martedì','mercoledì','giovedì','venerdì','sabato');
			$dtf->ShortDayNames = array('do','lu','ma','me','gi','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','€ %v','-€ %v','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('IT');
			$ci->Parent = internal_getLanguage('it');
			return $ci;
		case 'ja-jp':
			$ci = new CultureInfo('ja-JP','ja','ja','Japanese (Japan)','日本語 (日本)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月','');
			$dtf->ShortMonthNames = array('1','2','3','4','5','6','7','8','9','10','11','12','');
			$dtf->GenitiveMonthNames = array('1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月','');
			$dtf->DayNames = array('日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日');
			$dtf->ShortDayNames = array('日','月','火','水','木','金','土');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('0','.',',','¥','JPY','¥%v','-¥%v','Japanese Yen','円');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('JP');
			$ci->Parent = internal_getLanguage('ja');
			return $ci;
		case 'fr-fr':
			$ci = new CultureInfo('fr-FR','fr','fr','French (France)','Français (France)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->ShortMonthNames = array('janv.','févr.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.','');
			$dtf->GenitiveMonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->DayNames = array('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi');
			$dtf->ShortDayNames = array('di','lu','ma','me','je','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FR');
			$ci->Parent = internal_getLanguage('fr');
			return $ci;
		case 'ar-sa':
			$ci = new CultureInfo('ar-SA','ar','ar','Arabic (Saudi Arabia)','العربية (المملكة العربية السعودية)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('محرم','صفر','ربيع الأول','ربيع الثاني','جمادى الأولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->ShortMonthNames = array('محرم','صفر','ربيع الاول','ربيع الثاني','جمادى الاولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->GenitiveMonthNames = array('محرم','صفر','ربيع الأول','ربيع الثاني','جمادى الأولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('ح','ن','ث','ر','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ر.س.‏','SAR','ر.س.‏ %v','ر.س.‏%v-','Saudi Riyal','ريال سعودي');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('SA');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'bg-bg':
			$ci = new CultureInfo('bg-BG','bg','bg','Bulgarian (Bulgaria)','Български (България)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Януари','Февруари','Март','Април','Май','Юни','Юли','Август','Септември','Октомври','Ноември','Декември','');
			$dtf->ShortMonthNames = array('Януари','Февруари','Март','Април','Май','Юни','Юли','Август','Септември','Октомври','Ноември','Декември','');
			$dtf->GenitiveMonthNames = array('Януари','Февруари','Март','Април','Май','Юни','Юли','Август','Септември','Октомври','Ноември','Декември','');
			$dtf->DayNames = array('неделя','понеделник','вторник','сряда','четвъртък','петък','събота');
			$dtf->ShortDayNames = array('не','по','вт','ср','че','пе','съ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','лв','BGL','%v лв','-%v лв','Bulgarian Lev','лв.');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('BG');
			$ci->Parent = internal_getLanguage('bg');
			return $ci;
		case 'ca-es':
			$ci = new CultureInfo('ca-ES','ca','ca','Catalan (Catalan)','Català (català)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('gener','febrer','març','abril','maig','juny','juliol','agost','setembre','octubre','novembre','desembre','');
			$dtf->ShortMonthNames = array('gen','feb','març','abr','maig','juny','jul','ag','set','oct','nov','des','');
			$dtf->GenitiveMonthNames = array('gener','febrer','març','abril','maig','juny','juliol','agost','setembre','octubre','novembre','desembre','');
			$dtf->DayNames = array('diumenge','dilluns','dimarts','dimecres','dijous','divendres','dissabte');
			$dtf->ShortDayNames = array('dg','dl','dt','dc','dj','dv','ds');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('ES');
			$ci->Parent = internal_getLanguage('ca');
			return $ci;
		case 'zh-tw':
			$ci = new CultureInfo('zh-TW','zh-CHT','zh','Chinese (Taiwan)','中文(台灣)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->ShortMonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->GenitiveMonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->DayNames = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
			$dtf->ShortDayNames = array('日','一','二','三','四','五','六');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','NT$','TWD','NT$%v','-NT$%v','New Taiwan Dollar','新台幣');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('TW');
			$ci->Parent = internal_getLanguage('zh-CHT');
			return $ci;
		case 'cs-cz':
			$ci = new CultureInfo('cs-CZ','cs','cs','Czech (Czech Republic)','Čeština (Česká republika)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('leden','únor','březen','duben','květen','červen','červenec','srpen','září','říjen','listopad','prosinec','');
			$dtf->ShortMonthNames = array('I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII','');
			$dtf->GenitiveMonthNames = array('ledna','února','března','dubna','května','června','července','srpna','září','října','listopadu','prosince','');
			$dtf->DayNames = array('neděle','pondělí','úterý','středa','čtvrtek','pátek','sobota');
			$dtf->ShortDayNames = array('ne','po','út','st','čt','pá','so');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','Kč','CZK','%v Kč','-%v Kč','Czech Koruna','Koruna Česká');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('CZ');
			$ci->Parent = internal_getLanguage('cs');
			return $ci;
		case 'da-dk':
			$ci = new CultureInfo('da-DK','da','da','Danish (Denmark)','Dansk (Danmark)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','marts','april','maj','juni','juli','august','september','oktober','november','december','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','maj','jun','jul','aug','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januar','februar','marts','april','maj','juni','juli','august','september','oktober','november','december','');
			$dtf->DayNames = array('søndag','mandag','tirsdag','onsdag','torsdag','fredag','lørdag');
			$dtf->ShortDayNames = array('sø','ma','ti','on','to','fr','lø');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','kr','DKK','kr %v','kr -%v','Danish Krone','Dansk krone');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('DK');
			$ci->Parent = internal_getLanguage('da');
			return $ci;
		case 'el-gr':
			$ci = new CultureInfo('el-GR','el','el','Greek (Greece)','Ελληνικά (Ελλάδα)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Ιανουάριος','Φεβρουάριος','Μάρτιος','Απρίλιος','Μάιος','Ιούνιος','Ιούλιος','Αύγουστος','Σεπτέμβριος','Οκτώβριος','Νοέμβριος','Δεκέμβριος','');
			$dtf->ShortMonthNames = array('Ιαν','Φεβ','Μαρ','Απρ','Μαϊ','Ιουν','Ιουλ','Αυγ','Σεπ','Οκτ','Νοε','Δεκ','');
			$dtf->GenitiveMonthNames = array('Ιανουαρίου','Φεβρουαρίου','Μαρτίου','Απριλίου','Μαΐου','Ιουνίου','Ιουλίου','Αυγούστου','Σεπτεμβρίου','Οκτωβρίου','Νοεμβρίου','Δεκεμβρίου','');
			$dtf->DayNames = array('Κυριακή','Δευτέρα','Τρίτη','Τετάρτη','Πέμπτη','Παρασκευή','Σάββατο');
			$dtf->ShortDayNames = array('Κυ','Δε','Τρ','Τε','Πε','Πα','Σά');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','ευρώ');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('GR');
			$ci->Parent = internal_getLanguage('el');
			return $ci;
		case 'fi-fi':
			$ci = new CultureInfo('fi-FI','fi','fi','Finnish (Finland)','Suomi (Suomi)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('tammikuu','helmikuu','maaliskuu','huhtikuu','toukokuu','kesäkuu','heinäkuu','elokuu','syyskuu','lokakuu','marraskuu','joulukuu','');
			$dtf->ShortMonthNames = array('tammi','helmi','maalis','huhti','touko','kesä','heinä','elo','syys','loka','marras','joulu','');
			$dtf->GenitiveMonthNames = array('tammikuu','helmikuu','maaliskuu','huhtikuu','toukokuu','kesäkuu','heinäkuu','elokuu','syyskuu','lokakuu','marraskuu','joulukuu','');
			$dtf->DayNames = array('sunnuntai','maanantai','tiistai','keskiviikko','torstai','perjantai','lauantai');
			$dtf->ShortDayNames = array('su','ma','ti','ke','to','pe','la');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FI');
			$ci->Parent = internal_getLanguage('fi');
			return $ci;
		case 'he-il':
			$ci = new CultureInfo('he-IL','he','he','Hebrew (Israel)','עברית (ישראל)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ינואר','פברואר','מרץ','אפריל','מאי','יוני','יולי','אוגוסט','ספטמבר','אוקטובר','נובמבר','דצמבר','');
			$dtf->ShortMonthNames = array('ינו','פבר','מרץ','אפר','מאי','יונ','יול','אוג','ספט','אוק','נוב','דצמ','');
			$dtf->GenitiveMonthNames = array('ינואר','פברואר','מרץ','אפריל','מאי','יוני','יולי','אוגוסט','ספטמבר','אוקטובר','נובמבר','דצמבר','');
			$dtf->DayNames = array('יום ראשון','יום שני','יום שלישי','יום רביעי','יום חמישי','יום שישי','שבת');
			$dtf->ShortDayNames = array('א','ב','ג','ד','ה','ו','ש');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','₪','ILS','₪ %v','₪-%v','Israeli New Shekel','שקל חדש');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IL');
			$ci->Parent = internal_getLanguage('he');
			return $ci;
		case 'hu-hu':
			$ci = new CultureInfo('hu-HU','hu','hu','Hungarian (Hungary)','Magyar (Magyarország)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('január','február','március','április','május','június','július','augusztus','szeptember','október','november','december','');
			$dtf->ShortMonthNames = array('jan.','febr.','márc.','ápr.','máj.','jún.','júl.','aug.','szept.','okt.','nov.','dec.','');
			$dtf->GenitiveMonthNames = array('január','február','március','április','május','június','július','augusztus','szeptember','október','november','december','');
			$dtf->DayNames = array('vasárnap','hétfő','kedd','szerda','csütörtök','péntek','szombat');
			$dtf->ShortDayNames = array('V','H','K','Sze','Cs','P','Szo');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','Ft','HUF','%v Ft','-%v Ft','Hungarian Forint','forint');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('HU');
			$ci->Parent = internal_getLanguage('hu');
			return $ci;
		case 'is-is':
			$ci = new CultureInfo('is-IS','is','is','Icelandic (Iceland)','Íslenska (Ísland)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janúar','febrúar','mars','apríl','maí','júní','júlí','ágúst','september','október','nóvember','desember','');
			$dtf->ShortMonthNames = array('jan.','feb.','mar.','apr.','maí','jún.','júl.','ágú.','sep.','okt.','nóv.','des.','');
			$dtf->GenitiveMonthNames = array('janúar','febrúar','mars','apríl','maí','júní','júlí','ágúst','september','október','nóvember','desember','');
			$dtf->DayNames = array('sunnudagur','mánudagur','þriðjudagur','miðvikudagur','fimmtudagur','föstudagur','laugardagur');
			$dtf->ShortDayNames = array('su','má','þr','mi','fi','fö','la');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('0',',','.','kr.','ISK','%v kr.','-%v kr.','Icelandic Krona','Króna');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('IS');
			$ci->Parent = internal_getLanguage('is');
			return $ci;
		case 'ko-kr':
			$ci = new CultureInfo('ko-KR','ko','ko','Korean (Korea)','한국어 (대한민국)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월','');
			$dtf->ShortMonthNames = array('1','2','3','4','5','6','7','8','9','10','11','12','');
			$dtf->GenitiveMonthNames = array('1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월','');
			$dtf->DayNames = array('일요일','월요일','화요일','수요일','목요일','금요일','토요일');
			$dtf->ShortDayNames = array('일','월','화','수','목','금','토');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('0','.',',','₩','KRW','₩%v','-₩%v','Korean Won','원');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('KR');
			$ci->Parent = internal_getLanguage('ko');
			return $ci;
		case 'nl-nl':
			$ci = new CultureInfo('nl-NL','nl','nl','Dutch (Netherlands)','Nederlands (Nederland)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december','');
			$dtf->ShortMonthNames = array('jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december','');
			$dtf->DayNames = array('zondag','maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag');
			$dtf->ShortDayNames = array('zo','ma','di','wo','do','vr','za');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','€ %v','€ -%v','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('NL');
			$ci->Parent = internal_getLanguage('nl');
			return $ci;
		case 'nb-no':
			$ci = new CultureInfo('nb-NO','no','nb','Norwegian, Bokmål (Norway)','Norsk, bokmål (Norge)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','mars','april','mai','juni','juli','august','september','oktober','november','desember','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','mai','jun','jul','aug','sep','okt','nov','des','');
			$dtf->GenitiveMonthNames = array('januar','februar','mars','april','mai','juni','juli','august','september','oktober','november','desember','');
			$dtf->DayNames = array('søndag','mandag','tirsdag','onsdag','torsdag','fredag','lørdag');
			$dtf->ShortDayNames = array('sø','ma','ti','on','to','fr','lø');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','kr','NOK','kr %v','kr -%v','Norwegian Krone','Norsk krone');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%%v','-%v%');
			$ci->Region = internal_getRegion('NO');
			$ci->Parent = internal_getLanguage('no');
			return $ci;
		case 'pl-pl':
			$ci = new CultureInfo('pl-PL','pl','pl','Polish (Poland)','Polski (Polska)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('styczeń','luty','marzec','kwiecień','maj','czerwiec','lipiec','sierpień','wrzesień','październik','listopad','grudzień','');
			$dtf->ShortMonthNames = array('sty','lut','mar','kwi','maj','cze','lip','sie','wrz','paź','lis','gru','');
			$dtf->GenitiveMonthNames = array('stycznia','lutego','marca','kwietnia','maja','czerwca','lipca','sierpnia','września','października','listopada','grudnia','');
			$dtf->DayNames = array('niedziela','poniedziałek','wtorek','środa','czwartek','piątek','sobota');
			$dtf->ShortDayNames = array('N','Pn','Wt','Śr','Cz','Pt','So');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','zł','PLN','%v zł','-%v zł','Polish Zloty','Złoty');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('PL');
			$ci->Parent = internal_getLanguage('pl');
			return $ci;
		case 'pt-br':
			$ci = new CultureInfo('pt-BR','pt','pt','Portuguese (Brazil)','Português (Brasil)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro','');
			$dtf->ShortMonthNames = array('jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez','');
			$dtf->GenitiveMonthNames = array('janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro','');
			$dtf->DayNames = array('domingo','segunda-feira','terça-feira','quarta-feira','quinta-feira','sexta-feira','sábado');
			$dtf->ShortDayNames = array('dom','seg','ter','qua','qui','sex','sáb');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','R$','BRL','R$ %v','-R$ %v','Real','Real');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('BR');
			$ci->Parent = internal_getLanguage('pt');
			return $ci;
		case 'ro-ro':
			$ci = new CultureInfo('ro-RO','ro','ro','Romanian (Romania)','Română (România)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ianuarie','februarie','martie','aprilie','mai','iunie','iulie','august','septembrie','octombrie','noiembrie','decembrie','');
			$dtf->ShortMonthNames = array('ian.','feb.','mar.','apr.','mai.','iun.','iul.','aug.','sep.','oct.','nov.','dec.','');
			$dtf->GenitiveMonthNames = array('ianuarie','februarie','martie','aprilie','mai','iunie','iulie','august','septembrie','octombrie','noiembrie','decembrie','');
			$dtf->DayNames = array('duminică','luni','marţi','miercuri','joi','vineri','sâmbătă');
			$dtf->ShortDayNames = array('D','L','Ma','Mi','J','V','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','lei','ROL','%v lei','-%v lei','Romanian Leu','Leu');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('RO');
			$ci->Parent = internal_getLanguage('ro');
			return $ci;
		case 'ru-ru':
			$ci = new CultureInfo('ru-RU','ru','ru','Russian (Russia)','Русский (Россия)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь','');
			$dtf->ShortMonthNames = array('янв','фев','мар','апр','май','июн','июл','авг','сен','окт','ноя','дек','');
			$dtf->GenitiveMonthNames = array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря','');
			$dtf->DayNames = array('воскресенье','понедельник','вторник','среда','четверг','пятница','суббота');
			$dtf->ShortDayNames = array('Вс','Пн','Вт','Ср','Чт','Пт','Сб');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','р.','RUR','%vр.','-%vр.','Russian Ruble','рубль');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%v%','-%v%');
			$ci->Region = internal_getRegion('RU');
			$ci->Parent = internal_getLanguage('ru');
			return $ci;
		case 'hr-hr':
			$ci = new CultureInfo('hr-HR','hr','hr','Croatian (Croatia)','Hrvatski (Hrvatska)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('siječanj','veljača','ožujak','travanj','svibanj','lipanj','srpanj','kolovoz','rujan','listopad','studeni','prosinac','');
			$dtf->ShortMonthNames = array('sij','vlj','ožu','tra','svi','lip','srp','kol','ruj','lis','stu','pro','');
			$dtf->GenitiveMonthNames = array('siječanj','veljača','ožujak','travanj','svibanj','lipanj','srpanj','kolovoz','rujan','listopad','studeni','prosinac','');
			$dtf->DayNames = array('nedjelja','ponedjeljak','utorak','srijeda','četvrtak','petak','subota');
			$dtf->ShortDayNames = array('ne','po','ut','sr','če','pe','su');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','kn','HRK','%v kn','-%v kn','Croatian Kuna','hrvatska kuna');
			$ci->NumberFormat = new NumberFormat('2',',','.','- %v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%%v');
			$ci->Region = internal_getRegion('HR');
			$ci->Parent = internal_getLanguage('hr');
			return $ci;
		case 'sk-sk':
			$ci = new CultureInfo('sk-SK','sk','sk','Slovak (Slovakia)','Slovenčina (Slovenská republika)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('január','február','marec','apríl','máj','jún','júl','august','september','október','november','december','');
			$dtf->ShortMonthNames = array('I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII','');
			$dtf->GenitiveMonthNames = array('januára','februára','marca','apríla','mája','júna','júla','augusta','septembra','októbra','novembra','decembra','');
			$dtf->DayNames = array('nedeľa','pondelok','utorok','streda','štvrtok','piatok','sobota');
			$dtf->ShortDayNames = array('ne','po','ut','st','št','pi','so');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('SK');
			$ci->Parent = internal_getLanguage('sk');
			return $ci;
		case 'sq-al':
			$ci = new CultureInfo('sq-AL','sq','sq','Albanian (Albania)','shqipe (Shqipëria)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janar','shkurt','mars','prill','maj','qershor','korrik','gusht','shtator','tetor','nëntor','dhjetor','');
			$dtf->ShortMonthNames = array('Jan','Shk','Mar','Pri','Maj','Qer','Kor','Gsh','Sht','Tet','Nën','Dhj','');
			$dtf->GenitiveMonthNames = array('janar','shkurt','mars','prill','maj','qershor','korrik','gusht','shtator','tetor','nëntor','dhjetor','');
			$dtf->DayNames = array('e diel','e hënë','e martë','e mërkurë','e enjte','e premte','e shtunë');
			$dtf->ShortDayNames = array('Di','Hë','Ma','Më','En','Pr','Sh');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','Lek','ALL','%vLek','-%vLek','Albanian Lek','Lek');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%v%','-%v%');
			$ci->Region = internal_getRegion('AL');
			$ci->Parent = internal_getLanguage('sq');
			return $ci;
		case 'sv-se':
			$ci = new CultureInfo('sv-SE','sv','sv','Swedish (Sweden)','Svenska (Sverige)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','maj','jun','jul','aug','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december','');
			$dtf->DayNames = array('söndag','måndag','tisdag','onsdag','torsdag','fredag','lördag');
			$dtf->ShortDayNames = array('sö','må','ti','on','to','fr','lö');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','kr','SEK','%v kr','-%v kr','Swedish Krona','Svensk krona');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('SE');
			$ci->Parent = internal_getLanguage('sv');
			return $ci;
		case 'th-th':
			$ci = new CultureInfo('th-TH','th','th','Thai (Thailand)','ไทย (ไทย)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม','');
			$dtf->ShortMonthNames = array('ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.','');
			$dtf->GenitiveMonthNames = array('มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม','');
			$dtf->DayNames = array('อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์');
			$dtf->ShortDayNames = array('อ','จ','อ','พ','พ','ศ','ส');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','฿','THB','฿%v','-฿%v','Thai Baht','บาท');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('TH');
			$ci->Parent = internal_getLanguage('th');
			return $ci;
		case 'tr-tr':
			$ci = new CultureInfo('tr-TR','tr','tr','Turkish (Turkey)','Türkçe (Türkiye)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık','');
			$dtf->ShortMonthNames = array('Oca','Şub','Mar','Nis','May','Haz','Tem','Ağu','Eyl','Eki','Kas','Ara','');
			$dtf->GenitiveMonthNames = array('Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık','');
			$dtf->DayNames = array('Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi');
			$dtf->ShortDayNames = array('Pz','Pt','Sa','Ça','Pe','Cu','Ct');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','TL','TRY','%v TL','-%v TL','Turkish Lira','Türk Lirası');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('TR');
			$ci->Parent = internal_getLanguage('tr');
			return $ci;
		case 'ur-pk':
			$ci = new CultureInfo('ur-PK','ur','ur','Urdu (Islamic Republic of Pakistan)','اُردو (پاکستان)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('جنورى','فرورى','مارچ','اپريل','مئ','جون','جولاٸ','اگست','ستمبر','اکتوبر','نومبر','دسمبر','');
			$dtf->ShortMonthNames = array('جنورى','فرورى','مارچ','اپريل','مئ','جون','جولاٸ','اگست','ستمبر','اکتوبر','نومبر','دسمبر','');
			$dtf->GenitiveMonthNames = array('جنورى','فرورى','مارچ','اپريل','مئ','جون','جولاٸ','اگست','ستمبر','اکتوبر','نومبر','دسمبر','');
			$dtf->DayNames = array('اتوار','پير','منگل','بدھ','جمعرات','جمعه','هفته');
			$dtf->ShortDayNames = array('ا','پ','م','ب','ج','ج','ه');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','Rs','PKR','Rs%v','Rs%v-','Pakistan Rupee','روپيه');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('PK');
			$ci->Parent = internal_getLanguage('ur');
			return $ci;
		case 'id-id':
			$ci = new CultureInfo('id-ID','id','id','Indonesian (Indonesia)','Bahasa Indonesia (Indonesia)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agust','Sep','Okt','Nop','Des','');
			$dtf->GenitiveMonthNames = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember','');
			$dtf->DayNames = array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu');
			$dtf->ShortDayNames = array('M','S','S','R','K','J','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('0',',','.','Rp','IDR','Rp%v','(Rp%v)','Indonesian Rupiah','Rupiah');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%v %','-%v%');
			$ci->Region = internal_getRegion('ID');
			$ci->Parent = internal_getLanguage('id');
			return $ci;
		case 'uk-ua':
			$ci = new CultureInfo('uk-UA','uk','uk','Ukrainian (Ukraine)','Україньска (Україна)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Січень','Лютий','Березень','Квітень','Травень','Червень','Липень','Серпень','Вересень','Жовтень','Листопад','Грудень','');
			$dtf->ShortMonthNames = array('Січ','Лют','Бер','Кві','Тра','Чер','Лип','Сер','Вер','Жов','Лис','Гру','');
			$dtf->GenitiveMonthNames = array('січня','лютого','березня','квітня','травня','червня','липня','серпня','вересня','жовтня','листопада','грудня','');
			$dtf->DayNames = array('неділя','понеділок','вівторок','середа','четвер','п\'ятниця','субота');
			$dtf->ShortDayNames = array('Нд','Пн','Вт','Ср','Чт','Пт','Сб');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','грн.','UAH','%v грн.','-%v грн.','Ukrainian Grivna','гривня');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('UA');
			$ci->Parent = internal_getLanguage('uk');
			return $ci;
		case 'be-by':
			$ci = new CultureInfo('be-BY','be','be','Belarusian (Belarus)','Беларускі (Беларусь)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Студзень','Люты','Сакавік','Красавік','Май','Чэрвень','Ліпень','Жнівень','Верасень','Кастрычнік','Лістапад','Снежань','');
			$dtf->ShortMonthNames = array('Сту','Лют','Сак','Кра','Май','Чэр','Ліп','Жні','Вер','Кас','Ліс','Сне','');
			$dtf->GenitiveMonthNames = array('студзеня','лютага','сакавіка','красавіка','мая','чэрвеня','ліпеня','жніўня','верасня','кастрычніка','лістапада','снежня','');
			$dtf->DayNames = array('нядзеля','панядзелак','аўторак','серада','чацвер','пятніца','субота');
			$dtf->ShortDayNames = array('нд','пн','аў','ср','чц','пт','сб');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','р.','BYB','%v р.','-%v р.','Belarusian Ruble','рубль');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('BY');
			$ci->Parent = internal_getLanguage('be');
			return $ci;
		case 'sl-si':
			$ci = new CultureInfo('sl-SI','sl','sl','Slovenian (Slovenia)','Slovenski (Slovenija)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','marec','april','maj','junij','julij','avgust','september','oktober','november','december','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','maj','jun','jul','avg','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januar','februar','marec','april','maj','junij','julij','avgust','september','oktober','november','december','');
			$dtf->DayNames = array('nedelja','ponedeljek','torek','sreda','četrtek','petek','sobota');
			$dtf->ShortDayNames = array('ne','po','to','sr','če','pe','so');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','Euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('SI');
			$ci->Parent = internal_getLanguage('sl');
			return $ci;
		case 'et-ee':
			$ci = new CultureInfo('et-EE','et','et','Estonian (Estonia)','Eesti (Eesti)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('jaanuar','veebruar','märts','aprill','mai','juuni','juuli','august','september','oktoober','november','detsember','');
			$dtf->ShortMonthNames = array('jaan','veebr','märts','apr','mai','juuni','juuli','aug','sept','okt','nov','dets','');
			$dtf->GenitiveMonthNames = array('jaanuar','veebruar','märts','aprill','mai','juuni','juuli','august','september','oktoober','november','detsember','');
			$dtf->DayNames = array('pühapäev','esmaspäev','teisipäev','kolmapäev','neljapäev','reede','laupäev');
			$dtf->ShortDayNames = array('P','E','T','K','N','R','L');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',' ','kr','EEK','%v kr','-%v kr','Estonian Kroon','Kroon');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('EE');
			$ci->Parent = internal_getLanguage('et');
			return $ci;
		case 'lv-lv':
			$ci = new CultureInfo('lv-LV','lv','lv','Latvian (Latvia)','Latviešu (Latvija)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janvāris','februāris','marts','aprīlis','maijs','jūnijs','jūlijs','augusts','septembris','oktobris','novembris','decembris','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','Mai','Jūn','Jūl','Aug','Sep','Okt','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('janvārī','februārī','martā','aprīlī','maijā','jūnijā','jūlijā','augustā','septembrī','oktobrī','novembrī','decembrī','');
			$dtf->DayNames = array('svētdiena','pirmdiena','otrdiena','trešdiena','ceturtdiena','piektdiena','sestdiena');
			$dtf->ShortDayNames = array('Sv','Pr','Ot','Tr','Ce','Pk','Se');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','Ls','LVL','Ls %v','-Ls %v','Latvian Lats','Lats');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%%v','-%v%');
			$ci->Region = internal_getRegion('LV');
			$ci->Parent = internal_getLanguage('lv');
			return $ci;
		case 'lt-lt':
			$ci = new CultureInfo('lt-LT','lt','lt','Lithuanian (Lithuania)','Lietuvių (Lietuva)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('sausis','vasaris','kovas','balandis','gegužė','birželis','liepa','rugpjūtis','rugsėjis','spalis','lapkritis','gruodis','');
			$dtf->ShortMonthNames = array('Sau','Vas','Kov','Bal','Geg','Bir','Lie','Rgp','Rgs','Spl','Lap','Grd','');
			$dtf->GenitiveMonthNames = array('sausio','vasario','kovo','balandžio','gegužės','birželio','liepos','rugpjūčio','rugsėjo','spalio','lapkričio','gruodžio','');
			$dtf->DayNames = array('sekmadienis','pirmadienis','antradienis','trečiadienis','ketvirtadienis','penktadienis','šeštadienis');
			$dtf->ShortDayNames = array('S','P','A','T','K','Pn','Š');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','Lt','LTL','%v Lt','-%v Lt','Lithuanian Litas','Litas');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('LT');
			$ci->Parent = internal_getLanguage('lt');
			return $ci;
		case 'fa-ir':
			$ci = new CultureInfo('fa-IR','fa','fa','Persian (Iran)','فارسى (ايران)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('جانفييه','فيفرييه','مارس','أفريل','مي','جوان','جوييه','أوت','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('جانفييه','فيفرييه','مارس','أفريل','مي','جوان','جوييه','أوت','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('جانفييه','فيفرييه','مارس','أفريل','مي','جوان','جوييه','أوت','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','/',',','ريال','IRR','ريال %v','ريال%v-','Iranian Rial','رىال');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('IR');
			$ci->Parent = internal_getLanguage('fa');
			return $ci;
		case 'vi-vn':
			$ci = new CultureInfo('vi-VN','vi','vi','Vietnamese (Vietnam)','Tiếng Việt (Việt Nam)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Tháng Giêng','Tháng Hai','Tháng Ba','Tháng Tư','Tháng Năm','Tháng Sáu','Tháng Bảy','Tháng Tám','Tháng Chín','Tháng Mười','Tháng Mười Một','Tháng Mười Hai','');
			$dtf->ShortMonthNames = array('Thg1','Thg2','Thg3','Thg4','Thg5','Thg6','Thg7','Thg8','Thg9','Thg10','Thg11','Thg12','');
			$dtf->GenitiveMonthNames = array('Tháng Giêng','Tháng Hai','Tháng Ba','Tháng Tư','Tháng Năm','Tháng Sáu','Tháng Bảy','Tháng Tám','Tháng Chín','Tháng Mười','Tháng Mười Một','Tháng Mười Hai','');
			$dtf->DayNames = array('Chủ Nhật','Thứ Hai','Thứ Ba','Thứ Tư','Thứ Năm','Thứ Sáu','Thứ Bảy');
			$dtf->ShortDayNames = array('C','H','B','T','N','S','B');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','₫','VND','%v ₫','-%v ₫','Vietnamese Dong','Đồng');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('VN');
			$ci->Parent = internal_getLanguage('vi');
			return $ci;
		case 'hy-am':
			$ci = new CultureInfo('hy-AM','hy','hy','Armenian (Armenia)','Հայերեն (Հայաստան)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Հունվար','Փետրվար','Մարտ','Ապրիլ','Մայիս','Հունիս','Հուլիս','Օգոստոս','Սեպտեմբեր','Հոկտեմբեր','Նոյեմբեր','Դեկտեմբեր','');
			$dtf->ShortMonthNames = array('ՀՆՎ','ՓՏՎ','ՄՐՏ','ԱՊՐ','ՄՅՍ','ՀՆՍ','ՀԼՍ','ՕԳՍ','ՍԵՊ','ՀՈԿ','ՆՈՅ','ԴԵԿ','');
			$dtf->GenitiveMonthNames = array('Հունվար','Փետրվար','Մարտ','Ապրիլ','Մայիս','Հունիս','Հուլիս','Օգոստոս','Սեպտեմբեր','Հոկտեմբեր','Նոյեմբեր','Դեկտեմբեր','');
			$dtf->DayNames = array('Կիրակի','Երկուշաբթի','Երեքշաբթի','Չորեքշաբթի','Հինգշաբթի','ՈՒրբաթ','Շաբաթ');
			$dtf->ShortDayNames = array('Կ','Ե','Ե','Չ','Հ','Ո','Շ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','դր.','AMD','%v դր.','-%v դր.','Armenian Dram','դրամ');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','% %v','-%v%');
			$ci->Region = internal_getRegion('AM');
			$ci->Parent = internal_getLanguage('hy');
			return $ci;
		case 'az-latn-az':
			$ci = new CultureInfo('az-Latn-AZ','az','az','Azeri (Latin, Azerbaijan)','Azərbaycan­ılı (Azərbaycanca)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Yanvar','Fevral','Mart','Aprel','May','İyun','İyul','Avgust','Sentyabr','Oktyabr','Noyabr','Dekabr','');
			$dtf->ShortMonthNames = array('Yan','Fev','Mar','Apr','May','İyun','İyul','Avg','Sen','Okt','Noy','Dek','');
			$dtf->GenitiveMonthNames = array('yanvar','fevral','mart','aprel','may','iyun','iyul','avgust','sentyabr','oktyabr','noyabr','dekabr','');
			$dtf->DayNames = array('Bazar','Bazar ertəsi','Çərşənbə axşamı','Çərşənbə','Cümə axşamı','Cümə','Şənbə');
			$dtf->ShortDayNames = array('B','Be','Ça','Ç','Ca','C','Ş');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','man.','AZM','%v man.','-%v man.','Azerbaijanian Manat','manat');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('AZ');
			$ci->Parent = internal_getLanguage('az');
			return $ci;
		case 'eu-es':
			$ci = new CultureInfo('eu-ES','eu','eu','Basque (Basque)','Euskara (euskara)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('urtarrila','otsaila','martxoa','apirila','maiatza','ekaina','uztaila','abuztua','iraila','urria','azaroa','abendua','');
			$dtf->ShortMonthNames = array('urt.','ots.','mar.','api.','mai.','eka.','uzt.','abu.','ira.','urr.','aza.','abe.','');
			$dtf->GenitiveMonthNames = array('urtarrila','otsaila','martxoa','apirila','maiatza','ekaina','uztaila','abuztua','iraila','urria','azaroa','abendua','');
			$dtf->DayNames = array('igandea','astelehena','asteartea','asteazkena','osteguna','ostirala','larunbata');
			$dtf->ShortDayNames = array('ig','al','as','az','og','or','lr');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('ES');
			$ci->Parent = internal_getLanguage('eu');
			return $ci;
		case 'mk-mk':
			$ci = new CultureInfo('mk-MK','mk','mk','Macedonian (Former Yugoslav Republic of Macedonia)','Македонски јазик (Македонија)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('јануари','февруари','март','април','мај','јуни','јули','август','септември','октомври','ноември','декември','');
			$dtf->ShortMonthNames = array('јан','фев','мар','апр','мај','јун','јул','авг','сеп','окт','ное','дек','');
			$dtf->GenitiveMonthNames = array('јануари','февруари','март','април','мај','јуни','јули','август','септември','октомври','ноември','декември','');
			$dtf->DayNames = array('недела','понеделник','вторник','среда','четврток','петок','сабота');
			$dtf->ShortDayNames = array('не','по','вт','ср','че','пе','са');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','ден.','MKD','%v ден.','-%v ден.','Macedonian Denar','денар');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('MK');
			$ci->Parent = internal_getLanguage('mk');
			return $ci;
		case 'af-za':
			$ci = new CultureInfo('af-ZA','af','af','Afrikaans (South Africa)','Afrikaans (Suid Afrika)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januarie','Februarie','Maart','April','Mei','Junie','Julie','Augustus','September','Oktober','November','Desember','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Des','');
			$dtf->GenitiveMonthNames = array('Januarie','Februarie','Maart','April','Mei','Junie','Julie','Augustus','September','Oktober','November','Desember','');
			$dtf->DayNames = array('Sondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrydag','Saterdag');
			$dtf->ShortDayNames = array('So','Ma','Di','Wo','Do','Vr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','R','ZAR','R %v','R-%v','South African Rand','Rand');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('ZA');
			$ci->Parent = internal_getLanguage('af');
			return $ci;
		case 'ka-ge':
			$ci = new CultureInfo('ka-GE','ka','ka','Georgian (Georgia)','ქართული (საქართველო)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('იანვარი','თებერვალი','მარტი','აპრილი','მაისი','ივნისი','ივლისი','აგვისტო','სექტემბერი','ოქტომბერი','ნოემბერი','დეკემბერი','');
			$dtf->ShortMonthNames = array('იან','თებ','მარ','აპრ','მაის','ივნ','ივლ','აგვ','სექ','ოქტ','ნოემ','დეკ','');
			$dtf->GenitiveMonthNames = array('იანვარი','თებერვალი','მარტი','აპრილი','მაისი','ივნისი','ივლისი','აგვისტო','სექტემბერი','ოქტომბერი','ნოემბერი','დეკემბერი','');
			$dtf->DayNames = array('კვირა','ორშაბათი','სამშაბათი','ოთხშაბათი','ხუთშაბათი','პარასკევი','შაბათი');
			$dtf->ShortDayNames = array('კ','ო','ს','ო','ხ','პ','შ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','Lari','GEL','%v Lari','-%v Lari','Lari','ლარი');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('GE');
			$ci->Parent = internal_getLanguage('ka');
			return $ci;
		case 'fo-fo':
			$ci = new CultureInfo('fo-FO','fo','fo','Faroese (Faroe Islands)','Føroyskt (Føroyar)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','mars','apríl','mai','juni','juli','august','september','oktober','november','desember','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','mai','jun','jul','aug','sep','okt','nov','des','');
			$dtf->GenitiveMonthNames = array('januar','februar','mars','apríl','mai','juni','juli','august','september','oktober','november','desember','');
			$dtf->DayNames = array('sunnudagur','mánadagur','týsdagur','mikudagur','hósdagur','fríggjadagur','leygardagur');
			$dtf->ShortDayNames = array('su','má','tý','mi','hó','fr','ley');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','kr','DKK','kr %v','kr -%v','Danish Krone','Dansk krone');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('FO');
			$ci->Parent = internal_getLanguage('fo');
			return $ci;
		case 'hi-in':
			$ci = new CultureInfo('hi-IN','hi','hi','Hindi (India)','हिंदी (भारत)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितम्बर','अक्तूबर','नवम्बर','दिसम्बर','');
			$dtf->ShortMonthNames = array('जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितम्बर','अक्तूबर','नवम्बर','दिसम्बर','');
			$dtf->GenitiveMonthNames = array('जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितम्बर','अक्तूबर','नवम्बर','दिसम्बर','');
			$dtf->DayNames = array('रविवार','सोमवार','मंगलवार','बुधवार','गुरुवार','शुक्रवार','शनिवार');
			$dtf->ShortDayNames = array('र','स','म','ब','ग','श','श');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','रु','INR','रु %v','रु -%v','Indian Rupee','रुपया');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('hi');
			return $ci;
		case 'ms-my':
			$ci = new CultureInfo('ms-MY','ms','ms','Malay (Malaysia)','Bahasa Malaysia (Malaysia)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mac','Apr','Mei','Jun','Jul','Ogos','Sept','Okt','Nov','Dis','');
			$dtf->GenitiveMonthNames = array('Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember','');
			$dtf->DayNames = array('Ahad','Isnin','Selasa','Rabu','Khamis','Jumaat','Sabtu');
			$dtf->ShortDayNames = array('A','I','S','R','K','J','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('0',',','.','R','MYR','R%v','(R%v)','Malaysian Ringgit','Ringgit Malaysia');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%v %','-%v%');
			$ci->Region = internal_getRegion('MY');
			$ci->Parent = internal_getLanguage('ms');
			return $ci;
		case 'kk-kz':
			$ci = new CultureInfo('kk-KZ','kk','kk','Kazakh (Kazakhstan)','Қазақ (Қазақстан)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('қаңтар','ақпан','наурыз','сәуір','мамыр','маусым','шілде','тамыз','қыркүйек','қазан','қараша','желтоқсан','');
			$dtf->ShortMonthNames = array('Қаң','Ақп','Нау','Сәу','Мам','Мау','Шіл','Там','Қыр','Қаз','Қар','Жел','');
			$dtf->GenitiveMonthNames = array('қаңтар','ақпан','наурыз','сәуір','мамыр','маусым','шілде','тамыз','қыркүйек','қазан','қараша','желтоқсан','');
			$dtf->DayNames = array('Жексенбі','Дүйсенбі','Сейсенбі','Сәрсенбі','Бейсенбі','Жұма','Сенбі');
			$dtf->ShortDayNames = array('Жк','Дс','Сс','Ср','Бс','Жм','Сн');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','-',' ','Т','KZT','Т%v','-Т%v','Tenge','Т');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%v %','-%v%');
			$ci->Region = internal_getRegion('KZ');
			$ci->Parent = internal_getLanguage('kk');
			return $ci;
		case 'ky-kg':
			$ci = new CultureInfo('ky-KG','ky','ky','Kyrgyz (Kyrgyzstan)','Кыргыз (Кыргызстан)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь','');
			$dtf->ShortMonthNames = array('Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек','');
			$dtf->GenitiveMonthNames = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь','');
			$dtf->DayNames = array('Жекшемби','Дүйшөмбү','Шейшемби','Шаршемби','Бейшемби','Жума','Ишемби');
			$dtf->ShortDayNames = array('Жш','Дш','Шш','Шр','Бш','Жм','Иш');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','-',' ','сом','KGS','%v сом','-%v сом','som','сом');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('KG');
			$ci->Parent = internal_getLanguage('ky');
			return $ci;
		case 'sw-ke':
			$ci = new CultureInfo('sw-KE','sw','sw','Kiswahili (Kenya)','Kiswahili (Kenya)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('S','M','T','W','T','F','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','S','KES','S%v','(S%v)','Kenyan Shilling','Shilingi');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('KE');
			$ci->Parent = internal_getLanguage('sw');
			return $ci;
		case 'uz-latn-uz':
			$ci = new CultureInfo('uz-Latn-UZ','uz','uz','Uzbek (Latin, Uzbekistan)','U\'zbek (U\'zbekiston Respublikasi)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('yanvar','fevral','mart','aprel','may','iyun','iyul','avgust','sentyabr','oktyabr','noyabr','dekabr','');
			$dtf->ShortMonthNames = array('yanvar','fevral','mart','aprel','may','iyun','iyul','avgust','sentyabr','oktyabr','noyabr','dekabr','');
			$dtf->GenitiveMonthNames = array('yanvar','fevral','mart','aprel','may','iyun','iyul','avgust','sentyabr','oktyabr','noyabr','dekabr','');
			$dtf->DayNames = array('yakshanba','dushanba','seshanba','chorshanba','payshanba','juma','shanba');
			$dtf->ShortDayNames = array('yak','dsh','sesh','chr','psh','jm','sh');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('0',',',' ','su\'m','UZS','%v su\'m','-%v su\'m','Uzbekistan Sum','рубль');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('UZ');
			$ci->Parent = internal_getLanguage('uz');
			return $ci;
		case 'tt-ru':
			$ci = new CultureInfo('tt-RU','tt','tt','Tatar (Russia)','Татар (Россия)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Гыйнварь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь','');
			$dtf->ShortMonthNames = array('Гыйнв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек','');
			$dtf->GenitiveMonthNames = array('гыйнварь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь','');
			$dtf->DayNames = array('Якшәмбе','Дүшәмбе','Сишәмбе','Чәршәмбе','Пәнҗешәмбе','Җомга','Шимбә');
			$dtf->ShortDayNames = array('Якш','Дүш','Сиш','Чәрш','Пәнҗ','Җом','Шим');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','р.','RUR','%v р.','-%v р.','Russian Ruble','рубль');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('RU');
			$ci->Parent = internal_getLanguage('tt');
			return $ci;
		case 'pa-in':
			$ci = new CultureInfo('pa-IN','pa','pa','Punjabi (India)','ਪੰਜਾਬੀ (ਭਾਰਤ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ਜਨਵਰੀ','ਫ਼ਰਵਰੀ','ਮਾਰਚ','ਅਪ੍ਰੈਲ','ਮਈ','ਜੂਨ','ਜੁਲਾਈ','ਅਗਸਤ','ਸਤੰਬਰ','ਅਕਤੂਬਰ','ਨਵੰਬਰ','ਦਸੰਬਰ','');
			$dtf->ShortMonthNames = array('ਜਨਵਰੀ','ਫ਼ਰਵਰੀ','ਮਾਰਚ','ਅਪ੍ਰੈਲ','ਮਈ','ਜੂਨ','ਜੁਲਾਈ','ਅਗਸਤ','ਸਤੰਬਰ','ਅਕਤੂਬਰ','ਨਵੰਬਰ','ਦਸੰਬਰ','');
			$dtf->GenitiveMonthNames = array('ਜਨਵਰੀ','ਫ਼ਰਵਰੀ','ਮਾਰਚ','ਅਪ੍ਰੈਲ','ਮਈ','ਜੂਨ','ਜੁਲਾਈ','ਅਗਸਤ','ਸਤੰਬਰ','ਅਕਤੂਬਰ','ਨਵੰਬਰ','ਦਸੰਬਰ','');
			$dtf->DayNames = array('ਐਤਵਾਰ','ਸੋਮਵਾਰ','ਮੰਗਲਵਾਰ','ਬੁਧਵਾਰ','ਵੀਰਵਾਰ','ਸ਼ੁੱਕਰਵਾਰ','ਸ਼ਨੀਚਰਵਾਰ');
			$dtf->ShortDayNames = array('ਐ','ਸ','ਮ','ਬ','ਵ','ਸ਼','ਸ਼');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ਰੁ','INR','ਰੁ %v','ਰੁ -%v','Indian Rupee','ਰੁਪਿਆ');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('pa');
			return $ci;
		case 'gu-in':
			$ci = new CultureInfo('gu-IN','gu','gu','Gujarati (India)','ગુજરાતી (ભારત)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('જાન્યુઆરી','ફેબ્રુઆરી','માર્ચ','એપ્રિલ','મે','જૂન','જુલાઈ','ઑગસ્ટ','સપ્ટેમ્બર','ઑક્ટ્બર','નવેમ્બર','ડિસેમ્બર','');
			$dtf->ShortMonthNames = array('જાન્યુ','ફેબ્રુ','માર્ચ','એપ્રિલ','મે','જૂન','જુલાઈ','ઑગસ્ટ','સપ્ટે','ઑક્ટો','નવે','ડિસે','');
			$dtf->GenitiveMonthNames = array('જાન્યુઆરી','ફેબ્રુઆરી','માર્ચ','એપ્રિલ','મે','જૂન','જુલાઈ','ઑગસ્ટ','સપ્ટેમ્બર','ઑક્ટ્બર','નવેમ્બર','ડિસેમ્બર','');
			$dtf->DayNames = array('રવિવાર','સોમવાર','મંગળવાર','બુધવાર','ગુરુવાર','શુક્રવાર','શનિવાર');
			$dtf->ShortDayNames = array('ર','સ','મ','બ','ગ','શ','શ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','રૂ','INR','રૂ %v','રૂ -%v','Indian Rupee','રૂપિયો');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('gu');
			return $ci;
		case 'ta-in':
			$ci = new CultureInfo('ta-IN','ta','ta','Tamil (India)','தமிழ் (இந்தியா)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ஜனவரி','பெப்ரவரி','மார்ச்','ஏப்ரல்','மே','ஜூன்','ஜூலை','ஆகஸ்ட்','செப்டம்பர்','அக்டோபர்','நவம்பர்','டிசம்பர்','');
			$dtf->ShortMonthNames = array('ஜன.','பெப்.','மார்.','ஏப்.','மே','ஜூன்','ஜூலை','ஆக.','செப்.','அக்.','நவ.','டிச.','');
			$dtf->GenitiveMonthNames = array('ஜனவரி','பெப்ரவரி','மார்ச்','ஏப்ரல்','மே','ஜூன்','ஜூலை','ஆகஸ்ட்','செப்டம்பர்','அக்டோபர்','நவம்பர்','டிசம்பர்','');
			$dtf->DayNames = array('ஞாயிறு','திங்கள்','செவ்வாய்','புதன்','வியாழன்','வெள்ளி','சனி');
			$dtf->ShortDayNames = array('ஞ','த','ச','ப','வ','வ','ச');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ரூ','INR','ரூ %v','ரூ -%v','Indian Rupee','ரூபாய்');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('ta');
			return $ci;
		case 'te-in':
			$ci = new CultureInfo('te-IN','te','te','Telugu (India)','తెలుగు (భారత దేశం)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('జనవరి','ఫిబ్రవరి','మార్చి','ఏప్రిల్','మే','జూన్','జూలై','ఆగస్టు','సెప్టెంబర్','అక్టోబర్','నవంబర్','డిసెంబర్','');
			$dtf->ShortMonthNames = array('జనవరి','ఫిబ్రవరి','మార్చి','ఏప్రిల్','మే','జూన్','జూలై','ఆగస్టు','సెప్టెంబర్','అక్టోబర్','నవంబర్','డిసెంబర్','');
			$dtf->GenitiveMonthNames = array('జనవరి','ఫిబ్రవరి','మార్చి','ఏప్రిల్','మే','జూన్','జూలై','ఆగస్టు','సెప్టెంబర్','అక్టోబర్','నవంబర్','డిసెంబర్','');
			$dtf->DayNames = array('ఆదివారం','సోమవారం','మంగళవారం','బుధవారం','గురువారం','శుక్రవారం','శనివారం');
			$dtf->ShortDayNames = array('ఆ','స','మ','బ','గ','శ','శ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','రూ','INR','రూ %v','రూ -%v','Indian Rupee','రూపాయి');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('te');
			return $ci;
		case 'kn-in':
			$ci = new CultureInfo('kn-IN','kn','kn','Kannada (India)','ಕನ್ನಡ (ಭಾರತ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ಜನವರಿ','ಫೆಬ್ರವರಿ','ಮಾರ್ಚ್','ಎಪ್ರಿಲ್','ಮೇ','ಜೂನ್','ಜುಲೈ','ಆಗಸ್ಟ್','ಸೆಪ್ಟಂಬರ್','ಅಕ್ಟೋಬರ್','ನವೆಂಬರ್','ಡಿಸೆಂಬರ್','');
			$dtf->ShortMonthNames = array('ಜನವರಿ','ಫೆಬ್ರವರಿ','ಮಾರ್ಚ್','ಎಪ್ರಿಲ್','ಮೇ','ಜೂನ್','ಜುಲೈ','ಆಗಸ್ಟ್','ಸೆಪ್ಟಂಬರ್','ಅಕ್ಟೋಬರ್','ನವೆಂಬರ್','ಡಿಸೆಂಬರ್','');
			$dtf->GenitiveMonthNames = array('ಜನವರಿ','ಫೆಬ್ರವರಿ','ಮಾರ್ಚ್','ಎಪ್ರಿಲ್','ಮೇ','ಜೂನ್','ಜುಲೈ','ಆಗಸ್ಟ್','ಸೆಪ್ಟಂಬರ್','ಅಕ್ಟೋಬರ್','ನವೆಂಬರ್','ಡಿಸೆಂಬರ್','');
			$dtf->DayNames = array('ಭಾನುವಾರ','ಸೋಮವಾರ','ಮಂಗಳವಾರ','ಬುಧವಾರ','ಗುರುವಾರ','ಶುಕ್ರವಾರ','ಶನಿವಾರ');
			$dtf->ShortDayNames = array('ರ','ಸ','ಮ','ಬ','ಗ','ಶ','ಶ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ರೂ','INR','ರೂ %v','ರೂ -%v','Indian Rupee','ರೂಪಾಯಿ');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('kn');
			return $ci;
		case 'mr-in':
			$ci = new CultureInfo('mr-IN','mr','mr','Marathi (India)','मराठी (भारत)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('जानेवारी','फेब्रुवारी','मार्च','एप्रिल','मे','जून','जुलै','ऑगस्ट','सप्टेंबर','ऑक्टोबर','नोव्हेंबर','डिसेंबर','');
			$dtf->ShortMonthNames = array('जाने.','फेब्रु.','मार्च','एप्रिल','मे','जून','जुलै','ऑगस्ट','सप्टें.','ऑक्टो.','नोव्हें.','डिसें.','');
			$dtf->GenitiveMonthNames = array('जानेवारी','फेब्रुवारी','मार्च','एप्रिल','मे','जून','जुलै','ऑगस्ट','सप्टेंबर','ऑक्टोबर','नोव्हेंबर','डिसेंबर','');
			$dtf->DayNames = array('रविवार','सोमवार','मंगळवार','बुधवार','गुरुवार','शुक्रवार','शनिवार');
			$dtf->ShortDayNames = array('र','स','म','ब','ग','श','श');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','रु','INR','रु %v','रु -%v','Indian Rupee','रुपया');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('mr');
			return $ci;
		case 'sa-in':
			$ci = new CultureInfo('sa-IN','sa','sa','Sanskrit (India)','संस्कृत (भारतम्)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितम्बर','अक्तूबर','नवम्बर','दिसम्बर','');
			$dtf->ShortMonthNames = array('जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितम्बर','अक्तूबर','नवम्बर','दिसम्बर','');
			$dtf->GenitiveMonthNames = array('जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितम्बर','अक्तूबर','नवम्बर','दिसम्बर','');
			$dtf->DayNames = array('रविवासरः','सोमवासरः','मङ्गलवासरः','बुधवासरः','गुरुवासरः','शुक्रवासरः','शनिवासरः');
			$dtf->ShortDayNames = array('र','स','म','ब','ग','श','श');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','रु','INR','रु %v','रु -%v','Indian Rupee','रु्यकम्');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('sa');
			return $ci;
		case 'mn-mn':
			$ci = new CultureInfo('mn-MN','mn','mn','Mongolian (Cyrillic, Mongolia)','Монгол хэл (Монгол улс)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('1 дүгээр сар','2 дугаар сар','3 дугаар сар','4 дүгээр сар','5 дугаар сар','6 дугаар сар','7 дугаар сар','8 дугаар сар','9 дүгээр сар','10 дугаар сар','11 дүгээр сар','12 дугаар сар','');
			$dtf->ShortMonthNames = array('I','II','III','IV','V','VI','VII','VШ','IX','X','XI','XII','');
			$dtf->GenitiveMonthNames = array('1 дүгээр сарын','2 дугаар сарын','3 дугаар сарын','4 дүгээр сарын','5 дугаар сарын','6 дугаар сарын','7 дугаар сарын','8 дугаар сарын','9 дүгээр сарын','10 дугаар сарын','11 дүгээр сарын','12 дугаар сарын','');
			$dtf->DayNames = array('Ням','Даваа','Мягмар','Лхагва','Пүрэв','Баасан','Бямба');
			$dtf->ShortDayNames = array('Ня','Да','Мя','Лх','Пү','Ба','Бя');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','₮','MNT','%v₮','-%v₮','Tugrik','Төгрөг');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%v%','-%v%');
			$ci->Region = internal_getRegion('MN');
			$ci->Parent = internal_getLanguage('mn');
			return $ci;
		case 'gl-es':
			$ci = new CultureInfo('gl-ES','gl','gl','Galician (Galician)','Galego (Galego)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('xaneiro','febreiro','marzo','abril','maio','xuño','xullo','agosto','setembro','outubro','novembro','decembro','');
			$dtf->ShortMonthNames = array('xan','feb','mar','abr','maio','xuñ','xull','ago','set','out','nov','dec','');
			$dtf->GenitiveMonthNames = array('xaneiro','febreiro','marzo','abril','maio','xuño','xullo','agosto','setembro','outubro','novembro','decembro','');
			$dtf->DayNames = array('domingo','luns','martes','mércores','xoves','venres','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mé','xo','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('ES');
			$ci->Parent = internal_getLanguage('gl');
			return $ci;
		case 'kok-in':
			$ci = new CultureInfo('kok-IN','kok','kok','Konkani (India)','कोंकणी (भारत)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('जानेवारी','फेब्रुवारी','मार्च','एप्रिल','मे','जून','जुलै','ऑगस्ट','सप्टेंबर','ऑक्टोबर','नोवेम्बर','डिसेंबर','');
			$dtf->ShortMonthNames = array('जानेवारी','फेब्रुवारी','मार्च','एप्रिल','मे','जून','जुलै','ऑगस्ट','सप्टेंबर','ऑक्टोबर','नोवेम्बर','डिसेंबर','');
			$dtf->GenitiveMonthNames = array('जानेवारी','फेब्रुवारी','मार्च','एप्रिल','मे','जून','जुलै','ऑगस्ट','सप्टेंबर','ऑक्टोबर','नोवेम्बर','डिसेंबर','');
			$dtf->DayNames = array('आयतार','सोमार','मंगळार','बुधवार','बिरेस्तार','सुक्रार','शेनवार');
			$dtf->ShortDayNames = array('आ','स','म','ब','ब','स','श');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','रु','INR','रु %v','रु -%v','Indian Rupee','रुपय');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('kok');
			return $ci;
		case 'syr-sy':
			$ci = new CultureInfo('syr-SY','syr','syr','Syriac (Syria)','ܣܘܪܝܝܐ (سوريا)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ܟܢܘܢ ܐܚܪܝ','ܫܒܛ','ܐܕܪ','ܢܝܣܢ','ܐܝܪ','ܚܙܝܪܢ','ܬܡܘܙ','ܐܒ','ܐܝܠܘܠ','ܬܫܪܝ ܩܕܝܡ','ܬܫܪܝ ܐܚܪܝ','ܟܢܘܢ ܩܕܝܡ','');
			$dtf->ShortMonthNames = array('܏ܟܢ ܏ܒ','ܫܒܛ','ܐܕܪ','ܢܝܣܢ','ܐܝܪ','ܚܙܝܪܢ','ܬܡܘܙ','ܐܒ','ܐܝܠܘܠ','܏ܬܫ ܏ܐ','܏ܬܫ ܏ܒ','܏ܟܢ ܏ܐ','');
			$dtf->GenitiveMonthNames = array('ܟܢܘܢ ܐܚܪܝ','ܫܒܛ','ܐܕܪ','ܢܝܣܢ','ܐܝܪ','ܚܙܝܪܢ','ܬܡܘܙ','ܐܒ','ܐܝܠܘܠ','ܬܫܪܝ ܩܕܝܡ','ܬܫܪܝ ܐܚܪܝ','ܟܢܘܢ ܩܕܝܡ','');
			$dtf->DayNames = array('ܚܕ ܒܫܒܐ','ܬܪܝܢ ܒܫܒܐ','ܬܠܬܐ ܒܫܒܐ','ܐܪܒܥܐ ܒܫܒܐ','ܚܡܫܐ ܒܫܒܐ','ܥܪܘܒܬܐ','ܫܒܬܐ');
			$dtf->ShortDayNames = array('܏','܏','܏','܏','܏','܏','܏');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ل.س.‏','SYP','ل.س.‏ %v','ل.س.‏%v-','Syrian Pound','جنيه سوري');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('SY');
			$ci->Parent = internal_getLanguage('syr');
			return $ci;
		case 'dv-mv':
			$ci = new CultureInfo('dv-MV','dv','dv','Divehi (Maldives)','ދިވެހިބަސް (ދިވެހި ރާއްޖެ)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('محرم','صفر','ربيع الأول','ربيع الثاني','جمادى الأولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->ShortMonthNames = array('محرم','صفر','ربيع الاول','ربيع الثاني','جمادى الاولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->GenitiveMonthNames = array('محرم','صفر','ربيع الأول','ربيع الثاني','جمادى الأولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('ح','ن','ث','ر','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ރ.','MVR','%v ރ.','%v ރ.-','Rufiyaa','ރުފިޔާ');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','% %v','-%v%');
			$ci->Region = internal_getRegion('MV');
			$ci->Parent = internal_getLanguage('dv');
			return $ci;
		case 'ar-iq':
			$ci = new CultureInfo('ar-IQ','ar','ar','Arabic (Iraq)','العربية (العراق)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->ShortMonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->GenitiveMonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','د.ع.‏','IQD','د.ع.‏ %v','د.ع.‏%v-','Iraqi Dinar','دينار عراقي');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('IQ');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'zh-cn':
			$ci = new CultureInfo('zh-CN','zh-CHS','zh','Chinese (People\'s Republic of China)','中文(中华人民共和国)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->ShortMonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->GenitiveMonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->DayNames = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
			$dtf->ShortDayNames = array('日','一','二','三','四','五','六');
			$ci->DateTimeFormat = $dtf;
//			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','￥','CNY','￥%v','￥-%v','PRC Yuan Renminbi','人民币');
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','¥','CNY','¥%v','¥-%v','PRC Yuan Renminbi','人民币');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('CN');
			$ci->Parent = internal_getLanguage('zh-CHS');
			return $ci;
		case 'de-ch':
			$ci = new CultureInfo('de-CH','de','de','German (Switzerland)','Deutsch (Schweiz)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mrz','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez','');
			$dtf->GenitiveMonthNames = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->DayNames = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
			$dtf->ShortDayNames = array('So','Mo','Di','Mi','Do','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.','\'','SFr.','CHF','SFr. %v','SFr.-%v','Swiss Franc','Schweizer Franken');
			$ci->NumberFormat = new NumberFormat('2','.','\'','-%v');
			$ci->PercentFormat = new PercentFormat('2','.','\'','%%v','-%v%');
			$ci->Region = internal_getRegion('CH');
			$ci->Parent = internal_getLanguage('de');
			return $ci;
		case 'en-gb':
			$ci = new CultureInfo('en-GB','en','en','English (United Kingdom)','English (United Kingdom)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','£','GBP','£%v','-£%v','UK Pound Sterling','Pound Sterling');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('GB');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-mx':
			$ci = new CultureInfo('es-MX','es','es','Spanish (Mexico)','Español (México)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','MXN','$%v','-$%v','Mexican Peso','Peso');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('MX');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'fr-be':
			$ci = new CultureInfo('fr-BE','fr','fr','French (Belgium)','Français (Belgique)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->ShortMonthNames = array('janv.','févr.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.','');
			$dtf->GenitiveMonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->DayNames = array('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi');
			$dtf->ShortDayNames = array('di','lu','ma','me','je','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','€ %v','€ -%v','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('BE');
			$ci->Parent = internal_getLanguage('fr');
			return $ci;
		case 'it-ch':
			$ci = new CultureInfo('it-CH','it','it','Italian (Switzerland)','Italiano (Svizzera)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('gennaio','febbraio','marzo','aprile','maggio','giugno','luglio','agosto','settembre','ottobre','novembre','dicembre','');
			$dtf->ShortMonthNames = array('gen','feb','mar','apr','mag','gio','lug','ago','set','ott','nov','dic','');
			$dtf->GenitiveMonthNames = array('gennaio','febbraio','marzo','aprile','maggio','giugno','luglio','agosto','settembre','ottobre','novembre','dicembre','');
			$dtf->DayNames = array('domenica','lunedì','martedì','mercoledì','giovedì','venerdì','sabato');
			$dtf->ShortDayNames = array('do','lu','ma','me','gi','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.','\'','SFr.','CHF','SFr. %v','SFr.-%v','Swiss Franc','Franco svizzero');
			$ci->NumberFormat = new NumberFormat('2','.','\'','-%v');
			$ci->PercentFormat = new PercentFormat('2','.','\'','%%v','-%v%');
			$ci->Region = internal_getRegion('CH');
			$ci->Parent = internal_getLanguage('it');
			return $ci;
		case 'nl-be':
			$ci = new CultureInfo('nl-BE','nl','nl','Dutch (Belgium)','Nederlands (België)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december','');
			$dtf->ShortMonthNames = array('jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december','');
			$dtf->DayNames = array('zondag','maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag');
			$dtf->ShortDayNames = array('zo','ma','di','wo','do','vr','za');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','€ %v','€ -%v','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('BE');
			$ci->Parent = internal_getLanguage('nl');
			return $ci;
		case 'nn-no':
			$ci = new CultureInfo('nn-NO','no','nn','Norwegian, Nynorsk (Norway)','Norsk, Nynorsk (Noreg)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','mars','april','mai','juni','juli','august','september','oktober','november','desember','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','mai','jun','jul','aug','sep','okt','nov','des','');
			$dtf->GenitiveMonthNames = array('januar','februar','mars','april','mai','juni','juli','august','september','oktober','november','desember','');
			$dtf->DayNames = array('søndag','måndag','tysdag','onsdag','torsdag','fredag','laurdag');
			$dtf->ShortDayNames = array('sø','må','ty','on','to','fr','la');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','kr','NOK','kr %v','kr -%v','Norwegian Krone','Norsk krone');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%%v','-%v%');
			$ci->Region = internal_getRegion('NO');
			$ci->Parent = internal_getLanguage('no');
			return $ci;
		case 'pt-pt':
			$ci = new CultureInfo('pt-PT','pt','pt','Portuguese (Portugal)','Português (Portugal)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','');
			$dtf->ShortMonthNames = array('Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez','');
			$dtf->GenitiveMonthNames = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','');
			$dtf->DayNames = array('domingo','segunda-feira','terça-feira','quarta-feira','quinta-feira','sexta-feira','sábado');
			$dtf->ShortDayNames = array('dom','seg','ter','qua','qui','sex','sáb');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('PT');
			$ci->Parent = internal_getLanguage('pt');
			return $ci;
		case 'sr-latn-cs':
			$ci = new CultureInfo('sr-Latn-CS','sr','sr','Serbian (Latin, Serbia and Montenegro (Former))','Srpski (Srbija i Crna Gora (Prethodno))','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','maj','jun','jul','avg','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->DayNames = array('nedelja','ponedeljak','utorak','sreda','četvrtak','petak','subota');
			$dtf->ShortDayNames = array('ne','po','ut','sr','če','pe','su');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','Din.','CSD','%v Din.','-%v Din.','Serbian Dinar','dinar');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('CS');
			$ci->Parent = internal_getLanguage('sr');
			return $ci;
		case 'sv-fi':
			$ci = new CultureInfo('sv-FI','sv','sv','Swedish (Finland)','Svenska (Finland)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','maj','jun','jul','aug','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december','');
			$dtf->DayNames = array('söndag','måndag','tisdag','onsdag','torsdag','fredag','lördag');
			$dtf->ShortDayNames = array('sö','må','ti','on','to','fr','lö');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FI');
			$ci->Parent = internal_getLanguage('sv');
			return $ci;
		case 'az-cyrl-az':
			$ci = new CultureInfo('az-Cyrl-AZ','az','az','Azeri (Cyrillic, Azerbaijan)','Азәрбајҹан (Азәрбајҹан)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Јанвар','Феврал','Март','Апрел','Мај','Ијун','Ијул','Август','Сентјабр','Октјабр','Нојабр','Декабр','');
			$dtf->ShortMonthNames = array('Јан','Фев','Мар','Апр','Мај','Ијун','Ијул','Авг','Сен','Окт','Ноя','Дек','');
			$dtf->GenitiveMonthNames = array('јанвар','феврал','март','апрел','мај','ијун','ијул','август','сентјабр','октјабр','нојабр','декабр','');
			$dtf->DayNames = array('Базар','Базар ертәси','Чәршәнбә ахшамы','Чәршәнбә','Ҹүмә ахшамы','Ҹүмә','Шәнбә');
			$dtf->ShortDayNames = array('Б','Бе','Ча','Ч','Ҹа','Ҹ','Ш');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','ман.','AZM','%v ман.','-%v ман.','Azerbaijanian Manat','рубль');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('AZ');
			$ci->Parent = internal_getLanguage('az');
			return $ci;
		case 'ms-bn':
			$ci = new CultureInfo('ms-BN','ms','ms','Malay (Brunei Darussalam)','Bahasa Malaysia (Brunei Darussalam)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mac','Apr','Mei','Jun','Jul','Ogos','Sept','Okt','Nov','Dis','');
			$dtf->GenitiveMonthNames = array('Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember','');
			$dtf->DayNames = array('Ahad','Isnin','Selasa','Rabu','Khamis','Jumaat','Sabtu');
			$dtf->ShortDayNames = array('A','I','S','R','K','J','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('0',',','.','$','BND','$%v','-$%v','Brunei Dollar','Ringgit Brunei');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%v %','-%v%');
			$ci->Region = internal_getRegion('BN');
			$ci->Parent = internal_getLanguage('ms');
			return $ci;
		case 'uz-cyrl-uz':
			$ci = new CultureInfo('uz-Cyrl-UZ','uz','uz','Uzbek (Cyrillic, Uzbekistan)','Ўзбек (Ўзбекистон)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Январ','Феврал','Март','Апрел','Май','Июн','Июл','Август','Сентябр','Октябр','Ноябр','Декабр','');
			$dtf->ShortMonthNames = array('Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек','');
			$dtf->GenitiveMonthNames = array('январ','феврал','март','апрел','май','июн','июл','август','сентябр','октябр','ноябр','декабр','');
			$dtf->DayNames = array('якшанба','душанба','сешанба','чоршанба','пайшанба','жума','шанба');
			$dtf->ShortDayNames = array('якш','дш','сш','чш','пш','ж','ш');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','сўм','UZS','%v сўм','-%v сўм','Uzbekistan Sum','рубль');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('UZ');
			$ci->Parent = internal_getLanguage('uz');
			return $ci;
		case 'ar-eg':
			$ci = new CultureInfo('ar-EG','ar','ar','Arabic (Egypt)','العربية (مصر)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('3','.',',','ج.م.‏','EGP','ج.م.‏ %v','ج.م.‏%v-','Egyptian Pound','جنيه مصري');
			$ci->NumberFormat = new NumberFormat('3','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('3','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('EG');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'zh-hk':
			$ci = new CultureInfo('zh-HK','zh-CHT','zh','Chinese (Hong Kong S.A.R.)','中文(香港特别行政區)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','HK$','HKD','HK$%v','(HK$%v)','Hong Kong Dollar','港幣');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('HK');
			$ci->Parent = internal_getLanguage('zh-CHT');
			return $ci;
		case 'de-at':
			$ci = new CultureInfo('de-AT','de','de','German (Austria)','Deutsch (Österreich)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Jänner','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->ShortMonthNames = array('Jän','Feb','Mär','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez','');
			$dtf->GenitiveMonthNames = array('Jänner','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->DayNames = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
			$dtf->ShortDayNames = array('So','Mo','Di','Mi','Do','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','€ %v','-€ %v','Euro','Euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('AT');
			$ci->Parent = internal_getLanguage('de');
			return $ci;
		case 'en-au':
			$ci = new CultureInfo('en-AU','en','en','English (Australia)','English (Australia)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','AUD','$%v','-$%v','Australian Dollar','Australian Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('AU');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-es':
			$ci = new CultureInfo('es-ES','es','es','Spanish (Spain)','Español (España)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('ES');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'fr-ca':
			$ci = new CultureInfo('fr-CA','fr','fr','French (Canada)','Français (Canada)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->ShortMonthNames = array('janv.','févr.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.','');
			$dtf->GenitiveMonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->DayNames = array('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi');
			$dtf->ShortDayNames = array('di','lu','ma','me','je','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','$','CAD','%v $','(%v $)','Canadian Dollar','Dollar canadien');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('CA');
			$ci->Parent = internal_getLanguage('fr');
			return $ci;
		case 'sr-cyrl-cs':
			$ci = new CultureInfo('sr-Cyrl-CS','sr','sr','Serbian (Cyrillic, Serbia and Montenegro (Former))','Српски (Србија и Црна Гора (Претходно))','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->ShortMonthNames = array('јан','феб','мар','апр','мај','јун','јул','авг','сеп','окт','нов','дец','');
			$dtf->GenitiveMonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->DayNames = array('недеља','понедељак','уторак','среда','четвртак','петак','субота');
			$dtf->ShortDayNames = array('не','по','ут','ср','че','пе','су');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','Дин.','CSD','%v Дин.','-%v Дин.','Serbian Dinar','динар');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('CS');
			$ci->Parent = internal_getLanguage('sr');
			return $ci;
		case 'ar-ly':
			$ci = new CultureInfo('ar-LY','ar','ar','Arabic (Libya)','العربية (ليبيا)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','د.ل.‏','LYD','د.ل.‏ %v','د.ل.‏%v-','Libyan Dinar','دينار ليبي');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('LY');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'zh-sg':
			$ci = new CultureInfo('zh-SG','zh-CHS','zh','Chinese (Singapore)','中文(新加坡)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->ShortMonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->GenitiveMonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->DayNames = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
			$dtf->ShortDayNames = array('日','一','二','三','四','五','六');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','SGD','$%v','-$%v','Singapore Dollar','新币');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('SG');
			$ci->Parent = internal_getLanguage('zh-CHS');
			return $ci;
		case 'de-lu':
			$ci = new CultureInfo('de-LU','de','de','German (Luxembourg)','Deutsch (Luxemburg)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mrz','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez','');
			$dtf->GenitiveMonthNames = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->DayNames = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
			$dtf->ShortDayNames = array('So','Mo','Di','Mi','Do','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','Euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('LU');
			$ci->Parent = internal_getLanguage('de');
			return $ci;
		case 'en-ca':
			$ci = new CultureInfo('en-CA','en','en','English (Canada)','English (Canada)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','CAD','$%v','-$%v','Canadian Dollar','Canadian Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('CA');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-gt':
			$ci = new CultureInfo('es-GT','es','es','Spanish (Guatemala)','Español (Guatemala)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','Q','GTQ','Q%v','(Q%v)','Guatemalan Quetzal','Quetzal');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('GT');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'fr-ch':
			$ci = new CultureInfo('fr-CH','fr','fr','French (Switzerland)','Français (Suisse)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->ShortMonthNames = array('janv.','févr.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.','');
			$dtf->GenitiveMonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->DayNames = array('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi');
			$dtf->ShortDayNames = array('di','lu','ma','me','je','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.','\'','SFr.','CHF','SFr. %v','SFr.-%v','Swiss Franc','Franc suisse');
			$ci->NumberFormat = new NumberFormat('2','.','\'','-%v');
			$ci->PercentFormat = new PercentFormat('2','.','\'','%%v','-%v%');
			$ci->Region = internal_getRegion('CH');
			$ci->Parent = internal_getLanguage('fr');
			return $ci;
		case 'ar-dz':
			$ci = new CultureInfo('ar-DZ','ar','ar','Arabic (Algeria)','العربية (الجزائر)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('جانفييه','فيفرييه','مارس','أفريل','مي','جوان','جوييه','أوت','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('جانفييه','فيفرييه','مارس','أفريل','مي','جوان','جوييه','أوت','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('جانفييه','فيفرييه','مارس','أفريل','مي','جوان','جوييه','أوت','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','د.ج.‏','DZD','د.ج.‏ %v','د.ج.‏%v-','Algerian Dinar','دينار جزائري');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('DZ');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'zh-mo':
			$ci = new CultureInfo('zh-MO','zh-CHT','zh','Chinese (Macao S.A.R.)','中文(澳門特别行政區)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->ShortMonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->GenitiveMonthNames = array('一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','');
			$dtf->DayNames = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
			$dtf->ShortDayNames = array('日','一','二','三','四','五','六');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','MOP','MOP','MOP%v','(MOP%v)','Macao Pataca','Pataca');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('MO');
			$ci->Parent = internal_getLanguage('zh-CHT');
			return $ci;
		case 'de-li':
			$ci = new CultureInfo('de-LI','de','de','German (Liechtenstein)','Deutsch (Liechtenstein)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mrz','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez','');
			$dtf->GenitiveMonthNames = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->DayNames = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
			$dtf->ShortDayNames = array('So','Mo','Di','Mi','Do','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.','\'','CHF','CHF','CHF %v','CHF-%v','Swiss Franc','Schweizer Franken');
			$ci->NumberFormat = new NumberFormat('2','.','\'','-%v');
			$ci->PercentFormat = new PercentFormat('2','.','\'','%%v','-%v%');
			$ci->Region = internal_getRegion('LI');
			$ci->Parent = internal_getLanguage('de');
			return $ci;
		case 'en-nz':
			$ci = new CultureInfo('en-NZ','en','en','English (New Zealand)','English (New Zealand)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','NZD','$%v','-$%v','New Zealand Dollar','New Zealand Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('NZ');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-cr':
			$ci = new CultureInfo('es-CR','es','es','Spanish (Costa Rica)','Español (Costa Rica)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','₡','CRC','₡%v','(₡%v)','Costa Rican Colon','Colón');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%v %','-%v%');
			$ci->Region = internal_getRegion('CR');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'fr-lu':
			$ci = new CultureInfo('fr-LU','fr','fr','French (Luxembourg)','Français (Luxembourg)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->ShortMonthNames = array('janv.','févr.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.','');
			$dtf->GenitiveMonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->DayNames = array('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi');
			$dtf->ShortDayNames = array('di','lu','ma','me','je','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('LU');
			$ci->Parent = internal_getLanguage('fr');
			return $ci;
		case 'ar-ma':
			$ci = new CultureInfo('ar-MA','ar','ar','Arabic (Morocco)','العربية (المملكة المغربية)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('يناير','فبراير','مارس','ابريل','ماي','يونيو','يوليوز','غشت','شتنبر','اكتوبر','نونبر','دجنبر','');
			$dtf->ShortMonthNames = array('يناير','فبراير','مارس','ابريل','ماي','يونيو','يوليوز','غشت','شتنبر','اكتوبر','نونبر','دجنبر','');
			$dtf->GenitiveMonthNames = array('يناير','فبراير','مارس','ابريل','ماي','يونيو','يوليوز','غشت','شتنبر','اكتوبر','نونبر','دجنبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','د.م.‏','MAD','د.م.‏ %v','د.م.‏%v-','Moroccan Dirham','درهم مغربي');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('MA');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'en-ie':
			$ci = new CultureInfo('en-IE','en','en','English (Ireland)','English (Eire)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','€','EUR','€%v','-€%v','Euro','Euro');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('IE');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-pa':
			$ci = new CultureInfo('es-PA','es','es','Spanish (Panama)','Español (Panamá)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','B/.','PAB','B/. %v','(B/. %v)','Panamanian Balboa','Balboa');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('PA');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'fr-mc':
			$ci = new CultureInfo('fr-MC','fr','fr','French (Principality of Monaco)','Français (Principauté de Monaco)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->ShortMonthNames = array('janv.','févr.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.','');
			$dtf->GenitiveMonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->DayNames = array('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi');
			$dtf->ShortDayNames = array('di','lu','ma','me','je','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('MC');
			$ci->Parent = internal_getLanguage('fr');
			return $ci;
		case 'ar-tn':
			$ci = new CultureInfo('ar-TN','ar','ar','Arabic (Tunisia)','العربية (تونس)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('جانفي','فيفري','مارس','افريل','ماي','جوان','جويلية','اوت','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('جانفي','فيفري','مارس','افريل','ماي','جوان','جويلية','اوت','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('جانفي','فيفري','مارس','افريل','ماي','جوان','جويلية','اوت','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('3','.',',','د.ت.‏','TND','د.ت.‏ %v','د.ت.‏%v-','Tunisian Dinar','دينار تونسي');
			$ci->NumberFormat = new NumberFormat('3','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('3','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('TN');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'en-za':
			$ci = new CultureInfo('en-ZA','en','en','English (South Africa)','English (South Africa)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','R','ZAR','R %v','R-%v','South African Rand','Rand');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('ZA');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-do':
			$ci = new CultureInfo('es-DO','es','es','Spanish (Dominican Republic)','Español (República Dominicana)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','RD$','DOP','RD$%v','(RD$%v)','Dominican Peso','Peso');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('DO');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'ar-om':
			$ci = new CultureInfo('ar-OM','ar','ar','Arabic (Oman)','العربية (عمان)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ر.ع.‏','OMR','ر.ع.‏ %v','ر.ع.‏%v-','Rial Omani','ريال عماني');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('OM');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'en-jm':
			$ci = new CultureInfo('en-JM','en','en','English (Jamaica)','English (Jamaica)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','J$','JMD','J$%v','-J$%v','Jamaican Dollar','Jamaican Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('JM');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-ve':
			$ci = new CultureInfo('es-VE','es','es','Spanish (Venezuela)','Español (Republica Bolivariana de Venezuela)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','Bs','VEB','Bs %v','Bs -%v','Venezuelan Bolivar','Bolívar');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('VE');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'ar-ye':
			$ci = new CultureInfo('ar-YE','ar','ar','Arabic (Yemen)','العربية (اليمن)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ر.ي.‏','YER','ر.ي.‏ %v','ر.ي.‏%v-','Yemeni Rial','ريال يمني');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('YE');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'en-029':
			$ci = new CultureInfo('en-029','en','en','English (Caribbean)','English (Caribbean)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','USD','$%v','-$%v','US Dollar','US Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('029');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-co':
			$ci = new CultureInfo('es-CO','es','es','Spanish (Colombia)','Español (Colombia)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','$','COP','$ %v','($ %v)','Colombian Peso','Peso');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('CO');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'ar-sy':
			$ci = new CultureInfo('ar-SY','ar','ar','Arabic (Syria)','العربية (سوريا)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->ShortMonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->GenitiveMonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ل.س.‏','SYP','ل.س.‏ %v','ل.س.‏%v-','Syrian Pound','ليرة سوري');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('SY');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'en-bz':
			$ci = new CultureInfo('en-BZ','en','en','English (Belize)','English (Belize)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','BZ$','BZD','BZ$%v','(BZ$%v)','Belize Dollar','Belize Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('BZ');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-pe':
			$ci = new CultureInfo('es-PE','es','es','Spanish (Peru)','Español (Perú)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','S/.','PEN','S/. %v','S/. -%v','Peruvian Nuevo Sol','Nuevo Sol');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('PE');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'ar-jo':
			$ci = new CultureInfo('ar-JO','ar','ar','Arabic (Jordan)','العربية (الأردن)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->ShortMonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->GenitiveMonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('3','.',',','د.ا.‏','JOD','د.ا.‏ %v','د.ا.‏%v-','Jordanian Dinar','دينار اردني');
			$ci->NumberFormat = new NumberFormat('3','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('3','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('JO');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'en-tt':
			$ci = new CultureInfo('en-TT','en','en','English (Trinidad and Tobago)','English (Trinidad y Tobago)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','TT$','TTD','TT$%v','(TT$%v)','Trinidad Dollar','Trinidad Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('TT');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-ar':
			$ci = new CultureInfo('es-AR','es','es','Spanish (Argentina)','Español (Argentina)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','$','ARS','$ %v','$-%v','Argentine Peso','Peso');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('AR');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'ar-lb':
			$ci = new CultureInfo('ar-LB','ar','ar','Arabic (Lebanon)','العربية (لبنان)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->ShortMonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->GenitiveMonthNames = array('كانون الثاني','شباط','آذار','نيسان','أيار','حزيران','تموز','آب','أيلول','تشرين الأول','تشرين الثاني','كانون الأول','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ل.ل.‏','LBP','ل.ل.‏ %v','ل.ل.‏%v-','Lebanese Pound','ليرة لبناني');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('LB');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'en-zw':
			$ci = new CultureInfo('en-ZW','en','en','English (Zimbabwe)','English (Zimbabwe)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','Z$','ZWD','Z$%v','(Z$%v)','Zimbabwe Dollar','Zimbabwe Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('ZW');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-ec':
			$ci = new CultureInfo('es-EC','es','es','Spanish (Ecuador)','Español (Ecuador)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','$','USD','$ %v','($ %v)','US Dollar','US Dolar');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('EC');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'ar-kw':
			$ci = new CultureInfo('ar-KW','ar','ar','Arabic (Kuwait)','العربية (الكويت)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('3','.',',','د.ك.‏','KWD','د.ك.‏ %v','د.ك.‏%v-','Kuwaiti Dinar','دينار كويتي');
			$ci->NumberFormat = new NumberFormat('3','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('3','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('KW');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'en-ph':
			$ci = new CultureInfo('en-PH','en','en','English (Republic of the Philippines)','English (Philippines)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','Php','PHP','Php%v','(Php%v)','Philippine Peso','Philippine Peso');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('PH');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'es-cl':
			$ci = new CultureInfo('es-CL','es','es','Spanish (Chile)','Español (Chile)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','$','CLP','$ %v','-$ %v','Chilean Peso','Peso');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('CL');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'ar-ae':
			$ci = new CultureInfo('ar-AE','ar','ar','Arabic (U.A.E.)','العربية (الإمارات العربية المتحدة)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','د.إ.‏','AED','د.إ.‏ %v','د.إ.‏%v-','UAE Dirham','درهم اماراتي');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('AE');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'es-uy':
			$ci = new CultureInfo('es-UY','es','es','Spanish (Uruguay)','Español (Uruguay)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','$U','UYU','$U %v','($U %v)','Peso Uruguayo','Peso');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('UY');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'ar-bh':
			$ci = new CultureInfo('ar-BH','ar','ar','Arabic (Bahrain)','العربية (البحرين)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('3','.',',','د.ب.‏','BHD','د.ب.‏ %v','د.ب.‏%v-','Bahraini Dinar','دينار بحريني');
			$ci->NumberFormat = new NumberFormat('3','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('3','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('BH');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'es-py':
			$ci = new CultureInfo('es-PY','es','es','Spanish (Paraguay)','Español (Paraguay)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','Gs','PYG','Gs %v','(Gs %v)','Paraguay Guarani','Guaraní');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('PY');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'ar-qa':
			$ci = new CultureInfo('ar-QA','ar','ar','Arabic (Qatar)','العربية (قطر)','1');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->ShortMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->GenitiveMonthNames = array('يناير','فبراير','مارس','ابريل','مايو','يونيو','يوليو','اغسطس','سبتمبر','اكتوبر','نوفمبر','ديسمبر','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('أ','ا','ث','أ','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ر.ق.‏','QAR','ر.ق.‏ %v','ر.ق.‏%v-','Qatari Rial','ريال قطري');
			$ci->NumberFormat = new NumberFormat('2','.',',','%v-');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','%-%v');
			$ci->Region = internal_getRegion('QA');
			$ci->Parent = internal_getLanguage('ar');
			return $ci;
		case 'es-bo':
			$ci = new CultureInfo('es-BO','es','es','Spanish (Bolivia)','Español (Bolivia)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','$b','BOB','$b %v','($b %v)','Boliviano','Boliviano');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('BO');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'es-sv':
			$ci = new CultureInfo('es-SV','es','es','Spanish (El Salvador)','Español (El Salvador)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','USD','$%v','-$%v','US Dollar','US Dolar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('SV');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'es-hn':
			$ci = new CultureInfo('es-HN','es','es','Spanish (Honduras)','Español (Honduras)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','L.','HNL','L. %v','L. -%v','Honduran Lempira','Lempira');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('HN');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'es-ni':
			$ci = new CultureInfo('es-NI','es','es','Spanish (Nicaragua)','Español (Nicaragua)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','C$','NIO','C$ %v','(C$ %v)','Nicaraguan Cordoba Oro','Córdoba');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('NI');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'es-pr':
			$ci = new CultureInfo('es-PR','es','es','Spanish (Puerto Rico)','Español (Puerto Rico)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','USD','$ %v','($ %v)','US Dollar','US Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('PR');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
		case 'am-et':
			$ci = new CultureInfo('am-ET','','am','Amharic (Ethiopia)','አማርኛ (ኢትዮጵያ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ጃንዩወሪ','ፌብሩወሪ','ማርች','ኤፕረል','ሜይ','ጁን','ጁላይ','ኦገስት','ሴፕቴምበር','ኦክተውበር','ኖቬምበር','ዲሴምበር','');
			$dtf->ShortMonthNames = array('ጃንዩ','ፌብሩ','ማርች','ኤፕረ','ሜይ','ጁን','ጁላይ','ኦገስ','ሴፕቴ','ኦክተ','ኖቬም','ዲሴም','');
			$dtf->GenitiveMonthNames = array('ጃንዩወሪ','ፌብሩወሪ','ማርች','ኤፕረል','ሜይ','ጁን','ጁላይ','ኦገስት','ሴፕቴምበር','ኦክተውበር','ኖቬምበር','ዲሴምበር','');
			$dtf->DayNames = array('እሑድ','ሰኞ','ማክሰኞ','ረቡዕ','ሐሙስ','ዓርብ','ቅዳሜ');
			$dtf->ShortDayNames = array('እ','ሰ','ማ','ረ','ሐ','ዓ','ቅ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ETB','ETB','ETB%v','-ETB%v','Ethiopian Birr','ብር');
			$ci->NumberFormat = new NumberFormat('1','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('1','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('ET');
			return $ci;
		case 'tzm-latn-dz':
			$ci = new CultureInfo('tzm-Latn-DZ','','tzm','Tamazight (Latin) (Algeria)','Tamazight (Djazaïr)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Yenayer','Furar','Maghres','Yebrir','Mayu','Yunyu','Yulyu','Ghuct','Cutenber','Ktuber','Wambir','Dujanbir','');
			$dtf->ShortMonthNames = array('Yen','Fur','Mag','Yeb','May','Yun','Yul','Ghu','Cut','Ktu','Wam','Duj','');
			$dtf->GenitiveMonthNames = array('Yenayer','Furar','Maghres','Yebrir','Mayu','Yunyu','Yulyu','Ghuct','Cutenber','Ktuber','Wambir','Dujanbir','');
			$dtf->DayNames = array('Acer','Arime','Aram','Ahad','Amhadh','Sem','Sedh');
			$dtf->ShortDayNames = array('Ac','Ar','Ar','Ah','Am','Se','Se');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','DZD','DZD','%v DZD','-%v DZD','Algerian Dinar','Dinar');
			$ci->NumberFormat = new NumberFormat('2',',','.','%v-');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','%-%v');
			$ci->Region = internal_getRegion('DZ');
			return $ci;
		case 'iu-latn-ca':
			$ci = new CultureInfo('iu-Latn-CA','','iu','Inuktitut (Latin) (Canada)','Inuktitut (kanata)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Jaannuari','Viivvuari','Maatsi','Iipuri','Mai','Juuni','Julai','Aaggiisi','Sitipiri','Utupiri','Nuvipiri','Tisipiri','');
			$dtf->ShortMonthNames = array('Jan','Viv','Mas','Ipu','Mai','Jun','Jul','Agi','Sii','Uut','Nuv','Tis','');
			$dtf->GenitiveMonthNames = array('Jaannuari','Viivvuari','Maatsi','Iipuri','Mai','Juuni','Julai','Aaggiisi','Sitipiri','Utupiri','Nuvipiri','Tisipiri','');
			$dtf->DayNames = array('Naattiinguja','Naggajjau','Aippiq','Pingatsiq','Sitammiq','Tallirmiq','Sivataarvik');
			$dtf->ShortDayNames = array('N','N','A','P','S','T','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','CAD','$%v','-$%v','Canadian Dollar','Kanataup Kiinaujanga;');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('CA');
			return $ci;
		case 'sma-no':
			$ci = new CultureInfo('sma-NO','','sma','Sami (Southern) (Norway)','Åarjelsaemiengiele (Nöörje)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('tsïengele','goevte','njoktje','voerhtje','suehpede','ruffie','snjaltje','mïetske','skïerede','golke','rahka','goeve','');
			$dtf->ShortMonthNames = array('tsïen','goevt','njok','voer','sueh','ruff','snja','mïet','skïer','golk','rahk','goev','');
			$dtf->GenitiveMonthNames = array('tsïengelen','goevten','njoktjen','voerhtjen','suehpeden','ruffien','snjaltjen','mïetsken','skïereden','golken','rahkan','goeven','');
			$dtf->DayNames = array('aejlege','måanta','dæjsta','gaskevåhkoe','duarsta','bearjadahke','laavvardahke');
			$dtf->ShortDayNames = array('a','m','d','g','d','b','l');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','kr','NOK','kr %v','kr -%v','Norwegian Krone','kråvnoe');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%%v','-%v%');
			$ci->Region = internal_getRegion('NO');
			return $ci;
		case 'mn-mong-cn':
			$ci = new CultureInfo('mn-Mong-CN','','mn','Mongolian (Traditional Mongolian) (People\'s Republic of China)','ᠮᠤᠨᠭᠭᠤᠯ ᠬᠡᠯᠡ (ᠪᠦᠭᠦᠳᠡ ᠨᠠᠢᠷᠠᠮᠳᠠᠬᠤ ᠳᠤᠮᠳᠠᠳᠤ ᠠᠷᠠᠳ ᠣᠯᠣᠰ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ᠨᠢᠭᠡᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠬᠤᠶ᠋ᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠭᠤᠷᠪᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠲᠦᠷᠪᠡᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠲᠠᠪᠤᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠵᠢᠷᠭᠤᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠲᠤᠯᠤᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠨᠠᠢᠮᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠶᠢᠰᠦᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠠᠷᠪᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠠᠷᠪᠠᠨ ᠨᠢᠭᠡᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠠᠷᠪᠠᠨ ᠬᠤᠶ᠋ᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','');
			$dtf->ShortMonthNames = array('ᠨᠢᠭᠡᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠬᠤᠶ᠋ᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠭᠤᠷᠪᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠲᠦᠷᠪᠡᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠲᠠᠪᠤᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠵᠢᠷᠭᠤᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠲᠤᠯᠤᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠨᠠᠢᠮᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠶᠢᠰᠦᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠠᠷᠪᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠠᠷᠪᠠᠨ ᠨᠢᠭᠡᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠠᠷᠪᠠᠨ ᠬᠤᠶ᠋ᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','');
			$dtf->GenitiveMonthNames = array('ᠨᠢᠭᠡᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠬᠤᠶ᠋ᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠭᠤᠷᠪᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠲᠦᠷᠪᠡᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠲᠠᠪᠤᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠵᠢᠷᠭᠤᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠲᠤᠯᠤᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠨᠠᠢᠮᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠶᠢᠰᠦᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠠᠷᠪᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','ᠠᠷᠪᠠᠨ ᠨᠢᠭᠡᠳᠦᠭᠡᠷ ᠰᠠᠷ᠎ᠠ','ᠠᠷᠪᠠᠨ ᠬᠤᠶ᠋ᠠᠳᠤᠭᠠᠷ ᠰᠠᠷ᠎ᠠ','');
			$dtf->DayNames = array('ᠭᠠᠷᠠᠭ ᠤᠨ ᠡᠳᠦᠷ','ᠭᠠᠷᠠᠭ ᠤᠨ ᠨᠢᠭᠡᠨ','ᠭᠠᠷᠠᠭ ᠤᠨ ᠬᠣᠶᠠᠷ','ᠭᠠᠷᠠᠭ ᠤᠨ ᠭᠤᠷᠪᠠᠨ','ᠭᠠᠷᠠᠭ ᠤᠨ ᠳᠥᠷᠪᠡᠨ','ᠭᠠᠷᠠᠭ ᠤᠨ ᠲᠠᠪᠤᠨ','ᠭᠠᠷᠠᠭ ᠤᠨ ᠵᠢᠷᠭᠤᠭᠠᠨ');
			$dtf->ShortDayNames = array('ᠡ‍','ᠨᠢ‍','ᠬᠣ‍','ᠭᠤ‍','ᠳᠥ‍','ᠲᠠ‍','ᠵᠢ‍');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','¥','CNY','¥%v','¥-%v','PRC Renminbi','ᠠᠷᠠᠳ  ᠤᠨ ᠵᠤᠭᠤᠰ');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('CN');
			return $ci;
		case 'gd-gb':
			$ci = new CultureInfo('gd-GB','','gd','Scottish Gaelic (United Kingdom)','Gàidhlig (An Rìoghachd Aonaichte)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Am Faoilleach','An Gearran','Am Màrt','An Giblean','An Cèitean','An t-Ògmhios','An t-Iuchar','An Lùnastal','An t-Sultain','An Dàmhair','An t-Samhain','An Dùbhlachd','');
			$dtf->ShortMonthNames = array('Fao','Gea','Màr','Gib','Cèi','Ògm','Iuc','Lùn','Sul','Dàm','Sam','Dùb','');
			$dtf->GenitiveMonthNames = array('Am Faoilleach','An Gearran','Am Màrt','An Giblean','An Cèitean','An t-Ògmhios','An t-Iuchar','An Lùnastal','An t-Sultain','An Dàmhair','An t-Samhain','An Dùbhlachd','');
			$dtf->DayNames = array('Didòmhnaich','Diluain','Dimàirt','Diciadain','Diardaoin','Dihaoine','Disathairne');
			$dtf->ShortDayNames = array('D','L','M','C','A','H','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','£','GBP','£%v','-£%v','UK Pound Sterling','Nota Bhreatannach');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('GB');
			return $ci;
		case 'en-my':
			$ci = new CultureInfo('en-MY','en','en','English (Malaysia)','English (Malaysia)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('S','M','T','W','T','F','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','RM','MYR','RM%v','(RM%v)','Malaysian Ringgit','Malaysian Ringgit');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('MY');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'prs-af':
			$ci = new CultureInfo('prs-AF','','prs','Dari (Afghanistan)','درى (افغانستان)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('محرم','صفر','ربيع الأول','ربيع الثاني','جمادى الأولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->ShortMonthNames = array('محرم','صفر','ربيع الاول','ربيع الثاني','جمادى الاولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->GenitiveMonthNames = array('محرم','صفر','ربيع الأول','ربيع الثاني','جمادى الأولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('ح','ن','ث','ر','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','؋','AFN','؋%v','؋%v-','Afghani','افغانى');
			$ci->NumberFormat = new NumberFormat('2',',','.','%v-');
			$ci->PercentFormat = new PercentFormat('2',',','.','%v %','%-%v');
			$ci->Region = internal_getRegion('AF');
			return $ci;
		case 'bn-bd':
			$ci = new CultureInfo('bn-BD','','bn','Bengali (Bangladesh)','বাংলা (বাংলাদেশ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('জানুয়ারী','ফেব্রুয়ারী','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর','');
			$dtf->ShortMonthNames = array('জানু.','ফেব্রু.','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগ.','সেপ্টে.','অক্টো.','নভে.','ডিসে.','');
			$dtf->GenitiveMonthNames = array('জানুয়ারী','ফেব্রুয়ারী','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর','');
			$dtf->DayNames = array('রবিবার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার');
			$dtf->ShortDayNames = array('র','স','ম','ব','ব','শ','শ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','৳','BDT','৳ %v','৳ -%v','Bangladeshi Taka','টাকা');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('BD');
			return $ci;
		case 'wo-sn':
			$ci = new CultureInfo('wo-SN','','wo','Wolof (Senegal)','Wolof (Sénégal)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->ShortMonthNames = array('janv.','févr.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.','');
			$dtf->GenitiveMonthNames = array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre','');
			$dtf->DayNames = array('dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi');
			$dtf->ShortDayNames = array('di','lu','ma','me','je','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','XOF','XOF','%v XOF','-%v XOF','XOF Senegal','XOF Senegal');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('SN');
			return $ci;
		case 'rw-rw':
			$ci = new CultureInfo('rw-RW','','rw','Kinyarwanda (Rwanda)','Kinyarwanda (Rwanda)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Mutarama','Gashyantare','Werurwe','Mata','Gicurasi','Kamena','Nyakanga','Kanama','Nzeli','Ukwakira','Ugushyingo','Ukuboza','');
			$dtf->ShortMonthNames = array('Mut','Gas','Wer','Mat','Gic','Kam','Nya','Kan','Nze','Ukwa','Ugu','Uku','');
			$dtf->GenitiveMonthNames = array('Mutarama','Gashyantare','Werurwe','Mata','Gicurasi','Kamena','Nyakanga','Kanama','Nzeli','Ukwakira','Ugushyingo','Ukuboza','');
			$dtf->DayNames = array('Ku wa mbere','Ku wa kabiri','Ku wa gatatu','Ku wa kane','Ku wa gatanu','Ku wa gatandatu','Ku cyumweru');
			$dtf->ShortDayNames = array('mb','ka','ga','ka','ga','ga','cy');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','RWF','RWF','RWF %v','RWF-%v','Rwandan Franc','Ifaranga');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%%v','-%v%');
			$ci->Region = internal_getRegion('RW');
			return $ci;
		case 'qut-gt':
			$ci = new CultureInfo('qut-GT','','qut','K\'iche (Guatemala)','K\'iche (Guatemala)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('nab\'e ik\'','ukab\' ik\'','rox ik\'','ukaj ik\'','uro\' ik\'','uwaq ik\'','uwuq ik\'','uwajxaq ik\'','ub\'elej ik\'','ulaj ik\'','ujulaj ik\'','ukab\'laj ik\'','');
			$dtf->ShortMonthNames = array('nab\'e','ukab','rox','ukaj','uro','uwaq','uwuq','uwajxaq','ub\'elej','ulaj','ujulaj','ukab\'laj','');
			$dtf->GenitiveMonthNames = array('nab\'e ik\'','ukab\' ik\'','rox ik\'','ukaj ik\'','uro\' ik\'','uwaq ik\'','uwuq ik\'','uwajxaq ik\'','ub\'elej ik\'','ulaj ik\'','ujulaj ik\'','ukab\'laj ik\'','');
			$dtf->DayNames = array('juq\'ij','kaq\'ij','oxq\'ij','kajq\'ij','joq\'ij','waqq\'ij','wuqq\'ij');
			$dtf->ShortDayNames = array('ju','ka','ox','ka','jo','wa','wu');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','Q','GTQ','Q%v','(Q%v)','Guatemalan Quetzal','Quetzal');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('GT');
			return $ci;
		case 'sah-ru':
			$ci = new CultureInfo('sah-RU','','sah','Yakut (Russia)','Cаха (Россия)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Тохсунньу','Олунньу','Кулун тутар','Муус устар','Ыам ыйа','Бэс ыйа','От ыйа','Атырдьах ыйа','Балаҕан ыйа','Алтынньы','Сэтинньи','Ахсынньы','');
			$dtf->ShortMonthNames = array('тхс','олн','кул','мст','ыам','бэс','отй','атр','блҕ','алт','стн','ахс','');
			$dtf->GenitiveMonthNames = array('тохсунньу','олунньу','кулун тутар','муус устар','ыам ыйын','бэс ыйын','от ыйын','атырдьах ыйын','балаҕан ыйын','алтынньы','сэтинньи','ахсынньы','');
			$dtf->DayNames = array('баскыһыанньа','бэнидиэнньик','оптуорунньук','сэрэдэ','чэппиэр','бээтинсэ','субуота');
			$dtf->ShortDayNames = array('Бс','Бн','Оп','Ср','Чп','Бт','Сб');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','с.','RUB','%vс.','-%vс.','Russian Ruble','солкуобай');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%v%','-%v%');
			$ci->Region = internal_getRegion('RU');
			return $ci;
		case 'gsw-fr':
			$ci = new CultureInfo('gsw-FR','','gsw','Alsatian (France)','Elsässisch (Frànkrisch)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Jänner','Feverje','März','Àpril','Mai','Jüni','Jüli','Augscht','September','Oktower','Nowember','Dezember','');
			$dtf->ShortMonthNames = array('Jän.','Fev.','März','Apr.','Mai','Jüni','Jüli','Aug.','Sept.','Okt.','Now.','Dez.','');
			$dtf->GenitiveMonthNames = array('Jänner','Feverje','März','Àpril','Mai','Jüni','Jüli','Augscht','September','Oktower','Nowember','Dezember','');
			$dtf->DayNames = array('Sundàà','Mondàà','Dienschdàà','Mittwuch','Dunnerschdàà','Fridàà','Sàmschdàà');
			$dtf->ShortDayNames = array('Su','Mo','Di','Mi','Du','Fr','Sà');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FR');
			return $ci;
		case 'co-fr':
			$ci = new CultureInfo('co-FR','','co','Corsican (France)','Corsu (France)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ghjennaghju','ferraghju','marzu','aprile','maghju','ghjunghju','lugliu','aostu','settembre','ottobre','nuvembre','dicembre','');
			$dtf->ShortMonthNames = array('ghje','ferr','marz','apri','magh','ghju','lugl','aost','sett','otto','nuve','dice','');
			$dtf->GenitiveMonthNames = array('ghjennaghju','ferraghju','marzu','aprile','maghju','ghjunghju','lugliu','aostu','settembre','ottobre','nuvembre','dicembre','');
			$dtf->DayNames = array('dumenica','luni','marti','mercuri','ghjovi','venderi','sabbatu');
			$dtf->ShortDayNames = array('du','lu','ma','me','gh','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FR');
			return $ci;
		case 'oc-fr':
			$ci = new CultureInfo('oc-FR','','oc','Occitan (France)','Occitan (França)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('genier','febrier','març','abril','mai','junh','julh','agost','setembre','octobre','novembre','desembre','');
			$dtf->ShortMonthNames = array('gen.','feb.','mar.','abr.','mai.','jun.','jul.','ag.','set.','oct.','nov.','des.','');
			$dtf->GenitiveMonthNames = array('de genier','de febrier','de març','d\'abril','de mai','de junh','de julh','d\'agost','de setembre','d\'octobre','de novembre','de desembre','');
			$dtf->DayNames = array('dimenge','diluns','dimars','dimècres','dijòus','divendres','dissabte');
			$dtf->ShortDayNames = array('di','lu','ma','mè','jò','ve','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FR');
			return $ci;
		case 'mi-nz':
			$ci = new CultureInfo('mi-NZ','','mi','Maori (New Zealand)','Reo Māori (Aotearoa)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Kohi-tātea','Hui-tanguru','Poutū-te-rangi','Paenga-whāwhā','Haratua','Pipiri','Hōngongoi','Here-turi-kōkā','Mahuru','Whiringa-ā-nuku','Whiringa-ā-rangi','Hakihea','');
			$dtf->ShortMonthNames = array('Kohi','Hui','Pou','Pae','Hara','Pipi','Hōngo','Here','Mahu','Nuku','Rangi','Haki','');
			$dtf->GenitiveMonthNames = array('Kohi-tātea','Hui-tanguru','Poutū-te-rangi','Paenga-whāwhā','Haratua','Pipiri','Hōngongoi','Here-turi-kōkā','Mahuru','Whiringa-ā-nuku','Whiringa-ā-rangi','Hakihea','');
			$dtf->DayNames = array('Rātapu','Rāhina','Rātū','Rāapa','Rāpare','Rāmere','Rāhoroi');
			$dtf->ShortDayNames = array('Ta','Hi','Tū','Aa','Pa','Me','Ho');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','NZD','$%v','-$%v','New Zealand Dollar','tāra');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('NZ');
			return $ci;
		case 'ga-ie':
			$ci = new CultureInfo('ga-IE','','ga','Irish (Ireland)','Gaeilge (Éire)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Eanáir','Feabhra','Márta','Aibreán','Bealtaine','Meitheamh','Iúil','Lúnasa','Meán Fómhair','Deireadh Fómhair','Samhain','Nollaig','');
			$dtf->ShortMonthNames = array('Ean','Feabh','Már','Aib','Bealt','Meith','Iúil','Lún','M.Fómh','D.Fómh','Samh','Noll','');
			$dtf->GenitiveMonthNames = array('Eanáir','Feabhra','Márta','Aibreán','Bealtaine','Meitheamh','Iúil','Lúnasa','Meán Fómhair','Deireadh Fómhair','Samhain','Nollaig','');
			$dtf->DayNames = array('Dé Domhnaigh','Dé Luain','Dé Máirt','Dé Céadaoin','Déardaoin','Dé hAoine','Dé Sathairn');
			$dtf->ShortDayNames = array('Do','Lu','Má','Cé','De','Ao','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','€','EUR','€%v','-€%v','Euro','Euro');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('IE');
			return $ci;
		case 'se-se':
			$ci = new CultureInfo('se-SE','','se','Sami (Northern) (Sweden)','Davvisámegiella (Ruoŧŧa)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ođđajagemánnu','guovvamánnu','njukčamánnu','cuoŋománnu','miessemánnu','geassemánnu','suoidnemánnu','borgemánnu','čakčamánnu','golggotmánnu','skábmamánnu','juovlamánnu','');
			$dtf->ShortMonthNames = array('ođđj','guov','njuk','cuo','mies','geas','suoi','borg','čakč','golg','skáb','juov','');
			$dtf->GenitiveMonthNames = array('ođđajagimánu','guovvamánu','njukčamánu','cuoŋománu','miessemánu','geassemánu','suoidnemánu','borgemánu','čakčamánu','golggotmánu','skábmamánu','juovlamánu','');
			$dtf->DayNames = array('sotnabeaivi','mánnodat','disdat','gaskavahkku','duorastat','bearjadat','lávvardat');
			$dtf->ShortDayNames = array('s','m','d','g','d','b','l');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','kr','SEK','%v kr','-%v kr','Swedish Krona','kruvdno');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('SE');
			return $ci;
		case 'br-fr':
			$ci = new CultureInfo('br-FR','','br','Breton (France)','Brezhoneg (Frañs)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Genver','C\'hwevrer','Meurzh','Ebrel','Mae','Mezheven','Gouere','Eost','Gwengolo','Here','Du','Kerzu','');
			$dtf->ShortMonthNames = array('Gen.','C\'hwe.','Meur.','Ebr.','Mae','Mezh.','Goue.','Eost','Gwen.','Here','Du','Kzu','');
			$dtf->GenitiveMonthNames = array('Genver','C\'hwevrer','Meurzh','Ebrel','Mae','Mezheven','Gouere','Eost','Gwengolo','Here','Du','Kerzu','');
			$dtf->DayNames = array('Sul','Lun','Meurzh','Merc\'her','Yaou','Gwener','Sadorn');
			$dtf->ShortDayNames = array('Su','Lu','Mz','Mc','Ya','Gw','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FR');
			return $ci;
		case 'smn-fi':
			$ci = new CultureInfo('smn-FI','','smn','Sami (Inari) (Finland)','Sämikielâ (Suomâ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('uđđâivemáánu','kuovâmáánu','njuhčâmáánu','cuáŋuimáánu','vyesimáánu','kesimáánu','syeinimáánu','porgemáánu','čohčâmáánu','roovvâdmáánu','skammâmáánu','juovlâmáánu','');
			$dtf->ShortMonthNames = array('uđiv','kuov','njuh','cuoŋ','vyes','kesi','syei','porg','čoh','roov','ska','juov','');
			$dtf->GenitiveMonthNames = array('uđđâivemáánu','kuovâmáánu','njuhčâmáánu','cuáŋuimáánu','vyesimáánu','kesimáánu','syeinimáánu','porgemáánu','čohčâmáánu','roovvâdmáánu','skammâmáánu','juovlâmáánu','');
			$dtf->DayNames = array('pasepeivi','vuossargâ','majebargâ','koskokko','tuorâstâh','vástuppeivi','lávárdâh');
			$dtf->ShortDayNames = array('p','v','m','k','t','v','l');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','evro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FI');
			return $ci;
		case 'moh-ca':
			$ci = new CultureInfo('moh-CA','','moh','Mohawk (Canada)','Kanien\'Kéha (Canada)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Tsothohrkó:Wa','Enniska','Enniskó:Wa','Onerahtókha','Onerahtohkó:Wa','Ohiari:Ha','Ohiarihkó:Wa','Seskéha','Seskehkó:Wa','Kenténha','Kentenhkó:Wa','Tsothóhrha','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('Tsothohrkó:Wa','Enniska','Enniskó:Wa','Onerahtókha','Onerahtohkó:Wa','Ohiari:Ha','Ohiarihkó:Wa','Seskéha','Seskehkó:Wa','Kenténha','Kentenhkó:Wa','Tsothóhrha','');
			$dtf->DayNames = array('Awentatokentì:ke','Awentataón\'ke','Ratironhia\'kehronòn:ke','Soséhne','Okaristiiáhne','Ronwaia\'tanentaktonhne','Entákta');
			$dtf->ShortDayNames = array('S','M','T','W','T','F','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','CAD','$%v','-$%v','Canadian Dollar','Canadian Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('CA');
			return $ci;
		case 'arn-cl':
			$ci = new CultureInfo('arn-CL','','arn','Mapudungun (Chile)','Mapudungun (Chile)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sá');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','$','CLP','$ %v','-$ %v','Chilean Peso','Peso');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('CL');
			return $ci;
		case 'ii-cn':
			$ci = new CultureInfo('ii-CN','','ii','Yi (People\'s Republic of China)','ꆈꌠꁱꂷ (ꍏꉸꏓꂱꇭꉼꇩ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ꋍꆪ','ꑍꆪ','ꌕꆪ','ꇖꆪ','ꉬꆪ','ꃘꆪ','ꏃꆪ','ꉆꆪ','ꈬꆪ','ꊰꆪ','ꊯꊪꆪ','ꊰꑋꆪ','');
			$dtf->ShortMonthNames = array('ꋍꆪ','ꑍꆪ','ꌕꆪ','ꇖꆪ','ꉬꆪ','ꃘꆪ','ꏃꆪ','ꉆꆪ','ꈬꆪ','ꊰꆪ','ꊯꊪꆪ','ꊰꑋꆪ','');
			$dtf->GenitiveMonthNames = array('ꋍꆪ','ꑍꆪ','ꌕꆪ','ꇖꆪ','ꉬꆪ','ꃘꆪ','ꏃꆪ','ꉆꆪ','ꈬꆪ','ꊰꆪ','ꊯꊪꆪ','ꊰꑋꆪ','');
			$dtf->DayNames = array('ꑭꆏꑍ','ꆏꊂ꒔','ꆏꊂꑍ','ꆏꊂꌕ','ꆏꊂꇖ','ꆏꊂꉬ','ꆏꊂꃘ');
			$dtf->ShortDayNames = array('ꆏ','꒔','ꑍ','ꌕ','ꇖ','ꉬ','ꃘ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','¥','CNY','¥%v','¥-%v','PRC Renminbi','ꎆꃀ');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('CN');
			return $ci;
		case 'dsb-de':
			$ci = new CultureInfo('dsb-DE','','dsb','Lower Sorbian (Germany)','Solnoserbšćina (Nimska)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','měrc','apryl','maj','junij','julij','awgust','september','oktober','nowember','december','');
			$dtf->ShortMonthNames = array('jan','feb','měr','apr','maj','jun','jul','awg','sep','okt','now','dec','');
			$dtf->GenitiveMonthNames = array('januara','februara','měrca','apryla','maja','junija','julija','awgusta','septembra','oktobra','nowembra','decembra','');
			$dtf->DayNames = array('njeźela','ponjeźele','wałtora','srjoda','stwortk','pětk','sobota');
			$dtf->ShortDayNames = array('n','p','w','s','s','p','s');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','Euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('DE');
			return $ci;
		case 'ig-ng':
			$ci = new CultureInfo('ig-NG','','ig','Igbo (Nigeria)','Igbo (Nigeria)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Onwa mbu','Onwa ibua','Onwa ato','Onwa ano','Onwa ise','Onwa isi','Onwa asa','Onwa asato','Onwa itolu','Onwa iri','Onwa iri n\'ofu','Onwa iri n\'ibua','');
			$dtf->ShortMonthNames = array('mbu.','ibu.','ato.','ano.','ise','isi','asa','asa.','ito.','iri.','n\'of.','n\'ib.','');
			$dtf->GenitiveMonthNames = array('Onwa mbu','Onwa ibua','Onwa ato','Onwa ano','Onwa ise','Onwa isi','Onwa asa','Onwa asato','Onwa itolu','Onwa iri','Onwa iri n\'ofu','Onwa iri n\'ibua','');
			$dtf->DayNames = array('Aiku','Aje','Isegun','Ojo\'ru','Ojo\'bo','Eti','Abameta');
			$dtf->ShortDayNames = array('A','A','I','O','O','E','A');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','N','NIO','N %v','N-%v','Nigerian Naira','Naira');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('NG');
			return $ci;
		case 'kl-gl':
			$ci = new CultureInfo('kl-GL','','kl','Greenlandic (Greenland)','Kalaallisut (Kalaallit Nunaat)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januari','februari','martsi','apriili','maaji','juni','juli','aggusti','septembari','oktobari','novembari','decembari','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','mai','jun','jul','aug','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januari','februari','martsi','apriili','maaji','juni','juli','aggusti','septembari','oktobari','novembari','decembari','');
			$dtf->DayNames = array('sapaat','ataasinngorneq','marlunngorneq','pingasunngorneq','sisamanngorneq','tallimanngorneq','arfininngorneq');
			$dtf->ShortDayNames = array('sa','at','ma','pi','si','ta','ar');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','kr.','DKK','kr. %v','kr. -%v','Danish Krone','korunni');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('GL');
			return $ci;
		case 'lb-lu':
			$ci = new CultureInfo('lb-LU','','lb','Luxembourgish (Luxembourg)','Lëtzebuergesch (Luxembourg)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januar','Februar','Mäerz','Abrëll','Mee','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mäe','Abr','Mee','Jun','Jul','Aug','Sep','Okt','Nov','Dez','');
			$dtf->GenitiveMonthNames = array('Januar','Februar','Mäerz','Abrëll','Mee','Juni','Juli','August','September','Oktober','November','Dezember','');
			$dtf->DayNames = array('Sonndeg','Méindeg','Dënschdeg','Mëttwoch','Donneschdeg','Freideg','Samschdeg');
			$dtf->ShortDayNames = array('So','Mé','Dë','Më','Do','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('LU');
			return $ci;
		case 'ba-ru':
			$ci = new CultureInfo('ba-RU','','ba','Bashkir (Russia)','Башҡорт (Россия)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ғинуар','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь','');
			$dtf->ShortMonthNames = array('ғин','фев','мар','апр','май','июн','июл','авг','сен','окт','ноя','дек','');
			$dtf->GenitiveMonthNames = array('ғинуар','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь','');
			$dtf->DayNames = array('Йәкшәмбе','Дүшәмбе','Шишәмбе','Шаршамбы','Кесаҙна','Йома','Шәмбе');
			$dtf->ShortDayNames = array('Йш','Дш','Шш','Шр','Кс','Йм','Шб');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','һ.','RUB','%v һ.','-%v һ.','Russian Ruble','һум');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('RU');
			return $ci;
		case 'nso-za':
			$ci = new CultureInfo('nso-ZA','','nso','Sesotho sa Leboa (South Africa)','Sesotho sa Leboa (Afrika Borwa)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Pherekgong','Hlakola','Mopitlo','Moranang','Mosegamanye','Ngoatobošego','Phuphu','Phato','Lewedi','Diphalana','Dibatsela','Manthole','');
			$dtf->ShortMonthNames = array('Pher','Hlak','Mop','Mor','Mos','Ngwat','Phup','Phat','Lew','Dip','Dib','Man','');
			$dtf->GenitiveMonthNames = array('Pherekgong','Hlakola','Mopitlo','Moranang','Mosegamanye','Ngoatobošego','Phuphu','Phato','Lewedi','Diphalana','Dibatsela','Manthole','');
			$dtf->DayNames = array('Lamorena','Mošupologo','Labobedi','Laboraro','Labone','Labohlano','Mokibelo');
			$dtf->ShortDayNames = array('L','M','L','L','L','L','M');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','R','ZAR','R %v','R-%v','South African Rand','Rand');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('ZA');
			return $ci;
		case 'quz-bo':
			$ci = new CultureInfo('quz-BO','','quz','Quechua (Bolivia)','Runasimi (Bolivia Suyu)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Qulla puquy','Hatun puquy','Pauqar waray','ayriwa','Aymuray','Inti raymi','Anta Sitwa','Qhapaq Sitwa','Uma raymi','Kantaray','Ayamarq\'a','Kapaq Raymi','');
			$dtf->ShortMonthNames = array('Qul','Hat','Pau','ayr','Aym','Int','Ant','Qha','Uma','Kan','Aya','Kap','');
			$dtf->GenitiveMonthNames = array('Qulla puquy','Hatun puquy','Pauqar waray','ayriwa','Aymuray','Inti raymi','Anta Sitwa','Qhapaq Sitwa','Uma raymi','Kantaray','Ayamarq\'a','Kapaq Raymi','');
			$dtf->DayNames = array('intichaw','killachaw','atipachaw','quyllurchaw','Ch\' askachaw','Illapachaw','k\'uychichaw');
			$dtf->ShortDayNames = array('d','k','a','m','h','b','k');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','$b','BOB','$b %v','($b %v)','Boliviano','Boliviano');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('BO');
			return $ci;
		case 'yo-ng':
			$ci = new CultureInfo('yo-NG','','yo','Yoruba (Nigeria)','Yoruba (Nigeria)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Osu kinni','Osu keji','Osu keta','Osu kerin','Osu karun','Osu kefa','Osu keje','Osu kejo','Osu kesan','Osu kewa','Osu kokanla','Osu keresi','');
			$dtf->ShortMonthNames = array('kin.','kej.','ket.','ker.','kar.','kef.','kej.','kej.','kes.','kew.','kok.','ker.','');
			$dtf->GenitiveMonthNames = array('Osu kinni','Osu keji','Osu keta','Osu kerin','Osu karun','Osu kefa','Osu keje','Osu kejo','Osu kesan','Osu kewa','Osu kokanla','Osu keresi','');
			$dtf->DayNames = array('Aiku','Aje','Isegun','Ojo\'ru','Ojo\'bo','Eti','Abameta');
			$dtf->ShortDayNames = array('A','A','I','O','O','E','A');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','N','NIO','N %v','N-%v','Nigerian Naira','Naira');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('NG');
			return $ci;
		case 'ha-latn-ng':
			$ci = new CultureInfo('ha-Latn-NG','','ha','Hausa (Latin) (Nigeria)','Hausa (Nigeria)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Januwaru','Febreru','Maris','Afrilu','Mayu','Yuni','Yuli','Agusta','Satumba','Oktocba','Nuwamba','Disamba','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Afr','May','Yun','Yul','Agu','Sat','Okt','Nuw','Dis','');
			$dtf->GenitiveMonthNames = array('Januwaru','Febreru','Maris','Afrilu','Mayu','Yuni','Yuli','Agusta','Satumba','Oktocba','Nuwamba','Disamba','');
			$dtf->DayNames = array('Lahadi','Litinin','Talata','Laraba','Alhamis','Juma\'a','Asabar');
			$dtf->ShortDayNames = array('L','L','T','L','A','J','A');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','N','NIO','N %v','N-%v','Nigerian Naira','Naira');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('NG');
			return $ci;
		case 'fil-ph':
			$ci = new CultureInfo('fil-PH','','fil','Filipino (Philippines)','Filipino (Pilipinas)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Enero','Pebrero','Marso','Abril','Mayo','Hunyo','Hulyo','Agosto','Septyembre','Oktubre','Nobyembre','Disyembre','');
			$dtf->ShortMonthNames = array('En','Peb','Mar','Abr','Mayo','Hun','Hul','Agos','Sept','Okt','Nob','Dis','');
			$dtf->GenitiveMonthNames = array('Enero','Pebrero','Marso','Abril','Mayo','Hunyo','Hulyo','Agosto','Septyembre','Oktubre','Nobyembre','Disyembre','');
			$dtf->DayNames = array('Linggo','Lunes','Martes','Mierkoles','Huebes','Biernes','Sabado');
			$dtf->ShortDayNames = array('L','L','M','M','H','B','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','PhP','PHP','PhP%v','(PhP%v)','Philippine Peso','Philippine Peso');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('PH');
			return $ci;
		case 'ps-af':
			$ci = new CultureInfo('ps-AF','','ps','Pashto (Afghanistan)','پښتو (افغانستان)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('محرم','صفر','ربيع الأول','ربيع الثاني','جمادى الأولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->ShortMonthNames = array('محرم','صفر','ربيع الاول','ربيع الثاني','جمادى الاولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->GenitiveMonthNames = array('محرم','صفر','ربيع الأول','ربيع الثاني','جمادى الأولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة','');
			$dtf->DayNames = array('الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس','الجمعة','السبت');
			$dtf->ShortDayNames = array('ح','ن','ث','ر','خ','ج','س');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','٫','٬','؋','AFN','؋%v','؋%v-','Afghani','افغانى');
			$ci->NumberFormat = new NumberFormat('2',',','،','%v-');
			$ci->PercentFormat = new PercentFormat('2',',','،','%v %','%-%v');
			$ci->Region = internal_getRegion('AF');
			return $ci;
		case 'fy-nl':
			$ci = new CultureInfo('fy-NL','','fy','Frisian (Netherlands)','Frysk (Nederlân)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('jannewaris','febrewaris','maart','april','maaie','juny','july','augustus','septimber','oktober','novimber','desimber','');
			$dtf->ShortMonthNames = array('jann','febr','mrt','apr','maaie','jun','jul','aug','sept','okt','nov','des','');
			$dtf->GenitiveMonthNames = array('jannewaris','febrewaris','maart','april','maaie','juny','july','augustus','septimber','oktober','novimber','desimber','');
			$dtf->DayNames = array('Snein','Moandei','Tiisdei','Woansdei','Tongersdei','Freed','Sneon');
			$dtf->ShortDayNames = array('S','M','T','W','T','F','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','€ %v','€ -%v','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('NL');
			return $ci;
		case 'ne-np':
			$ci = new CultureInfo('ne-NP','','ne','Nepali (Nepal)','नेपाली (नेपाल)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('जनवरी','फेब्रुअरी','मार्च','अप्रिल','मे','जून','जुलाई','अगस्त','सेप्टेम्बर','अक्टोबर','नोभेम्बर','डिसेम्बर','');
			$dtf->ShortMonthNames = array('जन','फेब','मार्च','अप्रिल','मे','जून','जुलाई','अग','सेप्ट','अक्ट','नोभ','डिस','');
			$dtf->GenitiveMonthNames = array('जनवरी','फेब्रुअरी','मार्च','अप्रिल','मे','जून','जुलाई','अगस्त','सेप्टेम्बर','अक्टोबर','नोभेम्बर','डिसेम्बर','');
			$dtf->DayNames = array('आइतवार','सोमवार','मङ्गलवार','बुधवार','बिहीवार','शुक्रवार','शनिवार');
			$dtf->ShortDayNames = array('आ','सो','म','बु','बि','शु','श');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','रु','NPR','रु%v','-रु%v','Nepalese Rupees','रुपैयाँ');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('NP');
			return $ci;
		case 'se-no':
			$ci = new CultureInfo('se-NO','','se','Sami (Northern) (Norway)','Davvisámegiella (Norga)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ođđajagemánnu','guovvamánnu','njukčamánnu','cuoŋománnu','miessemánnu','geassemánnu','suoidnemánnu','borgemánnu','čakčamánnu','golggotmánnu','skábmamánnu','juovlamánnu','');
			$dtf->ShortMonthNames = array('ođđj','guov','njuk','cuo','mies','geas','suoi','borg','čakč','golg','skáb','juov','');
			$dtf->GenitiveMonthNames = array('ođđajagimánu','guovvamánu','njukčamánu','cuoŋománu','miessemánu','geassemánu','suoidnemánu','borgemánu','čakčamánu','golggotmánu','skábmamánu','juovlamánu','');
			$dtf->DayNames = array('sotnabeaivi','vuossárga','maŋŋebárga','gaskavahkku','duorastat','bearjadat','lávvardat');
			$dtf->ShortDayNames = array('s','m','d','g','d','b','l');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','kr','NOK','kr %v','kr -%v','Norwegian Krone','kruvdno');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%%v','-%v%');
			$ci->Region = internal_getRegion('NO');
			return $ci;
		case 'iu-cans-ca':
			$ci = new CultureInfo('iu-Cans-CA','','iu','Inuktitut (Syllabics) (Canada)','ᐃᓄᒃᑎᑐᑦ (ᑲᓇᑕ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ᔮᓐᓄᐊᕆ','ᕖᕝᕗᐊᕆ','ᒫᑦᓯ','ᐄᐳᕆ','ᒪᐃ','ᔫᓂ','ᔪᓚᐃ','ᐋᒡᒌᓯ','ᓯᑎᐱᕆ','ᐅᑐᐱᕆ','ᓄᕕᐱᕆ','ᑎᓯᐱᕆ','');
			$dtf->ShortMonthNames = array('ᔮᓐᓄ','ᕖᕝᕗ','ᒫᑦᓯ','ᐄᐳᕆ','ᒪᐃ','ᔫᓂ','ᔪᓚᐃ','ᐋᒡᒌ','ᓯᑎᐱ','ᐅᑐᐱ','ᓄᕕᐱ','ᑎᓯᐱ','');
			$dtf->GenitiveMonthNames = array('ᔮᓐᓄᐊᕆ','ᕖᕝᕗᐊᕆ','ᒫᑦᓯ','ᐄᐳᕆ','ᒪᐃ','ᔫᓂ','ᔪᓚᐃ','ᐋᒡᒌᓯ','ᓯᑎᐱᕆ','ᐅᑐᐱᕆ','ᓄᕕᐱᕆ','ᑎᓯᐱᕆ','');
			$dtf->DayNames = array('ᓈᑦᑏᖑᔭ','ᓇᒡᒐᔾᔭᐅ','ᐊᐃᑉᐱᖅ','ᐱᖓᑦᓯᖅ','ᓯᑕᒻᒥᖅ','ᑕᓪᓕᕐᒥᖅ','ᓯᕙᑖᕐᕕᒃ');
			$dtf->ShortDayNames = array('ᓈ','ᓇ','ᐊ','ᐱ','ᓯ','ᑕ','ᓯ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','CAD','$%v','-$%v','Canadian Dollar','ᑲᓇᑕᐅᑉ ᑮᓇᐅᔭᖓ');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('CA');
			return $ci;
		case 'sr-latn-rs':
			$ci = new CultureInfo('sr-Latn-RS','','sr','Serbian (Latin) (Serbia)','Srpski (Srbija)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','maj','jun','jul','avg','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->DayNames = array('nedelja','ponedeljak','utorak','sreda','četvrtak','petak','subota');
			$dtf->ShortDayNames = array('ne','po','ut','sr','če','pe','su');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','Din.','RSD','%v Din.','-%v Din.','Serbian Dinar','dinar');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('RS');
			return $ci;
		case 'si-lk':
			$ci = new CultureInfo('si-LK','','si','Sinhala (Sri Lanka)','සිංහ (ශ්‍රී ලංකා)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ජනවාරි','පෙබරවාරි','මාර්තු','අ‌ප්‍රේල්','මැයි','ජූනි','ජූලි','අ‌ගෝස්තු','සැප්තැම්බර්','ඔක්තෝබර්','නොවැම්බර්','දෙසැම්බර්','');
			$dtf->ShortMonthNames = array('ජන.','පෙබ.','මාර්තු.','අප්‍රේල්.','මැයි.','ජූනි.','ජූලි.','අගෝ.','සැප්.','ඔක්.','නොවැ.','දෙසැ.','');
			$dtf->GenitiveMonthNames = array('ජනවාරි','පෙබරවාරි','මාර්තු','අ‌ප්‍රේල්','මැයි','ජූනි','ජූලි','අ‌ගෝස්තු','සැප්තැම්බර්','ඔක්තෝබර්','නොවැම්බර්','දෙසැම්බර්','');
			$dtf->DayNames = array('ඉරිදා','සඳුදා','අඟහරුවාදා','බදාදා','බ්‍රහස්පතින්දා','සිකුරාදා','සෙනසුරාදා');
			$dtf->ShortDayNames = array('ඉ','ස','අ','බ','බ්‍ර','සි','සෙ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','රු.','LKR','රු. %v','(රු. %v)','Sri Lanka Rupee','රුපියල්');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('LK');
			return $ci;
		case 'sr-cyrl-rs':
			$ci = new CultureInfo('sr-Cyrl-RS','','sr','Serbian (Cyrillic) (Serbia)','Српски (Србија)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->ShortMonthNames = array('јан','феб','мар','апр','мај','јун','јул','авг','сеп','окт','нов','дец','');
			$dtf->GenitiveMonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->DayNames = array('недеља','понедељак','уторак','среда','четвртак','петак','субота');
			$dtf->ShortDayNames = array('не','по','ут','ср','че','пе','су');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','Дин.','RSD','%v Дин.','-%v Дин.','Serbian Dinar','динар');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('RS');
			return $ci;
		case 'lo-la':
			$ci = new CultureInfo('lo-LA','','lo','Lao (Lao P.D.R.)','ລາວ (ສ.ປ.ປ. ລາວ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ມັງກອນ','ກຸມພາ','ມີນາ','ເມສາ','ພຶດສະພາ','ມິຖຸນາ','ກໍລະກົດ','ສິງຫາ','ກັນຍາ','ຕຸລາ','ພະຈິກ','ທັນວາ','');
			$dtf->ShortMonthNames = array('ມັງກອນ','ກຸມພາ','ມີນາ','ເມສາ','ພຶດສະພາ','ມິຖຸນາ','ກໍລະກົດ','ສິງຫາ','ກັນຍາ','ຕຸລາ','ພະຈິກ','ທັນວາ','');
			$dtf->GenitiveMonthNames = array('ມັງກອນ','ກຸມພາ','ມີນາ','ເມສາ','ພຶດສະພາ','ມິຖຸນາ','ກໍລະກົດ','ສິງຫາ','ກັນຍາ','ຕຸລາ','ພະຈິກ','ທັນວາ','');
			$dtf->DayNames = array('ວັນອາທິດ','ວັນຈັນ','ວັນອັງຄານ','ວັນພຸດ','ວັນພະຫັດ','ວັນສຸກ','ວັນເສົາ');
			$dtf->ShortDayNames = array('ອ','ຈ','ອ','ພ','ພ','ສ','ເ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','₭','LAK','%v₭','(%v₭)','Kip','ກີບ');
			$ci->NumberFormat = new NumberFormat('2','.',',','(%v)');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v%','-%v %');
			$ci->Region = internal_getRegion('LA');
			return $ci;
		case 'km-kh':
			$ci = new CultureInfo('km-KH','','km','Khmer (Cambodia)','ខ្មែរ (កម្ពុជា)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('មករា','កុម្ភៈ','មិនា','មេសា','ឧសភា','មិថុនា','កក្កដា','សីហា','កញ្ញា','តុលា','វិច្ឆិកា','ធ្នូ','');
			$dtf->ShortMonthNames = array('១','២','៣','៤','៥','៦','៧','៨','៩','១០','១១','១២','');
			$dtf->GenitiveMonthNames = array('មករា','កុម្ភៈ','មិនា','មេសា','ឧសភា','មិថុនា','កក្កដា','សីហា','កញ្ញា','តុលា','វិច្ឆិកា','ធ្នូ','');
			$dtf->DayNames = array('ថ្ងៃអាទិត្យ','ថ្ងៃច័ន្ទ','ថ្ងៃអង្គារ','ថ្ងៃពុធ','ថ្ងៃព្រហស្បតិ៍','ថ្ងៃសុក្រ','ថ្ងៃសៅរ៍');
			$dtf->ShortDayNames = array('អា','ច','អ','ពុ','ព្','សុ','ស');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','៛','KHR','%v៛','-%v៛','Riel','x179Aៀល');
			$ci->NumberFormat = new NumberFormat('2','.',',','- %v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v%','-%%v');
			$ci->Region = internal_getRegion('KH');
			return $ci;
		case 'cy-gb':
			$ci = new CultureInfo('cy-GB','','cy','Welsh (United Kingdom)','Cymraeg (y Deyrnas Unedig)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Ionawr','Chwefror','Mawrth','Ebrill','Mai','Mehefin','Gorffennaf','Awst','Medi','Hydref','Tachwedd','Rhagfyr','');
			$dtf->ShortMonthNames = array('Ion','Chwe','Maw','Ebr','Mai','Meh','Gor','Aws','Med','Hyd','Tach','Rhag','');
			$dtf->GenitiveMonthNames = array('Ionawr','Chwefror','Mawrth','Ebrill','Mai','Mehefin','Gorffennaf','Awst','Medi','Hydref','Tachwedd','Rhagfyr','');
			$dtf->DayNames = array('Dydd Sul','Dydd Llun','Dydd Mawrth','Dydd Mercher','Dydd Iau','Dydd Gwener','Dydd Sadwrn');
			$dtf->ShortDayNames = array('Su','Ll','Ma','Me','Ia','Gw','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','£','GBP','£%v','-£%v','UK Pound Sterling','UK Pound Sterling');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('GB');
			return $ci;
		case 'bo-cn':
			$ci = new CultureInfo('bo-CN','','bo','Tibetan (People\'s Republic of China)','བོད་ཡིག (ཀྲུང་ཧྭ་མི་དམངས་སྤྱི་མཐུན་རྒྱལ་ཁབ།)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('སྤྱི་ཟླ་དང་པོ།','སྤྱི་ཟླ་གཉིས་པ།','སྤྱི་ཟླ་གསུམ་པ།','སྤྱི་ཟླ་བཞི་པ།','སྤྱི་ཟླ་ལྔ་པ།','སྤྱི་ཟླ་དྲུག་པ།','སྤྱི་ཟླ་བདུན་པ།','སྤྱི་ཟླ་བརྒྱད་པ།','སྤྱི་ཟླ་དགུ་པ།','སྤྱི་ཟླ་བཅུ་པོ།','སྤྱི་ཟླ་བཅུ་གཅིག་པ།','སྤྱི་ཟླ་བཅུ་གཉིས་པ།','');
			$dtf->ShortMonthNames = array('ཟླ་ ༡','ཟླ་ ༢','ཟླ་ ༣','ཟླ་ ༤','ཟླ་ ༥','ཟླ་ ༦','ཟླ་ ༧','ཟླ་ ༨','ཟླ་ ༩','ཟླ་ ༡༠','ཟླ་ ༡༡','ཟླ་ ༡༢','');
			$dtf->GenitiveMonthNames = array('སྤྱི་ཟླ་དང་པོ།','སྤྱི་ཟླ་གཉིས་པ།','སྤྱི་ཟླ་གསུམ་པ།','སྤྱི་ཟླ་བཞི་པ།','སྤྱི་ཟླ་ལྔ་པ།','སྤྱི་ཟླ་དྲུག་པ།','སྤྱི་ཟླ་བདུན་པ།','སྤྱི་ཟླ་བརྒྱད་པ།','སྤྱི་ཟླ་དགུ་པ།','སྤྱི་ཟླ་བཅུ་པོ།','སྤྱི་ཟླ་བཅུ་གཅིག་པ།','སྤྱི་ཟླ་བཅུ་གཉིས་པ།','');
			$dtf->DayNames = array('གཟའ་ཉི་མ།','གཟའ་ཟླ་བ།','གཟའ་མིག་དམར།','གཟའ་ལྷག་པ།','གཟའ་ཕུར་བུ།','གཟའ་པ་སངས།','གཟའ་སྤེན་པ།');
			$dtf->ShortDayNames = array('༧','༡','༢','༣','༤','༥','༦');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','¥','CNY','¥%v','¥-%v','PRC Renminbi','མི་དམངས་ཤོག་སྒོཪ།');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('CN');
			return $ci;
		case 'sms-fi':
			$ci = new CultureInfo('sms-FI','','sms','Sami (Skolt) (Finland)','Sääm´ǩiõll (Lää´ddjânnam)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ođđee´jjmään','tä´lvvmään','pâ´zzlâšttammään','njuhččmään','vue´ssmään','ǩie´ssmään','suei´nnmään','på´rǧǧmään','čõhččmään','kålggmään','skamm´mään','rosttovmään','');
			$dtf->ShortMonthNames = array('ođjm','tä´lvv','pâzl','njuh','vue','ǩie','suei','på´r','čõh','kålg','ska','rost','');
			$dtf->GenitiveMonthNames = array('ođđee´jjmannu','tä´lvvmannu','pâ´zzlâšttammannu','njuhččmannu','vue´ssmannu','ǩie´ssmannu','suei´nnmannu','på´rǧǧmannu','čõhččmannu','kålggmannu','skamm´mannu','rosttovmannu','');
			$dtf->DayNames = array('pâ´sspei´vv','vuõssargg','mââibargg','seärad','nelljdpei´vv','piâtnâc','sue´vet');
			$dtf->ShortDayNames = array('p','v','m','s','n','p','s');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FI');
			return $ci;
		case 'as-in':
			$ci = new CultureInfo('as-IN','','as','Assamese (India)','অসমীয়া (ভাৰত)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('জানুৱাৰী','ফেব্রুৱাৰী','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগষ্ট','চেপ্টেম্বর','অক্টোবর','নবেম্বর','ডিচেম্বর','');
			$dtf->ShortMonthNames = array('জানু','ফেব্রু','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগষ্ট','চেপ্টে','অক্টো','নবে','ডিচে','');
			$dtf->GenitiveMonthNames = array('জানুৱাৰী','ফেব্রুৱাৰী','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগষ্ট','চেপ্টেম্বর','অক্টোবর','নবেম্বর','ডিচেম্বর','');
			$dtf->DayNames = array('সোমবাৰ','মঙ্গলবাৰ','বুধবাৰ','বৃহস্পতিবাৰ','শুক্রবাৰ','শনিবাৰ','ৰবিবাৰ');
			$dtf->ShortDayNames = array('সো','ম','বু','বৃ','শু','শ','র');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ট','INR','%vট','ট -%v','Indian Rupee','টকা');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v%','-%v%');
			$ci->Region = internal_getRegion('IN');
			return $ci;
		case 'ml-in':
			$ci = new CultureInfo('ml-IN','','ml','Malayalam (India)','മലയാളം (ഭാരതം)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ജനുവരി','ഫെബ്റുവരി','മാറ്ച്ച്','ഏപ്റില്','മെയ്','ജൂണ്','ജൂലൈ','ഓഗസ്ററ്','സെപ്ററംബറ്','ഒക്ടോബറ്','നവംബറ്','ഡിസംബറ്','');
			$dtf->ShortMonthNames = array('ജനുവരി','ഫെബ്റുവരി','മാറ്ച്ച്','ഏപ്റില്','മെയ്','ജൂണ്','ജൂലൈ','ഓഗസ്ററ്','സെപ്ററംബറ്','ഒക്ടോബറ്','നവംബറ്','ഡിസംബറ്','');
			$dtf->GenitiveMonthNames = array('ജനുവരി','ഫെബ്റുവരി','മാറ്ച്ച്','ഏപ്റില്','മെയ്','ജൂണ്','ജൂലൈ','ഓഗസ്ററ്','സെപ്ററംബറ്','ഒക്ടോബറ്','നവംബറ്','ഡിസംബറ്','');
			$dtf->DayNames = array('ഞായറാഴ്ച','തിങ്കളാഴ്ച','ചൊവ്വാഴ്ച','ബുധനാഴ്ച','വ്യാഴാഴ്ച','വെള്ളിയാഴ്ച','ശനിയാഴ്ച');
			$dtf->ShortDayNames = array('ഞ','ത','ച','ബ','വ','വെ','ശ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ക','INR','ക %v','ക -%v','Indian Rupee','രൂപ');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			return $ci;
		case 'en-in':
			$ci = new CultureInfo('en-IN','en','en','English (India)','English (India)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','Rs.','INR','Rs. %v','Rs. -%v','Indian Rupee;','Rupee;');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'or-in':
			$ci = new CultureInfo('or-IN','','or','Oriya (India)','ଓଡ଼ିଆ (ଭାରତ)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ଜାନୁୟାରୀ','ଫ୍ରେବୃୟାରୀ','ମାର୍ଚ୍ଚ','ଏପ୍ରିଲ୍‌','ମେ','ଜୁନ୍‌','ଜୁଲାଇ','ଅଗଷ୍ଟ','ସେପ୍ଟେମ୍ବର','ଅକ୍ଟୋବର','ନଭେମ୍ବର','(ଡିସେମ୍ବର','');
			$dtf->ShortMonthNames = array('ଜାନୁୟାରୀ','ଫ୍ରେବୃୟାରୀ','ମାର୍ଚ୍ଚ','ଏପ୍ରିଲ୍‌','ମେ','ଜୁନ୍‌','ଜୁଲାଇ','ଅଗଷ୍ଟ','ସେପ୍ଟେମ୍ବର','ଅକ୍ଟୋବର','ନଭେମ୍ବର','(ଡିସେମ୍ବର','');
			$dtf->GenitiveMonthNames = array('ଜାନୁୟାରୀ','ଫ୍ରେବୃୟାରୀ','ମାର୍ଚ୍ଚ','ଏପ୍ରିଲ୍‌','ମେ','ଜୁନ୍‌','ଜୁଲାଇ','ଅଗଷ୍ଟ','ସେପ୍ଟେମ୍ବର','ଅକ୍ଟୋବର','ନଭେମ୍ବର','(ଡିସେମ୍ବର','');
			$dtf->DayNames = array('ରବିବାର','ସୋମବାର','ମଙ୍ଗଳବାର','ବୁଧବାର','ଗୁରୁବାର','ଶୁକ୍ରବାର','ଶନିବାର');
			$dtf->ShortDayNames = array('ର','ସୋ','ମ','ବୁ','ଗୁ','ଶୁ','ଶ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','ଟ','INR','ଟ %v','ଟ -%v','Indian Rupee','ଟଙ୍କା');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			return $ci;
		case 'bn-in':
			$ci = new CultureInfo('bn-IN','','bn','Bengali (India)','বাংলা (ভারত)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('জানুয়ারী','ফেব্রুয়ারী','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর','');
			$dtf->ShortMonthNames = array('জানু.','ফেব্রু.','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগ.','সেপ্টে.','অক্টো.','নভে.','ডিসে.','');
			$dtf->GenitiveMonthNames = array('জানুয়ারী','ফেব্রুয়ারী','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর','');
			$dtf->DayNames = array('রবিবার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার');
			$dtf->ShortDayNames = array('র','স','ম','ব','ব','শ','শ');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','টা','INR','টা %v','টা -%v','Indian Rupee','টাকা');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('IN');
			return $ci;
		case 'tk-tm':
			$ci = new CultureInfo('tk-TM','','tk','Turkmen (Turkmenistan)','Türkmençe (Türkmenistan)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Ýanwar','Fewral','Mart','Aprel','Maý','lýun','lýul','Awgust','Sentýabr','Oktýabr','Noýabr','Dekabr','');
			$dtf->ShortMonthNames = array('Ýan','Few','Mart','Apr','Maý','lýun','lýul','Awg','Sen','Okt','Not','Dek','');
			$dtf->GenitiveMonthNames = array('Ýanwar','Fewral','Mart','Aprel','Maý','lýun','lýul','Awgust','Sentýabr','Oktýabr','Noýabr','Dekabr','');
			$dtf->DayNames = array('Duşenbe','Sişenbe','Çarşenbe','Penşenbe','Anna','Şenbe','Ýekşenbe');
			$dtf->ShortDayNames = array('D','S','Ç','P','A','Ş','Ý');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','m.','TMT','%vm.','-%vm.','Turkmen manat','manat');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%v%','-%v%');
			$ci->Region = internal_getRegion('TM');
			return $ci;
		case 'bs-latn-ba':
			$ci = new CultureInfo('bs-Latn-BA','','bs','Bosnian (Latin) (Bosnia and Herzegovina)','Bosanski (Bosna i Hercegovina)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','mart','april','maj','juni','juli','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','maj','jun','jul','avg','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januar','februar','mart','april','maj','juni','juli','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->DayNames = array('nedjelja','ponedjeljak','utorak','srijeda','četvrtak','petak','subota');
			$dtf->ShortDayNames = array('ne','po','ut','sr','če','pe','su');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','KM','BAM','%v KM','-%v KM','Convertible Marks','konvertibilna marka');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('BA');
			return $ci;
		case 'mt-mt':
			$ci = new CultureInfo('mt-MT','','mt','Maltese (Malta)','Malti (Malta)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Jannar','Frar','Marzu','April','Mejju','Ġunju','Lulju','Awissu','Settembru','Ottubru','Novembru','Diċembru','');
			$dtf->ShortMonthNames = array('Jan','Fra','Mar','Apr','Mej','Ġun','Lul','Awi','Set','Ott','Nov','Diċ','');
			$dtf->GenitiveMonthNames = array('Jannar','Frar','Marzu','April','Mejju','Ġunju','Lulju','Awissu','Settembru','Ottubru','Novembru','Diċembru','');
			$dtf->DayNames = array('Il-Ħadd','It-Tnejn','It-Tlieta','L-Erbgħa','Il-Ħamis','Il-Ġimgħa','Is-Sibt');
			$dtf->ShortDayNames = array('I','I','I','L','I','I','I');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','€','EUR','€%v','-€%v','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('MT');
			return $ci;
		case 'sr-cyrl-me':
			$ci = new CultureInfo('sr-Cyrl-ME','','sr','Serbian (Cyrillic) (Montenegro)','Cрпски (Црна Гора)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->ShortMonthNames = array('јан','феб','мар','апр','мај','јун','јул','авг','сеп','окт','нов','дец','');
			$dtf->GenitiveMonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->DayNames = array('недеља','понедељак','уторак','среда','четвртак','петак','субота');
			$dtf->ShortDayNames = array('не','по','ут','ср','че','пе','су');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','Еуро');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('ME');
			return $ci;
		case 'se-fi':
			$ci = new CultureInfo('se-FI','','se','Sami (Northern) (Finland)','davvisámegiella (Suopma)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ođđajagemánnu','guovvamánnu','njukčamánnu','cuoŋománnu','miessemánnu','geassemánnu','suoidnemánnu','borgemánnu','čakčamánnu','golggotmánnu','skábmamánnu','juovlamánnu','');
			$dtf->ShortMonthNames = array('ođđj','guov','njuk','cuo','mies','geas','suoi','borg','čakč','golg','skáb','juov','');
			$dtf->GenitiveMonthNames = array('ođđajagimánu','guovvamánu','njukčamánu','cuoŋománu','miessemánu','geassemánu','suoidnemánu','borgemánu','čakčamánu','golggotmánu','skábmamánu','juovlamánu','');
			$dtf->DayNames = array('sotnabeaivi','vuossárga','maŋŋebárga','gaskavahkku','duorastat','bearjadat','lávvardat');
			$dtf->ShortDayNames = array('s','m','d','g','d','b','l');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','€','EUR','%v €','-%v €','Euro','euro');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('FI');
			return $ci;
		case 'zu-za':
			$ci = new CultureInfo('zu-ZA','','zu','isiZulu (South Africa)','isiZulu (iNingizimu Afrika)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('uMasingana','uNhlolanja','uNdasa','uMbaso','uNhlaba','uNhlangulana','uNtulikazi','uNcwaba','uMandulo','uMfumfu','uLwezi','uZibandlela','');
			$dtf->ShortMonthNames = array('Mas.','Nhlo.','Nda.','Mba.','Nhla.','Nhlang.','Ntu.','Ncwa.','Man.','Mfu.','Lwe.','Zib.','');
			$dtf->GenitiveMonthNames = array('uMasingana','uNhlolanja','uNdasa','uMbaso','uNhlaba','uNhlangulana','uNtulikazi','uNcwaba','uMandulo','uMfumfu','uLwezi','uZibandlela','');
			$dtf->DayNames = array('iSonto','uMsombuluko','uLwesibili','uLwesithathu','uLwesine','uLwesihlanu','uMgqibelo');
			$dtf->ShortDayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','R','ZAR','R %v','R-%v','South African Rand','Rand');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('ZA');
			return $ci;
		case 'xh-za':
			$ci = new CultureInfo('xh-ZA','','xh','isiXhosa (South Africa)','isiXhosa (uMzantsi Afrika)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Mqungu','Mdumba','Kwindla','Tshazimpuzi','Canzibe','Silimela','Khala','Thupha','Msintsi','Dwarha','Nkanga','Mnga','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('Mqungu','Mdumba','Kwindla','Tshazimpuzi','Canzibe','Silimela','Khala','Thupha','Msintsi','Dwarha','Nkanga','Mnga','');
			$dtf->DayNames = array('iCawa','uMvulo','uLwesibini','uLwesithathu','uLwesine','uLwesihlanu','uMgqibelo');
			$dtf->ShortDayNames = array('Ca','Mv','Lb','Lt','Ln','Lh','Mg');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','R','ZAR','R %v','R-%v','South African Rand','Rand');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('ZA');
			return $ci;
		case 'tn-za':
			$ci = new CultureInfo('tn-ZA','','tn','Setswana (South Africa)','Setswana (Aforika Borwa)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Ferikgong','Tlhakole','Mopitloe','Moranang','Motsheganong','Seetebosigo','Phukwi','Phatwe','Lwetse','Diphalane','Ngwanatsele','Sedimothole','');
			$dtf->ShortMonthNames = array('Fer.','Tlhak.','Mop.','Mor.','Motsh.','Seet.','Phukw.','Phatw.','Lwets.','Diph.','Ngwan.','Sed.','');
			$dtf->GenitiveMonthNames = array('Ferikgong','Tlhakole','Mopitloe','Moranang','Motsheganong','Seetebosigo','Phukwi','Phatwe','Lwetse','Diphalane','Ngwanatsele','Sedimothole','');
			$dtf->DayNames = array('Latshipi','Mosupologo','Labobedi','Laboraro','Labone','Labotlhano','Lamatlhatso');
			$dtf->ShortDayNames = array('Lp','Ms','Lb','Lr','Ln','Lt','Lm');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','R','ZAR','R %v','R-%v','South African Rand','Rand');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('ZA');
			return $ci;
		case 'hsb-de':
			$ci = new CultureInfo('hsb-DE','','hsb','Upper Sorbian (Germany)','Hornjoserbšćina (Němska)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','měrc','apryl','meja','junij','julij','awgust','september','oktober','nowember','december','');
			$dtf->ShortMonthNames = array('jan','feb','měr','apr','mej','jun','jul','awg','sep','okt','now','dec','');
			$dtf->GenitiveMonthNames = array('januara','februara','měrca','apryla','meje','junija','julija','awgusta','septembra','oktobra','nowembra','decembra','');
			$dtf->DayNames = array('njedźela','póndźela','wutora','srjeda','štwórtk','pjatk','sobota');
			$dtf->ShortDayNames = array('n','p','w','s','š','p','s');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','Euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('DE');
			return $ci;
		case 'bs-cyrl-ba':
			$ci = new CultureInfo('bs-Cyrl-BA','','bs','Bosnian (Cyrillic) (Bosnia and Herzegovina)','Босански (Босна и Херцеговина)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->ShortMonthNames = array('јан','феб','мар','апр','мај','јун','јул','авг','сеп','окт','нов','дец','');
			$dtf->GenitiveMonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->DayNames = array('недјеља','понедјељак','уторак','сриједа','четвртак','петак','субота');
			$dtf->ShortDayNames = array('н','п','у','с','ч','п','с');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','КМ','BAM','%v КМ','-%v КМ','Convertible Marks','конвертибилна марка');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('BA');
			return $ci;
		case 'tg-cyrl-tj':
			$ci = new CultureInfo('tg-Cyrl-TJ','','tg','Tajik (Cyrillic) (Tajikistan)','Тоҷикӣ (Тоҷикистон)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Январ','Феврал','Март','Апрел','Май','Июн','Июл','Август','Сентябр','Октябр','Ноябр','Декабр','');
			$dtf->ShortMonthNames = array('Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек','');
			$dtf->GenitiveMonthNames = array('январи','феврали','марти','апрели','маи','июни','июли','августи','сентябри','октябри','ноябри','декабри','');
			$dtf->DayNames = array('Яш','Душанбе','Сешанбе','Чоршанбе','Панҷшанбе','Ҷумъа','Шанбе');
			$dtf->ShortDayNames = array('Яш','Дш','Сш','Чш','Пш','Ҷм','Шн');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',';',' ','т.р.','TJS','%v т.р.','-%v т.р.','Ruble','рубл');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('TJ');
			return $ci;
		case 'sr-latn-ba':
			$ci = new CultureInfo('sr-Latn-BA','','sr','Serbian (Latin) (Bosnia and Herzegovina)','Srpski (Bosna i Hercegovina)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','maj','jun','jul','avg','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->DayNames = array('nedelja','ponedeljak','utorak','sreda','četvrtak','petak','subota');
			$dtf->ShortDayNames = array('ne','po','ut','sr','če','pe','su');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','KM','BAM','%v KM','-%v KM','Convertible Marks','konvertibilna marka');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('BA');
			return $ci;
		case 'smj-no':
			$ci = new CultureInfo('smj-NO','','smj','Sami (Lule) (Norway)','Julevusámegiella (Vuodna)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ådåjakmánno','guovvamánno','sjnjuktjamánno','vuoratjismánno','moarmesmánno','biehtsemánno','sjnjilltjamánno','bårggemánno','ragátmánno','gålgådismánno','basádismánno','javllamánno','');
			$dtf->ShortMonthNames = array('ådåj','guov','snju','vuor','moar','bieh','snji','bårg','ragá','gålg','basá','javl','');
			$dtf->GenitiveMonthNames = array('ådåjakmáno','guovvamáno','sjnjuktjamáno','vuoratjismáno','moarmesmáno','biehtsemáno','sjnjilltjamáno','bårggemáno','ragátmáno','gålgådismáno','basádismáno','javllamáno','');
			$dtf->DayNames = array('sådnåbiejvve','mánnodahka','dijstahka','gasskavahkko','duorastahka','bierjjedahka','lávvodahka');
			$dtf->ShortDayNames = array('s','m','d','g','d','b','l');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',',' ','kr','NOK','kr %v','kr -%v','Norwegian Krone','kråvnnå');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','%%v','-%v%');
			$ci->Region = internal_getRegion('NO');
			return $ci;
		case 'rm-ch':
			$ci = new CultureInfo('rm-CH','','rm','Romansh (Switzerland)','Rumantsch (Svizra)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('schaner','favrer','mars','avrigl','matg','zercladur','fanadur','avust','settember','october','november','december','');
			$dtf->ShortMonthNames = array('schan','favr','mars','avr','matg','zercl','fan','avust','sett','oct','nov','dec','');
			$dtf->GenitiveMonthNames = array('schaner','favrer','mars','avrigl','matg','zercladur','fanadur','avust','settember','october','november','december','');
			$dtf->DayNames = array('dumengia','glindesdi','mardi','mesemna','gievgia','venderdi','sonda');
			$dtf->ShortDayNames = array('du','gli','ma','me','gie','ve','so');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.','\'','fr.','CHF','fr. %v','fr.-%v','Swiss Franc','Franc svizzer');
			$ci->NumberFormat = new NumberFormat('2','.','\'','-%v');
			$ci->PercentFormat = new PercentFormat('2','.','\'','%%v','-%v%');
			$ci->Region = internal_getRegion('CH');
			return $ci;
		case 'smj-se':
			$ci = new CultureInfo('smj-SE','','smj','Sami (Lule) (Sweden)','Julevusámegiella (Svierik)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('ådåjakmánno','guovvamánno','sjnjuktjamánno','vuoratjismánno','moarmesmánno','biehtsemánno','sjnjilltjamánno','bårggemánno','ragátmánno','gålgådismánno','basádismánno','javllamánno','');
			$dtf->ShortMonthNames = array('ådåj','guov','snju','vuor','moar','bieh','snji','bårg','ragá','gålg','basá','javl','');
			$dtf->GenitiveMonthNames = array('ådåjakmáno','guovvamáno','sjnjuktjamáno','vuoratjismáno','moarmesmáno','biehtsemáno','sjnjilltjamáno','bårggemáno','ragátmáno','gålgådismáno','basádismáno','javllamáno','');
			$dtf->DayNames = array('ájllek','mánnodahka','dijstahka','gasskavahkko','duorastahka','bierjjedahka','lávvodahka');
			$dtf->ShortDayNames = array('á','m','d','g','d','b','l');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','kr','SEK','%v kr','-%v kr','Swedish Krona','kråvnnå');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('SE');
			return $ci;
		case 'quz-ec':
			$ci = new CultureInfo('quz-EC','','quz','Quechua (Ecuador)','Runasimi (Ecuador Suyu)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Qulla puquy','Hatun puquy','Pauqar waray','ayriwa','Aymuray','Inti raymi','Anta Sitwa','Qhapaq Sitwa','Uma raymi','Kantaray','Ayamarq\'a','Kapaq Raymi','');
			$dtf->ShortMonthNames = array('Qul','Hat','Pau','ayr','Aym','Int','Ant','Qha','Uma','Kan','Aya','Kap','');
			$dtf->GenitiveMonthNames = array('Qulla puquy','Hatun puquy','Pauqar waray','ayriwa','Aymuray','Inti raymi','Anta Sitwa','Qhapaq Sitwa','Uma raymi','Kantaray','Ayamarq\'a','Kapaq Raymi','');
			$dtf->DayNames = array('intichaw','killachaw','atipachaw','quyllurchaw','Ch\' askachaw','Illapachaw','k\'uychichaw');
			$dtf->ShortDayNames = array('d','k','a','m','h','b','k');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','$','USD','$ %v','($ %v)','US Dollar','US Dolar');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','%%v','-%v%');
			$ci->Region = internal_getRegion('EC');
			return $ci;
		case 'quz-pe':
			$ci = new CultureInfo('quz-PE','','quz','Quechua (Peru)','Runasimi (Peru Suyu)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('Qulla puquy','Hatun puquy','Pauqar waray','ayriwa','Aymuray','Inti raymi','Anta Sitwa','Qhapaq Sitwa','Uma raymi','Kantaray','Ayamarq\'a','Kapaq Raymi','');
			$dtf->ShortMonthNames = array('Qul','Hat','Pau','ayr','Aym','Int','Ant','Qha','Uma','Kan','Aya','Kap','');
			$dtf->GenitiveMonthNames = array('Qulla puquy','Hatun puquy','Pauqar waray','ayriwa','Aymuray','Inti raymi','Anta Sitwa','Qhapaq Sitwa','Uma raymi','Kantaray','Ayamarq\'a','Kapaq Raymi','');
			$dtf->DayNames = array('intichaw','killachaw','atipachaw','quyllurchaw','Ch\' askachaw','Illapachaw','k\'uychichaw');
			$dtf->ShortDayNames = array('d','k','a','m','h','b','k');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','S/.','PEN','S/. %v','S/. -%v','Peruvian Nuevo Sol','Nuevo Sol');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%%v','-%v%');
			$ci->Region = internal_getRegion('PE');
			return $ci;
		case 'hr-ba':
			$ci = new CultureInfo('hr-BA','hr','hr','Croatian (Latin) (Bosnia and Herzegovina)','Hrvatski (Bosna i Hercegovina)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('siječanj','veljača','ožujak','travanj','svibanj','lipanj','srpanj','kolovoz','rujan','listopad','studeni','prosinac','');
			$dtf->ShortMonthNames = array('sij','vlj','ožu','tra','svi','lip','srp','kol','ruj','lis','stu','pro','');
			$dtf->GenitiveMonthNames = array('siječnja','veljače','ožujka','travnja','svibnja','lipnja','srpnja','kolovoza','rujna','listopada','studenog','prosinca','');
			$dtf->DayNames = array('nedjelja','ponedjeljak','utorak','srijeda','četvrtak','petak','subota');
			$dtf->ShortDayNames = array('ne','po','ut','sr','če','pe','su');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','KM','BAM','%v KM','-%v KM','Convertible Marks','konvertibilna marka');
			$ci->NumberFormat = new NumberFormat('2',',','.','- %v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%%v');
			$ci->Region = internal_getRegion('BA');
			$ci->Parent = internal_getLanguage('hr');
			return $ci;
		case 'sr-latn-me':
			$ci = new CultureInfo('sr-Latn-ME','','sr','Serbian (Latin) (Montenegro)','Srpski (Crna Gora)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->ShortMonthNames = array('jan','feb','mar','apr','maj','jun','jul','avg','sep','okt','nov','dec','');
			$dtf->GenitiveMonthNames = array('januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar','');
			$dtf->DayNames = array('nedelja','ponedeljak','utorak','sreda','četvrtak','petak','subota');
			$dtf->ShortDayNames = array('ne','po','ut','sr','če','pe','su');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','€','EUR','%v €','-%v €','Euro','Euro');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('ME');
			return $ci;
		case 'sma-se':
			$ci = new CultureInfo('sma-SE','','sma','Sami (Southern) (Sweden)','Åarjelsaemiengiele (Sveerje)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('tsïengele','goevte','njoktje','voerhtje','suehpede','ruffie','snjaltje','mïetske','skïerede','golke','rahka','goeve','');
			$dtf->ShortMonthNames = array('tsïen','goevt','njok','voer','sueh','ruff','snja','mïet','skïer','golk','rahk','goev','');
			$dtf->GenitiveMonthNames = array('tsïengelen','goevten','njoktjen','voerhtjen','suehpeden','ruffien','snjaltjen','mïetsken','skïereden','golken','rahkan','goeven','');
			$dtf->DayNames = array('aejlege','måanta','dæjsta','gaskevåhkoe','duarsta','bearjadahke','laavvardahke');
			$dtf->ShortDayNames = array('a','m','d','g','d','b','l');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','kr','SEK','%v kr','-%v kr','Swedish Krona','kråvnoe');
			$ci->NumberFormat = new NumberFormat('2',',',' ','-%v');
			$ci->PercentFormat = new PercentFormat('2',',',' ','% %v','-%v%');
			$ci->Region = internal_getRegion('SE');
			return $ci;
		case 'en-sg':
			$ci = new CultureInfo('en-SG','en','en','English (Singapore)','English (Singapore)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->ShortMonthNames = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','');
			$dtf->GenitiveMonthNames = array('January','February','March','April','May','June','July','August','September','October','November','December','');
			$dtf->DayNames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$dtf->ShortDayNames = array('S','M','T','W','T','F','S');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','SGD','$%v','-$%v','Singapore Dollar','Singapore Dollar');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('SG');
			$ci->Parent = internal_getLanguage('en');
			return $ci;
		case 'ug-cn':
			$ci = new CultureInfo('ug-CN','','ug','Uyghur (People\'s Republic of China)','ئۇيغۇرچە (جۇڭخۇا خەلق جۇمھۇرىيىتى)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('1-ئاي','2-ئاي','3-ئاي','4-ئاي','5-ئاي','6-ئاي','7-ئاي','8-ئاي','9-ئاي','10-ئاي','11-ئاي','12-ئاي','');
			$dtf->ShortMonthNames = array('1-ئاي','2-ئاي','3-ئاي','4-ئاي','5-ئاي','6-ئاي','7-ئاي','8-ئاي','9-ئاي','10-ئاي','11-ئاي','12-ئاي','');
			$dtf->GenitiveMonthNames = array('1-ئاي','2-ئاي','3-ئاي','4-ئاي','5-ئاي','6-ئاي','7-ئاي','8-ئاي','9-ئاي','10-ئاي','11-ئاي','12-ئاي','');
			$dtf->DayNames = array('يەكشەنبە','دۈشەنبە','سەيشەنبە','چارشەنبە','پەيشەنبە','جۈمە','شەنبە');
			$dtf->ShortDayNames = array('ي','د','س','چ','پ','ج','ش');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','¥','CNY','¥%v','¥-%v','PRC Renminbi','خەلق پۇلى');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('CN');
			return $ci;
		case 'sr-cyrl-ba':
			$ci = new CultureInfo('sr-Cyrl-BA','','sr','Serbian (Cyrillic) (Bosnia and Herzegovina)','Cрпски (Босна и Херцеговина)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->ShortMonthNames = array('јан','феб','мар','апр','мај','јун','јул','авг','сеп','окт','нов','дец','');
			$dtf->GenitiveMonthNames = array('јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','');
			$dtf->DayNames = array('недеља','понедељак','уторак','среда','четвртак','петак','субота');
			$dtf->ShortDayNames = array('н','п','у','с','ч','п','с');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2',',','.','КМ','BAM','%v КМ','-%v КМ','Convertible Marks','конвертибилна марка');
			$ci->NumberFormat = new NumberFormat('2',',','.','-%v');
			$ci->PercentFormat = new PercentFormat('2',',','.','% %v','-%v%');
			$ci->Region = internal_getRegion('BA');
			return $ci;
		case 'es-us':
			$ci = new CultureInfo('es-US','es','es','Spanish (United States)','Español (Estados Unidos)','0');
			$dtf = internal_getDateTimeFormat($cultureCode);
			$dtf->MonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->ShortMonthNames = array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic','');
			$dtf->GenitiveMonthNames = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre','');
			$dtf->DayNames = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
			$dtf->ShortDayNames = array('do','lu','ma','mi','ju','vi','sa');
			$ci->DateTimeFormat = $dtf;
			$ci->CurrencyFormat = new CurrencyFormat('2','.',',','$','USD','$%v','-$%v','US Dollar','Dólar de EE.UU.');
			$ci->NumberFormat = new NumberFormat('2','.',',','-%v');
			$ci->PercentFormat = new PercentFormat('2','.',',','%v %','-%v%');
			$ci->Region = internal_getRegion('US');
			$ci->Parent = internal_getLanguage('es');
			return $ci;
			
		case '':
			return false;
			break;
            
        default:
            // this might be only a language, so get the default region
            $regions = internal_getRegionsForLanguage($cultureCode);
            if($regions !== false)
                return $regions[0]->DefaultCulture();
            else
            {
				// this could also be a region (i.e. "AU")
				$codes = internal_getCultureCodeFromRegion($cultureCode);
                if($codes !== false)
					return internal_getCultureInfo($codes[0]);
				else
				{
					log_trace("unknown culture: $cultureCode");
					return false;
				} 
            }                
            break;
	}
	return false;
}

/**
 * @internal 
 */
function internal_getDateTimeFormat($cultureCode)
{
	switch( strtolower( str_replace("_", "-", $cultureCode)) )
	{
		case 'en-us':
			return new DateTimeFormat('0','d4, M4 d5, y4 h1:m2:s2 t2','d4, M4 d5, y4','h1:m2:s2 t2','M4 d5','M1/d1/y4','h1:m2 t2','M4, y4','am','pm');
		case 'de-de':
			return new DateTimeFormat('1','d4, d1. M4 y4 H2:m2:s2','d4, d1. M4 y4','H2:m2:s2','d1. M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'it-it':
			return new DateTimeFormat('1','d4 d1 M4 y4 H1.m2.s2','d4 d1 M4 y4','H1.m2.s2','d1. M4','d2/M2/y4','H1.m2','M4 y4','','');
		case 'ja-jp':
			return new DateTimeFormat('0','y4\'年\'M1\'月\'d1\'日\' H1:m2:s2','y4\'年\'M1\'月\'d1\'日\'','H1:m2:s2','M1\'月\'d1\'日\'','y4/M2/d2','H1:m2','y4\'年\'M1\'月\'','午前','午後');
		case 'fr-fr':
			return new DateTimeFormat('1','d4 d1 M4 y4 H2:m2:s2','d4 d1 M4 y4','H2:m2:s2','d1. M4','d2/M2/y4','H2:m2','M4 y4','','');
		case 'ar-sa':
			return new DateTimeFormat('6','d2/M4/y4 h2:m2:s2 t2','d2/M4/y4','h2:m2:s2 t2','d2 M4','d2/M2/yy','h2:m2 t2','M4, y4','ص','م');
		case 'bg-bg':
			return new DateTimeFormat('1','d2 M4 y4 г. H2:m2:s2','d2 M4 y4 г.','H2:m2:s2','d2 M4','d2.M1.y4 г.','H2:m2','M4 y4 г.','','');
		case 'ca-es':
			return new DateTimeFormat('1','d4, d1 / M4 / y4 H2:m2:s2','d4, d1 / M4 / y4','H2:m2:s2','d2 M4','d2/M2/y4','H2:m2','M4 / y4','','');
		case 'zh-tw':
			return new DateTimeFormat('0','y4\'年\'M1\'月\'d1\'日\' t2 h2:m2:s2','y4\'年\'M1\'月\'d1\'日\'','t2 h2:m2:s2','M1\'月\'d1\'日\'','y4/M1/d1','t2 h2:m2','y4\'年\'M1\'月\'','上午','下午');
		case 'cs-cz':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','d2 M4','d1.M1.y4','H1:m2','M4 y4','dop.','odp.');
		case 'da-dk':
			return new DateTimeFormat('1','d1. M4 y4 H2:m2:s2','d1. M4 y4','H2:m2:s2','d1. M4','d2-M2-y4','H2:m2','M4 y4','','');
		case 'el-gr':
			return new DateTimeFormat('1','d4, d1 M4 y4 h1:m2:s2 t2','d4, d1 M4 y4','h1:m2:s2 t2','d2 M4','d1/M1/y4','h1:m2 t2','M4 y4','πμ','μμ');
		case 'fi-fi':
			return new DateTimeFormat('1','d1. M4\'ta \'y4 H1:m2:s2','d1. M4\'ta \'y4','H1:m2:s2','d1. M4\'ta\'','d1.M1.y4','H1:m2','M4 y4','','');
		case 'he-il':
			return new DateTimeFormat('0','d4 d2 M4 y4 H2:m2:s2','d4 d2 M4 y4','H2:m2:s2','d2 M4','d2/M2/y4','H2:m2','M4 y4','AM','PM');
		case 'hu-hu':
			return new DateTimeFormat('1','y4. M4 d1. H1:m2:s2','y4. M4 d1.','H1:m2:s2','M4 d1.','y4. M2. d2.','H1:m2','y4. M4','de.','du.');
		case 'is-is':
			return new DateTimeFormat('1','d1. M4 y4 H2:m2:s2','d1. M4 y4','H2:m2:s2','d1. M4','d1.M1.y4','H2:m2','M4 y4','','');
		case 'ko-kr':
			return new DateTimeFormat('0','y4\'년\' M1\'월\' d1\'일\' d4 t2 h1:m2:s2','y4\'년\' M1\'월\' d1\'일\' d4','t2 h1:m2:s2','M1\'월\' d1\'일\'','y4-M2-d2','t2 h1:m2','y4\'년\' M1\'월\'','오전','오후');
		case 'nl-nl':
			return new DateTimeFormat('1','d4 d1 M4 y4 H1:m2:s2','d4 d1 M4 y4','H1:m2:s2','d2 M4','d1-M1-y4','H1:m2','M4 y4','','');
		case 'nb-no':
			return new DateTimeFormat('1','d1. M4 y4 H2:m2:s2','d1. M4 y4','H2:m2:s2','d1. M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'pl-pl':
			return new DateTimeFormat('1','d1 M4 y4 H2:m2:s2','d1 M4 y4','H2:m2:s2','d1 M4','y4-M2-d2','H2:m2','M4 y4','','');
		case 'pt-br':
			return new DateTimeFormat('0','d4, d1 de M4 de y4 H1:m2:s2','d4, d1 de M4 de y4','H1:m2:s2','d2 de M4','d1/M1/y4','H1:m2','M4 de y4','','');
		case 'ro-ro':
			return new DateTimeFormat('1','d1 M4 y4 H2:m2:s2','d1 M4 y4','H2:m2:s2','d1 M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'ru-ru':
			return new DateTimeFormat('1','d1 M4 y4 г. H1:m2:s2','d1 M4 y4 г.','H1:m2:s2','M4 d2','d2.M2.y4','H1:m2','M4 y4 г.','','');
		case 'hr-hr':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','d1. M4','d1.M1.y4','H1:m2','M4, y4','','');
		case 'sk-sk':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','d2 M4','d1. M1. y4','H1:m2','M4 y4','','');
		case 'sq-al':
			return new DateTimeFormat('1','y4-M2-d2 h1:m2:s2.t2','y4-M2-d2','h1:m2:s2.t2','M4 d2','y4-M2-d2','h1:m2.t2','y4-M2','PD','MD');
		case 'sv-se':
			return new DateTimeFormat('1','den d1 M4 y4 H2:m2:s2','den d1 M4 y4','H2:m2:s2','den d1 M4','y4-M2-d2','H2:m2','M4 y4','','');
		case 'th-th':
			return new DateTimeFormat('1','d1 M4 y4 H1:m2:s2','d1 M4 y4','H1:m2:s2','d2 M4','d1/M1/y4','H1:m2','M4 y4','AM','PM');
		case 'tr-tr':
			return new DateTimeFormat('1','d2 M4 y4 d4 H2:m2:s2','d2 M4 y4 d4','H2:m2:s2','d2 M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'ur-pk':
			return new DateTimeFormat('1','d2 M4, y4 h1:m2:s2 t2','d2 M4, y4','h1:m2:s2 t2','d2 M4','d2/M2/y4','h1:m2 t2','M4, y4','AM','PM');
		case 'id-id':
			return new DateTimeFormat('1','d2 M4 y4 H1:m2:s2','d2 M4 y4','H1:m2:s2','d2 M4','d2/M2/y4','H1:m2','M4 y4','','');
		case 'uk-ua':
			return new DateTimeFormat('1','d1 M4 y4 р. H1:m2:s2','d1 M4 y4 р.','H1:m2:s2','d1 M4','d2.M2.y4','H1:m2','M4 y4 р.','','');
		case 'be-by':
			return new DateTimeFormat('1','d1 M4 y4 H1:m2:s2','d1 M4 y4','H1:m2:s2','d1 M4','d2.M2.y4','H1:m2','M4 y4','','');
		case 'sl-si':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','d1. M4','d1.M1.y4','H1:m2','M4 y4','','');
		case 'et-ee':
			return new DateTimeFormat('1','d1. M4 y4. a. H1:m2:s2','d1. M4 y4. a.','H1:m2:s2','d1. M4','d1.M2.y4','H1:m2','M4 y4. a.','EL','PL');
		case 'lv-lv':
			return new DateTimeFormat('1','d4, y4. gada d1. M4 H1:m2:s2','d4, y4. gada d1. M4','H1:m2:s2','d1. M4','y4.M2.d2.','H1:m2','y4. M4','','');
		case 'lt-lt':
			return new DateTimeFormat('1','y4 m. M4 d1 d1. H2:m2:s2','y4 m. M4 d1 d1.','H2:m2:s2','M4 d1 d1.','y4.M2.d2','H2:m2','y4 m. M4','','');
		case 'fa-ir':
			return new DateTimeFormat('0','d4, M4 d2, y4 h2:m2:s2 t2','d4, M4 d2, y4','h2:m2:s2 t2','M4 d2','M2/d2/y4','h2:m2 t2','M4, y4','ق.ظ','ب.ظ');
		case 'vi-vn':
			return new DateTimeFormat('1','d2 M4 y4 h1:m2:s2 t2','d2 M4 y4','h1:m2:s2 t2','d2 M4','d2/M2/y4','h1:m2 t2','M4 y4','SA','CH');
		case 'hy-am':
			return new DateTimeFormat('1','d1 M4, y4 H1:m2:s2','d1 M4, y4','H1:m2:s2','d1 M4','d2.M2.y4','H1:m2','M4, y4','','');
		case 'az-latn-az':
			return new DateTimeFormat('1','d1 M4 y4 H1:m2:s2','d1 M4 y4','H1:m2:s2','d1 M4','d2.M2.y4','H1:m2','M4 y4','','');
		case 'eu-es':
			return new DateTimeFormat('1','d4, y4.eko M4\'k d1 H2:m2:s2','d4, y4.eko M4\'k d1','H2:m2:s2','M4 d2','y4/M2/d2','H2:m2','y4.eko M4','','');
		case 'mk-mk':
			return new DateTimeFormat('1','d4, d2 M4 y4 H2:m2:s2','d4, d2 M4 y4','H2:m2:s2','d2 M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'af-za':
			return new DateTimeFormat('0','d2 M4 y4 h2:m2:s2 t2','d2 M4 y4','h2:m2:s2 t2','d2 M4','y4/M2/d2','h2:m2 t2','M4 y4','','nm');
		case 'ka-ge':
			return new DateTimeFormat('1','y4 \'წლის\' d2 M2, d4 H1:m2:s2','y4 \'წლის\' d2 M2, d4','H1:m2:s2','d2 M2','d2.M2.y4','H1:m2','M4 y4','','');
		case 'fo-fo':
			return new DateTimeFormat('1','d1. M4 y4 H2.m2.s2','d1. M4 y4','H2.m2.s2','d1. M4','d2-M2-y4','H2.m2','M4 y4','','');
		case 'hi-in':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','d2 M4','d2-M2-y4','H2:m2','M4, y4','पूर्वाह्न','अपराह्न');
		case 'ms-my':
			return new DateTimeFormat('1','d2 M4 y4 H1:m2:s2','d2 M4 y4','H1:m2:s2','d2 M4','d2/M2/y4','H1:m2','M4 y4','','');
		case 'kk-kz':
			return new DateTimeFormat('1','d1 M4 y4 \'ж.\' H1:m2:s2','d1 M4 y4 \'ж.\'','H1:m2:s2','d1 M4','d2.M2.y4','H1:m2','M4 y4','','');
		case 'ky-kg':
			return new DateTimeFormat('1','d1\'-\'M4 y4\'-ж.\' H1:m2:s2','d1\'-\'M4 y4\'-ж.\'','H1:m2:s2','d1 M4','d2.M2.yy','H1:m2','M4 y4\'-ж.\'','','');
		case 'sw-ke':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','M1/d1/y4','h1:m2 t2','M4, y4','AM','PM');
		case 'uz-latn-uz':
			return new DateTimeFormat('1','y4 \'yil\' d1-M4 H2:m2:s2','y4 \'yil\' d1-M4','H2:m2:s2','d1-M4','d2/M2 y4','H2:m2','M4 y4','','');
		case 't2-ru':
			return new DateTimeFormat('1','d1 M4 y4 H1:m2:s2','d1 M4 y4','H1:m2:s2','d1 M4','d2.M2.y4','H1:m2','M4 y4','','');
		case 'pa-in':
			return new DateTimeFormat('1','d2 M4 y4 d4 t2 h2:m2:s2','d2 M4 y4 d4','t2 h2:m2:s2','d2 M4','d2-M2-yy','t2 h2:m2','M4, y4','ਸਵੇਰੇ','ਸ਼ਾਮ');
		case 'gu-in':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','d2 M4','d2-M2-yy','H2:m2','M4, y4','પૂર્વ મધ્યાહ્ન','ઉત્તર મધ્યાહ્ન');
		case 'ta-in':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','d2 M4','d2-M2-y4','H2:m2','M4 y4','காலை','மாலை');
		case 'te-in':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','d2 M4','d2-M2-yy','H2:m2','M4, y4','పూర్వాహ్న','అపరాహ్న');
		case 'kn-in':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','d2 M4','d2-M2-yy','H2:m2','M4, y4','ಪೂರ್ವಾಹ್ನ','ಅಪರಾಹ್ನ');
		case 'mr-in':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','d2 M4','d2-M2-y4','H2:m2','M4, y4','म.पू.','म.नं.');
		case 'sa-in':
			return new DateTimeFormat('0','d2 M4 y4 d4 H2:m2:s2','d2 M4 y4 d4','H2:m2:s2','d2 M4','d2-M2-y4','H2:m2','M4, y4','पूर्वाह्न','अपराह्न');
		case 'mn-mn':
			return new DateTimeFormat('1','y4 \'оны\' M4 d1 H1:m2:s2','y4 \'оны\' M4 d1','H1:m2:s2','d1 M4','yy.M2.d2','H1:m2','y4 \'он\' M4','','');
		case 'gl-es':
			return new DateTimeFormat('1','d4, d2\' de \'M4\' de \'y4 H1:m2:s2','d4, d2\' de \'M4\' de \'y4','H1:m2:s2','d2 M4','d2/M2/yy','H1:m2','M4\' de \'y4','a.m.','p.m.');
		case 'kok-in':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','d2 M4','d2-M2-y4','H2:m2','M4, y4','म.पू.','म.नं.');
		case 'syr-sy':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ܩ.ܛ','ܒ.ܛ');
		case 'dv-mv':
			return new DateTimeFormat('0','d2/M4/y4 H2:m2:s2','d2/M4/y4','H2:m2:s2','d2 M4','d2/M2/yy','H2:m2','M4, y4','މކ','މފ');
		case 'ar-iq':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'zh-cn':
			return new DateTimeFormat('0','y4\'年\'M1\'月\'d1\'日\' H1:m2:s2','y4\'年\'M1\'月\'d1\'日\'','H1:m2:s2','M1\'月\'d1\'日\'','y4/M1/d1','H1:m2','y4\'年\'M1\'月\'','上午','下午');
		case 'de-ch':
			return new DateTimeFormat('1','d4, d1. M4 y4 H2:m2:s2','d4, d1. M4 y4','H2:m2:s2','d2 M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'en-gb':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','d2 M4','d2/M2/y4','H2:m2','M4 y4','AM','PM');
		case 'es-mx':
			return new DateTimeFormat('0','d4, d2 de M4 de y4 h2:m2:s2 t2','d4, d2 de M4 de y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4 de y4','a.m.','p.m.');
		case 'fr-be':
			return new DateTimeFormat('1','d4 d1 M4 y4 H1:m2:s2','d4 d1 M4 y4','H1:m2:s2','d1 M4','d1/M2/y4','H1:m2','M4 y4','','');
		case 'it-ch':
			return new DateTimeFormat('1','d4, d1. M4 y4 H2:m2:s2','d4, d1. M4 y4','H2:m2:s2','d1. M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'nl-be':
			return new DateTimeFormat('1','d4 d1 M4 y4 H1:m2:s2','d4 d1 M4 y4','H1:m2:s2','d2 M4','d1/M2/y4','H1:m2','M4 y4','','');
		case 'nn-no':
			return new DateTimeFormat('1','d1. M4 y4 H2:m2:s2','d1. M4 y4','H2:m2:s2','d1. M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'pt-pt':
			return new DateTimeFormat('1','d4, d1\' de \'M4\' de \'y4 H1:m2:s2','d4, d1\' de \'M4\' de \'y4','H1:m2:s2','d1/M1','d2-M2-y4','H1:m2','M4\' de \'y4','','');
		case 'sr-latn-cs':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','d1. M4','d1.M1.y4','H1:m2','M4 y4','','');
		case 'sv-fi':
			return new DateTimeFormat('1','\'den \'d1 M4 y4 H2:m2:s2','\'den \'d1 M4 y4','H2:m2:s2','\'den \'d1 M4','d1.M1.y4','H2:m2','M4 y4','','');
		case 'az-cyrl-az':
			return new DateTimeFormat('1','d1 M4 y4 H1:m2:s2','d1 M4 y4','H1:m2:s2','d1 M4','d2.M2.y4','H1:m2','M4 y4','','');
		case 'ms-bn':
			return new DateTimeFormat('1','d2 M4 y4 H1:m2:s2','d2 M4 y4','H1:m2:s2','d2 M4','d2/M2/y4','H1:m2','M4 y4','','');
		case 'uz-cyrl-uz':
			return new DateTimeFormat('1','y4 \'йил\' d1-M4 H2:m2:s2','y4 \'йил\' d1-M4','H2:m2:s2','d1-M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'ar-eg':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'zh-hk':
			return new DateTimeFormat('0','d4, d1 M4, y4 H1:m2:s2','d4, d1 M4, y4','H1:m2:s2','d1 M4','d1/M1/y4','H1:m2','M4, y4','','');
		case 'de-at':
			return new DateTimeFormat('1','d4, d2. M4 y4 H2:m2:s2','d4, d2. M4 y4','H2:m2:s2','d2 M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'en-au':
			return new DateTimeFormat('1','d4, d1 M4 y4 h1:m2:s2 t2','d4, d1 M4 y4','h1:m2:s2 t2','d2 M4','d1/M2/y4','h1:m2 t2','M4 y4','AM','PM');
		case 'es-es':
			return new DateTimeFormat('1','d4, d2\' de \'M4\' de \'y4 H1:m2:s2','d4, d2\' de \'M4\' de \'y4','H1:m2:s2','d2 M4','d2/M2/y4','H1:m2','M4\' de \'y4','','');
		case 'fr-ca':
			return new DateTimeFormat('0','d1 M4 y4 H2:m2:s2','d1 M4 y4','H2:m2:s2','d1 M4','y4-M2-d2','H2:m2','M4, y4','','');
		case 'sr-cyrl-cs':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','d1. M4','d1.M1.y4','H1:m2','M4 y4','','');
		case 'ar-ly':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'zh-sg':
			return new DateTimeFormat('0','d4, d1 M4, y4 t2 h1:m2:s2','d4, d1 M4, y4','t2 h1:m2:s2','d1 M4','d1/M1/y4','t2 h1:m2','M4, y4','AM','PM');
		case 'de-lu':
			return new DateTimeFormat('1','d4, d1. M4 y4 H2:m2:s2','d4, d1. M4 y4','H2:m2:s2','d2 M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'en-ca':
			return new DateTimeFormat('0','M4 d1, y4 h1:m2:s2 t2','M4 d1, y4','h1:m2:s2 t2','M4 d2','d2/M2/y4','h1:m2 t2','M4, y4','AM','PM');
		case 'es-gt':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'fr-ch':
			return new DateTimeFormat('1','d4, d1. M4 y4 H2:m2:s2','d4, d1. M4 y4','H2:m2:s2','d1 M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'ar-dz':
			return new DateTimeFormat('6','d2 M4, y4 H1:m2:s2','d2 M4, y4','H1:m2:s2','d2 M4','d2-M2-y4','H1:m2','M4, y4','ص','م');
		case 'zh-mo':
			return new DateTimeFormat('0','d4, d1 M4, y4 H1:m2:s2','d4, d1 M4, y4','H1:m2:s2','d1 M4','d1/M1/y4','H1:m2','M4, y4','','');
		case 'de-li':
			return new DateTimeFormat('1','d4, d1. M4 y4 H2:m2:s2','d4, d1. M4 y4','H2:m2:s2','d2 M4','d2.M2.y4','H2:m2','M4 y4','','');
		case 'en-nz':
			return new DateTimeFormat('1','d4, d1 M4 y4 h1:m2:s2 t2','d4, d1 M4 y4','h1:m2:s2 t2','d2 M4','d1/M2/y4','h1:m2 t2','M4 y4','a.m.','p.m.');
		case 'es-cr':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'fr-lu':
			return new DateTimeFormat('1','d4 d1 M4 y4 H2:m2:s2','d4 d1 M4 y4','H2:m2:s2','d1 M4','d2/M2/y4','H2:m2','M4 y4','','');
		case 'ar-ma':
			return new DateTimeFormat('1','d2 M4, y4 H1:m2:s2','d2 M4, y4','H1:m2:s2','d2 M4','d2-M2-y4','H1:m2','M4, y4','ص','م');
		case 'en-ie':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','d2 M4','d2/M2/y4','H2:m2','M4 y4','','');
		case 'es-pa':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','M2/d2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'fr-mc':
			return new DateTimeFormat('1','d4 d1 M4 y4 H2:m2:s2','d4 d1 M4 y4','H2:m2:s2','d1 M4','d2/M2/y4','H2:m2','M4 y4','','');
		case 'ar-tn':
			return new DateTimeFormat('1','d2 M4, y4 H1:m2:s2','d2 M4, y4','H1:m2:s2','d2 M4','d2-M2-y4','H1:m2','M4, y4','ص','م');
		case 'en-za':
			return new DateTimeFormat('0','d2 M4 y4 h2:m2:s2 t2','d2 M4 y4','h2:m2:s2 t2','d2 M4','y4/M2/d2','h2:m2 t2','M4 y4','AM','PM');
		case 'es-do':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'ar-om':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'en-jm':
			return new DateTimeFormat('0','d4, M4 d2, y4 h2:m2:s2 t2','d4, M4 d2, y4','h2:m2:s2 t2','M4 d2','d2/M2/y4','h2:m2 t2','M4, y4','AM','PM');
		case 'es-ve':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'ar-ye':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'en-029':
			return new DateTimeFormat('1','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','M2/d2/y4','h1:m2 t2','M4, y4','AM','PM');
		case 'es-co':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'ar-sy':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'en-bz':
			return new DateTimeFormat('0','d4, d2 M4 y4 h2:m2:s2 t2','d4, d2 M4 y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4 y4','AM','PM');
		case 'es-pe':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'ar-jo':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'en-tt':
			return new DateTimeFormat('0','d4, d2 M4 y4 h2:m2:s2 t2','d4, d2 M4 y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4 y4','AM','PM');
		case 'es-ar':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'ar-lb':
			return new DateTimeFormat('1','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'en-zw':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','M1/d1/y4','h1:m2 t2','M4, y4','AM','PM');
		case 'es-ec':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 H1:m2:s2','d4, d2\' de \'M4\' de \'y4','H1:m2:s2','d2 M4','d2/M2/y4','H1:m2','M4\' de \'y4','','');
		case 'ar-kw':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'en-ph':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','M1/d1/y4','h1:m2 t2','M4, y4','AM','PM');
		case 'es-cl':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 H1:m2:s2','d4, d2\' de \'M4\' de \'y4','H1:m2:s2','d2 M4','d2-M2-y4','H1:m2','M4\' de \'y4','','');
		case 'ar-ae':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'es-uy':
			return new DateTimeFormat('1','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'ar-bh':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'es-py':
			return new DateTimeFormat('1','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'ar-qa':
			return new DateTimeFormat('6','d2 M4, y4 h2:m2:s2 t2','d2 M4, y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4, y4','ص','م');
		case 'es-bo':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'es-sv':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'es-hn':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'es-ni':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'es-pr':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','d2 M4','d2/M2/y4','h2:m2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'am-et':
			return new DateTimeFormat('0','d4 \'፣\' M4 d1 \'ቀን\' y4 h1:m2:s2 t2','d4 \'፣\' M4 d1 \'ቀን\' y4','h1:m2:s2 t2','M4 d2','d1/M1/y4','h1:m2:s2 t2','M4 y4','ጡዋት','ከሰዓት');
		case 'tzm-latn-dz':
			return new DateTimeFormat('6','d2 M4, y4 H1:m2:s2','d2 M4, y4','H1:m2:s2','M4 d2','d2-M2-y4','H1:m2:s2','M4, y4','','');
		case 'iu-latn-ca':
			return new DateTimeFormat('0','d2d, M4 d2,y4 h1:m2:s2 t2','d2d, M4 d2,y4','h1:m2:s2 t2','M4 d2','d1/M2/y4','h1:m2:s2 t2','M4, y4','AM','PM');
		case 'sma-no':
			return new DateTimeFormat('1','M4 d1\'. b. \'y4 H2:m2:s2','M4 d1\'. b. \'y4','H2:m2:s2','M4 d2','d2.M2.y4','H2:m2:s2','M4 y4','','');
		case 'mn-mong-cn':
			return new DateTimeFormat('1','y4\'ᠣᠨ ᠤ᠋\' M1\'ᠰᠠᠷ᠎ᠠ  ᠢᠢᠨ \'d1\' ᠤ᠋ ᠡᠳᠦᠷ\' H1:m2:s2','y4\'ᠣᠨ ᠤ᠋\' M1\'ᠰᠠᠷ᠎ᠠ  ᠢᠢᠨ \'d1\' ᠤ᠋ ᠡᠳᠦᠷ\'','H1:m2:s2','M4 d2','y4/M1/d1','H1:m2:s2','y4\'ᠣᠨ\' M1\'ᠰᠠᠷ᠎ᠠ\'','','');
		case 'gd-gb':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','m','f');
		case 'en-my':
			return new DateTimeFormat('0','d4, d1 M4, y4 h1:m2:s2 t2','d4, d1 M4, y4','h1:m2:s2 t2','M4 d2','d1/M1/y4','h1:m2:s2 t2','M4, y4','AM','PM');
		case 'prs-af':
			return new DateTimeFormat('5','d2/M4/y4 h1:m2:s2 t2','d2/M4/y4','h1:m2:s2 t2','d2 M4','d2/M2/yy','h1:m2:s2 t2','M4, y4','غ.م','غ.و');
		case 'bn-bd':
			return new DateTimeFormat('1','d2 M4 y4 H2.m2.s2','d2 M4 y4','H2.m2.s2','M4 d2','d2-M2-yy','H2.m2.s2','M4, y4','পুর্বাহ্ন','অপরাহ্ন');
		case 'wo-sn':
			return new DateTimeFormat('1','d4 d1 M4 y4 H2:m2:s2','d4 d1 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','','');
		case 'rw-rw':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','M1/d1/y4','h1:m2:s2 t2','M4, y4','saa moya z.m.','saa moya z.n.');
		case 'qut-gt':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','M4 d2','d2/M2/y4','h2:m2:s2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'sah-ru':
			return new DateTimeFormat('1','M4 d1 y4 \'с.\' H1:m2:s2','M4 d1 y4 \'с.\'','H1:m2:s2','M4 d2','M2.d2.y4','H1:m2:s2','M4 y4 \'с.\'','','');
		case 'gsw-fr':
			return new DateTimeFormat('1','d4 d1 M4 y4 H2:m2:s2','d4 d1 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','','');
		case 'co-fr':
			return new DateTimeFormat('1','d4 d1 M4 y4 H2:m2:s2','d4 d1 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','','');
		case 'oc-fr':
			return new DateTimeFormat('1','d4,\' lo \'d1 M4\' de \'y4 H2:m2:s2','d4,\' lo \'d1 M4\' de \'y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','','');
		case 'mi-nz':
			return new DateTimeFormat('1','d4, d2 M4, y4 h1:m2:s2 t2','d4, d2 M4, y4','h1:m2:s2 t2','M4 d2','d2/M2/y4','h1:m2:s2 t2','M4, yy','a.m.','p.m.');
		case 'ga-ie':
			return new DateTimeFormat('1','d1 M4 y4 H2:m2:s2','d1 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','r.n.','i.n.');
		case 'se-se':
			return new DateTimeFormat('1','M4 d1\'. b. \'y4 H2:m2:s2','M4 d1\'. b. \'y4','H2:m2:s2','M4 d2','y4-M2-d2','H2:m2:s2','M4 y4','','');
		case 'br-fr':
			return new DateTimeFormat('1','d4 d1 M4 y4 H2:m2:s2','d4 d1 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','','');
		case 'smn-fi':
			return new DateTimeFormat('1','M4 d1\'. p. \'y4 H1:m2:s2','M4 d1\'. p. \'y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4 y4','','');
		case 'moh-ca':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','M1/d1/y4','h1:m2:s2 t2','M4, y4','AM','PM');
		case 'arn-cl':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 H1:m2:s2','d4, d2\' de \'M4\' de \'y4','H1:m2:s2','M4 d2','d2-M2-y4','H1:m2:s2','M4\' de \'y4','','');
		case 'ii-cn':
			return new DateTimeFormat('1','y4\'ꈎ\' M1\'ꆪ\' d1\'ꑍ\' H1:m2:s2','y4\'ꈎ\' M1\'ꆪ\' d1\'ꑍ\'','H1:m2:s2','M4 d2','y4/M1/d1','H1:m2:s2','y4\'ꈎ\' M1\'ꆪ\'','ꂵꆪꈌꈐ','ꂵꆪꈌꉈ');
		case 'dsb-de':
			return new DateTimeFormat('1','d4, \'dnja\' d1. M4 y4 H1:m2:s2','d4, \'dnja\' d1. M4 y4','H1:m2:s2','M4 d2','d1. M1. y4','H1:m2:s2','M4 y4','','');
		case 'ig-ng':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','d1/M1/y4','h1:m2:s2 t2','M4, y4','Ututu','Efifie');
		case 'kl-gl':
			return new DateTimeFormat('1','d1. M4 y4 H2:m2:s2','d1. M4 y4','H2:m2:s2','M4 d2','d2-M2-y4','H2:m2:s2','M4 y4','','');
		case 'lb-lu':
			return new DateTimeFormat('1','d4 d1 M4 y4 H2:m2:s2','d4 d1 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','','');
		case 'ba-ru':
			return new DateTimeFormat('1','d1 M4 y4 \'й\' H1:m2:s2','d1 M4 y4 \'й\'','H1:m2:s2','M4 d2','d2.M2.yy','H1:m2:s2','M4 y4','','');
		case 'nso-za':
			return new DateTimeFormat('0','d2 M4 y4 h2:m2:s2 t2','d2 M4 y4','h2:m2:s2 t2','M4 d2','y4/M2/d2','h2:m2:s2 t2','M4 y4','AM','PM');
		case 'quz-bo':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','M4 d2','d2/M2/y4','h2:m2:s2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'yo-ng':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','d1/M1/y4','h1:m2:s2 t2','M4, y4','Owuro','Ale');
		case 'ha-latn-ng':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','d1/M1/y4','h1:m2:s2 t2','M4, y4','Safe','Yam2a');
		case 'fil-ph':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','M1/d1/y4','h1:m2:s2 t2','M4, y4','AM','PM');
		case 'ps-af':
			return new DateTimeFormat('6','d2/M4/y4 h1:m2:s2 t2','d2/M4/y4','h1:m2:s2 t2','d2 M4','d2/M2/yy','h1:m2:s2 t2','M4, y4','غ.م','غ.و');
		case 'fy-nl':
			return new DateTimeFormat('1','d4 d1 M4 y4 H1:m2:s2','d4 d1 M4 y4','H1:m2:s2','M4 d2','d1-M1-y4','H1:m2:s2','M4 y4','','');
		case 'ne-np':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','M1/d1/y4','h1:m2:s2 t2','M4,y4','विहानी','बेलुकी');
		case 'se-no':
			return new DateTimeFormat('1','M4 d1\'. b. \'y4 H2:m2:s2','M4 d1\'. b. \'y4','H2:m2:s2','M4 d2','d2.M2.y4','H2:m2:s2','M4 y4','','');
		case 'iu-cans-ca':
			return new DateTimeFormat('0','d4,M4 d2,y4 h1:m2:s2 t2','d4,M4 d2,y4','h1:m2:s2 t2','M4 d2','d1/M1/y4','h1:m2:s2 t2','M4,y4','AM','PM');
		case 'sr-latn-rs':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4 y4','','');
		case 'si-lk':
			return new DateTimeFormat('1','y4 M4\' මස \'d2\' වැනිදා \'d4 h1:m2:s2 t2','y4 M4\' මස \'d2\' වැනිදා \'d4','h1:m2:s2 t2','M4 d2','y4-M2-d2','h1:m2:s2 t2','y4 M4','පෙ.ව.','ප.ව.');
		case 'sr-cyrl-rs':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4 y4','','');
		case 'lo-la':
			return new DateTimeFormat('0','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','ເຊົ້າ','ແລງ');
		case 'km-kh':
			return new DateTimeFormat('0','d1 M4 y4 H2:m2:s2','d1 M4 y4','H2:m2:s2','M4 d2','y4-M2-d2','H2:m2:s2','\'ខែ\' M2 \'ឆ្នាំ\' y4','ព្រឹក','ល្ងាច');
		case 'cy-gb':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','a.m.','p.m.');
		case 'bo-cn':
			return new DateTimeFormat('1','y4\'ལོའི་ཟླ\' M1\'ཚེས\' d1 H2:m2:s2','y4\'ལོའི་ཟླ\' M1\'ཚེས\' d1','H2:m2:s2','M4 d2','y4/M1/d1','H2:m2:s2','y4.M1','སྔ་དྲོ','ཕྱི་དྲོ');
		case 'sms-fi':
			return new DateTimeFormat('1','M4 d1\'. p. \'y4 H1:m2:s2','M4 d1\'. p. \'y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4 y4','','');
		case 'as-in':
			return new DateTimeFormat('1','y4,M4 d2, d4 t2 h1:m2:s2','y4,M4 d2, d4','t2 h1:m2:s2','M4 d2','d2-M2-y4','t2 h1:m2:s2','M4,yy','ৰাতিপু','আবেলি');
		case 'ml-in':
			return new DateTimeFormat('1','d2 M4 y4 H2.m2.s2','d2 M4 y4','H2.m2.s2','M4 d2','d2-M2-yy','H2.m2.s2','M4, y4','AM','PM');
		case 'en-in':
			return new DateTimeFormat('1','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','M4 d2','d2-M2-y4','H2:m2:s2','M4, y4','AM','PM');
		case 'or-in':
			return new DateTimeFormat('0','d2 M4 y4 H2:m2:s2','d2 M4 y4','H2:m2:s2','M4 d2','d2-M2-yy','H2:m2:s2','M4, y4','AM','PM');
		case 'bn-in':
			return new DateTimeFormat('1','d2 M4 y4 H2.m2.s2','d2 M4 y4','H2.m2.s2','M4 d2','d2-M2-yy','H2.m2.s2','M4, y4','পুর্বাহ্ন','অপরাহ্ন');
		case 'tk-tm':
			return new DateTimeFormat('1','y4 \'ý.\' M4 d1 H1:m2:s2','y4 \'ý.\' M4 d1','H1:m2:s2','M4 d2','d2.M2.yy','H1:m2:s2','y4 \'ý.\' M4','','');
		case 'bs-latn-ba':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4 y4','','');
		case 'mt-mt':
			return new DateTimeFormat('1','d4, d1\' ta\\\' \'M4 y4 H2:m2:s2','d4, d1\' ta\\\' \'M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','AM','PM');
		case 'sr-cyrl-me':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4 y4','','');
		case 'se-fi':
			return new DateTimeFormat('1','M4 d1\'. b. \'y4 H1:m2:s2','M4 d1\'. b. \'y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4 y4','','');
		case 'zu-za':
			return new DateTimeFormat('0','d2 M4 y4 h2:m2:s2 t2','d2 M4 y4','h2:m2:s2 t2','M4 d2','y4/M2/d2','h2:m2:s2 t2','M4 y4','AM','PM');
		case 'xh-za':
			return new DateTimeFormat('0','d2 M4 y4 h2:m2:s2 t2','d2 M4 y4','h2:m2:s2 t2','M4 d2','y4/M2/d2','h2:m2:s2 t2','M4 y4','AM','PM');
		case 'tn-za':
			return new DateTimeFormat('0','d2 M4 y4 h2:m2:s2 t2','d2 M4 y4','h2:m2:s2 t2','M4 d2','y4/M2/d2','h2:m2:s2 t2','M4 y4','AM','PM');
		case 'hsb-de':
			return new DateTimeFormat('1','d4, \'dnja\' d1. M4 y4 H1:m2:s2','d4, \'dnja\' d1. M4 y4','H1:m2:s2','M4 d2','d1. M1. y4','H1:m2:s2','M4 y4','','');
		case 'bs-cyrl-ba':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4, y4','','');
		case 'tg-cyrl-tj':
			return new DateTimeFormat('0','d1 M4 y4 H1:m2:s2','d1 M4 y4','H1:m2:s2','M4 d2','d2.M2.yy','H1:m2:s2','M4 y4','','');
		case 'sr-latn-ba':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4 y4','','');
		case 'smj-no':
			return new DateTimeFormat('1','M4 d1\'. b. \'y4 H2:m2:s2','M4 d1\'. b. \'y4','H2:m2:s2','M4 d2','d2.M2.y4','H2:m2:s2','M4 y4','','');
		case 'rm-ch':
			return new DateTimeFormat('1','d4, d1 M4 y4 H2:m2:s2','d4, d1 M4 y4','H2:m2:s2','M4 d2','d2/M2/y4','H2:m2:s2','M4 y4','','');
		case 'smj-se':
			return new DateTimeFormat('1','M4 d1\'. b. \'y4 H2:m2:s2','M4 d1\'. b. \'y4','H2:m2:s2','M4 d2','y4-M2-d2','H2:m2:s2','M4 y4','','');
		case 'quz-ec':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 H1:m2:s2','d4, d2\' de \'M4\' de \'y4','H1:m2:s2','M4 d2','d2/M2/y4','H1:m2:s2','M4\' de \'y4','','');
		case 'quz-pe':
			return new DateTimeFormat('0','d4, d2\' de \'M4\' de \'y4 h2:m2:s2 t2','d4, d2\' de \'M4\' de \'y4','h2:m2:s2 t2','M4 d2','d2/M2/y4','h2:m2:s2 t2','M4\' de \'y4','a.m.','p.m.');
		case 'hr-ba':
			return new DateTimeFormat('1','d1. M4 y4. H1:m2:s2','d1. M4 y4.','H1:m2:s2','M4 d2','d1.M1.y4.','H1:m2:s2','M4, y4','','');
		case 'sr-latn-me':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4 y4','','');
		case 'sma-se':
			return new DateTimeFormat('1','M4 d1\'. b. \'y4 H2:m2:s2','M4 d1\'. b. \'y4','H2:m2:s2','M4 d2','y4-M2-d2','H2:m2:s2','M4 y4','','');
		case 'en-sg':
			return new DateTimeFormat('0','d4, d1 M4, y4 h1:m2:s2 t2','d4, d1 M4, y4','h1:m2:s2 t2','M4 d2','d1/M1/y4','h1:m2:s2 t2','M4, y4','AM','PM');
		case 'ug-cn':
			return new DateTimeFormat('0','y4-\'يىلى\' M4 d1-\'كۈنى،\' H1:m2:s2','y4-\'يىلى\' M4 d1-\'كۈنى،\'','H1:m2:s2','M4 d2','y4-M1-d1','H1:m2:s2','y4-\'يىلى\' M4','چۈشتىن بۇرۇن','چۈشتىن كېيىن');
		case 'sr-cyrl-ba':
			return new DateTimeFormat('1','d1. M4 y4 H1:m2:s2','d1. M4 y4','H1:m2:s2','M4 d2','d1.M1.y4','H1:m2:s2','M4, y4','','');
		case 'es-us':
			return new DateTimeFormat('0','d4, M4 d2, y4 h1:m2:s2 t2','d4, M4 d2, y4','h1:m2:s2 t2','M4 d2','M1/d1/y4','h1:m2:s2 t2','M4\' de \'y4','AM','PM');
	}
	// fallback is en-US formatting
	log_error("No DateTimeFormat in cultureinfo.inc.php::internal_getDateTimeFormat($cultureCode)");
	return internal_getDateTimeFormat('en-US');
}

/**
 * @internal 
 */
function internal_getCultureCodeFromRegion($region)
{
	switch( strtoupper($region) )
	{
		case 'SA': return array('ar-SA');
		case 'BG': return array('bg-BG');
		case 'ES': return array('es-ES', 'ca-ES','eu-ES','gl-ES');
		case 'TW': return array('zh-TW');
		case 'CZ': return array('cs-CZ');
		case 'DK': return array('da-DK');
		case 'DE': return array('de-DE','dsb-DE','hsb-DE');
		case 'GR': return array('el-GR');
		case 'US': return array('en-US','es-US');
		case 'FI': return array('fi-FI','sv-FI','smn-FI','sms-FI','se-FI');
		case 'FR': return array('fr-FR','gsw-FR','co-FR','oc-FR','br-FR');
		case 'IL': return array('he-IL');
		case 'HU': return array('hu-HU');
		case 'IS': return array('is-IS');
		case 'IT': return array('it-IT');
		case 'JP': return array('ja-JP');
		case 'KR': return array('ko-KR');
		case 'NL': return array('nl-NL','fy-NL');
		case 'NO': return array('nb-NO','nn-NO','sma-NO','se-NO','smj-NO');
		case 'PL': return array('pl-PL');
		case 'BR': return array('pt-BR');
		case 'RO': return array('ro-RO');
		case 'RU': return array('ru-RU','tt-RU','sah-RU','ba-RU');
		case 'HR': return array('hr-HR');
		case 'SK': return array('sk-SK');
		case 'AL': return array('sq-AL');
		case 'SE': return array('sv-SE','se-SE','smj-SE','sma-SE');
		case 'TH': return array('th-TH');
		case 'TR': return array('tr-TR');
		case 'PK': return array('ur-PK');
		case 'ID': return array('id-ID');
		case 'UA': return array('uk-UA');
		case 'BY': return array('be-BY');
		case 'SI': return array('sl-SI');
		case 'EE': return array('et-EE');
		case 'LV': return array('lv-LV');
		case 'LT': return array('lt-LT');
		case 'IR': return array('fa-IR');
		case 'VN': return array('vi-VN');
		case 'AM': return array('hy-AM');
		case 'AZ': return array('az-Latn-AZ','az-Cyrl-AZ');
		case 'MK': return array('mk-MK');
		case 'ZA': return array('af-ZA','en-ZA','nso-ZA','zu-ZA','xh-ZA','tn-ZA');
		case 'GE': return array('ka-GE');
		case 'FO': return array('fo-FO');
		case 'IN': return array('hi-IN','pa-IN','gu-IN','ta-IN','te-IN','kn-IN','mr-IN','sa-IN','kok-IN','as-IN','ml-IN','en-IN','or-IN','bn-IN');
		case 'MY': return array('ms-MY','en-MY');
		case 'KZ': return array('kk-KZ');
		case 'KG': return array('ky-KG');
		case 'KE': return array('sw-KE');
		case 'UZ': return array('uz-Latn-UZ','uz-Cyrl-UZ');
		case 'MN': return array('mn-MN');
		case 'SY': return array('syr-SY','ar-SY');
		case 'MV': return array('dv-MV');
		case 'IQ': return array('ar-IQ');
		case 'CN': return array('zh-CN','mn-Mong-CN','ii-CN','bo-CN','ug-CN');
		case 'CH': return array('de-CH','it-CH','fr-CH','rm-CH');
		case 'GB': return array('en-GB','gd-GB','cy-GB');
		case 'MX': return array('es-MX');
		case 'BE': return array('nl-BE','fr-BE');
		case 'PT': return array('pt-PT');
		case 'CS': return array('sr-Latn-CS','sr-Cyrl-CS');
		case 'BN': return array('ms-BN');
		case 'EG': return array('ar-EG');
		case 'HK': return array('zh-HK');
		case 'AT': return array('de-AT');
		case 'AU': return array('en-AU');
		case 'CA': return array('en-CA','fr-CA','iu-Latn-CA','moh-CA','iu-Cans-CA');
		case 'LY': return array('ar-LY');
		case 'SG': return array('zh-SG','en-SG');
		case 'LU': return array('fr-LU','de-LU','lb-LU');
		case 'GT': return array('es-GT','qut-GT');
		case 'DZ': return array('ar-DZ','tzm-Latn-DZ');
		case 'MO': return array('zh-MO');
		case 'LI': return array('de-LI');
		case 'NZ': return array('en-NZ','mi-NZ');
		case 'CR': return array('es-CR');
		case 'MA': return array('ar-MA');
		case 'IE': return array('en-IE','ga-IE');
		case 'PA': return array('es-PA');
		case 'MC': return array('fr-MC');
		case 'TN': return array('ar-TN');
		case 'DO': return array('es-DO');
		case 'OM': return array('ar-OM');
		case 'JM': return array('en-JM');
		case 'VE': return array('es-VE');
		case 'YE': return array('ar-YE');
		case '029': return array('en-029');
		case 'CO': return array('es-CO');
		case 'BZ': return array('en-BZ');
		case 'PE': return array('es-PE','quz-PE');
		case 'JO': return array('ar-JO');
		case 'TT': return array('en-TT');
		case 'AR': return array('es-AR');
		case 'LB': return array('ar-LB');
		case 'ZW': return array('en-ZW');
		case 'EC': return array('es-EC','quz-EC');
		case 'KW': return array('ar-KW');
		case 'PH': return array('en-PH','fil-PH');
		case 'CL': return array('es-CL','arn-CL');
		case 'AE': return array('ar-AE');
		case 'UY': return array('es-UY');
		case 'BH': return array('ar-BH');
		case 'PY': return array('es-PY');
		case 'QA': return array('ar-QA');
		case 'BO': return array('es-BO','quz-BO');
		case 'SV': return array('es-SV');
		case 'HN': return array('es-HN');
		case 'NI': return array('es-NI');
		case 'PR': return array('es-PR');
		case 'ET': return array('am-ET');
		case 'AF': return array('prs-AF','ps-AF');
		case 'BD': return array('bn-BD');
		case 'SN': return array('wo-SN');
		case 'RW': return array('rw-RW');
		case 'NG': return array('ig-NG','yo-NG','ha-Latn-NG');
		case 'GL': return array('kl-GL');
		case 'NP': return array('ne-NP');
		case 'RS': return array('sr-Latn-RS','sr-Cyrl-RS');
		case 'LK': return array('si-LK');
		case 'LA': return array('lo-LA');
		case 'KH': return array('km-KH');
		case 'TM': return array('tk-TM');
		case 'BA': return array('bs-Latn-BA','bs-Cyrl-BA','sr-Latn-BA','hr-BA','sr-Cyrl-BA');
		case 'MT': return array('mt-MT');
		case 'ME': return array('sr-Cyrl-ME','sr-Latn-ME');
		case 'TJ': return array('tg-Cyrl-TJ');
	}
}

/**
 * @internal 
 */
function internal_getRegion($code)
{
	switch( strtolower($code) )
	{
		case 'sa': return new RegionInfo('SA','Saudi Arabia','المملكة العربية السعودية',array('ar-SA'));
		case 'bg': return new RegionInfo('BG','Bulgaria','България',array('bg-BG'));
		case 'es': return new RegionInfo('ES','Spain','Espanya',array('ca-ES','eu-ES','gl-ES','es-ES'));
		case 'tw': return new RegionInfo('TW','Taiwan','台灣',array('zh-TW'));
		case 'cz': return new RegionInfo('CZ','Czech Republic','Česká republika',array('cs-CZ'));
		case 'dk': return new RegionInfo('DK','Denmark','Danmark',array('da-DK'));
		case 'de': return new RegionInfo('DE','Germany','Deutschland',array('de-DE','dsb-DE','hsb-DE'));
		case 'gr': return new RegionInfo('GR','Greece','Ελλάδα',array('el-GR'));
		case 'us': return new RegionInfo('US','United States','United States',array('en-US','es-US'));
		case 'fi': return new RegionInfo('FI','Finland','Suomi',array('fi-FI','sv-FI','smn-FI','sms-FI','se-FI'));
		case 'fr': return new RegionInfo('FR','France','France',array('fr-FR','gsw-FR','co-FR','oc-FR','br-FR'));
		case 'il': return new RegionInfo('IL','Israel','ישראל',array('he-IL'));
		case 'hu': return new RegionInfo('HU','Hungary','Magyarország',array('hu-HU'));
		case 'is': return new RegionInfo('IS','Iceland','Ísland',array('is-IS'));
		case 'it': return new RegionInfo('IT','Italy','Italia',array('it-IT'));
		case 'jp': return new RegionInfo('JP','Japan','日本',array('ja-JP'));
		case 'kr': return new RegionInfo('KR','Korea','대한민국',array('ko-KR'));
		case 'nl': return new RegionInfo('NL','Netherlands','Nederland',array('nl-NL','fy-NL'));
		case 'no': return new RegionInfo('NO','Norway','Norge',array('nb-NO','nn-NO','sma-NO','se-NO','smj-NO'));
		case 'pl': return new RegionInfo('PL','Poland','Polska',array('pl-PL'));
		case 'br': return new RegionInfo('BR','Brazil','Brasil',array('pt-BR'));
		case 'ro': return new RegionInfo('RO','Romania','România',array('ro-RO'));
		case 'ru': return new RegionInfo('RU','Russia','Россия',array('ru-RU','tt-RU','sah-RU','ba-RU'));
		case 'hr': return new RegionInfo('HR','Croatia','Hrvatska',array('hr-HR'));
		case 'sk': return new RegionInfo('SK','Slovakia','Slovenská republika',array('sk-SK'));
		case 'al': return new RegionInfo('AL','Albania','Shqipëria',array('sq-AL'));
		case 'se': return new RegionInfo('SE','Sweden','Sverige',array('sv-SE','se-SE','smj-SE','sma-SE'));
		case 'th': return new RegionInfo('TH','Thailand','ไทย',array('th-TH'));
		case 'tr': return new RegionInfo('TR','Turkey','Türkiye',array('tr-TR'));
		case 'pk': return new RegionInfo('PK','Islamic Republic of Pakistan','پاکستان',array('ur-PK'));
		case 'id': return new RegionInfo('ID','Indonesia','Indonesia',array('id-ID'));
		case 'ua': return new RegionInfo('UA','Ukraine','Україна',array('uk-UA'));
		case 'by': return new RegionInfo('BY','Belarus','Беларусь',array('be-BY'));
		case 'si': return new RegionInfo('SI','Slovenia','Slovenija',array('sl-SI'));
		case 'ee': return new RegionInfo('EE','Estonia','Eesti',array('et-EE'));
		case 'lv': return new RegionInfo('LV','Latvia','Latvija',array('lv-LV'));
		case 'lt': return new RegionInfo('LT','Lithuania','Lietuva',array('lt-LT'));
		case 'ir': return new RegionInfo('IR','Iran','ايران',array('fa-IR'));
		case 'vn': return new RegionInfo('VN','Vietnam','Việt Nam',array('vi-VN'));
		case 'am': return new RegionInfo('AM','Armenia','Հայաստան',array('hy-AM'));
		case 'az': return new RegionInfo('AZ','Azerbaijan','Azərbaycanca',array('az-Latn-AZ','az-Cyrl-AZ'));
		case 'mk': return new RegionInfo('MK','Macedonia (FYROM)','Македонија',array('mk-MK'));
		case 'za': return new RegionInfo('ZA','South Africa','Suid Afrika',array('af-ZA','en-ZA','nso-ZA','zu-ZA','xh-ZA','tn-ZA'));
		case 'ge': return new RegionInfo('GE','Georgia','საქართველო',array('ka-GE'));
		case 'fo': return new RegionInfo('FO','Faroe Islands','Føroyar',array('fo-FO'));
		case 'in': return new RegionInfo('IN','India','भारत',array('hi-IN','pa-IN','gu-IN','ta-IN','te-IN','kn-IN','mr-IN','sa-IN','kok-IN','as-IN','ml-IN','en-IN','or-IN','bn-IN'));
		case 'my': return new RegionInfo('MY','Malaysia','Malaysia',array('ms-MY','en-MY'));
		case 'kz': return new RegionInfo('KZ','Kazakhstan','Қазақстан',array('kk-KZ'));
		case 'kg': return new RegionInfo('KG','Kyrgyzstan','Кыргызстан',array('ky-KG'));
		case 'ke': return new RegionInfo('KE','Kenya','Kenya',array('sw-KE'));
		case 'uz': return new RegionInfo('UZ','Uzbekistan','U\'zbekiston Respublikasi',array('uz-Latn-UZ','uz-Cyrl-UZ'));
		case 'mn': return new RegionInfo('MN','Mongolia','Монгол улс',array('mn-MN'));
		case 'sy': return new RegionInfo('SY','Syria','سوريا',array('syr-SY','ar-SY'));
		case 'mv': return new RegionInfo('MV','Maldives','ދިވެހި ރާއްޖެ',array('dv-MV'));
		case 'iq': return new RegionInfo('IQ','Iraq','العراق',array('ar-IQ'));
		case 'cn': return new RegionInfo('CN','People\'s Republic of China','中华人民共和国',array('zh-CN','mn-Mong-CN','ii-CN','bo-CN','ug-CN'));
		case 'ch': return new RegionInfo('CH','Switzerland','Schweiz',array('de-CH','it-CH','fr-CH','rm-CH'));
		case 'gb': return new RegionInfo('GB','United Kingdom','United Kingdom',array('en-GB','gd-GB','cy-GB'));
		case 'mx': return new RegionInfo('MX','Mexico','México',array('es-MX'));
		case 'be': return new RegionInfo('BE','Belgium','Belgique',array('nl-BE','fr-BE'));
		case 'pt': return new RegionInfo('PT','Portugal','Portugal',array('pt-PT'));
		case 'cs': return new RegionInfo('CS','Serbia and Montenegro (Former)','Srbija i Crna Gora (Prethodno)',array('sr-Latn-CS','sr-Cyrl-CS'));
		case 'bn': return new RegionInfo('BN','Brunei Darussalam','Brunei Darussalam',array('ms-BN'));
		case 'eg': return new RegionInfo('EG','Egypt','مصر',array('ar-EG'));
		case 'hk': return new RegionInfo('HK','Hong Kong S.A.R.','香港特别行政區',array('zh-HK'));
		case 'at': return new RegionInfo('AT','Austria','Österreich',array('de-AT'));
		case 'au': return new RegionInfo('AU','Australia','Australia',array('en-AU'));
		case 'ca': return new RegionInfo('CA','Canada','Canada',array('en-CA','fr-CA','iu-Latn-CA','moh-CA','iu-Cans-CA'));
		case 'ly': return new RegionInfo('LY','Libya','ليبيا',array('ar-LY'));
		case 'sg': return new RegionInfo('SG','Singapore','新加坡',array('en-SG','zh-SG'));
		case 'lu': return new RegionInfo('LU','Luxembourg','Luxemburg',array('lb-LU','fr-LU','de-LU'));
		case 'gt': return new RegionInfo('GT','Guatemala','Guatemala',array('es-GT','qut-GT'));
		case 'dz': return new RegionInfo('DZ','Algeria','الجزائر',array('ar-DZ','tzm-Latn-DZ'));
		case 'mo': return new RegionInfo('MO','Macao S.A.R.','澳門特别行政區',array('zh-MO'));
		case 'li': return new RegionInfo('LI','Liechtenstein','Liechtenstein',array('de-LI'));
		case 'nz': return new RegionInfo('NZ','New Zealand','New Zealand',array('en-NZ','mi-NZ'));
		case 'cr': return new RegionInfo('CR','Costa Rica','Costa Rica',array('es-CR'));
		case 'ma': return new RegionInfo('MA','Morocco','المملكة المغربية',array('ar-MA'));
		case 'ie': return new RegionInfo('IE','Ireland','Eire',array('en-IE','ga-IE'));
		case 'pa': return new RegionInfo('PA','Panama','Panamá',array('es-PA'));
		case 'mc': return new RegionInfo('MC','Principality of Monaco','Principauté de Monaco',array('fr-MC'));
		case 'tn': return new RegionInfo('TN','Tunisia','تونس',array('ar-TN'));
		case 'do': return new RegionInfo('DO','Dominican Republic','República Dominicana',array('es-DO'));
		case 'om': return new RegionInfo('OM','Oman','عمان',array('ar-OM'));
		case 'jm': return new RegionInfo('JM','Jamaica','Jamaica',array('en-JM'));
		case 've': return new RegionInfo('VE','Venezuela','Republica Bolivariana de Venezuela',array('es-VE'));
		case 'ye': return new RegionInfo('YE','Yemen','اليمن',array('ar-YE'));
		case '029': return new RegionInfo('029','Caribbean','Caribbean',array('en-029'));
		case 'co': return new RegionInfo('CO','Colombia','Colombia',array('es-CO'));
		case 'bz': return new RegionInfo('BZ','Belize','Belize',array('en-BZ'));
		case 'pe': return new RegionInfo('PE','Peru','Perú',array('es-PE','quz-PE'));
		case 'jo': return new RegionInfo('JO','Jordan','الأردن',array('ar-JO'));
		case 'tt': return new RegionInfo('TT','Trinidad and Tobago','Trinidad y Tobago',array('en-TT'));
		case 'ar': return new RegionInfo('AR','Argentina','Argentina',array('es-AR'));
		case 'lb': return new RegionInfo('LB','Lebanon','لبنان',array('ar-LB'));
		case 'zw': return new RegionInfo('ZW','Zimbabwe','Zimbabwe',array('en-ZW'));
		case 'ec': return new RegionInfo('EC','Ecuador','Ecuador',array('es-EC','quz-EC'));
		case 'kw': return new RegionInfo('KW','Kuwait','الكويت',array('ar-KW'));
		case 'ph': return new RegionInfo('PH','Republic of the Philippines','Philippines',array('en-PH','fil-PH'));
		case 'cl': return new RegionInfo('CL','Chile','Chile',array('es-CL','arn-CL'));
		case 'ae': return new RegionInfo('AE','U.A.E.','الإمارات العربية المتحدة',array('ar-AE'));
		case 'uy': return new RegionInfo('UY','Uruguay','Uruguay',array('es-UY'));
		case 'bh': return new RegionInfo('BH','Bahrain','البحرين',array('ar-BH'));
		case 'py': return new RegionInfo('PY','Paraguay','Paraguay',array('es-PY'));
		case 'qa': return new RegionInfo('QA','Qatar','قطر',array('ar-QA'));
		case 'bo': return new RegionInfo('BO','Bolivia','Bolivia',array('es-BO','quz-BO'));
		case 'sv': return new RegionInfo('SV','El Salvador','El Salvador',array('es-SV'));
		case 'hn': return new RegionInfo('HN','Honduras','Honduras',array('es-HN'));
		case 'ni': return new RegionInfo('NI','Nicaragua','Nicaragua',array('es-NI'));
		case 'pr': return new RegionInfo('PR','Puerto Rico','Puerto Rico',array('es-PR'));
		case 'et': return new RegionInfo('ET','Ethiopia','ኢትዮጵያ',array('am-ET'));
		case 'af': return new RegionInfo('AF','Afghanistan','افغانستان',array('prs-AF','ps-AF'));
		case 'bd': return new RegionInfo('BD','Bangladesh','বাংলাদেশ',array('bn-BD'));
		case 'sn': return new RegionInfo('SN','Senegal','Sénégal',array('wo-SN'));
		case 'rw': return new RegionInfo('RW','Rwanda','Rwanda',array('rw-RW'));
		case 'ng': return new RegionInfo('NG','Nigeria','Nigeria',array('ig-NG','yo-NG','ha-Latn-NG'));
		case 'gl': return new RegionInfo('GL','Greenland','Kalaallit Nunaat',array('kl-GL'));
		case 'np': return new RegionInfo('NP','Nepal','नेपाल',array('ne-NP'));
		case 'rs': return new RegionInfo('RS','Serbia','Srbija',array('sr-Latn-RS','sr-Cyrl-RS'));
		case 'lk': return new RegionInfo('LK','Sri Lanka','ශ්‍රී ලංකා',array('si-LK'));
		case 'la': return new RegionInfo('LA','Lao P.D.R.','ສ.ປ.ປ. ລາວ',array('lo-LA'));
		case 'kh': return new RegionInfo('KH','Cambodia','កម្ពុជា',array('km-KH'));
		case 'tm': return new RegionInfo('TM','Turkmenistan','Türkmenistan',array('tk-TM'));
		case 'ba': return new RegionInfo('BA','Bosnia and Herzegovina','Bosna i Hercegovina',array('bs-Latn-BA','bs-Cyrl-BA','sr-Latn-BA','hr-BA','sr-Cyrl-BA'));
		case 'mt': return new RegionInfo('MT','Malta','Malta',array('mt-MT'));
		case 'me': return new RegionInfo('ME','Montenegro','Црна Гора',array('sr-Cyrl-ME','sr-Latn-ME'));
		case 'tj': return new RegionInfo('TJ','Tajikistan','Тоҷикистон',array('tg-Cyrl-TJ'));
	}
    return false;
}

/**
 * @internal 
 */
function internal_getRegionsForLanguage($code)
{
	switch( strtolower($code) )
	{
		case 'ar': return array(internal_getRegion('SA'),internal_getRegion('IQ'),internal_getRegion('EG'),internal_getRegion('LY'),internal_getRegion('DZ'),internal_getRegion('MA'),internal_getRegion('TN'),internal_getRegion('OM'),internal_getRegion('YE'),internal_getRegion('SY'),internal_getRegion('JO'),internal_getRegion('LB'),internal_getRegion('KW'),internal_getRegion('AE'),internal_getRegion('BH'),internal_getRegion('QA'));
		case 'bg': return array(internal_getRegion('BG'));
		case 'ca': return array(internal_getRegion('ES'));
		case 'zh-cht': return array(internal_getRegion('TW'),internal_getRegion('HK'),internal_getRegion('MO'));
		case 'cs': return array(internal_getRegion('CZ'));
		case 'da': return array(internal_getRegion('DK'));
		case 'de': return array(internal_getRegion('DE'),internal_getRegion('CH'),internal_getRegion('AT'),internal_getRegion('LU'),internal_getRegion('LI'));
		case 'el': return array(internal_getRegion('GR'));
		case 'en': return array(internal_getRegion('US'),internal_getRegion('GB'),internal_getRegion('AU'),internal_getRegion('CA'),internal_getRegion('NZ'),internal_getRegion('IE'),internal_getRegion('ZA'),internal_getRegion('JM'),internal_getRegion('029'),internal_getRegion('BZ'),internal_getRegion('TT'),internal_getRegion('ZW'),internal_getRegion('PH'),internal_getRegion('MY'),internal_getRegion('IN'),internal_getRegion('SG'));
		case 'fi': return array(internal_getRegion('FI'));
		case 'fr': return array(internal_getRegion('FR'),internal_getRegion('BE'),internal_getRegion('CA'),internal_getRegion('CH'),internal_getRegion('LU'),internal_getRegion('MC'));
		case 'he': return array(internal_getRegion('IL'));
		case 'hu': return array(internal_getRegion('HU'));
		case 'is': return array(internal_getRegion('IS'));
		case 'it': return array(internal_getRegion('IT'),internal_getRegion('CH'));
		case 'ja': return array(internal_getRegion('JP'));
		case 'ko': return array(internal_getRegion('KR'));
		case 'nl': return array(internal_getRegion('NL'),internal_getRegion('BE'));
		case 'no': return array(internal_getRegion('NO'));
		case 'pl': return array(internal_getRegion('PL'));
		case 'pt': return array(internal_getRegion('BR'),internal_getRegion('PT'));
		case 'ro': return array(internal_getRegion('RO'));
		case 'ru': return array(internal_getRegion('RU'));
		case 'hr': return array(internal_getRegion('HR'),internal_getRegion('BA'));
		case 'sk': return array(internal_getRegion('SK'));
		case 'sq': return array(internal_getRegion('AL'));
		case 'sv': return array(internal_getRegion('SE'),internal_getRegion('FI'));
		case 'th': return array(internal_getRegion('TH'));
		case 'tr': return array(internal_getRegion('TR'));
		case 'ur': return array(internal_getRegion('PK'));
		case 'id': return array(internal_getRegion('ID'));
		case 'uk': return array(internal_getRegion('UA'));
		case 'be': return array(internal_getRegion('BY'));
		case 'sl': return array(internal_getRegion('SI'));
		case 'et': return array(internal_getRegion('EE'));
		case 'lv': return array(internal_getRegion('LV'));
		case 'lt': return array(internal_getRegion('LT'));
		case 'fa': return array(internal_getRegion('IR'));
		case 'vi': return array(internal_getRegion('VN'));
		case 'hy': return array(internal_getRegion('AM'));
		case 'az': return array(internal_getRegion('AZ'));
		case 'eu': return array(internal_getRegion('ES'));
		case 'mk': return array(internal_getRegion('MK'));
		case 'af': return array(internal_getRegion('ZA'));
		case 'ka': return array(internal_getRegion('GE'));
		case 'fo': return array(internal_getRegion('FO'));
		case 'hi': return array(internal_getRegion('IN'));
		case 'ms': return array(internal_getRegion('MY'),internal_getRegion('BN'));
		case 'kk': return array(internal_getRegion('KZ'));
		case 'ky': return array(internal_getRegion('KG'));
		case 'sw': return array(internal_getRegion('KE'));
		case 'uz': return array(internal_getRegion('UZ'));
		case 'tt': return array(internal_getRegion('RU'));
		case 'pa': return array(internal_getRegion('IN'));
		case 'gu': return array(internal_getRegion('IN'));
		case 'ta': return array(internal_getRegion('IN'));
		case 'te': return array(internal_getRegion('IN'));
		case 'kn': return array(internal_getRegion('IN'));
		case 'mr': return array(internal_getRegion('IN'));
		case 'sa': return array(internal_getRegion('IN'));
		case 'mn': return array(internal_getRegion('MN'));
		case 'gl': return array(internal_getRegion('ES'));
		case 'kok': return array(internal_getRegion('IN'));
		case 'syr': return array(internal_getRegion('SY'));
		case 'dv': return array(internal_getRegion('MV'));
		case 'zh-chs': return array(internal_getRegion('CN'),internal_getRegion('SG'));
		case 'es': return array(internal_getRegion('MX'),internal_getRegion('ES'),internal_getRegion('GT'),internal_getRegion('CR'),internal_getRegion('PA'),internal_getRegion('DO'),internal_getRegion('VE'),internal_getRegion('CO'),internal_getRegion('PE'),internal_getRegion('AR'),internal_getRegion('EC'),internal_getRegion('CL'),internal_getRegion('UY'),internal_getRegion('PY'),internal_getRegion('BO'),internal_getRegion('SV'),internal_getRegion('HN'),internal_getRegion('NI'),internal_getRegion('PR'),internal_getRegion('US'));
		case 'sr': return array(internal_getRegion('CS'));
	}
    return false;

}

/**
 * @internal 
 */
function internal_getLanguage($code)
{
	switch( strtolower($code) )
	{
		case 'ar': return new CultureInfo('ar','','ar','Arabic','العربية','1');
		case 'bg': return new CultureInfo('bg','','bg','Bulgarian','Български','0');
		case 'ca': return new CultureInfo('ca','','ca','Catalan','Català','0');
		case 'zh-cht': return new CultureInfo('zh-CHT','zh-Hant','zh','Chinese (Traditional)','中文(繁體)','0');
		case 'cs': return new CultureInfo('cs','','cs','Czech','Čeština','0');
		case 'da': return new CultureInfo('da','','da','Danish','Dansk','0');
		case 'de': return new CultureInfo('de','','de','German','Deutsch','0');
		case 'el': return new CultureInfo('el','','el','Greek','Ελληνικά','0');
		case 'en': return new CultureInfo('en','','en','English','English','0');
		case 'fi': return new CultureInfo('fi','','fi','Finnish','Suomi','0');
		case 'fr': return new CultureInfo('fr','','fr','French','Français','0');
		case 'he': return new CultureInfo('he','','he','Hebrew','עברית','1');
		case 'hu': return new CultureInfo('hu','','hu','Hungarian','Magyar','0');
		case 'is': return new CultureInfo('is','','is','Icelandic','Íslenska','0');
		case 'it': return new CultureInfo('it','','it','Italian','Italiano','0');
		case 'ja': return new CultureInfo('ja','','ja','Japanese','日本語','0');
		case 'ko': return new CultureInfo('ko','','ko','Korean','한국어','0');
		case 'nl': return new CultureInfo('nl','','nl','Dutch','Nederlands','0');
		case 'no': return new CultureInfo('no','','no','Norwegian','Norsk','0');
		case 'pl': return new CultureInfo('pl','','pl','Polish','Polski','0');
		case 'pt': return new CultureInfo('pt','','pt','Portuguese','Português','0');
		case 'ro': return new CultureInfo('ro','','ro','Romanian','Română','0');
		case 'ru': return new CultureInfo('ru','','ru','Russian','Русский','0');
		case 'hr': return new CultureInfo('hr','','hr','Croatian','Hrvatski','0');
		case 'sk': return new CultureInfo('sk','','sk','Slovak','Slovenčina','0');
		case 'sq': return new CultureInfo('sq','','sq','Albanian','Shqipe','0');
		case 'sv': return new CultureInfo('sv','','sv','Swedish','Svenska','0');
		case 'th': return new CultureInfo('th','','th','Thai','ไทย','0');
		case 'tr': return new CultureInfo('tr','','tr','Turkish','Türkçe','0');
		case 'ur': return new CultureInfo('ur','','ur','Urdu','اُردو','1');
		case 'id': return new CultureInfo('id','','id','Indonesian','Bahasa Indonesia','0');
		case 'uk': return new CultureInfo('uk','','uk','Ukrainian','Україньска','0');
		case 'be': return new CultureInfo('be','','be','Belarusian','Беларускі','0');
		case 'sl': return new CultureInfo('sl','','sl','Slovenian','Slovenski','0');
		case 'et': return new CultureInfo('et','','et','Estonian','Eesti','0');
		case 'lv': return new CultureInfo('lv','','lv','Latvian','Latviešu','0');
		case 'lt': return new CultureInfo('lt','','lt','Lithuanian','Lietuvių','0');
		case 'fa': return new CultureInfo('fa','','fa','Persian','فارسى','1');
		case 'vi': return new CultureInfo('vi','','vi','Vietnamese','Tiếng Việt','0');
		case 'hy': return new CultureInfo('hy','','hy','Armenian','Հայերեն','0');
		case 'az': return new CultureInfo('az','','az','Azeri','Azərbaycan­ılı','0');
		case 'eu': return new CultureInfo('eu','','eu','Basque','Euskara','0');
		case 'mk': return new CultureInfo('mk','','mk','Macedonian','Македонски јазик','0');
		case 'af': return new CultureInfo('af','','af','Afrikaans','Afrikaans','0');
		case 'ka': return new CultureInfo('ka','','ka','Georgian','ქართული','0');
		case 'fo': return new CultureInfo('fo','','fo','Faroese','Føroyskt','0');
		case 'hi': return new CultureInfo('hi','','hi','Hindi','हिंदी','0');
		case 'ms': return new CultureInfo('ms','','ms','Malay','Bahasa Malaysia','0');
		case 'kk': return new CultureInfo('kk','','kk','Kazakh','Қазащb','0');
		case 'ky': return new CultureInfo('ky','','ky','Kyrgyz','Кыргыз','0');
		case 'sw': return new CultureInfo('sw','','sw','Kiswahili','Kiswahili','0');
		case 'uz': return new CultureInfo('uz','','uz','Uzbek','U\'zbek','0');
		case 'tt': return new CultureInfo('tt','','tt','Tatar','Татар','0');
		case 'pa': return new CultureInfo('pa','','pa','Punjabi','ਪੰਜਾਬੀ','0');
		case 'gu': return new CultureInfo('gu','','gu','Gujarati','ગુજરાતી','0');
		case 'ta': return new CultureInfo('ta','','ta','Tamil','தமிழ்','0');
		case 'te': return new CultureInfo('te','','te','Telugu','తెలుగు','0');
		case 'kn': return new CultureInfo('kn','','kn','Kannada','ಕನ್ನಡ','0');
		case 'mr': return new CultureInfo('mr','','mr','Marathi','मराठी','0');
		case 'sa': return new CultureInfo('sa','','sa','Sanskrit','संस्कृत','0');
		case 'mn': return new CultureInfo('mn','','mn','Mongolian','Монгол хэл','0');
		case 'gl': return new CultureInfo('gl','','gl','Galician','Galego','0');
		case 'kok': return new CultureInfo('kok','','kok','Konkani','कोंकणी','0');
		case 'syr': return new CultureInfo('syr','','syr','Syriac','ܣܘܪܝܝܐ','1');
		case 'dv': return new CultureInfo('dv','','dv','Divehi','ދިވެހިބަސް','1');
		case 'zh-chs': return new CultureInfo('zh-CHS','zh-Hans','zh','Chinese (Simplified)','中文(简体)','0');
		case 'es': return new CultureInfo('es','','es','Spanish','Español','0');
		case 'sr': return new CultureInfo('sr','','sr','Serbian','Srpski','0');
		case 'am-et': return internal_getCultureInfo('am-ET');
		case 'tzm-latn-dz': return internal_getCultureInfo('tzm-Latn-DZ');
		case 'iu-latn-ca': return internal_getCultureInfo('iu-Latn-CA');
		case 'sma-no': return internal_getCultureInfo('sma-NO');
		case 'mn-mong-cn': return internal_getCultureInfo('mn-Mong-CN');
		case 'gd-gb': return internal_getCultureInfo('gd-GB');
		case 'prs-af': return internal_getCultureInfo('prs-AF');
		case 'bn-bd': return internal_getCultureInfo('bn-BD');
		case 'wo-sn': return internal_getCultureInfo('wo-SN');
		case 'rw-rw': return internal_getCultureInfo('rw-RW');
		case 'qut-gt': return internal_getCultureInfo('qut-GT');
		case 'sah-ru': return internal_getCultureInfo('sah-RU');
		case 'gsw-fr': return internal_getCultureInfo('gsw-FR');
		case 'co-fr': return internal_getCultureInfo('co-FR');
		case 'oc-fr': return internal_getCultureInfo('oc-FR');
		case 'mi-nz': return internal_getCultureInfo('mi-NZ');
		case 'ga-ie': return internal_getCultureInfo('ga-IE');
		case 'se-se': return internal_getCultureInfo('se-SE');
		case 'br-fr': return internal_getCultureInfo('br-FR');
		case 'smn-fi': return internal_getCultureInfo('smn-FI');
		case 'moh-ca': return internal_getCultureInfo('moh-CA');
		case 'arn-cl': return internal_getCultureInfo('arn-CL');
		case 'ii-cn': return internal_getCultureInfo('ii-CN');
		case 'dsb-de': return internal_getCultureInfo('dsb-DE');
		case 'ig-ng': return internal_getCultureInfo('ig-NG');
		case 'kl-gl': return internal_getCultureInfo('kl-GL');
		case 'lb-lu': return internal_getCultureInfo('lb-LU');
		case 'ba-ru': return internal_getCultureInfo('ba-RU');
		case 'nso-za': return internal_getCultureInfo('nso-ZA');
		case 'quz-bo': return internal_getCultureInfo('quz-BO');
		case 'yo-ng': return internal_getCultureInfo('yo-NG');
		case 'ha-latn-ng': return internal_getCultureInfo('ha-Latn-NG');
		case 'fil-ph': return internal_getCultureInfo('fil-PH');
		case 'ps-af': return internal_getCultureInfo('ps-AF');
		case 'fy-nl': return internal_getCultureInfo('fy-NL');
		case 'ne-np': return internal_getCultureInfo('ne-NP');
		case 'se-no': return internal_getCultureInfo('se-NO');
		case 'iu-cans-ca': return internal_getCultureInfo('iu-Cans-CA');
		case 'sr-latn-rs': return internal_getCultureInfo('sr-Latn-RS');
		case 'si-lk': return internal_getCultureInfo('si-LK');
		case 'sr-cyrl-rs': return internal_getCultureInfo('sr-Cyrl-RS');
		case 'lo-la': return internal_getCultureInfo('lo-LA');
		case 'km-kh': return internal_getCultureInfo('km-KH');
		case 'cy-gb': return internal_getCultureInfo('cy-GB');
		case 'bo-cn': return internal_getCultureInfo('bo-CN');
		case 'sms-fi': return internal_getCultureInfo('sms-FI');
		case 'as-in': return internal_getCultureInfo('as-IN');
		case 'ml-in': return internal_getCultureInfo('ml-IN');
		case 'or-in': return internal_getCultureInfo('or-IN');
		case 'bn-in': return internal_getCultureInfo('bn-IN');
		case 'tk-tm': return internal_getCultureInfo('tk-TM');
		case 'bs-latn-ba': return internal_getCultureInfo('bs-Latn-BA');
		case 'mt-mt': return internal_getCultureInfo('mt-MT');
		case 'sr-cyrl-me': return internal_getCultureInfo('sr-Cyrl-ME');
		case 'se-fi': return internal_getCultureInfo('se-FI');
		case 'zu-za': return internal_getCultureInfo('zu-ZA');
		case 'xh-za': return internal_getCultureInfo('xh-ZA');
		case 'tn-za': return internal_getCultureInfo('tn-ZA');
		case 'hsb-de': return internal_getCultureInfo('hsb-DE');
		case 'bs-cyrl-ba': return internal_getCultureInfo('bs-Cyrl-BA');
		case 'tg-cyrl-tj': return internal_getCultureInfo('tg-Cyrl-TJ');
		case 'sr-latn-ba': return internal_getCultureInfo('sr-Latn-BA');
		case 'smj-no': return internal_getCultureInfo('smj-NO');
		case 'rm-ch': return internal_getCultureInfo('rm-CH');
		case 'smj-se': return internal_getCultureInfo('smj-SE');
		case 'quz-ec': return internal_getCultureInfo('quz-EC');
		case 'quz-pe': return internal_getCultureInfo('quz-PE');
		case 'sr-latn-me': return internal_getCultureInfo('sr-Latn-ME');
		case 'sma-se': return internal_getCultureInfo('sma-SE');
		case 'ug-cn': return internal_getCultureInfo('ug-CN');
		case 'sr-cyrl-ba': return internal_getCultureInfo('sr-Cyrl-BA');
	}

}

/**
 * @internal 
 */
function internal_getCulturesByCurrency($code)
{
	switch( strtolower($code) )
	{
		case 'sar': return array('ar-SA');
		case 'bgl': return array('bg-BG');
		case 'eur': return array('de-DE','ca-ES','el-GR','fi-FI','fr-FR','it-IT','nl-NL','sk-SK','sl-SI','eu-ES','gl-ES','fr-BE','nl-BE','pt-PT','sv-FI','de-AT','es-ES','de-LU','fr-LU','en-IE','fr-MC','gsw-FR','co-FR','oc-FR','ga-IE','br-FR','smn-FI','dsb-DE','lb-LU','fy-NL','sms-FI','mt-MT','sr-Cyrl-ME','se-FI','hsb-DE','sr-Latn-ME');
		case 'twd': return array('zh-TW');
		case 'czk': return array('cs-CZ');
		case 'dkk': return array('da-DK','fo-FO','kl-GL');
		case 'usd': return array('en-US','en-029','es-EC','es-SV','es-PR','quz-EC','es-US');
		case 'ils': return array('he-IL');
		case 'huf': return array('hu-HU');
		case 'isk': return array('is-IS');
		case 'jpy': return array('ja-JP');
		case 'krw': return array('ko-KR');
		case 'nok': return array('nb-NO','nn-NO','sma-NO','se-NO','smj-NO');
		case 'pln': return array('pl-PL');
		case 'brl': return array('pt-BR');
		case 'rol': return array('ro-RO');
		case 'rur': return array('ru-RU','tt-RU');
		case 'hrk': return array('hr-HR');
		case 'all': return array('sq-AL');
		case 'sek': return array('sv-SE','se-SE','smj-SE','sma-SE');
		case 'thb': return array('th-TH');
		case 'try': return array('tr-TR');
		case 'pkr': return array('ur-PK');
		case 'idr': return array('id-ID');
		case 'uah': return array('uk-UA');
		case 'byb': return array('be-BY');
		case 'eek': return array('et-EE');
		case 'lvl': return array('lv-LV');
		case 'ltl': return array('lt-LT');
		case 'irr': return array('fa-IR');
		case 'vnd': return array('vi-VN');
		case 'amd': return array('hy-AM');
		case 'azm': return array('az-Latn-AZ','az-Cyrl-AZ');
		case 'mkd': return array('mk-MK');
		case 'zar': return array('af-ZA','en-ZA','nso-ZA','zu-ZA','xh-ZA','tn-ZA');
		case 'gel': return array('ka-GE');
		case 'inr': return array('hi-IN','pa-IN','gu-IN','ta-IN','te-IN','kn-IN','mr-IN','sa-IN','kok-IN','as-IN','ml-IN','en-IN','or-IN','bn-IN');
		case 'myr': return array('ms-MY','en-MY');
		case 'kzt': return array('kk-KZ');
		case 'kgs': return array('ky-KG');
		case 'kes': return array('sw-KE');
		case 'uzs': return array('uz-Latn-UZ','uz-Cyrl-UZ');
		case 'mnt': return array('mn-MN');
		case 'syp': return array('syr-SY','ar-SY');
		case 'mvr': return array('dv-MV');
		case 'iqd': return array('ar-IQ');
		case 'cny': return array('zh-CN','mn-Mong-CN','ii-CN','bo-CN','ug-CN');
		case 'chf': return array('de-CH','it-CH','fr-CH','de-LI','rm-CH');
		case 'gbp': return array('en-GB','gd-GB','cy-GB');
		case 'mxn': return array('es-MX');
		case 'csd': return array('sr-Latn-CS','sr-Cyrl-CS');
		case 'bnd': return array('ms-BN');
		case 'egp': return array('ar-EG');
		case 'hkd': return array('zh-HK');
		case 'aud': return array('en-AU');
		case 'cad': return array('en-CA','fr-CA','iu-Latn-CA','moh-CA','iu-Cans-CA');
		case 'lyd': return array('ar-LY');
		case 'sgd': return array('zh-SG','en-SG');
		case 'gtq': return array('es-GT','qut-GT');
		case 'dzd': return array('ar-DZ','tzm-Latn-DZ');
		case 'mop': return array('zh-MO');
		case 'nzd': return array('en-NZ','mi-NZ');
		case 'crc': return array('es-CR');
		case 'mad': return array('ar-MA');
		case 'pab': return array('es-PA');
		case 'tnd': return array('ar-TN');
		case 'dop': return array('es-DO');
		case 'omr': return array('ar-OM');
		case 'jmd': return array('en-JM');
		case 'veb': return array('es-VE');
		case 'yer': return array('ar-YE');
		case 'cop': return array('es-CO');
		case 'bzd': return array('en-BZ');
		case 'pen': return array('es-PE','quz-PE');
		case 'jod': return array('ar-JO');
		case 'ttd': return array('en-TT');
		case 'ars': return array('es-AR');
		case 'lbp': return array('ar-LB');
		case 'zwd': return array('en-ZW');
		case 'kwd': return array('ar-KW');
		case 'php': return array('en-PH','fil-PH');
		case 'clp': return array('es-CL','arn-CL');
		case 'aed': return array('ar-AE');
		case 'uyu': return array('es-UY');
		case 'bhd': return array('ar-BH');
		case 'pyg': return array('es-PY');
		case 'qar': return array('ar-QA');
		case 'bob': return array('es-BO','quz-BO');
		case 'hnl': return array('es-HN');
		case 'nio': return array('es-NI','ig-NG','yo-NG','ha-Latn-NG');
		case 'etb': return array('am-ET');
		case 'afn': return array('prs-AF','ps-AF');
		case 'bdt': return array('bn-BD');
		case 'xof': return array('wo-SN');
		case 'rwf': return array('rw-RW');
		case 'rub': return array('sah-RU','ba-RU');
		case 'npr': return array('ne-NP');
		case 'rsd': return array('sr-Latn-RS','sr-Cyrl-RS');
		case 'lkr': return array('si-LK');
		case 'lak': return array('lo-LA');
		case 'khr': return array('km-KH');
		case 'tmt': return array('tk-TM');
		case 'bam': return array('bs-Latn-BA','bs-Cyrl-BA','sr-Latn-BA','hr-BA','sr-Cyrl-BA');
		case 'tjs': return array('tg-Cyrl-TJ');
	}
}

/**
 * @internal 
 */
function internal_getAllCurrencyCodes()
{
	return array('AED','AFN','ALL','AMD','ARS','AUD','AZM','BAM','BDT','BGL','BHD','BND','BOB','BRL','BYB','BZD','CAD','CHF','CLP','CNY','COP','CRC','CSD','CZK','DKK','DOP','DZD','EEK','EGP','ETB','EUR','GBP','GEL','GTQ','HKD','HNL','HRK','HUF','IDR','ILS','INR','IQD','IRR','ISK','JMD','JOD','JPY','KES','KGS','KHR','KRW','KWD','KZT','LAK','LBP','LKR','LTL','LVL','LYD','MAD','MKD','MNT','MOP','MVR','MXN','MYR','NIO','NOK','NPR','NZD','OMR','PAB','PEN','PHP','PKR','PLN','PYG','QAR','ROL','RSD','RUB','RUR','RWF','SAR','SEK','SGD','SYP','THB','TJS','TMT','TND','TRY','TTD','TWD','UAH','USD','UYU','UZS','VEB','VND','XOF','YER','ZAR','ZWD');
}

/**
 * @internal 
 */
function internal_getAllCultureCodes()
{
	return array('af-ZA','am-ET','ar-AE','ar-BH','ar-DZ','ar-EG','ar-IQ','ar-JO','ar-KW','ar-LB','ar-LY','ar-MA','arn-CL','ar-OM','ar-QA','ar-SA','ar-SY','ar-TN','ar-YE','as-IN','az-Cyrl-AZ','az-Latn-AZ','ba-RU','be-BY','bg-BG','bn-BD','bn-IN','bo-CN','br-FR','bs-Cyrl-BA','bs-Latn-BA','ca-ES','co-FR','cy-GB','da-DK','de-AT','de-CH','de-DE','de-LI','de-LU','dsb-DE','dv-MV','el-GR','en-029','en-AU','en-BZ','en-CA','en-GB','en-IE','en-IN','en-JM','en-MY','en-NZ','en-PH','en-SG','en-TT','en-US','en-ZA','en-ZW','es-AR','es-BO','es-CL','es-CO','es-CR','es-DO','es-EC','es-ES','es-GT','es-HN','es-MX','es-NI','es-PA','es-PE','es-PR','es-PY','es-SV','es-US','es-UY','es-VE','et-EE','eu-ES','fa-IR','fi-FI','fil-PH','fo-FO','fr-BE','fr-CA','fr-CH','fr-FR','fr-LU','fr-MC','fy-NL','ga-IE','gd-GB','gl-ES','gsw-FR','gu-IN','ha-Latn-NG','he-IL','hi-IN','hr-BA','hr-HR','hsb-DE','hu-HU','hy-AM','id-ID','ig-NG','ii-CN','is-IS','it-CH','it-IT','iu-Cans-CA','iu-Latn-CA','ja-JP','ka-GE','kk-KZ','kl-GL','km-KH','kn-IN','kok-IN','ko-KR','ky-KG','lb-LU','lo-LA','lt-LT','lv-LV','mi-NZ','mk-MK','ml-IN','mn-MN','mn-Mong-CN','moh-CA','mr-IN','ms-BN','ms-MY','mt-MT','nb-NO','ne-NP','nl-BE','nl-NL','nn-NO','nso-ZA','oc-FR','or-IN','pa-IN','pl-PL','prs-AF','ps-AF','pt-BR','pt-PT','qut-GT','quz-BO','quz-EC','quz-PE','rm-CH','ro-RO','ru-RU','rw-RW','sah-RU','sa-IN','se-FI','se-NO','se-SE','si-LK','sk-SK','sl-SI','sma-NO','sma-SE','smj-NO','smj-SE','smn-FI','sms-FI','sq-AL','sr-Cyrl-BA','sr-Cyrl-CS','sr-Cyrl-ME','sr-Cyrl-RS','sr-Latn-BA','sr-Latn-CS','sr-Latn-ME','sr-Latn-RS','sv-FI','sv-SE','sw-KE','syr-SY','ta-IN','te-IN','tg-Cyrl-TJ','th-TH','tk-TM','tn-ZA','tr-TR','tt-RU','tzm-Latn-DZ','ug-CN','uk-UA','ur-PK','uz-Cyrl-UZ','uz-Latn-UZ','vi-VN','wo-SN','xh-ZA','yo-NG','zh-CN','zh-HK','zh-MO','zh-SG','zh-TW','zu-ZA');
}

/**
 * @internal 
 */
function internal_getAllRegionCodes()
{
	return array('029','AE','AF','AL','AM','AR','AT','AU','AZ','BA','BD','BE','BG','BH','BN','BO','BR','BY','BZ','CA','CH','CL','CN','CO','CR','CZ','DE','DK','DO','DZ','EC','EE','EG','ES','ET','FI','FO','FR','GB','GE','GL','GR','GT','HK','HN','HR','HU','IC','ID','IE','IL','IN','IQ','IR','IS','IT','JM','JO','JP','KE','KG','KH','KR','KW','KZ','LA','LB','LI','LK','LT','LU','LV','LY','MA','MC','ME','MK','MN','MO','MT','MV','MX','MY','NG','NI','NL','NO','NP','NZ','OM','PA','PE','PH','PK','PL','PR','PT','PY','QA','RO','RS','RU','RW','SA','SE','SG','SI','SK','SN','SV','SY','TH','TJ','TM','TN','TR','TT','TW','UA','US','UY','UZ','VE','VN','YE','ZA','ZW');
}

/**
 * @internal 
 */
function internal_getAllLanguageCodes()
{
	return array('af','am-ET','ar','arn-CL','as-IN','az','ba-RU','be','bg','bn-BD','bn-IN','bo-CN','br-FR','bs-Cyrl-BA','bs-Latn-BA','ca','co-FR','cy-GB','da','de','dsb-DE','dv','el','en','es','et','eu','fa','fi','fil-PH','fo','fr','fy-NL','ga-IE','gd-GB','gl','gsw-FR','gu','ha-Latn-NG','he','hi','hr','hsb-DE','hu','hy','id','ig-NG','ii-CN','is','it','iu-Cans-CA','iu-Latn-CA','ja','ka','kk','kl-GL','km-KH','kn','ko','kok','ky','lb-LU','lo-LA','lt','lv','mi-NZ','mk','ml-IN','mn','mn-Mong-CN','moh-CA','mr','ms','mt-MT','ne-NP','nl','no','nso-ZA','oc-FR','or-IN','pa','pl','prs-AF','ps-AF','pt','qut-GT','quz-BO','quz-EC','quz-PE','rm-CH','ro','ru','rw-RW','sa','sah-RU','se-FI','se-NO','se-SE','si-LK','sk','sl','sma-NO','sma-SE','smj-NO','smj-SE','smn-FI','sms-FI','sq','sr','sr-Cyrl-BA','sr-Cyrl-ME','sr-Cyrl-RS','sr-Latn-BA','sr-Latn-ME','sr-Latn-RS','sv','sw','syr','ta','te','tg-Cyrl-TJ','th','tk-TM','tn-ZA','tr','tt','tzm-Latn-DZ','ug-CN','uk','ur','uz','vi','wo-SN','xh-ZA','yo-NG','zh-CHS','zh-CHT','zu-ZA');
}
