<?PHP

	// 페이지 표시 - 주문 취소목록 : 주문 전체목록 구분
	$app_current_link = ( $_GET[_mode] == "cancellist" ? "/totalAdmin/_ordercancel.list.php" : "/totalAdmin/_cancel.list.php" );

	include_once("inc.header.php");

    $r = _MQ("
    	SELECT * FROM odtOrderProduct as op
    	left join odtOrder as o on (o.ordernum = op.op_oordernum)
    	WHERE ordernum='" . $ordernum . "' and op_uid = '".$uid."'
    ");

	$orderidTemp = ($r[orderid] == "guest" ? "비회원주문" : $r[orderid]);

	if( $r[orderid]!='guest' ) {
		$m_tmp = _MQ(" select serialnum from odtMember where userType = 'B' and id = '".$r[orderid]."' ");
		$orderidLink = "_member.form.php?_mode=modify&serialnum=".$m_tmp[serialnum]."&_PVSC=";
		$orderidTemp = "<a href='".$orderidLink."' target='_blank'>".$orderidTemp."</a>";
	}

    $OrderSumpriceD = number_format($r[sumprice]);
    $OrderDeliveryD = number_format($r[dPrice]);
    $OrderTotalPriceD = number_format($r[tPrice]);
    $OrderUsedpointD = number_format($r[gPrice]);
    $OrderGetpointD = number_format($r[gGetPrice]);
    $OrderDate = date("Y년 m월 d일 H시 i분",strtotime($r[orderdate]));

	$r[expressdate] = $r[expressdate] ? $r[expressdate] : date("Y-m-d");

?>

<form name=frm method=post action="_cancel.pro.php">
<input type=hidden name=_mode value='cancel'>
<input type=hidden name=ordernum value='<?=$ordernum?>'>
<input type="hidden" name="op_uid" value="<?=$uid?>"/>
<input type=hidden name=code value='<?=$code?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type="hidden" name="statusUpdate" value="yes">

				<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 취소요청상품정보</div>
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
		where op.op_oordernum='".$ordernum."' and op.op_uid = '".$uid."'
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
			op.* , p.prolist_img , cl.cl_title , cl.cl_price, o.orderstatus_step
		from odtOrderProduct as op
		left join odtProduct as p on (p.code=op.op_pcode)
		left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
		left join odtOrder as o on (o.ordernum = op.op_oordernum)
		where op.op_oordernum='".$ordernum."' and op.op_uid = '".$uid."' and op.op_is_addoption = 'N'
		order by p.code , op.op_delivery_price desc
	";
	$sres = _MQ_assoc($sque);
	// 현금영수증용 상품명 생성
	$cash_product_name = (count($sres)>1)?$sres[0][op_pname].'외 '.(count($sres)).'개':$sres[0][op_pname];

	foreach( $sres as $sk=>$sv ){


		// -- 이미지 ---
		$img_src	= app_thumbnail( "장바구니" , $sv );
		$img_src = @file_exists("/upfiles/product/" . $img_src) ? $img_src : $sv[prolist_img];

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
					$coupon_html_body .="<span  style='display:block'><span class='coupon_num'>".$coupon_row[opc_expressnum]." <span class='shop_state_pack'><span class='orange' style='padding:0px 7px!important'>미사용</span></span></span></span>";
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

		// -- 발송여부 ---
		$app_status = "<li style='clear:both;display:inline; float:left; padding-top:3px;'><span class='shop_state_pack'>";
		if($sv[op_orderproduct_type] == "product") {
			$app_status .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발송완료</span>" : "<span class='lightgray'>발송대기</span>");
		}
		else {
			$app_status .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발급완료</span>" : "<span class='lightgray'>발급대기</span>");
		}
		$app_status .= "</span></li>";
		// -- 발송여부 ---


		// -- 배송비 ---
		if($sv[op_orderproduct_type] != "product") {	// 배송적용 상품이 아니면
			$delivery_print = "-";
			$add_delivery_print = "";
		}
		else {
			$delivery_print = ($sv[op_delivery_price] > 0 && $delivery_print != "무료배송") ? number_format($sv[op_delivery_price])."원" : "무료배송"; // 배송정보.
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
				$status_print .= "<br><B><a href='".$arr_delivery_company[$sv[op_expressname]].rm_str($sv[op_expressnum])."' target='_blank' title='' >[배송조회]</a></B>";
			}
		}
		$status_print .= "</span></li>";
		// -- 진행상태 ---


		// -- 변수적용 ---
		$totalPrice += ($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt] ;//총상품가격
		$totalClprice += ($sv[cl_price] > 0 ? $v[cl_price] : 0 );  //총 상품별 사용 쿠폰가격
		$totadlPrice += $sv[op_delivery_price] + $sv[op_add_delivery_price] ;//총배송비

		// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------
		$totadisPrice += $sv['op_cancel_discount_price'];
		// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------


		echo "
			<tr>
				<td>". ($img_src ? "<img src='" . replace_image('/upfiles/product/'.$img_src) . "' style='width:100px;'>" : "-") ."</td>
				<td style='text-align:left; padding:10px;'>
					<B>" . stripslashes($sv[op_pname]) . "</B>
					" . ($sv[op_option1] ? "<br>".($sv[op_is_addoption]=="Y" ? "추가옵션" : "선택옵션")." : " . trim($sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3]) :  "<br>옵션없음" ) . "
					" . $coupon_html . "
				</td>
				<td>" . number_format($sv[op_pprice] + $sv[op_poptionprice]) . "원</td>
				<td><b>" . $sv[op_cnt] . "</b>개</td>
				<td>" . number_format(($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt]) . "원</td>
				" . $delivery_print . "
				<td><div class='btn_line_up_center'>" . $app_status . "</div></td>
				<td><div class='btn_line_up_center'>" . $status_print. "</div></td>
			</tr>
		";
	}

	// 추가옵션 출력
	$add_res = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$sv['op_pouid']."' and op_oordernum = '".$sv['op_oordernum']."' ");
	if( count($add_res) > 0 ){
		foreach($add_res as $adk=>$adv) {
			echo "
				<tr>
					<td>-</td>
					<td style='text-align:left; padding:10px;'>
						추가옵션 : " . trim($adv[op_option1]." ".$adv[op_option2]." ".$adv[op_option3]) . "
					</td>
					<td>" . number_format($adv[op_pprice] + $adv[op_poptionprice]) . "원</td>
					<td><b>" . $adv[op_cnt] . "</b>개</td>
					<td>" . number_format(($adv[op_pprice] + $adv[op_poptionprice]) * $adv[op_cnt]) . "원</td>
					<td></td>
					<td><div class='btn_line_up_center'>" . $app_status . "</div></td>
					<td><div class='btn_line_up_center'>" . $status_print. "</div></td>
				</tr>
			";
			// 추가옵션 합계 금액
			$totalAprice += ($adv[op_pprice] + $adv[op_poptionprice]) * $adv[op_cnt] + $adv[op_delivery_price] + $adv[op_add_delivery_price] ;
		}
	}


	echo "
					<tr>
						<td colspan=8 style='padding:10px; text-align:right;'>
							<b>총환불금액</b> :
							   총상품가격(<font color='EE0016'>" . number_format($totalPrice+$totalAprice) . "원</font>)
							+ 총배송비(<font color='EE0016'>" . number_format($totadlPrice) . "원</font>)
							- 총할인액(<font color='EE0016'>" . number_format($totadisPrice) . "원</font>)<!-- // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC -->
							=
							<font color='FF0000' style='font-size:16;LETTER-SPACING:-0.02em;font-family:굴림;'><b>
							" . number_format($totalPrice + $totadlPrice+$totalAprice - $totadisPrice) . "원</b></font>
							<input type='hidden' name='cancel_total' value='".($totalPrice + $totadlPrice+$totalAprice - $totadisPrice)."'/>

						</td>
					</tr>
				</tbody>
			</table>
			".($r[op_cancel_type]!='pg' ? _DescStr('고객 요청에 따라 적립금으로 환불됩니다. PG 연동은 되지 않습니다.') : _DescStr("카드결제는 취소처리시 PG 연동되며, 전액적립금결제를 제외한 다른 결제수단일 경우 환불계좌로 송금 후 처리하시기 바랍니다.").""._DescStr("<b>취소 요청한 금액보다 상계가능한 정산예정금액이 부족할 경우 부분취소가 불가능합니다. 이러한 경우 PG사에 문의하시기 바랍니다.</b>") )."
		</div>
	";

