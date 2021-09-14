<?php

header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정

echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
echo "<coupon_feed>";
echo "<version><![CDATA[1]]></version>";
echo "<fileversion>201107191400</fileversion>";
echo "<deals>";
##DATA FORMAT
$dataForm = "
<deal>
    <deal_id><![CDATA[{##AID##}]]></deal_id>
    <deal_url><![CDATA[{##LINK##}]]></deal_url>
    <title><![CDATA[{##PNAME##}]]></title>
    <description><![CDATA[{##PMSG##}]]></description>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <sale_start><![CDATA[{##STT_DATETIME##}]]></sale_start>
    <sale_end><![CDATA[{##END_DATETIME##}]]></sale_end>
    <coupon_start><![CDATA[{##BEG_DATE##}]]></coupon_start>
    <coupon_end><![CDATA[{##EXP_DATETIME2##}]]></coupon_end>
    <original_price><![CDATA[{##PRICEO##}]]></original_price>
    <sale_price><![CDATA[{##PRICES##}]]></sale_price>
    <current_count><![CDATA[{##CNTSALE##}]]></current_count>
    <max_count><![CDATA[{##CNTMAX##}]]></max_count>
    <image1><![CDATA[{##PIMG##}]]></image1>
    <image2><![CDATA[{##PIMG##}]]></image2>
    <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
    <shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
    <shop_address><![CDATA[{##SUP_ADDRESS##}]]></shop_address>
    <shop_homepage><![CDATA[]]></shop_homepage>
    <shop_parking><![CDATA[]]></shop_parking>
    <shop_bizhours><![CDATA[]]></shop_bizhours>
    <shop_closed><![CDATA[]]></shop_closed>
    <shop_trafficinfo><![CDATA[]]></shop_trafficinfo>
</deal>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</deals>";
echo "</coupon_feed>";
?>