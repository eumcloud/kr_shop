<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<p_xmltags>";
echo "<fileversion>201105271004</fileversion>";
##DATA FORMAT
$dataForm = "
<p_xmltag>
    <p_id>{##PID##}</p_id> 
    <p_url><![CDATA[{##LINK##}]]></p_url>
    <p_title><![CDATA[{##PNAME##}]]></p_title>
    <p_desc><![CDATA[{##PMSG##}]]></p_desc> 
    <p_area><![CDATA[{##RSSAREA1##}]]></p_area>  
    <p_category><![CDATA[{##CATEGORY##}]]></p_category>     
    <sale_start>{##STT_DATETIME##}</sale_start> 
    <sale_end>{##END_DATETIME##}</sale_end> 
    <coupon_use_start>{##BEG_DATETIME##}</coupon_use_start> 
    <coupon_use_end>{##EXP_DATETIME##}</coupon_use_end> 
    <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
    <shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
    <shop_address><![CDATA[{##SUP_ADDRESS##}]]></shop_address>
    <buy_count>{##CNTSALE##}</buy_count>
    <buy_limit>{##BUYLIMIT##}</buy_limit>
    <buy_max>{##CNTMAX##}</buy_max>
    <price_normal>{##PRICEO##}</price_normal>
    <price_discount>{##PRICES##}</price_discount>
    <discount_rate>{##PRICER##}</discount_rate>
    <image_url1><![CDATA[{##PIMG##}]]></image_url1>
</p_xmltag>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</p_xmltags>";
?>