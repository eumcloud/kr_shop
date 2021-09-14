<?PHP

	// 페이지 표시
	$app_current_link = "/totalAdmin/_order.list.php" . ( $_REQUEST["style"] == "b" ? "?style=b" : "" ) ;

	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where canceled='N' AND orderstatus='Y' ";

	// 무통장 관리 페이지 일 경우 처리
	$pass_paymethod = ( $style == "b" ? "B" : $pass_paymethod);
	$pass_paystatus = ( $style == "b" ? "N" : $pass_paystatus);


	if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(orderdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
	else if( $pass_sdate ) { $s_query .= " AND left(orderdate,10) >= '". $pass_sdate ."' "; }
	else if( $pass_edate ) { $s_query .= " AND left(orderdate,10) <= '". $pass_edate ."' "; }

	if( $pass_paymethod ) { $s_query .= " AND paymethod = '". $pass_paymethod ."' "; }//결제수단
	$pass_paystatus = $pass_paystatus ? $pass_paystatus : "Y";// 결제상태 미지정시 Y 고정
	if( $pass_paystatus ) { $s_query .= " AND paystatus = '". $pass_paystatus ."' "; }//결제상태
	if( $pass_paystatus2 ) { $s_query .= " AND paystatus2 = '". $pass_paystatus2 ."' "; }//결제승인
	if( $pass_ordernum ) { $s_query .= " AND ordernum like '%". $pass_ordernum ."%' "; }//주문번호
	if( $pass_orderid ) { $s_query .= " AND orderid like '%". $pass_orderid ."%' "; }//주문자ID
	if( $pass_ordername ) { $s_query .= " AND ordername like '%". $pass_ordername ."%' "; }//주문자이름
	if( $pass_orderhtel ) { $s_query .= " AND (concat_ws('',ordertel1,ordertel2,ordertel3) like '%". rm_str($pass_orderhtel) ."%'  or concat_ws('',orderhtel1,orderhtel2,orderhtel3) like '%". rm_str($pass_orderhtel) ."%' or concat_ws('',userhtel1,userhtel2,userhtel3) like '%". rm_str($pass_orderhtel) ."%') "; }//주문자연락처
	if( $pass_member_type ) { $s_query .= " AND member_type = '". $pass_member_type ."' "; }//회원타입
	if($pass_mobile_order) {  $s_query .= " and mobile = '".$pass_mobile_order."' "; } // 구매기기 LDD002
	if($pass_payname) { $s_query .= " and payname like '%".$pass_payname."%' "; } // 무통장 입금자명

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$que = " select count(*) as cnt from odtOrder $s_query ";
	$res = _MQ($que);
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$que = " 
		select * from odtOrder 
		" . $s_query . "
		ORDER BY serialnum desc limit $count , $listmaxcount 
	";
	$res = _MQ_assoc($que);
//		left join odtOrderCashlog on ( ordernum = ocs_ordernum )
?>

				<!-- 검색영역 -->
<form name=searchfrm method=post action='<?=$PHP_SELF?><?=($style ? "?style=".$style : "")?>'>
<input type=hidden name=mode value=search>
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="100px"/><col width="200px"/><col width="100px"/><col width="200px"/><col width="100px"/><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<td class="article">주문번호</td>
								<td class="conts"><input type=text name="pass_ordernum" class=input_text value="<?=$pass_ordernum?>"></td>
								<td class="article">주문자ID</td>
								<td class="conts"><input type=text name="pass_orderid" class=input_text value="<?=$pass_orderid?>"></td>
								<td class="article">주문자이름</td>
								<td class="conts"><input type=text name="pass_ordername" class=input_text value="<?=$pass_ordername?>"></td>
							</tr>
<?PHP
	// 무통장 입금 관리 페이지에서는 보이지 않게 함
	if( $style != "b"){
?>
							<tr>
								<td class="article">결제수단</td>
								<td class="conts"><?=_InputSelect( "pass_paymethod" , array_keys($arr_paymethod_name) , $pass_paymethod , "" , array_values($arr_paymethod_name) , "-결제수단-")?></td>
								<td class="article">결제상태</td>
								<td class="conts"><?=_InputSelect( "pass_paystatus" , array("Y" , "N") , $pass_paystatus , "" , array("결제완료" , "결제대기") , "-결제여부-")?></td>
								<td class="article">결제승인</td>
								<td class="conts"><?=_InputSelect( "pass_paystatus2" , array("Y" , "N") , $pass_paystatus2 , "" , array("결제승인" , "승인대기") , "-승인여부-")?></td>
							</tr>
<?}?>
							<tr>
								<td class="article">검색기간</td>
								<td class="conts" colspan=3>
									<input type=text name="pass_sdate" ID="pass_sdate" class=input_text value="<?=$pass_sdate?>" readonly style="width:100px;">
									~ 
									<input type=text name="pass_edate" ID="pass_edate" class=input_text value="<?=$pass_edate?>" readonly style="width:100px;">
								</td>
								<td class="article">주문자연락처</td>
								<td class="conts"><input type=text name="pass_orderhtel" class=input_text value="<?=$pass_orderhtel?>"></td>
							</tr>
							<tr>
								<td class="article">회원타입</td>
								<td class="conts"><?=_InputSelect( "pass_member_type" , array("member" , "guest") , $pass_member_type , "" , array("회원" , "비회원") , "-회원타입-")?></td>
								<?php // LDD002 { ?>
								<td class="article">구매기기</td>
								<td class="conts">
									<?=_InputSelect( "pass_mobile_order" , array("Y" , "N") , $pass_mobile_order , "" , array("모바일구매" , "PC구매") , "-구매기기-")?>
								</td>
								<?php // } LDD002 ?>
								<td class="article">무통장입금자명</td>
								<td class="conts">
									<input type="text" name="pass_payname" class="input_text" value="<?=$pass_payname?>"/>
								</td>
							</tr>
							<tr>
								<td class="conts" colspan="6">
									<?=_DescStr("<b>주문정보를 삭제할 경우 상품 재고량과 회원이 사용한 적립금이 환원되지 않습니다.</b>")?>
									<?=_DescStr("상품의 재고량과 회원이 사용한 적립금이 환원되기를 바란다면 반드시 <b><font color='red'>주문취소로 처리 하셨다가 삭제</font></b>하시기 바랍니다.")?>
									<?=_DescStr("<b>회원주문</b>인 경우 <b>주문번호가 볼드체(굵은글씨)로 표시</b> 됩니다.")?>
									<?=_DescStr("주문내역에 대한 <b>엑셀파일</b>은 검색조건에 맞는 내역만 저장됩니다.")?>
									<?php	
										# -- 2016-11-28 LCY :: 무통장다수개처리
										if( $style == 'b') {  
									?> 
									<?=_DescStr("무통장의 경우 선택입금확인 처리를 할 시 너무 많은 선택을 처리할경우 시스템이 멈출 수 있으니, 5개씩 나누어서 처리하시는게 좋습니다.")?>
									<?php 
										}
										# -- 2016-11-28 LCY :: 무통장다수개처리
									?>

								</td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == search) {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="전체목록" >전체목록</a></span>
							<?}?>
						</div>
					</div>
				</div>
