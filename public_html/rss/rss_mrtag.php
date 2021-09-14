<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");


## 미스터티켓 : 사이트 (http://www.mr-tag.com) RSS정보 : http://www.mr-tag.com/rss_sample.xml
## 카테고리 : [맛집, 카페/술집, 공연/전시, 레저/취미, 뷰티/생활, 여행, 기타]
## 지역 : [전체, 강남, 강북, 경기, 인천, 대전, 대구, 부산, 광주, 해외, 기타]

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<items>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
    <url><![CDATA[{##LINK##}]]></url>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <region><![CDATA[{##RSSAREA1##}]]></region>
    <image1><![CDATA[{##PIMG##}]]></image1>
    <image2><![CDATA[{##PIMG##}]]></image2>
    <descript><![CDATA[{##PNAME##}]]></descript>
    <price>{##PRICEO##}</price>
    <saleprice>{##PRICES##}</saleprice>
    <salerate>{##PRICER##}</salerate>
    <mincnt>{##CNTMIN##}</mincnt> 
    <maxcnt>{##CNTMAX##}</maxcnt> 
    <salecnt>{##CNTSALE##}</salecnt>
	<lng></lng>
	<lat></lat>
    <start_date><![CDATA[{##STT_DATETIME##}]]></start_date>
    <end_date><![CDATA[{##END_DATETIME##}]]></end_date>

</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
?>