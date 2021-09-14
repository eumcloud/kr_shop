<?PHP

	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_point.list.php";

	include_once("inc.header.php");


	if($_mode == "modify") {
        $row = _MQ(" SELECT * FROM odtPointLog WHERE pointNo='" . $pointNo . "' ");
	}


	// 유저검색에 의한 포인트 지급 처리 - onedaynet jjc - 2011-01-19
	if( sizeof($memSerialnum) > 0 ) {
		$arr_mem_data = array();
		$result = _MQ_assoc("SELECT id FROM odtMember WHERE serialnum in ('".implode("','" , $memSerialnum)."') ");
		foreach($result as $k=>$v ){
			$arr_mem_data[] = $v[id];
		}
	}

?>

<form name=frm method=post action=_point.pro.php enctype='multipart/form-data' >
<input type=hidden name=_mode value='<?=($_mode ? $_mode : "add")?>'>
<input type=hidden name=pointNo value='<?=$pointNo?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">

					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">제목<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='pointTitle' size=30 class='input_text' value="<?=$row[pointTitle]?>"></td>
									</tr>
									<tr>
										<td class="article">포인트<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='pointPoint' size=30 class='input_text' value="<?=$row[pointPoint]?>"><?=_DescStr("차감은 - 를 붙이세요")?></td>
									</tr>
									<tr>
										<td class="article">포인트 적립일<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='redRegidate' size=15 class='input_text' value="<?=($row[redRegidate] ? $row[redRegidate] : date("Y-m-d" , strtotime("+" . $row_setup[paypoint_joindate] ." day")))?>"></td>
									</tr>
									<tr>
										<td class="article">지급유저<?=($_mode == "modify" ? "" : "<span class='ic_ess' title='필수'></span>")?></td>
										<td class="conts">
											<?if($_mode == "modify") {?>
												<?=$row[pointID]?>
											<?} else {?>
												<span class="shop_btn_pack"><a href="#none" onclick="member_search()" class="small blue" title="유저검색" >유저검색</a></span>
												<textarea name="pointIDArray" ID="pointIDArray" class="input_text" style="width:100%;height:100px;" ><?echo ( sizeof($arr_mem_data) > 0 )? implode("," , $arr_mem_data)  :  ""  ;  ?></textarea><?=_DescStr("2명 이상일 경우에는 쉼표(,)로 구분하세요.")?>
											<?}?>
										</td>
									</tr>
								</tbody> 
							</table>
				
					</div>

					<?=_submitBTN("_point.list.php")?>

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
				pointTitle: { required: true },// 제목
				pointPoint: { required: true },// 포인트
				redRegidate: { required: true }// 포인트 적립일
<?if( $_mode == "modify" ) {?>
<?}else {?>
				,pointIDArray: { required: true }// 지급유저
<?}?>
            },
            messages: {
				pointTitle: { required: "제목을 입력하시기 바랍니다." },// 제목
				pointPoint: { required: "포인트를 입력하시기 바랍니다." },// 포인트
				redRegidate: { required: "포인트 적립일을 선택하시기 바랍니다." }// 포인트 적립일
<?if( $_mode == "modify" ) {?>
<?}else {?>
				pointIDArray: { required: "지급유저를 선택하시기 바랍니다." }// 지급유저
<?}?>
            }
        });
		// - validate --- 
	});
</SCRIPT>


<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
		$("input[name=redRegidate]").datepicker({changeMonth: true,changeYear: true});
        $("input[name=redRegidate]").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("input[name=redRegidate]").datepicker( "option",$.datepicker.regional["ko"] );
    });

</script>

<?PHP

	// - 회원선택으로부터 넘어왔을 경우 체크 ---
	if($_mode) {
		switch($_mode){
			// --- 선택회원 ---
			case "select":
				$app_id = implode("," , array_values($chk_id));
				break; 
			// --- 선택회원 ---

			// --- 검색회원 ---
			case "search":
				$chk_id = array();
				$sres = _MQ_assoc(" select id from odtMember " . enc('d' ,  $_search_que ) . " ORDER BY serialnum desc ");
				foreach($sres as $sk=>$sv){
					$chk_id[$sk] = $sv[id];
				}
				$app_id = implode("," , array_values($chk_id));
				break;
			// --- 검색회원 ---
		}
?>
<SCRIPT>
    $(document).ready(function(){
		$("textarea#pointIDArray").val("<?=$app_id?>");
	});
</SCRIPT>
<?PHP
	}
	// - 회원선택으로부터 넘어왔을 경우 체크 ---
	include_once("inc.footer.php");

?>