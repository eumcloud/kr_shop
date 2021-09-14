<?PHP

	// 페이지 표시 - 주문 취소목록 : 주문 전체목록 구분
	$app_current_link = ( $_GET[_mode] == "cancellist" ? "/totalAdmin/_ordercancel.list.php" : "/totalAdmin/_order.list.php" );

	include_once("inc.header.php");

	$r = _MQ("SELECT * FROM odtOrder WHERE ordernum='" . $ordernum . "'");

	$orderidTemp = ($r[member_type] == "member" ? $r[orderid] : "<span style='color:red;'>비회원</FONT>");

	$OrderSumpriceD = number_format($r[sumprice]);
	$OrderDeliveryD = number_format($r[dPrice]);
	$OrderTotalPriceD = number_format($r[tPrice]);
	$OrderUsedpointD = number_format($r[gPrice]);
	$OrderGetpointD = number_format($r[gGetPrice]);
	$OrderDate = date("Y년 m월 d일 H시 i분",strtotime($r[orderdate]));

	$r[expressdate] = $r[expressdate] ? $r[expressdate] : date("Y-m-d");

?>


<form name=frm method=post action="_order.pro.php">
<input type=hidden name=_mode value='modify'>
<input type=hidden name=ordernum value='<?=$ordernum?>'>
<input type=hidden name=code value='<?=$code?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type="hidden" name="statusUpdate" value="yes">

				<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 주문상품정보</div>

				<!-- 리스트영역 -->
				<div class='content_section_inner'>
					<table class='list_TB' summary='리스트기본'>
						<thead>
							<tr>
								<th scope='col' class='colorset'>이미지</th>
								<th scope='col' class='colorset'>상품정보</th>
								<th scope='col' class='colorset'>가격</th>
								<th scope='col' class='colorset'>수량</th>
								<th scope='col' class='colorset'>주문금액</th>
								<th scope='col' class='colorset'>배송비</th>
								<th scope='col' class='colorset'>상태</th>
								<th scope='col' class='colorset'>정보</th>
								<th scope='col' class='colorset'>부분취소</th><!-- LMH001 -->
							</tr>
						</thead>
						<tbody>
