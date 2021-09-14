<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

//    $code = $_POST[code];
//    $uid = $_POST[uid];

    // ----------------- 사전체크 ---------------------//
    // 필수 변수 체크
    if( !$uid && !$code ) {
        echo "error1"; //잘못된 접근입니다.
        exit;
    }




    // 상품정보, 옵션정보 추출
    include_once(dirname(__FILE__)."/option_select.top_inc.php");

    // 필수옵션이라면 종속된 추가옵션 일괄 삭제 LMH002
    $tmp_pouid = _MQ_result(" select otpo_pouid from odtTmpProductOption where otpo_is_addoption != 'Y' and otpo_uid = '".$uid."' ");
    if( $tmp_pouid>0 ){
    	_MQ_noreturn(" delete from odtTmpProductOption where otpo_addoption_parent = '".$tmp_pouid."' ");
    }

    // 넘어온 정보 삭제
	$sque = " delete from odtTmpProductOption where otpo_uid='{$uid}' ";
	mysql_query($sque);


	// 삭제후 남은 옵션중 필수 옵션이 없으면 모든 추가옵션 삭제
	$no_addoption_cnt = _MQ(" select count(*) as cnt from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_is_addoption = 'N' ");
	if($no_addoption_cnt[cnt]==0) {
		_MQ_noreturn(" delete from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' ");
	}






    // 옵션목록 적용
    include_once(dirname(__FILE__)."/option_select.bottom_inc.php");


    exit;

?>