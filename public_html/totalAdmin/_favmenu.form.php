<?PHP
	include_once("inc.header.php");
?>


					<!-- 검색영역 -->
					<div class="form_box_area">
						<?=_DescStr("적은 수의 순번일수록 먼저 나오게 됩니다.")?>
						<?=_DescStr("같은 순번일 경우 먼저 등록한 메뉴가 먼저 나오게 됩니다.")?>
					</div>
					<!-- // 검색영역 -->

					<!-- 리스트영역 -->
					<div class="content_section_inner">

						<table class="list_TB" summary="리스트기본">
							<!-- <colgroup>
								<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
							</colgroup> -->
							<thead>
								<tr>
									<th scope="col" class="colorset">번호</th>
									<th scope="col" class="colorset">순번</th>
									<th scope="col" class="colorset">메뉴명</th>
									<th scope="col" class="colorset">메뉴링크</th>
									<th scope="col" class="colorset">기능</th>
								</tr>
							</thead> 
							<tbody>

<form name='frm_add' method=post action='_favmenu.pro.php'>
<input type=hidden name='_mode' value='add'>
			<tr>
				<td>추가</td>
				<td><input type='text' name='_menuIdx' class='input_text' style='width:30px' value='99' /></td>
				<td><input type='text' name='_menuName' class='input_text' style='width:200px' /></td>
				<td><input type='text' name='_menuLink' class='input_text' style='width:400px' /></td>
				<td>
					<div class='btn_line_up_center'>
						<span class='shop_btn_pack btn_input_blue'><input type='submit' name='_modify' class='input_small ' value='추가' /></span>
					</div>
				</td>
			</tr>
</form>

<?PHP
	// - 데이터가 있는 경우 처리 ---
	$que = "select * from odtFavmenu where fm_appId='".$row_admin[id]."' order by fm_menuIdx asc , fm_uid asc "; // 전체관리자 - 고정
	$res = _MQ_assoc($que);
	foreach($res as $k=>$v){
		echo "
<form name='frm_".$k."' method=post action='_favmenu.pro.php'>
<input type=hidden name='_mode' value='modify'>
<input type=hidden name='_uid' value='".$v[fm_uid]."'>
			<tr>
				<td>". ($k + 1) ."</td>
				<td><input type='text' name='_menuIdx' class='input_text' style='width:30px' value='".stripslashes($v[fm_menuIdx])."' /></td>
				<td><input type='text' name='_menuName' class='input_text' style='width:200px' value='".stripslashes($v[fm_menuName])."' /></td>
				<td><input type='text' name='_menuLink' class='input_text' style='width:400px' value='".stripslashes($v[fm_menuLink])."' /></td>
				<td>
					<div class='btn_line_up_center'>
						<span class='shop_btn_pack btn_input_white'><input type='submit' name='_modify' class='input_small' value='수정' /></span>
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						<span class='shop_btn_pack'><input type='button' name='_delete' class='input_small gray' value='삭제' onclick=\"javascript:del('_favmenu.pro.php?_mode=delete&_uid=".$v[fm_uid]."');\" /></span>
					</div>
				</td>
			</tr>
</form>
		";
	}
	// - 데이터가 있는 경우 처리 ---
?>

							</tbody> 
						</table>

					</div>
					<!-- // 리스트및폼 -->

<?PHP
	include_once("inc.footer.php");
?>