<?PHP

	// 배송비 rowspan 적용을 위한 상품코드별 개수 추출
	$arr_pcodecnt = array();
	$tmpque = "
		select op_pcode , count(*) as cnt from odtOrderProduct as op
		left join odtProduct as p on (p.code=op.op_pcode)
		left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
		where op.op_oordernum='".$ordernum."'
		group by op_pcode
	";
	$tmpres = _MQ_assoc($tmpque);
	foreach( $tmpres as $k=>$v ){
		$arr_pcodecnt[$v[op_pcode]] = $v[cnt];
	}


    // 주문 상품정보 추출
    $totalPrice = 0 ;//총상품가격
    $sque = "
        select
            op.* , p.prolist_img , p.expire , cl.cl_title , cl.cl_price, o.orderstatus_step
        from odtOrderProduct as op
        left join odtProduct as p on (p.code=op.op_pcode)
        left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
        left join odtOrder as o on (o.ordernum = op.op_oordernum)
        where op.op_oordernum='".$ordernum."' order by p.code , op.op_delivery_price desc , op.op_is_addoption desc
    ";
    $sres = _MQ_assoc($sque);

		// 쿠폰별 가격 정산 추출 $v['ocs_ordernum']    2015-11-13 LCY002
		$cque = "
			select
				cl_price
			from odtOrderCouponLog
			where cl_oordernum='".$ordernum."'
		";
		$cres = _MQ_assoc($cque);
		$total_cprice=0;
		foreach($cres as $ck=>$cv){
			$total_cprice+=$cv['cl_price'];
		}



	// 현금영수증용 상품명 생성
	$cash_product_name = (count($sres)>1)?$sres[0][op_pname].'외 '.(count($sres)).'개':$sres[0][op_pname];

	foreach( $sres as $sk=>$sv ){
		// -- 이미지 ---
		$img_src	= app_thumbnail( "장바구니" , $sv );
		$img_src = @file_exists("/upfiles/product/" . $img_src) ? $img_src : $sv[prolist_img];

		// -- 추가옵션 ---
		$add_option = "";
		if($sv[op_add_option1]||$sv[op_add_option2]||$sv[op_add_option3]||$sv[op_add_option4]||$sv[op_add_option5]||$sv[op_add_option6]||$sv[op_add_option7]||$sv[op_add_option8]||$sv[op_add_option9]||$sv[op_add_option10]) {
			if($sv[op_add_option1_name]) { $add_option .= '['.$sv[op_add_option1_name].':'.$sv[op_add_option1].']&nbsp;'; }
			if($sv[op_add_option2_name]) { $add_option .= '['.$sv[op_add_option2_name].':'.$sv[op_add_option2].']&nbsp;'; }
			if($sv[op_add_option3_name]) { $add_option .= '['.$sv[op_add_option3_name].':'.$sv[op_add_option3].']&nbsp;'; }
			if($sv[op_add_option4_name]) { $add_option .= '['.$sv[op_add_option4_name].':'.$sv[op_add_option4].']&nbsp;'; }
			if($sv[op_add_option5_name]) { $add_option .= '['.$sv[op_add_option5_name].':'.$sv[op_add_option5].']&nbsp;'; }
			if($sv[op_add_option6_name]) { $add_option .= '['.$sv[op_add_option6_name].':'.$sv[op_add_option6].']&nbsp;'; }
			if($sv[op_add_option7_name]) { $add_option .= '['.$sv[op_add_option7_name].':'.$sv[op_add_option7].']&nbsp;'; }
			if($sv[op_add_option8_name]) { $add_option .= '['.$sv[op_add_option8_name].':'.$sv[op_add_option8].']&nbsp;'; }
			if($sv[op_add_option9_name]) { $add_option .= '['.$sv[op_add_option9_name].':'.$sv[op_add_option9].']&nbsp;'; }
			if($sv[op_add_option10_name]) { $add_option .= '['.$sv[op_add_option10_name].':'.$sv[op_add_option10].']'; }
		}
		// -- 추가옵션 ---


		// 유효기간
		unset($expire);
		if($sv[expire]) {
			$expire = "<span style='display:block'>유효기간 :  ".$sv[expire]." 까지 </span>";
		}

		// -- 쿠폰정보 ---
		unset($coupon_html,$coupon_html_body,$use_cnt,$notuse_cnt);
		if($sv[op_orderproduct_type] == "coupon") {
			$coupon_assoc = _MQ_assoc("select * from odtOrderProductCoupon where opc_opuid = '".$sv[op_uid]."'");
			if(sizeof($coupon_assoc) < 1) {
				$coupon_html_body = "결제가 확인되면 쿠폰이 발급됩니다.";
			}
			foreach($coupon_assoc as $coupon_key => $coupon_row) {

				// 미사용, 사용 쿠폰 개수
				if($coupon_row[opc_status] == "대기") {
					$notuse_cnt++;
					$coupon_html_body .="<span  style='display:block'><span class='coupon_num'>".$coupon_row[opc_expressnum]." <span class='shop_state_pack'><span class='orange' style='padding:0px 7px!important'>미사용</span></span></span><a href='/skin/".$row_setup[P_SKIN]."/mypage.order.pro.php?_mode=coupon_sms_resend&opcuid=".$coupon_row[opc_uid]."' class='coupon_sms' style='float:right' target='common_frame'>[문자발송]</a></span>";
				}
				else if($coupon_row[opc_status] == "사용") {
					$coupon_html_body .="<span  style='display:block'><span class='coupon_num'>".$coupon_row[opc_expressnum]." <span class='shop_state_pack' ><span class='light' style='padding:0px 7px!important'>사용</span></span></span></span>";
					$use_cnt++;
				}
				else if($coupon_row[opc_status] == "취소") {
					$coupon_html_body .="<span  style='display:block'><span class='coupon_num'>".$coupon_row[opc_expressnum]." <span class='shop_state_pack' ><span class='dark' style='padding:0px 7px!important'>취소</span></span></span></span>";
				}
			}
			$coupon_html .="
				<div class='option_box'>
					<div class='pro_option'>
						" . $expire . "
						" . $coupon_html_body . "
					</div>
				</div>
			";
		}
		// -- 쿠폰정보 ---

		// -- 배송상품정보 ::: 택배, 송장, 발송일 표기 ---
		if($sv[op_orderproduct_type] == "product" && $sv[op_delivstatus] == "Y" ) {
			$coupon_html .="
				<div class='option_box'>
					<div class='pro_option'>
						<span  style='display:block'><span class='coupon_num'>택배사 : ". $sv[op_expressname] ."</span></span>
						<span  style='display:block'><span class='coupon_num'>송장번호 : ". $sv[op_expressnum] ."</span></span>
						<span  style='display:block'><span class='coupon_num'>발송일 : ". substr($sv[op_expressdate],0, 10) ."</span></span>
					</div>
				</div>
			";
		}
		// -- 배송상품정보 ---

		// -- 발송여부 --- LMH001
		$app_status = "<li style='clear:both;display:inline; float:left; padding-top:3px;'><span class='shop_state_pack'>";

		if($sv[op_cancel]=='Y') { $app_status .= "<span class='gray'>주문취소</span>"; }
		else {
			if($sv[op_orderproduct_type] == "product") {
				$app_status .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발송완료</span>" : "<span class='lightgray'>발송대기</span>");
			}
			else {
				$app_status .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발급완료</span>" : "<span class='lightgray'>발급대기</span>");
			}
		}
		$app_status .= "</span></li>";
		// -- 발송여부 ---


		// -- 배송비 ---
		if($sv[op_orderproduct_type] != "product") {	// 배송적용 상품이 아니면
			$delivery_print = "해당없음";
			$add_delivery_print = "";
		}
		else {
			$delivery_print = ($sv[op_delivery_price] > 0 && $delivery_print != "무료배송") ? number_format($sv[op_delivery_price])."원" : "-"; // 배송정보.
			$add_delivery_print = ($sv[op_add_delivery_price] ? "<br>추가배송비 : +".number_format($sv[op_add_delivery_price])."원" : "") ;// 추가배송비 여부
		}
		// -- 배송비 ---

		// -- 배송상태 ---
		if($prev_pcode != $sv[op_pcode]) {
			$delivery_print =  "<td rowspan='".$arr_pcodecnt[$sv[op_pcode]]."'>".$delivery_print."".$add_delivery_print."</td>";
		}
		else {
			$delivery_print = "";
		}
		$prev_pcode = $sv[op_pcode];
		// -- 배송상태 ---


		// -- 진행상태 ---
		$status_print = "<li style='clear:both;display:inline; float:left; padding-top:3px;'><span class='shop_state_pack'>";
		if($sv[op_delivstatus] == "N") {
			$status_print .= $arr_o_status[$sv[orderstatus_step]] ;
		}
		else {
			if($sv[op_orderproduct_type] == "coupon") {
				if($notuse_cnt > 0) $status_print .= "<span class='orange'>미사용(".$notuse_cnt."개)</span>";
				if($use_cnt > 0) 	$status_print .= "<span class='light'>사용(".$use_cnt."개)</span>";
			}
			else {
				$status_print = $arr_o_status["발송완료"];
				$app_delivery_link = $arr_delivery_company[$sv[op_expressname]] . rm_str($sv[op_expressnum]);
				$status_print .= "<br><B><a href='".$app_delivery_link."' target='_blank' title='' >[배송조회]</a></B>";
			}
		}
		$status_print .= "</span></li>";
		// -- 진행상태 ---


		// -- 변수적용 ---
		$totalPrice += ($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt] ;//총상품가격
		$totalClprice += ($sv[cl_price] > 0 ? $v[cl_price] : 0 );  //총 상품별 사용 쿠폰가격
		$totadlPrice += $sv[op_delivery_price] + $sv[op_add_delivery_price] ;//총배송비


		// 부분취소 버튼 LMH001
		if($r[paystatus]=="Y" && $sv[op_is_addoption]!="Y") {
		switch($sv[op_cancel]) {
			case "Y": // 취소완료
				$_cancel_btn = "
				<div class='btn_line_up_center'>
					<li style='clear:both;display:inline; float:left; padding-top:3px;'><span class='shop_state_pack'><span class='red'>취소완료</span></span></li>
				</div>
				";
			break;
			case "R": // 취소요청중
				$_cancel_btn = "
				<div class='btn_line_up_center'>
					<li style='clear:both;display:inline; float:left; padding-top:3px;'><span class='shop_state_pack'><span class='lightgray'>취소요청중</span></span></li>
				</div>
				";
			break;
			case "N":
				$_cancel_btn = $_GET['_mode'] != "cancellist" ? "
				<div class='btn_line_up_center'>
					<span class='shop_btn_pack'><a href='#none' style='line-height:24px;' onclick='return false;' class='product_cancel input_small gray' data-ordernum='".$sv[op_oordernum]."' data-opuid='".$sv[op_uid]."'>부분취소</a></span>
				</div>
				" : "-";
			break;
		}} else { $_cancel_btn = "-"; }


		echo "
			<tr>
				<td>". ($img_src ? "<img src='" . replace_image('/upfiles/product/'.$img_src) . "' style='width:100px;'>" : "-") ."</td>
				<td style='text-align:left; padding:10px;'>
					<B>" . stripslashes($sv[op_pname]) . "</B>
					" . ($sv[op_option1] ? "<br>".($sv[op_is_addoption]=="Y" ? "추가옵션" : "선택옵션")." : " . trim($sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3]) :  "<br>옵션없음" ) . "
					" . ($sv[cl_title] ? "<br>적용쿠폰 : ".$sv[cl_title]." ( ".number_format($sv[cl_price])."원 할인" :  "" ) . "
					" . $coupon_html . "
				</td>
				<td>" . number_format($sv[op_pprice] + $sv[op_poptionprice]) . "원</td>
				<td><b>" . $sv[op_cnt] . "</b>개</td>
				<td>" . number_format(($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt]) . "원</td>
				" . $delivery_print . "
				<td><div class='btn_line_up_center'>" . $app_status . "</div></td>
				<td><div class='btn_line_up_center'>" . $status_print. "</div></td>
				<td>". $_cancel_btn ."</td><!-- LMH001 -->
			</tr>
		";
	}


	echo "
					<tr>
						<td colspan=10 style='padding:10px; text-align:right;'>
							<b>총합계금액</b> :
							   총상품가격(<font color='EE0016'>" . number_format($totalPrice) . "원</font>)
							+ 총배송비(<font color='EE0016'>" . number_format($r[dPrice]) . "원</font>)
							- 총할인금액(<font color='EE0016'>" . number_format($r[sPrice]) . "원</font>)
							=
							<font color='FF0000' style='font-size:16;LETTER-SPACING:-0.02em;font-family:굴림;'><b>
							" . number_format($r[tPrice]) . "원</b></font> ,
							적립금 :
							<font color='005190' style='font-size:13px'><b>" . number_format($r[gGetPrice]) . "원</b></font>
						</td>
					</tr>";

		if($r['sPrice']>0){  // 할인 상세내역 표시 2015-11-13 LCY002
		echo "          <tr>
							<td colspan=10 style='padding:10px; text-align:right;'>
								<b>할인상세내역</b> :
									쿠폰(<font color='EE0016'>" . number_format($total_cprice) . "원</font>)
								+   포인트(<font color='EE0016'>" . number_format($r['gPrice']) . "원</font>)
								+   프로모션코드(<font color='EE0016'>" . number_format($r['o_promotion_price']) . "원</font>)
								=
								<font color='FF0000' style='font-size:16;LETTER-SPACING:-0.02em;font-family:굴림;'><b>
								" . number_format($r['sPrice']) . "원</b></font>
							</td>
						</tr>
			";

		}


	echo "
				</tbody>
			</table>
		</div>
	";

?>




				<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 관리자 관리내용</div>
				<!-- 검색영역 -->
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<td class="article">관리자 관리내용</td>
								<td class="conts">
									<textarea name="comment1" cols="80" rows="3" class="input_text" style="width:100%;height:100px;" ><?=stripslashes($r[comment1])?></textarea>
								</td>
							</tr>
						</tbody>
					</table>
			</div>




			<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 결제정보</div>
			<!-- 검색영역 -->
			<div class="form_box_area">
				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">총결제금액</td>
							<td class="conts"><b><?=$OrderTotalPriceD?>원</b></td>
						</tr>
<?PHP
	// -- 취소상태 표시 ---
	if($r[canceled] == "Y"){
		echo "
						<tr>
							<td class='article'>주문상태</td>
							<td class='conts'>&nbsp;<font color='FF0000'><b>주문취소</b></font> (". date("Y년 m월 d일 H시 i분 s초",$r[canceldate]) .")</td>
						</tr>
		";
	}
	else {
		echo "<tr>
			<td class='article'>강제취소</td>
			<td class='conts'><span class='shop_btn_pack'><input type=button value='강제취소' class='input_small red' onclick=\"if(confirm('PG관리자에서 직접 주문을 취소하였거나 일부 오류로 강제 취소를 할 경우 사용 바랍니다.\\n\\n계속하시겠습니까?'))  document.location.href = '_order.pro.php?_mode=force_cancel&ordernum=".$ordernum."&_PVSC=".$_PVSC."';\"></span></td>
		</tr>";
	}

	if($r[tPrice] > 0) {
		echo "
						<tr>
							<td class='article'>결제상태</td>
							<td class='conts'>" . (
								$r[paystatus] == "Y" ?
									"&nbsp;<font color='FF0000'><b>결제확인</b></font>"
									:
									( !in_array( $r[paymethod] , array("B" , "E"))|| $r[canceled] == "Y" ?
										"&nbsp;<font color='FF0000'><b>미결제</b></font>"
										:
										"
											<label><input type='radio' name='paystatus' value='Y' " . ($r[paystatus]=='Y' ? "checked" : "") . "> 결제확인</label>&nbsp;&nbsp;&nbsp;
											<label><input type='radio' name='paystatus' value='N' " . ($r[paystatus]!='Y' ? "checked" : "") . "> 결제미확인</label>
										" . _DescStr("결제확인 시 쿠폰상품의 경우 자동발급되며, 그에 따라 메일이 발송되며 문자는 관리자 설정에 따릅니다.")
										. "<input type=hidden name=_get_tax value='".$r[taxorder]."'/>"
									)
							)  ."</td>
						</tr>
		";
	}
