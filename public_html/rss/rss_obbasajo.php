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
echo "<fileversion>201104221006</fileversion>";

##DATA FORMAT
$dataForm = "
<product>

	<url><![CDATA[{##LINK##}]]></url>
	<logourl><![CDATA[]]></logourl>
	<mainimage><![CDATA[{##PIMG##}]]></mainimage>
	<bigdeal><![CDATA[{##PIMG##}]]></bigdeal>
	<category>{##CATEGORY##}</category>
	<region><![CDATA[{##RSSAREA1##}]]></region>
	<name><![CDATA[{##PNAME##}]]></name>
	<blogkey><![CDATA[]]></blogkey>
	<sitename><![CDATA[{##SUP_NAME##}]]></sitename>
	<image1><![CDATA[{##PIMG##}]]></image1>
	<image2><![CDATA[{##PIMG##}]]></image2>
    <image3><![CDATA[{##PIMG##}]]></image3>
	<descript><![CDATA[{##PMSG##}]]></descript>
	<address><![CDATA[{##SUP_ADDRESS##}]]></address>
	<price>{##PRICEO##}</price>
	<saleprice>{##PRICES##}</saleprice>
	<salelate>{##PRICER##}</salelate>
	<fullcount>{##STOCK##}</fullcount>
	<mincnt>{##CNTMIN##}</mincnt>
	<maxcnt>{##CNTMAX##}</maxcnt>
	<salecnt>{##CNTSALE##}</salecnt>
	<startdate>{##STT_DATETIME##}</startdate>
	<enddate>{##END_DATETIME##}</enddate>
	<lng>0.0</lng>
	<lat>0.0</lat>
</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>