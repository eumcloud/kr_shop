<?PHP
# LDD007
$app_current_link = "/totalAdmin/_order4.list.php";
include_once("inc.header.php");

// 정산정보
$r = _MQ(" select * from `odtOrderSettleComplete` where `s_uid` = '{$suid}' ");
if(!$r['s_uid']) error_msg('잘못된 접근입니다.');
$r = array_merge($r , _text_info_extraction( "odtOrderSettleComplete" , $r['s_uid'] ));
$op_code = explode(',', $r['s_opuid']);
if(sizeof($op_code) <= 0) error_msg('잘못된 접근입니다.');


// 입점업체정보
$partner = _MQ(" select * from `odtMember` where `id` = '{$r['s_partnerCode']}' and `userType` = 'C' ");

// 주문정보 호출
$pr = _MQ_assoc("
	select
		*
	from
		`odtOrderProduct` as op left join
		`odtProduct` as p on (p.code=op.op_pcode) left join
		`odtOrder` as o on(op.op_oordernum = o.ordernum )
	where
		op.op_uid in ('". implode("' , '" , $op_code) ."') and
		op_partnerCode = '" . $r['s_partnerCode'] . "'
	");

?>



<!-- 주문정보 {-->
<div style=" margin-top:20px; margin-left:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold">◆ 정산 정보</div>
<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
			<col width="120px"><col width="250px"><col width="120px"><!-- 마지막값은수정안함 --><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<td class="article">정산일</td>
				<td class="conts" colspan="3"><?php echo date('Y-m-d', strtotime($r['s_date'])); ?></td>
			</tr>
			<tr>
				<td class="article">총금액</td>
				<td class="conts"><?php echo number_format($r['s_price']); ?>원</td>
				<td class="article">정산상품개수</td>
				<td class="conts"><?php echo number_format($r['s_count']); ?>개</td>
			</tr>
			<tr>
				<td class="article">입점업체 정산금액</td>
				<td class="conts"><?php echo number_format($r['s_com_price']); ?>원</td>
				<td class="article">배송비</td>
				<td class="conts"><?php echo number_format($r['s_delivery_price']); ?>원</td>
			</tr>
			<tr>
				<td class="article">수수료</td>
				<td class="conts"><?php echo number_format($r['s_discount']); ?>원</td>
				<td class="article">할인액</td>
				<td class="conts"><?php echo number_format($r['s_usepoint']); ?>원</td>
			</tr>
		</tbody>
	</table>
</div>
<!--} 주문정보 -->



<!-- 입점업체정보 {-->
<div style=" margin-left:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold">◆ 입점업체 정보</div>
<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
			<col width="120px"><col width="250px"><col width="120px"><!-- 마지막값은수정안함 --><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<td class="article">아이디</td>
				<td class="conts">
					<?php echo $partner['id']; ?>
				</td>
				<td class="article">업체명</td>
				<td class="conts">
					<?php echo $partner['cName']; ?>
				</td>
			</tr>
			<tr>
				<td class="article">대표명</td>
				<td class="conts">
					<?php echo $partner['ceoName']; ?>
				</td>
				<td class="article">담당자명</td>
				<td class="conts">
					<?php echo $partner['name']; ?>
				</td>
			</tr>
			<tr>
				<td class="article">이메일</td>
				<td class="conts">
					<?php echo $partner['email']; ?>
				</td>
				<td class="article">전화번호</td>
				<td class="conts">
					<?php echo ($partner[tel1]?tel_format($partner[tel1].$partner[tel2].$partner[tel3]):''); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<!--} 입점업체정보 -->









<!-- 주문정보 {-->
<div style=" margin-left:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold">◆ 주문상품 정보</div>
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
				<th scope='col' class='colorset'>주문자명</th>
				<th scope='col' class='colorset'>결제방법</th>
				<th scope='col' class='colorset'>상태</th>
				<th scope='col' class='colorset'>정보</th>
				<th scope='col' class='colorset'>상세보기</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($pr as $sk=>$sv) {

				// -- 이미지 ---
				$img_src = app_thumbnail( "장바구니" , $sv );
				$img_src = @file_exists("../upfiles/product/" . $img_src) ? $img_src : $sv[prolist_img];

				// -- 추가옵션 ---
				$add_option = "";
				if($sv[op_add_option1]||$sv[op_add_option2]||$sv[op_add_option3]||$sv[op_add_option4]||$sv[op_add_option5]||$sv[op_add_option6]||$sv[op_add_option7]||$sv[op_add_option8]||$sv[op_add_option9]||$sv[op_add_option10]) {
					if($sv[op_add_option1]) { $add_option .= '['.$sv[op_add_option1_name].':'.$sv[op_add_option1].']&nbsp;'; }
					if($sv[op_add_option2]) { $add_option .= '['.$sv[op_add_option2_name].':'.$sv[op_add_option2].']&nbsp;'; }
					if($sv[op_add_option3]) { $add_option .= '['.$sv[op_add_option3_name].':'.$sv[op_add_option3].']&nbsp;'; }
					if($sv[op_add_option4]) { $add_option .= '['.$sv[op_add_option4_name].':'.$sv[op_add_option4].']&nbsp;'; }
					if($sv[op_add_option5]) { $add_option .= '['.$sv[op_add_option5_name].':'.$sv[op_add_option5].']&nbsp;'; }
					if($sv[op_add_option6]) { $add_option .= '['.$sv[op_add_option6_name].':'.$sv[op_add_option6].']&nbsp;'; }
					if($sv[op_add_option7]) { $add_option .= '['.$sv[op_add_option7_name].':'.$sv[op_add_option7].']&nbsp;'; }
					if($sv[op_add_option8]) { $add_option .= '['.$sv[op_add_option8_name].':'.$sv[op_add_option8].']&nbsp;'; }
					if($sv[op_add_option9]) { $add_option .= '['.$sv[op_add_option9_name].':'.$sv[op_add_option9].']&nbsp;'; }
					if($sv[op_add_option10]) { $add_option .= '['.$sv[op_add_option10_name].':'.$sv[op_add_option10].']'; }
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
							$coupon_html_body .="<span  style='display:block; padding-top:3px;'><span class='coupon_num'>".$coupon_row[opc_expressnum]." <span class='shop_state_pack'><span class='orange' style='padding:0px 7px!important'>미사용</span></span></span></span>";
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
					$delivery_print = $sv[op_delivery_price] > 0 ? number_format($sv[op_delivery_price])."원" : "무료배송"; // 배송정보.
					$add_delivery_print = ($sv[op_add_delivery_price] ? "<br>추가배송비 : +".number_format($sv[op_add_delivery_price])."원" : "") ;// 추가배송비 여부
				}
				// -- 배송비 ---

				// -- 배송상태 ---
				if($prev_pcode != $sv[op_pcode].$sv['op_oordernum']) {
					$delivery_print =  $delivery_print.$add_delivery_print;
				}
				else {
					$delivery_print = "";
				}
				$prev_pcode = $sv[op_pcode].$sv['op_oordernum'];
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
			?>
			<tr>
				<td>
					<?php echo ($img_src ? "<img src='" . replace_image('/upfiles/product/'.$img_src) . "' style='width:100px;'>" : "-"); ?>
				</td>
				<td style='text-align:left; padding:10px;'>
					<B><?php echo stripslashes($sv[op_pname]); ?></B>
					<?php echo ($sv[op_option1] ? "<br>선택옵션 : ".trim($sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3]) :  "<br>옵션없음" ); ?>
					<?php echo ($add_option ? "<br>추가옵션 : ".trim($add_option) :  "" ); ?>
					<?php echo ($sv[cl_title] ? "<br>적용쿠폰 : ".$sv[cl_title]." ( ".number_format($sv[cl_price])."원 할인" :  "" ); ?>
					<?php echo $coupon_html; ?>
				</td>
				<td>
					<?php echo number_format($sv[op_pprice] + $sv[op_poptionprice]); ?>원
				</td>
				<td>
					<b><?php echo number_format($sv[op_cnt]); ?></b> 개
				</td>
				<td>
					<?php echo number_format(($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt]); ?>원
				</td>
				<td>
					<?php echo $delivery_print; ?>
				</td>
				<td>
					<?=$sv[ordername]?>
				</td>
				<td>
					<?=$arr_paymethod_name[$sv[paymethod]]?>
				</td>
				<td>
					<div class='btn_line_up_center'><?php echo $app_status; ?></div>
				</td>
				<td>
					<div class='btn_line_up_center'><?php echo $status_print; ?></div>
				</td>
				<td>
					<div class="btn_line_up_center view_bt">
						<span class="shop_btn_pack"><input type="button" onclick="location.href='_order.form.php?_mode=modify&ordernum=<?php echo $sv[op_oordernum]; ?>';" value="상세보기" class="input_small blue"></span>
					</div>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<!--} 주문정보 -->

<!-- 목록으로 {-->
<div class="bottom_btn_area">
	<div class="btn_line_up_center">
		<span class="shop_btn_pack">
			<input type="button" name="" class="input_large red" value="인쇄하기" onclick="window.print();">
			<input type="button" name="" class="input_large gray" value="목록보기" onclick="location.href=('_order4.list.php?<?php echo enc('d', $_PVSC); ?>');">
		</span>
	</div>
</div>
<!--} 목록으로 -->







<?php
	// JJC001
	// ---------------------------------------
	// ----- 세금계산서 사용여부에 따른 노출 -----
	// ---------------------------------------
	if($row_setup['TAX_CHK'] == 'Y') :

		// 세금계산서 관련 변수 파일 불러오기
		include_once( dirname(__FILE__)."/../include/addons/barobill/include/var.php");

		//		// 바로빌 정발행 내부상태값 테이블
		//		공백
		//		1000 => "임시저장"
		//		2010 => "발행대기 - 발행예정_승인대기",
		//		2011 => "발행대기 - 발행예정_승인완료",
		//		4012 => "거부 - 발행예정_거부",
		//		5013 => "취소 - 발행예정_공급자취소[승인전 취소]",
		//		5031 => "취소 - 발행예정-공급자취소[승인후 취소] 국세청 승인번호가 없음", // 발행완료 후 공급자에 의한 발행취소 국세청 승인번호가 있음
		//		3014 => "발행완료 - 발행완료[즉시발행/즉시 전송]",
		//		3011 => "발행완료 - 발행완료[발행예정후 발행]",

		function tax_btn($app_mode , $app_tax_btn_nm , $color="blue"){
			global $suid , $_PVSC ;
			return "<div style='float:left;margin-left:5px;'><span class='shop_btn_pack'><input type='button' onclick=\"if(confirm('정말 실행하시겠습니까?')) { location.href=('/include/addons/barobill/_tax.pro.php?suid=" . $suid . "&mode=" . $app_mode . "&_PVSC=". $_PVSC ."');}\" value='" . $app_tax_btn_nm . "' class='input_small ". $color ."'></span></div>";//common_frame.
		}


		// 세금계산서 상태에 따른 버튼 노출 변경
		switch( $r['s_tax_status'] ){
			case 1000 ://임시저장
				$app_tax_btn = tax_btn("issue" , "세금계산서 발행" , "red") . tax_btn("delete" , "세금계산서 삭제"); break;
			case 2010 : case 2011 : //발행대기
			case 4012 : //거부
				$app_tax_btn = ""; break;
			case 3014 : case 3011 : //발행완료
				$app_tax_btn = tax_btn("cancel" , "세금계산서 발행취소"); break;
			case 5013 : case 5031 : //발행취소
				$app_tax_btn = tax_btn("delete" , "세금계산서 삭제"); break;
			default : //미발행상태
				$app_tax_btn = tax_btn("regist" , "세금계산서 임시저장"); break;
		}


		// 상태값 추출
		if($r['s_tax_mgtnum'] && $r['s_tax_status'] == -9999 ) {

			// 세금계산서 상태값 업데이트
			include_once( dirname(__FILE__)."/../include/addons/barobill/api_ti/_tax.GetTaxInvoiceState.php");

		}

?>
<!-- 입점업체세금계산서정보 {-->
<div style=" margin-left:20px; margin-right:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold;">

	◆ 입점업체세금계산서(공급받는자) 정보

	<div style="float:right;margin-right:">
		<span class="shop_btn_pack"><input type="button" onclick="window.open('_entershop.form.php?_mode=modify&serialnum=<?=$partner['serialnum']?>');" value="정보수정" class="input_small red"></span>
	</div>

</div>
<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
			<col width="120px"><col width="250px"><col width="120px"><!-- 마지막값은수정안함 --><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<td class="article">세금계산서 연동상태</td>
				<td class="conts">
					<B><?=($r['s_tax_status'] ? ($r['s_tax_status'] < 0 ? $arr_error_code[$r[s_tax_status]] : $arr_inner_state_table[$r[s_tax_status]]) : "미발행상태")?></B>
<?PHP

	// 상태값 추출
	if($r['s_tax_mgtnum'] && in_array($r['s_tax_status'] , array(1000 , 2010 , 2011 , 3014 , 3011)) ) {
		// 세금계산서 잔여포인트 추출 - return_balance
		include_once( dirname(__FILE__)."/../include/addons/barobill/api_ti/_tax.GetBalanceCostAmount.php");
		echo "<br>(바로빌 잔여포인트 : <B style='color:red; font-size:15px;'>" . number_format($return_balance) . "</B> P)";
	}

?>
				</td>
				<td class="article">실행버튼</td>
				<td class="conts">
					<?=$app_tax_btn?>
<?PHP

	// 상태값 추출
	if($r['s_tax_mgtnum'] &&  in_array($r['s_tax_status'] , array(2010 ,2011 , 3014 , 3011)) ) {
		// 인쇄 팝업 URL
		$app_tax_mgtnum = $r['s_tax_mgtnum'];
		include_once( dirname(__FILE__)."/../include/addons/barobill/api_ti/_tax.GetTaxInvoicePrintURL.php");
	}

?>
				</td>
			</tr>
			<tr>
				<td class="article">공급업체 사업자명</td>
				<td class="conts"><?=$partner[cName]?>&nbsp;</td>
				<td class="article">사업자번호</td>
				<td class="conts"><?=$partner[cNumber]?>&nbsp;</td>
			</tr>
			<tr>
				<td class="article">대표자</td>
				<td class="conts"><?=$partner[ceoName]?>&nbsp;</td>
				<td class="article">주소</td>
				<td class="conts"><?=$partner[address]?>&nbsp;</td>
			</tr>
			<tr>
				<td class="article">업태</td>
				<td class="conts"><?=$partner[cItem1]?>&nbsp;</td>
				<td class="article">업종</td>
				<td class="conts"><?=$partner[cItem2]?>&nbsp;</td>
			</tr>
			<!-- 역발행 패치 -->
			<tr>
				<td class="article">세금계산서 발행금액</td>
				<td class="conts" colspan="5">
					<?//$_tax_Price = $r['s_price']-$r['s_com_price'];?>
					<!-- 총 금액 <b><?php echo number_format($r['s_price']); ?></b>원에서 입점업체 정산금액 <b><?php echo number_format($r['s_com_price']); ?></b>원을 제외한 수수료 <b><?php echo number_format($r['s_discount']); ?></b>원에 대한 세금계산서를 발행합니다. -->
					<!-- 총 금액 <b><?php echo number_format($r['s_price']); ?></b>원에서 입점업체 정산금액 <b><?php echo number_format($r['s_com_price']); ?></b>원을 제외한 금액 <b><?php echo number_format($_tax_Price); ?></b>원에 대한 세금계산서를 발행합니다. -->
					수수료 <b><?php echo number_format($r['s_discount']); ?></b>원에 대한 세금계산서를 발행합니다.
				</td>
			</tr>
			<!-- // 역발행 패치 -->
		</tbody>
	</table>

	<?=_DescStr("
		<B> 세금계산서는 발행순서</B><br>
			0. 미발행(해당 문서 정보가 없습니다. 라고 표기됩니다.)<br>
			1. 세금계산서 임시저장<br>
			2. 세금계산서 발행 (임시저장시 발행가능)<br>
			3. 세금계산서 취소 (발행시 취소가능)<br>
			4. 세금계산서 삭제 (임시저장, 발행시 삭제가능)<br>
	")?>

</div>
<!--} 입점업체세금계산서정보 -->



<div style=" margin-left:20px; margin-bottom:-15px; overflow:hidden; font-size:15px;font-weight:bold">◆ 세금계산서 연동기록</div>
<!-- 리스트영역 -->
<div class="content_section_inner">

	<table class="list_TB" summary="리스트기본">
		<colgroup>
			<col width="80px"/><col width="120px"/><col width="*"/><col width="200px"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col" class="colorset">NO</th>
				<th scope="col" class="colorset">연동형태</th>
				<th scope="col" class="colorset">연동기록</th>
				<th scope="col" class="colorset">연동일시</th>
			</tr>
		</thead>
		<tbody>
<?PHP

	$sres = _MQ_assoc(" select * from odtOrderSettleCompleteLog where sl_suid='".$r['s_uid']."' ORDER BY sl_uid asc ");
	if(sizeof($sres) < 1) echo "<tr><td colspan=10 height='100'>내용이 없습니다.</td></tr>";
	foreach($sres as $sk=>$sv) {

		$_num =  $sk + 1;

		echo "
			<tr>
				<td>". $_num ."</td>
				<td>" . $arr_tax_mode_status[$sv['sl_mode']] . "</td>
				<td style='text-align:left; margin-left:5px;'>" . (in_array($sv['sl_remark'],array_keys($arr_error_code))?"[".$sv['sl_remark']."] ".$arr_error_code[$sv['sl_remark']]:$sv['sl_remark']) . "</td>
				<td>" . $sv['sl_rdate']. "</td>
			</tr>
		";

	}

?>

						</tbody>
					</table>
<?php
	endif;
	// ---------------------------------------
	// ----- 세금계산서 사용여부에 따른 노출 -----
	// ---------------------------------------
?>





<style media="print">
#header, .aside_first, .aside_second, .bottom_btn_area, .view_bt {display:none;}
.container {width:1024px;}
</style>
<?PHP include_once("inc.footer.php"); ?>