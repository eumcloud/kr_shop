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
echo "<ver>0</ver>"; 
echo "<fileversion>201103221533</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
	<title><![CDATA[[{##PNAME##}]]></title>
	<link><![CDATA[{##LINK##}]]></link>
	<time_start>{##STT_DATETIME##}</time_start>
	<time_end>{##END_DATETIME##}</time_end>
	<price_original>{##PRICEO##}</price_original>
	<price_now>{##PRICES##}</price_now>
	<sale_percent>{##PRICER##}</sale_percent>
	<sell_count>{##CNTSALE##}</sell_count>
	<count_min>{##CNTMIN##}</count_min>
	<count_max>{##CNTMAX##}</count_max>
	<photo1><![CDATA[{##PIMG##}]]></photo1>
	<type>c</type>
	<area><![CDATA[{##RSSAREA1##}]]></area>
	<category><![CDATA[{##CATEGORY##}]]></category>
	<addr><![CDATA[{##SUP_ADDRESS##}]]></addr> 
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>