<?PHP

	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_coupon.list.php";

	include_once("inc.header.php");


	if($_mode == "modify") {
        $row = _MQ(" SELECT * FROM odtCoupon WHERE coNo='" . $coNo . "' ");
	}


	// 회원검색에 의한 쿠폰 지급 처리 - onedaynet jjc - 2011-01-19
	if( sizeof($memSerialnum) > 0 ) {
		$arr_mem_data = array();
		$result = _MQ_assoc("SELECT id FROM odtMember WHERE serialnum in ('".implode("','" , $memSerialnum)."') ");
		foreach($result as $k=>$v ){
			$arr_mem_data[] = $v[id];
		}
	}

?>

<form name=frm method=post action=_coupon.pro.php enctype='multipart/form-data' >
<input type=hidden name=_mode value='<?=($_mode ? $_mode : "add")?>'>
<input type=hidden name=coNo value='<?=$coNo?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">

					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">쿠폰명<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='coName' size=30 class='input_text' value="<?=$row[coName]?>"></td>
									</tr>
									<tr>
										<td class="article">쿠폰가격<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='coPrice' size=30 class='input_text' value="<?=$row[coPrice]?>"></td>
									</tr>
									<tr>
										<td class="article">쿠폰만료일<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='coLimit' size=15 class='input_text' value="<?=($row[coLimit] ? $row[coLimit] : date("Y-m-d" , strtotime("+" . $row_setup[paypoint_joindate] ." day")))?>"></td>
									</tr>
									<tr>
										<td class="article">지급유저<?=($_mode == "modify" ? "" : "<span class='ic_ess' title='필수'></span>")?></td>
										<td class="conts">
											<?if($_mode == "modify") {?>
												<?=$row[coID]?>
											<?} else {?>
												<span class="shop_btn_pack"><a href="#none" onclick="member_search()" class="small blue" title="유저검색" >유저검색</a></span>
												<textarea name="pointIDArray" class="input_text" style="width:100%;height:100px;" ><?echo ( sizeof($arr_mem_data) > 0 )? implode("," , $arr_mem_data)  :  ""  ;  ?></textarea><?=_DescStr("2명 이상일 경우에는 쉼표(,)로 구분하세요.")?>
											<?}?>
										</td>
									</tr>
								</tbody> 
							</table>
				
					</div>

					<?=_submitBTN("_coupon.list.php")?>

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
				coName: { required: true },// 쿠폰명
				coPrice: { required: true },// 쿠폰가격
				coLimit: { required: true }// 쿠폰쿠폰만료일
<?if( $_mode == "modify" ) {?>
<?}else {?>
				,pointIDArray: { required: true }// 지급유저
<?}?>
            },
            messages: {
				coName: { required: "쿠폰명을 입력하시기 바랍니다." },// 쿠폰명
				coPrice: { required: "쿠폰를 입력하시기 바랍니다." },// 쿠폰가격
				coLimit: { required: "쿠폰 적립일을 선택하시기 바랍니다." }// 쿠폰만료일
<?if( $_mode == "modify" ) {?>
<?}else {?>
				,pointIDArray: { required: "지급유저를 선택하시기 바랍니다." }// 지급유저
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
		$("input[name=coLimit]").datepicker({changeMonth: true,changeYear: true});
        $("input[name=coLimit]").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("input[name=coLimit]").datepicker( "option",$.datepicker.regional["ko"] );
    });

</script>

<?PHP
	include_once("inc.footer.php");
?>