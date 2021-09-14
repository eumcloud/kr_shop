<?PHP

	// *** 주문취소 시 --> 티켓발행 취소 ***

	// - 주문정보 추출 ---
	$op_assoc = get_order_product_info($_ordernum);

	foreach($op_assoc as $op_key => $op_row) {

		if($op_row[op_orderproduct_type] == "coupon") {
			// 티켓발급을 취소.
			_MQ_noreturn("update odtOrderProductCoupon set opc_status='취소' where opc_opuid = '".$op_row[op_uid]."'");

			// 상태를 배송대기로 변경
			_MQ_noreturn("update odtOrderProduct set op_delivstatus = 'N' where op_uid = '".$op_row[op_uid]."'");			
		}

	}

?>