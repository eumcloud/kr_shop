<?PHP

	// -- 주문상품의 op_usepoint 적용 : 할인액이 있을 경우에만 적용 ---
	if($_price_sale_total) {
		// --- 상품쿠폰사용여부 체크 ---
		$arr_odtOrderCouponLog = array();
		$arr_odtOrderCouponLogMember = array();
		$clres = _MQ_assoc("select * from odtOrderCouponLog where cl_oordernum = '". $_ordernum ."' and cl_type = 'product' ");
		foreach($clres as $sk=>$sv){
			$arr_odtOrderCouponLog[$sv[cl_pcode]] = $sv[cl_price];
		}
		// --- 상품쿠폰사용여부 체크 ---

		// 쿠폰사용총합
		$app_coupon_price_sum = array_sum(array_values($arr_odtOrderCouponLog));

		// 총구매상품가격
		$opres = _MQ("select IFNULL(sum((op_pprice + op_poptionprice) * op_cnt),0) as op_sum from odtOrderProduct where op_oordernum = '". $_ordernum ."' ");
		$app_opsum = $opres[op_sum];

		// --- 사용포인트적용 - 개별적용 ---
		$sum_usepoint = 0;
		$opres = _MQ_assoc("select * from odtOrderProduct where op_oordernum = '". $_ordernum ."' ");
		foreach($opres as $sk=>$sv){
			// 상품가격 * 갯수 * (총할인액 - 총상품쿠폰사용액) / 총구매상품가격 + 상품쿠폰사용액
			$app_usepoint = round( ($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt] * ($_price_sale_total - $app_coupon_price_sum)   / $app_opsum) + $arr_odtOrderCouponLog[$sv[op_pcode]];
			$sque = " update odtOrderProduct set op_usepoint = '". $app_usepoint ."' where op_uid='". $sv[op_uid] ."'";
			_MQ_noreturn($sque);
			$arr_odtOrderCouponLog[$sv[op_pcode]] = 0; // 한번 적용한 상품쿠폰가격 초기화
			$sum_usepoint += $app_usepoint;
		}
		// --- 사용포인트적용 - 개별적용 ---

		// 총할인액 - 적용한 사용포인트 차액 있을 경우 처리
		if( $_price_sale_total  <> $sum_usepoint ) {
			$opres = _MQ("select * from odtOrderProduct where op_oordernum = '". $_ordernum ."' order by op_usepoint desc limit 1  ");
			$app_price = $_price_sale_total - $sum_usepoint;
			if( $app_price > 0 ) {
				_MQ_noreturn(" update odtOrderProduct set op_usepoint = op_usepoint + " . $app_price  ." where op_uid='". $opres[op_uid] ."' ");
			} 
			else {
				_MQ_noreturn(" update odtOrderProduct set op_usepoint = op_usepoint - " . abs($app_price)  ." where op_uid='". $opres[op_uid] ."' ");
			}
		}
	}
	// -- 주문상품의 op_usepoint 적용 ---

?>