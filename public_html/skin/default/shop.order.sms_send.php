<?
	// *** 결제확인 시 --> 문자발송 ***

	// - 주문정보 추출 ---
	$osr = get_order_info($_ordernum);

	$app_pname = ""; // 상품명 설정
	$ospr = get_order_product_info($_ordernum);
	foreach($ospr as $k=>$v){
		$app_pname = ( $app_name ? $app_name : $v[op_pname] );
	}
	$app_pcnt = sizeof($ospr);

	/*-------------- 문자 발송 ---------------*/
	$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
	$smskbn = "order_mem";	// 문자 발송 유형
	if($row_sms[$smskbn][smschk] == "y") {
		$sms_to		= phone_print($osr[orderhtel1],$osr[orderhtel2],$osr[orderhtel3]);
		$sms_from	= $row_company[tel];

		//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		// 치환작업
		$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $osr['ordernum'], array(
			'{{주문번호}}'     => $osr['ordernum'],
			'{{결제금액}}' => number_format($osr['tPrice']),
			'{{구매자명}}' => $osr['ordername'],
			'{{사이트명}}' => $row_setup['site_name'],
			'{{주문상품명}}' => $app_pname,
			'{{주문상품수}}' => $app_pcnt,
		));
		$sms_msg = $arr_sms_msg['msg'];
		$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
		//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

	}

	$smskbn = "order_adm";	// 문자 발송 유형
	if($row_sms[$smskbn][smschk] == "y") {
		$sms_to		= $row_company[htel];
		$sms_from	= $row_company[tel];

		//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		// 치환작업
		$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $osr['ordernum']);
		$sms_msg = $arr_sms_msg['msg'];
		$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
		//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

	}
	//onedaynet_sms_multisend($arr_send);
	//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
	onedaynet_alimtalk_multisend($arr_send);
	/*-------------- // 문자 발송 ---------------*/
?>