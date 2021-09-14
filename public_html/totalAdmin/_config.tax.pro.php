<?php

	include "inc.php";


	// 서비스 , 테스트에 따라 certkey 변경
	$TAX_CERTKEY = ($TAX_MODE == "service" ? $tax_barobill_certkery_service : $tax_barobill_certkery_test);


	// -- odtSetup 적용 ---
	$que = "
		update odtSetup set 
			TAX_BAROBILL_ID = '".$TAX_BAROBILL_ID."', 
			TAX_BAROBILL_PW = '".$TAX_BAROBILL_PW."', 			
			TAX_BAROBILL_NAME = '".$TAX_BAROBILL_NAME."', 
			TAX_CHK				= '".$TAX_CHK."', 
			TAX_MODE				= '".$TAX_MODE."', 
			TAX_CERTKEY				= '".$TAX_CERTKEY."'
		where 
			serialnum = 1
	";
	_MQ_noreturn($que);


	// -- odtCompany 적용 ---
	$sque = " 
		update odtCompany set 
			htel = '".$htel."', 
			email = '".$email."', 
			tel = '".$tel."', 
			name = '". $name ."',
			ceoname = '". $ceoname ."',
			number1 = '". $number1 ."',
			taxaddress = '". $taxaddress ."',
			taxstatus = '". $taxstatus ."',
			taxitem = '". $taxitem ."'
		where
		serialnum = 1";
	_MQ_noreturn($sque);

	error_frame_reload("수정되었습니다");



?>