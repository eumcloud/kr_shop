<?PHP

	include_once("inc.php");

    $toDay = date("YmdHis");
	$fileName = "order2excel";

    ## Exel 파일로 변환 #############################################
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");



	if($_mode == "search_excel") {
		if( !$_search_que ){
			error_msg("잘못된 접근입니다.");
		}
		$s_query = enc('d',$_search_que);
	}
	else {
		if( sizeof($OpUid) == 0 ){
			error_msg("주문상품을 선택하시기 바랍니다.");
		}
		$s_query = " where op.op_uid in ('". implode("','" , $OpUid) ."') ";
	}


    echo "
		<TABLE border=1>
			<TR>
				<th>SerialNum</th>
				<th>주문번호</th>
				<th>상품명</th>
				<th>수량</th>
				<th>주문가격</th>
				". 
					(
						$ordertype == "coupon" ? 
							"<th>쿠폰번호</th>" : 
							"<th>배송비용</th>
							<th>택배업체</th>
							<th>송장번호</th>
							".($reserve_del == 'Y'?'<th>배송요청일</th>':null)."
							<th>배송일시</th>"
					) 
				."
				<th>주문자</th>
				<th>이메일</th>
				<th>전화번호</th>
				<th>핸드폰번호</th>
				<th>수령인</th>
				<th>수령인전화</th>
				<th>수령인핸드폰</th>
				<th>수령인이메일</th>
				<th>새 우편번호</th>
				<th>우편번호</th>
				<th>주소</th>
				<th>도로명주소</th>
				<th>배송메세지</th>
				<th>주문일시</th>
				<th>결제일시</th>
			</TR>
	";

    // 현 페이지 주문번호 추출
    $que = "
        SELECT 
			op.*, o.* 
		FROM odtOrderProduct as op 
        left join odtOrder as o on ( o.ordernum=op.op_oordernum )
        " . $s_query . "
	";
    $res = _MQ_assoc($que);
	foreach($res as $sk=>$sv){

		// --- 옵션값 추출  ---
		$itemName = $sv[op_pname];
		$itemName .= ($sv[op_option1] ? " (".($sv[op_is_addoption]=="Y" ? "추가" : "선택").":".$sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3].")" : "");// 해당상품에 대한 옵션내역이 있으면

		// --- 쿠폰 추출 ---
		$arr_coupon = array();
		if($ordertype == "coupon" ){			
			$coupon_assoc = _MQ_assoc("select * from odtOrderProductCoupon where opc_opuid = '".$sv[op_uid]."'");
			foreach($coupon_assoc as $ssk=>$coupon_row){
				$arr_coupon[$ssk] = $coupon_row[opc_expressnum] . "(". ($coupon_row[opc_status] == "대기" ? "미사용" : $coupon_row[opc_status]) .")";
			}
		}

		echo "
			<TR>
				<td>". $sv[op_uid] ."</td>
				<td>". $sv[op_oordernum] ."</td>
				<td>". $itemName ."</td>
				<td>".$sv[op_cnt]."</td>
				<td>". number_format(($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt]) ."원</td>
				". 
					(
						$ordertype == "coupon" ? 
							"<td>". implode(", " , array_values($arr_coupon)) ."</td>" : 
							"
								<td>". number_format($sv[op_delivery_price] + $sv[op_add_delivery_price]) ."원</td>
								<td>".$sv[op_expressname]."</td>
								<td style=\"mso-number-format:'\@'\">".$sv[op_expressnum]."</td>
								".($reserve_del == 'Y'?'<td>'.$sv['delivery_date'].'</td>':null)."
								<td>".$sv[op_expressdate]."</td>
							"
					) 
				."
				<td>".$sv[ordername]."</td>
				<td>".$sv[orderemail]."</td>
				<td>".phone_print($sv[ordertel1],$sv[ordertel2],$sv[ordertel3])."</td>
				<td>".phone_print($sv[orderhtel1],$sv[orderhtel2],$sv[orderhtel3])."</td>
				<td>". ( $ordertype == "coupon" ? $sv[username] : $sv[recname] ) ."</td>
				<td>". ( $ordertype == "coupon" ? phone_print($sv[usertel1],$sv[usertel2],$sv[usertel3]) : phone_print($sv[rectel1],$sv[rectel2],$sv[rectel3]) ) ."</td>
				<td>". ( $ordertype == "coupon" ? phone_print($sv[userhtel1],$sv[userhtel2],$sv[userhtel3]) : phone_print($sv[rechtel1],$sv[rechtel2],$sv[rechtel3]) ) ."</td>
				<td>". ( $ordertype == "coupon" ? $sv[useremail] : $sv[recemail] ) ."</td>
				<td>".$sv[reczonecode] ."</td>
				<td>".$sv[reczip1]."-".$sv[reczip2]."</td>
				<td>".$sv[recaddress]." ". $sv[recaddress1] ."</td>
				<td>".$sv[recaddress_doro] ."</td>
				<td>".$sv[comment]."</td>
				<td>".$sv[orderdate]."</td>
				<td>".$sv[paydate]."</td>
			</TR>
		";
	}

	echo "</table>";
?>