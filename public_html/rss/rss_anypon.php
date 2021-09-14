<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<rss version=\"2.0\">";
echo "<fileversion>201103120037</fileversion>";
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
    <image><![CDATA[{##PIMG##}]]></image>
    <originprice><![CDATA[{##PRICEO##}]]></originprice>
    <dcprice><![CDATA[{##PRICES##}]]></dcprice>
    <dcpercent><![CDATA[{##PRICER##}]]></dcpercent>
    <mincount><![CDATA[{##CNTMIN##}]]></mincount>
    <maxcount><![CDATA[{##CNTMAX##}]]></maxcount>
    <maxorder><![CDATA[{##BUYLIMIT##}]]></maxorder>
    <curcount><![CDATA[{##CNTSALE##}]]></curcount>
    <close><![CDATA[1]]></close>
    <startdate><![CDATA[{##STT_DATETIMEHM##}]]></startdate>
    <enddate><![CDATA[{##END_DATETIMEHM##}]]></enddate>
    <pubdate><![CDATA[{##NOW_DATETIMEHM##}]]></pubdate>
    <shopname><![CDATA[{##SUP_NAME##}]]></shopname>
    <address><![CDATA[{##SUP_ADDRESS##}]]></address>
	<lng>0.0</lng>
	<lat>0.0</lat>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>