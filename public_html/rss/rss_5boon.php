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
echo "<products>";
echo "<fileversion>201107111320</fileversion>";
//echo "<title>$home_title</title>";
//echo "<link>http://$_SERVER[HTTP_HOST]</link>";
//echo "<description><![CDATA[]]></description>";
##DATA FORMAT
$dataForm = "
     <product>
        <url><![CDATA[{##LINK##}]]></url>
        <logourl><![CDATA[{##LOGO##}]]></logourl>
        <division><![CDATA[{##CATEGORY##}]]></division>
        <region><![CDATA[{##RSSAREA1##}]]></region>
        <name><![CDATA[[{##PNAME##}]]></name>
        <image1><![CDATA[{##PIMG##}]]></image1>
        <image2></image2>
        <image3></image3>
        <descript><![CDATA[{##DETAIL##}]]></descript>
        <address><![CDATA[{##SUP_ADDRESS##}]]></address>
        <price>{##PRICEO##}</price>
        <saleprice>{##PRICES##}</saleprice>
        <salerate>{##PRICER##}</salerate>
        <fullcount>{##CNTMAX##}</fullcount>
        <mincnt>{##CNTMIN##}</mincnt>
        <maxcnt>{##BUYLIMIT##}</maxcnt>
        <salecnt>{##CNTSALE##}</salecnt>
        <limitdate>{##END_DATETIME##}</limitdate>
        <lng></lng>
        <lat></lat>
    </product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>