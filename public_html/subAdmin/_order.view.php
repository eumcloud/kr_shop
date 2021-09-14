<?PHP

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
		where 
			op.op_oordernum='".$ordernum."' AND op.op_partnerCode = '" . $com[id] . "'
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
		where op.op_oordernum='".$ordernum."' AND op.op_partnerCode = '" . $com[id] . "'
	";
	$sres = _MQ_assoc($sque);
	// 현금영수증용 상품명 생성
	$cash_product_name = (count($sres)>1)?$sres[0][op_pname].'외 '.(count($sres)).'개':$sres[0][op_pname];

	foreach( $sres as $sk=>$sv ){


		// -- 이미지 ---
		$img_src	= app_thumbnail( "장바구니" , $sv );
		$img_src = @file_exists("/upfiles/product/" . $img_src) ? $img_src : $sv[prolist_img];

		// -- 추가옵션 ---
		$add_option = "";
		if($sv[op_add_option1_name]) {
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
					$coupon_html_body .="<span  style='display:block; padding-top:3px;' ><span class='coupon_num'>".$coupon_row[opc_expressnum]." <span class='shop_state_pack'><span class='orange' style='padding:0px 7px!important'>미사용</span></span></span><a href='/".$row_setup[P_SKIN]."/mypage.order.pro.php?_mode=coupon_sms_resend&opcuid=".$coupon_row[opc_uid]."' class='coupon_sms' style='float:right' target='common_frame'>[문자발송]</a></span>";
				} 
				else if($coupon_row[opc_status] == "사용") {
					$coupon_html_body .="<span  style='display:block; padding-top:3px;'><span class='coupon_num'>".$coupon_row[opc_expressnum]." <span class='shop_state_pack' ><span class='light' style='padding:0px 7px!important'>사용</span></span></span></span>";
					$use_cnt++;		
				} 
				else if($coupon_row[opc_status] == "취소") {
					$coupon_html_body .="<span  style='display:block; padding-top:3px;'><span class='coupon_num'>".$coupon_row[opc_expressnum]." <span class='shop_state_pack' ><span class='dark' style='padding:0px 7px!important'>취소</span></span></span></span>";
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


		echo "
			<tr>
				<td>". ($img_src ? "<img src='" . replace_image('/upfiles/product/'.$img_src) . "' style='width:100px;'>" : "-") ."</td>
				<td style='text-align:left; padding:10px;'>
					<B>" . stripslashes($sv[op_pname]) . "</B>
					" . ($sv[op_option1] ? "<br>선택옵션 : " . trim($sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3]) :  "<br>옵션없음" ) . "
					" . ($add_option ? "<br>추가옵션 : " . trim($add_option) :  "" ) . "
					" . ($sv[cl_title] ? "<br>적용쿠폰 : ".$sv[cl_title]." ( ".number_format($sv[cl_price])."원 할인" :  "" ) . "
					" . $coupon_html . "
				</td>
				<td>" . number_format($sv[op_pprice] + $sv[op_poptionprice]) . "원</td>
				<td><b>" . $sv[op_cnt] . "</b>개</td>
				<td>" . number_format(($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt]) . "원</td>
				<td><div class='btn_line_up_center'>" . $app_status . "</div></td>
				<td><div class='btn_line_up_center'>" . $status_print. "</div></td>
			</tr>
		";
	}


	echo "
				</tbody> 
			</table>
		</div>
	";

?>

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
							<td class="conts"><b><?=$r[ordername]?> </b> (<?=$orderidTemp?>)</td>
						</tr>
						<tr>
							<td class="article">전화번호</td>
							<td class="conts"><?=$r[ordertel1]?> - <?=$r[ordertel2]?> - <?=$r[ordertel3]?></td>
						</tr>
						<tr>
							<td class="article">휴대폰번호</td>
							<td class="conts"><?=$r[orderhtel1]?> - <?=$r[orderhtel2]?> - <?=$r[orderhtel3]?></td>
						</tr>
						<tr>
							<td class="article">E-mail</td>
							<td class="conts"><?=$r[orderemail]?></td>
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
							<td class="conts"><?=$r[username]?></td>
						</tr>
						<tr>
							<td class="article">사용자 휴대폰</td>
							<td class="conts"><?=$r[userhtel1]?> - <?=$r[userhtel2]?> - <?=$r[userhtel3]?></td>
						</tr>
						<tr>
							<td class="article">사용자 E-mail</td>
							<td class="conts"><?=$r[useremail]?></td>
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
							<td class="conts"><?=$r[recname]?></td>
						</tr>
						<tr>
							<td class="article">전화번호</td>
							<td class="conts"><?=$r[rectel1]?> - <?=$r[rectel2]?> - <?=$r[rectel3]?></td>
						</tr>
						<tr>
							<td class="article">휴대폰번호</td>
							<td class="conts"><?=$r[rechtel1]?> - <?=$r[rechtel2]?> - <?=$r[rechtel3]?></td>
						</tr>
						<tr>
							<td class="article">지번 주소</td>
							<td class="conts">[<?=$r[reczip1]?>-<?=$r[reczip2]?>]<?=$r[recaddress]?> <?=$r[recaddress1]?></td>
						</tr>
						<tr>
							<td class="article">도로명주소</td>
							<td class="conts">
								<?=$r[recaddress_doro]?>
								<?=_DescStr("지번주소의 상세주소부분이 빠져있을 수 있으므로 주의하시기 바랍니다.")?>
								<?=_DescStr("예) 101동 101호, OO빌딩 2층 202호 등")?>
							</td>
						</tr>
						<tr>
							<td class="article">새 우편번호</td>
							<td class="conts">
								<?=$r[reczonecode]?>
							</td>
						</tr>
						<?php
						# LDD018
						if($r['delivery_date'] != '0000-00-00') {
						?>
						<tr>
							<td class="article">배송 요청일</td>
							<td class="conts"><?=$r['delivery_date']?></td>
						</tr>
						<?php } ?>
						<tr>
							<td class="article">배송시 유의사항</td>
							<td class="conts"><?=nl2br(htmlspecialchars(stripslashes($r[comment])))?></td>
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
						<input type="button" name="" class="input_large gray" value="목록보기" onclick="location.href=('_order2.list.php?<?=enc("d" , $_PVSC)?>');">
					</span>
				</div>
			</div>
			<!-- 버튼영역 -->
</form>



<?PHP
	include_once("inc.footer.php");
?>