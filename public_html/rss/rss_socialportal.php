<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
//echo "<rss version='2.0' xmlns:dc='http://purl.org/dc/elements/1.1/'>";
echo "<channel>";
echo "<fileversion>201106220210</fileversion>";
echo "<title>$company[homepage_title]</title>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";
echo "<description><![CDATA[]]></description>";

echo "<siteinfo>";
echo "<name><![CDATA[$company[homepage_title]]]></name>";
echo "<url><![CDATA[http://$_SERVER[HTTP_HOST]/images/group/top_logo.gif]]></url>";
echo "<logo><![CDATA[http://$_SERVER[HTTP_HOST]]]></logo>";
echo "</siteinfo>";

##DATA FORMAT
$dataForm = "
<item>
    <idx><![CDATA[[{##CATEID##}]]></idx>
    <title><![CDATA[[{##PNAME##}]]></title>
    <link><![CDATA[{##LINK##}]]></link>
    <time_start><![CDATA[{##STT_DATETIME##}]]></time_start>
    <time_end><![CDATA[{##END_DATETIME##}]]></time_end>
    <coupon_wdate_start>{##BEG_DATE##}</coupon_wdate_start>
    <coupon_wdate_end>{##EXP_DATE##}</coupon_wdate_end>
    <price_original><![CDATA[{##PRICEO##}]]></price_original>
    <price_now><![CDATA[{##PRICES##}]]></price_now>
    <sale_percent><![CDATA[{##PRICER##}]]></sale_percent>
    <sell_count>{##CNTSALE##}</sell_count>
    <count_min><![CDATA[{##CNTMIN##}]]></count_min>
    <count_max><![CDATA[{##CNTMAX##}]]></count_max>
    <photo1><![CDATA[{##PIMG##}]]></photo1>
    <photo2><![CDATA[{##PIMG##}]]></photo2>
    <photo3><![CDATA[{##PIMG##}]]></photo3>
    <photo4><![CDATA[{##PIMG##}]]></photo4>
    <photo5><![CDATA[{##PIMG##}]]></photo5>
    <area><![CDATA[{##RSSAREA1##}]]></area>
    <area2><![CDATA[{##RSSAREA1##}]]></area2>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <category2><![CDATA[{##CATEGORY##}]]></category2>
    <shop><![CDATA[{##SUP_NAME##}]]></shop>
    <tel>{##SUP_TEL##}</tel>
    <addr><![CDATA[{##SUP_ADDRESS##}]]></addr>
    <latitude></latitude>
	<longitude></longitude>
	<desc_text><![CDATA[{##PMSG##}]]]></desc_text>
	<desc_html><![CDATA[]]></desc_html>
    <mobile_link><![CDATA[]]></mobile_link>
    <mobile_paysupport></mobile_paysupport>
    <partner_link><![CDATA[]]></partner_link>
    <commission_price></commission_price>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
//echo "</rss>";
?>