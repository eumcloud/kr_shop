<?PHP

	// *** 결제확인 시 --> 상품 수량 차감 과 판매량 증가 ***

	// - 주문정보 추출 ---
	$osr = get_order_product_info($_ordernum);

	foreach($osr as $k => $v) {

		// 옵션 고유번호가 있다면..
		if($v[op_pouid]){
			// 일반옵션인지 추가옵션인지 구분하여 처리
			if($v[op_is_addoption]=="Y"){
				// 판매된 옵션 수량 차감 및 판매량 증가
				_MQ_noreturn("update odtProductAddoption set pao_salecnt = pao_salecnt + '".$v[op_cnt]."' , pao_cnt = pao_cnt - '".$v[op_cnt]."' where pao_uid='".$v[op_pouid]."'");	
			}else{
				// 판매된 상품 수량 차감 및 판매량 증가
				_MQ_noreturn("update odtProduct set saleCnt = saleCnt + '".$v[op_cnt]."' ,stock = stock - '".$v[op_cnt]."' where code = '".$v[op_pcode]."'");
				// 판매된 옵션 수량 차감 및 판매량 증가
				_MQ_noreturn("update odtProductOption set oto_salecnt = oto_salecnt + '".$v[op_cnt]."' , oto_cnt = oto_cnt - '".$v[op_cnt]."' where oto_uid='".$v[op_pouid]."'");
			}
		}else{
			// 판매된 상품 수량 차감 및 판매량 증가
			_MQ_noreturn("update odtProduct set saleCnt = saleCnt + '".$v[op_cnt]."' ,stock = stock - '".$v[op_cnt]."' where code = '".$v[op_pcode]."'");
		}

	}

?>