?>
						<tr>
							<td class="article">결제수단</td>
							<td class="conts">
								<b><?=$arr_paymethod_name[$r[paymethod]]?></b>
<?PHP
	if( in_array( $r[paymethod] , array("H" , "L" , "C") ) ) {
		echo $r[paystatus]=="Y" ? "(승인번호: <b>".$r[authum]."</b>)" : "<font color=red>(미결제)</font>";
	}
?>
							</td>
						</tr>
<?PHP
	if( in_array( $r[paymethod] , array("B") ) ) {  // 무통장 입금 정보

		$OrderBankDiv = explode('/',$r[paybankname]);
		$BankName = $OrderBankDiv[0];
		$BankPerson = $OrderBankDiv[1];

		echo "
						<tr>
							<td class='article'>입금은행</td>
							<td class='conts'>" . $BankName . " " . $r[paybanknum] . " " . $BankPerson . "</td>
						</tr>
						<tr>
							<td class='article'>결제예정일</td>
							<td class='conts'>" . $r[paydatey] . "-" . $r[paydatem] . "-" . $r[paydated] . "</td>
						</tr>
						<tr>
							<td class='article'>입금인명</td>
							<td class='conts'>" . $r[payname] . "</td>
						</tr>
		";
	}
?>

<?PHP
    if( in_array( $r[paymethod] , array("V","E") ) ) { // 가상계좌 입금 정보

        $v_bank = _MQ("select ool_tid, ool_date, ool_account_num, ool_deposit_name, ool_bank_name, ool_escrow_fee from odtOrderOnlinelog where ool_ordernum='$ordernum' and ool_type='R'");

        // 수수료있을시 수수료표시
        if($v_bank['ool_escrow_fee']>0){
            echo "
                        <tr>
                            <td class='article'>에스크로수수료</td>
                            <td class='conts'>" . number_format($v_bank[ool_escrow_fee]) . "원</td>
                        </tr>
            ";
        }

        echo "
                        <tr>
                            <td class='article'>입금은행</td>
                            <td class='conts'>" . $v_bank[ool_bank_name] . " " . $v_bank[ool_account_num] . " " . $v_bank[ool_deposit_name] . "</td>
                        </tr>
                        <tr>
                            <td class='article'>결제예정일</td>
                            <td class='conts'>" . date('Y-m-d',strtotime($v_bank[ool_date])+$row_setup[P_V_DATE]*86400) . "</td>
                        </tr>
                        <tr>
                            <td class='article'>입금인명</td>
                            <td class='conts'>" . $v_bank[ool_deposit_name] . "</td>
                        </tr>
        ";
    }
