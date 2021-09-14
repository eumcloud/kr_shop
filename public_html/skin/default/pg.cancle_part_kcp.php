<?
	// 거래번호
	//$ocl = _MQ("select oc_tid from odtOrderCardlog where oc_oordernum = '".$_ordernum."' order by oc_uid desc limit 1");
	$ocl = _MQ("select oc_tid from odtOrderCardlog where oc_oordernum = '".$_ordernum."' and oc_tid != '' order by oc_uid desc limit 1"); // 2016-11-15 간혹 주문완료페이지 back키 입력으로 잘못된데이터가 추가되는경우 발생 수정 SSJ
	$tno = $ocl[oc_tid]; // PG사 거래 번호

	// 취소할 금액
	//$tmp = _MQ(" select sum( (op_pprice + op_poptionprice) * op_cnt + op_delivery_price + op_add_delivery_price) as sum from odtOrderProduct where op_cancel = 'N' and op_oordernum = '".$_ordernum."' ");
	// -- 2016-09-09 ::: 취소가능잔액 오류 수정 --- SSJ
	$tmp = _MQ(" select sum( (op_pprice + op_poptionprice) * op_cnt + op_delivery_price + op_add_delivery_price) as sum from odtOrderProduct where IF(op_cancel_type = 'pg' , op_cancel != 'Y' , 1 )  and op_oordernum = '".$_ordernum."' ");
	// -- 2016-09-09 ::: 취소가능잔액 오류 수정 --- SSJ
	$tmp2 = _MQ(" select sum(op_usepoint) as sum from odtOrderProduct where op_oordernum = '".$_ordernum."' and op_cancel != 'Y' ");
	$_cancel_price = trim($_total_amount);
	//$_confirm_price = ($tmp[sum] - $tmp2[sum] - $_cancel_price) > 0 ? ($tmp[sum] - $tmp2[sum] - $_cancel_price ) : 0;
	$_confirm_price = ($tmp[sum] - $tmp2[sum]) > 0 ? ($tmp[sum] - $tmp2[sum]) : 0; // 2017-06-05 ::: KCP 부분 취소 오류 수정 ::: JJC
	# 부분취소 금액은 해당 주문에서 포인트를 제외한 실제 지불한 금액의 총액을 가져온다.
	// $_confirm_price = $_confirm_price + $_cancel_price; // -- 2016-09-09 ::: 취소가능잔액 오류 수정 --- SSJ --> 이부분 주석처리
	#############################################################################################
	## KCP 결제 취소 START
	#############################################################################################
	require PG_DIR."/kcp/cfg/site_conf_inc.php";       // 환경설정 파일 include
	require PG_DIR."/kcp/files/pp_ax_hub_lib.php";     // library [수정불가]

	$c_PayPlus = new C_PP_CLI;
	$c_PayPlus->mf_clear();

	$tno     = trim($tno);
	$tran_cd = "00200000";
	$cust_ip = getenv("REMOTE_ADDR"); // 요청 IP

	$c_PayPlus->mf_set_modx_data( "tno",      $tno );		// KCP 원거래 거래번호
	$c_PayPlus->mf_set_modx_data( "mod_type", "STPC" );		// 원거래 변경 요청 종류
	$c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip );	// 변경 요청자 IP
	$c_PayPlus->mf_set_modx_data( "mod_desc", "cancel" );	// 변경 사유

	// 2017-06-05 ::: KCP 부분 취소 오류 수정 ::: JJC 
	// 예) 총 결제금액 20,000원
	//		1회 - 5,000원 부분취소시 --> mod_mny : 5,000 , rem_mny : 20,000
	//		2회 - 10,000원 부분취소시 --> mod_mny : 10,000 , rem_mny : 15,000
	$c_PayPlus->mf_set_modx_data( "mod_mny", $_cancel_price );	// 취소요청금액
	$c_PayPlus->mf_set_modx_data( "rem_mny", $_confirm_price );	// (취소 요청이 있기전 금액)

	$c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key ,  $tran_cd,    "", $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib", $ordernum, $cust_ip, "3", 0, 0, $g_conf_key_dir, $g_conf_log_dir);

	$res_cd  = $c_PayPlus->m_res_cd;
	$res_msg = iconv("euckr","utf8",$c_PayPlus->m_res_msg);	// 결과 메세지

	// 취소 성공 여부
	$is_pg_status = $res_cd == "0000" ? true : false;

	if( $is_pg_status === true ) {
		$amount			= $c_PayPlus->mf_get_res_data( "amount"       ); // 총 금액
		$panc_mod_mny	= $c_PayPlus->mf_get_res_data( "panc_mod_mny" ); // 부분취소 요청금액
		$panc_rem_mny	= $c_PayPlus->mf_get_res_data( "panc_rem_mny" ); // 부분취소 가능금액
	}

	// 취소결과 로그 기록
	card_cancle_log_write($tno,$res_msg);	// 카드거래번호 , 결과 메세지

?>