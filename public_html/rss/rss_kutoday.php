<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<channel>";
echo "<fileversion>201103221533</fileversion>";

##DATA FORMAT
$dataForm = "
<item>

	<linkUrl><![CDATA[{##LINK##}]]></linkUrl>
	<title><![CDATA[{##PNAME##}]]></title>
	<subtitle></subtitle>
	<description><![CDATA[{##PMSG##}]]></description>
	<storeName>{##SUP_NAME##}</storeName>
	<storeAddr>{##SUP_ADDRESS##}</storeAddr>
	<storePhone>{##SUP_TEL##}</storePhone>
	<storePark>0</storePark>
	<author>{##PID##}</author>
	<category>{##CATEGORY##}</category>
	<area><![CDATA[{##RSSAREA1##}]]></area>
	<minCount>{##CNTMIN##}</minCount>
	<maxCount>{##CNTMAX##}</maxCount>
	<curCount>{##CNTSALE##}</curCount>
	<pubDate>{##NOW_DATETIME##}</pubDate>
	<simage><![CDATA[{##PIMG##}]]></simage>
	<mimage><![CDATA[{##PIMG##}]]></mimage>
	<price>{##PRICEO##}</price>
	<dcPrice>{##PRICES##}</dcPrice>
	<dcRate>{##PRICER##}</dcRate>
	<dcinfo></dcinfo>
	<shipFree>Y</shipFree>
	<iteminfo></iteminfo>
	<sDate>{##STT_DATETIME##}</sDate>
	<eDate>{##END_DATETIME##}</eDate>

	
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>