?>
<? if($r[paystatus]=='Y' && in_array($r[paymethod],array( "V" ))) { ?>
						<tr>
							<td class="article">가상계좌 환불정보 - 은행</td>
							<td class="conts"><?=_InputSelect( "cancel_bank" , array_keys($ool_bank_name_array) , $r['cancel_bank'] , "" , array_values($ool_bank_name_array) , "-은행-")?></td>
						</tr>
						<tr>
							<td class="article">가상계좌 환불정보 - 계좌번호</td>
							<td class="conts"><input type="text" name="cancel_bank_account" class="input_text" size="50" value="<?=$r['cancel_bank_account']?>"><?=_DescStr("숫자만 입력해주시기 바랍니다.")?></td>
						</tr>
<?}?>
<?
		if($r[paystatus]=='Y' && in_array($r[paymethod],array("B","V","E"))) { ?>
							<tr>
								<td class='article'>현금영수증</td>
								<td class='conts'>
									&nbsp;<label><input type=checkbox name=_get_tax value='Y' <?=$r[taxorder] == "Y" ? "checked" : NULL;?>> 현금영수증 발행을 신청합니다. </label>
									<?
										// 2016-09-09 수정 :: 가상계좌만 가능하게 수정함.
										if($r[taxorder]=='Y' && in_array($r[paymethod],array("V")) ){ if(in_array($row_setup[P_KBN],array('L','K','B'))) { // 올더게이트, 이니시스는 현금영수증 자동 발행기능 미제공
										$cash_status = _MQ("select * from odtOrderCashlog where ocs_ordernum = '{$ordernum}' order by ocs_uid desc limit 1");

										if(/*$r[o_paymethod]=='virtual'&&*/!in_array($r[orderstatus_step],array('결제취소','결제실패','결제대기','주문취소'))&&sizeof($cash_status)==0){ ?>&nbsp;<span id="cash_status"><a id="cash_issue">현금영수증 발행하기</a></span><? }
										//if($r[o_paymethod]=='online') { echo _DescStr('무통장입금의 경우 현금영수증 자동 발행기능을 지원하지 않습니다. 직접 발행해야 합니다.'); }

										// 현금영수증 출력 준비
										$CST_PLATFORM = $row_setup[P_MODE]; $CST_MID = $row_setup[P_ID]; $LGD_MID = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
									?>
									<div class="cash_container" style="display: <?=($r[taxorder]=='Y'||sizeof($cash_status)>0)?'block':'none'?>;">
										<div class="cash_log" style="border: 1px solid #ccc; padding: 10px; margin-top: 5px; <?=(sizeof($cash_status)==0?'display:none;':'')?>">
											<p><strong>현금영수증 발행 내역</strong></p>
											<?
												$cash_status_list = _MQ_assoc("select * from odtOrderCashlog where ocs_ordernum='{$ordernum}' order by ocs_uid");
												$cash_cancel_cnt = _MQ("select count(*) as cnt from odtOrderCashlog where ocs_ordernum='{$ordernum}' and ocs_method='CANCEL'");
												foreach($cash_status_list as $v) {
													$cash_del = $cash_cancel_cnt==0?" <span class='cash_cancel' style='display:inline-block; margin-left:10px; color: red; cursor: pointer;' data-tid='".$v['ocs_tid']."'>취소</span> ":'';
													echo
													"<p style='padding-top: 5px; display: block; margin-top: 5px; border-top: 1px solid #ddd;'><strong>"
													.(($v['ocs_method']=='AUTH')?"<span style='color:green;'>O</span> 발행":"<span style='color:red;'>X</span> 취소")."일:</strong> ".date('Y-m-d h:i',strtotime($v['ocs_date']))
													." / <strong>주문번호</strong>: ".$v['ocs_ordernum']
													." / <strong>현금영수증 승인번호</strong>: ".$v['ocs_cashnum']
													//." / <strong>소비자번호</strong>: ".$v['ocs_cardnum']
													." / <strong>금액</strong>: ".number_format($v['ocs_amount'])
													.$cash_del."</p>";
												}
											?>
										</div>
									</div>
									<? } else { echo _DescStr('현금영수증은 '.$arr_pg_type[$row_setup[P_KBN]].' 가맹점페이지에서 직접 발행해야 합니다.'); }} ?>
								</td>
							</tr>
	<? } ?>





<?PHP
	// 발송/발급상태
	echo "
					<tr>
						<td class='article'>결제/발송/발급상태</td>
						<td class='conts'>
							<span class='shop_state_pack'>" . $arr_o_status[$sv[orderstatus_step]] . "</span>
							" . ( in_array($r[order_type] , array("coupon" , "both")) && in_array($r[orderstatus_step] , array("결제확인" , "발급대기" , "발급완료")) ?
								"<br><br><span class='shop_btn_pack'><a type='button' class='small blue' href='#none' onclick=\"window.open('./_order.coupon_view.php?ordernum=" . $r[ordernum] . "','','width=850px,height=700px,scrollbars=yes');\">쿠폰발송메일보기</a></span>" :
								""
							) . "
						</td>
					</tr>
	";

