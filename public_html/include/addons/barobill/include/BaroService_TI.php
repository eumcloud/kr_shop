<?php
	/*
	================================================================================================
	바로빌 전자세금계산서 연동서비스
	version : 3.6 (2013-12)
			
	바로빌 연동개발지원 사이트
	http://dev.barobill.co.kr/

	Copyright (c) 2009 BaroBill
	http://www.barobill.co.kr/


	연동사업자란?
	바로빌이 제공한 WebService를 이용하여 솔루션에 전자세금계산서와 관련된 기능을 개발하는 사업자

	연계사업자란?
	연동사업자가 공급한 솔루션을 사용하는 연동사의 고객
	'================================================================================================
	*/


	//------------------------------------------------------------------------------------------------
	//바로빌 연동서비스 웹서비스 참조(WebService Reference) URL
	$BAROSERVICE_URL = ($row_setup['TAX_MODE'] == "test" ? 'http://testws.baroservice.com/TI.asmx?WSDL' : 'http://ws.baroservice.com/TI.asmx?WSDL'); //테스트베드용 , 실서비스용 체크
	$CERTKEY = $row_setup['TAX_CERTKEY'];
	//------------------------------------------------------------------------------------------------


	$BaroService_TI = new SoapClient($BAROSERVICE_URL, array(

								'trace'		=> 'true',

								'encoding'	=> 'UTF-8' //소스를 ANSI로 사용할 경우 euc-kr로 수정

							));

	
function getErrStr($CERTKEY, $ErrCode){

		global $BaroService_TI;



		$ErrStr = $BaroService_TI->GetErrString(array(

			'CERTKEY'		=> $CERTKEY,

			'ErrCode'		=> $ErrCode

		))->GetErrStringResult;


		return $ErrStr;

	}
?>