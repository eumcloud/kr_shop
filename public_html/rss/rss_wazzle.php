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
	<product_id>{##PIDN##}</product_id>
	<product_title>{##PNAME##}</product_title>
	<product_desc>{##PMSG##}</product_desc>
	<sale_start>{##STT_DATETIME##}</sale_start>
	<sale_end>{##END_DATETIME##}</sale_end>
	<price_normal>{##PRICEO##}</price_normal>
	<price_discount>{##PRICES##}</price_discount>
	<discount_rate>{##PRICER##}</discount_rate>
	<buy_count>{##CNTSALE##}</buy_count>
	<count_max>{##CNTMAX##}</count_max>
	<image_url1>{##PIMG##}</image_url1>
	<product_url><![CDATA[{##LINK##}]]></product_url>
	<service>{##SUP_NAME##}</service>
	<category1>{##CATEGORY##}</category1>
	<category2>{##CATEGORY##}</category2>
	<address>{##SUP_ADDRESS##}</address>
</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>