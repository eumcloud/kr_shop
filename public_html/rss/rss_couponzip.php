<?php

## 쿠폰집 RSS양식 : 2011-01-29 : tindevil
## 카테고리목록 : 맛집c01,공연c02,Lifec03,여행c04,미분류c05
## 지역코드 : 전국00,강남01,홍대/신촌02,강서03,ㅏㄱㅇ북04,강동05,경기06,일산07,분당08,인천/부천09,부산10,울산11
##            대구12,경북13,경남14,대전15,천안16,충남17,충북18,광주19,전남20,전북21,춘천22,강원23,미분류24

// 필요한 설정파일 불러오기
include_once("../include/inc.php");


function getRowCount($addonquery) {
    $addonResult = @mysql_query($addonquery);
   return @mysql_num_rows($addonResult);
}


##솔루션의 형태를 파악한다 odt 와 sns 두종류가있음
$slntype = "odt";
if ( getRowCount("show tables like 'snsProduct'")*1 > 0 ) 
{
    $slntype = "sns";
}
##판매종료패치확인
$datepatch = getRowCount("show columns from ".$slntype."Product where Field in ('sale_enddate')")*1;

##현재판매되는 상품을 찾기(미래상품은 제외됨)
function saleItem($cateCode,$slntype,$datepatch) {
    $today = date('Y-m-d');
    $que = "select code from ".$slntype."Product ";
    $que .= "where code = parent_code and cateCode = '$cateCode' and sale_date <= '$today' ";

    if ($datepatch) {
       $que .= " and sale_enddate >= '$today'";
    }

    $que = $que."order by sale_date desc limit 1";
    $res = mysql_query($que);
    return @mysql_result($res,0);
}


#지역코드값을 반환
function GetAreaCode($param1)
{
    return $param1;
    switch($param1)
    {
        case "전체":
        case "전국":
            return  "00";
            break;
        case "강남":
            return  "01";
            break;
        case "홍대":
        case "신촌":
            return  "02";
            break;
        case "강서":
            return  "03";
            break;
        case "강북":
            return  "04";
            break;
        case "강동":
            return  "05";
            break;
        case "경기":
            return  "06";
            break;
        case "일산":
            return  "07";
            break;
        case "분당":
            return  "08";
            break;
        case "인천":
        case "부천":
            return  "09";
            break;
        case "부산":
            return  "10";
            break;
        case "울산":
            return  "11";
            break;
        case "대구":
            return  "12";
            break;
        case "경북":
        case "경상북도":
            return  "13";
            break;
        case "경남":
        case "경상남도":
            return  "14";
            break;
        case "대전":
            return  "15";
            break;
        case "천안":
            return  "16";
            break;
        case "충남":
        case "충청남도":
            return  "17";
            break;
        case "충북":
        case "충청북도":
            return  "18";
            break;
        case "광주":
            return  "19";
            break;
        case "전남":
        case "전라남도":
            return  "20";
            break;
        case "전북":
        case "전라북도":
            return  "21";
            break;
        case "춘천":
            return  "22";
            break;
        case "강원":
            return  "23";
            break;
        case "분류없음":
        case "미분류":
        case "기타":
            return  "24";
            break;
        default:
            return $param1;
    }
}

#카테고리 코드값을 반환
function GetCateCode($param1)
{
    return $param1;
    switch($param1)
    {
        case "맛집":
            return  "c01";
            break;
        case "공연":
            return  "c02";
            break;
        case "LITE":
            return  "c03";
            break;
        case "life":
            return  "c03";
            break;
        case "여행":
            return  "c04";
            break;
        case "분류없음":
        case "미분류":
        case "기타":
            return  "c05";
            break;
        default:
            return $param1;
    }
}

// TEXT 형식으로 변환
function get_html($str)
{
    $str = str_replace("&gt;", ">", $str);
    $str = str_replace("&lt;", "<", $str);
    $str = str_replace("&amp;", "&", $str);
    $str = str_replace("&quot;", "\"", $str);
    return $str;
}

function indexOf($needle, $haystack){ 
    $c = count($needle); 
    for($i = 0; $i < $c; $i++){ 
        if($needle[$i] == $haystack) return $i; 
    } 
    return -1; 
} 




