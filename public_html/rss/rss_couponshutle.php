<?php

/* 2011-02-08 : tindevil
//카테고리및 지역정보는 현재 필드데이터를 그대로 사용합니다.
//아래요구조건에 맞추려면 별도의 데이터조작이 필요함

지역목차(area노드 부분에 출력 해주시면 됩니다.)
전국, 가로수길/신사, 강남역/역삼, 논현/영동, 목동, 사당, 삼성/선릉, 서래마을/반포, 신림/봉천,
서초/교대, 신천/잠실, 압구정/청담, 양재/도곡, 여의도, 건대/광진, 광화문, 대학로, 마포, 명동,
신촌/이대, 이태원/한남, 인사동/삼청동, 종로/청계천, 영등포/신도림, 홍대, 강남기타, 강북기타,
분당, 일산, 부산, 대구, 인천, 울산, 대전, 부천, 제주, 수원, 여행상품, 창원, 강원, 안양, 광주,
거제, 청주, 진주, 천안, 기타지역

카테고리목차(category노드 부분에 출력 해주시면 됩니다.)
미분류, 맛집/카페, 뷰티/생활, 여행/레저, 공연/전시, 패션, 교육, 배송상품/기타
*/

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<coupon_feed>";
echo "<fileversion>201103120037</fileversion>";
echo "<name><![CDATA[$company[homepage_title]]]></name>";
echo "<url><![CDATA[http://$_SERVER[HTTP_HOST]]]></url>";
echo "<deals>";

##DATA FORMAT
$dataForm = "
<deal>
    <title><![CDATA[{##PNAME##}]]></title>
    <description><![CDATA[{##PMSG##}]]></description>
    <url><![CDATA[{##LINK##}]]></url>
    <original>{##PRICEO##}</original> 
    <discount>{##PRICER##}</discount> 
    <price>{##PRICES##}</price> 
    <now_count>{##CNTSALE##}</now_count> 
    <max_count>{##CNTMAX##}</max_count> 
    <min_count>{##CNTMIN##}</min_count> 
    <images>
        <image><![CDATA[{##PIMG##}]]></image>
    </images>
    <start_at><![CDATA[{##STT_DATETIME##}]]></start_at>
    <end_at><![CDATA[{##END_DATETIME##}]]></end_at>
    <category><![CDATA[{##CATEGORY##}]]></category>

    <shops>
        <shop>
            <shop_address><![CDATA[{##SUP_ADDRESS##}]]></shop_address>
            <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
            <shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
            <region><![CDATA[{##RSSAREA1##}]]></region>
        </shop>
    </shops>
</deal>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</deals>";
echo "</coupon_feed>";
?>