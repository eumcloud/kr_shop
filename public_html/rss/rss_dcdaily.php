<?php

header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<channel>";
echo "<fileversion>201107261710</fileversion>";
echo "<title>$company[homepage_title]</title>";


##DATA FORMAT
$dataForm = "
<item>
    <type>C</type>
    <title><![CDATA[{##PNAME##}]]></title>
    <link><![CDATA[{##LINK##}]]></link>
    <photo1><![CDATA[{##PIMG##}]]></photo1>
    <price_original><![CDATA[{##PRICEO##}]]></price_original>
    <price_now><![CDATA[{##PRICES##}]]></price_now>
    <sale_percent><![CDATA[{##PRICER##}]]></sale_percent>
	<count_max>{##CNTMAX##}</count_max>
    <count_min>{##CNTMIN##}</count_min>
	<sell_count>{##CNTSALE##}</sell_count>
    <area><![CDATA[{##RSSAREA1##}]]></area>
    <category>{##CATEGORY##}</category>
    <shop>{##SUP_NAME##}</shop>
    <addr>{##SUP_ADDRESS##}</addr>
    <phone>{##SUP_TEL##}</phone>    
    <lat>0.0</lat>
	<lng>0.0</lng>
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