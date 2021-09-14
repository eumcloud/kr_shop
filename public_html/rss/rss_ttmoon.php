<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<products>";
echo "<fileversion>201107141541</fileversion>";
##DATA FORMAT
$dataForm = "
<product>
    
    <product_id>{##PID##}</product_id> 
    <product_type>c</product_type>
    <product_url><![CDATA[{##LINK##}]]></product_url>
    <product_title><![CDATA[{##PNAME##}]]></product_title>
    <product_desc>{##PMSG##}</product_desc> 
    <product_area>{##RSSAREA1##}</product_area> 
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
    <product_division>{##CATEGORY##}</product_division>
	
</product>";


##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>