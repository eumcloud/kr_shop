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
echo "<title>$company[homepage_title]</title>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";

##DATA FORMAT
$dataForm = "
<item>

	<title><![CDATA[{##PNAME##}]]></title>
	<link><![CDATA[{##LINK##}]]></link>
	<category>{##CATEGORY##}</category>
	<shopname><![CDATA[{##SUP_NAME##}]]></shopname>
	<description><![CDATA[{##PMSG##}]]></description>
	<img_url><![CDATA[{##PIMG##}]]></img_url>
	<startdate>{##STT_DATETIME##}</startdate>
	<enddate>{##END_DATETIME##}</enddate>
	<original_price>{##PRICEO##}</original_price>
	<down_price>{##PRICES##}</down_price>
	<down_percent>{##PRICER##}</down_percent>
	<now_count>{##CNTSALE##}</now_count>
	<success_count>{##CNTMAX##}</success_count>
	<addr><![CDATA[{##SUP_ADDRESS##}]]></addr>

	
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>