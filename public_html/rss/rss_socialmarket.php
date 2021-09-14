<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
$home_title = str_replace("&","&amp;",$company[homepage_title]);
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<channel>";
echo "<fileversion>201107111400</fileversion>";
//echo "<title>$home_title</title>";
//echo "<link>http://$_SERVER[HTTP_HOST]</link>";
///echo "<description><![CDATA[]]></description>";
##DATA FORMAT
$dataForm = "
     <item>
        <title><![CDATA[[{##PNAME##}]]></title>
        <link><![CDATA[{##LINK##}]]></link>
        <time_start><![CDATA[{##STT_DATETIME##}]]></time_start>
        <time_end><![CDATA[{##END_DATETIME##}]]></time_end>
        <price_original><![CDATA[{##PRICEO##}]]></price_original>
        <price_now><![CDATA[{##PRICES##}]]></price_now>
        <sale_percent><![CDATA[{##PRICER##}]]></sale_percent>
        <sell_count><![CDATA[{##CNTSALE##}]]></sell_count>
        <count_min><![CDATA[{##CNTMIN##}]]></count_min>
        <count_max><![CDATA[{##CNTMAX##}]]></count_max>
        <photo1><![CDATA[{##PIMG##}]]></photo1>
        <photo2></photo2>
        <photo3></photo3>
        <photo4></photo4>
        <photo5></photo5>
        <type>c</type>
        <area><![CDATA[{##RSSAREA1##}]]></area>
        <area2><![CDATA[{##RSSAREA2##}]]></area2>
        <category><![CDATA[{##CATEGORY##}]]></category>
        <category2></category2>
        <addr><![CDATA[{##SUP_ADDRESS##}]]></addr>
        <latitude></latitude>
		<longitude></longitude>
        <desc_text><![CDATA[{##PMSG##}]]></desc_text>
        <desc_html><![CDATA[{##DETAIL##}]]></desc_html>
    </item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>