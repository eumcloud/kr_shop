<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

	$option_type_chk = $pr[option_type_chk];

    // ----------------- 사전체크 ---------------------//
    // 필수 변수 체크
    if( !$code || !$uid) {
        echo "error1"; //잘못된 접근입니다.
        exit;
    }

	$app_uid = $uid;

	// 넘어온 정보의 중복체크
    $cntr = _MQ(" select count(*) as cnt from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_pouid='" . $app_uid . "' and otpo_is_addoption = 'Y' ");
    if($cntr[cnt] > 0 ) {
        echo "error2"; //이미 선택한 옵션입니다.
        exit;
    }

	// 선택옵션이 1개이상 선택되었는지 체크
	$cntr2 = _MQ(" select count(*) as cnt from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."'  and otpo_is_addoption != 'Y' ");
	if($cntr2[cnt] <= 0 ) {
        echo "error6"; // 상세옵션을 먼저 선택해 주시기 바랍니다.
        exit;
    }
    // ----------------- 사전체크 ---------------------//





    // 상품정보, 옵션정보 추출
    include_once(dirname(__FILE__)."/add_option_select.top_inc.php");




    //현재옵션의 재고수량 -- 옵션이 있을 경우에만
    if( $app_uid ) {
        $option_stock = $arr_option_data[$app_uid]['option_cnt'] - 1; // 한개를 추가하므로 - 1을 적용함
        if($option_stock < 0 ) {
            echo "error3"; //선택 옵션의 재고량이 부족합니다.
            exit;
        }
    }

	// 부모 pouid
	$_addoption_parent = _MQ_result(" select otpo_pouid from odtTmpProductOption where otpo_is_addoption != 'Y' and otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_pcode = '".$code."' order by otpo_uid desc limit 1 ");


    // 넘어온 정보 추가 LMH002 (otpo_addoption_parent 추가)
    $sque = "
        insert odtTmpProductOption set 
            otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."',
			otpo_pouid = '". $app_uid ."',
            otpo_pcode='" . $code . "',
            otpo_cnt=1,
            otpo_poptionname ='".$arr_option_data[$app_uid]['option_name1']."',
			otpo_poptionname2 ='".$arr_option_data[$app_uid]['option_name2']."',
			otpo_poptionname3 ='".$arr_option_data[$app_uid]['option_name3']."',
            otpo_pprice ='0',
            otpo_ppurprice ='0',
			otpo_poptionpurprice ='".$arr_option_data[$app_uid]['option_supplyprice']."',
            otpo_poptionprice ='".$arr_option_data[$app_uid]['option_price']."',
			otpo_is_addoption = 'Y',
			otpo_addoption_parent = '".$_addoption_parent."'
    ";
            //otpo_poptionprice ='".($arr_option_data[$app_uid]['option_price']+$r[price])."' // 옵션가격 추가형 => 옵션가격 비추가형
    _MQ_noreturn($sque);



    // 옵션목록 적용
    include_once(dirname(__FILE__)."/option_select.bottom_inc.php");



    exit;

?>