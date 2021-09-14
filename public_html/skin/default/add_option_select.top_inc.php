<?PHP
	// - 상품추출 ---
    $que = "select * from odtProduct where code = '".$code."' ";
    $r = _MQ($que);

    $arr_option_data = array();

	$poque = "
		SELECT 
			po2.*,
			po1.pao_uid as po1_uid, po1.pao_poptionname as po1_poptionname
		FROM odtProductAddoption as po2 
		inner join odtProductAddoption as po1 on ( po1.pao_uid = SUBSTRING_INDEX(po2.pao_parent,',',1) and po1.pao_depth=1)
		WHERE po2.pao_pcode='" . $code . "' and po2.pao_depth=2 ORDER BY po2.pao_uid ASC
	";
	$pores = _MQ_assoc($poque);
	foreach( $pores as $k=>$por ){
		$arr_option_data[$por[pao_uid]]['option_name1'] = $por[po1_poptionname];
		$arr_option_data[$por[pao_uid]]['option_name2'] = $por[pao_poptionname];
		$arr_option_data[$por[pao_uid]]['option_supplyprice'] = $por[pao_poptionpurprice];
		$arr_option_data[$por[pao_uid]]['option_price'] = $por[pao_poptionprice];
		$arr_option_data[$por[pao_uid]]['option_cnt'] = $por[pao_cnt];
	}
?>
