<?PHP

	// 필수 변수
	// $_ordernum , $_uid
	if(!$ordr){
		$ordr = _MQ("
			SELECT * FROM odtOrderProduct as op 
			left join odtOrder as o on (o.ordernum = op.op_oordernum) 
			WHERE ordernum='" . $_ordernum . "' and op_uid = '".$_uid."'
		");
	}

	// 지급한 포인트가 있다면 회수 처리한다
	if( $ordr['member_type']=="member" && $ordr['op_point'] > 0 ) { 

		// 지급된 포인트 정보 추출
		$res_pl = _MQ(" select * from odtPointLog where pointID='".$ordr['orderid']."' and pointTitle = '구매 적립금 적용 (주문번호 : {$_ordernum})' ");
		
		// 지급된 포인트 회수
		shop_pointlog_delete( $ordr['orderid'] , "구매 적립금 적용 (주문번호 : {$_ordernum})" );

		// 나머지 적립금 재지급
		$new_point = ($res_pl["pointPoint"]-$ordr['op_point']);
		if($new_point > 0){
			// 적립금 지급일을 기존의 지급일과 동일하게 조정
			$datetime1 = strtotime($res_pl['redRegidate']);
			$datetime2 = strtotime(date('Y-m-d'));
			$interval = ($datetime1 - $datetime2) / (24 * 60 * 60);
			shop_pointlog_insert( $ordr['orderid'] , "구매 적립금 적용 (주문번호 : {$_ordernum})" , $new_point , "N" , $interval);
		}

	}
