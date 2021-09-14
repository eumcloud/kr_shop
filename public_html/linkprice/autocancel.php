<?php

	// DataBase 접속
	include_once (dirname(__FILE__)."/../include/inc.php") ;

	//1. 실적의 상태를 확인할 수 있는 스크립트를 제작해 주시면 됩니다.
	//2. 이 스크립트에서는 주문번호(o_cd)와 상품코드(p_cd)로 실적의 상태를 조회하게 됩니다.
	//3. 즉, 제작하신 스크립트 파일이 autocancel.php 라면 다음과 같이 o_cd, p_cd 파라미터로 값을 전달하여 호출을 합니다.
	//		http://www.merchant.com/linkprice/autocancel.php?o_cd=123&p_cd=AAA
	//4. 화면에는 다음과 같이 '결과코드'와 '실적 상태'가 출력되어야 합니다.
	//5. 소스코드 보기를 보면 HTML 태그나 자바스크립트없이 데이터값만 출력되었음을 알 수 있습니다.
	//6. 출력 데이터는 '결과코드'와 '실적상태'이며 자세한 내용은 아래 표를 참조해 주시기 바랍니다. 그리고 필드의 구분자는 탭문자(\t)입니다.
	//		결과코드		실적상태							링크프라이스 처리지침
	//		0					미정(예, 미입금)				20일 이전일 경우 취소
	//		1					주문완료							정상확정
	//		2					주문취소							취소
	//		3					주문번호의 주문이 없음		취소
	//		4					확인요망(예외상황)			링크프라이스 담당자 확인 후 처리

	// 넘어온 변수
	//	$o_cd - 주문번호
	//	$p_cd - 상품코드


	$r = _MQ(" select * from odtOrder where ordernum='". addslashes($o_cd) ."'  ");
	if($r['ordernum']){
		if( $r['canceled'] == 'Y' ) {
			echo '2	주문취소';
		}
		else if( $r['paystatus'] == 'N' ) {
			echo '0	미정(예, 미입금)';
		}
		else if( $r['paystatus'] == 'Y' ) {
			echo '1	주문완료';
		}
		else {
			echo '4	확인요망(예외상황)';
		}
	}
	else {
		echo '3	주문번호의 주문이 없음';
	}
	// DataBase 접속 끊기
?>