?>

			<!-- 버튼영역 -->
			<div class="bottom_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack">
						<?=($_mode == "cancellist" ? "" : "<input onclick=\"return confirm('취소요청을 처리하시겠습니까? ".(!in_array($r[paymethod],array('C','G'))?'\\n반드시 아래 환불계좌로 송금하신 후에 처리하시기 바랍니다.':'')."');\" type='submit' name='' class='input_large red' value='취소처리하기'>")?>
						<input type="button" name="" class="input_large gray" value="목록보기" onclick="location.href=('<?=($_mode == "cancellist" ? "_ordercancel.list.php" : "_cancel.list.php")?>?<?=enc("d" , $_PVSC)?>');">
					</span>
				</div>
			</div>
			<!-- 버튼영역 -->

</form>

<form name=frm method=post action="_cancel.pro.php">
<input type=hidden name=_mode value='modify'>
<input type=hidden name=ordernum value='<?=$ordernum?>'>
<input type="hidden" name="op_uid" value="<?=$uid?>"/>
<input type=hidden name=code value='<?=$code?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type="hidden" name="statusUpdate" value="yes">

			<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 고객 요청내용</div>
			<!-- 검색영역 -->
			<div class="form_box_area">
				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">고객 요청내용</td>
							<td class="conts">
								<textarea name="cancel_msg" cols="80" rows="3" class="input_text" style="width:100%;height:100px;" ><?=stripslashes($r[op_cancel_msg])?></textarea>
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
							<td class="article">총환불금액</td>
							<td class="conts">
							<?
									// 2016-11-30 ::: 환불 비용 계산 ::: JJC ---
								//		return $__cancelTotal = array('pg'=>PG비용 , 'point'=>포인트비용);
								//		reutnr $__console = 타입; // 적립금환불 요청 시

								//	넘길 변수
								//		$opr <== 부분취소 상품의 주문상품 / 주문 / 상품배열정보
								//		$ordernum <== 주분번호
								//		$totalPrice <== 부분취소 상품의 상품가격
								//		$totadlPrice <== 부분취소 상품의  배송비
								//		$totalAprice
								//		$totalDiscount <== 부분취소 상품의 할인비용
								$opr = $r;
								$totalPrice = ($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt] ;//총상품가격
								$totadlPrice = $r['op_delivery_price'] + $r['op_add_delivery_price'] ;//총배송비
								$totalDiscount = $r['op_cancel_discount_price'];// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------
								include("_cancel.inc_calc.php");// *** 파일생성 ***

								// 2016-11-30 ::: 환불 비용 계산 ::: JJC ---
							?>
							PG환불: <b><?=number_format($__cancelTotal['pg'])?></b>원 + 적립금환불: <b><?=number_format($__cancelTotal['point'])?></b>원
							= <b><?=number_format($__cancelTotal['pg']+$__cancelTotal['point'])?></b>원
							<? if($__refundToBe > $__cancelTotal['pg']+$__cancelTotal['point']) { echo _DescStr("상품금액이 환불가능한 최대 금액을 초과하여 일부만 환불합니다.","orange"); } ?>
							</td>
						</tr>
