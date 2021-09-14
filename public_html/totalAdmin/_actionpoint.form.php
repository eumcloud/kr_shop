<?PHP

	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_actionpoint.list.php";

	include_once("inc.header.php");


	if($_mode == "modify") {
        $row = _MQ(" SELECT * FROM odtActionLog WHERE acNo='" . $acNo . "' ");
	}


	// 회원검색에 의한 포인트 지급 처리 - onedaynet jjc - 2011-01-19
	if( sizeof($memSerialnum) > 0 ) {
		$arr_mem_data = array();
		$result = _MQ_assoc("SELECT id FROM odtMember WHERE serialnum in ('".implode("','" , $memSerialnum)."') ");
		foreach($result as $k=>$v ){
			$arr_mem_data[] = $v[id];
		}
	}

?>

<form name=frm method=post action=_actionpoint.pro.php enctype='multipart/form-data' >
<input type=hidden name=_mode value='<?=($_mode ? $_mode : "add")?>'>
<input type=hidden name=acNo value='<?=$acNo?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">

					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">제목<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='acTitle' size=30 class='input_text' value="<?=$row[acTitle]?>"></td>
									</tr>
									<tr>
										<td class="article">액션포인트<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='acPoint' size=30 class='input_text' value="<?=$row[acPoint]?>"><?=_DescStr("차감은 - 를 붙이세요.")?></td>
									</tr>
									<tr>
										<td class="article">지급유저<?=($_mode == "modify" ? "" : "<span class='ic_ess' title='필수'></span>")?></td>
										<td class="conts">
											<?if($_mode == "modify") {?>
												<?=$row[acID]?>
											<?} else {?>
												<span class="shop_btn_pack"><a href="#none" onclick="member_search()" class="small blue" title="유저검색" >유저검색</a></span>
												<textarea name="pointIDArray" class="input_text" style="width:100%;height:100px;" ><?echo ( sizeof($arr_mem_data) > 0 )? implode("," , $arr_mem_data)  :  ""  ;  ?></textarea><?=_DescStr("2명 이상일 경우에는 쉼표(,)로 구분하세요.")?>
											<?}?>
										</td>
									</tr>
								</tbody> 
							</table>
				
					</div>

					<?=_submitBTN("_actionpoint.list.php")?>

</form>


<SCRIPT LANGUAGE="JavaScript">
	function member_search() {
		window.open('_point.member_search.php','new','width=800,height=700,scrollbars=yes');
	}
</SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
    $(document).ready(function(){
		// -  validate --- 
        $("form[name=frm]").validate({
			ignore: "input[type=text]:hidden",
            rules: {
				acTitle: { required: true },// 제목
				acPoint: { required: true },// 엑션포인트
<?if( $_mode == "modify" ) {?>
<?}else {?>
				,acIDArray: { required: true }// 지급유저
<?}?>
            },
            messages: {
				acTitle: { required: "제목을 입력하시기 바랍니다." },// 제목
				acPoint: { required: "포인트를 입력하시기 바랍니다." },// 엑션포인트
<?if( $_mode == "modify" ) {?>
<?}else {?>
				acIDArray: { required: "지급유저를 선택하시기 바랍니다." }// 지급유저
<?}?>
            }
        });
		// - validate --- 
	});
</SCRIPT>


<?PHP
	include_once("inc.footer.php");
?>
