<?php


	# JJC001 - 세금계산서 연동처리
	include_once( dirname(__FILE__)."/../../inc.php");


	if($row_setup['TAX_CHK'] <> "Y") {error_alt("세금계산서를 사용하는 상태가 아닙니다.");}
	if(!$mode) { error_alt("잘못된 접근입니다."); }
	if(!$suid) { error_alt("잘못된 접근입니다."); }



	include_once( dirname(__FILE__)."/include/BaroService_TI.php");
	include_once( dirname(__FILE__)."/include/var.php");



	// --- 정산정보 추출 ---
	$oscr = _MQ(" 
		select 
			s.*, m.*
		from odtOrderSettleComplete as s
		left join odtMember as m on (m.id=s.s_partnerCode and userType = 'C')
		where 
			s.s_uid = '" . $suid . "' 
	");
	// --- 정산정보 추출 ---



	// 세금계산서 상태에 따른 모듈 적용
	switch( $mode ){
		case "issue" :include_once( dirname(__FILE__)."/api_ti/_tax.IssueTaxInvoice.php");break;//세금계산서 발행
		case "cancel" :include_once( dirname(__FILE__)."/api_ti/_tax.ProcTaxInvoice.php");break;//세금계산서 발행취소
		case "delete" :include_once( dirname(__FILE__)."/api_ti/_tax.DeleteTaxInvoice.php");break;//세금계산서 삭제
		case "regist" :include_once( dirname(__FILE__)."/api_ti/_tax.RegistTaxInvoice.php");break;//세금계산서 임시저장
	}


	// 상태값 추출
	if($MgtKey) {
		// 세금계산서 상태값 업데이트
		include_once( dirname(__FILE__)."/api_ti/_tax.GetTaxInvoiceState.php"); 
	}

	error_frame_loc("/totalAdmin/_order4.view.php?suid=". $suid ."&_PVSC=" . $_PVSC);

?>