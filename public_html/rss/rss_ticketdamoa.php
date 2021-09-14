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
echo "<ver>0</ver>"; 
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
	<count_max>{##CNTMAX##}</count_max>
	<count_min>{##CNTMIN##}</count_min>
	<address><![CDATA[{##SUP_ADDRESS##}]]></address>
	<desc_text><![CDATA[{##PMSG##}]]></desc_text>
	<photo1><![CDATA[{##PIMG##}]]></photo1>
	<type>c</type>
	<area><![CDATA[{##RSSAREA1##}]]></area>
	<area2><![CDATA[{##RSSAREA2##}]]></area2>
	<category><![CDATA[{##CATEGORY##}]]></category>
	<service_name>{##SUP_NAME##}</service_name>
    <service_phone>{##SUP_TEL##}</service_phone>
	<ad_num></ad_num>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>