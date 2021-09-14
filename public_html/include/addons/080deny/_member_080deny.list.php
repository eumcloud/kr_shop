<?php

	if( !( $row_setup['s_deny_use'] == "Y" && $row_setup['s_deny_tel'] ) )  { 
			error_loc_msg("_addons.php?pass_menu=080deny/_receipt.form","080수신거부설정을 확인해주시기 바랍니다.");
	}
	
	// 수신거부 처리상태
	$arr_080deny_status = array(
		'OK' => '정상적으로 수신거부처리' , 
		'MULTI' => '다수검색으로 인한 미처리' , 
		'NO' => '미검색으로 인한 미처리' , 
		'FALSE' => '080 수신거부 관리자 미설정 오류' 
	);

?>
				<!-- 검색영역 -->
				<div class="form_box_area">
<form name=searchfrm method=post action='<?=$_SERVER["PHP_SELF"]?>'>
<input type='hidden' name='mode' value='search'>
<input type='hidden' name='pass_menu' value='<?=$pass_menu?>'>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">거부전화번호</td>
								<td class="conts"><input type=text name='pass_hp'  class='input_text' value='<?=$pass_hp?>'></td>
								<td class="article">적용상태</td>
								<td class="conts"><?=_InputSelect( "pass_status" , array_keys($arr_080deny_status), $pass_status, "" , array_values($arr_080deny_status) , '') ?></td>
							</tr>
						</tbody> 
					</table>

					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>?pass_menu=<?=$pass_menu?>" class="medium gray" title="목록" >전체목록</a></span>
							<?}?>							
						</div>
					</div>

					<?=_DescStr("정상적으로 수신거부처리 : 수신거부요청번호가 검색되어 해당 회원을 <strong>수신거부로 처리</strong>한 상태입니다.")?>
					<?=_DescStr("다수검색으로 인한 미처리 : 수신거부요청번호가 <strong>다수 검색</strong>되어 해당 회원을 수신거부로 처리하지 못한 상태입니다. 운영자께서 확인한 후 수동으로 처리해주시기 바랍니다.")?>
					<?=_DescStr("미검색으로 인한 미처리 : 수신거부요청번호가 검색되지 않아 수신거부를 처리하지 못한 상태입니다.")?>
					<?=_DescStr("080 수신거부 관리자 미설정 오류 : 환경설정 &gt; 080수신거부설정이 정상적으로 등록되지 않은 상태입니다. 해당 서비스를 이용하기 위해서는 설정을 등록해주시기 바랍니다.")?>

</form>
				</div>
				<!-- // 검색영역 -->


<form name='frm' method='post' action="/include/addons/080deny/_member_080deny.pro.php">
<input type=hidden name=_mode value=''>
<input type=hidden name=_PVSC value=<?=$_PVSC?>>
				<!-- 리스트영역 -->
				<div class="content_section_inner">
					

					<div class="ctl_btn_area">
						<span class="shop_btn_pack"><a href="#none" class="small white" onclick="selectDelete()" title="선택삭제" >선택삭제</a></span>
					</div>


					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<th scope="col" class="colorset"><input type="checkbox" name="allchk"></th>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">수신거부번호</th>
								<th scope="col" class="colorset">수신거부요청번호</th>
								<th scope="col" class="colorset">적용상태</th>
								<th scope="col" class="colorset">거부요청시간</th>
								<th scope="col" class="colorset">기록시간</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 

<?PHP
	######## 검색 체크
	$s_query = " from odtMember080Deny where 1 ";
	if( $pass_hp !="" ) { $s_query .= " and md_hp like '%${pass_hp}%' "; }
	if( $pass_status !="" ) { $s_query .= " and md_status = '${pass_status}' "; }

	$s_orderby = " ORDER BY md_uid desc ";

	$listmaxcount = 30 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select *  $s_query $s_orderby limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='200'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v){

		$_del = "<span class='shop_btn_pack'><a href='#none'  onclick='del(\"/include/addons/080deny/_member_080deny.pro.php?_mode=delete&_uid=" . $v[md_uid] . "&_PVSC=" . $_PVSC . "\");' class='small gray' title='삭제' >삭제</a></span>";

		$_num = $TotalCount - $count - $k ;

		echo "
			<tr>
				<td><input type=checkbox name='chk_pcode[".$v[md_uid]."]' value='Y' class=class_pcode></td>
				<td>" . $_num ."</td>
				<td>".$v['md_refusal_num']."</td>
				<td>".$v['md_hp']."</td>
				<td>".$arr_080deny_status[$v['md_status']]."</td>
				<td>".date("Y-m-d H:i:s" , strtotime($v['md_refusal_time']))."</td>
				<td>".$v['md_rdate']."</td>
				<td><div class='btn_line_up_center'>${_del}</div></td>
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

<SCRIPT>

	 function selectDelete() {
		 if($('.class_pcode').is(":checked")){
			 if(confirm("정말 삭제하시겠습니까?")){
				$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
				$("form[name=frm]").attr("action" , "/include/addons/080deny/_member_080deny.pro.php");
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택하시기 바랍니다.');
		 }
	 }

	// - 전체선택 / 해제
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.class_pcode').attr('checked',true);
			}
			else {
				$('.class_pcode').attr('checked',false);
			}
		});
	});

</SCRIPT>
