<?PHP

	// 팝업형태 적용
	$app_mode = "popup";
	include_once("inc.header.php");

	if ( !$_COOKIE["auth_adminid"] ) {
		error_loc("/");
	}


	// 검색 체크
	$s_query = " where userType='B' and isRobot = 'N' and passwd!='deluser' ";
	if( $pass_id !="" ) { $s_query .= " and id like '%${pass_id}%' "; }
	if( $pass_name !="" ) { $s_query .= " and name like '%${pass_name}%' "; }
	if( $pass_address !="" ) { $s_query .= " and (address like '%${pass_address}%' or address1 like '%${pass_address}%' or address_doro like '%${pass_address}%')  "; }
	if( $pass_htel != "" ) { $s_query .= " AND concat_ws('',htel1,htel2,htel3) like '%".rm_str($pass_htel)."%' "; }
	if( $pass_tel != "" ) { $s_query .= " AND concat_ws('',tel1,tel2,tel3) like '%".rm_str($pass_tel)."%' "; }

?>


				<!-- 내부 서브타이틀 -->
				<div class="sub_title"><span class="icon"></span><span class="title">포인트 적용을 위한 회원 검색</span></div>
				<!-- // 내부 서브타이틀 -->

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
								<td class="article">&nbsp;</td>
								<td class="conts">&nbsp;</td>
							</tr>
						</tbody> 
					</table>

					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="검색풀기" >검색풀기</a></span>
<?}?>
						</div>
					</div>
				</div>
</form>
				<!-- // 검색영역 -->




<?PHP
	if($mode == "search") { 
?>
<form name=frm method=post >
<input type=hidden name=_mode value=''>

				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<!-- 리스트 제어버튼영역 //-->
					<div class="ctl_btn_area">
						<span class="shop_btn_pack"><a href="javascript:select_send('member');" class="small white" title="선택회원적용" >선택회원적용</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->

					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<th scope="col" class="colorset"><input type="checkbox" name="allchk"></th>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">아이디</th>
								<th scope="col" class="colorset">이름</th>
								<th scope="col" class="colorset">주소</th>
								<th scope="col" class="colorset">연락처</th>
								<th scope="col" class="colorset">포인트</th>
								<th scope="col" class="colorset">등록일</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP

	$res = _MQ_assoc(" select *, concat(tel1,'-',tel2,'-',tel3) as tel, concat(htel1,'-',htel2,'-',htel3) as htel from odtMember {$s_query} ORDER BY serialnum desc ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_num = $k + 1 ;

		echo "
			<tr>
				<td><input type=checkbox name='chk_id[]' value='".$v[id]."' class=class_id></td>
				<td>".${_num}."</td>
				<td>" . $v[id] . "</td>
				<td>" . $v[name] . "</td>
				<td style='text-align:left;'>" . trim($v[address] ." ". $v[address1] ) . ( $v[address_doro] ? "<br>( 도로명주소 : " . $v[address_doro].")" : "" ) . "</td>
				<td>" . $v[tel] . "<br>" . $v[htel] . "</td>
				<td style='text-align:right;'>" . number_format($v[point]) . "</td>
				<td>" . date("Y.m.d" , $v[signdate]) . "</td>
			</tr>
		";
	}

?>

						</tbody> 
					</table>
			</div>
</form>
<?PHP
	}
?>


<?PHP
	include_once("inc.footer.php");
?>





<script>
	// - 선택적용 ---
	 function select_send(_type) {
		 if($('.class_id').is(":checked")){
			var sList = "";
			$("input[name='chk_id[]']").each(function () {
				var sThisVal = (this.checked ? this.value : "" );
				sList += (sList=="" ? sThisVal : "," + sThisVal);
			});
			opener.document.frm.pointIDArray.value = sList;
			self.close();
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