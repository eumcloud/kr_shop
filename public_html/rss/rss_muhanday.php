<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<products>";
echo "<fileversion>201103120037</fileversion>";
echo "<url><![CDATA[$_SERVER[HTTP_HOST]]]></url>";

/*
<url>       : 상품링크(http...)
<area1>     : 위치정보1(서울)
<area2>     : 위치정보2(강남)
<category>  : 카테고리명(뷰티~)
<name>      : 상품명
<desc>      : 상품설명(SMS구문)
<detail>    : 상품상세설명
<price>     : 판매가
<saleprice> : 할인가
<salerate>  : 할인율
<mincnt>    : 최소구매도달인원
<maxcnt>    : 최대수량
<salecnt>   : 현재판매량
<startdate> : 판매시작일(년-월-일 시:분:초_
<enddate>   : 판매종료일
<shop_location> : 매장위치
<shop_name>     : 공급업체명
<shop_addr>     : 공급업체주소
*/

##DATA FORMAT
$dataForm = "
<product>
    <pid><![CDATA[{##PID##}]]></pid>
    <mimg><![CDATA[{##PIMG##}]]></mimg>
    <url><![CDATA[{##LINK##}]]></url>
    <caution><![CDATA[{##CAUTION##}]]></caution>
    <area1><![CDATA[{##RSSAREA1##}]]></area1>
    <area2><![CDATA[{##RSSAREA2##}]]></area2>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <name><![CDATA[{##PNAME##}]]></name>
    <desc><![CDATA[{##PMSG##}]]></desc>
    <detail><![CDATA[{##DETAIL##}]]></detail> 
    <price><![CDATA[{##PRICEO##}]]></price> 
    <saleprice><![CDATA[{##PRICES##}]]></saleprice>
    <salerate><![CDATA[{##PRICER##}]]></salerate>
    <mincnt><![CDATA[{##CNTMIN##}]]></mincnt> 
    <maxcnt><![CDATA[{##CNTMAX##}]]></maxcnt> 
    <stock><![CDATA[{##STOCK##}]]></stock> 
    <salecnt><![CDATA[{##CNTSALE##}]]></salecnt> 
    <startdate><![CDATA[{##STT_DATETIME##}]]></startdate> 
    <enddate><![CDATA[{##END_DATETIME##}]]></enddate> 
    <shop_location><![CDATA[{##SUP_IMAGE##}]]></shop_location> 
    <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name> 
    <shop_addr><![CDATA[{##SUP_ADDRESS##}]]></shop_addr> 
</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>