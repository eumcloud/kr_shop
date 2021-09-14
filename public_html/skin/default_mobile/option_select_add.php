<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

//    $code = $_POST[code];
//    $uid1 = $_POST[uid1];
    if($uid1 == "undefined") {
        $uid1 = "";
    }
//    $uid2 = $_POST[uid2];
    if($uid2 == "undefined") {
        $uid2 = "";
    }
//    $uid3 = $_POST[uid3];
    if($uid3 == "undefined") {
        $uid3 = "";
    }


	$pque = "select option_type_chk from odtProduct where code='". $code ."' ";
    
	$pr = _MQ($pque);
	$option_type_chk = $pr[option_type_chk];
	switch($option_type_chk){
		case "1depth": $app_uid = $uid1; break;
		case "2depth": $app_uid = $uid2; break;
		case "3depth": $app_uid = $uid3; break;
	}


    // LCY : 2021-05-26 : 추가 -- 만약 다른상품의 odtTmpProductOption 데이터가 있다면 삭제한다. --
    _MQ_noreturn("DELETE FROM odtTmpProductOption WHERE otpo_pcode != '".$code."' and otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."'  ");


    // ----------------- 사전체크 ---------------------//
    // 필수 변수 체크
    if( !$code || (!$uid1 && !$uid2 && !$uid3) ) {
        echo "error1"; //잘못된 접근입니다.
        exit;
    }

    // 넘어온 정보의 중복체크
    $cntr = _MQ(" select count(*) as cnt from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_pouid='" . $app_uid . "' and otpo_is_addoption != 'Y' ");
    if($cntr[cnt] > 0 ) {
        echo "error2"; //이미 선택한 옵션입니다.
        exit;
    }
    // ----------------- 사전체크 ---------------------//





    // 상품정보, 옵션정보 추출
    include_once(dirname(__FILE__)."/option_select.top_inc.php");




    //현재옵션의 재고수량 -- 옵션이 있을 경우에만
    if( $app_uid ) {
        $option_stock = $arr_option_data[$app_uid]['option_cnt'] - 1; // 한개를 추가하므로 - 1을 적용함
        if($option_stock < 0 ) {
            echo "error3"; //선택 옵션의 재고량이 부족합니다.
            exit;
        }
    }



   // 전체 재고 확인
	$cnt_que = " select ifnull(sum(otpo_cnt),0) as sum from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_is_addoption != 'Y' ";
	$cnt_r = _MQ($cnt_que);
	if($r[stock] < ($cnt_r[sum]+1)) {
		echo "error4"; //재고량이 부족합니다.
		exit;
	}else {
		// 넘어온 정보 추가
		$sque = "
			insert odtTmpProductOption set 
				otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."',
				otpo_pouid = '". $app_uid ."',
				otpo_pcode='" . $code . "',
				otpo_cnt=1,
				otpo_poptionname ='".$arr_option_data[$app_uid]['option_name1']."',
				otpo_poptionname2 ='".$arr_option_data[$app_uid]['option_name2']."',
				otpo_poptionname3 ='".$arr_option_data[$app_uid]['option_name3']."',
				otpo_pprice ='". $r[price] ."',
				otpo_ppurprice ='". $r[purPrice] ."',
				otpo_poptionpurprice ='".$arr_option_data[$app_uid]['option_supplyprice']."',
				otpo_poptionprice ='".$arr_option_data[$app_uid]['option_price']."'
		";
				//otpo_poptionprice ='".($arr_option_data[$app_uid]['option_price']+$r[price])."' // 옵션가격 추가형 => 옵션가격 비추가형
		_MQ_noreturn($sque);
	}
            //otpo_poptionprice ='".($arr_option_data[$app_uid]['option_price']+$r[price])."' // 옵션가격 추가형 => 옵션가격 비추가형


    // 옵션목록 적용
    include_once(dirname(__FILE__)."/option_select.bottom_inc.php");



    exit;

?>