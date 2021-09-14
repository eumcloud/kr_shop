<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

	// _type ::: up/down
	// code
	// uid
	// cnt
	if( $_type == "up" ) {
		$app_cnt = $cnt +1;
	}
	else if( $_type == "down" ){
		$app_cnt = $cnt -1;
	}



    // ----------------- 사전체크 ---------------------//
    // 필수 변수 체크
    if( !$code or !$uid or !$cnt ) {
        echo "error1"; //잘못된 접근입니다.
        exit;
    }



	// 상품정보, 옵션정보 추출
	include_once(dirname(__FILE__)."/option_select.top_inc.php");

	// 선택재고량은 0 초과여야 함
	if( $app_cnt > 0 ) {

		// 구매제한 초과
		if($r[buy_limit] > 0 && $app_cnt > $r[buy_limit]) {
			echo "error5"; // 구매제한 초과
			exit;
		}

		//현재옵션의 재고수량
		$ptores = " select otpo_pouid from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_uid ='" . $uid . "' ";
		$ptor = _MQ($ptores);
		$otpo_pouid = $ptor[otpo_pouid];

		if( $otpo_pouid && $_type != "down" ) {
				$option_stock = $arr_option_data[$otpo_pouid]['option_cnt'] - $app_cnt ; // cnt 개를 추가하므로 - cnt을 적용함
				if($option_stock < 0 ) {
					echo "error3"; //선택 옵션의 재고량이 부족합니다.
					exit;
				}
		}
		else {
			$option_type_chk = "none";
		}

		// 전체 재고 확인
		$cnt_que = " select ifnull(sum(otpo_cnt),0) as sum from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_is_addoption != 'Y' ";
		$cnt_r = _MQ($cnt_que);
		if($r[stock] < ($cnt_r[sum]+1) && $_type != "down" ) {
				echo "error4"; //재고량이 부족합니다.
				exit;
		}else {
			// 수량 업데이트
			$sque = "update odtTmpProductOption set otpo_cnt='" . $app_cnt . "' where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_uid='" . $uid . "' ";
			_MQ_noreturn($sque);
		}

	}




	// 옵션목록 적용
	include_once(dirname(__FILE__)."/option_select.bottom_inc.php");



    exit;

?>