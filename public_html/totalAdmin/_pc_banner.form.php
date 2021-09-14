<?PHP
// 메뉴 지정 변수
$app_current_link = "/totalAdmin/_pc_banner.list.php";

include_once("inc.header.php");

// 상품카테고리별 카테고리 종류
$arr_banner_loc = _product_category_banner();

$app_cpname = "";
if( $_mode == "modify" ) {
	$que = "  select * from odtBanner where b_uid='". $_uid ."' ";
	$r = _MQ($que);
}
else {
	$sr = _MQ(" select ifnull(max(b_idx),0) as max_idx from odtBanner ");
	$r['b_idx'] = $sr['max_idx'] + 10;
}

if(!$r['b_target']) {
	$r['b_target'] = "_self";
}
?>


<form name="frm" method="post" action="_pc_banner.pro.php" enctype="multipart/form-data" autocomplete="off" >
	<input type="hidden" name="_mode" value="<?=$_mode?>">
	<input type="hidden" name="_uid" value="<?=$_uid?>">
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">

	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
			</colgroup>
			<tbody>
			<tr>
				<td class="article">배너위치</td>
				<td class="conts"><?=_InputSelect( "_loc" , array_keys($arr_banner_loc), $r['b_loc'], " hname='배너위치' required ", array_values($arr_banner_loc), "-위치선택-")?></td>
			</tr>
			<tr>
				<td class="article">배너이미지</td>
				<td class="conts"><?=_PhotoForm( "..".IMG_DIR_BANNER, "_img", $r['b_img'])?></td>
			</tr>
			<tr>
				<td class="article">배너 링크 형식</td>
				<td class="conts"><?=_InputRadio( "_target", array('_self','_blank'), $r['b_target'], "", array('같은창','새창'), "")?></td>
			</tr>
			<tr>
				<td class="article">배너 링크 정보</td>
				<td class="conts">
					<input type="text" name="_link" value="<?=$r['b_link']?>" size="80" maxlength="150" class="input_text">
					<span class="shop_btn_pack" style="float:none;"><a onClick="productWin();" class='small blue'>상품연결</a></span>
				</td>
			</tr>
			<tr>
				<td class="article">배너 타이틀</td>
				<td class="conts">
					<input type=text name="_title" value="<?=$r[b_title]?>" size="80" maxlength="150" class="input_text">
					<?=_DescStr("배너 타이틀은 배너이미지에 마우스를 올렸을 경우 배너의 내용을 설명해주는 것으로 웹표준화의 주요 방침 입니다.<br>&nbsp;또한 팝업창의 경우 팝업창 타이틀에 적용됩니다.")?>
				</td>
			</tr>
			<tr>
				<td class="article">배너 노출</td>
				<td class="conts"><?=_InputRadio("_view", array('Y','N'), ($r['b_view']?$r['b_view']:"Y"), "", array('노출','숨김'), "")?></td>
			</tr>
			<tr>
				<td class="article">노출순위</td>
				<td class="conts"><input type="text" name="_idx" class="input_text" style="width:20px" value="<?=$r['b_idx']?>"  hname="노출순위"/>순위&nbsp;&nbsp;<?=_DescStr("낮은 순위가 먼저 나오며, 순위가 같을 경우 먼저 최근 등록한 순으로 나옵니다.")?></td>
			</tr>
			<tr>
				<td class="article">게재기간</td>
				<td class="conts">
					시작일 : <input type="text" name="_sdate" ID="_sdate" value="<?=$r['b_sdate']?$r['b_sdate']:date('Y-m-d')?>" class="input_text" size="11" readonly style="cursor:pointer;"/> ~
					종료일 <input type="text" name="_edate" ID="_edate" value="<?=$r['b_edate']?$r['b_edate']:date('Y-m-d',strtotime('+365 day'))?>" class="input_text" size="11" readonly style="cursor:pointer;"/>
				</td>
			</tr>

			</tbody> 
		</table>

	</div>

	<?=_submitBTN("_pc_banner.list.php")?>

</form>



<link rel="stylesheet" href="/include/js/jquery/jqueryui/jquery-ui.min.css" type="text/css">
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script language='javascript' src='../include/js/lib.validate.js'></script>
<script>
	$(function() {
		$("#_sdate").datepicker({
			changeMonth: true,
			changeYear: true
		});
		$("#_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
		$("#_sdate").datepicker( "option",$.datepicker.regional["ko"] );

		$("#_edate").datepicker({
			changeMonth: true,
			changeYear: true
		});
		$("#_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
		$("#_edate").datepicker( "option",$.datepicker.regional["ko"] );
	});
	function productWin() {
		window.open('_banner.product_link.pop.php?relation_procode=<? $relation_prop_code = str_replace("/?pn=product.view&pcode=","",$r[b_link]); echo $relation_prop_code; ?>','relation', 'width=1200, height=800, scrollbars=yes');
	}
	function click_link(field) {
		if($("input[name="+field+"]").val().length == 0 ) {
			alert("url을 입력하세요");
		}
		else {
			// 미리 /include/js/default.js 불러와야 함
			window.open($("input[name="+field+"]").val(), 'click_link', ''); 
		}
	}
</script>
<?PHP include_once("inc.footer.php"); ?>