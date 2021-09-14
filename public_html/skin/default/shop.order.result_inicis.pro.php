<?PHP

	session_start();
	$ordernum = $_SESSION["session_ordernum"];//주문번호
	include_once(dirname(__FILE__)."/../../include/inc.php");


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

	// --> 비회원 구매를 위한 쿠키 적용여부 파악
	cookie_chk();


		/**************************
		 * 1. 라이브러리 인클루드 *
		 **************************/
		require(PG_DIR."/inicis/libs/INILib.php");
		
		
		/***************************************
		 * 2. INIpay50 클래스의 인스턴스 생성 *
		 ***************************************/
		$inipay = new INIpay50;

		/*********************
		 * 3. 지불 정보 설정 *
		 *********************/
		$inipay->SetField("inipayhome", PG_DIR."/inicis"); // 이니페이 홈디렉터리(상점수정 필요)
		$inipay->SetField("type", "securepay");                         // 고정 (절대 수정 불가)
		$inipay->SetField("pgid", "INIphp".$pgid);                      // 고정 (절대 수정 불가)
		$inipay->SetField("subpgip","203.238.3.10");                    // 고정 (절대 수정 불가)
		$inipay->SetField("admin", $_SESSION['INI_ADMIN']);    // 키패스워드(상점아이디에 따라 변경)
		$inipay->SetField("debug", false);                             // 로그모드("true"로 설정하면 상세로그가 생성됨.)
		$inipay->SetField("uid", $uid);                                 // INIpay User ID (절대 수정 불가)
		$inipay->SetField("uip", getenv("REMOTE_ADDR"));                // 고정 (절대 수정 불가)
		$inipay->SetField("goodname", iconv("utf-8","euckr",$goodname));// 상품명 
		$inipay->SetField("currency", $currency);                       // 화폐단위

		$inipay->SetField("mid", $_SESSION['INI_MID']);        // 상점아이디
		$inipay->SetField("rn", $_SESSION['INI_RN']);          // 웹페이지 위변조용 RN값
		$inipay->SetField("price", $_SESSION['INI_PRICE']);        // 가격
		$inipay->SetField("enctype", $_SESSION['INI_ENCTYPE']);// 고정 (절대 수정 불가)


				 /*----------------------------------------------------------------------------------------
						 price 등의 중요데이터는
						 브라우저상의 위변조여부를 반드시 확인하셔야 합니다.

						 결제 요청페이지에서 요청된 금액과
						 실제 결제가 이루어질 금액을 반드시 비교하여 처리하십시오.

						 설치 메뉴얼 2장의 결제 처리페이지 작성부분의 보안경고 부분을 확인하시기 바랍니다.
						 적용참조문서: 이니시스홈페이지->가맹점기술지원자료실->기타자료실 의
																						'결제 처리 페이지 상에 결제 금액 변조 유무에 대한 체크' 문서를 참조하시기 바랍니다.
						 예제)
						 원 상품 가격 변수를 OriginalPrice 하고  원 가격 정보를 리턴하는 함수를 Return_OrgPrice()라 가정하면
						 다음 같이 적용하여 원가격과 웹브라우저에서 Post되어 넘어온 가격을 비교 한다.

				$OriginalPrice = Return_OrgPrice();
				$PostPrice = $_SESSION['INI_PRICE']; 
				if ( $OriginalPrice != $PostPrice )
				{
						//결제 진행을 중단하고  금액 변경 가능성에 대한 메시지 출력 처리
						//처리 종료 
				}

						----------------------------------------------------------------------------------------*/
		$inipay->SetField("buyername", iconv("utf-8","euckr",$buyername));       // 구매자 명
		$inipay->SetField("buyertel",  $buyertel);        // 구매자 연락처(휴대폰 번호 또는 유선전화번호)
		$inipay->SetField("buyeremail",$buyeremail);      // 구매자 이메일 주소
		$inipay->SetField("paymethod", $paymethod);       // 지불방법 (절대 수정 불가)
		$inipay->SetField("encrypted", $encrypted);       // 암호문
		$inipay->SetField("sessionkey",$sessionkey);      // 암호문
		$inipay->SetField("url", "http://".$_SERVER[HTTP_HOST]); // 실제 서비스되는 상점 SITE URL로 변경할것
		$inipay->SetField("cardcode", $cardcode);         // 카드코드 리턴
		$inipay->SetField("parentemail", $parentemail);   // 보호자 이메일 주소(핸드폰 , 전화결제시에 14세 미만의 고객이 결제하면  부모 이메일로 결제 내용통보 의무, 다른결제 수단 사용시에 삭제 가능)
		
		/*-----------------------------------------------------------------*
		 * 수취인 정보 *                                                   *
		 *-----------------------------------------------------------------*
		 * 실물배송을 하는 상점의 경우에 사용되는 필드들이며               *
		 * 아래의 값들은 INIsecurepay.html 페이지에서 포스트 되도록        *
		 * 필드를 만들어 주도록 하십시요.                                  *
		 * 컨텐츠 제공업체의 경우 삭제하셔도 무방합니다.                   *
		 *-----------------------------------------------------------------*/
		$inipay->SetField("recvname",iconv("utf-8","euckr",$recvname));     // 수취인 명
		$inipay->SetField("recvtel",$recvtel);                                                      // 수취인 연락처
		$inipay->SetField("recvaddr",iconv("utf-8","euckr",$recvaddr));     // 수취인 주소
		$inipay->SetField("recvpostnum",$recvpostnum);                                      // 수취인 우편번호
		$inipay->SetField("recvmsg",iconv("utf-8","euckr",$recvmsg));           // 전달 메세지

		$inipay->SetField("joincard",$joincard);                                                    // 제휴카드코드
		$inipay->SetField("joinexpire",$joinexpire);                                            // 제휴카드유효기간
		$inipay->SetField("id_customer",$id_customer);                                      // user_id

		
		/****************
		 * 4. 지불 요청 *
		 ****************/
		$inipay->startAction();

		$ordernum = $inipay->GetResult('MOID');

		$keys = array('tid',
									'ResultCode',
									'ResultMsg',
									'MOID',
									'ApplDate',
									'ApplTime',
									'ApplNum',
									'PayMethod',
									'TotPrice',
									'EventCode',
									'CARD_Num',
									'CARD_Interest',
									'CARD_Quota',
									'CARD_Code',
									'CARD_BankCode');

		// - 결제 성공 기록정보 저장 ---

		$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
		foreach($keys as $name) {
			$app_oc_content .= $name . "||" .iconv("euc-kr","utf-8",$inipay->GetResult($name)) . "§§" ;
		}


		if(is_login()) $indr = $mem_info;

		// 주문정보 추출
		$r = _MQ("select * from odtOrder where ordernum='". $ordernum ."' ");


		// - 주문결제기록 저장 ---
		$que = "
			insert odtOrderCardlog set
				 oc_oordernum = '".$ordernum."'
				,oc_tid = '". $inipay->GetResult('TID') ."'
				,oc_content = '". $app_oc_content ."'
				,oc_rdate = now();
		";
		if(!preg_match('/중복/i' , $app_oc_content)) _MQ_noreturn($que);
		// - 주문결제기록 저장 ---
		// - 결제 성공 기록정보 저장 ---


		if( $inipay->GetResult('ResultCode') == "00" ) {
			// -- 최종결제요청 결과 성공 DB처리 ---
				//echo "최종결제요청 결과 성공 DB처리하시기 바랍니다.<br>";

			$order = _MQ("select * from odtOrder as o left join odtOrderCardlog as oc on (o.ordernum = oc.oc_oordernum) where o.ordernum = '$ordernum'");

				if($inipay->GetResult('CSHR_ResultCode')) {
					$cash_no = $inipay->GetResult('CSHR_ResultCode');
		            _MQ_noreturn("update odtOrder set taxorder='Y' where ordernum='".$ordernum."'");
		            /*_MQ_noreturn("
						insert into odtOrder_cashlog (
							ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
						) values (
							'$ordernum', '$order[o_mid]', now(), '$cash_no', '$receipt_no', '$app_time', '$res_desc', '$method', '', '$amount', '$order[o_paymethod]', ''
						)
					");*/
		        }

			if(in_array($inipay->GetResult('PayMethod'),array('VBank'))) { // 가상계좌 또는 계좌이체 일때
				$ool_type = 'R';
				$tno = $inipay->GetResult('TID');
				$app_time = $inipay->GetResult('ApplDate');
				$amount = trim($inipay->GetResult('TotPrice'));
				$account = $inipay->GetResult('VACT_Num');
				$depositor = $order[ordername]?$order[ordername]:$inipay->GetResult('VACT_InputName');
				$bankcode = $inipay->GetResult('VACT_BankCode');
				$bank_owner = $inipay->GetResult('VACT_Name');
					_MQ_noreturn("
						insert into odtOrderOnlinelog (
						ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
						) values (
						'$ordernum', '$order[orderid]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$depositor', '$ool_bank_name_array[$bankcode]', '$bankcode', '$escw_yn', '', '$buyr_tel2', '$bank_owner'
						)
					");
				include_once($_SERVER['DOCUMENT_ROOT'].'/pages/shop.order.mail.send.virtual.php'); // 가상계좌 문자 & 메일 2016-12-16 LDD

				// 결제완료페이지 이동
				error_loc("/?pn=shop.order.complete",'top');
			} else {
				// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
				include "shop.order.result.pro.php";
				//$_applnum = $inipay->GetResult('ApplNum');
				$tno = $inipay->GetResult('TID');
				_MQ_noreturn("update odtOrder set authum = '".$tno."' where ordernum = '$ordernum'");
				// 결제완료페이지 이동
				error_loc("/?pn=shop.order.complete",'top');
			}
 
        
    }else {

			//최종결제요청 결과 실패 DB처리
			//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
			_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");
			error_loc_msg("/?pn=shop.order.result" , iconv("euc-kr","utf-8",$inipay->GetResult('ResultMsg'))." - 결제에 실패하였습니다. 다시한번 확인 바랍니다.","top");
    }
?>