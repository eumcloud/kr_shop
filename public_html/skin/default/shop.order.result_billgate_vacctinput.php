<?php

/*@extract($_GET);
@extract($_POST);
@extract($_SERVER);
*/
session_start();

@extract($_GET);
@extract($_POST);
@extract($_SERVER);
@extract($_REQUEST);

	$ool_bank_name_array = array(
			'039'=>'경남',
			'034'=>'광주',
			'004'=>'국민',
			'003'=>'기업',
			'011'=>'농협',
			'031'=>'대구',
			'032'=>'부산',
			'002'=>'산업',
			'045'=>'새마을금고',
			'007'=>'수협',
			'088'=>'신한',
			'026'=>'신한',
			'048'=>'신협',
			'005'=>'외환',
			'020'=>'우리',
			'071'=>'우체국',
			'037'=>'전북',
			'035'=>'제주',
			'081'=>'하나',
			'027'=>'한국씨티',
			'053'=>'씨티',
			'023'=>'SC은행',
			'009'=>'동양증권',
			'078'=>'신한금융투자증권',
			'040'=>'삼성증권',
			'030'=>'미래에셋증권',
			'043'=>'한국투자증권',
			'069'=>'한화증권'
		);

include_once(dirname(__FILE__)."/../../include/inc.php");

/*

	$SERVICE_ID 		:	빌게이트에서 발급된 SERVICE_ID
	$SERVICE_CODE		:	서비스코드(가상계좌:1800)
	$ORDER_ID			:	주문번호
	$ORDER_DATE			:	주문일시
	$RESPONSE_CODE		:	응답코드
	$RESPONSE_MESSAGE	:	응답메세지
	$TRANSACTION_ID		:	거래번호
	$AUTH_AMOUNT		:	승인금액
	$AUTH_DATE			:	승인날짜

*/

	$order_no = $ORDER_ID;


		// 여기에 DB 설정

		$ool_type = 'I';
		$r = _MQ("select * from odtOrderOnlinelog where ool_ordernum='$order_no' order by ool_uid desc");

		if($r[ool_amount_total]!=$AUTH_AMOUNT) { echo "AMOUNT ERROR"; exit; } // 빌게이트는 부분입금을 지원하지 않는다.

		_MQ_noreturn("
			insert into odtOrderOnlinelog (
				ool_ordernum,
				ool_member,
				ool_date,
				ool_tid,
				ool_type,
				ool_respdate,
				ool_amount_current,
				ool_amount_total,
				ool_account_num,
				ool_account_code,
				ool_deposit_name,
				ool_bank_name,
				ool_bank_code,
				ool_escrow,
				ool_escrow_code,
				ool_deposit_tel,
				ool_bank_owner
			) values (
				'$order_no',
				'$r[ool_member]',
				now(),
				'$TRANSACTION_ID',
				'$ool_type',
				'$AUTH_DATE',
				'$AUTH_AMOUNT',
				'$r[ool_amount_total]',
				'$r[ool_account_num]',
				'',
				'',
				'$r[ool_bank_name]',
				'$r[ool_bank_code]',
				'Y',
				'',
				'$r[ool_deposit_tel]',
				'$r[ool_bank_owner]'
			)
		");

		// 빌게이트는 현금영수증 발급 관련 리턴값이 없다.
		/*if(!empty($no_cshr_tid)) {
			_MQ_noreturn("update smart_order set o_get_tax='Y' where o_ordernum='$order_no'");
			_MQ_noreturn("insert into smart_order_cashlog (ocs_ordernum,ocs_member,ocs_date,ocs_tid,ocs_cashnum,ocs_respdate,ocs_amount,ocs_method,ocs_type) values ('$order_no','$r[ool_member]',now(),'$no_cshr_tid','$no_cshr_appl','$tm_cshr','$amt_input','AUTH','virtual')");
		}*/

		$r = _MQ("select * from odtOrderOnlinelog as ol inner join odtOrder as o on (o.ordernum=ol.ool_ordernum) where ol.ool_ordernum='$order_no' order by ol.ool_uid desc limit 1");

		// - 2016-09-05 ::: JJC ::: 주문정보 추출 ::: 가상계좌 - 이미 결제가 되었다면 추가 적용을 하지 않게 처리함. ---
		$iosr = get_order_info($order_no);

		if($r[ool_amount_total] == $r[ool_amount_current] && $iosr['paystatus'] <> "Y" ) {

			$sque = "update odtOrder set paystatus='Y' , orderstatus_step='결제확인' , paydate = now() where ordernum='". $order_no ."' ";
			_MQ_noreturn($sque);

				// 상품 재고 차감 및 판매량 증가
				$_ordernum = $order_no;
				include_once("shop.order.salecntadd_pro.php");

				// 결제가 확인되었을 경우 - 포인트 쿠폰 - 적용
				// 제공변수 : $_ordernum
				$_ordernum = $order_no;
				include_once("shop.order.pointadd_pro.php");

				// 제휴마케팅 처리
				$_ordernum = $order_no;
				include_once("shop.order.aff_marketing_pro.php");

				// 쿠폰상품은 티켓을 발행한다.
				// 제공변수 : $_ordernum
				$_ordernum = $order_no;
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
					$_ordernum = $order_no;
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

?>
RC:111