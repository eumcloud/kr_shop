<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<rss version=\"2.0\" xmlns:dc=\"http://purn.org/dc/elements/1.1/\">";
echo "<fileversion>201109281540</fileversion>";
echo "<channel>";
echo "<title><![CDATA[$company[homepage_title]]]></title>";
echo "<link><![CDATA[http://$_SERVER[HTTP_HOST]]]></link>";
echo "<description><![CDATA[]]></description>";


##DATA FORMAT
$dataForm = "
<item>
    <title><![CDATA[[{##PNAME##}]]></title>
    <link><![CDATA[{##LINK##}]]></link>
    <description><![CDATA[{##PMSG##}]]></description>
    <categoryname><![CDATA[{##CATEGORY##}]]></categoryname>
    <areaname><![CDATA[{##RSSAREA1##}]]></areaname>
    <address><![CDATA[{##SUP_ADDRESS##}]]></address>
    <image><![CDATA[{##PIMG##}]]></image>
    <originprice><![CDATA[{##PRICEO##}]]></originprice>
    <dcprice><![CDATA[{##PRICES##}]]></dcprice>
    <dcpercent><![CDATA[{##PRICER##}]]></dcpercent>
    <mincount><![CDATA[{##CNTMIN##}]]></mincount>
    <maxcount><![CDATA[{##CNTMAX##}]]></maxcount>
    <curcount><![CDATA[{##CNTSALE##}]]></curcount>
    <close><![CDATA[0]]></close>
    <startdate><![CDATA[{##STT_DATETIMEHM##}]]></startdate>
    <enddate><![CDATA[{##END_DATETIMEHM##}]]></enddate>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>