<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<products>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
		<product><![CDATA[{##PID##}]]></product>
		<sale_start><![CDATA[{##STT_DATETIME##}]]></sale_start>
		<limitdate><![CDATA[{##END_DATETIME##}]]></limitdate>
		<name><![CDATA[{##PNAME##}]]></name>
		<descript><![CDATA[{##PMSG##}]]></descript>
		<region><![CDATA[{##RSSAREA1##}]]></region>
		<url><![CDATA[{##LINK##}]]></url>
		<price><![CDATA[{##PRICEO##}]]></price>
		<salerate><![CDATA[{##PRICER##}]]></salerate>
		<saleprice><![CDATA[{##PRICES##}]]></saleprice>
		<maxcnt>{##CNTMAX##}</maxcnt>
		<mincnt>{##CNTMIN##}</mincnt>
		<salecnt>{##CNTSALE##}</salecnt>
		<shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
		<address><![CDATA[{##SUP_ADDRESS##}]]></address>
		<shop_tel>{##SUP_TEL##}</shop_tel>
		<image1><![CDATA[{##PIMG##}]]></image1>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>