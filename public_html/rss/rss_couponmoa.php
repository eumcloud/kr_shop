<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include "./addon_rss.php";

##START
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<products>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<product>
    <product_id>{##PID##}</product_id> 
    <product_url><![CDATA[{##LINK##}]]></product_url>
    <mobile_url><![CDATA[{##MLINK##}]]></mobile_url>
    <product_url2><![CDATA[{##LINK##}]]></product_url2>
    <mobile_url2><![CDATA[{##MLINK##}]]></mobile_url2>
    <product_title><![CDATA[{##PNAME##}]]></product_title>
    <product_desc><![CDATA[{##PMSG##}]]></product_desc> 
    <product_area>{##RSSAREA1##}</product_area> 

	<categorys>
		<category>
			<category1><![CDATA[{##CATEGORY_01##}]]></category1> 
			<category2><![CDATA[{##CATEGORY_02##}]]></category2> 
			<category3><![CDATA[{##CATEGORY_03##}]]></category3> 
		</category>
	</categorys>

    <sale_start>{##STT_DATETIME##}</sale_start> 
    <sale_end>{##END_DATETIME##}</sale_end> 
    <coupon_use_start>{##BEG_DATETIME##}</coupon_use_start> 
    <coupon_use_end>{##EXP_DATETIME##}</coupon_use_end> 

    <buy_count>{##CNTSALE##}</buy_count> 
    <buy_limit>{##BUYLIMIT##}</buy_limit> 
    <buy_max>{##CNTMAX##}</buy_max> 
	<free_shipping><![CDATA[]]></free_shipping>
    <price_normal>{##PRICEO##}</price_normal> 
    <price_discount>{##PRICES##}</price_discount> 
    <discount_rate>{##PRICER##}</discount_rate> 
    <image_url1><![CDATA[{##PIMG##}]]></image_url1> 
    <image_url2 /> 

	<shops>
		<shop>
			<shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
			<shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
			<shop_address><![CDATA[{##SUP_ADDRESS##}]]></shop_address>
		</shop>
	</shops>

</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>