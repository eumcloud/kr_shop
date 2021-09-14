<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<rss version=\"2.0\">";
echo "<fileversion>201103120037</fileversion>";
echo "<channel>";
echo "<title><![CDATA[$company[homepage_title]]]></title>";
echo "<link><![CDATA[http://$_SERVER[HTTP_HOST]]]></link>";
echo "<description><![CDATA[]]></description>";
echo "<language>ko</language>";
echo "<webMaster><![CDATA[]]></webMaster>";  //이메일등록

/*
카테고리 선택.. 
음식/외식: A, 
뷰티/패션: B, 
공연/전시: C, 
여행/레져: D, 
교육/취미: E, 
원데이상품: F, 
티켓/쿠폰: G 
*/

##DATA FORMAT
$dataForm = "
<item>
    <title><![CDATA[[{##PNAME##}]]></title>
    <today_url><![CDATA[{##LINK##}]]></today_url>
    <content_description><![CDATA[{##PMSG##}]]></content_description>
    <shopname><![CDATA[{##SUP_NAME##}]]></shopname>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <image_url><![CDATA[{##PIMG##}]]></image_url>
    <original_price>{##PRICEO##}</original_price>
    <sale_price>{##PRICES##}</sale_price>
    <discount_rate>{##PRICER##}</discount_rate>
    <start_time><![CDATA[{##STT_DATETIME##}]]></start_time>
    <end_time><![CDATA[{##END_DATETIME##}]]></end_time>
    <max_count>{##CNTMAX##}</max_count>
    <min_count>{##CNTMIN##}</min_count>
    <now_count>{##CNTSALE##}</now_count>
    <area><![CDATA[{##RSSAREA1##}]]></area>
    <location><![CDATA[{##RSSAREA2##}]]></location>
	<status>0</status>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>