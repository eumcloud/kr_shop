<?PHP
	$app_mode = "popup";
	include_once("inc.header.php");

	if ( !$_COOKIE["auth_adminid"] ) {
		error_loc("/");
	}

	if(!$pass_code) {
		error_msgPopup_s("상품코드값이 넘어오지 않았습니다.");
	}

	// - 데이터가 있는 경우 처리 ---
	$que = "select * from odtProductReqInfo where pri_pcode='" . $pass_code . "' order by pri_uid asc  ";
	$res = _MQ_assoc($que);


	// 기본항목이 존재하지 않으면 추가한다. 
	if(sizeof($res) == 0 ){
		foreach($arr_reqinfo_keys as $req_k => $req_v) {
			$is_key = _MQ("select count(*) as cnt from odtProductReqInfo where pri_key = '".$req_v."' and pri_pcode='".$pass_code."'");
			if($is_key[cnt] < 1) _MQ_noreturn("insert into odtProductReqInfo set pri_key = '".$req_v."', pri_pcode='".$pass_code."'");
		}
		echo '<script>window.location.reload(true);</script>';
	}
?>


					<!-- 검색영역 -->
					<div class="form_box_area">
						<?=_DescStr("<B>정보제공고시 항목 관리</B>를 할 수 있는 페이지 입니다.")?>
						<?=_DescStr("상품에 필요한 정보제공고시를 항목: 내용으로 등록하며, 등록된 내용은 <B>상품 상세페이지에 노출</B>됩니다.")?>
						<?=_DescStr("<b style='color:red;'>새 항목을 추가하기 전 반드시 먼저 저장 해야합니다.</b>","orange")?>
					</div>
					<!-- // 검색영역 -->

					<!-- 리스트영역 -->
					<div class="content_section_inner">

<form name='frm_add' method=post action='_product_reqinfo.pro.php'>
<input type=hidden name='_mode' value='add'>
<input type=hidden name='pass_code' value='<?=$pass_code?>'>
		<table class="list_TB" summary="리스트기본">
			<colgroup>
				<col width="80"/><col width="*"/><col width="*"/><col width="80"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col" class="colorset">번호</th>
					<th scope="col" class="colorset">항목명</th>
					<th scope="col" class="colorset">항목내용</th>
					<th scope="col" class="colorset">기능</th>
				</tr>
			</thead> 
			<tbody>
			<tr>
				<td>추가</td>
				<td><input type='text' name='_key' class='input_text' style='width:200px' /></td>
				<td><input type='text' name='_value' class='input_text' style='width:400px' /></td>
				<td>
					<div class='btn_line_up_center'>
						<span class='shop_btn_pack btn_input_blue'><input type='submit' name='_modify' class='input_small ' value='추가' /></span>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
</form>

<form name='frm_' method=post action='_product_reqinfo.pro.php'>
<input type=hidden name='_mode' value='modify'>
<input type=hidden name='pass_code' value='<?=$pass_code?>'>
<table class="list_TB" summary="리스트기본" style="border-top: 0;">
	<colgroup>
		<col width="80"/><col width="*"/><col width="*"/><col width="80"/>
	</colgroup>
	<tbody>
<?PHP
	foreach($res as $k=>$v){
		echo "
			<tr>
				<td>". ($k + 1) ."</td>
				<td><input type='text' name='_key[".$v[pri_uid]."]' class='input_text' style='width:200px' value='".stripslashes($v[pri_key])."' /></td>
				<td><input type='text' name='_value[".$v[pri_uid]."]' class='input_text' style='width:400px' value='".stripslashes($v[pri_value])."' /></td>
				<td>
					<div class='btn_line_up_center'>
						<!--<span class='shop_btn_pack btn_input_white'><input type='submit' name='_modify' class='input_small' value='수정' /></span>
						<span class='shop_btn_pack'><span class='blank_3'></span></span>-->
						<span class='shop_btn_pack'><input type='button' name='_delete' class='input_small gray' value='삭제' onclick=\"javascript:del('_product_reqinfo.pro.php?_mode=delete&pass_code=". $pass_code ."&_uid=".$v[pri_uid]."');return false;\" /></span>
					</div>
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
			<input type="submit" name="" class="input_large red" value="저장하기">
		</span>
	</div>
</div>
<!-- 버튼영역 -->
</form>


					</div>
					<!-- // 리스트및폼 -->

<?PHP
	include_once("inc.footer.php");
?>