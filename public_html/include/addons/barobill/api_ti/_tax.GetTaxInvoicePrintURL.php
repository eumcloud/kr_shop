<?php

	include_once( dirname(__FILE__)."/../include/BaroService_TI.php");
	include_once( dirname(__FILE__)."/../include/var.php");

	$CERTKEY = $row_setup['TAX_CERTKEY'];			//인증키
	$CorpNum = rm_str($row_company[number1]); //연계사업자 사업자번호 ('-' 제외, 10자리)

	$MgtKey = $app_tax_mgtnum;			//자체문서관리번호
	$ID = $row_setup['TAX_BAROBILL_ID'];				//연계사업자 아이디
	$PWD = $row_setup['TAX_BAROBILL_PW'];				//연계사업자 비밀번호

	if($MgtKey && $row_setup['TAX_CERTKEY'] && $CorpNum && $ID && $PWD) {

		$Result = $BaroService_TI->GetTaxInvoicePrintURL(array(
					'CERTKEY'		=> $CERTKEY,
					'CorpNum'		=> $CorpNum,
					'MgtKey'		=> $MgtKey,
					'ID'			=> $ID,
					'PWD'			=> $PWD
					))->GetTaxInvoicePrintURLResult;

		if($Result < 0){
			//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
			echo "<div style='float:left;margin-left:5px;'>오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result) ."</div>";
		}
		else{
			//echo "<a href=\"$Result\" target=\"_blank\">$Result</a>";
			if($AdminMode == 'subAdmin') {

				echo "<span class=\"shop_state_pack\"><span class=\"gray\" style=\"cursor:pointer\"  onclick=\"open_window('tax_print', '" . $Result . "', 100, 100, 1000, 630, '', '', '', '', '');\">세금계산서 인쇄</span></span>";
			}
			else {
				echo "<div style='float:left;margin-left:5px;'><span class='shop_btn_pack'><input type='button' onclick=\"open_window('tax_print', '" . $Result . "', 100, 100, 1000, 630, '', '', '', '', '');\" value='세금계산서 인쇄' class='input_small gray'></span></div>";
			}
		}

	}
?>