?>

						<?php
						// SSJ 2018-05-16 :: 주문취소정보 노출 ---{
						if($r['canceled'] == 'Y'){
							// 환불정보 - 부분취소정보
							$arrCinfo = array();
							foreach($sres as $sk=>$sv){
								// 부분취소정보
								if($sv['op_cancel'] <> 'N'){
									$arrCinfo[strtotime($sv['op_cancel_rdate'])] = '[' . $sv['op_cancel_rdate'] . '] 부분취소 요청('. $sv['op_pname'] .') - ' . ($sv['op_cancel_mem_type']<>'admin'?'회원취소':'운영자취소');
									$arrCinfo[strtotime($sv['op_cancel_cdate'])] = '[' . $sv['op_cancel_cdate'] . '] 부분취소 완료('. $sv['op_pname'] .') - 운영자취소';
								}

							}
							ksort($arrCinfo);
						?>
						<tr>
							<td class="article">취소정보</td>
							<td class="conts">
								<?php if(count($arrCinfo) > 0){ ?>
									<?php echo implode('<br>', $arrCinfo); ?><br>
								<?php } ?>
								[<?php echo date('Y-m-d H:i:s', $r['canceldate']); ?>] 주문취소 - <?php echo ($r['cancel_mem_type']<>'admin'?'회원취소':'운영자취소'); ?>
							</td>
						</tr>
						<?php
						}
						//--- SSJ 2018-05-16 :: 주문취소정보 노출
						?>

					</tbody>
				</table>
			</div>

			<!-- 버튼영역 -->
			<div class="bottom_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack">
						<?=($_mode == "cancellist" ? "" : "<input type='submit' name='' class='input_large red' value='등록하기'>")?>
						<input type="button" name="" class="input_large gray" value="목록보기" onclick="location.href=('<?=($_mode == "cancellist" ? "_ordercancel.list.php" : "_order.list.php")?>?<?=enc("d" , $_PVSC)?>');">
					</span>
				</div>
			</div>
			<!-- 버튼영역 -->






			<div style=" margin-left:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold">◆ 주문자 정보</div>

			<!-- 검색영역 -->
			<div class="form_box_area">

				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>

						<tr>
							<td class="article">주문번호</td>
							<td class="conts"><b><?=$r[ordernum]?></b></td>
						</tr>
						<tr>
							<td class="article">주문일시</td>
							<td class="conts"><b><?=$OrderDate?></b></td>
						</tr>
						<tr style="display:none">
							<td class="article">배송비결제</td>
							<td class="conts"><?=($r[delchk]=="Y" ? "<b>배송비(착불)</b>" : ($r['dPrice']>0?"<b>배송비(선불)</b>":'<b>무료배송</b>'))?></td>
						</tr>
						<tr>
							<td class="article">주문자명</td>
							<td class="conts"><b><?=$r[ordername]?> </b> (<?=$orderidTemp?>)</td>
						</tr>
						<tr>
							<td class="article">전화번호</td>
							<td class="conts">
								<input type="text" name="ordertel1" class="input_text" size="4" maxlength="4" value="<?=$r[ordertel1]?>"> -
								<input type="text" name="ordertel2" class="input_text" size="4" maxlength="4" value="<?=$r[ordertel2]?>"> -
								<input type="text" name="ordertel3" class="input_text" size="4" maxlength="4" value="<?=$r[ordertel3]?>">
							</td>
						</tr>
						<tr>
							<td class="article">휴대폰번호</td>
							<td class="conts">
								<input type="text" name="orderhtel1" class="input_text" size="4" maxlength="4" value="<?=$r[orderhtel1]?>"> -
								<input type="text" name="orderhtel2" class="input_text" size="4" maxlength="4" value="<?=$r[orderhtel2]?>"> -
								<input type="text" name="orderhtel3" class="input_text" size="4" maxlength="4" value="<?=$r[orderhtel3]?>">
							</td>
						</tr>
						<tr>
							<td class="article">E-mail</td>
							<td class="conts"><input type="text" name="orderemail" class="input_text" size="50" value="<?=$r[orderemail]?>"></td>
						</tr>
					</tbody>
				</table>
			</div>




<?PHP
	// 쿠폰상품이 있을경우에만 노출.
	if($r[order_type] == "coupon" || $r[order_type] == "both") {
?>
			<div style=" margin-left:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold">◆ 사용자 정보</div>
			<!-- 검색영역 -->
			<div class="form_box_area">
				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">사용자 이름</td>
							<td class="conts"><input type="text" name="username" class="input_text" size="20" value="<?=$r[username]?>"></td>
						</tr>
						<tr>
							<td class="article">사용자 휴대폰</td>
							<td class="conts">
								<input type="text" name="userhtel1" class="input_text" size="4" maxlength="4" value="<?=$r[userhtel1]?>"> -
								<input type="text" name="userhtel2" class="input_text" size="4" maxlength="4" value="<?=$r[userhtel2]?>"> -
								<input type="text" name="userhtel3" class="input_text" size="4" maxlength="4" value="<?=$r[userhtel3]?>">
							</td>
						</tr>
						<tr>
							<td class="article">사용자 E-mail</td>
							<td class="conts"><input type="text" name="useremail" class="input_text" size="50" value="<?=$r[useremail]?>"></td>
						</tr>
					</tbody>
				</table>
			</div>
<?PHP
	}
?>






