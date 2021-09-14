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
echo "<fileversion>201103140041</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
    <product_number><![CDATA[[{##PIDN##}]]></product_number>
    <product_category><![CDATA[{##CATEGORY##}]]></product_category>
    <product_fullname><![CDATA[[{##PNAME##}]]></product_fullname>
    <product_desc><![CDATA[{##PMSG##}]]></product_desc>
    <product_area><![CDATA[{##RSSAREA1##}]]></product_area>
    <shop_zipcode><![CDATA[{##SUP_ZIP##}]]></shop_zipcode>
    <shop_address1><![CDATA[{##SUP_ADDRESS##}]]></shop_address1>
    <shop_address2><![CDATA[{##SUP_ADDRESS1##}]]></shop_address2>
    <shop_address3><![CDATA[]]></shop_address3>
    <link_web><![CDATA[{##LINK##}]]></link_web>
    <link_image><![CDATA[{##PIMG##}]]></link_image>
    <price_original>{##PRICEO##}</price_original> 
    <price_discount>{##PRICES##}</price_discount> 
    <price_percent>{##PRICER##}</price_percent> 
    <sales_max><![CDATA[{##CNTMAX##}]]></sales_max>
    <sales_min><![CDATA[{##CNTMIN##}]]></sales_min>
    <sales_cur>{##CNTSALE##}</sales_cur>
    <sales_stock>{##STOCK##}</sales_stock>
    <sales_per>0</sales_per>
    <date_start><![CDATA[{##STT_DATETIME##}]]></date_start>
    <date_end><![CDATA[{##END_DATETIME##}]]></date_end> 
    <etc_etc><![CDATA[{##SUP_NAME##}]]></etc_etc>
    <etc_statusYN></etc_statusYN>
    <etc_eventYN></etc_eventYN>
    <map_location_ver></map_location_ver>
    <map_location_hor></map_location_hor>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
?>