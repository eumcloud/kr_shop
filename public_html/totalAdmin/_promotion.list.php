<?PHP
	// LMH005
	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_name !="" ) { $s_query .= " and pr_name like '%${pass_name}%' "; }
	if( $pass_code !="" ) { $s_query .= " and pr_code like '%${pass_code}%' "; }
	if( $pass_expire !="" ) { $s_query .= " and pr_expire_date='${pass_expire}' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtPromotionCode $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

?>


<form name=searchfrm method=post action='<?=$PHP_SELF?>'>
<input type=hidden name=mode value=search>
				<!-- 검색영역 -->
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120"/><col width="200"/><col width="120"/><col width="200"/><col width="120"/><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">프로모션코드명</td>
								<td class="conts"><input type="text" name="pass_name" class="input_text" value="<?=$pass_name?>"></td>
								<td class="article">프로모션코드</td>
								<td class="conts"><input type="text" name="pass_code" class="input_text" value="<?=$pass_code?>"></td>
								<td class="article">만료일</td>
								<td class="conts"><input type="text" name="pass_expire" readonly class="input_text" value="<?=$pass_expire?>"></td>
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
							<span class="shop_btn_pack"><a href="_promotion.form.php?_loc=<?=$_loc?>&_mode=add" class="medium red" title="프로모션코드 등록" >프로모션코드 등록</a></span>
						</div>
					</div>
					<?=_DescStr("고객은 주문 시 프로모션코드를 적용할 수 있으며, 만료일 내에 사용해야 합니다.")?>
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
						<span class="shop_btn_pack"><a href="javascript:select_send('delete');" class="small white" title="선택코드삭제" >선택코드삭제</a></span>
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
								<th scope="col" class="colorset">사용여부</th>
								<th scope="col" class="colorset">프로모션코드</th>
								<th scope="col" class="colorset">코드명</th>
								<th scope="col" class="colorset">할인금액</th>
								<th scope="col" class="colorset">만료일</th>
								<th scope="col" class="colorset">등록일</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP

	$res = _MQ_assoc(" select * from odtPromotionCode {$s_query} ORDER BY pr_uid desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small white' onclick='location.href=(\"_promotion.form.php?_mode=modify&pr_uid=$v[pr_uid]&_PVSC=${_PVSC}\");'></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_promotion.pro.php?_mode=delete&pr_uid=$v[pr_uid]&_PVSC=${_PVSC}\");'></span>";

		$_num = $TotalCount - $count - $k ;

		echo "
			<tr>
				<td><input type=checkbox name='chk_id[]' value='".$v[pr_uid]."' class=class_id></td>
				<td>". $_num ."</td>
				<td>".($v[pr_use]=="Y"?"사용":"<span style='color:red;'>미사용</span>")."</td>
				<td>" . $v[pr_code] . "</td>
				<td>". ($v[pr_name]?$v[pr_name]:"-") ."</td>
				<td>" . ($v[pr_type]=='P'?$v[pr_amount]."%":number_format($v[pr_amount])."원") . "</td>
				<td>".date('Y-m-d',strtotime($v[pr_expire_date]))."</td>
				<td>".date('Y-m-d',strtotime($v[pr_rdate]))."</td>
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


<script>
	// - 타입별 액션 적용 ---
	function type_action(_type , _mode){
		switch(_type){
			// 삭제
			case "delete":
				$("input[name=_mode]").val(_mode + "_delete");
				$("form[name=frm]").attr("action" , "_promotion.pro.php");
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
		$("input[name=pass_expire]").datepicker({changeMonth: true,changeYear: true});
        $("input[name=pass_expire]").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("input[name=pass_expire]").datepicker( "option",$.datepicker.regional["ko"] );
    });
</script>




<?PHP
	include_once("inc.footer.php");
?>


