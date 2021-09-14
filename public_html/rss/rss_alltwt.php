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
echo "<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">";
echo "<channel>";
echo "<fileversion>201106071834</fileversion>";
echo "<title>$home_title</title>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";
echo "<description><![CDATA[]]></description>";
##DATA FORMAT
$dataForm = "
     <item>
        <title><![CDATA[[{##PNAME##}]]></title>
        <link><![CDATA[{##LINK##}]]></link>
        <category><![CDATA[{##CATEGORY##}]]></category>
        <area><![CDATA[{##RSSAREA1##}]]></area>
        <img>{##PIMG##}</img>
        <nprice><![CDATA[{##PRICEO##}]]></nprice>
        <rprice><![CDATA[{##PRICES##}]]></rprice>
        <dcrate><![CDATA[{##PRICER##}]]></dcrate>
        <mincnt><![CDATA[{##CNTMIN##}]]></mincnt>
        <maxcnt><![CDATA[{##CNTMAX##}]]></maxcnt>
        <salecnt><![CDATA[{##CNTSALE##}]]></salecnt>
        <startdate><![CDATA[{##STT_DATETIME##}]]></startdate>
        <enddate><![CDATA[{##END_DATETIME##}]]></enddate>
        <description><![CDATA[{##PMSG##}]]></description>
    </item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>