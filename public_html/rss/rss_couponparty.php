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
echo "<title><![CDATA[$company[homepage_title]]]></title>";
echo "<link><![CDATA[http://$_SERVER[HTTP_HOST]]]></link>";
echo "<description><![CDATA[]]></description>";
##DATA FORMAT
$dataForm = "
<item>
	
	<uid><![CDATA[$uid]]></uid>
	<link><![CDATA[{##LINK##}]]></link>
	<mobilelink><![CDATA[]]></mobilelink>
	<category><![CDATA[{##CATEGORY##}]]></category>
	<title><![CDATA[{##PNAME##}]]></title>
	<description><![CDATA[{##PMSG##}]]></description>
	<imageurl><![CDATA[{##PIMG##}]]></imageurl>
	<shopname><![CDATA[{##SUP_NAME##}]]></shopname>
	<shoptel><![CDATA[{##SUP_TEL##}]]></shoptel>
	<shopaddress><![CDATA[{##SUP_ADDRESS##}]]></shopaddress>
	<startdatetime><![CDATA[{##STT_DATETIME##}]]></startdatetime>
	<enddatetime><![CDATA[{##END_DATETIME##}]]></enddatetime>
	<usestartdatetime><![CDATA[{##BEG_DATETIMEHM2##}]]></usestartdatetime>
	<useenddatetime><![CDATA[{##EXP_DATETIMEHM##}]]></useenddatetime>
	<originalprice>{##PRICEO##}</originalprice>			
	<downprice>{##PRICES##}</downprice>
	<downpercent>{##PRICER##}</downpercent>
	<mincount>{##CNTMIN##}</mincount>
	<maxcount>{##CNTMAX##}</maxcount>
	<nowcount>{##CNTSALE##}</nowcount>
	<end>0</end>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>