</form>
				<!-- // 검색영역 -->


<form name=frm method=post action="_order.pro.php" target="common_frame">
<input type=hidden name=_mode value=''>
<input type=hidden name=_seachcnt value='<?=$TotalCount?>'>
<input type=hidden name=_PVSC value="<?=$_PVSC?>">
<input type=hidden name=_search_que value="<?=enc('e',$s_query)?>">

				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<!-- 리스트 제어버튼영역 //-->
					<div class="top_btn_area">
<?if( $pass_paystatus == "Y") {  // 결제완료된 주문목록에만 나오게 함?>
						<span class="shop_btn_pack"><a href="javascript:select_auth_send();" class="small white" title="선택결제승인" >선택결제승인</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
<?}?>
<?php	
	# -- 2016-11-28 LCY :: 무통장다수개처리
	if( $style == 'b') {  
?> 
						<span class="shop_btn_pack"><a href="javascript:select_paystatus_send();" class="small white" title="선택입금확인" >선택입금확인</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
<?php 
	}
	# -- 2016-11-28 LCY :: 무통장다수개처리
?>


						<span class="shop_btn_pack"><a href="javascript:select_excel_send();" class="small white" title="선택엑셀다운로드" >선택엑셀다운로드</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:search_excel_send();" class="small white" title="검색엑셀다운로드" >검색엑셀다운로드(<?=number_format($TotalCount)?>)</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:mass_cancel();" class="small white" title="선택주문취소" >선택주문취소</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->

					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<th scope="col" class="colorset"><input type="checkbox" name="allchk"></th>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">주문일</th>
								<th scope="col" class="colorset">주문번호<br>주문자</th>
								<th scope="col" class="colorset">상품정보</th>
								<th scope="col" class="colorset">연락처</th>
								<th scope="col" class="colorset">결제방법<br>결제금액</th>
								<th scope="col" class="colorset">결제상황</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody>
