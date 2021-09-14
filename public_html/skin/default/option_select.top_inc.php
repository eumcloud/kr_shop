<?PHP

	// - 상품추출 ---
    $que = "select * from odtProduct where code = '".$code."' ";
    $r = _MQ($que);

    $arr_option_data = array();
	if($r[option_type_chk] == "3depth") {
		$poque = "
			SELECT 
				po3.* , 
				po2.oto_uid as po2_uid, po2.oto_poptionname as po2_poptionname,
				po1.oto_uid as po1_uid, po1.oto_poptionname as po1_poptionname
			FROM odtProductOption as po3 
			inner join odtProductOption as po2 on ( po2.oto_uid = SUBSTRING_INDEX(po3.oto_parent , ',',-1) and po2.oto_depth=2)
			inner join odtProductOption as po1 on ( po1.oto_uid = po2.oto_parent and po1.oto_depth=1)
			WHERE po3.oto_pcode='" . $code . "' and po3.oto_depth=3 ORDER BY po3.oto_uid ASC
		";
		$pores = _MQ_assoc($poque);
		foreach( $pores as $k=>$por ){
			$arr_option_data[$por[oto_uid]]['option_name1'] = $por[po1_poptionname];
			$arr_option_data[$por[oto_uid]]['option_name2'] = $por[po2_poptionname];
			$arr_option_data[$por[oto_uid]]['option_name3'] = $por[oto_poptionname];
			$arr_option_data[$por[oto_uid]]['option_supplyprice'] = $por[oto_poptionpurprice];
			$arr_option_data[$por[oto_uid]]['option_price'] = $por[oto_poptionprice];
			$arr_option_data[$por[oto_uid]]['option_cnt'] = $por[oto_cnt];
		}
	}
	else if($r[option_type_chk] == "2depth") {
		$poque = "
			SELECT 
				po2.*,
				po1.oto_uid as po1_uid, po1.oto_poptionname as po1_poptionname
			FROM odtProductOption as po2 
			inner join odtProductOption as po1 on ( po1.oto_uid = po2.oto_parent and po1.oto_depth=1)
			WHERE po2.oto_pcode='" . $code . "' and po2.oto_depth=2 ORDER BY po2.oto_uid ASC
		";
		$pores = _MQ_assoc($poque);
		foreach( $pores as $k=>$por ){
			$arr_option_data[$por[oto_uid]]['option_name1'] = $por[po1_poptionname];
			$arr_option_data[$por[oto_uid]]['option_name2'] = $por[oto_poptionname];
			$arr_option_data[$por[oto_uid]]['option_supplyprice'] = $por[oto_poptionpurprice];
			$arr_option_data[$por[oto_uid]]['option_price'] = $por[oto_poptionprice];
			$arr_option_data[$por[oto_uid]]['option_cnt'] = $por[oto_cnt];
		}
	}
	else if($r[option_type_chk] == "1depth") {
		$poque = " SELECT * FROM odtProductOption WHERE oto_pcode='" . $code . "' and oto_depth=1 ORDER BY oto_uid ASC ";
		$pores = _MQ_assoc($poque);
		foreach( $pores as $k=>$por ){
			$arr_option_data[$por[oto_uid]]['option_name1'] = $por[oto_poptionname];
			$arr_option_data[$por[oto_uid]]['option_supplyprice'] = $por[oto_poptionpurprice];
			$arr_option_data[$por[oto_uid]]['option_price'] = $por[oto_poptionprice];
			$arr_option_data[$por[oto_uid]]['option_cnt'] = $por[oto_cnt];
		}
	}
?>
