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
echo "<fileversion>201107141517</fileversion>";
echo "<title>$company[homepage_title]</title>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";
echo "<description><![CDATA[]]></description>";
##DATA FORMAT
$dataForm = "
<item>
    <idx><![CDATA[{##PID##}]]></idx>
    <locaiton><![CDATA[{##RSSAREA1##}]]></locaiton>
    <locaiton2><![CDATA[{##RSSAREA2##}]]></locaiton2>
    <address><![CDATA[{##SUP_ADDRESS##}]]></address>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <title><![CDATA[{##PNAME##}]]></title>
    <description><![CDATA[{##PMSG##}]]></description>
    <tag></tag>
    <link><![CDATA[{##LINK##}]]></link>
    <image><![CDATA[{##PIMG##}]]></image>
    <originalPrice>{##PRICEO##}</originalPrice>			
    <salePrice>{##PRICES##}</salePrice>
    <saleScale>{##PRICER##}</saleScale>
    <buyPerson>{##CNTSALE##}</buyPerson>
    <minPerson>{##CNTMIN##}</minPerson>
    <maxPerson>{##CNTMAX##}</maxPerson>
    <sDate>{##STT_DATETIME##}</sDate>
    <eDate>{##END_DATETIME##}</eDate>
    <finish>0</finish>
    <expireDate>{##EXP_DATE2##}</expireDate>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>