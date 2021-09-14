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
echo "<fileversion>201105251147</fileversion>";
##DATA FORMAT
$dataForm = "
<product>

	<category>{##CATEGORY##}</category>
	<name><![CDATA[{##PNAME##}]]></name>
	<description><![CDATA[{##PMSG##}]]></description>
	<price_original>{##PRICEO##}</price_original>
    <price_rate>{##PRICER##}</price_rate>
	<price_discount>{##PRICES##}</price_discount>
	<sdate>{##STT_DATETIME##}</sdate>
	<edate>{##END_DATETIME##}</edate>
	<image><![CDATA[{##PIMG##}]]></image>		
	<url><![CDATA[{##LINK##}]]></url>
	<min_cnt>{##CNTMIN##}</min_cnt>
	<max_cnt>{##CNTMAX##}</max_cnt>
	<sale_cnt>{##CNTSALE##}</sale_cnt>
	<shops>
	<shop>
	<shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
	<shop_address><![CDATA[{##SUP_ADDRESS##}]]></shop_address>
	<shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
	<latitude></latitude>
	<longitude></longitude>
	</shop>
	</shops>
	
</product>";


##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>