<?php
header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<rss version='2.0' xmlns:dc='http://purl.org/dc/elements/1.1/'>";
echo "<fileversion>201105251115</fileversion>";
echo "<channel>";

##DATA FORMAT
$dataForm = "
<item>	
	<product_code>{##PPID##}</product_code>
	<category><![CDATA[{##CATEGORY##}]]></category>
	<addr><![CDATA[{##SUP_ADDRESS##}]]></addr>
	<title><![CDATA[{##PNAME##}]]></title>
	<time_start>{##STT_DATETIME##}</time_start>
    <time_end>{##END_DATETIME##}</time_end>
    <price_pub>{##PRICEO##}</price_pub>
    <price_sale>{##PRICES##}</price_sale>
    <sale>{##PRICER##}</sale>	
    <link><![CDATA[{##LINK##}]]></link>
	<description><![CDATA[{##PMSG##}]]></description>
	<cnt_min>{##CNTMIN##}</cnt_min>
    <cnt_max>{##CNTMAX##}</cnt_max>
    <cnt_sale>{##CNTSALE##}</cnt_sale>
    <cnt_saletoday>{##CNTSALETODAY##}</cnt_saletoday>
    <pic_small><![CDATA[{##PIMG##}]]></pic_small>
	<pic_1><![CDATA[{##PIMG##}]]></pic_1>
    <pic_2><![CDATA[{##PIMG##}]]></pic_2>
    <lng>0.0</lng>
    <lat>0.0</lat>    
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>