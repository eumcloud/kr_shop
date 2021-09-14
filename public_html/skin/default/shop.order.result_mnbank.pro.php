<?PHP


	require_once $_SERVER["DOCUMENT_ROOT"] . '/pages/mnbank/Encryptor.class.php';
	include_once( $_SERVER["DOCUMENT_ROOT"] ."/include/inc.php");

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
	$receiptTypeNo = $_POST['receiptTypeNo'];
	$cashReceipt = $_POST['cashReceipt'];


	$mKey = $row_setup[P_PW];//상점키
	$encryptor = new Encryptor($mKey, $ediDate);
	$decAmt = $encryptor->decData($amt);
	$decMoid = $encryptor->decData($moid);
	
	// 주문정보 추출
	$ordernum = $decMoid;
	$or = _MQ(" select * from odtOrder where ordernum='". $ordernum ."' ");

	//회원사 DB에 저장되어있던 값
	$amtDb = $or[tPrice];//금액
	$moidDb = $or[ordernum];//moid


	if( $decAmt!=$amtDb || $decMoid!=$moidDb ){

		//echo "위변조 데이터를 오류입니다.";

		//최종결제요청 결과 실패 DB처리
		//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
		_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");
		error_loc_msg("/" , "결제에 실패하였습니다.\\n\\n오류메시지 : 위변조 데이터를 오류입니다. ( $decAmt , $amtDb :: $decMoid , $moidDb ) \\n\\n다시 한번 확인 바랍니다.",'top');

	}
	else {


		//3001 카드 결제 성공
		//4000 계좌이체 결제 성공
		//4100 가상계좌 발급 성공
		//4110 가상계좌 입금 성공
		//A000 휴대폰결제 처리 성공
		if( in_array($resultCd , array("3001" , "4000" , "4100", "A000")) ) {

			// ******* 결제결과 수신 여부 알림 *****************
			ResultConfirm::send($tid, "000");
			// ******* 결제결과 수신 여부 알림 *****************

			// --------------------------- DB처리 ------------------------------
			// 현금영수증을 신청했으면 DB 업데이트
			if($cashReceipt == 1) { 
				_MQ_noreturn("update odtOrder set taxorder = 'Y' where ordernum = '$ordernum'");
			}

			// - 주문결제기록 저장 ---
			$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
			foreach($_POST as $k=>$v) {
				$app_oc_content .= $k . "||" . $v . "§§" ;
			}
			$que = "
				insert odtOrderCardlog set
					 oc_oordernum = '".$ordernum."'
					,oc_tid = '". $tid ."'
					,oc_content = '". $app_oc_content ."'
					,oc_rdate = now();
			";
			_MQ_noreturn($que);
			// - 주문결제기록 저장 ---


			// - 가상계좌 처리 ---
			if($payMethod == "VBANK" && $resultCd == "4100") {
				$ool_type = 'R';
				$tno = $tid;
				$app_time = $authDate;
				$amount = $amtDb;
				$account = $vbankNum;
				$depositor = $or[ordername];
				$bankcode = $fnCd;
				$bankname = $fnName;
				$bank_owner = $or[ordername];
				_MQ_noreturn("
					insert into odtOrderOnlinelog (
					ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
					) values (
					'$ordernum', '$or[orderid]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$or[ordername]', '$bankname', '$bankcode', '$escw_yn', '', '$buyerTel', '$depositor'
					)
				");
				include_once($_SERVER['DOCUMENT_ROOT'].'/pages/shop.order.mail.send.virtual.php'); // 가상계좌 문자 & 메일 2016-12-16 LDD
			}
			// - 가상계좌 처리 ---
			// - 신용카드 , 계좌이체 처리 ---
			else {

				_MQ_noreturn("update odtOrder set authum = '" . $tid . "' where ordernum = '" . $ordernum . "' ");

				// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
				include "shop.order.result.pro.php";
			}

			// 결제완료페이지 이동
			error_loc("/?pn=shop.order.complete","top");
			// --------------------------- DB처리 ------------------------------

		}


		else {

			//최종결제요청 결과 실패 DB처리
			//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
			_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");
			error_loc_msg("/" , "결제에 실패하였습니다.\\n\\n오류메시지 : ( ". $resultCd ." : ". $resultMsg ." ) \\n\\n다시 한번 확인 바랍니다.",'top');

		}

	}

?>