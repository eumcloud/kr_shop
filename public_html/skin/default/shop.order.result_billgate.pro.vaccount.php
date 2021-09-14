<?
session_start();
$ordernum = $_SESSION["session_ordernum"];//주문번호
include_once(dirname(__FILE__)."/../../include/inc.php");
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

cookie_chk();

$ordernum = $ORDER_ID;

// - 결제 성공 기록정보 저장 ---
$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
if($RESPONSE_CODE) { $app_oc_content = $RESPONSE_CODE."||".$RESPONSE_MESSAGE. "§§"; }
if($DETAIL_RESPONSE_CODE) { $app_oc_content .= $DETAIL_RESPONSE_CODE."||".$DETAIL_RESPONSE_MESSAGE. "§§"; }

// 회원정보 추출
if(is_login()) $indr = $mem_info;

// 주문정보 추출
$r = _MQ("select * from odtOrder where ordernum='". $ordernum ."' ");

// - 주문결제기록 저장 ---
$que = "
	insert odtOrderCardlog set
		 oc_oordernum = '".$ordernum."'
		,oc_tid = '". $TRANSACTION_ID ."'
		,oc_content = '". $app_oc_content ."'
		,oc_rdate = now();
";
_MQ_noreturn($que);
// - 주문결제기록 저장 ---
// - 결제 성공 기록정보 저장 ---

// 현금영수증을 신청했으면 주문정보 업데이트
if(isset($AUTH_DATEIDENTIFIER)) {
	_MQ_noreturn("update odtOrder set taxorder = 'Y' where ordernum = '$ordernum'");
}

if(!strcmp($RESPONSE_CODE, "0000")) { // 인증 성공인 경우 

	$order = _MQ("select * from odtOrder as o left join odtOrderCardlog as oc on (o.ordernum = oc.oc_oordernum) where o.ordernum = '$ordernum'");
	$ool_type = 'R';
	$tno = $TRANSACTION_ID;
	$app_time = $ORDER_DATE;
	$amount = trim($AMOUNT);
	$account = $ACCOUNT_NUMBER;
	$bankcode = $BANK_CODE;
	$depositor = $order[ordername];
	_MQ_noreturn("
		insert into odtOrderOnlinelog (
		ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
		) values (
		'$ordernum', '$order[orderid]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$depositor', '$ool_bank_name_array[$bankcode]', '$bankcode', '$escw_yn', '', '$buyr_tel2', '$bank_owner'
		)
	");
	include_once($_SERVER['DOCUMENT_ROOT'].'/pages/shop.order.mail.send.virtual.php'); // 가상계좌 문자 & 메일 2016-12-16 LDD
	echo "<script language='javascript'>opener.location.href=('/?pn=shop.order.complete');window.close();</script>";

}else{ 

	_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");
	echo "<script language='javascript'>alert('결제에 실패하였습니다. 다시 한번 확인 바랍니다.');opener.location.href=('/?pn=shop.order.result');window.close();</script>";

} ?>
