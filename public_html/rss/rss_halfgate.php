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
echo "<fileversion>201103140041</fileversion>";
echo "<channel>";
echo "<title><![CDATA[$company[homepage_title]]]></title>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";
echo "<description><![CDATA[]]></description>";

/*
<!--로고-->
<logo><![CDATA[http://www.halfgate.co.kr/images/main/logo.gif]]></logo>
<!--공지사항 제목-->
<noticeTitle><![CDATA[사이트 점검]]></noticeTitle>
<!--공지사항 (HTML)-->
<noticeHtml><![CDATA[html]]></noticeHtml>
<!--이벤트 Title-->
<eventTitle><![CDATA[크리스마스 이벤트]]></eventTitle>
<!--이벤트 내용(HTML)-->
<eventHtml><![CDATA[html]]></eventHtml>
<!--이벤트 URL-->
<eventLink><![CDATA[http://...]]></eventLink>

<!-- 카테고리 코드값 입력 01(맛집),02(식품),03(패션),04(뷰티),05(디지털)
          ,06(가전),07(유아동),08(출산),09(생활),10(건강),11(문화),12(공연),13(여행),14(레저) -->

 <!-- 상품 판매 대표 지역[서울인 경우 : 강남, 강북, 종로, 강서, 강동][수도권, 광역시 및 기타시도:시지명까지만]시지명까지만] -->
	  <!-- ex.	부산(O), 광안리(x)	  -->

*/
echo "<logo><![CDATA[]]></logo>";
echo "<noticeTitle><![CDATA[]]></noticeTitle>";
echo "<noticeHtml><![CDATA[]]></noticeHtml>";
echo "<eventTitle><![CDATA[]]></eventTitle>";
echo "<eventHtml><![CDATA[]]></eventHtml>";
echo "<eventLink><![CDATA[]]></eventLink>";

##DATA FORMAT
$dataForm = "
<item>
    <meta_id><![CDATA[[{##PID##}]]></meta_id>
    <support_mobile>N</support_mobile>
    <mobile_url><![CDATA[]]></mobile_url>
    <max_count>{##CNTMAX##}</max_count>
    <min_count>{##CNTMIN##}</min_count>
    <shops>
		<shop>
            <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
            <shop_address><![CDATA[{##SUP_ADDRESS##}]]></shop_address>
            <shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
            <shop_region><![CDATA[{##RSSAREA2##}]]></shop_region>
            <shop_gps><![CDATA[]]></shop_gps>
        </shop>
    </shops>
    <title><![CDATA[[{##PNAME##}]]></title>
    <pricePub>{##PRICEO##}</pricePub> 
    <priceSale>{##PRICES##}</priceSale> 
    <discount>{##PRICER##}</discount> 
    <image><![CDATA[{##PIMG##}]]></image>
	<image><![CDATA[{##PIMG##}]]></image>
	<image><![CDATA[{##PIMG##}]]></image>
	<image><![CDATA[{##PIMG##}]]></image>
    <oimage><![CDATA[{##PIMG##}]]></oimage>
    <detailimage><![CDATA[]]></detailimage>
    <link><![CDATA[{##LINK##}]]></link>
    <startDt><![CDATA[{##STT_DATETIME##}]]></startDt>
    <endDt><![CDATA[{##END_DATETIME##}]]></endDt>
    <nowCount>{##CNTSALE##}</nowCount>
    <nowStock>{##STOCK##}</nowStock>
    <category>{##CATEGORY##}</category>
    <areaName><![CDATA[{##RSSAREA1##}]]></areaName>
    <description><![CDATA[{##PMSG##}]]></description>
    <descriptionHtml><![CDATA[{##DESC_ADDR##}]]></descriptionHtml>
    <movieLink><![CDATA[]]></movieLink>
    <keyword><![CDATA[{##KEYWORD##}]]></keyword>
    <talkLink><![CDATA[]]></talkLink>
    <status>1</status>
    <sendStartDt><![CDATA[]]></sendStartDt>
    <sendEndDt><![CDATA[]]></sendEndDt>
    <pubDate><![CDATA[{##NOW_DATETIMEHM##}]]></pubDate>   
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>