<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<rss version='2.0' xmlns:dc='http://purl.org/dc/elements/1.1/'>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
    <title><![CDATA[{##PNAME##}]]></title>
    <image><![CDATA[{##PIMG##}]]></image>
    <link><![CDATA[{##LINK##}]]></link>
    <priceSale>{##PRICES##}</priceSale>
    <pricePub>{##PRICEO##}</pricePub>
    <discount><![CDATA[{##PRICER##}]]></discount>
    <nowCount><![CDATA[{##CNTSALE##}]]></nowCount>
    <nowStock>{##STOCK##}</nowStock>
    <description><![CDATA[{##PMSG##}]]></description>
    <area><![CDATA[{##RSSAREA1##}]]></area>
    <keyword>{##KEYWORD##}</keyword>
    <category><![CDATA[{##CATEGORY##}]]></category>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</rss>";
?>