<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
$home_title = str_replace("&","&amp;",$company[homepage_title]);
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<fileversion>201107141517</fileversion>";
echo "<products>";

##DATA FORMAT
$dataForm = "
<product>
	<url><![CDATA[{##LINK##}]]></url>
    <logourl></logourl>
    <mainimage><![CDATA[{##PIMG##}]]></mainimage>
    <division>{##CATEGORY##}</division>
    <region><![CDATA[{##RSSAREA1##}]]></region>
	<name><![CDATA[{##PNAME##}]]></name>
    <blogkey></blogkey>
	<sitename><![CDATA[{##SUP_NAME##}]]></sitename>
    <image1></image1>
	<image2></image2>
    <image3></image3>
    <descript><![CDATA[{##PMSG##}]]></descript>
	<address>{##SUP_ADDRESS##}</address>
	<price>{##PRICEO##}</price>
	<saleprice>{##PRICES##}</saleprice>
	<salerate>{##PRICER##}</salerate>
	<fullcount></fullcount>
    <mincnt>{##CNTMIN##}</mincnt>
	<maxcnt>{##CNTMAX##}</maxcnt>
    <salecnt></salecnt>
	<startdate>{##STT_DATETIME##}</startdate>
	<enddatetime>{##END_DATETIME##}</enddatetime>
    <lat>0.0</lat>
	<lng>0.0</lng>
</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>
