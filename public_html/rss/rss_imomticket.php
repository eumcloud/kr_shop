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
##DATA FORMAT
$dataForm = "
<item>

	<title><![CDATA[{##PNAME##}]]></title>
	<link><![CDATA[{##LINK##}]]></link>
	<photo1><![CDATA[{##PIMG##}]]></photo1>
	<price_original>{##PRICEO##}</price_original>
	<price_now>{##PRICES##}</price_now>
	<sale_percent>{##PRICER##}</sale_percent>
	<count_min>{##CNTMIN##}</count_min>
	<count_max>{##CNTMAX##}</count_max>
	<sell_count>{##CNTSALE##}</sell_count>
	<area><![CDATA[{##RSSAREA1##}]]></area>
	<category>{##CATEGORY##}</category>
	<shop><![CDATA[{##SUP_NAME##}]]></shop>
	<addr><![CDATA[{##SUP_ADDRESS##}]]></addr>
	<phone><![CDATA[{##SUP_TEL##}]]></phone>
	<lat></lat>
	<lng></lng>
	<time_start>{##STT_DATETIME##}</time_start>
	<time_end>{##END_DATETIME##}</time_end>
	<desc_text><![CDATA[{##PMSG##}]]></desc_text>
	<status>Y</status>
	
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>