<?
############################################################
## Onedaynet RSS 데이터정보 : Ver 0.1-beta-
## Create by Tindevil@nate.com
## BetaVersion
############################################################
## 자유로운 편집 및 사용이 가능합니다. 제작자주석삭제는 허락하지 않습니다.
############################################################
#                   치환자 설명 ver 0.1-beta-
############################################################
# 하단의 REPLACE 정보를 참고하세요. 
############################################################

##INCLUDE
include_once("addon.php");

##솔루션의 형태를 파악한다 odt 와 sns 두종류가있음
function get_slntype() {
    $retval = "odt";
    if ( getRowCount("show tables like 'snsProduct'")*1 > 0 ) 
    {
        $retval = "sns";
    }
    return $retval;
}
//echo "<br>slntype->".$slntype;

##판매종료패치확인
function get_datepatch($slntype) {
    return getRowCount("show columns from ".$slntype."Product where Field in ('sale_enddate')")*1;
    //echo "<br>datepatch->".$datepatch;
}

##원데이몰판단(saleCnt) 가 없음
function get_onedaytype($slntype) {
    return getRowCount("show columns from ".$slntype."Product where Field in ('saleCnt')")*1;
    //echo "<br>datepatch->".$datepatch;
}


##지역우선순위 패치확인
function get_sortpatch($slntype) {
    return getRowCount("show columns from ".$slntype."Category where Field in ('cateidx')")*1;
    //echo "<br>datepatch->".$datepatch;
}

##TEXT 형식으로 변환
function get_html($str)
{
    $str = str_replace("&gt;", ">", $str);
    $str = str_replace("&lt;", "<", $str);
    $str = str_replace("&amp;", "&", $str);
    $str = str_replace("&quot;", "\"", $str);
    return $str;
}

##현재판매되는 상품을 찾기(미래상품은 제외됨)
function CurrentItem($cateCode,$slntype,$datepatch,$changeTime) {
    $today = date('Y-m-d');

    $add_qry = " and sale_date <= '".$today."'";    //기본설정사항
    if ($datepatch) {   //종료일자 변경패치를 했을경우
        if($changeTime == "00" ) {
            $add_qry = " and sale_date <= '".$today."' and sale_enddate >='".$today."' ";
        }
        else if($changeTime <= date("H") ) {
            $add_qry = " and sale_date <= '".$today."' and sale_enddate >'".$today."' ";
        }
        else {
            $add_qry = " and sale_date < '".$today."' and sale_enddate >='".$today."' ";
        }
    }
    $Query = "select code from ".$slntype."Product where code = parent_code and cateCode = '".$cateCode."' $add_qry order by sale_date desc";


//echo "<br>test-->".$Query;exit;

    $Result = mysql_query($Query);

    $arrPro = array();
    while($rowPro = mysql_fetch_assoc($Result)){
        $arrPro[] = $rowPro[code];
    }
    //return @mysql_result($Result,0);
    return $arrPro;
}

##판매종료일을 찾기위함(변경시간과 일치하도록 : 기본값)
function getNextDate00($sale_date,$end_date,$datepatch,$changeTime) {
    if ($datepatch) {   //종료일자패치되었다면 종료일을 사용
        return $end_date;
    } else {    //다음날로지정
        $SaleTime = mktime(0,0,0,substr($sale_date,5,2),substr($sale_date,8,2),substr($sale_date,0,4));
        $NextDay = date("Y-m-d",mktime(0,0,0,date("m",$SaleTime),date("d",$SaleTime)+1,date("Y",$SaleTime)));
        return $NextDay;
    }
}
##판매종료일을 찾기위함(변경시간 -1초로 계산)
function getNextDate($sale_date,$end_date,$datepatch,$changeTime) {

    if ($datepatch) {   //종료일자패치되었다면 종료일을 사용

        if ($sale_date == $end_date) {  //같은날이면 동일날을 표시
            $NextDay = $end_date;
        } else {    //다른날이면
            $SaleTime = mktime(0,0,0,substr($end_date,5,2),substr($end_date,8,2),substr($end_date,0,4));
            if ($changeTime=="00") {
                $NextDay = date("Y-m-d",mktime(0,0,0,date("m",$SaleTime),date("d",$SaleTime)-1,date("Y",$SaleTime)));
            } else {
                $NextDay = date("Y-m-d",mktime(0,0,0,date("m",$SaleTime),date("d",$SaleTime),date("Y",$SaleTime)));
            }    
        }
        return $NextDay;

    } else {    //다음날로지정
        $SaleTime = mktime(0,0,0,substr($sale_date,5,2),substr($sale_date,8,2),substr($sale_date,0,4));
        if ($changeTime=="00") {
            $NextDay = date("Y-m-d",mktime(0,0,0,date("m",$SaleTime),date("d",$SaleTime),date("Y",$SaleTime)));
        } else {
            $NextDay = date("Y-m-d",mktime(0,0,0,date("m",$SaleTime),date("d",$SaleTime)+1,date("Y",$SaleTime)));
        }

        return $NextDay;
    }
}


