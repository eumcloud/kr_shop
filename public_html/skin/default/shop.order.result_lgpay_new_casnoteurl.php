<?php
		session_start();
		$ordernum = $_SESSION["session_ordernum"];//주문번호
		if(!$HTTP_POST_VARS){// PHP 5.3 이하일경우 변수 대체
			$HTTP_POST_VARS = $_POST;
		}

		include_once(dirname(__FILE__)."/../../include/inc.php");

		$paytype_array = array(
			'virtual'	=>	'SC0040',
			'online'	=>	'SC0100',
			'iche'		=>	'SC0030'
		);

		$ocs_type = array_flip($paytype_array);


    /*
     * [상점 결제결과처리(DB) 페이지]
     *
     * 1) 위변조 방지를 위한 hashdata값 검증은 반드시 적용하셔야 합니다.
     *
     */
    $LGD_RESPCODE            = $_POST["LGD_RESPCODE"];				// 응답코드: 0000(성공) 그외 실패
    $LGD_RESPMSG             = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_RESPMSG"]);              // 응답메세지
    $LGD_MID                 = $_POST["LGD_MID"];					// 상점아이디
    $LGD_OID                 = $_POST["LGD_OID"];					// 주문번호
    $LGD_AMOUNT              = $_POST["LGD_AMOUNT"];				// 거래금액
    $LGD_TID                 = $_POST["LGD_TID"];					// LG유플러스에서 부여한 거래번호
    $LGD_PAYTYPE             = $_POST["LGD_PAYTYPE"];				// 결제수단코드
    $LGD_PAYDATE             = $_POST["LGD_PAYDATE"];				// 거래일시(승인일시/이체일시)
    $LGD_HASHDATA            = $_POST["LGD_HASHDATA"];				// 해쉬값
    $LGD_FINANCECODE         = $_POST["LGD_FINANCECODE"];			// 결제기관코드(은행코드)
    $LGD_FINANCENAME         = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_FINANCENAME"]);          // 결제기관이름(은행이름)
    $LGD_ESCROWYN            = $_POST["LGD_ESCROWYN"];				// 에스크로 적용여부
    $LGD_TIMESTAMP           = $_POST["LGD_TIMESTAMP"];				// 타임스탬프
    $LGD_ACCOUNTNUM          = $_POST["LGD_ACCOUNTNUM"];			// 계좌번호(무통장입금)
    $LGD_CASTAMOUNT          = $_POST["LGD_CASTAMOUNT"];			// 입금총액(무통장입금)
    $LGD_CASCAMOUNT          = $_POST["LGD_CASCAMOUNT"];			// 현입금액(무통장입금)
    $LGD_CASFLAG             = $_POST["LGD_CASFLAG"];				// 무통장입금 플래그(무통장입금) - 'R':계좌할당, 'I':입금, 'C':입금취소
    $LGD_CASSEQNO            = $_POST["LGD_CASSEQNO"];				// 입금순서(무통장입금)
    $LGD_CASHRECEIPTNUM      = $_POST["LGD_CASHRECEIPTNUM"];		// 현금영수증 승인번호
    $LGD_CASHRECEIPTSELFYN   = $_POST["LGD_CASHRECEIPTSELFYN"];		// 현금영수증자진발급제유무 Y: 자진발급제 적용, 그외 : 미적용
    $LGD_CASHRECEIPTKIND     = $_POST["LGD_CASHRECEIPTKIND"];		// 현금영수증 종류 0: 소득공제용 , 1: 지출증빙용
		$LGD_PAYER     			 = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_PAYER"]);      			// 입금자명

    // $LGD_TELNO               = $HTTP_POST_VARS["LGD_TELNO"];                // 입금자 휴대폰번호
    // $LGD_ACCOUNTOWNER        = $HTTP_POST_VARS["LGD_ACCOUNTOWNER"];                // 예금주명


    /*
     * 구매정보
     */
    $LGD_BUYER               = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_BUYER"]);                // 구매자
    $LGD_PRODUCTINFO         = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_PRODUCTINFO"]);          // 상품명
    $LGD_BUYERID             = $HTTP_POST_VARS["LGD_BUYERID"];              // 구매자 ID
    $LGD_BUYERADDRESS        = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_BUYERADDRESS"]);         // 구매자 주소
    $LGD_BUYERPHONE          = $HTTP_POST_VARS["LGD_BUYERPHONE"];           // 구매자 전화번호
    $LGD_BUYEREMAIL          = $HTTP_POST_VARS["LGD_BUYEREMAIL"];           // 구매자 이메일
    $LGD_BUYERSSN            = $HTTP_POST_VARS["LGD_BUYERSSN"];             // 구매자 주민번호
    $LGD_PRODUCTCODE         = $HTTP_POST_VARS["LGD_PRODUCTCODE"];          // 상품코드
    $LGD_RECEIVER            = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_RECEIVER"]);             // 수취인
    $LGD_RECEIVERPHONE       = $HTTP_POST_VARS["LGD_RECEIVERPHONE"];        // 수취인 전화번호
    $LGD_DELIVERYINFO        = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_DELIVERYINFO"]);         // 배송지

		$LGD_MERTKEY = $row_setup[P_PW];  //LG유플러스에서 발급한 상점키로 변경해 주시기 바랍니다.

    $LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);

    /*
     * 상점 처리결과 리턴메세지
     *
     * OK  : 상점 처리결과 성공
     * 그외 : 상점 처리결과 실패
     *
     * ※ 주의사항 : 성공시 'OK' 문자이외의 다른문자열이 포함되면 실패처리 되오니 주의하시기 바랍니다.
     */
    $resultMSG = "결제결과 상점 DB처리(LGD_CASNOTEURL) 결과값을 입력해 주시기 바랍니다.";

	// @ LCY 2017-01-24 주문번호추가
	$order_no =  $LGD_OID;


    if ( $LGD_HASHDATA2 == $LGD_HASHDATA ) { //해쉬값 검증이 성공이면
        if ( "0000" == $LGD_RESPCODE ){ //결제가 성공이면
        	if( "R" == $LGD_CASFLAG ) {
                /*
                 * 무통장 할당 성공 결과 상점 처리(DB) 부분
                 * 상점 결과 처리가 정상이면 "OK"
                 */

                //_MQ_noreturn("update odrOrder set o_status='결제대기' where o_ordernum='$LGD_OID'");


                //if( 무통장 할당 성공 상점처리결과 성공 )
                $resultMSG = "OK";
        	}else if( "I" == $LGD_CASFLAG ) {
 	            /*
    	         * 무통장 입금 성공 결과 상점 처리(DB) 부분
        	     * 상점 결과 처리가 정상이면 "OK"
            	 */
 	            _MQ_noreturn("
					insert into odtOrderOnlinelog (
						ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_deposit_tel, ool_bank_owner
					) values (
						'$LGD_OID', '$LGD_BUYERID', now(), '$LGD_TID', '$LGD_CASFLAG', '$LGD_PAYDATE', '$LGD_AMOUNT', '$LGD_AMOUNT', '$LGD_ACCOUNTNUM', '$LGD_CASSEQNO', '$LGD_PAYER', '$LGD_FINANCENAME', '$LGD_FINANCECODE', '$LGD_ESCROWYN', '$LGD_TELNO', '$LGD_ACCOUNTOWNER'
					)
                ");

                $r = _MQ("select * from odtOrderOnlinelog as ol inner join odtOrder as o on (o.ordernum=ol.ool_ordernum) where ol.ool_ordernum='$LGD_OID' order by ol.ool_uid desc limit 1");

				// - 2016-09-05 ::: JJC ::: 주문정보 추출 ::: 가상계좌 - 이미 결제가 되었다면 추가 적용을 하지 않게 처리함. ---
				$iosr = get_order_info($order_no);

				if($r[ool_amount_total] == $r[ool_amount_current] && $iosr['paystatus'] <> "Y" ) {

                    if($LGD_CASHRECEIPTNUM&&$r[taxorder]=='Y') { // 현금영수증을 신청했고, 승인번호가 발급되었을 경우 DB에 등록
                        _MQ_noreturn("
                            insert into odtOrderCashlog (
                                ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
                            ) values (
                                '$LGD_OID', '$LGD_BUYERID', now(), '$LGD_TID', '$LGD_CASHRECEIPTNUM', '$LGD_PAYDATE', '', 'AUTH', '', '$LGD_AMOUNT', '$ocs_type[$LGD_PAYTYPE]', ''
                            )
                        ");
                    }

                	$sque = "update odtOrder set paystatus='Y' , orderstatus_step='결제확인' , paydate = now() where ordernum='". $LGD_OID ."' ";
					_MQ_noreturn($sque);

					// 상품 재고 차감 및 판매량 증가
					$_ordernum = $LGD_OID;
					include_once("shop.order.salecntadd_pro.php");

					// 결제가 확인되었을 경우 - 포인트 쿠폰 - 적용
					// 제공변수 : $_ordernum
					$_ordernum = $LGD_OID;
					include_once("shop.order.pointadd_pro.php");

					// 제휴마케팅 처리
					$_ordernum = $LGD_OID;
					include_once("shop.order.aff_marketing_pro.php");

					// 쿠폰상품은 티켓을 발행한다.
					// 제공변수 : $_ordernum
					$_ordernum = $LGD_OID;
					include_once("shop.order.couponadd_pro.php");

					// - 문자발송 ---
					$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					$smskbn = "payconfirm_mem";	// 문자 발송 유형
					if($row_sms[$smskbn][smschk] == "y") {
						$sms_to		= phone_print($r[orderhtel1],$r[orderhtel2],$r[orderhtel3]);
						$sms_from	= $row_company[tel];

						//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
						// 치환작업
						$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $r['ordernum']);
						$sms_msg = $arr_sms_msg['msg'];
						$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
						//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

					}
					//onedaynet_sms_multisend($arr_send);
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					onedaynet_alimtalk_multisend($arr_send);
					// - 문자발송 ---

                    order_status_update($_ordernum);

					// - 메일발송 ---
					$_oemail = $r[orderemail];
					if( mailCheck($_oemail) ){
						$_ordernum = $LGD_OID;
						$_type = "online"; // 결제확인처리
						include_once("shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
						$_title = "주문하신 상품의 결제가 성공적으로 완료되었습니다!";
						//$_title_img = "images/mailing/title_order.gif";
						$_title_content = '<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님이 주문하신 내역입니다.</strong>';
						$_content = $mailing_app_content;
						$_content = get_mail_content($_title,$_title_content,$_content);
						mailer( $_oemail , $_title , $_content );
					}
					// - 메일발송 ---
                }
            	//if( 무통장 입금 성공 상점처리결과 성공 )
            	$resultMSG = "OK";
        	}else if( "C" == $LGD_CASFLAG ) {
 	            /*
    	         * 무통장 입금취소 성공 결과 상점 처리(DB) 부분
        	     * 상점 결과 처리가 정상이면 "OK"
            	 */
                $CST_PLATFORM = $row_setup[P_MODE];
                $CST_MID = $row_setup[P_ID];
                $LGD_MID = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
                $configPath = PG_DIR . "/lgpay/lgdacom";
                require_once(PG_DIR."/lgpay/lgdacom/XPayClient.php");
                $xpay = &new XPayClient($configPath, $CST_PLATFORM);
                $xpay->Init_TX($LGD_MID);
                $xpay->Set("LGD_TXNAME", "CashReceipt");
                $xpay->Set("LGD_METHOD", 'CANCEL');
                $xpay->Set("LGD_PAYTYPE", $LGD_PAYTYPE);
                $xpay->Set("LGD_ENCODING", 'UTF-8');
                $xpay->Set("LGD_ENCODING_NOTEURL", 'UTF-8');
                $xpay->Set("LGD_ENCODING_RETURNURL", 'UTF-8');

                if ($xpay->TX()) {

                $ocs_cashnum = $xpay->Response("LGD_CASHRECEIPTNUM",0);
                $ocs_respdate = $xpay->Response("LGD_RESPDATE",0);
                $ocs_seqno = $xpay->Response("LGD_SEQNO",0);
                $ocs_msg = $xpay->Response_Msg();

                }

                $cash = _MQ("select * from odtOrderCashlog where ocs_ordernum='$LGD_OID' order by ocs_uid desc limit 1");

                _MQ_noreturn("
                    insert into odtOrderCashlog (
                        ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
                    ) values (
                        '$LGD_OID', '$LGD_BUYERID', now(), '$LGD_TID', '$ocs_cashnum', '$ocs_respdate', '$ocs_msg', 'CANCEL', '$cash[ocs_cardnum]', '$cash[ocs_amount]', '$LGD_PAYTYPE', '$ocs_seqno'
                    )
                ");

                _MQ_noreturn("
                    insert into odtOrderOnlinelog (
                        ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_deposit_tel, ool_bank_owner
                    ) values (
                        '$LGD_OID', '$LGD_BUYERID', now(), '$LGD_TID', '$LGD_CASFLAG', '$LGD_PAYDATE', '$LGD_CASCAMOUNT', '$LGD_CASTAMOUNT', '$LGD_ACCOUNTNUM', '$LGD_CASSEQNO', '$LGD_PAYER', '$LGD_FINANCENAME', '$LGD_FINANCECODE', '$LGD_ESCROWYN', '$LGD_TELNO', '$LGD_ACCOUNTOWNER'
                    )
                ");

            	//if( 무통장 입금취소 성공 상점처리결과 성공 )
            	$resultMSG = "OK";
        	}
        } else { //결제가 실패이면
            /*
             * 거래실패 결과 상점 처리(DB) 부분
             * 상점결과 처리가 정상이면 "OK"
             */
            //if( 결제실패 상점처리결과 성공 )
            $resultMSG = "OK";
        }
    } else { //해쉬값이 검증이 실패이면

         // hashdata검증 실패 로그를 처리하시기 바랍니다.

        $resultMSG = "결제결과 상점 DB처리(LGD_CASNOTEURL) 해쉬값 검증이 실패하였습니다.";
    }



    echo $resultMSG;
?>
