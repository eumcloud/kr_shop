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
echo "<items>";
echo "<fileversion>201107120950</fileversion>";

##DATA FORMAT
$dataForm = "
     <item>
        <remoteIdx><![CDATA[{##PPID##}]]></remoteIdx> 
        <url><![CDATA[{##LINK##}]]></url>
        <title><![CDATA[[{##PNAME##}]]></title>
        <detail><![CDATA[[{##DETAIL##}]]></detail>
        <dateFrom>{##STT_DATETIME##}</dateFrom>
        <dateTo>{##END_DATETIME##}</dateTo>
        <shopName><![CDATA[{##SUP_NAME##}]]></shopName>
        <shopPhone>{##SUP_TEL##}</shopPhone>
        <shopAddress><![CDATA[{##SUP_ADDRESS##}]]></shopAddress>
        <curCount>{##CNTSALE##}</curCount>
        <minCount>{##CNTMIN##}</minCount>
        <maxCount>{##CNTMAX##}</maxCount>
        <nomalPrice>{##PRICEO##}</nomalPrice>
        <salePrice>{##PRICES##}</salePrice>
        <saleRate>{##PRICER##}</saleRate>
        <image><![CDATA[{##PIMG##}]]></image>
        <area><![CDATA[{##RSSAREA1##}]]></area>
        <category><![CDATA[{##CATEGORY##}]]></category>
    </item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
?>