<?php
include_once(dirname(__FILE__)."/../../include/inc.php");

$arr_method = array('CC','B'); // 신용카드, 계좌이체
foreach( $arr_method as $mk => $mv){


	$url = $row_setup['P_MODE'] == 'test' ? "https://agenttest.daoupay.com/DailyTransactionDownLoad.jsp" : "https://agent.daoupay.com/DailyTransactionDownLoad.jsp";
	$timestamp =strtotime("-1days" );
	$REQUESTDATE = date("Ymd",$timestamp);
	$REQUESTDATE = date("Ymd");
	$CPID			= $row_setup['P_ID'];						// 상점ID
	$PASSWORD = $row_setup['P_PG_ENC_KEY'];
	$METHOD = $mv;
	$DATAFORMAT = '1';
	$TYPE = '2';
// https://agenttest.daoupay.com/DailyTransactionDownLoad.jsp?CPID=CTS15553&PASSWORD=TS1653505!&METHOD=CC&REQUESTDATE=20190422&DATAFORMAT=1&TYPE=2
	$get_data = CurlExec( $url.'?CPID='.$CPID.'&PASSWORD='.$PASSWORD.'&METHOD='.$METHOD.'&REQUESTDATE='.$REQUESTDATE.'&DATAFORMAT='.$DATAFORMAT.'&TYPE='.$TYPE.'');
	$get_data = str_replace('<DATA>','',$get_data);
	$get_data = str_replace('</DATA>','',$get_data);
	$get_data = explode("\n",trim($get_data));

	$data= array();
	foreach($get_data as $k => $v){
		$data[] = explode("|" , $v);
	}

	if( count($data) == 0 ){ continue;}
	/*
		0번째 상태    S = 성공 , C = 취소
		1번째 거래일시
		2번째 키움페이 거래번호
		3번째 상점 주문번호
		4번째 결제금액
	*/
	foreach($data as $k => $v){

		$card_fail_order = _MQ(" select count(*) as cnt from odtOrder where paymethod in ('C', 'L') and paystatus='N' and canceled='N' and orderdate >= '". date("Y-m-d", strtotime("-1 days"))." 00:00:00' and ordernum='".$data[$k][3]."' ");

		if( $card_fail_order['cnt'] > 0 && $data[$k][0] == 'S' ){

			$app_kiwoom_oc_content = "다우거래번호||" .$data[$k][2]. "§§" ;
			$app_kiwoom_oc_content .= "결제금액||" .$data[$k][4]. "§§" ;
			$app_kiwoom_oc_content .= "카드사|| §§" ;
			$que = "
				insert odtOrderCardlog set
						oc_oordernum	= '".$data[$k][3]."'
						,oc_tid			= '". $data[$k][2] ."'
						,oc_content		= '". addslashes($app_kiwoom_oc_content) ."'
						,oc_rdate		= now();
			";
			_MQ_noreturn($que);

			$ordernum = $data[$k][3];
			// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
			include "shop.order.result.pro.php";
		}else{ continue; }
		
	}

}
?>

