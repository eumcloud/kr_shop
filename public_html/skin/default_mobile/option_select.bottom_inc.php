<?PHP

	// 전체 재고 확인
	$cnt_que = " select ifnull(sum(otpo_cnt),0) as sum from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_is_addoption != 'Y' ";
	$cnt_r = _MQ($cnt_que);
	if($r[stock] < $cnt_r[sum]) {
		echo "error4"; //재고량이 부족합니다.
		exit;
	}

	if(!$arr_option_data[$app_uid]['option_cnt'] && $option_type_chk == "none") {
		$arr_option_data[$app_uid]['option_cnt'] = $r[stock];
	}


	echo "<ul>";

	$price_sum = 0;
	$cnt_sum = 0;
	$sque = " select * from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' order by otpo_uid asc "; // LMH002 (order by 변경)
	$sres = _MQ_assoc($sque);
	if( sizeof($sres) > 0 ){
		foreach( $sres as $k=>$sr ){
			$is_addoption = ($sr[otpo_is_addoption]=="Y" ? "add_" : "" );
			$option_name1 = $sr[otpo_poptionname].($sr[otpo_poptionname2]?"<span class='divi'></span> ":"");
			$option_name2 = $sr[otpo_poptionname2].($sr[otpo_poptionname3]?"<span class='divi'></span> ":"");
			$option_name3 = $sr[otpo_poptionname3];
			$prefix = $sr[otpo_is_addoption]=="Y"?"추가. ":"선택 ".($k+1).". ";
			echo "
				<li ".($sr['otpo_is_addoption']=="Y" ? " class='option_ess' " : null ).">
					<div class='opt_name'>".$prefix.$option_name1.$option_name2.$option_name3."
					</div>
					<div class='btn_updown'>
						<a href='#none' onclick=\"".$is_addoption."option_select_update('down','" . $sr[otpo_uid] . "','" . $sr[otpo_pcode] . "');return false;\" class='btn_minus' ></a>
						<input type='text' id='input_cnt_".$is_addoption."".$sr[otpo_uid]."' value='".$sr[otpo_cnt]."' readonly>
						<a href='#none' onclick=\"".$is_addoption."option_select_update('up','" . $sr[otpo_uid] . "','" . $sr[otpo_pcode] . "');return false;\" class='btn_plus'></a>
					</div>
					<div class='right'>
						<div class='price'>".number_format(($sr[otpo_pprice] + $sr[otpo_poptionprice]) * $sr[otpo_cnt])."<em>원</em></div>
						<a href=\"javascript:option_select_del('" . $sr[otpo_uid] . "','".$sr[otpo_pcode]."')\"  class='btn_del'></a>
					</div>
				</li>
			";
			$price_sum += ($sr[otpo_pprice] + $sr[otpo_poptionprice]) * $sr[otpo_cnt];
			$cnt_sum += $sr[otpo_cnt];
		}
	}

	echo "
		</ul>
		<input type='hidden' name='option_select_expricesum' ID='option_select_expricesum' value='{$price_sum}'>
		<input type='hidden' name='option_select_cnt' id='option_select_cnt' value='{$cnt_sum}'>
	";

?>


