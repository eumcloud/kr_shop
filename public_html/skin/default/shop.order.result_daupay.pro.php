<?
include_once(dirname(__FILE__)."/../../include/inc.php");

// GET으로 넘어오는 값을 인코딩시킨다.
foreach($_GET as $k => $v)
	${$k} = iconv("euckr","utf8",$v);

// 다우페이 서버 IP대역이 아니면 중지시킨다 (다우페이 요청사항 - 다우페이 시스템 통보 서버 IP 대역 : 27.102.213.200~209) , 테스트서버IP : 123.140.121.205
if(!preg_match("/^27.102.213.20([0-9]{1})|123.140.121.205/",$_SERVER[REMOTE_ADDR])) exit;

/*------------------------------------------*/
// 다우페이 관련변수
/*------------------------------------------*/
// 카드사 코드정보
$daupay_card_code_array = array(
						"CCLG"=>"신한카드",
						"CCKM"=>"국민카드",
						"CCDI"=>"현대카드",
						"CCSS"=>"삼성카드",
						"CCNH"=>"NH농협카드",
						"CCSU"=>"수협카드",
						"CCCJ"=>"제주카드",
						"CCJB"=>"전북카드",
						"CCHN"=>"하나SK카드",
						"CCBC"=>"BC카드",
						"CCKE"=>"외한카드",
						"CCLO"=>"롯데카드",
						"CCCT"=>"시티카드",
						"CCPH"=>"우리카드",
						"CCKJ"=>"광주카드",
						"CCCU"=>"신협카드",
					);

// 결제처리 완료 HTML
$success_html = "<html><body><RESULT>SUCCESS</RESULT></body></html>";

// 응답변수(공통)
$PAYMETHOD;						// 결제수단 (CARD:카드, BANK:실시간계좌이체, VACCOUNTISSUE: 가상계좌발급, VACCOUNT:가상계좌 입금통보)
$ordernum = $ORDERNO;			// 주문번호
$ISMOBILE = $RESERVEDINDEX1;	// 모바일여부 (1:모바일, 0:pc)

if($PAYMETHOD == "CARD") {
	$DAOUTRX;			// 다우거래번호
	$AMOUNT;			// 결제금액
	$AUTHDATE;			// 결제일자(카드)
	$CARDCODE;			// 카드사 코드
} else if($PAYMETHOD == "BANK") {
	$DAOUTRX;			// 다우거래번호
	$AMOUNT;			// 결제금액
	$SETTDATE;			// 결제일자(계좌이체)
	$BANKNAME;			// 은행명
} else if($PAYMETHOD == "VACCOUNTISSUE") {	// 가상계좌 발급
	$BANKCODE;			// 입금 은행코드
	$BANKNAME;			// 입금 은행명
	$ACCOUNTNO;			// 입금계좌번호
	$RECEIVERNAME;		// 수취인명
	$DEPOSITENDDATE;	// 입금만료일
} else if($PAYMETHOD == "VACCOUNT") {		// 가상계좌 입금통보
	$DAOUTRX;			// 다우거래번호
	$AMOUNT;			// 결제금액
	$SETTDATE;			// 결제일자(계좌이체)
}

/*------------------------------------------*/
// 결제사전 처리.
/*------------------------------------------*/
// 주문정보 불러온다.
$order_info = get_order_info($ordernum);
if(!$order_info[ordernum]) { echo "해당주문이 없음"; exit; }

// 이미 결제 완료처리되었다면 결제 완료를 출력하고 중지시킨다.
if($order_info[paystatus] == "Y") { echo $success_html; exit; }

