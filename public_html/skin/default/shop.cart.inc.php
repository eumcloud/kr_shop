<?php

	// 선택 구매 초기화 2015-12-04 LDD
	if($_COOKIE["AuthShopCOOKIEID"] && $pn == 'shop.cart.list') _MQ_noreturn(" update odtCart set c_direct = 'N' where c_cookie = '{$_COOKIE["AuthShopCOOKIEID"]}' ");
	// 선택 구매 초기화 2015-12-04 LDD

	// JJC003 묶음배송

	// --- 장바구니 정보 추출 ---
	$arr_cart = $arr_customer = $arr_delivery = $arr_product_info = array();
	/* 추가배송비개선 - 2017-05-19::SSJ  */
	$que = "
		select 
			c.* , p.*, oto.*, pao.*,
			m.cName, m.id , m.com_delprice , m.com_delprice_free , m.com_del_company ,
			CASE c_is_addoption WHEN 'Y' THEN c_addoption_parent ELSE c_pouid END as app_pouid
			, com_del_addprice_use, com_del_addprice_use_normal, com_del_addprice_use_unit, com_del_addprice_use_free
		from odtCart as c 
		left join odtProduct as p on (p.code=c.c_pcode)
		left join odtProductOption as oto on (oto.oto_uid = c.c_pouid)
		left join odtProductAddoption as pao on (pao.pao_uid = c.c_pouid)
		left join odtMember as m on (m.id=p.customerCode and m.userType = 'C')
		where 
			c.c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
			".($pn!='shop.cart.list'?"and c.c_direct = 'Y'":"")."
		order by c_rdate asc , c_is_addoption desc
	";
	/* 추가배송비개선 - 2017-05-19::SSJ  */
	$r = _MQ_assoc($que);
	foreach( $r as $k=>$v ){

		// 장바구니 정보 저장
		foreach( $v as $sk=>$sv ){
			$arr_cart[$v['customerCode']][$v['c_pcode']][$v['c_pouid']][$sk] = $sv;
			$arr_product_info[$v['c_pcode']][$sk] = $sv;
		}

		/* 추가배송비개선 - 2017-05-19::SSJ  */
		// 입점업체 정보 저장
		$arr_customer[$v['customerCode']] = array('cName'=>$v['cName'] , 'com_delprice'=>$v['com_delprice'] , 'com_delprice_free'=>$v['com_delprice_free'] , 'com_del_company'=>$v['com_del_company'] , 'com_del_addprice_use'=>$v['com_del_addprice_use'] , 'com_del_addprice_use_normal'=>$v['com_del_addprice_use_normal'] , 'com_del_addprice_use_unit'=>$v['com_del_addprice_use_unit'] , 'com_del_addprice_use_free'=>$v['com_del_addprice_use_free']);
		/* 추가배송비개선 - 2017-05-19::SSJ  */


		// 배송비용 계산을 위한 입점업체별 주문금액합산 - 개별배송 , 무료배송일 경우 가격 포함 하지 않음.
		if( $v['del_type']=="normal" && $v['setup_delivery'] == "Y" ){ 
			$arr_delivery[$v['customerCode']] += $v['c_cnt'] * ($v['c_price'] + $v['c_optionprice']);
		}

		// 상품 형태 - 둘다 Y 인경우 both
		if($v['setup_delivery'] == "Y") { $order_type_product = "Y"; }
		if($v['setup_delivery'] != "Y") { $order_type_coupon = "Y"; }

	}
	// --- 업체별 배송비 정보 계산 ---


	// --- 업체별 배송비 처리 ---
	if(sizeof(array_filter($arr_delivery)) > 0 ) {
		foreach( array_filter($arr_delivery) as $k=>$v ){
			$arr_customer[$k]['app_delivery_price'] = 0; //무료배송
			if($arr_customer[$k]['com_delprice_free'] > 0) {
				$arr_customer[$k]['app_delivery_price'] = ($arr_customer[$k]['com_delprice_free'] > $v ? $arr_customer[$k]['com_delprice'] : 0 ); // 배송비적용
			}
			else {// 0일 경우 배송비 무조건 적용
				$arr_customer[$k]['app_delivery_price'] = $arr_customer[$k]['com_delprice'];//배송비적용
			}
		}
	}
	// --- 업체별 배송비 처리 ---


?>