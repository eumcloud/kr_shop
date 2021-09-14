<?php
session_start();
include(dirname(__FILE__)."/../../include/inc.php");
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
	'48'=>'신협',
	'05'=>'외환',
	'20'=>'우리',
	'71'=>'우체국',
	'37'=>'전북',
	'35'=>'제주',
	'81'=>'하나',
	'27'=>'한국씨티',
	'23'=>'SC은행',
	'09'=>'동양증권',
	'78'=>'신한금융투자증권',
	'40'=>'삼성증권',
	'30'=>'미래에셋증권',
	'43'=>'한국투자증권',
	'69'=>'한화증권'
);

 /***************************************************************************************************************
 * 올더게이트로부터 가상계좌 입/출금 데이타를 받아서 상점에서 처리 한 후 
 * 올더게이트로 다시 응답값을 리턴하는 페이지입니다.
 * 상점 DB처리 부분을 업체에 맞게 수정하여 작업하시기 바랍니다.
***************************************************************************************************************/

/*********************************** 올더게이트로 부터 넘겨 받는 값들 시작 *************************************/
$trcode     = trim( $_POST["trcode"] );					    //거래코드
$service_id = trim( $_POST["service_id"] );					//상점아이디
$orderdt    = trim( $_POST["orderdt"] );				    //승인일자
$virno      = trim( $_POST["virno"] );				        //가상계좌번호
$deal_won   = trim( $_POST["deal_won"] );					//입금액
$ordno		= trim( $_POST["ordno"] );                      //주문번호
$inputnm	= trim( $_POST["inputnm"] );					//입금자명
/*********************************** 올더게이트로 부터 넘겨 받는 값들 끝 *************************************/

/***************************************************************************************************************
 * 상점에서 해당 거래에 대한 처리 db 처리 등....
 *
 * trcode = "1" ☞ 일반가상계좌 입금통보전문
 * trcode = "2" ☞ 일반가상계좌 취소통보전문
 *
***************************************************************************************************************/

if($trcode == '1') { // 입금

	_MQ_noreturn("
		update odtOrderOnlinelog set ool_amount_current = '$deal_won', ool_respdate = '$orderdt', ool_type='I' where ool_ordernum = '$ordno'
    ");
	$r = _MQ("select * from odtOrderOnlinelog as ol inner join odtOrder as o on (o.ordernum=ol.ool_ordernum) where ol.ool_ordernum='$ordno' order by ol.ool_uid desc limit 1");
	if($r[ool_amount_total] == $r[ool_amount_current]) {
		$sque = "update odtOrder set paystatus='Y' , orderstatus_step='결제확인' , paydate = now() where ordernum='". $ordno ."' ";
		_MQ_noreturn($sque);

					// 결제가 확인되었을 경우 - 포인트 쿠폰 - 적용
					// 제공변수 : $_ordernum
					$_ordernum = $ordernum;
					include_once(dirname(__FILE__)."/shop.order.pointadd_pro.php");

					// 쿠폰상품은 티켓을 발행한다.
					// 제공변수 : $_ordernum
					$_ordernum = $ordernum;
					include_once(dirname(__FILE__)."/shop.order.couponadd_pro.php");

					// 제휴마케팅 처리
					$_ordernum = $ordernum;
					include_once(dirname(__FILE__)."/shop.order.aff_marketing_pro.php");

					// 상품 재고 차감 및 판매량 증가
					$_ordernum = $ordernum;
					include_once(dirname(__FILE__)."/shop.order.salecntadd_pro.php");

					// 결제완료 문자발송
					$_ordernum = $ordernum;
					include_once(dirname(__FILE__)."/shop.order.sms_send.php");

					// 제휴마케팅 처리
					$_ordernum = $ordernum;
					include_once(dirname(__FILE__)."/shop.order.aff_marketing_pro.php");



		// - 메일발송 ---
		$_oemail = $r[o_oemail];
		if( mailCheck($_oemail) ){
			$_ordernum = $ordno;
			$_type = "online"; // 결제확인처리
			include_once(dirname(__FILE__)."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
			$_title = "주문하신 상품의 결제가 성공적으로 완료되었습니다!";
			//$_title_img = "images/mailing/title_order.gif";
			$_title_content = '<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님이 주문하신 내역입니다.</strong>';
			$_content = $mailing_app_content;
			$_content = get_mail_content($_title,$_title_content,$_content);
			mailer( $_oemail , $_title , $_content );
		}
		// - 메일발송 ---
	}
	$rSuccYn = 'y';
} 

else if($trcode == '2') { // 취소


	$rSuccYn = 'y';
}

else {
	$rSuccYn = 'n';
}


/******************************************처리 결과 리턴******************************************************/
$rResMsg  = "";
//$rSuccYn  = "y";// 정상 : y 실패 : n

//정상처리 경우 거래코드|상점아이디|주문일시|가상계좌번호|처리결과|
$rResMsg .= $trcode."|";
$rResMsg .= $service_id."|";
$rResMsg .= $orderdt."|";
$rResMsg .= $virno."|";
$rResMsg .= $rSuccYn."|";

echo $rResMsg;
/******************************************처리 결과 리턴******************************************************/
?> 