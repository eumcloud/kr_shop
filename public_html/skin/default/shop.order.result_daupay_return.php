<?
include_once(dirname(__FILE__)."/../../include/inc.php");

// ���� ��� ó���� DBó�������� (shop.order.result_daupay.pro.php) ���� ó���Ѵ�.

$que = "select * from odtOrder where ordernum='". $ordernum ."'";
$r = _MQ($que);

if($r[paymethod] == "C" || $r[paymethod] == "L") {

	if($r[paystatus] == "Y")
		error_loc_nomsgPopup("/?pn=shop.order.complete");
	else
		error_loc_msgPopup("/","������ ���������� �Ǿ�����, �Ͻ����� ������ �߻��Ͽ����ϴ�. �����ڿ��� �����ϼ���.");

} else if($r[paymethod] == "V") {

	error_loc_nomsgPopup("/?pn=shop.order.complete");

}
?>