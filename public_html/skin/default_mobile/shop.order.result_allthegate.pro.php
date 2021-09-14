<?php

	include_once(dirname(__FILE__)."/../../include/inc.php");
	$ordernum = $_SESSION["session_ordernum"];//주문번호

	// --> 비회원 구매를 위한 쿠키 적용여부 파악
	cookie_chk();

	// 회원정보 추출
	if(is_login()) $indr = $row_member;

	// 주문정보 추출
	$r = _MQ("select * from odtOrder where ordernum='". $ordernum ."' ");

	// 결제금액이 정상인지 체크
	if($_POST[Amt] != $r[tPrice]) {
		error_loc_msg("/?pn=shop.order.result" , "결제금액이 다릅니다. 정상결제금액 : ".$r[tPrice].", 요청된결제금액 : ".$_POST[Amt]);
	}

/********************************************************************************
*
* 프로젝트 : AGSMobile V1.0
* (※ 본 프로젝트는 아이폰 및 안드로이드에서 이용하실 수 있으며 일반 웹페이지에서는 결제가 불가합니다.)
*
* 파일명 : AGS_pay_ing.php
* 최종수정일자 : 2010/10/6
*
* 올더게이트 결제창에서 리턴된 데이터를 받아서 소켓결제요청을 합니다.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
*
*  ※ 유의사항 ※
*  1.  "|"(파이프) 값은 결제처리 중 구분자로 사용하는 문자이므로 결제 데이터에 "|"이 있을경우
*   결제가 정상적으로 처리되지 않습니다.(수신 데이터 길이 에러 등의 사유)
********************************************************************************/
	
	
	/****************************************************************************
	*
	* [1] 라이브러리(AGSLib.php)를 인클루드 합니다.
	*
	****************************************************************************/
	require (PG_M_DIR."/Ags/lib/AGSLib.php");


	/****************************************************************************
	*
	* [2]. agspay4.0 클래스의 인스턴스를 생성합니다.
	*
	****************************************************************************/
	$agspay = new agspay40;



	/****************************************************************************
	*
	* [3] AGS_pay.html 로 부터 넘겨받을 데이타
	*
	****************************************************************************/

	/*공통사용*/
	$agspay->SetValue("AgsPayHome",PG_M_DIR."/Ags");			//올더게이트 결제설치 디렉토리 (상점에 맞게 수정)
	$agspay->SetValue("StoreId",trim($_POST["StoreId"]));		//상점아이디
	$agspay->SetValue("log","true");							//true : 로그기록, false : 로그기록안함.
	$agspay->SetValue("logLevel","INFO");						//로그레벨 : DEBUG, INFO, WARN, ERROR, FATAL (해당 레벨이상의 로그만 기록됨)
	$agspay->SetValue("UseNetCancel","true");					//true : 망취소 사용. false: 망취소 미사용
	$agspay->SetValue("Type", "Pay");							//고정값(수정불가)
	$agspay->SetValue("RecvLen", 7);							//수신 데이터(길이) 체크 에러시 6 또는 7 설정. 
	
	$agspay->SetValue("AuthTy",trim($_POST["AuthTy"]));			//결제형태
	$agspay->SetValue("SubTy",trim($_POST["SubTy"]));			//서브결제형태
	$agspay->SetValue("OrdNo",trim($_POST["OrdNo"]));			//주문번호
	$agspay->SetValue("Amt",trim($_POST["Amt"]));				//금액
	$agspay->SetValue("UserEmail",trim($_POST["UserEmail"]));	//주문자이메일
	$agspay->SetValue("ProdNm",trim($_POST["ProdNm"]));			//상품명

	/*신용카드&가상계좌사용*/
	$agspay->SetValue("MallUrl",trim($_POST["MallUrl"]));		//MallUrl(무통장입금) - 상점 도메인 가상계좌추가
	$agspay->SetValue("UserId",trim($_POST["UserId"]));			//회원아이디


	/*신용카드사용*/
	$agspay->SetValue("OrdNm",trim($_POST["OrdNm"]));			//주문자명
	$agspay->SetValue("OrdPhone",trim($_POST["OrdPhone"]));		//주문자연락처
	$agspay->SetValue("OrdAddr",trim($_POST["OrdAddr"]));		//주문자주소 가상계좌추가
	$agspay->SetValue("RcpNm",trim($_POST["RcpNm"]));			//수신자명
	$agspay->SetValue("RcpPhone",trim($_POST["RcpPhone"]));		//수신자연락처
	$agspay->SetValue("DlvAddr",trim($_POST["DlvAddr"]));		//배송지주소
	$agspay->SetValue("Remark",trim($_POST["Remark"]));			//비고
	$agspay->SetValue("DeviId",trim($_POST["DeviId"]));			//단말기아이디
	$agspay->SetValue("AuthYn",trim($_POST["AuthYn"]));			//인증여부
	$agspay->SetValue("Instmt",trim($_POST["Instmt"]));			//할부개월수
	$agspay->SetValue("UserIp",$_SERVER["REMOTE_ADDR"]);		//회원 IP

	/*신용카드(ISP)*/
	$agspay->SetValue("partial_mm",trim($_POST["partial_mm"]));		//일반할부기간
	$agspay->SetValue("noIntMonth",trim($_POST["noIntMonth"]));		//무이자할부기간
	$agspay->SetValue("KVP_CURRENCY",trim($_POST["KVP_CURRENCY"]));	//KVP_통화코드
	$agspay->SetValue("KVP_CARDCODE",trim($_POST["KVP_CARDCODE"]));	//KVP_카드사코드
	$agspay->SetValue("KVP_SESSIONKEY",$_POST["KVP_SESSIONKEY"]);	//KVP_SESSIONKEY
	$agspay->SetValue("KVP_ENCDATA",$_POST["KVP_ENCDATA"]);			//KVP_ENCDATA
	$agspay->SetValue("KVP_CONAME",trim($_POST["KVP_CONAME"]));		//KVP_카드명
	$agspay->SetValue("KVP_NOINT",trim($_POST["KVP_NOINT"]));		//KVP_무이자=1 일반=0
	$agspay->SetValue("KVP_QUOTA",trim($_POST["KVP_QUOTA"]));		//KVP_할부개월

	/*신용카드(안심)*/
	$agspay->SetValue("CardNo",trim($_POST["CardNo"]));			//카드번호
	$agspay->SetValue("MPI_CAVV",$_POST["MPI_CAVV"]);			//MPI_CAVV
	$agspay->SetValue("MPI_ECI",$_POST["MPI_ECI"]);				//MPI_ECI
	$agspay->SetValue("MPI_MD64",$_POST["MPI_MD64"]);			//MPI_MD64

	/*신용카드(일반)*/
	$agspay->SetValue("ExpMon",trim($_POST["ExpMon"]));				//유효기간(월)
	$agspay->SetValue("ExpYear",trim($_POST["ExpYear"]));			//유효기간(년)
	$agspay->SetValue("Passwd",trim($_POST["Passwd"]));				//비밀번호
	$agspay->SetValue("SocId",trim($_POST["SocId"]));				//주민등록번호/사업자등록번호

	/*핸드폰사용*/
	$agspay->SetValue("HP_SERVERINFO",trim($_POST["HP_SERVERINFO"]));	//SERVER_INFO(핸드폰결제)
	$agspay->SetValue("HP_HANDPHONE",trim($_POST["HP_HANDPHONE"]));		//HANDPHONE(핸드폰결제)
	$agspay->SetValue("HP_COMPANY",trim($_POST["HP_COMPANY"]));			//COMPANY(핸드폰결제)
	$agspay->SetValue("HP_ID",trim($_POST["HP_ID"]));					//HP_ID(핸드폰결제)
	$agspay->SetValue("HP_SUBID",trim($_POST["HP_SUBID"]));				//HP_SUBID(핸드폰결제)
	$agspay->SetValue("HP_UNITType",trim($_POST["HP_UNITType"]));		//HP_UNITType(핸드폰결제)
	$agspay->SetValue("HP_IDEN",trim($_POST["HP_IDEN"]));				//HP_IDEN(핸드폰결제)
	$agspay->SetValue("HP_IPADDR",trim($_POST["HP_IPADDR"]));			//HP_IPADDR(핸드폰결제)

	/*가상계좌사용*/
	$agspay->SetValue("VIRTUAL_CENTERCD",trim($_POST["VIRTUAL_CENTERCD"]));	//은행코드(가상계좌)
	$agspay->SetValue("VIRTUAL_DEPODT",trim($_POST["VIRTUAL_DEPODT"]));		//입금예정일(가상계좌)
	$agspay->SetValue("ZuminCode",trim($_POST["ZuminCode"]));				//주민번호(가상계좌)
	$agspay->SetValue("MallPage",trim($_POST["MallPage"]));					//상점 입/출금 통보 페이지(가상계좌)
	$agspay->SetValue("VIRTUAL_NO",trim($_POST["VIRTUAL_NO"]));				//가상계좌번호(가상계좌)

	/*에스크로사용*/
	$agspay->SetValue("ES_SENDNO",trim($_POST["ES_SENDNO"]));				//에스크로전문번호

	/*추가사용필드*/
	$agspay->SetValue("Column1", trim($_POST["Column1"]));						//추가사용필드1   
	$agspay->SetValue("Column2", trim($_POST["Column2"]));						//추가사용필드2
	$agspay->SetValue("Column3", trim($_POST["Column3"]));						//추가사용필드3
	
	/****************************************************************************
	*
	* [4] 올더게이트 결제서버로 결제를 요청합니다.
	*
	****************************************************************************/
	$agspay->startPay();

	
	/****************************************************************************
	*
	* [5] 결제결과에 따른 상점DB 저장 및 기타 필요한 처리작업을 수행하는 부분입니다.
	*
	*	아래의 결과값들을 통하여 각 결제수단별 결제결과값을 사용하실 수 있습니다.
	*	
	*	-- 공통사용 --
	*	업체ID : $agspay->GetResult("rStoreId")
	*	주문번호 : $agspay->GetResult("rOrdNo")
	*	상품명 : $agspay->GetResult("rProdNm")
	*	거래금액 : $agspay->GetResult("rAmt")
	*	성공여부 : $agspay->GetResult("rSuccYn") (성공:y 실패:n)
	*	결과메시지 : $agspay->GetResult("rResMsg")
	*
	*	1. 신용카드
	*	
	*	전문코드 : $agspay->GetResult("rBusiCd")
	*	거래번호 : $agspay->GetResult("rDealNo")
	*	승인번호 : $agspay->GetResult("rApprNo")
	*	할부개월 : $agspay->GetResult("rInstmt")
	*	승인시각 : $agspay->GetResult("rApprTm")
	*	카드사코드 : $agspay->GetResult("rCardCd")
	*
	*
	*	2.가상계좌
	*	가상계좌의 결제성공은 가상계좌발급의 성공만을 의미하며 입금대기상태로 실제 고객이 입금을 완료한 것은 아닙니다.
	*	따라서 가상계좌 결제완료시 결제완료로 처리하여 상품을 배송하시면 안됩니다.
	*	결제후 고객이 발급받은 계좌로 입금이 완료되면 MallPage(상점 입금통보 페이지(가상계좌))로 입금결과가 전송되며
	*	이때 비로소 결제가 완료되게 되므로 결제완료에 대한 처리(배송요청 등)은  MallPage에 작업해주셔야 합니다.
	*	결제종류 : $agspay->GetResult("rAuthTy") (가상계좌 일반 : vir_n 유클릭 : vir_u 에스크로 : vir_s)
	*	승인일자 : $agspay->GetResult("rApprTm")
	*	가상계좌번호 : $agspay->GetResult("rVirNo")
	*
	*	3.핸드폰결제
	*	핸드폰결제일 : $agspay->GetResult("rHP_DATE")
	*	핸드폰결제 TID : $agspay->GetResult("rHP_TID")
	*
	****************************************************************************/


	##주문정보의 결제정보를 넘겨준다.
	$authum					= $agspay->GetResult('rApprNo');    //승인번호
	$ordernum       = $agspay->GetResult('rOrdNo');           //주문번호
	$tPriceResult		= $agspay->GetResult('rAmt');   //결제금액
	$apprTm         = $agspay->GetResult('rApprTm');    //결제시간
	$dealNo         = $agspay->GetResult('rDealNo');    //신용카드공통거래번호
	$subTy          = $agspay->GetResult("SubTy");      //서브결제형태
	

	// - 결제 성공 기록정보 저장 ---
	$keys = array('rResMsg','AuthTy','rApprTm','rDealNo','SubTy','rSuccYn','rCardCd','rCardNm','rMembNo','rAquiCd','rAquiNm');
	$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
	foreach($keys as $name) {
		$app_oc_content .= $name . "||" .iconv("euc-kr","utf-8",$agspay->GetResult($name)) . "§§" ;
	}

	// - 주문결제기록 저장 ---
	$que = "
		insert odtOrderCardlog set
			 oc_oordernum = '".$ordernum."'
			,oc_tid				= '". $authum ."'
			,oc_content		= '".$app_oc_content."'
			,oc_rdate			= now();
	";
	_MQ_noreturn($que);
	// - 주문결제기록 저장 ---

	if($agspay->GetResult("rSuccYn") == "y")
	{ 
		if($agspay->GetResult("AuthTy") == "virtual"){
			//가상계좌결제의 경우 입금이 완료되지 않은 입금대기상태(가상계좌 발급성공)이므로 상품을 배송하시면 안됩니다. 
			$ool_ordernum = iconv('euc-kr','utf-8',$agspay->GetResult('rOrdNo'));
			$ool_member = $r[orderid];
			$ool_tid = $agspay->GetResult('rDealNo');
			$ool_type = 'R';
			$ool_respdate = iconv('euc-kr','utf-8',$agspay->GetResult('rApprTm'));
			$ool_amount_current = trim($_POST['Amt']);
			//$ool_amount_total = $ool_amount_total_final;
			$ool_account_num = iconv('euc-kr','utf-8',$agspay->GetResult('rVirNo'));
			$ool_account_code = '';
			$ool_deposit_name = $r[ordername];
			$ool_bank_name_array = array(
				'39'=>'경남',
				'34'=>'광주',
				'04'=>'국민',
				'03'=>'기업',
				'11'=>'농협',
				'31'=>'대구',
				'32'=>'부산',
				'02'=>'산업',
				'45'=>'새마을금고',
				'07'=>'수협',
				'88'=>'신한',
				'48'=>'신협',
				'05'=>'외환',
				'20'=>'우리',
				'71'=>'우체국',
				'37'=>'전북',
				'35'=>'제주',
				'81'=>'하나',
				'27'=>'한국씨티',
				'23'=>'SC은행',
				'09'=>'동양증권',
				'78'=>'신한금융투자증권',
				'40'=>'삼성증권',
				'30'=>'미래에셋증권',
				'43'=>'한국투자증권',
				'69'=>'한화증권'
			);
			$ool_bank_name = $ool_bank_name_array[$agspay->GetResult('VIRTUAL_CENTERCD')];
			$ool_bank_code = iconv('euc-kr','utf-8',$agspay->GetResult('VIRTUAL_CENTERCD'));

			// -- 2016-11-17 에스크로 적용여부에따른 에스크로수수료 적용 수정 SSJ ----
			//$ool_escrow = iconv('euc-kr','utf-8',$agspay->GetResult('ES_SENDNO'));
			//$ool_escrow_code = substr($agspay->GetResult('rResMsg'),-6);
			$ex_escrow = explode(':', $agspay->GetResult('rResMsg'));
			$ool_escrow_code = $ex_escrow[1];
			//$ool_escrow = 'Y';
			$ool_escrow = ($ool_escrow_code ? 'Y' : 'N');
			$ool_amount_total_final = $agspay->GetResult('rAmt');
			$ool_escrow_fee = 0;
			if($ool_escrow == "Y"){
				// 가상계좌 에스크로 이용 결제시 수수료 계산
				if($agspay->GetResult('rAmt') < 30000) { $ool_escrow_fee = 500; }
				if($agspay->GetResult('rAmt') >= 30000 && $agspay->GetResult('rAmt') < 200000) { $ool_escrow_fee = 800; }
				if($agspay->GetResult('rAmt') >= 200000 && $agspay->GetResult('rAmt') < 500000) { $ool_escrow_fee = 1400; }
				if($agspay->GetResult('rAmt') >= 500000 && $agspay->GetResult('rAmt') < 1000000) { $ool_escrow_fee = 2500; }
				if($agspay->GetResult('rAmt') >= 1000000) { $ool_escrow_fee = 4900; }
			}
			$ool_amount_total = $ool_amount_total_final + $ool_escrow_fee;
			// -- 2016-11-17 에스크로 적용여부에따른 에스크로수수료 적용 수정 SSJ ----

			$ool_deposit_tel = $indr[htel1].'-'.$indr[htel2].'-'.$indr[htel3];
			$ool_bank_owner = $indr[name];
			_MQ_noreturn("
				insert into odtOrderOnlinelog (
					ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner, ool_escrow_fee
				) values (
					'$ool_ordernum', '$ool_member', now(), '$ool_tid', '$ool_type', '$ool_respdate', '$ool_amount_current', '$ool_amount_total', '$ool_account_num', '$ool_account_code', '$ool_deposit_name', '$ool_bank_name', '$ool_bank_code', '$ool_escrow', '$ool_escrow_code', '$ool_deposit_tel', '$ool_bank_owner' , '$ool_escrow_fee'
				)
	        ");
			include_once($_SERVER['DOCUMENT_ROOT'].'/pages/shop.order.mail.send.virtual.php'); // 가상계좌 문자 & 메일 2016-12-16 LDD
			error_loc("/?pn=shop.order.complete");
		}else{
			// 결제성공에 따른 상점처리부분

			// -- 최종결제요청 결과 성공 DB처리 ---

			$_rApprNo = $agspay->GetResult('rApprNo');
			_MQ_noreturn("update odtOrder set authum = '$_rApprNo' where ordernum = '$ordernum'");

			// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
			include dirname(__FILE__)."/shop.order.result.pro.php";

			// 결제완료페이지 이동
			error_loc("/?pn=shop.order.complete");

		}
	}
	else
	{
      $errmsg = iconv("euc-kr","utf-8",$agspay->GetResult("rResMsg"));     //실패사유

			_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");
			error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.[ 사유 : " . $errmsg."]");

	}

	_MQ_noreturn("update odtOrder set orderstatus_step='결제실패' where ordernum='". $ordernum ."' ");
	error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.");

?>