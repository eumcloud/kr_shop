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
echo "<fileversion>201107141436</fileversion>";
##DATA FORMAT
$dataForm = "
<item>

    <title><![CDATA[{##PNAME##}]]></title>
    <link><![CDATA[{##LINK##}]]></link>
    <time_start>{##STT_DATETIME##}</time_start>
    <time_end>{##END_DATETIME##}</time_end>
    <price_original>{##PRICEO##}</price_original>
    <price_now>{##PRICES##}</price_now>
    <price_percent>{##PRICER##}</price_percent>
    <sell_count>{##CNTSALE##}</sell_count>
    <count_min>{##CNTMIN##}</count_min>
    <count_max>{##CNTMAX##}</count_max>
    <photo1><![CDATA[{##PIMG##}]]></photo1>
    <photo2><![CDATA[{##PIMG##}]]></photo2>
    <photo3><![CDATA[{##PIMG##}]]></photo3>
    <photo4><![CDATA[{##PIMG##}]]></photo4>	
    <photo5><![CDATA[{##PIMG##}]]></photo5>
    <type>c</type>
    <area><![CDATA[{##RSSAREA1##}]]></area>
    <area2><![CDATA[{##RSSAREA2##}]]></area2>
    <category>{##CATEGORY##}</category>
    <category2>{##CATEGORY##}</category2>
    <addr>{##SUP_ADDRESS##}</addr>
    <latitude>0.0</latitude>
    <longitude>0.0</longitude>
    <desc_text><![CDATA[{##PMSG##}]]></desc_text>
    <desc_html><![CDATA[{##DESC_ADDR##}]]></desc_html>	
</item>";



##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>