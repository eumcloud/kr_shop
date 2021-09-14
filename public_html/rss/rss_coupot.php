<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<channel>";
echo "<fileversion>201103221533</fileversion>";
echo "<title><![CDATA[$company[homepage_title]]]></title>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";
echo "<description></description>";

##DATA FORMAT
$dataForm = "
<item>
	<type>c</type>
	<title>{##PNAME##}</title>
	<link><![CDATA[{##LINK##}]]></link>
	<image><![CDATA[{##PIMG##}]]></image>
	<description><![CDATA[{##PMSG##}]]></description>
	<category>{##CATEGORY##}</category>
	<area><![CDATA[{##RSSAREA1##}]]></area>
	<price_original>{##PRICEO##}</price_original>
	<price_discount>{##PRICES##}</price_discount>
	<price_percent>{##PRICER##}</price_percent>
	<count_max>{##CNTMAX##}</count_max>
	<count_now>{##CNTSALE##}</count_now> 
	<date_start>{##STT_DATETIME##}</date_start>
	<date_end>{##END_DATETIME##}</date_end>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>