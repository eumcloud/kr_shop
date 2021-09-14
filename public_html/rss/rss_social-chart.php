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

	<product_id>{##PID##}</product_id> 
	<product_url><![CDATA[{##LINK##}]]></product_url>
    <product_title>{##PNAME##}</product_title>
    <product_desc>{##PMSG##}</product_desc> 
    <product_area>{##RSSAREA1##}</product_area>
	<product_location>{##RSSAREA2##}</product_location>
    <product_category>{##CATEGORY##}</product_category> 
	<sale_end>{##END_DATETIME##}</sale_end> 
	<price_normal>{##PRICEO##}</price_normal> 
	<price_discount>{##PRICES##}</price_discount> 
    <discount_rate>{##PRICER##}</discount_rate>
	<buy_count>{##CNTSALE##}</buy_count> 
	<shop_name>{##SUP_NAME##}</shop_name>
	<image_url1><![CDATA[{##PIMG##}]]></image_url1>

</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>