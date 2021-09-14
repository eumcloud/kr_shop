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
echo "<fileversion>201103221533</fileversion>";
##DATA FORMAT
$dataForm = "
<product>

        <product_id>{##PPID##}</product_id>
        <product_title><![CDATA[{##PNAME##}]]></product_title>
        <product_desc><![CDATA[{##PMSG##}]]></product_desc>
        <sale_start>{##STT_DATETIME##}</sale_start>
        <sale_end>{##END_DATETIME##}</sale_end>
        <price_normal>{##PRICEO##}</price_normal>
        <price_discount>{##PRICES##}</price_discount>
        <discount_rate>{##PRICER##}</discount_rate>
        <buy_count>{##CNTSALE##}</buy_count>
        <buy_max>{##CNTMAX##}</buy_max>
        <buy_limit>{##CNTMIN##}</buy_limit>
        <image_url1><![CDATA[{##PIMG##}]]></image_url1>
        <product_url><![CDATA[{##LINK##}]]></product_url>
        <category1>{##RSSAREA1##}</category1>
        <category2>{##CATEGORY##}</category2>
        <shop_name>{##SUP_NAME##}</shop_name>
        <shop_addr>{##SUP_ADDRESS##}</shop_addr>

</product>";


##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>