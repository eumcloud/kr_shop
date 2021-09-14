<?php

    $ool_bank_name_array = array(
            '39'=>'경남',
            '34'=>'광주',
            '04'=>'국민',
            '03'=>'기업',
            '11'=>'농협',
            '31'=>'대구',
            '32'=>'부산',
            '02'=>'산업',
            '45'=>'새마을금고',
            '07'=>'수협',
            '88'=>'신한',
            '26'=>'신한',
            '48'=>'신협',
            '05'=>'외환',
            '20'=>'우리',
            '71'=>'우체국',
            '37'=>'전북',
            '35'=>'제주',
            '81'=>'하나',
            '27'=>'한국씨티',
            '53'=>'씨티',
            '23'=>'SC은행',
            '09'=>'동양증권',
            '78'=>'신한금융투자증권',
            '40'=>'삼성증권',
            '30'=>'미래에셋증권',
            '43'=>'한국투자증권',
            '69'=>'한화증권'
        );


	include_once(dirname(__FILE__)."/../../include/inc.php");
	$ordernum = $_SESSION["session_ordernum"];//주문번호
	// 결제기록 키값 배열 정의
	$keys = array( 'm_resultCode',  'm_resultMsg',  'm_payMethod',  'm_moid',  'm_tid',  'm_resultprice',  'm_pgAuthDate',  'm_pgAuthTime',  'm_mid',  'm_buyerName',  'm_noti',  'm_nextUrl',  'm_notiUrl',  'm_codegw');

	// --> 비회원 구매를 위한 쿠키 적용여부 파악
	//cookie_chk();

	// 회원정보 추출
	if(is_login()) $indr = $row_member;

  require_once(PG_M_DIR."/inicis/libs/INImx.php");
	
	$inimx = new INImx;

	$inimx->reqtype 	= "PAY";  //결제요청방식
	$inimx->inipayhome 	= PG_M_DIR."/inicis"; //로그기록 경로 (이 위치의 하위폴더에 log폴더 생성 후 log폴더에 대해 777 권한 설정)
	$inimx->id_merchant = substr($P_TID,'10','10');  //
	$inimx->status			= $P_STATUS;
	$inimx->rmesg1			= $P_RMESG1;
	$inimx->tid		= $P_TID;
	$inimx->req_url		= $P_REQ_URL;
	$inimx->noti		= $P_NOTI;


