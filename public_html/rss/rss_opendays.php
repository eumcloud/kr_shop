<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">";
echo "<fileversion>201103221533</fileversion>";
echo "<channel>";
echo "<title>$company[homepage_title]</title>";
echo "<link>http://$_SERVER[HTTP_HOST]></link>";
echo "<description><![CDATA[]]></description>";
##DATA FORMAT
$dataForm = "

<item>
	<title>{##PNAME##}</title>
	<link><![CDATA[{##LINK##}]]></link>
	<image1>{##PIMG##}</image1>
	<buyCount>{##CNTSALE##}</buyCount>
	<maxCount>{##CNTMAX##}</maxCount>
	<minCount>{##CNTMIN##}</minCount>
	<price>{##PRICES##}</price>
	<price0>{##PRICEO##}</price0>
	<addr0>{##SUP_ADDRESS##}</addr0>
	<addr1>{##SUP_ADDRESS1##}</addr1>
	<description><![CDATA[{##PMSG##}]]></description>
	<dc:date>{##STT_DATETIME##}</dc:date>
	<category0><![CDATA[{##CATEGORY##}]]></category0>
	<category1><![CDATA[{##CATEGORY##}]]></category1>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>