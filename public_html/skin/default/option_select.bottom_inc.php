<?PHP

	// 전체 재고 확인
	$cnt_que = " select ifnull(sum(otpo_cnt),0) as sum from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and otpo_is_addoption != 'Y' ";
	$cnt_r = _MQ($cnt_que);
	if($r['stock'] < $cnt_r['sum']) {
		echo "error4"; //재고량이 부족합니다.
		exit;
	}

	if(!$arr_option_data[$app_uid]['option_cnt'] && $option_type_chk == "none") {
		$arr_option_data[$app_uid]['option_cnt'] = $r['stock'];
	}


	echo "<ul class='add_option_ess'>";

	$price_sum = 0;
	$cnt_sum = 0;
	$sque = " select * from odtTmpProductOption where otpo_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' order by otpo_uid asc ";  // LMH002 (order by 변경)
	$sres = _MQ_assoc($sque);
	if( sizeof($sres) > 0 ){
		foreach( $sres as $k=>$sr ){
			$is_addoption = ($sr['otpo_is_addoption']=="Y" ? "add_" : "" );
			echo "
							<li ".($sr['otpo_is_addoption']=="Y" ? " class='option_ess' " : null ).">
								<span class='option_name'>".$sr['otpo_poptionname']." ".$sr['otpo_poptionname2']." ".$sr['otpo_poptionname3']." ".
								$br.$add_option_name_1.$add_option_name_2.$add_option_name_3.$add_option_name_4.$add_option_name_5.$add_option_name_6.$add_option_name_7.$add_option_name_8.$add_option_name_9.$add_option_name_10.
								"</span>
								<span class='updown_box'>
									<input type='text' name='' class='updown_input' value='".$sr['otpo_cnt']."' ID='input_cnt_".$is_addoption."".$sr['otpo_uid']."' readonly />
									<span class='updown'><a href='#none' onclick=\"".$is_addoption."option_select_update('up' , '" . $sr['otpo_uid'] . "','" . $sr['otpo_pcode'] . "')\" class='btn_up' title='더하기'></a><a href='#none' onclick=\"".$is_addoption."option_select_update('down' , '" . $sr['otpo_uid'] . "','" . $sr['otpo_pcode'] . "')\" class='btn_down' title='빼기'></a></span>
								</span>
								<span class='option_price'>" . number_format(($sr['otpo_pprice'] + $sr['otpo_poptionprice']) * $sr['otpo_cnt']) . "원</span>
								<a href='#none' onclick=\"option_select_del('" . $sr['otpo_uid'] . "','".$sr['otpo_pcode']."')\" class='btn_delete' title='옵션삭제'><img src='/pages/images/view_option_delete.gif' alt='옵션삭제' /></a>
							</li>
			";
			$add_option_name_1='';$add_option_name_2='';$add_option_name_3='';$add_option_name_4='';$add_option_name_5='';$add_option_name_6='';$add_option_name_7='';$add_option_name_8='';$add_option_name_9='';$add_option_name_10='';
			$price_sum += ($sr['otpo_pprice'] + $sr['otpo_poptionprice']) * $sr['otpo_cnt'];
			$cnt_sum += $sr['otpo_cnt'];
		}
	}
	else {
		/*echo "
			<li>
				<span class='option_name' style='text-align:center;width:100%!important'>구매하실 상품 옵션을 선택해 주시기 바랍니다.</span>
			</li>
		";*/
	}
	echo "
		</ul>
		<input type=hidden name=option_select_expricesum ID='option_select_expricesum' value='".$price_sum."'/>
		<input type=hidden name=option_select_cnt id='option_select_cnt' value='".$cnt_sum."'/>
	";

?>


