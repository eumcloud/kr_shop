<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

# 주문정보 조회
$ordernum = ($ordernum?$ordernum:$_ordernum);
$Vorder = _MQ(" select * from `odtOrder` where `ordernum` = '{$ordernum}' ");
$BankInfo = _MQ(" select * from `odtOrderOnlinelog` where `ool_ordernum` = '{$ordernum}' ");
$_paybankname = ($BankInfo['ool_uid']?"[{$BankInfo['ool_bank_name']}] {$BankInfo['ool_account_num']}".($BankInfo['ool_bank_owner']?", {$BankInfo['ool_bank_owner']}" : null):$Vorder['paybankname']);


# 메일발송
if(mailCheck($Vorder['orderemail']) ){
	// $_ordernum ==> 주문번호
	$_ordernum = $ordernum;
	$_type = "virtual"; // 결제확인처리
	include($_SERVER['DOCUMENT_ROOT'].'/pages/shop.order.mail.php'); // 메일 내용 불러오기 ($mailing_content)
	$_title = "[".$row_setup['site_name']."] 가상계좌 결제를 하셨습니다.";
	$_title_content = '
	<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님이 주문하신 내역입니다.</strong><br />
	기한내에 입금해주시면 주문이 완료됩니다.
	';
	$_content = $mailing_app_content;
	$_content = get_mail_content($_title, $_title_content, $_content);
	mailer($Vorder['orderemail'], $_title, $_content);
}


# 문자 발송
$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
$smskbn = "virtual_mem";	// 문자 발송 유형
$SMSProduct = get_order_product_info($_ordernum); // 2016-07-19 LDD
$SMSProductCnt = sizeof($SMSProduct);
$SMSProduct = $SMSProduct[0]; // 2016-07-19 LDD
if($row_sms[$smskbn]['smschk'] == "y") {
	//$sms_to		= phone_print($_ohtel1,$_ohtel2,$_ohtel3);
	$sms_to		= phone_print($Vorder['orderhtel1'],$Vorder['orderhtel2'],$Vorder['orderhtel3']);
	$sms_from	= $row_company['tel'];

	//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
	// 치환작업
	$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $_ordernum, array(
		'{{주문번호}}'     => $_ordernum,
		'{{결제금액}}' => number_format($Vorder['tPrice']),
		'{{입금계좌정보}}' => $_paybankname,
		'{{사이트명}}' => $row_setup['site_name'],
		'{{주문상품명}}' => $SMSProduct['op_pname'],
		'{{주문상품수}}' => $SMSProductCnt,
	));
	$sms_msg = $arr_sms_msg['msg'];
	$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
	//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

}

$smskbn = "virtual_adm";	// 문자 발송 유형
if($row_sms[$smskbn]['smschk'] == "y") {
	$sms_to		= $row_company['htel'];
	$sms_from	= $row_company['tel'];

	//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
	// 치환작업
	$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $_ordernum, array(
		'{{주문번호}}'     => $_ordernum,
		'{{결제금액}}' => number_format($Vorder['tPrice']),
		'{{입금계좌정보}}' => $_paybankname,
		'{{사이트명}}' => $row_setup['site_name'],
		'{{주문상품명}}' => $SMSProduct['op_pname'],
		'{{주문상품수}}' => $SMSProductCnt,
	));
	$sms_msg = $arr_sms_msg['msg'];
	$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
	//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

}
//onedaynet_sms_multisend($arr_send);
//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
onedaynet_alimtalk_multisend($arr_send);