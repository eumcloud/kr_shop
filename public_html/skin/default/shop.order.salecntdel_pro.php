<?PHP

	// *** 결제확인 시 --> 상품 수량 증가와 판매량 차감 ***

	// - 주문정보 추출 ---
	$osr = get_order_product_info($_ordernum);

	foreach($osr as $k => $v) {
		// 옵션 고유번호가 있다면..
		if($v[op_pouid]){
			if( $v[op_is_addoption] != 'Y' ) {
				// 추가옵션이 포함된 옵션인지 체크
				$add_res = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$v['op_pouid']."' and op_oordernum = '".$v['op_oordernum']."' ");
				if( count($add_res) > 0 ) {
					foreach($add_res as $adk=>$adv) {
						// 판매된 옵션 수량 차감 및 판매량 증가
						_MQ_noreturn("update odtProductAddoption set pao_salecnt = pao_salecnt - '".$adv[op_cnt]."' , pao_cnt = pao_cnt + '".$adv[op_cnt]."' where pao_uid='".$adv[op_pouid]."'");
					}
				}
				// 판매된 상품 수량 차감 및 판매량 증가
				_MQ_noreturn("update odtProduct set saleCnt = saleCnt - '".$v[op_cnt]."' ,stock = stock + '".$v[op_cnt]."' where code = '".$v[op_pcode]."'");
				// 판매된 옵션 수량 차감 및 판매량 증가
				_MQ_noreturn("update odtProductOption set oto_salecnt = oto_salecnt - '".$v[op_cnt]."' , oto_cnt = oto_cnt + '".$v[op_cnt]."' where oto_uid='".$v[op_pouid]."'");
			}
		} else {
			// 판매된 상품 수량 차감 및 판매량 증가
			_MQ_noreturn("update odtProduct set saleCnt = saleCnt - '".$v[op_cnt]."' ,stock = stock + '".$v[op_cnt]."' where code = '".$v[op_pcode]."'");
		}

	}

?>