<?php
# LDD011
include_once("inc.php");

switch ($tran_type) {
	
	// 엑셀 업로드
	case 'ins_excel':

		# Error Reproting level modify
		error_reporting(E_ALL ^ E_NOTICE);

		# Excel Class Load
		require_once($_SERVER['DOCUMENT_ROOT']."/include/reader.php");


		# 첨부파일 확인
		if($_FILES['w_excel_file']['size'] <= 0) error_loc_msg("_product_option.form.php?pass_mode=".$pass_mode."&pass_code=" . $pass_code, "첨부파일이 없습니다.");


		# 첨부파일 확장자 검사
		$ext = '';
		$ext = substr(strrchr($_FILES['w_excel_file']['name'],"."),1);	//확장자앞 .을 제거하기 위하여 substr()함수를 이용
		$ext = strtolower($ext); //확장자를 소문자로 변환
		if($ext != 'xls') error_loc_msg("_product_option.form.php?pass_mode=".$pass_mode."&pass_code=" . $pass_code, "xls 파일만 업로드 가능합니다.");


		// --------------------- 상품옵션 정보 추출 --------------------- //
		$arr_option_uid = array(); // -- UID 별 옵션 저장
		$r = _MQ_assoc("select * from odtProductOption where oto_pcode='" . $pass_code . "' ");
		foreach( $r as $k=>$v ){
			$arr_option_uid[$v[oto_uid]] = $v;
		}

		$arr_option_name = array(); // -- optionname 별 옵션 저장
		foreach( $arr_option_uid as $k=>$v ){
			switch( $v['oto_depth'] ){
				case "3": 
					$ex = explode("," , $v['oto_parent']);
					$app_depth1_poptionname = $arr_option_uid[$ex[0]]['oto_poptionname'];
					$app_depth2_poptionname = $arr_option_uid[trim($ex[1]." " . $ex[2])]['oto_poptionname'];
					$arr_option_name[$app_depth1_poptionname][$app_depth2_poptionname][$v['oto_poptionname']] = $v;
				break;
				case "2": 
					$app_depth1_poptionname = $arr_option_uid[$v['oto_parent']]['oto_poptionname'];
					$arr_option_name[$app_depth1_poptionname][$v['oto_poptionname']][0] = $v;
				break;
				case "1": 
					$arr_option_name[$v['oto_poptionname']][0][0] = $v;
				break;
			}
		}
		// --------------------- 상품옵션 정보 추출 --------------------- //


		# Excel Load
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('utf-8');
		$data->read($_FILES['w_excel_file']['tmp_name']);
		//die(ViewArr($data->sheets[0]['cells'])); // 출력해보기


		# Excel 처리
		$arr_option_chk = array();
		for ($i=2; $i<=$data->sheets[0]['numRows']; $i++) {

			$r = $data->sheets[0]['cells'][$i];
			switch( $pass_mode ){
				case "3depth": 
					$_option1		= trim($r[1]);
					$_option2		= trim($r[2]);
					$_option3		= trim($r[3]);
					$_supplyprice	= trim($r[4]);
					$_price			= trim($r[5]);
					$_stock			= trim($r[6]);
				break;
				case "2depth": 
					$_option1		= trim($r[1]);
					$_option2		= trim($r[2]);
					$_option3		= 0;
					$_supplyprice	= trim($r[3]);
					$_price			= trim($r[4]);
					$_stock			= trim($r[5]);
				break;
				case "1depth": 
					$_option1		= trim($r[1]);
					$_option2		= 0;
					$_option3		= 0;
					$_supplyprice	= trim($r[2]);
					$_price			= trim($r[3]);
					$_stock			= trim($r[4]);
				break;
			}

			// JJC : 1차 옵션며이 없을 경우 넘김
			if(!$_option1) {continue;}

			# 수정 또는 추가 처리 {
			if($arr_option_name[$_option1][$_option2][$_option3]) { // 수정

				$que = "
					update odtProductOption set 
						oto_poptionprice='". $_price."',
						oto_poptionpurprice='". $_supplyprice."',
						oto_cnt='". $_stock."'
					where oto_uid='". $arr_option_name[$_option1][$_option2][$_option3]['oto_uid'] ."'
				";
				_MQ_noreturn($que);
			}
			else { // 추가

				switch( $pass_mode ){
					case "3depth": 

						// 1차 입력
						if(!$arr_option_chk[$_option1]) {

							// 순번추출 - 1차
					        $r1 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='1' ");
					        $max_sort = $r1['max_sort'] + 1;

							_MQ_noreturn(" insert odtProductOption set oto_poptionname='". $_option1 ."', oto_poptionprice='0', oto_poptionpurprice='0', oto_cnt='0' , oto_depth='1', oto_pcode='". $pass_code ."', oto_sort='". $max_sort ."' ");
							$_uid1 = mysql_insert_id();
							$arr_option_chk[$_option1] ++;
						}
						// 2차 입력
						if(!$arr_option_chk[$_option1 . $_option_date . $_option2]) {

							// 순번추출 - 2차
					        $r2 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='2' and oto_parent='" . $_uid1 . "' ");
					        $max_sort2 = $r2['max_sort'] + 1;

							_MQ_noreturn(" insert odtProductOption set oto_poptionname='". $_option2 ."', oto_poptionprice='0', oto_poptionpurprice='0', oto_cnt='0' , oto_depth='2', oto_parent = '". $_uid1 ."', oto_pcode='". $pass_code ."', oto_sort='". $max_sort2 ."' ");
							$_uid2 = mysql_insert_id();
							$arr_option_chk[$_option1 . $_option_date . $_option2] ++;
						}

						// 순번추출 - 3차
				        $r3 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='3' and find_in_set('" . $_uid2 . "' , oto_parent) > 0 ");
				        $max_sort3 = $r3['max_sort'] + 1;

						// 3차 입력
						_MQ_noreturn(" insert odtProductOption set oto_poptionname='". $_option3 ."', oto_poptionprice='".$_price."' , oto_poptionpurprice='".$_supplyprice."', oto_cnt='".$_stock."', oto_depth='3', oto_parent = '". $_uid1 .",". $_uid2 ."', oto_pcode='". $pass_code ."', oto_sort='". $max_sort3 ."' ");
					break;

					case "2depth": 

						// 1차 입력
						if(!$arr_option_chk[$_option1]) {

							// 순번추출 - 1차
					        $r1 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='1' ");
					        $max_sort = $r1['max_sort'] + 1;

							_MQ_noreturn(" insert odtProductOption set  oto_poptionname='". $_option1 ."', oto_poptionprice='0', oto_poptionpurprice='0', oto_cnt='0' , oto_depth='1', oto_pcode='". $pass_code ."', oto_sort='". $max_sort ."' ");
							$_uid1 = mysql_insert_id();
							$arr_option_chk[$_option1] ++;
						}

						// 순번추출 - 2차
				        $r2 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='2' and oto_parent='" . $_uid1 . "' ");
				        $max_sort2 = $r2['max_sort'] + 1;

						// 2차 입력
						_MQ_noreturn(" insert odtProductOption set  oto_poptionname='". $_option2 ."', oto_poptionprice='".$_price."', oto_poptionpurprice='".$_supplyprice."', oto_cnt='".$_stock."', oto_depth='2', oto_parent = '". $_uid1 ."', oto_pcode='". $pass_code ."', oto_sort='". $max_sort2 ."' ");
					break;

					case "1depth": 

						// 순번추출
						$r1 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='1' ");
						$max_sort = $r1['max_sort'] + 1;

						// 1차 입력
						_MQ_noreturn(" insert odtProductOption set  oto_poptionname='". $_option1 ."', oto_poptionprice='".$_price."', oto_poptionpurprice='".$_supplyprice."', oto_cnt='".$_stock."', oto_depth='1', oto_pcode='". $pass_code ."', oto_sort='". $max_sort ."' ");
					break;
				}
			}
			# } 수정 또는 추가 처리
		}
		error_loc("_product_option.form.php?pass_mode=".$pass_mode."&pass_code=" . $pass_code);
	break;
	
	case 'down_excel':
		# code...
	break;
}