<?PHP

	// LMH008

	// 페이지 표시 - 주문 취소목록 : 주문 전체목록 구분
	$app_current_link = "/totalAdmin/_return.list.php";

	include_once("inc.header.php");

    $r = _MQ("
    	SELECT * FROM odtRequestReturn as rr
    	left join odtOrder as o on (o.ordernum = rr.rr_ordernum)
    	WHERE rr.rr_uid = '".$uid."'
    ");

    $ordernum = $r['rr_ordernum'];

?>

				<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 상품정보</div>
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
	$ex = explode("," , $r['rr_opuid']);
	$tmpque = "
		select op_pcode , count(*) as cnt from odtOrderProduct as op
		left join odtProduct as p on (p.code=op.op_pcode)
		left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
		where op.op_oordernum='".$ordernum."' and op.op_uid in ('". implode("' , '" , $ex) ."')
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
		where op.op_oordernum='".$ordernum."' and op.op_uid in ('". implode("' , '" , $ex) ."') and op.op_is_addoption = 'N'
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
				</tbody>
			</table>
			"._DescStr("교환/반품 처리는 주문과 연동되지 않습니다. 부분취소/환불 등이 필요하면 주문내역에서 직접 처리하시기 바랍니다.")."
		</div>
	";
?>

<form name=frm method=post action="_return.pro.php" target="common_frame">
<input type=hidden name=_mode value='modify'>
<input type=hidden name=ordernum value='<?=$ordernum?>'>
<input type="hidden" name="uid" value="<?=$r[rr_uid]?>"/>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">

			<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 고객 요청내용</div>
			<!-- 검색영역 -->
			<div class="form_box_area">
				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">분류</td>
							<td class="conts">
								<? foreach($arr_return_type as $k=>$v) { ?>
								<label><input type="radio" name="_type" value="<?=$k?>" <?=$k==$r[rr_type]?'checked':''?>/> <?=$v?></label>&nbsp;&nbsp;&nbsp;
								<? } ?>
							</td>
						</tr>
						<tr>
							<td class="article">사유</td>
							<td class="conts">
								<select name="_reason">
								<? foreach($arr_return_reason as $k=>$v) { ?>
								<option value="<?=$v?>" <?=$v==$r[rr_reason]?'selected':''?>><?=$v?></option>
								<? } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="article">내용</td>
							<td class="conts">
								<textarea name="_content" class="input_text" style="width:100%;height:100px;" ><?=stripslashes($r[rr_content])?></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 관리자 처리내용</div>
			<!-- 검색영역 -->
			<div class="form_box_area">
				<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">상태변경</td>
							<td class="conts">
								<? foreach($arr_return_status as $k=>$v) { ?>
								<label><input type="radio" name="_status" value="<?=$k?>" <?=$k==$r[rr_status]?'checked':''?>/> <?=$v?></label>&nbsp;&nbsp;&nbsp;
								<? } ?>
								<?=_DescStr("상태가 변경되면 고객에게 문자가 발송됩니다.")?>
							</td>
						</tr>
						<tr>
							<td class="article">답변내용</td>
							<td class="conts">
								<textarea name="_admcontent" class="input_text" style="width:100%;height:100px;" ><?=stripslashes($r[rr_admcontent])?></textarea>
							</td>
						</tr>
						<tr>
							<td class="article">접수일</td>
							<td class="conts">
								<?=$r[rr_rdate]?>
							</td>
						</tr>
						<tr>
							<td class="article">처리일</td>
							<td class="conts">
								<?=rm_str($r[rr_edate])>0?$r[rr_edate]:'-'?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>



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
							<td class="conts"><b><?=$r[orderdate]?></b></td>
						</tr>
						<tr>
							<td class="article">배송비결제</td>
							<td class="conts"><?=($r[delchk]=="Y" ? "<b>배송비(착불)</b>" : "<b>배송비(선불)</b>")?></td>
						</tr>
						<tr>
							<td class="article">주문자명</td>
							<td class="conts">
								<b><?=$r[ordername]?></b> (<?=$r[member_type]=='member'?$r[orderid]:'비회원주문'?>)
							</td>
						</tr>
						<tr>
							<td class="article">휴대폰번호</td>
							<td class="conts"><?=phone_print($r[orderhtel1],$r[orderhtel2],$r[orderhtel3])?></td>
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
						<input type='submit' name='' class='input_large red' value='정보수정하기'>
						<input type="button" name="" class="input_large gray" value="목록보기" onclick="location.href=('_return.list.php?<?=enc("d" , $_PVSC)?>');">
					</span>
				</div>
			</div>
			<!-- 버튼영역 -->

</form>
<?PHP
	include_once("inc.footer.php");
?>

