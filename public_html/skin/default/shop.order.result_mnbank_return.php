<?PHP

	include_once( $_SERVER["DOCUMENT_ROOT"] ."/include/inc.php");
	require_once( $_SERVER["DOCUMENT_ROOT"] . '/pages/mnbank/Encryptor.class.php');


	//webTx에서 받은 결과값들
	$payMethod = $_POST['payMethod'];
	$mid = $_POST['mid'];
	$tid = $_POST['tid'];
	$mallUserId = $_POST['mallUserId'];
	$amt = $_POST['amt'];
	$buyerName = $_POST['buyerName'];
	$buyerTel = $_POST['buyerTel'];
	$buyerEmail = $_POST['buyerEmail'];
	$mallReserved = $_POST['mallReserved'];
	$goodsName = $_POST['goodsName'];
	$moid = $_POST['moid'];
	$authDate = $_POST['authDate'];
	$authCode = $_POST['authCode'];
	$fnCd = $_POST['fnCd'];
	$fnName = $_POST['fnName'];
	$resultCd = $_POST['resultCd'];
	$resultMsg = $_POST['resultMsg'];
	$errorCd = $_POST['errorCd'];
	$errorMsg = $_POST['errorMsg'];
	$vbankNum = $_POST['vbankNum'];
	$vbankExpDate = $_POST['vbankExpDate'];
	$ediDate = $_POST['ediDate'];
	$receiptTypeNo = $_POST['receiptTypeNo'];// ?? 임의로 추가한 부분 - 현금영수증 번호

	$mKey = $row_setup[P_PW];//상점키

	$encryptor = new Encryptor($mKey, $ediDate);
	$decAmt = $encryptor->decData($amt);
	$decMoid = $encryptor->decData($moid);


	// 주문정보 추출
	//$ordernum = $decMoid;
	$oque = "select * from odtOrderOnlinelog as ol inner join odtOrder as o on (o.ordernum=ol.ool_ordernum) where ol.ool_tid='" . $tid . "' and ol.ool_type='R' order by ol.ool_uid desc limit 1";
	$or = _MQ($oque);

	//회원사 DB에 저장되어있던 값
	$amtDb = $or[tPrice];//금액
	$moidDb = $or[ordernum];//moid
	$ordernum = $or[ordernum];//moid

/*
	if( $decAmt!=$amtDb || $decMoid!=$moidDb ){

		echo "위변조 데이터를 오류입니다.";

		//최종결제요청 결과 실패 DB처리
		//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
		_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");

	}
	else {
*/

		//3001 카드 결제 성공
		//4000 계좌이체 결제 성공
		//4100 가상계좌 발급 성공
		//4110 가상계좌 입금 성공
		//A000 휴대폰결제 처리 성공
		if( in_array($resultCd , array("4110" )) ) {
			$ool_type = 'I';
			$tno = $authCode;
			$app_time = $authDate;
			$amount = $decAmt;
			$account = $vbankNum;
			$depositor = $or[ordername];
			$bankcode = $fnCd;
			$bankname = $fnName;
			$bank_owner = $or[ordername];


			// 이미 입력된 값이 있는지 체크해서 없으면 insert
			$tmp = _MQ("select count(*) as cnt from odtOrderOnlinelog where ool_type='I' and ool_tid='".$tno."' and ool_ordernum='".$ordernum."' and ool_amount_current='".$amount."' ");
			if($tmp[cnt] == 0) {

				_MQ_noreturn("
					insert into odtOrderOnlinelog (
					ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
					) values (
					'$ordernum', '$or[orderid]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$or[ordername]', '$bankname', '$bankcode', '$escw_yn', '', '$buyerTel', '$depositor'
					)
				");

				if($receiptTypeNo) {
					_MQ_noreturn("update odtOrder set taxorder='Y' where ordernum='$ordernum'");
					_MQ_noreturn("insert into odtOrderCashlog (ocs_ordernum,ocs_member,ocs_date,ocs_tid,ocs_cashnum,ocs_respdate,ocs_amount,ocs_method,ocs_type) values ('$ordernum','$or[orderid]',now(),'$tno','$receiptTypeNo','$app_time','$amount','AUTH','virtual')");
				}

				_MQ_noreturn("update odtOrder set authum = '" . $tno . "' where ordernum = '" . $ordernum . "' ");

				$r = _MQ("select * from odtOrderOnlinelog as ol inner join odtOrder as o on (o.ordernum=ol.ool_ordernum) where ol.ool_ordernum='$ordernum' order by ol.ool_uid desc limit 1");

				// - 2016-09-05 ::: JJC ::: 주문정보 추출 ::: 가상계좌 - 이미 결제가 되었다면 추가 적용을 하지 않게 처리함. ---
				$iosr = get_order_info($order_no);

				if($r[ool_amount_total] == $r[ool_amount_current] && $iosr['paystatus'] <> "Y" ) {

					$sque = "update odtOrder set paystatus='Y' , orderstatus_step='결제확인' , paydate = now() where ordernum='". $ordernum ."' ";
					_MQ_noreturn($sque);

					// 상품 재고 차감 및 판매량 증가
					$_ordernum = $ordernum;
					include_once("shop.order.salecntadd_pro.php");

					// 결제가 확인되었을 경우 - 포인트 쿠폰 - 적용
					// 제공변수 : $_ordernum
					$_ordernum = $ordernum;
					include_once("shop.order.pointadd_pro.php");

					// 제휴마케팅 처리
					$_ordernum = $ordernum;
					include_once("shop.order.aff_marketing_pro.php");

					// 쿠폰상품은 티켓을 발행한다.
					// 제공변수 : $_ordernum
					$_ordernum = $ordernum;
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
					$_oemail = $r[o_oemail];
					if( mailCheck($_oemail) ){
						$_ordernum = $ordernum;
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
				} else { echo "통보실패"; }
			} echo "정상처리";
		} else { echo "통보실패"; }
//	}

?>