//echo "<script>alert('주문이 실패하였습니다.'); location.href='/';</script>";


  if($inimx->status =="00")   // 모바일 인증이 성공시
  {

	  $inimx->startAction();  // 승인요청
	  
	  
	  
	  $inimx->getResult();  //승인결과 파싱




	// 2017-01-05 ::: 세션이 없어질 경우 - 주문변수 처리 ::: JJC
	$ordernum = $inimx->m_moid ? $inimx->m_moid : $ordernum ;

	// 주문정보 추출
	$r = $inicis_or = _MQ("select * from odtOrder where ordernum='". $ordernum ."' ");

	// 결제금액이 정상인지 체크
	if($inimx->m_resultprice != $r['tPrice']) {
		error_loc_msg("/?pn=shop.order.result" , "결제금액이 다릅니다. 정상결제금액 : ".$r['tPrice'].", 요청된결제금액 : ".$inimx->m_resultprice,"top");
	}


	if($inimx->m_resultCode != '00') {
		error_loc_msg("/?pn=shop.order.result" , iconv('euckr','utf8',$inimx->m_resultMsg)." - 결제에 실패하였습니다. 다시한번 확인 바랍니다.");  
	}

	 switch($inimx->m_payMethod)
	 {   
    
        case(CARD):  //신용카드 안심클릭
        
					echo"처리 중입니다.";

					if(!$inimx->m_authCode || $inimx->m_authCode=="")
					{
							//해당 정보가 없으면 주문 취소
							$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
							foreach($keys as $name) {
								$app_oc_content .= $name . "||" .iconv("euc-kr","utf-8",$inimx->$name) . "§§" ; // 데이터 저장
							}
							$que = "
								insert odtOrderCardlog set
									 oc_oordernum = '". $ordernum ."'
									,oc_tid = '".$inimx->m_tid."'
									,oc_content = '{$app_oc_content}'
									,oc_rdate = now();
							";
							_MQ_noreturn($que);
			
							_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");
							error_loc_msg("/?pn=shop.order.result" , iconv('euckr','utf8',$inimx->m_resultMsg)." - 결제에 실패하였습니다. 다시한번 확인 바랍니다.");
					}


					/*
            echo("승인결과코드:".$inimx->m_resultCode."<br>");
            echo("결과메시지:".$inimx->m_resultMsg."<br>");
            echo("지불수단:".$inimx->m_payMethod."<br>");
            echo("주문번호:".$inimx->m_moid."<br>");
            echo("TID:".$inimx->m_tid."<br>");
            echo("승인금액:".$inimx->m_resultprice."<br>");
            echo("승인일:".$inimx->m_pgAuthDate."<br>");
            echo("승인시각:".$inimx->m_pgAuthTime."<br>");
            echo("상점ID:".$inimx->m_mid."<br>");
            echo("구매자명:".$inimx->m_buyerName."<br>");
            echo("P_NOTI:".$inimx->m_noti."<br>");
            echo("NEXT_URL:".$inimx->m_nextUrl."<br>");
            echo("NOTI_URL:".$inimx->m_notiUrl."<br>");
            echo("승인번호:".$inimx->m_authCode."<br>");
            echo("할부개월:".$inimx->m_cardQuota."<br>");
            echo("카드코드:".$inimx->m_cardCode."<br>");
            echo("발급사코드:".$inimx->m_cardIssuerCode."<br>");
            echo("카드번호:".$inimx->m_cardNumber."<br>");
            echo("가맹점번호:".$inimx->m_cardMember."<br>");
            echo("매입사코드:".$inimx->m_cardpurchase."<br>");
            echo("부분취소가능여부(0:불가, 1:가능):".$inimx->m_prtc."<br>");
					*/

            # 결제완료 (최종데이터의 업데이트는 pro.php가수행함)

						// 회원정보 추출
						if(is_login()) $indr = $row_member;

						// 주문정보 추출
						$r = _MQ("select * from odtOrder where ordernum='". $ordernum ."' ");

						$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
						foreach($keys as $name) {
							$app_oc_content .= $name . "||" .iconv("euc-kr","utf-8",$inimx->$name) . "§§" ; // 데이터 저장
						}
						$que = "
							insert odtOrderCardlog set
								 oc_oordernum = '". $ordernum ."'
								,oc_tid = '".$inimx->m_tid."'
								,oc_content = '{$app_oc_content}§§subTy||".$subTy."'
								,oc_rdate = now();
						";
						_MQ_noreturn($que);
						
						// -- 최종결제요청 결과 성공 DB처리 ---

						// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
						include dirname(__FILE__)."/shop.order.result.pro.php";

						$tno = $inimx->m_tid;
						_MQ_noreturn("update odtOrder set authum = '".$tno."' where ordernum = '$ordernum'");

						// 결제완료페이지 이동
						error_loc("/?pn=shop.order.complete");
					
						exit;

            break;
            
          case(MOBILE):  //휴대폰결제
        /*
             echo("승인결과코드:".$inimx->m_resultCode."<br>");
             echo("결과메시지:".$inimx->m_resultMsg."<br>");
             echo("지불수단:".$inimx->m_payMethod."<br>");
             echo("주문번호:".$inimx->m_moid."<br>");
             echo("TID:".$inimx->m_tid."<br>");
             echo("승인금액:".$inimx->m_resultprice."<br>");
             echo("승인일:".$inimx->m_pgAuthDate."<br>");
             echo("승인시각:".$inimx->m_pgAuthTime."<br>");
             echo("상점ID:".$inimx->m_mid."<br>");
             echo("구매자명:".$inimx->m_buyerName."<br>");
             echo("P_NOTI:".$inimx->m_noti."<br>");
             echo("NEXT_URL:".$inimx->m_nextUrl."<br>");
             echo("NOTI_URL:".$inimx->m_notiUrl."<br>");
             echo("통신사:".$inimx->m_codegw."<br>");
          */  
            break;
            
            case(VBANK):  //가상계좌
        /*
           echo("승인결과코드:".$inimx->m_resultCode."<br>");
             echo("결과메시지:".$inimx->m_resultMsg."<br>");
             echo("지불수단:".$inimx->m_payMethod."<br>");
             echo("주문번호:".$inimx->m_moid."<br>");
             echo("TID:".$inimx->m_tid."<br>");
             echo("승인금액:".$inimx->m_resultprice."<br>");
             echo("요청일:".$inimx->m_pgAuthDate."<br>");
             echo("요청시각:".$inimx->m_pgAuthTime."<br>");
             echo("상점ID:".$inimx->m_mid."<br>");
             echo("구매자명:".$inimx->m_buyerName."<br>");
             echo("P_NOTI:".$inimx->m_noti."<br>");
             echo("NEXT_URL:".$inimx->m_nextUrl."<br>");
             echo("NOTI_URL:".$inimx->m_notiUrl."<br>");
             echo("가상계좌번호:".$inimx->m_vacct."<br>");
             echo("입금예정일:".$inimx->m_dtinput."<br>");
             echo("입금예정시각:".$inimx->m_tminput."<br>");
             echo("예금주:".$inimx->m_nmvacct."<br>");
             echo("은행코드:".$inimx->m_vcdbank."<br>");
					*/

                $ool_type = 'R';
                $tno = $inimx->m_tid;
                $app_time = $inimx->m_pgAuthDate;
                $amount = $inimx->m_resultprice;
                $account = $inimx->m_vacct;
                $depositor = $inimx->m_buyerName;
                $bankcode = $inimx->m_vcdbank;
                $bank_owner = $inimx->m_nmvacct;
                    _MQ_noreturn("
                        insert into odtOrderOnlinelog (
                        ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
                        ) values (
                        '$ordernum', '$inicis_or[orderid]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$inicis_or[ordername]', '$ool_bank_name_array[$bankcode]', '$bankcode', '$escw_yn', '', '$buyr_tel2', '$bank_owner'
                        )
                    ");
				include_once($_SERVER['DOCUMENT_ROOT'].'/pages/shop.order.mail.send.virtual.php'); // 가상계좌 문자 & 메일 2016-12-16 LDD

				// 결제완료페이지 이동
				error_loc("/?pn=shop.order.complete");



            break;
            
            default: //문화상품권,해피머니
/*
         echo("승인결과코드:".$inimx->m_resultCode."<br>");
             echo("결과메시지:".$inimx->m_resultMsg."<br>");
             echo("지불수단:".$inimx->m_payMethod."<br>");
             echo("주문번호:".$inimx->m_moid."<br>");
             echo("TID:".$inimx->m_tid."<br>");
             echo("승인금액:".$inimx->m_resultprice."<br>");
             echo("승인일:".$inimx->m_pgAuthDate."<br>");
             echo("승인시각:".$inimx->m_pgAuthTime."<br>");
             echo("상점ID:".$inimx->m_mid."<br>");
             echo("구매자명:".$inimx->m_buyerName."<br>");
             echo("P_NOTI:".$inimx->m_noti."<br>");
             echo("NEXT_URL:".$inimx->m_nextUrl."<br>");
             echo("NOTI_URL:".$inimx->m_notiUrl."<br>");
*/
          }
	
	}
	else                      // 모바일 인증 실패
	{

		// 2017-01-09 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC
		$oc_res_cnt = _MQ(" select count(*) as cnt from odtOrderCardlog where oc_oordernum = '".$ordernum."' and oc_tid != '' and (oc_content like '%resultCode||0000§§%' or oc_content like '%m_resultCode||00§§%') ");
		if($oc_res_cnt['cnt'] == 1 ) {

			// 결제완료페이지 이동
			error_loc("/?pn=shop.order.complete");

		}
		// 결제실패 처리
		else {

			//해당 정보가 없으면 주문 취소
			$que = "
				insert odtOrderCardlog set
					 oc_oordernum = '". $ordernum ."'
					,oc_tid = '".$inimx->m_tid."'
					,oc_content = 'apprTm||".$apprTm."§§dealNo||".$inimx->m_authCode."§§status||".$inimx->status."§§rmesg1||".$inimx->rmesg1."'
					,oc_rdate = now();
			";
			_MQ_noreturn($que);

			_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' , orderstep='fail' , ordersau = '". addslashes(iconv('euckr','utf8',$inimx->rmesg1)) ."' where ordernum='". $ordernum ."' ");
			error_loc_msg("/?pn=shop.order.result" , iconv('euckr','utf8',$inimx->rmesg1)." - 결제에 실패하였습니다. 다시한번 확인 바랍니다.");        

		}
		// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC
	}
	  
  
?>
