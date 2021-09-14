<?php

	session_start();
	$ordernum = $_SESSION["session_ordernum"] != '' ? $_SESSION["session_ordernum"] : $_POST['LGD_OID'];//주문번호
	include_once(dirname(__FILE__)."/../../include/inc.php");

	// --> 비회원 구매를 위한 쿠키 적용여부 파악
		cookie_chk();
    /*
     * [최종결제요청 페이지(STEP2-2)]
	 *
	 * 매뉴얼 "5.1. XPay 결제 요청 페이지 개발"의 "단계 5. 최종 결제 요청 및 요청 결과 처리" 참조
     *
     * LG유플러스으로 부터 내려받은 LGD_PAYKEY(인증Key)를 가지고 최종 결제요청.(파라미터 전달시 POST를 사용하세요)
     */

	$configPath = PG_DIR . "/lgpay/lgdacom"; //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정. 

    /*
     *************************************************
     * 1.최종결제 요청 - BEGIN
     *  (단, 최종 금액체크를 원하시는 경우 금액체크 부분 주석을 제거 하시면 됩니다.)
     *************************************************
     */

    $CST_PLATFORM               = $_POST["CST_PLATFORM"];
   	$CST_MID                    = $_POST["CST_MID"];
    $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
    $LGD_PAYKEY                 = $_POST["LGD_PAYKEY"];

    require_once(PG_DIR."/lgpay/lgdacom/XPayClient.php");

	// (1) XpayClient의 사용을 위한 xpay 객체 생성
	// (2) Init: XPayClient 초기화(환경설정 파일 로드) 
	// configPath: 설정파일
	// CST_PLATFORM: - test, service 값에 따라 lgdacom.conf의 test_url(test) 또는 url(srvice) 사용
	//				- test, service 값에 따라 테스트용 또는 서비스용 아이디 생성

    $xpay = &new XPayClient($configPath, $CST_PLATFORM);
	
	// (3) Init_TX: 메모리에 mall.conf, lgdacom.conf 할당 및 트랜잭션의 고유한 키 TXID 생성
		$xpay->Init_TX($LGD_MID);    
    $xpay->Set("LGD_TXNAME", "PaymentByKey");
    $xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
    
    //금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
	//$DB_AMOUNT = "DB나 세션에서 가져온 금액"; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
	//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
	//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);


    // -- 2016-12-14 LCY :: 데이터 처리 
		// 회원정보 추출
		if(is_login()) $indr = $row_member;    

		// 주문정보 추출
		$r = _MQ("select * from odtOrder where ordernum='". $ordernum ."' ");

		// 결제금액이 정상인지 체크
		if($_POST[LGD_AMOUNT] != $r[tPrice]) {
			error_loc_msg("/?pn=shop.order.result" , "결제금액이 다릅니다. 정상결제금액 : ".$r[tPrice].", 요청된결제금액 : ".$_POST[LGD_AMOUNT],"top");
		}
	    
    /*
     *************************************************
     * 1.최종결제 요청(수정하지 마세요) - END
     *************************************************
     */

    /*
     * 2. 최종결제 요청 결과처리
     *
     * 최종 결제요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
     */
	// (4) TX: lgdacom.conf에 설정된 URL로 소켓 통신하여 최종 인증요청, 결과값으로 true, false 리턴
    if ($xpay->TX()) {
        //1)결제결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
        // echo "결제요청이 완료되었습니다.  <br>";
        // echo "TX 통신 응답코드 = " . $xpay->Response_Code() . "<br>";		//통신 응답코드("0000" 일 때 통신 성공)
        // echo "TX 통신 응답메시지 = " . $xpay->Response_Msg() . "<p>";
            
        // echo "거래번호 : " . $xpay->Response("LGD_TID",0) . "<br>";
        // echo "상점아이디 : " . $xpay->Response("LGD_MID",0) . "<br>";
        // echo "상점주문번호 : " . $xpay->Response("LGD_OID",0) . "<br>";
        // echo "결제금액 : " . $xpay->Response("LGD_AMOUNT",0) . "<br>";
        // echo "결과코드 : " . $xpay->Response("LGD_RESPCODE",0) . "<br>";	//LGD_RESPCODE 가 반드시 "0000" 일때만 결제 성공, 그 외는 모두 실패
        // echo "결과메세지 : " . $xpay->Response("LGD_RESPMSG",0) . "<p>";
            

    		$ordernum = $xpay->Response("LGD_OID",0);

        $keys = $xpay->Response_Names();
        // -- 결제 기록정보 이어붙이기  
        $app_oc_content = ""; 
        foreach($keys as $name) {
					$app_oc_content .= $name . "||" .$xpay->Response($name, 0) . "§§" ;
        }
        
        // -- 2016-12-15 LCY :: 결제결과가 실패일경우 주문번호가 넘어오지 않는 문제 패치
        if(trim($ordernum) == '' && $xpay->Response_Code() != '0000' ){
					$ordernum = $_SESSION["session_ordernum"];
					$app_oc_content = '결제실패';
        }

				// - 주문결제기록 저장 ---
				$que = "
					insert odtOrderCardlog set
						 oc_oordernum = '".$ordernum."'
						,oc_tid = '". $xpay->Response("LGD_TID",0) ."'
						,oc_content = '". $app_oc_content ."'
						,oc_rdate = now();
				";
				_MQ_noreturn($que);
				// - 주문결제기록 저장 ---
				$insert_oc_uid = mysql_insert_id();// 2017-01-04 ::: 결제기록 고유번호 저장. ::: JJC
				// - 결제 성공 기록정보 저장 ---

        
        if( "0000" == $xpay->Response_Code() ) {
         	// -- 최종결제요청 결과 성공 DB처리 ---
           	//echo "최종결제요청 결과 성공 DB처리하시기 바랍니다.<br>";

        		if($xpay->Response("LGD_CASHRECEIPTCODE",0)=='0000') { // 현금영수증을 신청했으면 DB 업데이트
                    _MQ_noreturn("update odtOrder set taxorder = 'Y' where ordernum = '$ordernum'");
                }

                if($xpay->Response("LGD_PAYTYPE",0)=='SC0010') { // 카드결제 일때
                	$_authum = $xpay->Response("LGD_FINANCEAUTHNUM",0);
                	_MQ_noreturn("update odtOrder set authum = '$_authum' where ordernum = '$ordernum'");
					// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
					include dirname(__FILE__)."/shop.order.result.pro.php";
					error_loc("/?pn=shop.order.complete","top");
				} else if($xpay->Response("LGD_PAYTYPE",0)=='SC0040') { // 가상계좌 일때
					$ool_type = 'R';
					$tno = $xpay->Response("LGD_TID",0);
                    $app_time = $xpay->Response("LGD_PAYDATE",0);
                    $amount = $xpay->Response("LGD_AMOUNT",0);
                    $account = $xpay->Response("LGD_ACCOUNTNUM",0);
                    $bankname = $xpay->Response("LGD_FINANCENAME",0);
                    $bankcode = $xpay->Response("LGD_FINANCECODE",0);
                    $escw_yn = 'Y';
                    $buyr_tel2 = $xpay->Response("LGD_BUYERPHONE",0);
                    $depositor = $xpay->Response("LGD_BUYER",0);
                    $payer = $xpay->Response("LGD_PAYER",0);
					_MQ_noreturn("
						insert into odtOrderOnlinelog (
						ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
						) values (
						'$ordernum', '$r[orderid]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$r[ordername]', '$bankname', '$bankcode', '$escw_yn', '', '$buyr_tel2', '$depositor'
						)
					");
					include_once($_SERVER['DOCUMENT_ROOT'].'/pages/shop.order.mail.send.virtual.php'); // 가상계좌 문자 & 메일 2016-12-16 LDD
             		error_loc("/?pn=shop.order.complete","top");
				} else if($xpay->Response("LGD_PAYTYPE",0)=='SC0030') { // 실시간 계좌이체 일때
             		$_authum = $xpay->Response("LGD_FINANCEAUTHNUM",0);
                	_MQ_noreturn("update odtOrder set authum = '$_authum' where ordernum = '$ordernum'");
					// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
					include dirname(__FILE__)."/shop.order.result.pro.php";
					error_loc("/?pn=shop.order.complete","top");
				} else {
					// 결제완료페이지 이동
					error_loc("/?pn=shop.order.complete","top");
				}
     
        }else{

			//최종결제요청 결과 실패 DB처리
			//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";

			// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC
			$oc_res_cnt = _MQ(" select count(*) as cnt from odtOrderCardlog where oc_oordernum = '".$ordernum."' and oc_tid = '". $xpay->Response("LGD_TID",0) ."' and oc_content like '%LGD_RESPCODE||0000%' ");
			if($oc_res_cnt['cnt'] == 1 ) {

				// 결제 실패기록 삭제
				_MQ_noreturn("delete from odtOrderCardlog where oc_uid='". $insert_oc_uid ."' ");

				// 결제완료페이지 이동
				error_loc("/?pn=shop.order.complete",'top');

			}

			// 결제실패 처리
			else {

				_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");
				error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.","top");

			}
			// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC

        }
    }else {
			//2)API 요청실패 화면처리
			//echo "결제요청이 실패하였습니다.  <br>";
			//echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
			//echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";

			// - 주문결제기록 저장 ---
			$app_oc_content = "LGD_RESPMSG||" . $xpay->Response_Msg() . "§§"; // 주문결제기록 정보 이어 붙이기

			$que = "
				insert odtOrderCardlog set
					 oc_oordernum = '". $ordernum ."'
					,oc_tid = ''
					,oc_content = '". $app_oc_content ."'
					,oc_rdate = now();
			";
			_MQ_noreturn($que);
			// - 주문결제기록 저장 ---
			// - 결제 성공 기록정보 저장 ---


			//최종결제요청 결과 실패 DB처리
			//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
			_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");
			error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.","top");
    }
?>
