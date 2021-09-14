<?PHP

	// 페이지 표시
	$app_current_link = "/totalAdmin/_cancel.list.php" . ( $_REQUEST["style"] == "b" ? "?style=b" : "" ) ;

	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where 1 ";

	if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(op_cancel_rdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
	else if( $pass_sdate ) { $s_query .= " AND left(op_cancel_rdate,10) >= '". $pass_sdate ."' "; }
	else if( $pass_edate ) { $s_query .= " AND left(op_cancel_rdate,10) <= '". $pass_edate ."' "; }

	if( $pass_paymethod ) { $s_query .= " AND o.paymethod = '". $pass_paymethod ."' "; }//결제수단
	if( $pass_ordernum ) { $s_query .= " AND replace(op.op_oordernum,'-','') like '%". rm_str($pass_ordernum) ."%' "; }//주문번호
	if( $pass_orderid ) { $s_query .= " AND o.orderid like '%". $pass_orderid ."%' "; }//주문자ID
	if( $pass_ordername ) { $s_query .= " AND o.ordername like '%". $pass_ordername ."%' "; }//주문자이름
	if( $pass_orderhtel ) { $s_query .= " AND (concat(o.ordertel1 ,'',o.ordertel2 ,'',o.ordertel3) like '%". rm_str($pass_orderhtel) ."%' or concat(o.orderhtel1 ,'',o.orderhtel2 ,'',o.orderhtel3) like '%". rm_str($pass_orderhtel) ."%') "; }//주문자연락처
	if( $pass_bank ) { $s_query .= " AND op.op_cancel_bank = '".$pass_bank."' "; }
	if( $pass_bank_account ) { $s_query .= " AND op.op_cancel_bank_account like '%".$pass_bank_account."%' "; }
	if( $pass_bank_name) { $s_query .= " AND op.op_cancel_bank_name like '%".$pass_bank_name."%' "; }
	if( $pass_cancel ) { $s_query .= " AND op.op_cancel = '".$pass_cancel."' "; } else { $s_query .= " AND (op.op_cancel = 'Y' OR op.op_cancel = 'R')"; }
	if( $pass_cancel_type ) { $s_query .= " and op.op_cancel_type = '".$pass_cancel_type."' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$que = " select count(*) as cnt from odtOrderProduct as op left join odtOrder as o on (o.ordernum = op.op_oordernum) $s_query ";
	$res = _MQ($que);
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$que = " 
		select 
			* ,
			concat(o.ordertel1 ,'-',o.ordertel2 ,'-',o.ordertel3) as ordertel ,
			concat(o.orderhtel1 ,'-',o.orderhtel2 ,'-',o.orderhtel3) as orderhtel
		from odtOrderProduct as op left join odtOrder as o on (o.ordernum = op.op_oordernum)
		" . $s_query . " and op.op_is_addoption = 'N'
		ORDER BY op.op_cancel_rdate desc limit $count , $listmaxcount 
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
								<td class="article">주문자ID</td>
								<td class="conts"><input type=text name="pass_orderid" class=input_text value="<?=$pass_orderid?>"></td>
								<td class="article">주문자이름</td>
								<td class="conts"><input type=text name="pass_ordername" class=input_text value="<?=$pass_ordername?>"></td>
								<td class="article">주문자연락처</td>
								<td class="conts"><input type=text name="pass_orderhtel" class=input_text value="<?=$pass_orderhtel?>"></td>
							</tr>
							<tr>
								<td class="article">주문번호</td>
								<td class="conts"><input type=text name="pass_ordernum" class=input_text value="<?=$pass_ordernum?>"></td>
								<td class="article">결제수단</td>
								<td class="conts"><?=_InputSelect( "pass_paymethod" , array_keys($arr_paymethod_name) , $pass_paymethod , "" , array_values($arr_paymethod_name) , "-결제수단-")?></td>
								<td class="article">검색기간</td>
								<td class="conts">
									<input type=text name="pass_sdate" ID="pass_sdate" class=input_text value="<?=$pass_sdate?>" readonly style="width:100px;">
									~ 
									<input type=text name="pass_edate" ID="pass_edate" class=input_text value="<?=$pass_edate?>" readonly style="width:100px;">
								</td>
							</tr>
							<tr>
								<td class="article">환불은행</td>
								<td class="conts">
									<select name="pass_bank">
										<option value="">- 선택 -</option>
										<? foreach($ksnet_bank as $k=>$v) { ?>
										<option value="<?=$k?>" <?=$pass_bank==$k?'selected':''?>><?=$v?></option>
										<? } ?>
									</select>
								</td>
								<td class="article">환불계좌번호</td>
								<td class="conts">
									<input type="text" name="pass_bank_account" class="input_text" value="<?=$pass_bank_account?>"/>
								</td>
								<td class="article">환불예금주</td>
								<td class="conts">
									<input type="text" name="pass_bank_name" class="input_text" value="<?=$pass_bank_name?>"/>
								</td>
							</tr>
							<tr>
								<td class="article">취소상황</td>
								<td class="conts">
									<select name="pass_cancel">
										<option value="">- 선택 -</option>
										<option value="Y" <?=$pass_cancel=='Y'?'selected':''?>>취소완료</option>
										<option value="R" <?=$pass_cancel=='R'?'selected':''?>>취소요청중</option>
									</select>
								</td>
								<td class="article">환불수단</td>
								<td class="conts" colspan="3">
									<select name="pass_cancel_type">
										<option value="">- 선택 -</option>
										<option value="pg" <?=$pass_cancel_type=='pg'?'selected':''?>>PG연동</option>
										<option value="point" <?=$pass_cancel_type=='point'?'selected':''?>>포인트</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="conts" colspan="6">
									<?=_DescStr("<b>부분취소 요청된 주문을 확인하고 최종 취소처리 할 수 있습니다.</b>")?>
									<?=_DescStr("주문내역에 대한 <b>엑셀파일</b>은 검색조건에 맞는 내역만 저장됩니다.")?>
									<?=_DescStr("카드결제는 취소처리시 PG 연동되며, 전액적립금결제를 제외한 다른 결제수단일 경우 환불계좌로 송금 후 처리하시기 바랍니다.")?>
									<?=_DescStr("포인트로 환불을 요청한 경우 PG 연동되지 않으며 처리 즉시 고객 적립금으로 환불됩니다.")?>
									<?=_DescStr("<b>취소 요청한 금액보다 상계가능한 정산예정금액이 부족할 경우 부분취소가 불가능합니다. 이러한 경우 PG사에 문의하시기 바랍니다.</b>")?>
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


<form name=frm method=post action="_cancel.pro.php" target="common_frame">
<input type=hidden name=_mode value=''>
<input type=hidden name=_seachcnt value='<?=$TotalCount?>'>
<input type=hidden name=_PVSC value="<?=$_PVSC?>">
<input type=hidden name=_search_que value="<?=enc('e',$s_query)?>">

				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<!-- 리스트 제어버튼영역 //-->
					<div class="top_btn_area">
<?if( $pass_paystatus == "Y") {  // 결제완료된 주문목록에만 나오게 함?>
						<span class="shop_btn_pack"><a href="javascript:select_auth_send();" class="small white" title="선택엑셀다운로드" >선택결제승인</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
<?}?>
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
								<th scope="col" class="colorset">요청일</th>
								<th scope="col" class="colorset">취소일</th>
								<th scope="col" class="colorset">주문번호<br>주문자</th>
								<th scope="col" class="colorset">상품정보</th>
								<th scope="col" class="colorset">연락처<br/>환불계좌</th>
								<th scope="col" class="colorset">결제방법<br>환불금액</th>
								<th scope="col" class="colorset">결제상황</th>
								<th scope="col" class="colorset">환불수단<br/>취소상황</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody>
<?PHP

	if(sizeof($res) == 0 ) echo "<tr><td colspan=20 height='40'>주문 내역이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='상세보기' class='input_small blue' onclick='location.href=(\"_cancel.form.php?_mode=modify&ordernum=" . $v[ordernum] . "&uid=".$v[op_uid]."&_PVSC=" . $_PVSC . "\");'></span>";
		$_del = ($v[op_cancel] != "Y" ? "<span class='shop_btn_pack'><input type=button value='취소처리' class='input_small gray'  onclick='cancel(\"_cancel.pro.php?_mode=cancel&ordernum=" . $v[ordernum] . "&op_uid=".$v[op_uid]."&_PVSC=" . $_PVSC . "\");'></span>" : "");
		$_reqdel = ($v[op_cancel] != "Y" ? "<span class='shop_btn_pack'><input type=button value='부분취소요청삭제' class='input_small red'  onclick='cancel(\"_cancel.pro.php?_mode=req_cancel&ordernum=" . $v[ordernum] . "&op_uid=".$v[op_uid]."&_PVSC=" . $_PVSC . "\");'></span>" : "");

		$cancel_price = ($v[op_pprice] + $v[op_poptionprice]) * $v[op_cnt] + $v[op_delivery_price] + $v[op_add_delivery_price] - $v['op_cancel_discount_price'] ;// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC  ;

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
			where op.op_oordernum='". $v[ordernum] ."' and op.op_uid = '".$v[op_uid]."' and op.op_is_addoption = 'N'
			order by p.code desc
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
				$itemName .= " (선택:".$sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3].")";
				$itemName .= " " . $sv[op_cnt]."개";
			}
			else {
				$itemName .= "<B>". $sv[op_pname] ."</B>";
				$itemName .= " " . $sv[op_cnt]."개";
			}
			$add_res = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$sv['op_pouid']."' and op_oordernum = '".$sv['op_oordernum']."' ");
			if( count($add_res) > 0 ){
				foreach($add_res as $adk=>$adv) {
					$itemName .= "<br/>(추가:".$adv[op_option1]." ".$adv[op_option2]." ".$adv[op_option3].")";
					$itemName .= " " . $adv[op_cnt]."개";
					$cancel_price += ($adv[op_pprice] + $adv[op_poptionprice]) * $adv[op_cnt] + $adv[op_delivery_price] + $adv[op_add_delivery_price] ;
				}
			}

			// 쿠폰이미지/주의사항이 등록되어있지않으면 취소선으로 표시한다.
			$itemName =  ($warning1 == "Y" || $warning2 == "Y" ? "<strike>".$itemName."</strike>" : $itemName);
			$itemName =  ($sk <> 0 ? "<br>" : "") . $itemName ;

			// -- 발송여부 ---
			$itemName .= "</li><li style='display:inline; float:right; padding-right:3px; padding-top:3px;'><span class='shop_state_pack'>";
			if($sv[op_orderproduct_type] == "product") {
				$itemName .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발송완료</span>" : "<span class='lightgray'>발송대기</span>");
			} 
			else {
				$itemName .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발급완료</span>" : "<span class='lightgray'>발급대기</span>");
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


        // -- 취소상태 ---
        $_cancelstatus = "<li style='display:inline; float:left; clear:both; padding-top:3px;'><span class='shop_state_pack'>";
        $_cancelstatus .= $v[op_cancel] == 'R' ? "<span class='lightgray'>취소요청중</span>" : "<span class='red'>취소완료</span>";
        $_cancelstatus .= "</span></li>";
        // -- 취소상태 ---

        // -- 환불수단 ---
        $_canceltype = $v[op_cancel_type]=='pg' ? 'PG연동' : '포인트';
        $_canceltype = $_canceltype.'<br/>';
        // -- 환불수단 ---


        // -- 결제진행사항 ---
        $orderstepArray = array(
			"before"=>"<span class='green'>주문서작성중</span>",
			"ing"=>"<span class='blue'>진행중</span>",
			"cancle"=>"<span class='gray'>사용자취소</span>",
			"fail"=>"<span class='gray' onclick='alert(\"".$v[ordersau]."\")' style='cursor:pointer;'>결제실패[사유]</span>",
			"finish"=>"<span class='red'>정상처리</span>"
		);
        if( !preg_match("/결제완료/i" , $_paystatus_ ) && !in_array($v[paymethod] , array("B" , "E"))) {
            $orderstep = "<li style='display:inline; float:left; clear:both; padding-top:3px;'><span class='shop_state_pack'>". $orderstepArray[$v[orderstep]] ."</span></li>";
        } 
		else if($v[paystatus2] == "C") {
			$orderstep = "<li style='display:inline; float:left; clear:both; padding-top:3px;'><span class='shop_state_pack'><span class='blue'>취소요청</span></span></li>";
        }
        // -- 결제진행사항 ---

		// --- 현금영수증미발행건 : 무통장/현금영수증 연동안되는 pg사 처리 안됨 ---- 
//		if($v[taxorder]=="Y" && !$v[ocs_tid]){
//			$_cashstatus_ = "<li style='display:inline; float:left; clear:both; padding-top:3px;'><span class='shop_state_pack'><span class='purple'>현금영수증</span></span></li>";
//		}else{
//			$_cashstatus_ = "";
//		}
		// --- 현금영수증미발행건 ----

		// 모바일 아이콘 LDD002
		$device_icon = '<span class="shop_state_pack" style="display:block"><span class="blue">PC주문</span></span>';
		if($v['mobile'] == 'Y') $device_icon = '<span class="shop_state_pack" style="display:block"><span class="orange">MOBILE주문</span></span>';

		echo "
							<tr>
								<td><input type=checkbox name='OpUid[]' value='".$v[op_uid]."' class=class_ordernum></td>
								<td>". $_num ."</td>
								<td>". date("y.m.d",strtotime($v[op_cancel_rdate])) ."</td>
								<td>".( rm_str($v[op_cancel_cdate])>0 ? date("y.m.d",strtotime($v[op_cancel_cdate])) : '-' )."</td>
								<td>
									". $v[ordernum] ."<br>
									" . $v[ordername] . " <A HREF='_member.form.php?_mode=modify&id=" . $v[orderid] . "' target='_blank' ><U>(" . $v[orderid] . ")</U></A>
								</td>
								<td class='left'>". $device_icon.$tmp_content ."</td>
								<td>
									" . ($v[orderhtel] ? $v[orderhtel] : "") . "<br/>
									" . ( $v[paymethod]!='C' ? $ksnet_bank[$v[op_cancel_bank]]." ".$v[op_cancel_bank_account]." ".$v[op_cancel_bank_name] : "-" ) . "
								</td>
								<td>
									". $arr_paymethod_name[$v[paymethod]] ."<br>
									<b>" . ($cancel_price > 0 ? "<font color='FF6600'>".number_format($cancel_price)."원</font>" : "<font color='#0000FF' style='display:none;'>전액적립금</font>") . "</b>
								</td>
								<td><div class='btn_line_up_center'>
									" . $_cashstatus_ . "
									" . $_paystatus_ . "
									" . $orderstep . "
								</div></td>
								<td>
									<div class='btn_line_up_center'>
									" . $_canceltype.$_cancelstatus . "
									</div>
								</td>
								<td>
									<div class='btn_line_up_center'>
										". $_mod."
										<span class='shop_btn_pack'><span class='blank_3'></span></span>
										". $_del."
									</div>
									" . ($_reqdel ? "<br><div class='btn_line_up_center' >" . $_reqdel . "</div>" : "") . "
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
			$("input[name=_mode]").val("mass");
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