$slntype = get_slntype();
$coomeCom =getRow("SELECT * FROM ".$slntype."Company");
$comName = $coomeCom[name];
$comHome = $coomeCom[homepage];
$comTel = $coomeCom[tel];

//echo"===>".$comName;

##반복구문생성
function RunLoop($content,$hourtype="00",$viewcount="200") {



    $slntype = get_slntype();       //솔루션형태 odt,sns
    $datepatch = get_datepatch($slntype);   //판매종료패치여부
    $sortpatch = get_sortpatch($slntype);   //지역우선순위패치여부

    ##상점기본정보
    $basic = getRow("SELECT * FROM ".$slntype."Setup");  //상점기본설정
    $CHG_HOUR = $basic[changeTime]; //상품변경시간
    if ($CHG_HOUR=="24") $CHG_HOUR="00";
    $CHG_HOURE = $CHG_HOUR*1-1; //상품변경종료시간
    if ($CHG_HOURE == -1) $CHG_HOURE =23;

    $company =getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
    if ("" == $CHG_HOUR || "0" == $CHG_HOUSR) $CHG_HOUR = "00";
    if ("" == $CHG_HOURE || "0" == $CHG_HOURE) $CHG_HOURE = "00";


## 다음소셜 shop(company)

    $HOMEPAGE = "http://".$company[homepage]; //홈페이지타이틀(다음소셜 업체 키값으로 사용)
    $COMPRI = $company[MranDsum]; //홈페이지타이틀(다음소셜 업체 키값으로 사용)
    $COMNAME = $company[name]; //회사명
    $COMINPUT = date ("Ymdhis", $company[inputdate]);//내용등록일
    $COMMODIFY = date ("Ymdhis", $company[modifydate]); //내용수정일


//  $COMTEL = $company[tel]; //회사 대표 전화번호
    $telchk = explode("-", $company[tel]);

    if(!$telchk[2])         {    $COMTEL = $telchk[0]."-".$telchk[1];}
    else                    {    $COMTEL = $company[tel];            }

    $COMEMAIL = $company[email]; //회사 대표 이메일
    $COMSALESNUMBER = $company[number2]; //회사 통신판매번호


    $LOGO = ""; //회사로고
    $BEG_DATE = "";                  //쿠폰시작일
    $KEYWORD = "";      //키워드(미확인)
    $DESC_URL = "";     //상세설명 주소
        
    


##    $sortKey = "catecode";  //지역정렬순위 기본값 : 지역코드순
##    if ($sortpatch) $sortKey = "cateidx";   //지역우선순위되어있을시에는 해당 정렬값사용
##    $CQuery  = "select catecode,catename from ".$slntype."Category where cHidden = 'no' order by $sortKey asc  ";


       
        ##해당지역의 현재 판매되어야할 아이템을 찾는다.

        // 상품 및 카테고리 정보 추출
        $todaydatetime = sprintf("%s %02d:%02d",date("Y-m-d"),date("H"),date("i"));
        // $sque = "
        //     SELECT p.* , c.parent_catecode , c.catename
        //     FROM odtProduct as p
        //     inner join odtCategory as c on (c.catecode=p.cateCode and c.cHidden='no')
        //     WHERE  
        //     concat(p.sale_enddate, ' ', lpad( p.sale_enddateh, 2, '0' ) , ':', lpad( p.sale_enddatem, 2, '0' ))  > '$todaydatetime' 
        //     and concat( p.sale_date, ' ', lpad( p.sale_dateh, 2, '0' ) , ':', lpad( p.sale_datem, 2, '0' )) <= '$todaydatetime'
        //     AND ifnull(p.stock,0) > 0 
        //     AND p.code = p.parent_code 
        //     ORDER BY c.cateidx asc , p.sale_date asc
        // ";
        $sque = "
            SELECT p.* 
            FROM odtProduct as p
            WHERE  
			if(sale_type = 'T', (CURDATE() BETWEEN sale_date and sale_enddate), sale_type = 'A')
            AND ifnull(p.stock,0) > 0 
            ORDER BY p.sale_date asc
        ";
		//echo $sque;
        $Pres = mysql_query($sque);

        $buffer = $content;
        $curcount = 0;  //표시가능수량

        ##총상품 갯수
        $testCnt = mysql_num_rows($Pres);

        while($Prow = mysql_fetch_assoc($Pres)){

            // - 텍스트 정보 추출 ---
            $Prow = array_merge($Prow , _text_info_extraction( "odtProduct" , $Prow[serialnum] ));

			// 카테고리 정보 추출
			$CategoryRow = _MQ("
				select
					c1.catename as c1_catename,
					c2.catename as c2_catename,
					c3.catename as c3_catename
				from odtProductCategory as pct
				left join odtCategory as c3 on (c3.catecode = pct.pct_cuid and c3.catedepth=3)
				left join odtCategory as c2 on (c2.catecode = substring_index(c3.parent_catecode , ',' ,-1) and c2.catedepth=2)
				left join odtCategory as c1 on (c1.catecode = substring_index(c3.parent_catecode , ',' ,1) and c1.catedepth=1)
				where pct_pcode='".$Prow[code]."'
				order by pct_uid asc 
				limit 1
			");
			$CATEGORY_01 = $CategoryRow[c1_catename];
			$CATEGORY_02 = $CategoryRow[c2_catename];
			$CATEGORY_03 = $CategoryRow[c3_catename];


            $content = $buffer;
            $AID = $Prow[cateCode];      //지역코드	
            $PID = $Prow[code];          //상품코드	
            $ANAME = $Prow[catename];    //지역명


            $PNAME = $Prow[mainName] ? $Prow[mainName] : $Prow[name];    //메인상품명이없을경우 해당상품명을 사용

            $COU_ENDDATE = date('Ymdhis',strtotime(($Prow[expire])));                  //쿠폰만료일


            if($Prow[setup_delivery] == "Y") {
                $PTYPE = "상품";    //상품타입(쿠폰,상품)
                $PTYPE_M = "g";     //상품타입(쿠폰 : c,상품 : g)

            }else{
                $PTYPE = "쿠폰";    //상품타입(쿠폰,상품)
                $PTYPE_M = "c";     //상품타입(쿠폰 : c,상품 : g)
            }

            $SUP_IMAGE = $Prow[com_juso]; //공급업체매장위치
            $DETAIL = $Prow[comment2]; //상품상세설명
			
			# -- 업로드 파일 경로 패치
			$DETAIL = str_replace('"upfiles/', '"http://'.$_SERVER['HTTP_HOST'].'/upfiles/', $DETAIL);
			$DETAIL = str_replace('"/upfiles/', '"http://'.$_SERVER['HTTP_HOST'].'/upfiles/', $DETAIL);

            $CAUTION = $Prow[comment3]; //상품주의사항

            $MAINNAME = $Prow[mainName];    //메인상품명
            $SUBNAME = $Prow[name]; //서브상품명
            $STOCK = $Prow[stock];  //상품재고량

            //$STOCK = getScalar("select sum(stock) from ".$slntype."Product where parent_code ='".$PID."'");

            //if( $STOCK < 1 || $PNAME == "") continue; //재고가없거나 상품명이 없을경우 PASS

            $BUYLIMIT = $Prow[buy_limit];   //구매제한수
            $CATEGORY = $Prow[rsscate];     //카테고리명(미확인)

            $PPID = $Prow[parent_code];     //쿠폰 상위 상품코드
            $PIMG = $Prow[main_img];        //쿠폰 메인 이미지
            if ( !strpos($PIMG,"://") && $PIMG ) $PIMG ="http://$_SERVER[HTTP_HOST]/upfiles/product/".$PIMG; //주소값붙인다.

            $PMSG = str_replace("&nbsp;" , "" , strip_tags($Prow[comment_proinfo]));         //쿠폰 설명


            //티켓몰용 LINK값
            $LINK = "http://".$_SERVER[HTTP_HOST]."/?pn=product.view&pcode=$PID";

            //모바일용 LINK값
            $MLINK = "http://".$_SERVER[HTTP_HOST]."/m/?pn=product.view&pcode=$PID";

            //원데이몰용 LINK(원데이몰 사용자는 아래 두줄의 주석을 해제하시기 바랍니다)
            //$cateName = array("01"=>"today","02"=>"week","03"=>"live","04"=>"three","05"=>"five");
            //$LINK = "http://".$_SERVER[HTTP_HOST]."/?Pid=u05b01m01&code=".$PID."&ctcode=".$AID;   //원데이몰용2
            /* index.html 에 적용해야하는 코드
            if($Pid=="u05b01m01") { //미리보기였을때 판매중인상품이라면 해당 today 로 넘긴다.
                $ctcode = $_REQUEST['ctcode'];
                $icode = $_REQUEST['code'];
                $nowcode = nowSaleItem($ctcode);
                if($icode = $nowcode) {
                    $cateName = array("01"=>"today","02"=>"week","03"=>"live","04"=>"three","05"=>"five");
                    echo "<meta http-equiv='Refresh' content='0; URL=http://$_SERVER[HTTP_HOST]/?main=$cateName[$ctcode]'>";
                    exit;
                }
            }
            */

            $PRICEO = $Prow[price_org];     //판매가(원가)
            $PRICES = $Prow[price];         //판매가(할인가)
            $PRICER = $Prow[price_per];     //할인율

//            $CNTMIN = $Prow[saleCntMax];    //목표도달인원
			$CNTMIN = 1; // 1로 고정
            $CNTSALE = $Prow[saleCnt];    //판매수량
    //        echo"<br>BeCnt==>".$CNTSALE;
            // $cntQue = "SELECT * FROM odtProduct where sale_date = '".$Prow[sale_date]."' and sale_enddate = '".$Prow[sale_enddate]."' and code = '".$Prow[code]."' ";
            // $cntRes = mysql_query($cntQue);

            // if($cntRes){
            //     while($cntRow = mysql_fetch_array($cntRes)){
            //         $CNTSALE =  $CNTSALE + $cntRow[saleCnt];
            //     }
            // }
    //        echo"<br>AftCnt==>".$CNTSALE;
    //        exit;
            
            $setup_query ="select licenseNumber from ".$slntype."Setup";
            $setup_result=mysql_query($setup_query);
            while ($Record = mysql_fetch_array($setup_result)){
                
                $b = "/ONEDAYNET/";
                $c = $Record[licenseNumber];            
            }
                $separation = preg_match($b,$c);

          if($separation == 1){
                /*
             $rlt_sale = mysql_query("select sum(op_cnt) from view_order where op_pcode like '%".$PID."%' AND orderdate like '".date("Y-m-d")."%' and paystatus ='Y' and canceled = 'N' ");
                $CNTSALE_TODAY = 0;
                while($saler = mysql_fetch_array($rlt_sale)) {
                     $CNTSALE_TODAY = $saler[0];
                    
                } 
                */
          }else{
                $rlt_sale = getRows("select pLog from ".$slntype."Order where pLog like '%".$PID."%' AND orderdate like '".date("Y-m-d")."%' and paystatus ='Y' and canceled = 'N' ");
                $CNTSALE_TODAY = 0;
                while($saler = @mysql_fetch_array($rlt_sale)) {
                    $plogarray = explode("^",$saler[pLog]);
                    foreach($plogarray as $plogdata) {
                        list($pcode,$pqty,$pamt) = explode("|",$plogdata);
                        if($pcode == $PID) $CNTSALE_TODAY = $CNTSALE_TODAY*1 + $pqty*1;
                    }
                }           
/*
                if (!get_onedaytype($slntype)) { //원데이몰이경우

                    //판매량 데이터베이스에서 추출
                    $rlt_sale = getRows("select pLog from ".$slntype."Order where pLog like '%".$PID."%' and paystatus ='Y' and canceled = 'N' ");
                    $CNTSALE = 0;
                    while($saler = mysql_fetch_array($rlt_sale)) {
                        $plogarray = explode("^",$saler[pLog]);
                        foreach($plogarray as $plogdata) {
                            list($pcode,$pqty,$pamt) = explode("|",$plogdata);
                            if($pcode == $PID) $CNTSALE = $CNTSALE*1 + $pqty*1;
                        }
                    }
                } 
*/
          }
     

            $CNTMAX = $CNTSALE*1+$STOCK*1;    //재고량+판매량
            $COMMENT2 = $Prow[comment2];    //상품상세설명

            $STT_DATE = $Prow[sale_date];    //판매시작일
            $END_DATE = $Prow[sale_enddate];    //판매종료일
            if($Prow[sale_date]=="0") $Prow[sale_date]="00";
            if($Prow[sale_enddateh]=="0") $Prow[sale_enddateh]="00";


            //상품별 판매마감시간 가져오기
            $CHG_HOURPS=$Prow[sale_dateh]? $Prow[sale_dateh]:$CHG_HOUR;
            $CHG_HOURPE=$Prow[sale_enddateh]? $Prow[sale_enddateh]:$CHG_HOUR;
            $CHG_MPS=$Prow[sale_datem]? $Prow[sale_datem]:"00";
            $CHG_MPE=$Prow[sale_enddatem]? $Prow[sale_enddatem]:"00";


    if ($hourtype == "00") {    //기본값 종료시간을 지정된 시간으로 표시
            $END_DATE = getNextDate00($STT_DATE,$END_DATE,$datepatch,$basic[changeTime]);       //판매종료일
    } else {
            $END_DATE = getNextDate($STT_DATE,$END_DATE,$datepatch,$basic[changeTime]);       //판매종료일
    }

            $EXP_DATE = "";//$Prow[expire];                  //쿠폰만료일
            if ($EXP_DATE == "0000-00-00") $EXP_DATE = "";

            $NOW_DATETIME = date("Y-m-d H:i:s");                  //쿠폰만료일
            $NOW_DATETIMEH = date("Y-m-d H");                  //쿠폰만료일
            $NOW_DATETIMEHM = date("Y-m-d H:i");                  //쿠폰만료일

            $STT_DATETIME = $STT_DATE ? "$STT_DATE ".sprintf("%02d",$CHG_HOURPS).":".sprintf("%02d",$CHG_MPS).":00" : "";    //판매시작일(시분초)
            $STT_DATETIMEH = $STT_DATE ? "$STT_DATE ".sprintf("%02d",$CHG_HOUR)."" : "";    //판매시작일(시)
            $STT_DATETIMEHM = $STT_DATE ? "$STT_DATE ".sprintf("%02d",$CHG_HOUR).":00" : "";    //판매시작일(시분)

            $BEG_DATETIME = $BEG_DATE ? "$BEG_DATE ".sprintf("%02d",$CHG_HOUR).":00:00" : "";    //쿠폰시작
            $BEG_DATETIMEH = $BEG_DATE ? "$BEG_DATE ".sprintf("%02d",$CHG_HOUR)."" : "";    //
            $BEG_DATETIMEHM = $BEG_DATE ? "$BEG_DATE ".sprintf("%02d",$CHG_HOUR).":00" : "";    //


//sprintf("%02d",$Prow[sale_enddateh])

    if($separation == 1){
            $END_DATETIME = $END_DATE ? "$END_DATE ".sprintf("%02d",$Prow[sale_enddateh]).":".sprintf("%02d",$Prow[sale_enddatem]).":59" : "";    //판매종료일
            $END_DATETIMEH = $END_DATE ? "$END_DATE ".sprintf("%02d",$Prow[sale_enddateh])."" : "";    //판매종료일
            $END_DATETIMEHM = $END_DATE ? "$END_DATE ".sprintf("%02d",$Prow[sale_enddateh]).":" . sprintf("%02d",$Prow[sale_enddatem]) : "";    //판매종료일

            $EXP_DATETIME = $EXP_DATE ? "$EXP_DATE ".$CHG_HOURE.":59:59" : "";    //쿠폰만료
            $EXP_DATETIMEH = $EXP_DATE ? "$EXP_DATE ".$CHG_HOURE."" : "";    //
            $EXP_DATETIMEHM = $EXP_DATE ? "$EXP_DATE ".$CHG_HOURE.":59" : "";    //
    }
    else if ($hourtype == "00") {    //기본값 종료시간을 지정된 시간으로 표시
            $END_DATETIME = $END_DATE ? "$END_DATE ".$CHG_HOURPE.":".$CHG_MPE.":00" : "";    //판매종료일
            $END_DATETIMEH = $END_DATE ? "$END_DATE ".$CHG_HOUR."" : "";    //판매종료일
            $END_DATETIMEHM = $END_DATE ? "$END_DATE ".$CHG_HOUR.":00" : "";    //판매종료일

            $EXP_DATETIME = $EXP_DATE ? "$EXP_DATE ".$CHG_HOUR.":00:00" : "";    //쿠폰만료
            $EXP_DATETIMEH = $EXP_DATE ? "$EXP_DATE ".$CHG_HOUR."" : "";    //
            $EXP_DATETIMEHM = $EXP_DATE ? "$EXP_DATE ".$CHG_HOUR.":00" : "";    //
    } else {    //종료시간형태를 지정된시간에 -1초 한값으로 표시
            $END_DATETIME = $END_DATE ? "$END_DATE ".$CHG_HOURE.":59:59" : "";    //판매종료일
            $END_DATETIMEH = $END_DATE ? "$END_DATE ".$CHG_HOURE."" : "";    //판매종료일
            $END_DATETIMEHM = $END_DATE ? "$END_DATE ".$CHG_HOURE.":59" : "";    //판매종료일

            $EXP_DATETIME = $EXP_DATE ? "$EXP_DATE ".$CHG_HOURE.":59:59" : "";    //쿠폰만료
            $EXP_DATETIMEH = $EXP_DATE ? "$EXP_DATE ".$CHG_HOURE."" : "";    //
            $EXP_DATETIMEHM = $EXP_DATE ? "$EXP_DATE ".$CHG_HOURE.":59" : "";    //
    }

            ##시간정보에 숫자부분만 추출
            $STT_DATETIME2 = ereg_replace("[^0-9]", "", $STT_DATETIME); //판매시작일(시분초)

            $STT_DATETIMEH2 = ereg_replace("[^0-9]", "", $STT_DATETIMEH);    //판매시작일(시)
            $STT_DATETIMEHM2 = ereg_replace("[^0-9]", "", $STT_DATETIMEHM);    //판매시작일(시분)

            $END_DATETIME2 = ereg_replace("[^0-9]", "", $END_DATETIME);   //판매종료일
            $END_DATETIMEH2 = ereg_replace("[^0-9]", "", $END_DATETIMEH);    //판매종료일
            $END_DATETIMEHM2 = ereg_replace("[^0-9]", "", $END_DATETIMEHM);   //판매종료일

            $BEG_DATETIME2 = ereg_replace("[^0-9]", "", $BEG_DATETIME);    //쿠폰시작
            $BEG_DATETIMEH2 = ereg_replace("[^0-9]", "", $BEG_DATETIMEH);    //
            $BEG_DATETIMEHM2 = ereg_replace("[^0-9]", "", $BEG_DATETIMEHM);    //

            $EXP_DATETIME2 = ereg_replace("[^0-9]", "", $EXP_DATETIME);    //쿠폰만료
            $EXP_DATETIMEH2 = ereg_replace("[^0-9]", "", $EXP_DATETIMEH);    //
            $EXP_DATETIMEHM2 = ereg_replace("[^0-9]", "", $EXP_DATETIMEHM);     //

            $EXP_DATE2 = ereg_replace("[^0-9]", "", $EXP_DATE);    //쿠폰만료
            $BEG_DATE2 = ereg_replace("[^0-9]", "", $BEG_DATE);    //쿠폰시작


			// 상시판매일 경우 제외
            if($Prow['sale_type'] == 'T') {
				//시작일자가 현재보다 미래일경우에는 나오지않도록한다.
				$stt_time_data = mktime(substr($STT_DATETIME,11,2),substr($STT_DATETIME,14,2),substr($STT_DATETIME,17,2), substr($STT_DATETIME,5,2),substr($STT_DATETIME,8,2),substr($STT_DATETIME,0,4));
				if ($stt_time_data > mktime() ) continue;

				//종료일자가 현재보다 과거일경우에는 나오지않도록한다.
				$end_time_data = mktime(substr($END_DATETIME,11,2),substr($END_DATETIME,14,2),substr($END_DATETIME,17,2), substr($END_DATETIME,5,2),substr($END_DATETIME,8,2),substr($END_DATETIME,0,4));
				if ($end_time_data < mktime() ) continue;
			}

            //공급업체정보
            $SupplyInfo = getRow("select address,address1,cName,tel1,tel2,tel3,zip1,zip2, signdate, modifydate from ".$slntype."Member where userType = 'C' and id='".$Prow[customerCode]."'");
            $SUP_ADDRESS = $SupplyInfo[address];    //업체주소
            $SUP_ADDRESS1 = $SupplyInfo[address1];    //업체주소(뒷부분)
            $SUP_NAME = $SupplyInfo[cName];          //업체명
            $SUP_TEL1 = $SupplyInfo[tel1];          //업체전화번호1
            $SUP_TEL2 = $SupplyInfo[tel2];          //업체전화번호2
            $SUP_TEL3 = $SupplyInfo[tel3];          //업체전화번호3
            $SUP_TEL =  $SUP_TEL1."-".$SUP_TEL2."-".$SUP_TEL3;  //업체전화번호가 없다면 공란으로
            if ($SUP_TEL == "--") $SUP_TEL = "";
            $SUP_ZIP1 = $SupplyInfo[zip1];    //업체주소
            $SUP_ZIP2 = $SupplyInfo[zip2];    //업체주소(뒷부분)
            $SUP_ZIP = $SUP_ZIP1."-".$SUP_ZIP2;

            if ($SUP_ZIP == "-") $SUP_ZIP = "";

    ## 다음소셜 stores
            $SUP_SIGNDATE =  date ("Ymdhis", $SupplyInfo[signdate]);        //내용 입력일
            $SUP_MODIFYDATE =  date ("Ymdhis", $SupplyInfo[modifydate]);    //내용 수정일
    //      $CATEID = "";       //카테고리ID(미확인)

            $Prow[setup_delivery]=="Y" ? $DELCHK = "D" : $DELCHK = "T";   // 티켓or배송 구분
            $Prow[isNOW]=="N" ? $NOWCHK = "D" : $NOWCHK = "I";   // 실시간상품 확인 (공동구매(기본) : D ,  실시간 : I)
            $REGDTTM = date('Ymdhis',strtotime(($Prow[inputDate])));              //상품등록일
            

            $Prow[p_viewAge]? $LIMITADULT = $Prow[p_viewAge] : $LIMITADULT = "N";             // 성인상품여부 (Y : 성인상품, N : 일반)

            if($Prow[rssarea1]){
                $areachk = substr_count("전국", $Prow[rssarea1]);
            }
            $areachk == 1 ? $AREA_CHK = "Y" : $AREA_CHK = "N";


            //링크주소에서 www. 를제거
            $LINK = str_replace("www.","",$LINK);  
            $MLINK = str_replace("www.","",$MLINK);

            //
            if ($curcount > $viewcount) continue;

            $curcount = $curcount*1 +1;

            ##데이터치환
            $content = str_replace("{##AID##}",$AID,$content);              //지역코드
            $content = str_replace("{##ANAME##}",$ANAME,$content);          //지역코드명
            $content = str_replace("{##PID##}",$PID,$content);              //상품코드
            $content = str_replace("{##PIDN##}",$PIDN,$content);              //상품코드(숫자)
            $content = str_replace("{##PNAME##}",$PNAME,$content);          //상품명
            $content = str_replace("{##PMSG##}",$PMSG,$content);            //상품정보
            $content = str_replace("{##PIMG##}",$PIMG,$content);            //상품이미지
            $content = str_replace("{##PPID##}",$PPID,$content);            //상품코드(부모코드)
            $content = str_replace("{##BUYLIMIT##}",$BUYLIMIT,$content);    //구매제한
            $content = str_replace("{##LINK##}",$LINK,$content);            //링크URL

            $content = str_replace("{##MLINK##}",$MLINK,$content);            //모바일용 링크URL

            $content = str_replace("{##DETAIL##}",$DETAIL,$content);            //상품상세설명
            $content = str_replace("{##MAINNAME##}",$MAINNAME,$content);    //메인이름
            $content = str_replace("{##SUBNAME##}",$SUBNAME,$content);      //서브이름
            $content = str_replace("{##CAUTION##}",$CAUTION,$content);      //상품주의사항

            $content = str_replace("{##PRICEO##}",$PRICEO,$content);        //상품금액(원가)
            $content = str_replace("{##PRICES##}",$PRICES,$content);        //판매금액(할인가)
            $content = str_replace("{##PRICER##}",$PRICER,$content);        //할인율

            $content = str_replace("{##SUP_ADDRESS##}",$SUP_ADDRESS,$content);      //입점업체주소
            $content = str_replace("{##SUP_ADDRESS1##}",$SUP_ADDRESS1,$content);      //입점업체주소
            $content = str_replace("{##SUP_IMAGE##}",$SUP_IMAGE,$content);      //입점업체주소
            $content = str_replace("{##SUP_NAME##}",$SUP_NAME,$content);      //입점업체명
            $content = str_replace("{##SUP_TEL##}",$SUP_TEL,$content);        //공급업체전화
            $content = str_replace("{##SUP_TEL1##}",$SUP_TEL1,$content);        //공급업체전화1
            $content = str_replace("{##SUP_TEL2##}",$SUP_TEL2,$content);        //공급업체전화2
            $content = str_replace("{##SUP_TEL3##}",$SUP_TEL3,$content);        //공급업체전화3
            $content = str_replace("{##SUP_ZIP##}",$SUP_ZIP,$content);        //공급업체우편번호1
            $content = str_replace("{##SUP_ZIP1##}",$SUP_ZIP1,$content);        //공급업체우편번호2
            $content = str_replace("{##SUP_ZIP2##}",$SUP_ZIP2,$content);        //공급업체우편번호3

            $content = str_replace("{##CNTMIN##}",$CNTMIN,$content);        //최소판매량(목표도달인원)
            $content = str_replace("{##CNTMAX##}",$CNTMAX,$content);        //최대판매량
            $content = str_replace("{##CNTSALE##}",$CNTSALE,$content);      //판매량
            $content = str_replace("{##CNTSALETODAY##}",$CNTSALE_TODAY,$content);      //판매량(금일)
            $content = str_replace("{##STOCK##}",$STOCK,$content);          //상품재고

            $content = str_replace("{##STT_DATE##}",$STT_DATE,$content);    //판매시작일
            $content = str_replace("{##END_DATE##}",$END_DATE,$content);    //판매종료일
            $content = str_replace("{##EXP_DATE##}",$EXP_DATE,$content);    //쿠폰만료일
            $content = str_replace("{##BEG_DATE##}",$BEG_DATE,$content);    //쿠폰시작일

            $content = str_replace("{##CHG_HOUR##}",$CHG_HOUR,$content);    //상품변경시간
            $content = str_replace("{##CATEGORY##}",$CATEGORY,$content);    //카테고리명
            $content = str_replace("{##CATEGORY_01##}",$CATEGORY_01,$content);    //카테고리명 - 1차 카테고리
            $content = str_replace("{##CATEGORY_02##}",$CATEGORY_02,$content);    //카테고리명 - 2차 카테고리
            $content = str_replace("{##CATEGORY_03##}",$CATEGORY_03,$content);    //카테고리명 - 3차 카테고리
            $content = str_replace("{##CATEID##}",$CATEID,$content);        //카테고리ID

            $content = str_replace("{##STT_DATETIME##}",$STT_DATETIME,$content);    //판매시작일
            $content = str_replace("{##STT_DATETIMEH##}",$STT_DATETIMEH,$content);    //판매시작일
            $content = str_replace("{##STT_DATETIMEHM##}",$STT_DATETIMEHM,$content);    //판매시작일

            $content = str_replace("{##END_DATETIME##}",$END_DATETIME,$content);    //판매종료일
            $content = str_replace("{##END_DATETIMEH##}",$END_DATETIMEH,$content);    //판매종료일
            $content = str_replace("{##END_DATETIMEHM##}",$END_DATETIMEHM,$content);    //판매종료일

            $content = str_replace("{##EXP_DATETIME##}",$EXP_DATETIME,$content);    //쿠폰만료일
            $content = str_replace("{##EXP_DATETIMEH##}",$EXP_DATETIMEH,$content);    //쿠폰만료일
            $content = str_replace("{##EXP_DATETIMEHM##}",$EXP_DATETIMEHM,$content);    //쿠폰만료일

            $content = str_replace("{##BEG_DATETIME##}",$BEG_DATETIME,$content);    //쿠폰시작일
            $content = str_replace("{##BEG_DATETIMEH##}",$BEG_DATETIMEH,$content);    //쿠폰시작일
            $content = str_replace("{##BEG_DATETIMEHM##}",$BEG_DATETIMEHM,$content);    //쿠폰시작일


            $content = str_replace("{##STT_DATETIME2##}",$STT_DATETIME2,$content);    //판매시작일
            $content = str_replace("{##STT_DATETIMEH2##}",$STT_DATETIMEH2,$content);    //판매시작일
            $content = str_replace("{##STT_DATETIMEHM2##}",$STT_DATETIMEHM2,$content);    //판매시작일

            $content = str_replace("{##END_DATETIME2##}",$END_DATETIME2,$content);    //판매종료일
            $content = str_replace("{##END_DATETIMEH2##}",$END_DATETIMEH2,$content);    //판매종료일
            $content = str_replace("{##END_DATETIMEHM2##}",$END_DATETIMEHM2,$content);    //판매종료일

            $content = str_replace("{##EXP_DATETIME2##}",$EXP_DATETIME2,$content);    //쿠폰만료일
            $content = str_replace("{##EXP_DATETIMEH2##}",$EXP_DATETIMEH2,$content);    //쿠폰만료일
            $content = str_replace("{##EXP_DATETIMEHM2##}",$EXP_DATETIMEHM2,$content);    //쿠폰만료일

            $content = str_replace("{##BEG_DATETIME2##}",$BEG_DATETIME2,$content);    //쿠폰시작일
            $content = str_replace("{##BEG_DATETIMEH2##}",$BEG_DATETIMEH2,$content);    //쿠폰시작일
            $content = str_replace("{##BEG_DATETIMEHM2##}",$BEG_DATETIMEHM2,$content);    //쿠폰시작일

            $content = str_replace("{##EXP_DATE2##}",$EXP_DATE2,$content);    //쿠폰만료일 날짜(숫자만취함)
            $content = str_replace("{##BEG_DATE2##}",$BEG_DATE2,$content);    //쿠폰시작일 날짜(숫자만취함)

            $content = str_replace("{##NOW_DATETIME##}",$NOW_DATETIME,$content);    //현재시간
            $content = str_replace("{##NOW_DATETIMEH##}",$NOW_DATETIMEH,$content);    //현재시간
            $content = str_replace("{##NOW_DATETIMEHM##}",$NOW_DATETIMEHM,$content);    //현재시간

            $content = str_replace("{##KEYWORD##}",$KEYWORD,$content);      //키워드

            $content = str_replace("{##LOGO##}",$LOGO,$content);            //회사로고
            $content = str_replace("{##DESC_ADDR##}",$DESC_URL,$content);   //상세설명주소

            $content = str_replace("{##PTYPE##}",$PTYPE,$content);          //쿠폰,배송 구분
            $content = str_replace("{##PTYPE_M##}",$PTYPE_M,$content);          //쿠폰,배송 구분 (쿠폰 : c  상품 : g)
            
            $content = str_replace("{##RSSAREA1##}",$Prow[rssarea1],$content);          //지역정보
            $content = str_replace("{##RSSAREA2##}",$Prow[rssarea2],$content);          //지역정보-위치

            $content = str_replace("{##HOMEPAGE##}",$HOMEPAGE,$content);          //홈페이지타이틀(키값)
            $content = str_replace("{##COMPRI##}",$COMPRI,$content);          //홈페이지타이틀(키값)

            $content = str_replace("{##COMNAME##}",$COMNAME,$content);          //회사명
            $content = str_replace("{##COMINPUT##}",$COMINPUT,$content);          //입력일
            $content = str_replace("{##COMMODIFY##}",$COMMODIFY,$content);          //수정일
            $content = str_replace("{##COMTEL##}",$COMTEL,$content);          //회사 대표 전화번호
            $content = str_replace("{##COMEMAIL##}",$COMEMAIL,$content);          //회사 대표 EMAIL
            $content = str_replace("{##COMSALESNUMBER##}",$COMSALESNUMBER,$content);          //회사 통신판매번호

            $content = str_replace("{##SUP_SIGNDATE##}",$SUP_SIGNDATE,$content);          //입점업체 등록일
            $content = str_replace("{##SUP_MODIFYDATE##}",$SUP_MODIFYDATE,$content);          //입점업체 수정일
            $content = str_replace("{##DELCHK##}",$DELCHK,$content);          //판매상품속성 (티켓or배송)
            $content = str_replace("{##NOWCHK##}",$NOWCHK,$content);          //실시간 상품 구분
            $content = str_replace("{##REGDTTM##}",$REGDTTM,$content);          //상품등록일
            $content = str_replace("{##LIMITADULT##}",$LIMITADULT,$content);          //성인상품구분
            $content = str_replace("{##AREA_CHK##}",$AREA_CHK,$content);          //전국상품 구분
            $content = str_replace("{##daumSTT_DATE##}",$daumSTT_DATE,$content);          //daum_시작시간
            $content = str_replace("{##daumEND_DATE##}",$daumEND_DATE,$content);          //daum_종료시간
            $content = str_replace("{##COU_ENDDATE##}",$COU_ENDDATE,$content);          //daum_쿠폰_종료시간


            echo $content;
        }
//echo "view_Cnt-->".$curcount;
//echo "tot_Cnt-->".$testCnt;

    }   //end wile



#}
?>