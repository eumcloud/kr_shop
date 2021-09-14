<?PHP

	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_pointID !="" ) { $s_query .= " and pointID like '%${pass_pointID}%' "; }
	if( $pass_pointTitle !="" ) { $s_query .= " and pointTitle like '%${pass_pointTitle}%' "; }
	if( $pass_redRegidate !="" ) { $s_query .= " and redRegidate='${pass_redRegidate}' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtPointLog $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

?>


<form name=searchfrm method=post action='<?=$PHP_SELF?>'>
<input type=hidden name=mode value=search>
				<!-- 검색영역 -->
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<tbody> 
							<tr>
								<td class="article">아이디</td>
								<td class="conts"><input type=text name=pass_pointID class=input_text value="<?=$pass_pointID?>"></td>
								<td class="article">제목</td>
								<td class="conts"><input type=text name=pass_pointTitle class=input_text value="<?=$pass_pointTitle?>"></td>
								<td class="article">지급일</td>
								<td class="conts"><input type=text name=pass_redRegidate class=input_text value="<?=$pass_redRegidate?>"></td>
							</tr>
						</tbody> 
					</table>

					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="전체목록" >전체목록</a></span>
<?}?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_point.form.php?_loc=<?=$_loc?>&_mode=add" class="medium red" title="포인트등록" >포인트등록</a></span>
						</div>
					</div>
					<?=_DescStr("포인트 삭제 시 <B>처리완료된 포인트</B>일 경우 회원포인트도 함께 감소됩니다.")?>
				</div>
</form>
				<!-- // 검색영역 -->



<form name=frm method=post >
<input type=hidden name=_mode value=''>
<input type=hidden name=_seachcnt value='<?=$TotalCount?>'>
<input type=hidden name=_PVSC value="<?=$_PVSC?>">
<input type=hidden name=_search_que value="<?=enc('e',$s_query)?>">


				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<!-- 리스트 제어버튼영역 //-->
					<div class="ctl_btn_area">
						<span class="shop_btn_pack"><a href="javascript:select_send('delete');" class="small white" title="선택포인트삭제" >선택포인트삭제</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->

					<table class="list_TB" summary="리스트기본">
						<!-- <colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
						</colgroup> -->
						<thead>
							<tr>
								<th scope="col" class="colorset"><input type="checkbox" name="allchk"></th>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">아이디</th>
								<th scope="col" class="colorset">제목</th>
								<th scope="col" class="colorset">지급포인트</th>
								<th scope="col" class="colorset">사용포인트</th>
								<th scope="col" class="colorset">상태</th>
								<th scope="col" class="colorset">지급예정일</th>
								<th scope="col" class="colorset">등록일</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP

	$res = _MQ_assoc(" select * from odtPointLog {$s_query} ORDER BY pointNo desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small white' onclick='location.href=(\"_point.form.php?_mode=modify&pointNo=$v[pointNo]&_PVSC=${_PVSC}\");'></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_point.pro.php?_mode=delete&pointNo=$v[pointNo]&_PVSC=${_PVSC}\");'></span>";

		$_num = $TotalCount - $count - $k ;

		echo "
			<tr>
				<td><input type=checkbox name='chk_id[]' value='".$v[pointNo]."' class=class_id></td>
				<td>".${_num}."</td>
				<td>" . $v[pointID] . "</td>
				<td style='text-align:left;'>" . $v[pointTitle] . "</td>
				<td style='text-align:right;'>".($v[pointPoint] > 0 ? number_format($v[pointPoint]) : NULL)."</td>
				<td style='text-align:right;'>".($v[pointPoint] < 0 ? number_format($v[pointPoint]) : NULL)."</td>
				<td>" . ($v[pointStatus] == "Y" ? "<span class='shop_state_pack'><span class='orange'>처리완료</span></span>" : "<span class='shop_state_pack'><span class='gray'>지급예정</span></span>") . "</td>
				<td>" . $v[redRegidate] . "</td>
				<td>" . substr($v[pointRegidate],0,10) . "</td>
				<td>
					<div class='btn_line_up_center'>
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						".$_mod."
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						".$_del."
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
						<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=")?>
					</div>
					<!-- // 페이지네이트 -->

			</div>
</form>



<?PHP
	include_once("inc.footer.php");
?>






<script>
	// - 타입별 액션 적용 ---
	function type_action(_type , _mode){
		switch(_type){
			// 삭제
			case "delete":
				$("input[name=_mode]").val(_mode + "_delete");
				$("form[name=frm]").attr("action" , "_point.pro.php");
				break;
		}
	}
	// - 타입별 액션 적용 ---
	// - 선택적용 ---
	 function select_send(_type) {
		 if($('.class_id').is(":checked")){
			type_action(_type , "select");
			 document.frm.submit();
		 }
		 else {
			 alert('1명 이상 선택하시기 바랍니다.');
		 }
	 }
	// - 선택적용 ---
	// - 전체선택 / 해제
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.class_id').attr('checked',true);
			}
			else {
				$('.class_id').attr('checked',false);
			}
		});
	});
</script>
<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
		$("input[name=pass_redRegidate]").datepicker({changeMonth: true,changeYear: true});
        $("input[name=pass_redRegidate]").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("input[name=pass_redRegidate]").datepicker( "option",$.datepicker.regional["ko"] );
    });
</script>
