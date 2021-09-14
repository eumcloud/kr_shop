<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<items>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<deal>
    <url><![CDATA[{##LINK##}]]></url>
    <><![CDATA[]]></>
    <image><![CDATA[{##PIMG##}]]></image>
	<title><![CDATA[{##PNAME##}]]></title>
	<description><![CDATA[{##PMSG##}]]></description>
    <original>{##PRICEO##}</original>			
	<price>{##PRICES##}</price>
	<discount>{##PRICER##}</discount>
    <min_count>{##CNTMIN##}</min_count>
	<max_count>{##CNTMAX##}</max_count>
	<now_count>{##CNTSALE##}</now_count>
	<shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
	<shop_address>{##SUP_ADDRESS##}</shop_address>
	<shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
	<start_at>{##STT_DATETIME##}</start_at>
	<end_at>{##END_DATETIME##}</end_at>
</deal>";

##FORLOOP
RunLoop($dataForm);

##END
echo "<items>";
?>