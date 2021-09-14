<?PHP
	// LMH008
	// 페이지 표시
	$app_current_link = "/totalAdmin/_return.list.php";

	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where 1 ";

	if( $mode == "search" ) {
		if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(rr_rdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
		else if( $pass_sdate ) { $s_query .= " AND left(rr_rdate,10) >= '". $pass_sdate ."' "; }
		else if( $pass_edate ) { $s_query .= " AND left(rr_rdate,10) <= '". $pass_edate ."' "; }
		if( $pass_orderid ) { $s_query .= " AND o.orderid like '%". $pass_orderid ."%' "; }//주문자ID
		if( $pass_ordername ) { $s_query .= " AND o.ordername like '%". $pass_ordername ."%' "; }//주문자이름
		if( $pass_orderhtel ) { $s_query .= " AND (o.orderemail like '%".$pass_orderhtel."%' or concat(o.orderhtel1 ,'',o.orderhtel2 ,'',o.orderhtel3) like '%". rm_str($pass_orderhtel) ."%') "; }//주문자연락처
		if( $pass_ordernum !="" ) { $s_query .= " and replace(rr_ordernum,'-','') like '%".rm_str($pass_ordernum)."%' "; }
		if( $pass_type !="" ) { $s_query .= " and rr_type='{$pass_type}' "; }
		if( $pass_reason !="" ) { $s_query .= " and rr_reason='{$pass_reason}' "; }
		if( $pass_status !="" ) { $s_query .= " and rr_status='{$pass_status}' "; }
	}

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$que = " select count(*) as cnt from odtRequestReturn as rr left join odtOrderProduct as op on (op.op_uid = SUBSTRING_INDEX(rr.rr_opuid , ',',-1) and rr.rr_ordernum = op.op_oordernum) left join odtOrder as o on (o.ordernum = op.op_oordernum) $s_query ";
	$res = _MQ($que);
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$que = "
		select
			*
		from odtRequestReturn as rr
		left join odtOrderProduct as op on (op.op_uid = SUBSTRING_INDEX(rr.rr_opuid , ',',-1) and rr.rr_ordernum = op.op_oordernum)
		left join odtOrder as o on (o.ordernum = op.op_oordernum)
		" . $s_query . " and op.op_is_addoption = 'N'
		ORDER BY rr.rr_uid desc limit $count , $listmaxcount
	";
	$res = _MQ_assoc($que);
//		left join odtOrderCashlog on ( ordernum = ocs_ordernum )
?>

				<!-- 검색영역 -->
<form name=searchfrm method=post action='<?=$PHP_SELF?>'>
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
								<td class="article">검색기간</td>
								<td class="conts" colspan="10">
									<input type=text name="pass_sdate" ID="pass_sdate" class=input_text value="<?=$pass_sdate?>" readonly style="width:100px;">
									~
									<input type=text name="pass_edate" ID="pass_edate" class=input_text value="<?=$pass_edate?>" readonly style="width:100px;">
								</td>
							<tr>
								<td class="article">분류</td>
								<td class="conts">
									<select name="pass_type">
										<option value="">- 선택 -</option>
										<? foreach($arr_return_type as $k=>$v) { ?>
										<option value="<?=$k?>" <?=$k==$pass_type?'selected':''?>><?=$v?></option>
										<? } ?>
									</select>
								</td>
								<td class="article">사유</td>
								<td class="conts">
									<select name="pass_reason">
										<option value="">- 선택 -</option>
										<? foreach($arr_return_reason as $k=>$v) { ?>
										<option value="<?=$v?>" <?=$v==$pass_reason?'selected':''?>><?=$v?></option>
										<? } ?>
									</select>
								</td>
								<td class="article">상태</td>
								<td class="conts">
									<select name="pass_status">
										<option value="">- 선택 -</option>
										<? foreach($arr_return_status as $k=>$v) { ?>
										<option value="<?=$k?>" <?=$k==$pass_status?'selected':''?>><?=$v?></option>
										<? } ?>
									</select>
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


<form name=frm method=post action="_return.pro.php" target="common_frame">
<input type=hidden name=_mode value=''>
<input type=hidden name=_seachcnt value='<?=$TotalCount?>'>
<input type=hidden name=_PVSC value="<?=$_PVSC?>">
<input type=hidden name=_search_que value="<?=enc('e',$s_query)?>">

				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<!-- 리스트 제어버튼영역 //-->
					<!-- <div class="top_btn_area">
						<span class="shop_btn_pack"><a href="javascript:select_excel_send();" class="small white" title="선택엑셀다운로드" >선택엑셀다운로드</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:search_excel_send();" class="small white" title="검색엑셀다운로드" >검색엑셀다운로드(<?=number_format($TotalCount)?>)</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:mass_cancel();" class="small white" title="선택완료처리" >선택완료처리</a></span>
					</div> -->
					<!-- // 리스트 제어버튼영역 -->

					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<!-- <th scope="col" class="colorset"><input type="checkbox" name="allchk"></th> -->
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">분류</th>
								<th scope="col" class="colorset">상태</th>
								<th scope="col" class="colorset">사유</th>
								<th scope="col" class="colorset">요청일</th>
								<th scope="col" class="colorset">처리일</th>
								<th scope="col" class="colorset">주문번호<br>주문자</th>
								<th scope="col" class="colorset">상품정보</th>
								<th scope="col" class="colorset">연락처</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead>
						<tbody>
<?PHP

	if(sizeof($res) == 0 ) echo "<tr><td colspan=20 height='40'>내역이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='상세보기' class='input_small blue' onclick='location.href=(\"_return.form.php?_mode=modify&ordernum=" . $v[ordernum] . "&uid=".$v[rr_uid]."&_PVSC=" . $_PVSC . "\");'></span>";
		//$_del = ($v[op_cancel] != "Y" ? "<span class='shop_btn_pack'><input type=button value='완료처리' class='input_small gray'  onclick='cancel(\"_return.pro.php?_mode=cancel&ordernum=" . $v[ordernum] . "&uid=".$v[rr_uid]."&_PVSC=" . $_PVSC . "\");'></span>" : "");
		$_num = $TotalCount - $count - $k ;
		$v[orderhtel] = $v[orderhtel1] ? phone_print($v[orderhtel1],$v[orderhtel2],$v[orderhtel3]) : "-";
		$op_row = _MQ_assoc(" select * from odtOrderProduct where op_uid in ('".$v[rr_opuid]."') ");
		$option_name = "";
		/*foreach($op_row as $pk=>$pv) {
			$option_name = $pv['op_option1'] ? array($pv['op_option1'],$pv['op_option2'],$pv['op_option3']) : '옵션없음';
			$option_name = is_array($option_name) ? implode(' ',$option_name) : $option_name; $option_name = $option_name." ".number_format($pv[op_cnt])."개";
			$add_row = _MQ_assoc(" select concat(op_option1,' ',op_option2,' ',op_option3) as option_name, op_cnt from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$pv['op_pouid']."' and op_oordernum = '".$v['ordernum']."' and op_pcode = '".$pv['op_pcode']."' order by op_uid asc ");
			$add_option_name_array = array(); unset($add_option_name);
			if(count($add_row)>0) {
				$add_option_name = " (추가: ";
				foreach($add_row as $adk=>$adv) {
					$add_option_name_array[] = $adv['option_name']." <strong>".number_format($adv['op_cnt'])."</strong>개";
				}
				$add_option_name .= implode(" / ",$add_option_name_array);
				$add_option_name .= ") ";
			}
			$option_name .= $add_option_name;
		}*/

		// 상품정보 추출
		$ex = explode("," , $v['rr_opuid']);
		$option_res = _MQ_assoc(" select * from odtOrderProduct where op_oordernum = '".$v['rr_ordernum']."' and op_uid in ('". implode("' , '" , $ex) ."') order by op_uid asc ");
		$option_info = $option_res[0]; $option_name = $option_info['op_pname'];
		$option_name .= count($option_res) > 1 ? " 외 ".number_format(count($option_res)-1)." 건" : "";

		echo "
		<tr>
			<!--<td><input type=checkbox name='OpUid[]' value='".$v[rr_uid]."' class=class_ordernum></td>-->
			<td>". $_num ."</td>
			<td>
				<div class='btn_line_up_center'>
				" . $arr_return_type[$v[rr_type]] . "
				</div>
			</td>
			<td>
				<div class='btn_line_up_center'>
				" . $arr_return_status[$v[rr_status]] . "
				</div>
			</td>
			<td>
				<div class='btn_line_up_center'>
				" . $v[rr_reason] . "
				</div>
			</td>
			<td>". date("y.m.d",strtotime($v[rr_rdate])) ."</td>
			<td>".( rm_str($v[rr_edate])>0 ? date("y.m.d",strtotime($v[rr_edate])) : '-' )."</td>
			<td>
				". $v[rr_ordernum] ."<br>
				" . ($v[ordername]?$v[ordername]:"<span style='color:red;'>(삭제된 주문)</span>") ."
			</td>
			<td class='left'>". $option_name ."</td>
			<td>
				" . ($v[orderhtel] ? $v[orderhtel]."<br/>".$v[orderemail] : "") . "
			</td>
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