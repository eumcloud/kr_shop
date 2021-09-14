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
	if( !$code or !$uid ) {
			echo "error1"; //잘못된 접근입니다.
			exit;
	}
	// 상품정보, 옵션정보 추출
	include_once(dirname(__FILE__)."/option_select.top_inc.php");


	$otpo_pouid =  $uid;

	$option_stock = $arr_option_data[$otpo_pouid]['option_cnt'] - $app_cnt ; // cnt 개를 추가하므로 - cnt을 적용함
	if($option_stock < 0 ) {
		echo "error3"; //선택 옵션의 재고량이 부족합니다.
		exit;
	}

	if($app_cnt < 1) {
		$sque = "delete from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_pouid='" . $otpo_pouid . "' ";
		_MQ_noreturn($sque);
	} else {

		$ptores = " select count(*) as cnt from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_pouid ='" . $otpo_pouid . "' ";
		$ptor = _MQ($ptores);

		if( $ptor[cnt] ) {	 // 이미 입력된 값이면 수정

			 $sque = "update odtTmpProductOption set otpo_cnt='" . $app_cnt . "' where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_pouid='" . $otpo_pouid . "' ";
			_MQ_noreturn($sque);

		} else {

			$sque = "insert into odtTmpProductOption set 
							otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."',
							otpo_pouid = '". $otpo_pouid ."',
							otpo_pcode='" . $code . "',
							otpo_cnt='".$app_cnt."',
							otpo_poptionname ='".$arr_option_data[$otpo_pouid]['option_name1']."',
							otpo_poptionname2 ='".$arr_option_data[$otpo_pouid]['option_name2']."',
							otpo_poptionname3 ='".$arr_option_data[$otpo_pouid]['option_name3']."',
							otpo_pprice ='". $r[price] ."',
							otpo_ppurprice ='". $r[purPrice] ."',
							otpo_poptionpurprice ='".$arr_option_data[$app_uid]['option_supplyprice']."',
							otpo_poptionprice ='".$arr_option_data[$app_uid]['option_price']."'
			";
			_MQ_noreturn($sque);

		}

	}

	$ptores = " select sum(otpo_cnt*(otpo_pprice + otpo_poptionprice)) as total_price from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_pcode ='" . $code . "' ";
	$ptor = _MQ($ptores);
	echo number_format($ptor[total_price]);	// 옵션 합계 금액 

?>



