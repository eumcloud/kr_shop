<?PHP

	//JJC002

	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where userType='B' and isRobot = 'N' ";
	if( $pass_id !="" ) { $s_query .= " and id like '%${pass_id}%' "; }
	if( $pass_name !="" ) { $s_query .= " and name like '%${pass_name}%' "; }
	if( $pass_address !="" ) { $s_query .= " and (address like '%${pass_address}%' or address1 like '%${pass_address}%' or address_doro like '%${pass_address}%')  "; }
	if( $pass_htel != "" ) { $s_query .= " AND concat_ws('',htel1,htel2,htel3) like '%".rm_str($pass_htel)."%' "; }
	if( $pass_tel != "" ) { $s_query .= " AND concat_ws('',tel1,tel2,tel3) like '%".rm_str($pass_tel)."%' "; }
	if( $pass_sendchk !="" ) { $s_query .= " and ms_sendchk = '" . $pass_sendchk . "' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtMemberSleep $s_query ");
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
								<td class="conts"><input type=text name=pass_id class=input_text value="<?=$pass_id?>"></td>
								<td class="article">이름</td>
								<td class="conts"><input type=text name=pass_name class=input_text value="<?=$pass_name?>"></td>
								<td class="article">주소</td>
								<td class="conts"><input type=text name=pass_address class=input_text value="<?=$pass_address?>"></td>
							</tr>
							<tr>
								<td class="article">일반전화</td>
								<td class="conts"><input type=text name=pass_tel class=input_text value="<?=$pass_tel?>"></td>
								<td class="article">휴대전화</td>
								<td class="conts"><input type=text name=pass_htel class=input_text value="<?=$pass_htel?>"></td>
								<td class="article">메일발송</td>
								<td class="conts"><?=_InputSelect( "pass_sendchk" , array('Y','N') , $pass_sendchk , "" , array('발송','미발송') , "-선택-")?></td>
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
						</div>
					</div>
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
						<span class="shop_btn_pack"><a href="javascript:select_send('mail');" class="small gray" title="선택휴면메일발송" >선택휴면메일발송</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:search_send('mail');" class="small gray" title="검색휴면메일발송" >검색휴면메일발송 (<?=number_format($TotalCount)?>)</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:select_send('excel');" class="small white" title="선택엑셀저장" >선택엑셀저장</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:search_send('excel');" class="small white" title="검색엑셀저장" >검색엑셀저장 (<?=number_format($TotalCount)?>)</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:select_send('delete');" class="small white" title="선택회원삭제" >선택회원삭제</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:select_send('return');" class="small white" title="선택회원휴면풀기" >선택회원휴면풀기</a></span>
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
								<th scope="col" class="colorset">이름</th>
								<th scope="col" class="colorset">주소</th>
								<th scope="col" class="colorset">연락처</th>
								<th scope="col" class="colorset">등록일</th>
								<th scope="col" class="colorset">휴면전환일</th>
								<th scope="col" class="colorset">휴면메일<br>발송여부</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP

	$res = _MQ_assoc(" select *, concat(tel1,'-',tel2,'-',tel3) as tel, concat(htel1,'-',htel2,'-',htel3) as htel from odtMemberSleep {$s_query} ORDER BY serialnum desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_return = "<span class='shop_btn_pack'><input type=button value='휴면풀기' class='input_small blue'  onclick=\"if(confirm('휴면상태를 풀어 회원으로 전환하시겠습니까?')){location.href=('_membersleep.pro.php?_mode=return&serialnum=" . $v[serialnum] . "&_PVSC=" . $_PVSC . "');}\"></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_membersleep.pro.php?_mode=delete&serialnum=" . $v[serialnum] . "&_PVSC=" . $_PVSC . "\");'></span>";

		$_num = $TotalCount - $count - $k ;

		echo "
			<tr>
				<td><input type=checkbox name='chk_id[]' value='".$v[id]."' class=class_id></td>
				<td>".${_num}."</td>
				<td>" . $v[id] . "</td>
				<td>" . $v[name] . "</td>
				<td style='text-align:left;'>" . trim($v[address] ." ". $v[address1] ) . ( $v[address_doro] ? "<br>( 도로명주소 : " . $v[address_doro].")" : "" ) . "</td>
				<td>" . implode("<br/>",array(phone_print($v[tel1],$v[tel2],$v[tel3]),phone_print($v[htel1],$v[htel2],$v[htel3]))) . "</td>
				<td>" . date("Y.m.d" , $v[signdate]) . "</td>
				<td>" . substr($v['ms_rdate'],0,10) . "</td>
				<td>" . ($v['ms_sendchk'] == "Y" ? $arr_adm_button['발송'] : $arr_adm_button['미발송']) . "</td>
				<td>
					<div class='btn_line_up_center'>
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						".$_return."
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
			// 엑셀저장
			case "excel":
				$("input[name=_mode]").val(_mode + "_excel");
				$("form[name=frm]").attr("action" , "_membersleep.pro.php");
				break;
			// 삭제
			case "delete":
				$("input[name=_mode]").val(_mode + "_delete");
				$("form[name=frm]").attr("action" , "_membersleep.pro.php");
				break;
			// 삭제
			case "return":
				$("input[name=_mode]").val(_mode + "_return");
				$("form[name=frm]").attr("action" , "_membersleep.pro.php");
				break;
			// 메일 발송
			case "mail":
				$("input[name=_mode]").val(_mode + "_mail");
				$("form[name=frm]").attr("action" , "_membersleep_mail.form.php");
				break;
		}
	}
	// - 타입별 액션 적용 ---

	// - 선택적용 ---
	 function select_send(_type) {
		 if($('.class_id').is(":checked")){
			type_action(_type , "select");
			if( _type == "delete" ) {
				if(confirm('정말 삭제하시겠습니까?')){
					document.frm.submit();
				}
			}
			else {
				document.frm.submit();
			}
		 }
		 else {
			 alert('1명 이상 선택하시기 바랍니다.');
		 }
	 }
	// - 선택적용 ---
	// - 검색적용 ---
	 function search_send(_type) {
		 if($('input[name=_seachcnt]').val()*1 > 0 ){
			type_action(_type , "search");
			if( _type == "delete" ) {
				if(confirm('정말 삭제하시겠습니까?')){
					document.frm.submit();
				}
			}
			else {
				document.frm.submit();
			}
		 }
		 else {
			 alert('1명 이상 검색하시기 바랍니다.');
		 }
	 }
	// - 검색적용 ---



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