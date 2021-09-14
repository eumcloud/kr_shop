<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<fileversion>201103120037</fileversion>";
echo "<product>";




##DATA FORMAT
$dataForm = "
<item>
    <title><![CDATA[[{##PNAME##}]]></title>
    <link><![CDATA[{##LINK##}]]></link>
    <mobile_link></mobile_link>
    <description><![CDATA[{##PMSG##}]]></description>
    <categoryname><![CDATA[{##CATEGORY##}]]></categoryname>
    <areaname><![CDATA[{##RSSAREA1##}]]></areaname>
    <address><![CDATA[{##SUP_ADDRESS##}]]></address>
    <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
    <shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
    <image><![CDATA[{##PIMG##}]]></image>
    <originprice>{##PRICEO##}</originprice>
    <dcprice>{##PRICES##}</dcprice>
    <dcpercent>{##PRICER##}</dcpercent>
    <mincount>{##CNTMIN##}</mincount>
    <maxcount>{##CNTMAX##}</maxcount>
    <curcount>{##CNTSALE##}</curcount>
    <close>1</close>
    <startdate><![CDATA[{##STT_DATETIME##}]]></startdate>
    <enddate><![CDATA[{##END_DATETIME##}]]></enddate>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</product>";
echo "</rss>";
?>
