<?php
include_once(dirname(__FILE__).'/../../include/inc.php');

// 사용안함 처리시 실행하지 않도록 처리
if($row_setup['s_product_auto_on'] == 'N') return;
/*
$row_setup['s_product_auto_C']
$row_setup['s_product_auto_L']
$row_setup['s_product_auto_B']
$row_setup['s_product_auto_G']
$row_setup['s_product_auto_V']

$row_setup['s_coupon_auto_C']
$row_setup['s_coupon_auto_L']
$row_setup['s_coupon_auto_B']
$row_setup['s_coupon_auto_G']
$row_setup['s_coupon_auto_V']
*/

//echo '배송상품<br>';
// 조건 데이터 호출 (상품)
$_queP = "
		select
			op.op_uid, op.op_oordernum, op.op_expressdate
		from
			`odtOrderProduct` as op left join
			`odtOrder` as o on(op.op_oordernum = o.ordernum)
		where
			op.op_orderproduct_type = 'product' and
			op.op_cancel = 'N' and
			op.op_settlementstatus not in ('ready', 'complete') and
			orderstatus_step='발송완료' and
			timestamp(op.op_expressdate) > 0 and
			DATE_ADD(op.op_expressdate , INTERVAL 
				(
					case o.paymethod 
					when 'C' then " . $row_setup['s_product_auto_C'] . "
					when 'L' then " . $row_setup['s_product_auto_L'] . "
					when 'B' then " . $row_setup['s_product_auto_B'] . "
					when 'G' then " . $row_setup['s_product_auto_G'] . "
					when 'V' then " . $row_setup['s_product_auto_V'] . "
					end 
				)
			day) <= CURDATE()
		";
$AutoOrderProduct = _MQ_assoc($_queP);
foreach($AutoOrderProduct as $k=>$v) {

	_MQ_noreturn(" update `odtOrderProduct` set `op_settlementstatus` = 'ready' where op_uid = '{$v['op_uid']}' ");
	/*
	echo " update `odtOrderProduct` set `op_settlementstatus` = 'ready' where op_uid = '{$v['op_uid']}' "
	.' / 배송일 : '.$v['op_expressdate']
	.' / 주문코드 : '.$v['op_oordernum']
	.'<br>';
	*/
}
//echo '<hr>쿠폰상품<br>';

$_queC = "
		select
			op.op_uid, op.op_oordernum, op.op_expressdate
		from
			`odtOrderProduct` as op left join
			`odtOrder` as o on(op.op_oordernum = o.ordernum)
		where
			op.op_orderproduct_type != 'product' and
			op.op_cancel = 'N' and
			op.op_settlementstatus not in ('ready', 'complete') and
			o.orderstatus_step='발급완료' and
			timestamp(op.op_coupon_use) > 0 and
			DATE_ADD(op.op_coupon_use , INTERVAL 
				(
					case o.paymethod 
					when 'C' then " . $row_setup['s_coupon_auto_C'] . "
					when 'L' then " . $row_setup['s_coupon_auto_L'] . "
					when 'B' then " . $row_setup['s_coupon_auto_B'] . "
					when 'G' then " . $row_setup['s_coupon_auto_G'] . "
					when 'V' then " . $row_setup['s_coupon_auto_V'] . "
					end 
				)
			day) <= CURDATE()
		";
$AutoOrdercoupon = _MQ_assoc($_queC);
foreach($AutoOrdercoupon as $k=>$v) {

	_MQ_noreturn(" update `odtOrderProduct` set `op_settlementstatus` = 'ready' where `op_uid` = '{$v['op_uid']}' ");
	/*
	echo " update `odtOrderProduct` set `op_settlementstatus` = 'ready' where `op_uid` = '{$v['op_uid']}' "
	.' / 쿠폰사용일 : '.$v['op_expressdate']
	.' / 주문코드 : '.$v['op_oordernum']
	.'<br>';
	*/
}

//echo '<hr>총 처리 될 개수: '.(count($AutoOrderProduct)+count($AutoOrdercoupon)).'개';