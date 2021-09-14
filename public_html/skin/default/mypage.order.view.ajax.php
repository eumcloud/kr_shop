<? //LMH001

	include_once dirname(__FILE__)."/../../include/inc.php";

	function return_json() {
		GLOBAL $__result, $__result_text, $__result_array;
		echo json_encode(
			array(
				"result"=>$__result,
				"result_text"=>$__result_text,
				"data"=>$__result_array
			)
		);
		exit;
	}

	$__result = 'OK'; $__result_text = ''; $__result_array = array();

	if(!$ordernum || !$op_uid || !$mode) {
		$__result = 'FAIL'; $__result_text = '잘못된 접근입니다.'; return_json();
	}

	switch($mode) {

		case 'product': // 상품정보
			# 2016-08-24 :: op.op_partnerCode, p.del_type 추가
		// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------
			$res = _MQ("
				select
					o.*, p.code, p.prolist_img, p.name, op.op_pprice, op.op_delivery_price, op.op_add_delivery_price, op.op_cnt, op.op_poptionprice,
					concat(op.op_option1,' ',op.op_option2,' ',op.op_option3) as option_name, op.op_orderproduct_type, op.op_pouid, op.op_partnerCode, p.del_type
				from odtOrderProduct as op
				left join odtProduct as p on (op.op_pcode = p.code)
				left join odtOrder as o on (o.ordernum = op.op_oordernum)
				where op_oordernum = '".$ordernum."' and op_uid = '".$op_uid."'
				");

			if($res['op_orderproduct_type']=="coupon") {
				$coupon_cnt = _MQ_result("select count(*) from odtOrderProductCoupon where opc_opuid = '".$op_uid."' and opc_status != '대기' ");
				if( $coupon_cnt > 0 ) { $__result = "FAIL"; $__result_text = "사용/취소된 쿠폰이 있으므로 취소할 수 없습니다. 고객센터(".$row_company['tel'].")로 문의하세요."; return_json(); }
			}


			$av_check = true;
			if( $row_setup['P_KBN']=='I' ) { // 이니시스일 경우 한 주문당 최대 9회 부분취소 가능
				$av_cnt = _MQ(" select count(*) as cnt from odtOrderProduct where op_cancel != 'N' and op_cancel_type = 'pg' and op_oordernum = '".$ordernum."' ");
				if( $av_cnt['cnt'] > 8 ) { $av_check = false; }
			}
			if( $res['paymethod'] == 'G' ) { $av_check = false; }




			//추가옵션이 있다면 함께 출력
			unset($_addoption_price,$_addoption_cnt); $_addoption_name = array();
			$tmp = _MQ_assoc("
				select *, concat(op_option1,' ',op_option2,' ',op_option3) as option_name from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$res[op_pouid]."' and op_oordernum = '".$ordernum."'
				");
			foreach($tmp as $tk=>$tv) {
				$_addoption_price += ($tv['op_pprice'] + $tv['op_poptionprice']) * $tv['op_cnt'] + $tv['op_delivery_price'] + $tv['op_add_delivery_price'];
				$_addoption_name[] = $tv['option_name'];
				$_addoption_cnt += $tv['op_cnt'];
			}
			$_addoption_name = implode('<br/>추가옵션: ',$_addoption_name);

	    $chk_product_cnt = _MQ("
				select count(*) as cnt from odtOrderProduct
				where
					op_oordernum = '".$ordernum."' and
					op_uid != '".$op_uid."' and
					op_pcode = '". $res['code'] ."' and
					op_cancel = 'N' and
					op_is_addoption !='Y'
			");

			if($chk_product_cnt['cnt'] == 0 ) {
				$cl_r = _MQ(" select cl_price from odtOrderCouponLog where cl_type = 'product' and cl_oordernum = '". $ordernum ."' and cl_pcode = '". $res['code'] ."' ");
				$app_discount = $cl_r['cl_price'];
			}

			// 2016-11-30 ::: 부분취소 - 선택주문상품이외에 정상적인 주문상품이 없는 경우 - 전체 취소과정 -> 할인액 추가 ::: JJC --------------------
			$use_product_cnt = _MQ(" select count(*) as cnt from odtOrderProduct where op_cancel = 'N' and op_oordernum = '".$ordernum."' and op_uid != '".$op_uid."' ");
			if($use_product_cnt['cnt'] == 0 ) {
				$app_discount += ($res['paymethod'] <> "G" ? $res['gPrice'] : 0);// 포인트 사용액 - 취소시 포인트 별도 추가됨
				$app_discount += $res['o_promotion_price'];// 프로모션 사용액

				# -- 사용자쿠폰 사용액 추가
				$cl_r = _MQ("select cl_price from odtOrderCouponLog where cl_type = 'member' and cl_oordernum = '". $ordernum ."' and cl_pcode = '". $res['code'] ."' ");
				$app_discount += $cl_r['cl_price'];
				// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------

			}
			// 2016-11-30 ::: 부분취소 - 선택주문상품이외에 정상적인 주문상품이 없는 경우 - 전체 취소과정 -> 할인액 추가 ::: JJC --------------------


			$app_delivery =  $res['op_delivery_price'] + $res['op_add_delivery_price'];

			$__result_array = array(
				'pcode' => $res['code'],
				'image' => replace_image(IMG_DIR_PRODUCT.$res['prolist_img']),
				'name' => $res['name'],
				'option' => trim($res['option_name']),
				'addoption' => trim($_addoption_name),
				'price' => number_format(($res['op_pprice'] + $res['op_poptionprice']) * $res['op_cnt'] + $res['op_delivery_price'] + $res['op_add_delivery_price'] + $_addoption_price),
				'cnt' => number_format($res['op_cnt'] + $_addoption_cnt),
				'pg_check' => $av_check===true?'Y':'N',
				'delivery' => number_format($app_delivery), // 2016-05-24 추가
				'discount' => number_format($app_discount), // 할인비용 - // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
				'return' => number_format(($res['op_pprice'] + $res['op_poptionprice']) * $res['op_cnt'] + $_addoption_price + $app_delivery - $app_discount), // 2016-05-24 추가  - // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
				);
			return_json();
		break;

		case 'view': // 상품정보

			$res = _MQ("
				select
					p.code, p.prolist_img, p.name, op.op_pprice, op.op_delivery_price, op.op_add_delivery_price, op.op_cnt, op.op_poptionprice,
					concat(op.op_option1,' ',op.op_option2,' ',op.op_option3) as option_name, op.op_pouid,
					op.op_cancel_rdate, op.op_cancel_msg, op.op_cancel_bank, op.op_cancel_bank_account, op.op_cancel_bank_name, op.op_cancel_type
					, op.op_cancel_discount_price
				from odtOrderProduct as op
				left join odtProduct as p on (op.op_pcode = p.code)
				where op_oordernum = '".$ordernum."' and op_uid = '".$op_uid."'
				");

			//추가옵션이 있다면 함께 출력
			unset($_addoption_price,$_addoption_cnt); $_addoption_name = array();
			$tmp = _MQ_assoc("
				select *, concat(op_option1,' ',op_option2,' ',op_option3) as option_name from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$res['op_pouid']."' and op_oordernum = '".$ordernum."'
				");
			foreach($tmp as $tk=>$tv) {
				$_addoption_price += ($tv['op_pprice'] + $tv['op_poptionprice']) * $tv['op_cnt'] + $tv['op_delivery_price'] + $tv['op_add_delivery_price'];
				$_addoption_name[] = $tv['option_name'];
				$_addoption_cnt += $tv['op_cnt'];
			}
			$_addoption_name = implode('<br/>추가옵션: ',$_addoption_name);

			$__result_array = array(
				'pcode' => $res['code'],
				'image' => replace_image(IMG_DIR_PRODUCT.$res['prolist_img']),
				'name' => $res['name'],
				'option' => trim($res['option_name']),
				'addoption' => trim($_addoption_name),
				'price' => number_format(($res['op_pprice'] + $res['op_poptionprice']) * $res['op_cnt'] + $res['op_delivery_price'] + $res['op_add_delivery_price'] + $_addoption_price),

				'delivery' => number_format($res['op_delivery_price'] + $res['op_add_delivery_price']), // 2016-05-24 추가
				'discount' => number_format($res['op_cancel_discount_price']), // 할인비용 - // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
				'return' => number_format(($res['op_pprice'] + $res['op_poptionprice']) * $res['op_cnt'] + $res['op_delivery_price'] + $res['op_add_delivery_price'] + $_addoption_price - $res['op_cancel_discount_price'] ), // 2016-05-24 추가  // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC

				'cnt' => number_format($res['op_cnt'] + $_addoption_cnt),
				'msg' => $res['op_cancel_msg'],
				'date' => date('Y년 m월 d일',strtotime($res['op_cancel_rdate'])),
				'bank' => $ksnet_bank[$res['op_cancel_bank']],
				'bank_account' => $res['op_cancel_bank_account'],
				'bank_name' => $res['op_cancel_bank_name'],
				'cancel_type' => $res['op_cancel_type']
				);
			return_json();
		break;

		case 'cancel':

			$chk = _MQ("
				select op.*, o.*, p.del_type, o.orderid, o.paymethod from odtOrderProduct as op
				left join odtProduct as p on (op.op_pcode = p.code)
				left join odtOrder as o on (o.ordernum = op.op_oordernum)
				where op_oordernum = '".$ordernum."' and op_uid = '".$op_uid."'
			");
			if($chk['op_orderproduct_type']=="coupon") {
				$coupon_cnt = _MQ_result("select count(*) from odtOrderProductCoupon where opc_opuid = '".$op_uid."' and opc_status != '대기' ");
				if( $coupon_cnt > 0 ) { echo "사용/취소된 쿠폰이 있으므로 취소할 수 없습니다. 관리자에게 문의하세요."; exit; }
			}

			if($chk['op_cancel']!='N') { echo "취소요청할 수 없는 주문입니다."; exit; }
			if( !in_array($chk['paymethod'],array('C','G')) && (!$cancel_bank || !$cancel_bank_name || !$cancel_bank_account) && $cancel_type=='pg' ) { echo "환불계좌정보를 입력하세요."; exit; }

			if($save_myinfo=='Y') {
				_MQ_noreturn(" update odtMember set
					cancel_bank = '".$cancel_bank."',
					cancel_bank_name = '".$cancel_bank_name."',
					cancel_bank_account = '".$cancel_bank_account."'
					where id = '".$chk['orderid']."'
				");
			}


			# 배송비 처리
			if($chk['op_delivery_price'] > 0 && $chk['del_type'] == 'normal' ){ // 부분취소 할려는 배송상품의 배송타입이 입점이라면

				# 주문번호가 일치하고, 옵션상품 번호가 해당번호가아니고, 업체가 같고, 배송조건이 입점형이고, 추가옵션이 아니고, 취소가 아닌 주문상품을 가져온다.
				$_chk = _MQ("SELECT op.op_uid FROM odtOrderProduct AS op
								LEFT JOIN odtProduct AS p ON (p.code = op.op_pcode)
								WHERE op.op_oordernum = '".$ordernum."' AND op.op_uid != '".$op_uid."'
								AND op.op_partnerCode = '".$chk['op_partnerCode']."' AND p.del_type = 'normal'
								AND op_is_addoption = 'N' AND op.op_cancel = 'N'
				");

				if(count($_chk) > 0) { // 입점업체가 있다면
					# 현재 주문상품의 배송비를 0원으로
					_MQ_noreturn("UPDATE odtOrderProduct SET op_delivery_price = '0' WHERE op_uid = '".$op_uid."' AND op_oordernum = '".$ordernum."'    ");
					# 찾은 업체중 하나의 주문상품의 배송비를 기존의 배송비로 적용
					_MQ_noreturn("UPDATE odtOrderProduct SET op_delivery_price = '".$chk['op_delivery_price']."' WHERE op_uid = '".$_chk['op_uid']."' AND op_oordernum = '".$ordernum."'    ");
				}


			}

	    $chk_product_cnt = _MQ("
				select count(*) as cnt from odtOrderProduct
				where
					op_oordernum = '".$ordernum."' and
					op_uid != '".$op_uid."' and
					op_pcode = '". $chk['op_pcode'] ."' and
					op_cancel = 'N' and
					op_is_addoption !='Y'
			");

			if($chk_product_cnt['cnt'] == 0 ) {
				$cl_r = _MQ(" select cl_price from odtOrderCouponLog where cl_type = 'product' and cl_oordernum = '". $ordernum ."' and cl_pcode = '". $chk['op_pcode'] ."' ");
				$app_discount = $cl_r['cl_price'];
				$a=1;
			}

			$use_product_cnt = _MQ(" select count(*) as cnt from odtOrderProduct where op_cancel = 'N' and op_oordernum = '".$ordernum."' and op_uid != '".$op_uid."' ");
			if($use_product_cnt['cnt'] == 0 ) {

				# -- 사용자쿠폰 사용액 추가
				$cl_r = _MQ("select cl_price from odtOrderCouponLog where cl_type = 'member' and cl_oordernum = '". $ordernum ."' and cl_pcode = '". $chk['op_pcode'] ."' ");
				$app_discount += $cl_r['cl_price'];

				$app_discount += ($chk['paymethod'] <> "G" ? $chk['gPrice'] : 0);// 포인트 사용액 - 취소시 포인트 별도 추가됨
				$app_discount += $chk['o_promotion_price'];// 프로모션 사용액
			}



			if($app_discount > 0 ) {
				$add_que .= " op_cancel_discount_price = '". $app_discount ."' , "; // 추가 query
			}
			// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------

			//추가옵션이 있다면 함께 취소
			$tmp = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$chk['op_pouid']."' and op_oordernum = '".$ordernum."' ");
			if( count($tmp)>0 ) {
				foreach($tmp as $tk=>$tv) {
					_MQ_noreturn(" update odtOrderProduct set
						" . $add_que . "
						op_cancel = 'R',
						op_cancel_msg = '".$cancel_msg."',
						op_cancel_bank = '".$cancel_bank."',
						op_cancel_bank_name = '".$cancel_bank_name."',
						op_cancel_bank_account = '".$cancel_bank_account."',
						op_cancel_rdate = now(),
						op_cancel_type = '".$cancel_type."',
						op_cancel_mem_type = '".$cancel_mem_type."'
						where op_oordernum = '".$ordernum."' and op_uid = '".$tv['op_uid']."'
					");
				}
			}

			_MQ_noreturn(" update odtOrderProduct set
			" . $add_que . "
				op_cancel = 'R',
				op_cancel_msg = '".$cancel_msg."',
				op_cancel_bank = '".$cancel_bank."',
				op_cancel_bank_name = '".$cancel_bank_name."',
				op_cancel_bank_account = '".$cancel_bank_account."',
				op_cancel_rdate = now(),
				op_cancel_type = '".$cancel_type."',
				op_cancel_mem_type = '".$cancel_mem_type."'
				where op_oordernum = '".$ordernum."' and op_uid = '".$op_uid."'
			");

			echo "OK";
		break;

	}

?>