<?PHP
	// 배송상품이 있을경우에만 노출.
	if($r[order_type] == "product" || $r[order_type] == "both") {
?>
			<div style=" margin-left:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold">◆ 받는분 정보</div>
			<!-- 검색영역 -->
			<div class="form_box_area">
				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">받는분이름</td>
							<td class="conts"><input type="text" name="recname" class="input_text" size="20" value="<?=$r[recname]?>"></td>
						</tr>
						<tr>
							<td class="article">전화번호</td>
							<td class="conts">
								<input type="text" name="rectel1" class="input_text" size="4" maxlength="4" value="<?=$r[rectel1]?>"> -
								<input type="text" name="rectel2" class="input_text" size="4" maxlength="4" value="<?=$r[rectel2]?>"> -
								<input type="text" name="rectel3" class="input_text" size="4" maxlength="4" value="<?=$r[rectel3]?>">
							</td>
						</tr>
						<tr>
							<td class="article">휴대폰번호</td>
							<td class="conts">
								<input type="text" name="rechtel1" class="input_text" size="4" maxlength="4" value="<?=$r[rechtel1]?>"> -
								<input type="text" name="rechtel2" class="input_text" size="4" maxlength="4" value="<?=$r[rechtel2]?>"> -
								<input type="text" name="rechtel3" class="input_text" size="4" maxlength="4" value="<?=$r[rechtel3]?>">
							</td>
						</tr>
						<tr>
							<td class="article">받는분 E-mail</td>
							<td class="conts"><input type="text" name="recemail" class="input_text" size="50" value="<?=$r[recemail]?>"></td>
						</tr>
						<tr>
							<td class="article">우편번호</td>
							<td class="conts">
								<div class='btn_line_up_center'>
									<span class='shop_btn_pack'>
										<input type="text" name="_rzip1" id="_post1" class="input_text" size="5" maxlength="4" value="<?=$r[reczip1]?>"> -
										<input type="text" name="_rzip2" id="_post2" class="input_text" size="5" maxlength="4" value="<?=$r[reczip2]?>">
									</span>
									<span class='shop_btn_pack'><span class='blank_3'></span></span>
									<span class='shop_btn_pack'><a type='button' class='small blue' href='#none' onclick="new_post_view();">우편번호찾기</a></span>
								</div>
							</td>
						</tr>
						<tr>
							<td class="article">배송지 주소</td>
							<td class="conts">
								<input type="text" name="_raddress" id="_addr1" class="input_text" size="75" value="<?=$r[recaddress]?>"><br>
								<input type="text" name="_raddress1" id="_addr2" class="input_text" size="75" value="<?=$r[recaddress1]?>">
							</td>
						</tr>
						<tr>
							<td class="article">도로명 주소</td>
							<td class="conts">
								<input type="text" name="_raddress_doro" id="_addr_doro" class="input_text" size="75" value="<?=$r[recaddress_doro]?>">
							</td>
						</tr>
						<tr>
							<td class="article">새 우편번호</td>
							<td class="conts">
								<input type="text" name="_rzonecode" id="_zonecode" class="input_text" size="75" value="<?=$r[reczonecode]?>">
							</td>
						</tr>
						<?php
						# LDD018
						if($r['delivery_date'] != '0000-00-00') {
						?>
						<tr>
							<td class="article">배송 요청일</td>
							<td class="conts">
								<input type="text" name="delivery_date" class="input_text" size="10" value="<?=$r['delivery_date']?>" readonly>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td class="article">배송시 유의사항</td>
							<td class="conts"><textarea name="comment" cols="80" rows="3" class="input_text" style="width:100%;height:100px;" ><?=htmlspecialchars(stripslashes($r[comment]))?></textarea></td>
						</tr>
					</tbody>
				</table>
			</div>

<?PHP
	}
?>

			<!-- 버튼영역 -->
			<div class="bottom_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack">
						<?=($_mode == "cancellist" ? "" : "<input type='submit' name='' class='input_large red' value='등록하기'>")?>
						<input type="button" name="" class="input_large gray" value="목록보기" onclick="location.href=('<?=($_mode == "cancellist" ? "_ordercancel.list.php" : "_order.list.php")?>?<?=enc("d" , $_PVSC)?>');">
					</span>
				</div>
			</div>
			<!-- 버튼영역 -->
</form>

<? if($r[paymethod]=='V' && $r[paystatus]=='Y') { ?>
		<div style=" margin-left:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold">◆ 주문 취소하기</div>
			<!-- 검색영역 -->
			<form action="_order.pro.php" method="post" target="" name="refund">
			<div class="form_box_area">
				<input type="hidden" name="_mode" value="<?=($r[moneyback])?'moneyback':'cancel'?>"/>
				<input type="hidden" name="ordernum" value="<?=$r[ordernum]?>"/>
				<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"/>
				<? if($r[moneyback]=='환불완료') { ?>
				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
					<tr>
						<td class="article">환불완료</td>
						<td class="conts">
							<?=_DescStr($r[moneyback_comment]." 로 환불이 완료되었습니다. 자세한 사항은 <a href='_order_moneyback.list.php'>환불요청관리</a> 페이지에서 확인하세요.")?>
						</td>
					</tr>
					</tbody>
				</table>
				<? } else { ?>
				<? if($r[moneyback]=='환불요청') { ?>
				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
					<tr>
						<td class="article">환불요청중</td>
						<td class="conts">
							<?=$r[moneyback_comment]?>
							<?=_DescStr("환불 요청 중입니다. 아래에서 환불 계좌정보를 변경할 수 있습니다.")?>
							<?=_DescStr("자세한 사항은 <a href='_order_moneyback.list.php'>환불요청관리</a> 페이지에서 확인하세요.")?>
						</td>
					</tr>
					</tbody>
				</table>
				<? } ?>
				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">환불받을 은행</td>
							<td class="conts">
								<select class="input_text" name="bank_code">
								<option value="">- 선택 -</option>
								<?
									$ool_bank_name_array = array('39'=>'경남', '34'=>'광주', '04'=>'국민', '03'=>'기업', '11'=>'농협', '31'=>'대구', '32'=>'부산', '02'=>'산업', '45'=>'새마을금고', '07'=>'수협', '88'=>'신한', '26'=>'신한', '48'=>'신협', '05'=>'외환', '20'=>'우리', '71'=>'우체국', '37'=>'전북', '35'=>'제주', '81'=>'하나', '27'=>'한국씨티', '53'=>'씨티', '23'=>'SC은행', '09'=>'동양증권', '78'=>'신한금융투자증권', '40'=>'삼성증권', '30'=>'미래에셋증권', '43'=>'한국투자증권', '69'=>'한화증권');
									foreach($ool_bank_name_array as $k=>$v) {
								?>
								<option value="<?=$k?>"><?=$v?></option>
								<? } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="article">환불받을 계좌번호</td>
							<td class="conts"><input type="text" name="refund_account" class="input_text" value=""></td>
						</tr>
						<tr>
							<td class="article">환불계좌 예금주명</td>
							<td class="conts"><input type="text" name="refund_nm" class="input_text" value=""></td>
						</tr>
					</tbody>
				</table>
				<?=_DescStr("가상계좌 결제는 고객의 환불계좌 정보를 입력해야 취소됩니다.")?>
			<? } ?>
			</div>
			<!-- 버튼영역 -->
			<? if($r[moneyback]!='환불완료') { ?>
			<div class="bottom_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack">
						<input type="submit" name="" class="input_large gray submit_refund" value="주문취소">
					</span>
				</div>
			</div>
			<? } ?>
			<!-- 버튼영역 -->
		</form>
<? } ?>

<? include_once $_SERVER[DOCUMENT_ROOT]."/newpost/newpost.search.php"; ?>
<script>
<?
	$clque = "select * from odtOrderCardlog where oc_oordernum= '".$ordernum."'"; $clr = _MQ($clque);
	$company_info = _MQ("select * from odtCompany where serialnum='1'");
	$paymethod_convert = array('B'=>'online','C'=>'card','V'=>'virtual','L'=>'iche');
?>

// 현금영수증을 신청합니다. 버튼을 누르면 odtOrder 테이블의 taxorder 필드 업데이트
$('input[name=_get_tax]').on('click',function(){
	if($(this).is(':checked')) { $('.cash_container').css('display','inline-block'); var tax = 'Y'; } else { $('.cash_container').hide(); var tax = 'N'; }
	//$.post('_order.form.cashUpdate.php',{tax: tax, ordernum: '<?=$ordernum?>'});
	$.ajax({
		data: {tax:tax, ordernum: '<?=$ordernum?>'},
		type: 'POST',
		cache: false,
		url: '_order.form.cashUpdate.php',
		success: function() { window.location.reload(); }
	});
});

$('#cash_issue').on('click',function(e){ // 현금영수증 발행 버튼
	e.preventDefault();
	if (confirm('<?=$r[ordername]?>님 <?=$r[orderhtel1].'-'.$r[orderhtel2].'-'.$r[orderhtel3]?> 번호로 현금영수증 발행을 신청합니다.')) {
		$.ajax({
			data: {
				method:		'AUTH',
				ordernum:	'<?=$ordernum?>',
				paymethod:	'<?=$paymethod_convert[$r[paymethod]]?>',
				tid:		'<?=$clr[oc_tid]?>',
				member:		'<?=$r[orderid]?>',
				amount:		'<?=$r[tPrice]?>',
				num:		'<?=$r[orderhtel1].$r[orderhtel2].$r[orderhtel3]?>',
				use:		'1', // 발급용도 1 = 소득공제, 2 = 지출증빙
				product:	'<?=$cash_product_name?>', // 상품명
				store:		'<?=$company_info[number1]?>' // 상점 사업자등록번호
			},
			type: 'POST',
			cache: false,
			url: '/pages/totalCashReceipt.ajax.php',
			success: function(data) {
				if(data=='AUTH'){ // 작업에 성공했다면 진행 - AUTH = 현금영수증 발행, OK = 현금영수증 신청 완료
					$('#cash_status').remove();
					window.location.reload();
				} else if(data=='OK') {
					return false;
				} else { // 아니라면 오류 메세지
					alert('현금영수증 발행에 실패했습니다.'+data);
				}
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	} else {
		return false;
	}
});

$('.cash_cancel').on('click',function(e){ // 현금영수증 발행 버튼
	e.preventDefault();
	var tid = $(this).attr('data-tid');
	if (confirm('현금영수증 발행을 취소합니다.')) {
		$.ajax({
			data: {
				method:		'CANCEL',
				tid: 		tid,
				ordernum:	'<?=$ordernum?>',
				paymethod:	'<?=$paymethod_convert[$r[paymethod]]?>',
				member:		'<?=$r[orderid]?>',
				amount:		'<?=$r[tPrice]?>',
				num:		'<?=$r[orderhtel1].$r[orderhtel2].$r[orderhtel3]?>',
				use:		'1', // 발급용도 1 = 소득공제, 2 = 지출증빙
				product:	'<?=$cash_product_name?>', // 상품명
				store:		'<?=$company_info[number1]?>' // 상점 사업자등록번호
			},
			type: 'POST',
			cache: false,
			url: '/pages/totalCashReceipt.ajax.php',
			success: function(data) {
				if(data=='CANCEL'){ // 작업에 성공했다면 진행 - AUTH = 현금영수증 발행, OK = 현금영수증 신청 완료
					$('#cash_status').remove();
					window.location.reload();
				} else if(data=='OK') {
					return false;
				} else { // 아니라면 오류 메세지
					alert('현금영수증 취소에 실패했습니다.'+data);
				}
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	} else {
		return false;
	}
});

$("form[name=refund]").validate({
	ignore: "input[type=text]:hidden",
	rules: {
		bank_code: { required: true },
		refund_account: { required: true },
		refund_nm: { required: true },
	},
	messages: {
		bank_code: { required: "은행을 선택하세요" },
		refund_account: { required: "계좌번호를 입력하세요" },
		refund_nm: { required: "예금주명을 입력하세요" },
	}
});

</script>

<!-- ●●●●●●●●●● 부분취소신청 (티플형) LMH001 -->
<?
	$_member = _MQ(" select * from odtMember where id = '".$r[orderid]."' and userType = 'B' ");
?>
<div class="cm_ly_pop_tp" id="product_cancel_pop" style="display:none;width:500px;">

	<!--  레이어팝업 공통타이틀 영역 -->
	<div class="title_box">부분취소/환불 신청<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>

	<!-- 하얀색박스공간 -->
	<div class="inner_box">

        <!-- 설명글 -->
        <div class="top_txt">
            부분 취소할 상품을 꼭 다시한번 확인하시고,<br/>
            다음 정보를 입력해주시면 관리자의 확인 후 처리됩니다.

            <?php if( in_array($r['paymethod'],array('B','V')) ) { ?>
            <!-- 가상계좌 / 직접환불 안내문구 -->
            <div class="top_txt" style="margin-bottom:0;">
                <?php if( in_array($r['paymethod'],array('V')) ) { ?>
                <strong>가상계좌</strong>의 부분취소는 지원하지 않습니다.<br/>
                <?php } ?>
                <strong>직접환불</strong>은 PG사와 연동되지 않습니다.
                <br/>고객님의 계좌로 직접 환불처리 후 취소처리 해주시기 바랍니다.
            </div>
            <?php } ?>
        </div>

		<!-- 상품정보 -->
		<div class="this_item">
			<div class="thumb"><a href="#none" onclick="return false;"><img class="product_thumb" src="" alt="" /></a></div>
			<div class="info">
				<div class="info_title">부분취소 신청할 상품정보</div>
				<dl>
					<dt class="product_name"></dt>
					<dd style="margin-left:0;" class="product_option"></dd>
				</dl>
        <? // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>
        <div class="info_price">
            <span class="txt">상품금액 : <strong class="product_price">0</strong></span>
            <span class="bar" style='margin:2px 5px 0 5px;'></span>
            <span class="txt">배송비 : <strong class="delivery_price">0</strong></span>
            <span class="bar" style='margin:2px 5px 0 5px;'></span>
            <span class="txt">할인액 : <strong class="discount_price">0</strong></span>
        </div>
        <div class="info_price">
            <span class="txt">환불금액 : <strong class="return_price">0</strong></span>
        </div>
        <? // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>
			</div>
		</div>
		<!-- / 상품정보 -->

		<form name="product_cancel">
		<input type="hidden" name="mode" value="cancel"/><input type="hidden" name="ordernum" value=""/><input type="hidden" name="op_uid" value=""/><input type="hidden" name="cancel_mem_type" value="admin"/>
        <!-- 폼들어가는곳 -->
        <div class="form_box">
            <ul>
                <li>
                    <span class="opt">환불수단</span>
                    <div class="value">
                        <?php if(in_array($row_setup['P_KBN'],array('I','A','K','L','B','D'))) { ?>
                            <?php if( !in_array($r['paymethod'],array('C','L','G')) ) { ?>
                                <label class="save_check"><input type="radio" name="cancel_type" class="cancel_type_pg" checked value="pg"/>직접 환불</label>&nbsp;&nbsp;&nbsp;
                            <?php }else{ ?>
                                <label class="save_check"><input type="radio" name="cancel_type" class="cancel_type_pg" checked value="pg"/>PG사 결제 취소</label>&nbsp;&nbsp;&nbsp;
                            <?php } ?>
                        <?php } ?>
                        <label class="save_check"><input type="radio" name="cancel_type" class="cancel_type_point" value="point"/>적립금 환불</label>
                    </div>
                </li>
                <?php if( !in_array($r['paymethod'],array('C','L','G')) ) { ?>
                <li class='view_pg'><?php // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>
                    <span class="opt">환불계좌</span>
                    <div class="value">
                        <input type="text" name="cancel_bank_name" class="input_design icon_name" value="<?php echo $_member['cancel_bank_name']; ?>" placeholder="예금주" style="width:140px;box-shadow:0;border:0;"/>
                        <select name="cancel_bank" class="select_design" style="width:140px; margin-left:5px">
                            <?php foreach($ksnet_bank as $kk=>$vv) { ?>
                            <option value="<?php echo $kk; ?>" <?php echo ($_member['cancel_bank']==$kk?'selected':''); ?>><?php echo $vv; ?></option>
                            <?php } ?>
                        </select>
                        <input type="text" name="cancel_bank_account" class="input_design icon_bank" value="<?php echo $_member['cancel_bank_account']; ?>" placeholder="계좌번호" style="box-shadow:0;border:0;"/>
                        <!-- <label class="save_check"><input type="checkbox" name="save_myinfo" value="Y"/>나의 정보에 함께 저장하기</label> -->
                    </div>
                </li>
                <?php } ?>
                <li>
                    <span class="opt">전달내용</span>
                    <div class="value">
                        <textarea name="cancel_msg" rows="3" cols="" class="textarea_design" placeholder="관리자에게 전달할 내용이 있다면 입력하세요." ></textarea>
                    </div>
                </li>
            </ul>
        </div>
        <!-- / 폼들어가는곳 -->

		<!-- 레이어팝업 버튼공간 -->
		<div class="button_pack">
			<span class="lineup" style="display:inline-block;">
				<button type="submit" class="btn_md_black">취소신청<span class="edge"></span></button>
				<a href="#none" onclick="return false;" title="" class="close btn_md_white">닫기<span class="edge"></span></a>
			</span>
		</div>
		<!-- / 레이어팝업 버튼공간 -->
		</form>

	</div>
	<!-- / 하얀색박스공간 -->

</div>
<script>
$(document).ready(function(){

    // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC  -----------
    $('input[name=cancel_type]').on('change',function(){
        var type = $(this).val();
        if( type=='pg' ) { $('.view_pg').show(); } else { $('.view_pg').hide(); }
    });
    // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC  -----------

	$('.product_cancel').on('click',function(){
		var ordernum = $(this).data('ordernum'), op_uid = $(this).data('opuid'), $product_pop = $('#product_cancel_pop'), $product_form = $('form[name=product_cancel]');
		$.ajax({
			data: {'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'product'},
			type: 'POST',
			cache: false,
			url: '/pages/mypage.order.view.ajax.php',
			dataType: 'JSON',
			success: function(data) {
				if(data['result']=='OK'){
					$product_pop.find('.product_thumb').attr('src',data['data']['image']);
					$product_pop.find('.product_name').text(data['data']['name']);

					$product_pop.find('.product_price').text(data['data']['price']);
					$product_pop.find('.delivery_price').text(data['data']['delivery']);//배송비용
					$product_pop.find('.discount_price').text(data['data']['discount']);//할인비용 // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					$product_pop.find('.return_price').text(data['data']['return']);//환불금액

					if(data['data']['option']) {
						$product_pop.find('.product_option').text('옵션: ' + data['data']['option']);
						if(data['data']['addoption']) {
							$product_pop.find('.product_option').append('<br/>추가옵션: '+data['data']['addoption']);
						}
					} else { $product_pop.find('.product_option').text(''); }
					$product_form.find('input[name=ordernum]').val(ordernum);
					$product_form.find('input[name=op_uid]').val(op_uid);
					if(data['data']['pg_check']=='N') {
						$('input[name=cancel_type].cancel_type_pg').parent().hide();
						$('input[name=cancel_type].cancel_type_pg').prop('disabled',true);
						$('input[name=cancel_type].cancel_type_point').prop('checked',true).trigger('change');
					}
					$('#product_cancel_pop').lightbox_me({
						centered: true, closeEsc: false,
						onLoad: function() { },
						onClose: function(){
							$product_form.find('input[name=ordernum]').val('');
							$product_form.find('input[name=op_uid]').val('');
						}
					});
				} else {
					alert(data['result_text']);
				}
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	});
	$('form[name=product_cancel]').on('submit',function(e){ e.preventDefault();
		if(confirm("정말 주문을 취소하시겠습니까?")===true) {
			var data = $(this).serialize();
			$.ajax({
				data: data,
				type: 'POST',
				cache: false,
				url: '/pages/mypage.order.view.ajax.php',
				success: function(data) {
					if(data=='OK') {
						alert('성공적으로 취소요청 되었습니다.'); location.reload(); return false;
					} else {
						alert(data);
					}
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
	});
});
</script>
<!-- / 부분취소신청 -->

<?php
# LDD018
if($r['delivery_date'] != '0000-00-00') {
?>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
	$(function() {
		$("input[name=delivery_date]").datepicker({changeMonth: true, changeYear: true });
		$("input[name=delivery_date]").datepicker( "option", "dateFormat", "yy-mm-dd" );
		$("input[name=delivery_date]").datepicker( "option",$.datepicker.regional["ko"] );
	});
</script>
<?php } ?>


<?PHP
	include_once("inc.footer.php");
?>
