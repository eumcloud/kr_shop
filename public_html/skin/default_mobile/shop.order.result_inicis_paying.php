<?php
	include_once(dirname(__FILE__)."/../../include/inc.php");

//*******************************************************************************
// FILE NAME : mx_rnoti.php
// FILE DESCRIPTION :
// 이니시스 smart phone 결제 결과 수신 페이지 샘플
// 기술문의 : ts@inicis.com
// HISTORY
// 2010. 02. 25 최초작성
//*******************************************************************************

  $PGIP = $_SERVER['REMOTE_ADDR'];

  // 2019-03-25 SSJ :: 이니시스 승인 시스템 고도화에 따른 IP추가
  if($PGIP == "211.219.96.165" || $PGIP == "118.129.210.25" || $PGIP == "118.129.210.24" || $PGIP == "192.168.187.140" || $PGIP == "172.20.22.40" || $PGIP == "127.0.0.1" || $PGIP == "39.115.212.9" || $PGIP == "39.115.212.10" || $PGIP == "183.109.71.50" || $PGIP == "183.109.71.30" || $PGIP == "183.109.71.153" || $PGIP == "203.238.37.15")    //PG에서 보냈는지 IP로 체크 118.129.210.24, 192.168.187.140, 172.20.22.40, 127.0.0.1은 사내 네트웍에서 테스트하기 위한 용도임
  {
    // 이니시스 NOTI 서버에서 받은 Value
    $P_TID;				// 거래번호
    $P_MID;				// 상점아이디
    $P_AUTH_DT;			// 승인일자
    $P_STATUS;			// 거래상태 (00:성공, 01:실패)
    $P_TYPE;			// 지불수단
    $P_OID;				// 상점주문번호
    $P_FN_CD1;			// 금융사코드1
    $P_FN_CD2;			// 금융사코드2
    $P_FN_NM;			// 금융사명 (은행명, 카드사명, 이통사명)
    $P_AMT;				// 거래금액
    $P_UNAME;			// 결제고객성명
    $P_RMESG1;			// 결과코드
    $P_RMESG2;			// 결과메시지
    $P_NOTI;			// 노티메시지(상점에서 올린 메시지)
    $P_AUTH_NO;			// 승인번호

    $P_CARD_ISSUER_CODE; //카드 발급사 코드
    $P_CARD_NUM; //카드번호


    $P_TID = $_REQUEST[P_TID];
    $P_MID = $_REQUEST[P_MID];
    $P_AUTH_DT = $_REQUEST[P_AUTH_DT];
    $P_STATUS = $_REQUEST[P_STATUS];
    $P_TYPE = $_REQUEST[P_TYPE];
    $P_OID = $_REQUEST[P_OID];
    $P_FN_CD1 = $_REQUEST[P_FN_CD1];
    $P_FN_CD2 = $_REQUEST[P_FN_CD2];
    $P_FN_NM = $_REQUEST[P_FN_NM];
    $P_AMT = $_REQUEST[P_AMT];
    $P_UNAME = $_REQUEST[P_UNAME];
    $P_RMESG1 = $_REQUEST[P_RMESG1];
    $P_RMESG2 = $_REQUEST[P_RMESG2];
    $P_NOTI = $_REQUEST[P_NOTI];
    $P_AUTH_NO = $_REQUEST[P_AUTH_NO];
    $P_CARD_ISSUER_CODE = $_REQUEST[P_CARD_ISSUER_CODE];
    $P_CARD_NUM = $_REQUEST[P_CARD_NUM];

	// 가상계좌번호 추출
	$ex_rmesg1 = explode("|", $P_RMESG1);
	$_tmp_ex = explode("=", $ex_rmesg1[0]);
	$P_VACCT_NO = $_tmp_ex[1];


    //WEB 방식의 경우 가상계좌 채번 결과 무시 처리
    //(APP 방식의 경우 해당 내용을 삭제 또는 주석 처리 하시기 바랍니다.)
     if($P_TYPE == "VBANK")	//결제수단이 가상계좌이며
        {
           if($P_STATUS != "02") //입금통보 "02" 가 아니면(가상계좌 채번 : 00 또는 01 경우)
           {
             // echo "OK";
             // return;     //원래 mx_rnoti.php 소스는 OK리턴하고 종료하도록 되어 있으나, 테스트를 위해 이 부분을 삭제함
           }
        }

    $PageCall_time = date("H:i:s");
    $value = array(
            "PageCall time" => $PageCall_time,
            "P_TID"			=> $P_TID,
            "P_MID"     => $P_MID,
            "P_AUTH_DT" => $P_AUTH_DT,
            "P_STATUS"  => $P_STATUS,
            "P_TYPE"    => $P_TYPE,
            "P_OID"     => $P_OID,
            "P_FN_CD1"  => $P_FN_CD1,
            "P_FN_CD2"  => $P_FN_CD2,
            "P_FN_NM"   => $P_FN_NM,
            "P_AMT"     => $P_AMT,
            "P_UNAME"   => $P_UNAME,
            "P_RMESG1"  => $P_RMESG1,
            "P_RMESG2"  => $P_RMESG2,
            "P_NOTI"    => $P_NOTI,
            "P_AUTH_NO" => $P_AUTH_NO,
            "P_CARD_ISSUER_CODE" => $P_CARD_ISSUER_CODE,
            "P_CARD_NUM" => $P_CARD_NUM
            );
        // 결제처리에 관한 로그 기록
		writeLog($value);

		// 주문번호
		$ordernum = $order_no = $P_OID;

		// - 결제 관련 로그 기록 ---
		$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
		foreach($_REQUEST as $key => $value) $app_oc_content .= $key . "||" .iconv("euc-kr","utf-8",$value) . "§§" ;

		// 로그 기록.
		$que = "
				insert odtOrderCardlog set
				 oc_oordernum = '". $ordernum ."'
				,oc_tid = '".$P_TID."'
				,oc_content = '".$app_oc_content."'
				,oc_rdate = now();
		";
		_MQ_noreturn($que);

		if($P_STATUS == "00") {	// 결제 성공

			// 2015-04-17 모바일 가상계좌 구매시 자동 결제확인 되는 버그 수정 패치
			if($P_TYPE == "VBANK") {

				echo "OK"; //절대로 지우지 마세요
				return;
			}

			// -- 최종결제요청 결과 성공 DB처리 ---
			$sque = "update odtOrder set paystatus='Y' , orderstatus_step='결제확인' , paydate = now() , authum = '" . $P_TID . "' where ordernum='". $ordernum ."' ";
			_MQ_noreturn($sque);

			/*
			결제완료후 실행되는 소스는 isp.php 파일에서 처리한다. (메일,문자,포인트 차감등)
			*/

			echo "OK"; //절대로 지우지 마세요

		} else if($P_STATUS == "02" && $P_TYPE == "VBANK") { // 가상계좌 입금통보시
			$ool_type = 'I';
			$r = _MQ("select * from odtOrderOnlinelog where ool_ordernum='$P_OID' order by ool_uid desc");
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
					'$P_OID',
					'$r[ool_member]',
					now(),
					'$P_TID',
					'$ool_type',
					'$P_AUTH_DT',
					'$P_AMT',
					'$r[ool_amount_total]',
					'$P_VACCT_NO',
					'',
					'$P_UNAME',
					'$P_FN_NM',
					'$P_FN_CD1',
					'Y',
					'',
					'$r[ool_deposit_tel]',
					'$r[ool_bank_owner]'
				)
			");
			// 현금영수증항목은 넘어오지 않으므로 주석처리
			if(!empty($no_cshr_tid)) {
				_MQ_noreturn("update odtOrder set taxorder='Y' where ordernum='$order_no'");
				_MQ_noreturn("insert into odtOrderCashlog (ocs_ordernum,ocs_member,ocs_date,ocs_tid,ocs_cashnum,ocs_respdate,ocs_amount,ocs_method,ocs_type) values ('$order_no','$r[ool_member]',now(),'$no_cshr_tid','$no_cshr_appl','$tm_cshr','$amt_input','AUTH','virtual')");
			}

			$r = _MQ("select * from odtOrderOnlinelog as ol inner join odtOrder as o on (o.ordernum=ol.ool_ordernum) where ol.ool_ordernum='$order_no' order by ol.ool_uid desc limit 1");

			if($r[ool_amount_total] == $r[ool_amount_current]) {
				$sque = "update odtOrder set paystatus='Y' , orderstatus_step='결제확인' , paydate = now() , authum = '" . $P_TID . "' where ordernum='". $order_no ."' ";
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
				$_oemail = $r[o_oemail];
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

			echo "OK"; //절대로 지우지 마세요
			return;

		} else {
			echo "FAIL";
		}

		return;


} // 노티서버의 IP주소를 확인하는 if문의 end
else
{
	echo "FAIL"; // 잘못된 접근...
}


function writeLog($msg)
{
    $path=PG_M_DIR."/inicis/log/";
    $file = "noti_input_".date("Ymd").".log";
    if(!($fp = fopen($path.$file, "a+"))) return 0;

    ob_start();
    print_r($msg);
    $ob_msg = ob_get_contents();
    ob_clean();

    if(fwrite($fp, " ".$ob_msg."\n") === FALSE)
    {
        fclose($fp);
        return 0;
    }
    fclose($fp);
    return 1;
}


?>