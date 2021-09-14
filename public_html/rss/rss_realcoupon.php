<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<rss version='2.0' xmlns:dc='http://purl.org/dc/elements/1.1/'>";
echo "<channel>";
echo "<fileversion>201106201200</fileversion>";
echo "<title>$company[homepage_title]</title>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";
echo "<description><![CDATA[]]></description>";

echo "<image>";
echo "<url><![CDATA[http://$_SERVER[HTTP_HOST]/images/group/top_logo.gif]]></url>";
echo "<title><![CDATA[$company[homepage_title]]]></title>";
echo "<link><![CDATA[http://$_SERVER[HTTP_HOST]]]></link>";
echo "</image>";

echo "<category>소셜쿠폰</category>";
echo "<language>ko</language>";
echo "<pubDate></pubDate>";
echo "<ttl>60</ttl>";

##DATA FORMAT
$dataForm = "
<item>
    <prod_no><![CDATA[{##PID##}]]></prod_no>
    <prod_img><![CDATA[{##PIMG##}]]></prod_img> 
    <category><![CDATA[{##CATEGORY##}]]></category>
    <keyword>{##KEYWORD##}</keyword>
    <title><![CDATA[{##PNAME##}]]></title>
    <description><![CDATA[{##PNAME##}]]></description>
    <link><![CDATA[{##LINK##}]]></link>
    <mlink><![CDATA[{##LINK##}]]></mlink>
    <shop_name>{##SUP_NAME##}</shop_name>
    <shop_tel>{##SUP_TEL##}</shop_tel>
    <location><![CDATA[{##RSSAREA1##}]]></location>
    <zipcode>{##SUP_ZIP1##}</zipcode>
    <addr_city>{##SUP_ZIP2##}</addr_city>
    <addr_gu></addr_gu>
    <addr_dong></addr_dong>
    <addr_etc></addr_etc>
    <price_original><![CDATA[{##PRICEO##}]]></price_original>
    <price_discount><![CDATA[{##PRICES##}]]></price_discount>
    <price_percent><![CDATA[{##PRICER##}]]></price_percent>
    <sale_max_num><![CDATA[{##CNTMAX##}]]></sale_max_num>
    <sale_min_num><![CDATA[{##CNTMIN##}]]></sale_min_num>
    <sale_now_num><![CDATA[{##CNTSALE##}]]></sale_now_num>
    <sale_remain_num>{##CNTSALE##}</sale_remain_num>
    <sale_info><![CDATA[{##CAUTION##}]]></sale_info>
    <startdate><![CDATA[{##STT_DATETIME##}]]></startdate>
    <enddate><![CDATA[{##END_DATETIME##}]]></enddate>
    <map_tag><![CDATA[]]></map_tag>
    <zone_horizon></zone_horizon>
	<zone_vertical></zone_vertical>
    <status>Y</status>
    <pubDate></pubDate>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>