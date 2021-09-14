<?php

	include_once( dirname(__FILE__)."/../include/BaroService_TI.php");
	include_once( dirname(__FILE__)."/../include/var.php");

	$CERTKEY = $row_setup['TAX_CERTKEY'];			//인증키
	$CorpNum = rm_str($row_company[number1]); //연계사업자 사업자번호 ('-' 제외, 10자리)

	$Result = $BaroService_TI->GetBalanceCostAmount(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum
				))->GetBalanceCostAmountResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
		$return_balance = "오류발생(". $Result .")";
	}else{
		$return_balance = $Result;	//잔여포인트
	}

?>