<?
include_once(dirname(__FILE__)."/../../include/inc.php");

// 결제 결과 처리는 DB처리페이지 (shop.order.result_daupay.pro.php) 에서 처리한다.

$que = "select * from odtOrder where ordernum='". $ordernum ."'";
$r = _MQ($que);

if($r[paymethod] == "C" || $r[paymethod] == "L") {

	if($r[paystatus] == "Y")
		error_loc_nomsgPopup("/?pn=shop.order.complete");
	else
		error_loc_msgPopup("/","결제는 정상적으로 되었으나, 일시적인 오류가 발생하였습니다. 관리자에게 문의하세요.");

} else if($r[paymethod] == "V") {

	error_loc_nomsgPopup("/?pn=shop.order.complete");

}
?>