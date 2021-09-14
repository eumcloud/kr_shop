<?php
header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<rss version=\"2.0\">";
echo "<channel>";
echo "<fileversion>201103221533</fileversion>";
echo "<title><![CDATA[$company[homepage_title]]]></title>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";
echo "<description><![CDATA[]]></description>";
echo "<noticeTitle><![CDATA[사이트 점검]]></noticeTitle>";
echo "<noticeHtml><![CDATA[html]]></noticeHtml>";
echo "<eventTitle><![CDATA[크리스마스 이벤트]]></eventTitle>";
echo "<eventHtml><![CDATA[html]]></eventHtml>";
echo "<eventLink><![CDATA[http://...]]></eventLink>";

##DATA FORMAT
$dataForm = "
<item>

	<title><![CDATA[{##PNAME##}]]></title>
	<pricePub>{##PRICEO##}</pricePub>			
	<priceSale>{##PRICES##}</priceSale>
	<discount>{##PRICER##}</discount>
	<image><![CDATA[{##PIMG##}]]></image>
	<image><![CDATA[{##PIMG##}]]></image>
	<image><![CDATA[{##PIMG##}]]></image>
	<image><![CDATA[{##PIMG##}]]></image>
	<oimage><![CDATA[{##PIMG##}]]></oimage>
	<detailimage><![CDATA[{##PIMG##}]]></detailimage>
	<link><![CDATA[{##LINK##}]]></link>
	<startDt><![CDATA[{##STT_DATETIME##}]]></startDt>
	<endDt><![CDATA[{##END_DATETIME##}]]></endDt>
	<nowcount>{##CNTSALE##}</nowcount>
	<nowStock>{##STOCK##}</nowStock>
	<category>{##CATEGORY##}</category>
	<areaName><![CDATA[{##RSSAREA1##}]]></areaName>
	<description><![CDATA[{##PMSG##}]]></description>
	<descriptionHtml><![CDATA[HTML]]></descriptionHtml>
	<movieLink><![CDATA[http://...]]></movieLink>
	<keyword><![CDATA[{##KEYWORD##}]]></keyword>
	<talkLink><![CDATA[http://]]></talkLink>
	<status>1</status>
	<sendStartDt><![CDATA[2010-11-10 00:00:00]]></sendStartDt>
	 <sendEndDt><![CDATA[2010-11-10 23:59:59]]></sendEndDt>
	 <pubDate><![CDATA[2010-11-10 00:05:00]]></pubDate>
	
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>