<?PHP
	// -- 취소상태 표시 ---
	if($r[op_cancel] == "Y"){
		echo "
						<tr>
							<td class='article'>주문상태</td>
							<td class='conts'>&nbsp;<font color='FF0000'><b>주문취소</b></font> (". date("Y년 m월 d일 H시 i분 s초",strtotime($r[op_cancel_cdate])) .")</td>
						</tr>
		";
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
											<input type='radio' name='paystatus' value='Y' " . ($r[paystatus]=='Y' ? "checked" : "") . ">결제확인
											<input type='radio' name='paystatus' value='N' " . ($r[paystatus]!='Y' ? "checked" : "") . ">결제미확인
										" . _DescStr("결제확인 시 쿠폰상품의 경우 자동발급되며, 그에 따라 메일이 발송되며 문자는 관리자 설정에 따릅니다.")
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

		$v_bank = _MQ("select ool_tid, ool_date, ool_account_num, ool_deposit_name, ool_bank_name from odtOrderOnlinelog where ool_ordernum='$ordernum' and ool_type='R'");

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



<?PHP
    // 발송/발급상태
	echo "
					<tr>
						<td class='article'>결제/발송/발급상태</td>
						<td class='conts'>
							<span class='shop_state_pack'>" . $arr_o_status[$sv[orderstatus_step]] . "</span>
						</td>
					</tr>
	";

?>

					</tbody>
				</table>
			</div>

		<? if(!in_array($r[paymethod],array('C','G'))) { ?>
			<div style=" margin-left:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold">◆ 환불계좌 정보</div>

			<!-- 검색영역 -->
			<div class="form_box_area">

				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">은행명</td>
							<td class="conts">
							<? if($r[op_cancel]=='Y') { ?>
								<b><?=$ksnet_bank[$r[op_cancel_bank]]?></b>
							<? } else { ?>
								<select name="cancel_bank">
									<option value="">- 은행 선택 -</option>
									<? foreach($ksnet_bank as $k=>$v) { ?>
									<option value="<?=$k?>" <?=$k==$r[op_cancel_bank]?'selected':''?>><?=$v?></option>
									<? } ?>
								</select>
							<? } ?>
							</td>
						</tr>
						<tr>
							<td class="article">계좌번호</td>
							<td class="conts">
							<? if($r[op_cancel]=='Y') { ?>
								<b><?=$r[op_cancel_bank_account]?></b>
							<? } else { ?>
								<input type="text" name="cancel_bank_account" value="<?=$r[op_cancel_bank_account]?>" class="input_text"/>
							<? } ?>
							</td>
						</tr>
						<tr>
							<td class="article">예금주명</td>
							<td class="conts">
							<? if($r[op_cancel]=='Y') { ?>
								<b><?=$r[op_cancel_bank_name]?></b>
							<? } else { ?>
								<input type="text" name="cancel_bank_name" value="<?=$r[op_cancel_bank_name]?>" class="input_text"/>
							<? } ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		<? } ?>


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
						<tr>
							<td class="article">배송비결제</td>
							<td class="conts"><?=($r[delchk]=="Y" ? "<b>배송비(착불)</b>" : "<b>배송비(선불)</b>")?></td>
						</tr>
						<tr>
							<td class="article">주문자명</td>
							<td class="conts">
								<b><?=$r[ordername]?></b> (<?=$orderidTemp?>)
							</td>
						</tr>
						<tr>
							<td class="article">전화번호</td>
							<td class="conts"><?=tel_format($r[ordertel1].$r[ordertel2].$r[ordertel3])?></td>
						</tr>
						<tr>
							<td class="article">휴대폰번호</td>
							<td class="conts"><?=tel_format($r[orderhtel1].$r[orderhtel2].$r[orderhtel3])?></td>
						</tr>
						<tr>
							<td class="article">E-mail</td>
							<td class="conts"><?=$r[orderemail]?></td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- 버튼영역 -->
			<div class="bottom_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack">
						<?=($_mode == "cancellist" ? "" : "<input type='submit' name='' class='input_large red' value='정보수정하기'>")?>
						<input type="button" name="" class="input_large gray" value="목록보기" onclick="location.href=('<?=($_mode == "cancellist" ? "_ordercancel.list.php" : "_cancel.list.php")?>?<?=enc("d" , $_PVSC)?>');">
					</span>
				</div>
			</div>
			<!-- 버튼영역 -->

</form>
<?PHP
	include_once("inc.footer.php");
?>


<script>
	function new_post_view() {
		window.open(
			'../newpost/newpost.search.php' ,
			'newpost',
			'left=20, top=20 ,width=524 ,height=600 ,toolbar=no ,menubar=no ,status=no ,scrollbars=yes ,resizable=no'
		);
	}

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
		url: '/pages/_order.form.cashUpdate.php',
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