Header("Content-type: text/xml"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");   

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<deals>";
echo "<fileversion>201103120037</fileversion>";
$basic = mysql_fetch_array(mysql_query("SELECT * FROM ".$slntype."Setup WHERE serialnum='1'"));
$author = explode("-",$basic[licenseNumber]);
$author_base = "O".$author[1];

//$CQuery  = "select catecode,catename from ".$slntype."Category where cHidden = 'no' order by catecode asc  ";
$CQuery  = "select * from odtProduct where sale_date <= CURDATE() and sale_enddate >= CURDATE() and stock > 0 order by sale_date asc  ";
$CResult = mysql_query($CQuery);
while ($CRecord = mysql_fetch_array($CResult))
{
//    $cateCode = $CRecord[catecode]; //지역코드	
//    $cateName = $CRecord[catename];//$row_cate[catename]

    $author = $author_base.$cateCode;   //UNIQ코드

//    $code = saleItem($cateCode,$slntype,$datepatch);
//    if(!$code) continue;    //현재판매중인 item 이없으면 PASS

    ##상품정보 불러오기
//    $Query = "select * from ".$slntype."Product where code = '".$code."'";
//    $Result = mysql_query($Query);
//    $row = mysql_fetch_array($Result);
	$row = $CRecord;

	// - 텍스트 정보 추출 ---
	$row = array_merge($row , _text_info_extraction( "odtProduct" , $row[serialnum] ));

    ##상품명
    $ProdName = $row[mainName] ? $row[mainName] : $row[name];

    ##상품의재고가없거나 상품명이 없을경우 넘어간다.
    if(!$row[stock] || !$ProdName) continue;

    #공급업체정보조회
    $userInfo = @mysql_fetch_array(@mysql_query("select * from ".$slntype."Member where id='".$row[customerCode]."'"));
    $company_name = $userInfo[cName];
    $company_addr = $userInfo[address];

    ##서브상품을 포함하여 판매량 합계
    $CntSum = $row[saleCnt];                    //판매량 //@mysql_result(@mysql_query("select sum(saleCnt) as a from ".$slntype."Product where parent_code = '".$row[parent_code]."'"),0);
//    $CntMin = $row[saleCntMax];                 //목표도달인원
	$CntMin = 1; // 1로 고정
    $CntMax = $row[saleCnt]*1+$row[stock]*1;    //최대수량


    ##상품,쿠폰구분
    if ( $row[setup_delivery] == "Y" )  { $ptype = "상품"; }
    else                                { $ptype = "쿠폰"; }

    $dateM =substr($row[sale_enddate],5,2);
    $dateD =substr($row[sale_enddate],8,2);
    $dateY =substr($row[sale_enddate],0,4);
    $SaleTime = mktime(0,0,0,$dateM,$dateD,$dateY);
    
    if ($basic[changeTime]=="00") {
        $NextDay = date("Y-m-d",mktime(0,0,0,date("m",$SaleTime),date("d",$SaleTime),date("Y",$SaleTime)));
    } else {
        $NextDay = date("Y-m-d",mktime(0,0,0,date("m",$SaleTime),date("d",$SaleTime)+1,date("Y",$SaleTime)));
    }

    //$NextDay = $row[sale_date]."=".$dateM."/".$dateD."/".$dateY;

    $end_hour = $basic[changeTime]=="00" ? "24" :  $basic[changeTime];
    $stt_datetime = $row[sale_date]."-".$basic[changeTime]."-00-00";

    $end_hour -= 1;
    $end_hours =  sprintf("%02d",$end_hour);

    $end_datetime = $NextDay."-".$end_hours."-59-59";


    ##공급업체 연락처
    unset($company_tel);
    if ( $userInfo[tel2] ) {
        $company_tel = $userInfo[tel1]."-".$userInfo[tel2]."-".$userInfo[tel3];
    }
    if ( !$company_tel && $userInfo[htel2] ) {
        $company_tel = $userInfo[htel1]."-".$userInfo[htel2]."-".$userInfo[htel3];
    }

    ##상품의 갱신일자데이터가없으므로 입력일자로 대신한다
    $edit_time = str_replace(":","-",$row[inputDate]);
    $edit_time = str_replace(" ","-",$edit_time);

    $link_url = "http://".$_SERVER[HTTP_HOST]."/?pn=product.view&pcode=".$row[code];

    ##상품기본이미지
    $main_img = $row[main_img] ? "http://".$_SERVER[HTTP_HOST]."/upfiles/product/".$row[main_img] : "";

    ##상품의 추가이미지(1~5 : 상세이미지로 교체해야함)
    $des_img1 = $row[big1_img] ? "http://".$_SERVER[HTTP_HOST].$row[big1_img] : "";
    $des_img2 = $row[big2_img] ? "http://".$_SERVER[HTTP_HOST].$row[big2_img] : "";
    $des_img3 = $row[big3_img] ? "http://".$_SERVER[HTTP_HOST].$row[big3_img] : "";
    $des_img4 = $row[big4_img] ? "http://".$_SERVER[HTTP_HOST].$row[big4_img] : "";
    $des_img5 = $row[big5_img] ? "http://".$_SERVER[HTTP_HOST].$row[big5_img] : "";

    ##지역코드데이터 확인
    $AreaStr = "/(전국|전체|강남|홍대|신촌|강서|강북|강동|경기|일산|분당|인천|부천|부산|울산|대구|경북|경상북도|경남|경상남도|대전|천안|충남|충청남도|충북|충청북도|광주|전남|전라남도|전북|전라북도|춘천|강원|미분류|분류없음|기타)/";
    unset($area);
    if ($row[rssarea1]) {
        preg_match($AreaStr, $row[rssarea1], $matches);
        $area = $matches[1];
    }
    if( !$area && $row[rssarea2] ) {
        preg_match($AreaStr, $row[rssarea2], $matches);
        $area = $matches[1];
    }
    if( !$area && $company_addr ) {
        preg_match($AreaStr, $company_addr, $matches);
        $area = $matches[1];
    }
    if ( $area ) $area = GetAreaCode($area);

    ##카테고리코드데이터 확인
    $CateStr = "/(맛집|기타|없음|미분류|공연|라이프|LIFE|life|여행)/";
    unset($category);
    if ($row[rsscate]) {
        preg_match($CateStr, $row[rsscate], $matches);
        $category = $matches[1];
    }
    if( !$category && $cateName ) {
        preg_match($CateStr, $cateName, $matches);
        $category = $matches[1];
    }
    if( !$category && $ProdName ) {
        preg_match($CateStr, $ProdName, $matches);
        $category = $matches[1];
    }
    if ( $category ) $category = GetCateCode($category);


    ##상세설명 이미지 추출
    unset($detailImg);
    $detailImgStr = get_html($row[comment2]);
    preg_match_all("/<IMG[^>]*SRC=[\"']?([^>\"']+)[\"']?[^>]*>/i",$detailImgStr, $matches);  
    foreach($matches as $key => $value)  
    {  
        foreach($value as $key_2 => $value_2)  
        {  
            $value_2 =  ereg_replace(".thumb","",$value_2);
            $value_2 =  ereg_replace("IMG src=","",$value_2);
            $value_2 =  ereg_replace("img src=","",$value_2);
            $value_2 =  ereg_replace("IMG align=absMiddle src=","",$value_2);
            $value_2 =  ereg_replace("IMG align=center src=","",$value_2);
            $value_2 =  ereg_replace("<\"","",$value_2);
            $value_2 =  ereg_replace("\">","",$value_2);
            $detailImg[] = $value_2;
        }  
        break;  
    }

    for ($i=0;$i<5;$i++) {
        $buffer = $detailImg[$i];

        $firsttag = indexOf(str_split($buffer), chr(34));
        $firstcolon = indexOf(str_split($buffer), ":");

        if ($firsttag == -1) {
            $detailImg[$i] = $buffer;
        } else {

            if ($firsttag < $firstcolon) {
                $buffer = substr($buffer,$firsttag+1);
            }
            $endtag = indexOf(str_split($buffer), chr(34));
            if ($endtag == -1) {
                $detailImg[$i] = $buffer;
            } else {
                $detailImg[$i] = substr($buffer,0,$endtag);
            }
        }
        $detailImg[$i] = str_replace("\\","",$detailImg[$i]);
        $detailImg[$i] = str_replace(" ","",$detailImg[$i]);
        if ( $firstcolon == -1 && $detailImg[$i] != "" ) {   //:가없다면 상대경로이다. 수정
            $detailImg[$i] =  "http://".$_SERVER[HTTP_HOST].$detailImg[$i];
        }
    }

    ##옵션정보확인 // option_type_chk
    unset($array_optName);
    unset($array_optPrice);
	if($row[option_type_chk]=="nooption"){
		// 옵션정보없음
		$ores = array();
	}elseif($row[option_type_chk]=="1depth"){
		$oque = " select oto_poptionname as optionname ,oto_poptionprice from odtProductOption where oto_pcode = '".$row[code]."' and oto_depth = 1 order by oto_uid asc ";
		$ores = _MQ_assoc($oque);
	
	}elseif($row[option_type_chk]=="2depth"){
		$oque = " select concat(po2.oto_poptionname,' ',po.oto_poptionname) as optionname ,po.oto_poptionprice from odtProductOption as po
						inner join odtProductOption as po2 on (po.oto_parent = po2.oto_uid)
						where po.oto_pcode = '".$row[code]."' and po.oto_depth = 2 order by po.oto_uid asc ";
		$ores = _MQ_assoc($oque);
	
	}elseif($row[option_type_chk]=="3depth"){
		$oque = " select concat(po2.oto_poptionname,' ',po3.oto_poptionname,' ',po.oto_poptionname) as optionname ,po.oto_poptionprice from odtProductOption as po
						inner join odtProductOption as po2 on ( SUBSTRING_INDEX(po.oto_parent, ',', 1) = po2.oto_uid)
						inner join odtProductOption as po3 on ( SUBSTRING_INDEX(po.oto_parent, ',', 2) = po3.oto_uid)
						where po.oto_pcode = '".$row[code]."' and po.oto_depth = 3 order by po.oto_uid asc ";
		$ores = _MQ_assoc($oque);
	}
	if($ores){
		foreach($ores as $ok=>$ov){
			$array_optName[] = $ov[optionname];
			$array_optPrice[] = $ov[oto_poptionprice];
		}
	}
//    $optionName = "$row[optionName]";
//    $array_optName = explode("|",$optionName);
//    $optionPrice = "$row[optionPrice]";
//    $array_optPrice = explode("|",$optionPrice);

?>
<deal>
<author><?=$author?></author>
<title><![CDATA[<?= $ProdName;?>]]></title>
<ptype><?= $ptype;?></ptype>
<time_start><?=$stt_datetime?></time_start>
<time_end><?=$end_datetime?></time_end>
<expire_date><?=$row[expire]?></expire_date>
<price_pub><?=$row[price_org]?></price_pub>
<price_sale><?=$row[price]?></price_sale> 
<price_rate><?=$row[price_per]?></price_rate>
<link_url><![CDATA[ <?=$link_url?> ]]></link_url>
<sale_image><![CDATA[<?=$main_img?>]]></sale_image>
<category><![CDATA[<?=$category?>]]></category>
<area><![CDATA[<?=$area?>]]></area>
<description><![CDATA[<?=stripslashes($row[comment_proinfo])?>]]></description>
<cnt_min><?=$CntMin?></cnt_min> 
<cnt_max><?=$CntMax?></cnt_max> 
<cnt_now><?=$CntSum?></cnt_now>
<shop_name><![CDATA[<?=$company_name?>]]></shop_name>
<addr><![CDATA[<?=$company_addr?>]]></addr>
<lng><![CDATA[<?=$company_addr?>]]></lng>
<lat></lat>
<review_url><![CDATA[<?=$row[blogUrl]?>]]></review_url>

<des_img1><![CDATA[<?=$detailImg[0]?>]]></des_img1>
<des_img2><![CDATA[<?=$detailImg[1]?>]]></des_img2>
<des_img3><![CDATA[<?=$detailImg[2]?>]]></des_img3>
<des_img4><![CDATA[<?=$detailImg[3]?>]]></des_img4>
<des_img5><![CDATA[<?=$detailImg[4]?>]]></des_img5>

<sub_title1><![CDATA[<?=$array_optName[0]?>]]></sub_title1>
<sub_price1><?=$array_optPrice[0]?></sub_price1>
<sub_title2><![CDATA[<?=$array_optName[1]?>]]></sub_title2>
<sub_price2><?=$array_optPrice[1]?></sub_price2>
<sub_title3><![CDATA[<?=$array_optName[2]?>]]></sub_title3>
<sub_price3><?=$array_optPrice[2]?></sub_price3>
<sub_title4><![CDATA[<?=$array_optName[3]?>]]></sub_title4>
<sub_price4><?=$array_optPrice[3]?></sub_price4>
<sub_title5><![CDATA[<?=$array_optName[4]?>]]></sub_title5>
<sub_price5><?=$array_optPrice[4]?></sub_price5>
<phone_num><![CDATA[<?=$company_tel?>]]></phone_num>
<use_info><![CDATA[<?=get_html($row[comment3])?>]]></use_info>
<edit_time><![CDATA[<?=$edit_time?>]]></edit_time>
</deal>
<?
}
?>

</deals>