<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<items>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
    <url><![CDATA[{##LINK##}]]></url>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <address><![CDATA[{##SUP_ADDRESS##}]]></address>
    <location><![CDATA[{##RSSAREA1##}]]></location>
    <title><![CDATA[[{##PNAME##}]]></title>
    <image><![CDATA[{##PIMG##}]]></image>
    <price>{##PRICEO##}</price>
    <dcprice>{##PRICES##}</dcprice>
    <dcrate>{##PRICER##}</dcrate>
    <mincnt>{##CNTMIN##}</mincnt>
    <maxcnt>{##CNTMAX##}</maxcnt>
    <salecnt>{##CNTSALE##}</salecnt> 
    <startdate>{##STT_DATETIME##}</startdate>
    <limitdate>{##END_DATETIME##}</limitdate>
    <expire>{##END_DATETIME##}</expire>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
?>