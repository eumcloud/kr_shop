<?PHP
include_once("inc.header.php");

$arr_master = array();
$app_first_master = "";
$res = _MQ_assoc(" select * from odtAdmin ORDER BY serialnum asc ");
foreach( $res as $k=>$v ){
	$arr_master[$v['id']] = $v['name'] . " - " . $v['id'];
	$app_first_master = $app_first_master ? $app_first_master : $v['id'];
}
$pass_master = $pass_master ? $pass_master : $app_first_master; // 선택하지 않을 경우 첫번째 관리자 선택


// 메뉴정보 불러오기
$arr_memu = array();
$res = _MQ_assoc(" SELECT * FROM m_menu_set WHERE m15_id = '" . $pass_master . "' ");
foreach( $res as $k=>$v ){
	$arr_memu[$v['m15_code1']][$v['m15_code2']] = $v['m15_vkbn'];
}
?>

<form name="searchfrm" method="post" action="<?=$_SERVER["PHP_SELF"]?>">
	<input type="hidden" name="mode" value="search">
	<!-- 검색영역 -->
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="120px"/>
				<col width="*"/>
			</colgroup>
			<tbody> 
				<tr>
					<td class="article">관리자선택</td>
					<td class="conts"><?=_InputSelect("pass_master", array_keys($arr_master), $pass_master, "", array_values($arr_master), "-선택-")?></td>
				</tr>
			</tbody> 
		</table>
		
		<!-- 버튼영역 -->
		<div class="top_btn_area">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
			</div>
		</div>
	</div>
	<!-- // 검색영역 -->
</form>




<!-- 리스트영역 -->
<div class="content_section_inner">
	<form name="frm" method="post" action="_totadm_menuset.pro.php" target="common_frame" enctype="multipart/form-data">
		<input type="hidden" name="m2_id" value="<?=$pass_master?>">

		<!-- 버튼영역 -->
		<div class="bottom_btn_area" style="margin-bottom: 10px;">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack">
					<input type="submit" name="" class="input_large red" value="저장하기">
				</span>
			</div>
		</div>
		<!-- 버튼영역 -->

		<table class="list_TB" summary="리스트기본">
			<colgroup>
				<col width="100px"/><col width="300px"/><col width="*"/><col width="200px"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col" class="colorset">번호</th>
					<th scope="col" class="colorset">대메뉴</th>
					<th scope="col" class="colorset">소메뉴</th>
					<th scope="col" class="colorset">상태
					<div style="margin:5px 0;">
						<label>전체노출<input type="radio" name="allchk" class="allchk allchk_y" value="Y"></label>
						<label>전체숨김<input type="radio" name="allchk" class="allchk allchk_n" value="N"></label>
					</div>					
					</th>
				</tr>
			</thead> 
			<tbody>
				<?PHP
				// - 데이터가 있는 경우 처리 ---
				$que = "SELECT * FROM m_adm_menu where m2_vkbn='y' ORDER BY m2_code1 , m2_code2 ";
				$res = _MQ_assoc($que);
				foreach($res as $k=>$v){

					echo "
						<tr>
							<td>". ($k + 1) ."</td>
							<td class='left'>". $v['m2_name1'] ."</td>
							<td class='left'>". $v['m2_name2'] ."</td>
							<td>" . _InputRadio("_status[".$k."]", array("Y" , "N"), $arr_memu[$v['m2_code1']][$v['m2_code2']], " class='readio_chk' ", array("노출" , "숨김")) . "
								<input type=hidden name='m2_seq[]' value='".$k."'>
								<input type=hidden name='m2_code1[".$k."]' value='".$v['m2_code1']."'>
								<input type=hidden name='m2_code2[".$k."]' value='".$v['m2_code2']."'>
							</td>
						</tr>
					";
				}
				// - 데이터가 있는 경우 처리 ---
				?>

			</tbody> 
		</table>

		<!-- 버튼영역 -->
		<div class="bottom_btn_area" style="margin-top: 10px;">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack">
					<input type="submit" class="input_large red" value="저장하기">
				</span>
			</div>
		</div>
		<!-- 버튼영역 -->
	</form>
</div>
<!-- // 리스트및폼 -->

<script>
$('.allchk').click(function(){
	if($(this).val() == 'Y'){ // 전체노출이라면
		$('input.readio_chk[value="Y"]').prop('checked',true);
	}else{
		$('input.readio_chk[value="N"]').prop('checked',true);
	}
})
</script>

<?PHP include_once("inc.footer.php"); ?>