<?PHP

	if(sizeof($res) == 0 ) echo "<tr><td colspan=9 height='40'>주문 내역이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='상세보기' class='input_small blue' onclick='location.href=(\"_order.form.php?_mode=modify&ordernum=" . $v[ordernum] . "&_PVSC=" . $_PVSC . "\");'></span>";
		$_del = ($v[canceled] == "N" ? "<span class='shop_btn_pack'><input type=button value='주문취소' class='input_small gray'  onclick='cancel(\"_order.pro.php?_mode=cancel&ordernum=" . $v[ordernum] . "&_PVSC=" . $_PVSC . "\");'></span>" : "");
		if($v[paymethod]=='V' && $v[paystatus]=='Y') {
			$_del = ($v[canceled] == "N" ? "<span class='shop_btn_pack'><a class='small gray' onclick='alert(\"결제완료된 가상계좌 건은 상세페이지에서 환불계좌 정보 입력 후 취소 가능합니다.\");location.href=(\"_order.form.php?_mode=modify&ordernum=" . $v[ordernum] . "&_PVSC=" . $_PVSC . "\");'>주문취소</a></span>" : "");
		}

		$_num = $TotalCount - $count - $k ;

		// -- 상품정보 추출 ---
		$tmp_content = ""; // 상품정보 - 문장
		$tmp_pname = ""; // 첫번째 옵션 상품명 임시 저장
		$sque = "
			SELECT 
				op.* , ttt.ttt_value as comment3
			FROM odtOrderProduct as op 
			left join odtProduct as p on ( p.code=op.op_pcode )
			left join odtTableText as ttt on ( p.serialnum = ttt.ttt_datauid and ttt.ttt_tablename = 'odtProduct' and ttt.ttt_keyword = 'comment3')
			where op.op_oordernum='". $v[ordernum] ."' order by p.code, op.op_is_addoption desc
		";
		$sres = _MQ_assoc($sque);
		foreach($sres as $sk=>$sv) {

			// -- 다수 옵션일 경우 상품명만 미리 추출 ---
			$itemName = "";// 옵션정보 초기화
			if($tmp_pname <> $sv[op_pname] && $sv[op_option1] ) {
				$tmp_pname = $sv[op_pname];
				$itemName .= "<li style='display:inline; float:left;padding-left:3px; clear:both; padding-top:3px;'><B>". $sv[op_pname] ."</B></li>";
			}


			$itemName .= "<li style='display:inline; float:left;padding-left:3px; clear:both; padding-top:3px;'>";

			// 쿠폰일경우 이미지/주의사항확인
			$warning1 = "N";  //쿠폰이미지
			$warning2 = "N";  //쿠폰주의사항
			if($sv[op_orderproduct_type]=="coupon") {
				if(!$sv[comment3]) $warning2 = "Y";
			}

			// 옵션값 추출(OrderNumValue:주문번호 offset:주문일련번호)
			if($sv[op_option1]) {   // 해당상품에 대한 옵션내역이 있으면
				$itemName .= " (".($sv[op_is_addoption]=="Y" ? "추가" : "선택").":".$sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3].")";
			}
			else {
				$itemName .= "<B>". $sv[op_pname] ."</B>";
			}
			$itemName .= " " . $sv[op_cnt]."개";

			// 쿠폰이미지/주의사항이 등록되어있지않으면 취소선으로 표시한다.
			$itemName =  ($warning1 == "Y" || $warning2 == "Y" ? "<strike>".$itemName."</strike>" : $itemName);
			$itemName =  ($sk <> 0 ? "<br>" : "") . $itemName ;

			// -- 발송여부 --- LMH001
			$itemName .= "</li><li style='display:inline; float:right; padding-right:3px; padding-top:3px;'><span class='shop_state_pack'>";

			if($sv[op_cancel]=='Y') { $itemName .= "<span class='gray'>주문취소</span>"; }
			else {
				if($sv[op_orderproduct_type] == "product") { 
					$itemName .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발송완료</span>" : "<span class='lightgray'>발송대기</span>"); 
				} 
				else { 
					$itemName .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발급완료</span>" : "<span class='lightgray'>발급대기</span>"); 
				}
			}
			$itemName .= "</span></li>";
			// -- 발송여부 ---

			$tmp_content .= $itemName;
        }
		// -- 상품정보 추출 ---


        // -- 결제상태 ---
		$_paystatus_ = "<li style='display:inline; float:left; clear:both; padding-top:3px;'><span class='shop_state_pack'>";
        if($v[paystatus] == "Y") {
			$_paystatus_ .= ( $v[paystatus2] =="Y" ? "<span class='red'>결제승인</span>" : "<span class='orange'>결제확인</span>" );
        }
        else if(in_array($v[paymethod] , array("B" , "E"))) {
			$_paystatus_ .= "<span class='green'>결제전</span>" ;
		}
        else {
			$_paystatus_ .= ($v[orderstep] == "fail" ? "<span class='gray'>결제실패</span>" : "<span class='lightgray'>결제대기</span>");
        }
		$_paystatus_ .= "</span></li>";
        // -- 결제상태 ---


        // -- 결제진행사항 ---
		unset($orderstep);
        $orderstepArray = array(
			"before"=>"<span class='green'>주문서작성중</span>",
			"ing"=>"<span class='blue'>진행중</span>",
			"cancle"=>"<span class='gray'>사용자취소</span>",
			"fail"=>"<span class='gray' onclick='alert(\"".$v[ordersau]."\")' style='cursor:pointer;'>결제실패[사유]</span>",
			"finish"=>"<span class='red'>정상처리</span>"
		);
        if( !($v[paystatus] != "Y" && $v[paystatus2] != "Y" && in_array($v[paymethod] , array("B" , "E"))) ) {
            $orderstep = "<li style='display:inline; float:left; clear:both; padding-top:3px;'><span class='shop_state_pack'>". $orderstepArray[$v[orderstep]] ."</span></li>";
        } 
		else if($v[paystatus2] == "C") {
			$orderstep = "<li style='display:inline; float:left; clear:both; padding-top:3px;'><span class='shop_state_pack'><span class='blue'>취소요청</span></span></li>";
        }
        // -- 결제진행사항 ---

		// --- 현금영수증미발행건 : 무통장/현금영수증 연동안되는 pg사 처리 안됨 ---- 
		// if($v[taxorder]=="Y" && !$v[ocs_tid]){
		//	$_cashstatus_ = "<li style='display:inline; float:left; clear:both; padding-top:3px;'><span class='shop_state_pack'><span class='purple'>현금영수증</span></span></li>";
		// }else{
		//	$_cashstatus_ = "";
		// }
		// --- 현금영수증미발행건 ----

		// 모바일 아이콘 LDD002
		$device_icon = '<span class="shop_state_pack" style="display:block"><span class="blue">PC주문</span></span>';
		if($v['mobile'] == 'Y') $device_icon = '<span class="shop_state_pack" style="display:block"><span class="orange">MOBILE주문</span></span>';


		if($v['order_type']=="product" || $v['order_type']=="both") {
			$tel = tel_format($v[rectel1]."-".$v[rectel2]."-".$v[rectel3]);
			$htel = tel_format($v[rechtel1]."-".$v[rechtel2]."-".$v[rechtel3]);
		}
		else if($v['order_type']=="coupon" ) {
			$tel = tel_format($v[ordertel1]."-".$v[ordertel2]."-".$v[ordertel3]);
			$htel = tel_format($v[userhtel1]."-".$v[userhtel2]."-".$v[userhtel3]);
		}

		$tel = $tel ? $tel : tel_format($v[ordertel1]."-".$v[ordertel3]."-".$v[ordertel2]);
		$htel = $htel ? $htel : tel_format($v[orderhtel1]."-".$v[orderhtel2]."-".$v[orderhtel3]);
		$arr_tel = array_filter(array($tel , $htel));

		echo "
							<tr>
								<td><input type=checkbox name='OrderNum[]' value='".$v[ordernum]."' class=class_ordernum></td>
								<td>". $_num ."</td>
								<td>". date("y.m.d",strtotime($v[orderdate])) ."</td>
								<td>
									". $v[ordernum] ."<br>
									" . $v[ordername] . ( $v[member_type] == "member" ? "<A HREF='_member.form.php?_mode=modify&id=" . $v[orderid] . "' target='_blank' ><U>(" . $v[orderid] . ")</U></A>" : "<span style='color:red;'>(비회원)</FONT>" ) . " 
								</td> 
								<td class='left'>". $device_icon . $tmp_content ."</td>
								<td>" . implode("<br>" , $arr_tel) . "</td>
								<td>
									". $arr_paymethod_name[$v[paymethod]] ."<br>
									<b>" . ($v[tPrice] > 0 ? "<font color='FF6600'>".number_format($v[tPrice])."원</font>" : "<font color='#0000FF'>전액적립금</font>") . "</b>
								</td>
								<td><div class='btn_line_up_center'>
									" . $_cashstatus_ . "
									" . $_paystatus_ . "
									" . $orderstep . "
								</div></td>
								<td>
									<div class='btn_line_up_center'>
										". $_mod."
										<span class='shop_btn_pack'><span class='blank_3'></span></span>
										". $_del."
									</div>
								</td>
							</tr>
		";
	}
