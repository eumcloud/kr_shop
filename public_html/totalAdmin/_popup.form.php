<?PHP
// 페이지 표시
$app_current_link = "/totalAdmin/_popup.list.php";

include_once("inc.header.php");

if( $_mode == "modify" ) {
	$que = " select * from odtPopup where p_uid='". $_uid ."' ";
	$r = _MQ($que);
}
else {
	$sr = _MQ(" select ifnull(max(p_idx),0) as max_idx from odtPopup ");
	$r['p_idx'] = $sr['max_idx'] + 10;
}
?>

<form name="frm" method="post" action="_popup.pro.php" enctype="multipart/form-data">
	<input type="hidden" name="_mode" value="<?=$_mode?>">
	<input type="hidden" name="_uid" value="<?=$_uid?>">
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">

	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/>
				<col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">이미지</td>
					<td class="conts"><?=_PhotoForm("..".IMG_DIR_BANNER, "_img", $r['p_img'])?></td>
				</tr>

				<tr>
					<td class="article">링크 형식</td>
					<td class="conts"><?=_InputRadio("_target", array('_self','_blank'), $r['p_target'], "", array('같은창','새창'), "")?></td>
				</tr>
				<tr>
					<td class="article">링크 정보(url)</td>
					<td class="conts">
						<input type="text" name="_link" value="<?=$r["p_link"]?>" class="input_text" style="width:300px">
						<span class="shop_btn_pack" style="float:none;"><a onClick="productWin();" class='small blue'>상품연결</a></span>
						<br><?=_DescStr(" 팝업클릭시 바로갈 수 있는 링크주소를 입력하시기 바랍니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">타이틀</td>
					<td class="conts">
						<input type="text" name="_title" value="<?=$r["p_title"]?>" style="width:200px" class="input_text" hname="타이틀" >
						<br><?=_DescStr(" 타이틀은 이미지에 마우스를 올렸을 경우 의 내용을 설명해주는 것으로 웹표준화의 주요 방침 입니다.<br>&nbsp;&nbsp;또한 팝업창의 경우 팝업창 타이틀에 적용됩니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">노출</td>
					<td class="conts">
						<?=_InputRadio("_view", array('Y','N'), ($r['p_view']?$r['p_view']:"Y"), "", array('노출','숨김'), "")?>
					</td>
				</tr>
				<tr>
					<td class="article">노출순위</td>
					<td class="conts"><input type="text" name="_idx" class="input_text" style="width:20px" value="<?=$r['p_idx']?>"  hname='노출순위'/>순위&nbsp;&nbsp;<?=_DescStr("낮은 순위가 먼저 나오며, 순위가 같을 경우 먼저 최근 등록한 순으로 나옵니다.")?></td>
				</tr>
				<tr>
					<td class="article">노출위치<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						왼쪽으로부터 <input type="text" name="_left" class="input_text" style="width:50px" value="<?=$r['p_left']?>" />px
						위쪽으로부터 <input type="text" name="_top" class="input_text" style="width:50px" value="<?=$r['p_top']?>" />px
						<?=_DescStr("노출위치는 브라우저(창) 전체가 아니라 해당 사이트 전체 영역 기준으로부터의 위치를 말합니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">노출기간</td>
					<td class="conts">
						시작일 : <input type="text" name="_sdate" class="input_text" size="11"  ID="_sdate" value="<?=$r['p_sdate']?>" readonly style="cursor:pointer;"/>
						~
						종료일 <input type="text" name="_edate" class="input_text" size="11" ID="_edate" value="<?=$r['p_edate']?>" readonly style="cursor:pointer;"/>
					</td>
				</tr>
			</tbody> 
		</table>
	</div>
	<?=_submitBTN("_popup.list.php")?>
</form>


<link rel="stylesheet" href="/include/js/jquery/jqueryui/jquery-ui.min.css" type="text/css">
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
        $("#_sdate").datepicker({changeMonth: true,changeYear: true});
        $("#_sdate").datepicker("option", "dateFormat", "yy-mm-dd");
        $("#_sdate").datepicker("option", $.datepicker.regional["ko"]);

        $("#_edate").datepicker({changeMonth: true,changeYear: true});
        $("#_edate").datepicker("option", "dateFormat", "yy-mm-dd");
        $("#_edate").datepicker("option", $.datepicker.regional["ko"]);
    });
    function productWin() {
        window.open('_banner.product_link.pop.php?relation_procode=<?php $relation_prop_code = str_replace("/?pn=product.view&pcode=", "", $r[p_link]); echo $relation_prop_code; ?>', 'relation', 'width=1200, height=800, scrollbars=yes');
    }
</script>

<?PHP include_once("inc.footer.php"); ?>