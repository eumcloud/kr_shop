<?PHP

	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_coID !="" ) { $s_query .= " and coID like '%${pass_coID}%' "; }
	if( $pass_coName !="" ) { $s_query .= " and coName like '%${pass_coName}%' "; }
	if( $pass_coLimit !="" ) { $s_query .= " and coLimit='${pass_coLimit}' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtCoupon $s_query ");
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
								<td class="conts"><input type=text name=pass_coID class=input_text value="<?=$pass_coID?>"></td>
								<td class="article">쿠폰명</td>
								<td class="conts"><input type=text name=pass_coName class=input_text value="<?=$pass_coName?>"></td>
								<td class="article">만료일</td>
								<td class="conts"><input type=text name=pass_coLimit class=input_text value="<?=$pass_coLimit?>"></td>
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
							<span class="shop_btn_pack"><a href="_coupon.form.php?_loc=<?=$_loc?>&_mode=add" class="medium red" title="쿠폰등록" >쿠폰등록</a></span>
						</div>
					</div>
					<?=_DescStr("회원을 지정하여 쿠폰을 발급할 수 있으며, 발급된 쿠폰은 이벤트 쿠폰으로서 상품 구매 시 사용이 가능합니다.")?>
					<?=_DescStr("회원은 발급받은 쿠폰을 MY페이지에서 확인할 수 있으며, 만료일 내에 사용하여야 합니다.")?>
					<?=_DescStr("쿠폰이 발급되면 초기 1회에 한하여 회원이 로그인했을때 알림창으로 쿠폰발급을 알려주오니, 따로 통보할 필요가 없습니다.")?>

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
						<span class="shop_btn_pack"><a href="javascript:select_send('delete');" class="small white" title="선택쿠폰삭제" >선택쿠폰삭제</a></span>
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
								<th scope="col" class="colorset">쿠폰명</th>
								<th scope="col" class="colorset">쿠폰가격</th>
								<th scope="col" class="colorset">쿠폰만료일</th>
								<th scope="col" class="colorset">사용여부</th>
								<th scope="col" class="colorset">발급일</th>
								<th scope="col" class="colorset">사용일</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP

	$res = _MQ_assoc(" select * from odtCoupon {$s_query} ORDER BY coNo desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small white' onclick='location.href=(\"_coupon.form.php?_mode=modify&coNo=$v[coNo]&_PVSC=${_PVSC}\");'></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_coupon.pro.php?_mode=delete&coNo=$v[coNo]&_PVSC=${_PVSC}\");'></span>";

		$_num = $TotalCount - $count - $k ;

		echo "
			<tr>
				<td><input type=checkbox name='chk_id[]' value='".$v[coNo]."' class=class_id></td>
				<td>". $_num ."</td>
				<td>" . $v[coID] . "</td>
				<td style='text-align:left;'>" . $v[coName] . "</td>
				<td style='text-align:right;'>". number_format($v[coPrice]) ."원</td>
				<td>" . $v[coLimit] . "</td>
				<td>" . ($v[coUse] == "Y" ? "<span class='shop_state_pack'><span class='orange'>사용</span></span>" : ($v[coUse] == "E" ? "<span class='shop_state_pack'><span class='gray'>만료</span></span>" : "<span class='shop_state_pack'><span class='lightgray'>미사용</span></span>")) . "</td>
				<td>" . substr($v[coRegidate],0,10) . "</td>
				<td>" . ( $v[coUsedate] ? substr($v[coUsedate],0,10) : "-") . "</td>
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
				$("form[name=frm]").attr("action" , "_coupon.pro.php");
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
		$("input[name=pass_coLimit]").datepicker({changeMonth: true,changeYear: true});
        $("input[name=pass_coLimit]").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("input[name=pass_coLimit]").datepicker( "option",$.datepicker.regional["ko"] );
    });
</script>
