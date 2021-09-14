<?php


	// --- 정산정보 추출 ---
	$re_oscr = _MQ(" select * from odtOrderSettleComplete where s_uid = '" . $suid . "' ");
	// --- 정산정보 추출 ---


	//$CERTKEY = '';			//인증키
	$CorpNum = rm_str($row_company[number1]);			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = $re_oscr['s_tax_mgtnum'];			//자체문서관리번호


	$Result = $BaroService_TI->GetTaxInvoiceState(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				))->GetTaxInvoiceStateResult;

	if (is_null($Result)){
		//echo '문서 상태를 불러오지 못했습니다.';
		_MQ_noreturn(" update odtOrderSettleComplete set s_tax_status ='-9999' where s_uid='". $oscr['s_uid'] ."' ");
	}else if ($Result->BarobillState < 0){
		//echo "오류코드 : $Result->BarobillState<br><br>".getErrStr($CERTKEY, $Result->BarobillState);
		_MQ_noreturn(" update odtOrderSettleComplete set s_tax_status ='". $Result->BarobillState ."' where s_uid='". $oscr['s_uid'] ."' ");
	}else{
		_MQ_noreturn(" update odtOrderSettleComplete set s_tax_status ='". $Result->BarobillState ."' where s_uid='". $oscr['s_uid'] ."' ");
//		echo "자체문서관리번호 : $Result->MgtKey<br>
//			바로빌문서관리번호 : $Result->InvoiceKey<br>
//			바로빌상태코드 : $Result->BarobillState : ". $arr_inner_state_table[$Result->BarobillState] ."<br>
//			개봉여부 : $Result->IsOpened<br>
//			메모1 : $Result->Remark1<br>
//			메모2 : $Result->Remark2<br>
//			국세청전송상태 : $Result->NTSSendState<br>
//			국세청승인번호 : $Result->NTSSendKey<br>
//			국세청전송결과 : $Result->NTSSendResult<br>
//			국세청전송일시 : $Result->NTSSendDT<br>
//			전송결과수신일시 : $Result->NTSResultDT";
	}
?>