/*------------------------------------------*/
// 결제 방식 별로 처리한다..
/*------------------------------------------*/
if($PAYMETHOD == "CARD" || $PAYMETHOD == "BANK") {			// 카드 & 실시간 계좌이체

	// 결과값 저장한다.
	if($PAYMETHOD == "CARD") {
		$app_oc_content .= "다우거래번호||" .$DAOUTRX. "§§" ;
		$app_oc_content .= "결제금액||" .$AMOUNT. "§§" ;
		$app_oc_content .= "카드사||" .$daupay_card_code_array[$CARDCODE]. "§§" ;
	} else if($PAYMETHOD == "BANK") {
		$app_oc_content .= "다우거래번호||" .$DAOUTRX. "§§" ;
		$app_oc_content .= "결제금액||" .$AMOUNT. "§§" ;
		$app_oc_content .= "은행명||" .$BANKNAME. "§§" ;
	}

	$que = "
		insert odtOrderCardlog set
				oc_oordernum	= '".$order_info[ordernum]."'
				,oc_tid			= '". $DAOUTRX ."'
				,oc_content		= '". $app_oc_content ."'
				,oc_rdate		= now();
	";
	_MQ_noreturn($que);

	// 결제금액 변조 체크
	if($order_info[tPrice] != $AMOUNT) {
		_MQ_noreturn("update odtOrderCardlog set oc_content = concat(oc_content,'DB처리페이지 에러||결제금액 다름§§') where oc_oordernum = '".$order_info[ordernum]."'");
		exit;
	}

	// 승인번호 입력.
	$sque = "update odtOrder set authum = '".$DAOUTRX."' where ordernum='". $ordernum ."' ";
	_MQ_noreturn($sque);

	// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
	include "shop.order.result.pro.php";

	/*------------------------------------------*/
	// 정상처리 메세지 출력
	/*------------------------------------------*/
	echo $success_html;
	exit;

} else if($PAYMETHOD == "VACCOUNTISSUE") {		// 가상계자 발급

	$ordernum		= $order_info[ordernum];				// 주문번호
	$member			= $order_info[orderid];					// 주문자ID
	$tid			= "";									// 거래번호
	$type			= "R";									// 유형 (R:계좌발급, I:입금)
	$respdate		= $DEPOSITENDDATE;						// 입금만료일
	$amount_current	= 0;									// 현재 입금값.
	$amount_total	= $order_info[tPrice];					// 총 입금해야할 금액.
	$account_num	= $ACCOUNTNO;							// 계좌번호
	$account_code	= "";									// 입금순서
	$deposit_name	= $RECEIVERNAME;						// 수취인명
	$bank_name		= $BANKNAME;							// 은행명
	$bank_code		= $BANKCODE;							// 은행코드
	$escrow			= "";									// 에스크로 적용여부
	$escrow_code	= "";									// 에스크로 코드
	$deposit_tel	= tel_format($order_info[orderhtel1].$order_info[orderhtel2].$order_info[orderhtel3]);					// 입금자 전화
	$bank_owner		= $order_info[ordername];					// 예금주

	// 결과값 저장한다.
	$app_oc_content	= "입금은행코드||".$BANKCODE. "§§";			// 입금 은행코드
	$app_oc_content	.= "입금은행명||".$BANKNAME. "§§";			// 입금 은행명
	$app_oc_content	.= "입금계좌번호||".$ACCOUNTNO. "§§";			// 입금계좌번호
	$app_oc_content	.= "수취인명||".$RECEIVERNAME. "§§";			// 수취인명
	$app_oc_content	.= "입금만료일||".$DEPOSITENDDATE. "§§";		// 입금만료일

	$que = "
		insert odtOrderCardlog set
				oc_oordernum	= '".$order_info[ordernum]."'
				,oc_tid			= '". $DAOUTRX ."'
				,oc_content		= '". $app_oc_content ."'
				,oc_rdate		= now();
	";
	_MQ_noreturn($que);

	$que = "insert into odtOrderOnlinelog set
				ool_ordernum		= '$order_info[ordernum]',
				ool_member			= '$order_info[orderid]',
				ool_date			= now(),
				ool_tid				= '',
				ool_type			= '$type',
				ool_respdate		= '$respdate',
				ool_amount_current	= '$amount_current',
				ool_amount_total	= '$amount_total',
				ool_account_num		= '$account_num',
				ool_account_code	= '$account_code',
				ool_deposit_name	= '$deposit_name',
				ool_bank_name		= '$bank_name',
				ool_bank_code		= '$bank_code',
				ool_escrow			= '$escrow',
				ool_escrow_code		= '$escrow_code',
				ool_deposit_tel		= '$deposit_tel',
				ool_bank_owner		= '$bank_owner'";

	_MQ_noreturn($que);

	include_once($_SERVER['DOCUMENT_ROOT'].'/pages/shop.order.mail.send.virtual.php'); // 가상계좌 문자 & 메일 2016-12-16 LDD

	/*------------------------------------------*/
	// 정상처리 메세지 출력
	/*------------------------------------------*/
	echo $success_html;
	exit;

} else if($PAYMETHOD == "VACCOUNT") {	// 가상계좌 입금통보

	$tid			= $DAOUTRX;								// 거래번호
	$type			= "I";									// 유형 (R:계좌발급, I:입금)
	$amount_current	= $AMOUNT;								// 현재 입금값.
	$msg			= $SETTDATE."||".$AMOUNT."||".$DAOUTRX."§§";		// 메모 (입금일과 입금금액을 메모한다.)

	// 결과값 저장한다.
	$app_oc_content	= "다우거래번호||".$DAOUTRX. "§§";			// 다우거래번호
	$app_oc_content	.= "결제금액||".$AMOUNT. "§§";			// 결제금액
	$app_oc_content	.= "결제일자||".$SETTDATE. "§§";			// 결제일자

	$que = "
		insert odtOrderCardlog set
				oc_oordernum	= '".$order_info[ordernum]."'
				,oc_tid			= '". $DAOUTRX ."'
				,oc_content		= '". $app_oc_content ."'
				,oc_rdate		= now();
	";
	_MQ_noreturn($que);

	$que = "update odtOrderOnlinelog set
				ool_tid				= '$tid',
				ool_type			= '$type',
				ool_msg				= concat(ool_msg,'$msg'),
				ool_amount_current	= $amount_current
			where
				ool_ordernum		= '$order_info[ordernum]'
				";

	_MQ_noreturn($que);


	// - 2016-09-05 ::: JJC ::: 주문정보 추출 ::: 가상계좌 - 이미 결제가 되었다면 추가 적용을 하지 않게 처리함. ---
	if($order_info['tPrice'] == $AMOUNT && $order_info['paystatus'] <> "Y" ) {	// 결제금액과 입금금액이 같으면 완료 처리

		// 승인번호 입력.
		$sque = "update odtOrder set authum = '".$DAOUTRX."' where ordernum='". $ordernum ."' ";
		_MQ_noreturn($sque);

		// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
		include "shop.order.result.pro.php";
	}

	/*------------------------------------------*/
	// 정상처리 메세지 출력
	/*------------------------------------------*/
	echo $success_html;
	exit;
}
?>