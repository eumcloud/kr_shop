<?php

	//$CERTKEY = '';							//인증키 - BaroService_TI.php 적용됨

	$IssueDirection = 1;					//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
	$TaxInvoiceType = 1;					//1-세금계산서, 2-계산서, 4-위수탁세금계산서, 5-위수탁계산서

	//-------------------------------------------
	//과세형태
	//-------------------------------------------
	//TaxInvoiceType 이 1,4 일 때 : 1-과세, 2-영세
	//TaxInvoiceType 이 2,5 일 때 : 3-면세
	//-------------------------------------------
	$TaxType = 1;

	$TaxCalcType = 1;						//세율계산방법 : 1-절상, 2-절사, 3-반올림
	$PurposeType = 2;						//1-영수, 2-청구

	$Kwon = '';								//별지서식 11호 상의 [권] 항목
	$Ho = '';								//별지서식 11호 상의 [호] 항목
	$SerialNum = $oscr[s_uid];				//별지서식 11호 상의 [일련번호] 항목

	//-------------------------------------------
	//공급가액 총액
	//-------------------------------------------
	//$AmountTotal = $oscr['s_com_price'] - ceil($oscr['s_com_price'] / 10);
	$AmountTotal = $oscr['s_discount'] - ceil($oscr['s_discount'] / 11); // 역발행 패치 // 2016-10-13 총액의 10%가 아니라 공급가의 10%적용으로 변경 SSJ
	//$AmountTotal = ($oscr['s_price']-$oscr['s_com_price']) - floor(($oscr['s_price']-$oscr['s_com_price']) / 11); // 역발행 패치 2016-02-29 계산식변경 (총금액-입점업체 정산금액)

	//-------------------------------------------
	//세액합계
	//-------------------------------------------
	//$TaxType 이 2 또는 3 으로 셋팅된 경우 0으로 입력
	//-------------------------------------------
	//$TaxTotal = ceil($oscr['s_com_price'] / 10);
	$TaxTotal = ceil($oscr['s_discount'] / 11); // 역발행 패치 // 2016-10-13 총액의 10%가 아니라 공급가의 10%적용으로 변경 SSJ
	//$TaxTotal = floor(($oscr['s_price']-$oscr['s_com_price']) / 11); // 역발행 패치

	//-------------------------------------------
	//합계금액
	//-------------------------------------------
	//공급가액 총액 + 세액합계 와 일치해야 합니다.
	//-------------------------------------------
	//$TotalAmount = $oscr['s_com_price'];
	$TotalAmount = $oscr['s_discount']; // 역발행 패치
	//$TotalAmount = ($oscr['s_price']-$oscr['s_com_price']); // 역발행 패치

	$Cash = '';								//현금
	$ChkBill = '';							//수표
	$Note = '';								//어음
	$Credit = '';							//외상미수금

	$Remark1 = '';
	$Remark2 = '';
	$Remark3 = '';

	$WriteDate = '';						//작성일자 (YYYYMMDD), 공백입력 시 Today로 작성됨.

	//-------------------------------------------
	//공급자 정보 - 정발행시 세금계산서 작성자
	//------------------------------------------

	// 세금계산서 발행 되었을 경우 차단
	if( in_array($oscr['s_tax_status'],array(3011,3014)) ) {
		error_msg("세금계산서가 발행된 상태입니다. 임시저장 할 수 없습니다.");
	}

	$MgtKey = shop_ordernum_create();
	_MQ_noreturn(" update odtOrderSettleComplete set s_tax_mgtnum ='". $MgtKey ."' where s_uid='". $oscr['s_uid'] ."' ");// MgtKey 정보 입력

	$InvoicerParty = array(
		'MgtNum' 		=> $MgtKey ,	 //정발행시 필수입력 - 자체문서관리번호 - 24자리이내 고유키
		'CorpNum' 		=> rm_str($row_company[number1]),	//필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
		'TaxRegID' 		=> '1111',
		'CorpName' 		=> $row_company[name],		//필수입력
		'CEOName' 		=> $row_company[ceoname],				//필수입력
		'Addr' 			=> $row_company[taxaddress],
		'BizType' 		=> $row_company[taxstatus],
		'BizClass' 		=> $row_company[taxitem],
		'ContactID' 	=> $row_setup['TAX_BAROBILL_ID'],						//필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> $row_setup['TAX_BAROBILL_NAME'],				//필수입력
		'TEL' 			=> $row_company[htel],
		'HP' 			=> $row_company[tel],
		'Email' 		=> $row_company[email]			//필수입력
	);

	//-------------------------------------------
	//공급받는자 정보 - 역발행시 세금계산서 작성자
	//------------------------------------------
	$InvoiceeParty = array(
		'MgtNum' 		=> '',						//역발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> rm_str($oscr[cNumber]),			//필수입력
		'TaxRegID' 		=> "2222",
		'CorpName' 		=> $oscr[cName],	//필수입력
		'CEOName' 		=> $oscr[ceoName],				//필수입력
		'Addr' 			=> $oscr[address],
		'BizType' 		=> $oscr[cItem1],
		'BizClass' 		=> $oscr[cItem2],
		'ContactID' 	=> '',						//역발행시 필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> $oscr['name'],				//필수입력
		'TEL' 			=> tel_format($oscr[tel1].$oscr[tel2].$oscr[tel3]),
		'HP' 			=> tel_format($oscr[htel1].$oscr[htel2].$oscr[htel3]),
		'Email' 		=> $oscr['email']			//역발행시 필수입력
	);


	//-------------------------------------------
	//수탁자 정보 - 위수탁 발행시 세금계산서 작성자
	//------------------------------------------
	$BrokerParty = array(
		'MgtNum' 		=> '',						//위수탁발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> '',						//위수탁발행시 필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
		'TaxRegID' 		=> '',
		'CorpName' 		=> '',						//위수탁발행시 필수입력
		'CEOName' 		=> '',						//위수탁발행시 필수입력
		'Addr' 			=> '',
		'BizType' 		=> '',
		'BizClass' 		=> '',
		'ContactID' 	=> '',						//위수탁발행시 필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> '',						//위수탁발행시 필수입력
		'TEL' 			=> '',
		'HP' 			=> '',
		'Email' 		=> ''						//위수탁발행시 필수입력
	);

	//-------------------------------------------
	//품목
	//-------------------------------------------

	// 주문정보 호출
	$tmpr = _text_info_extraction( "odtOrderSettleComplete" , $oscr['s_uid'] );
	$op_code = explode(',', $tmpr['s_opuid']);
	$pr = _MQ(" select * from odtOrderProduct as op where op.op_uid in ('". implode("' , '" , $op_code) ."') and op_partnerCode = '" . $oscr['s_partnerCode'] . "' order by op_uid asc limit 1");
	$app_pname = trim(stripslashes($pr['op_pname'] . " " . $pr['op_option1'] . " " . $pr['op_option2'] . " " . $pr['op_option3'])) ;// 상품명
	$app_pname .= (sizeof($op_code) > 1 ? " 외 " . (sizeof($op_code)-1) . "건" : "");
	$app_pname .= " 정산수수료";

	$TaxInvoiceTradeLineItems = array(
		'TaxInvoiceTradeLineItem'	=> array(
			array(
				'PurchaseExpiry'=> '',			//YYYYMMDD
				'Name'			=> $app_pname ,
				'Information'	=> 'EA1',
				'ChargeableUnit'=> '1',
				'UnitPrice'		=> $AmountTotal ,
				'Amount'		=> $AmountTotal ,
				'Tax'			=> $TaxTotal,
				'Description'	=> ''
			)
		)
	);

	//-------------------------------------------
	//전자세금계산서
	//-------------------------------------------
	$TaxInvoice = array(
		'InvoiceKey'				=> '',
		'InvoiceeASPEmail'			=> '',
		'IssueDirection'			=> $IssueDirection,
		'TaxInvoiceType'			=> $TaxInvoiceType,
		'TaxType'					=> $TaxType,
		'TaxCalcType'				=> $TaxCalcType,
		'PurposeType'				=> $PurposeType,
		'ModifyCode'				=> $ModifyCode,
		'Kwon'						=> $Kwon,
		'Ho'						=> $Ho,
		'SerialNum'					=> $SerialNum,
		'Cash'						=> $Cash,
		'ChkBill'					=> $ChkBill,
		'Note'						=> $Note,
		'Credit'					=> $Credit,
		'WriteDate'					=> $WriteDate,
		'AmountTotal'				=> $AmountTotal,
		'TaxTotal'					=> $TaxTotal,
		'TotalAmount'				=> $TotalAmount,
		'Remark1'					=> $Remark1,
		'Remark2'					=> $Remark2,
		'Remark3'					=> $Remark3,
		'InvoicerParty'				=> $InvoicerParty,
		'InvoiceeParty'				=> $InvoiceeParty,
		'BrokerParty'				=> $BrokerParty,
		'TaxInvoiceTradeLineItems'	=> $TaxInvoiceTradeLineItems
	);

	//정발행
	//echo "<xmp>".print_R($TaxInvoice , true)."</xmp>";
	$Result = $BaroService_TI->RegistTaxInvoice(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoicerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				))->RegistTaxInvoiceResult;
	/*
	//역발행
	$Result = $BaroService_TI->RegistTaxInvoiceReverse(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoiceeParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				))->Result;

	//위수탁
	$Result = $BaroService_TI->RegistBrokerTaxInvoice(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['BrokerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				))->Result;
	*/



//ViewArr($Result);
	if ($Result < 0) {
		//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
		tax_log_insert($oscr['s_uid'] , $mode , $Result , getErrStr($CERTKEY, $Result));
	}
	else{
		//echo $Result; //1-성공
		tax_log_insert($oscr['s_uid'] , $mode , $Result , "성공");
	}

?>