?>

						</tbody> 
					</table>

					<!-- 페이지네이트 -->
					<div class="list_paginate">			
						<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
					</div>
					<!-- // 페이지네이트 -->

			</div>
</form>




<?PHP
	include_once("inc.footer.php");
?>




<SCRIPT>

	// # -- 2016-11-28 LCY :: 무통장다수개처리
	function select_paystatus_send()
	{
		if(confirm("선택된 항목을 입금확인 처리 하시겠습니까?") == false){
			return false;
		}

		// -- 체크항목 
		 if($('.class_ordernum').is(":checked")){
			$("input[name=_mode]").val("select_paystatus");
			$("form[name=frm]")[0].submit();
		 }
		 else { // 체크 안되었을 시
			 alert('1건 이상 선택시 입금확인이 가능합니다.');
		 }

	}
	// # -- 2016-11-28 LCY :: 무통장다수개처리 

	// - 결제승인 ---
	 function select_auth_send() {
		 if($('.class_ordernum').is(":checked")){
			$("input[name=_mode]").val("auth");
			$("form[name=frm]")[0].submit();
		 }
		 else {
			 alert('1건 이상 선택시 결제승인이 가능합니다..');
		 }
	 }
	// - 결제승인 ---
	// - 선택엑셀 ---
	 function select_excel_send() {
		 if($('.class_ordernum').is(":checked")){
			$("input[name=_mode]").val("select_excel");
			$("form[name=frm]")[0].submit();
		 }
		 else {
			 alert('1건 이상 선택시 엑셀다운로드가 가능합니다..');
		 }
	 }
	// - 선택엑셀 ---
	// - 검색엑셀 ---
	 function search_excel_send() {
		 if($('input[name=_seachcnt]').val()*1 > 0 ){
			$("input[name=_mode]").val("search_excel");
			$("form[name=frm]")[0].submit();
		 }
		 else {
			 alert('1건 이상 검색시 엑셀다운로드가 가능합니다..');
		 }
	 }
	 // - 검색엑셀 ---
	 // - 선택취소 ---
	 function mass_cancel() {
	 	var c=confirm('정말 주문을 취소하시겠습니까?');
	 	if(c) {
		 if($('.class_ordernum:checked').length > 0 ){
			$("input[name=_mode]").val("mass_cancel");
			$("form[name=frm]")[0].submit();
		 }
		}
		 else {
			 alert('1건 이상 선택하세요.');
		 }
	 }
	 // - 선택취소 ---
	// - 전체선택해제 ---
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.class_ordernum').attr('checked',true);
			}
			else {
				$('.class_ordernum').attr('checked',false);
			}
		});
	});
	// - 전체선택해제 ---
</SCRIPT>

<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
        $("#pass_sdate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_sdate").datepicker( "option",$.datepicker.regional["ko"] );

        $("#pass_edate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_edate").datepicker( "option",$.datepicker.regional["ko"] );
    });
</script>