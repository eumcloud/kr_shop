<?PHP

	// 팝업형태 적용
	$app_mode = "popup";

	include_once("inc.header.php");

	if( $_uid ) {
		$que = "  select * from odtDeliveryAddpriceNew where da_uid='". $_uid ."' ";
		$r = _MQ($que);
		//ViewArr($r);
	}else{
		error_msgPopup_s("잘못된 접근입니다.");
	}
?>

<div style="padding:20px;">
	<div class="form_box_area" style="margin:0!important;">
		<form name="frm" method="post" action="_delivery_addprice_new.pro.php" enctype="multipart/form-data" autocomplete="off" onsubmit="return frm_submit(this);" style="margin:0;padding:0;">
			<input type="hidden" name="_mode" value='modify'>
			<input type="hidden" name="_uid" value='<?=$_uid?>'>


			<table class="form_TB">
				<colgroup>
					<col width="120px"/><col width="*"/>
				</colgroup>
				<tbody>
				<tr>
					<td class="article">상세주소</td>
					<td class="conts">
						<input type="text" name="addr" style="width:450px;" class="input_text" id="_addr1"  value="<?=trim(str_replace(trim($r['da_sido']), '', trim($r['da_addr'])))?>">
					</td>
				</tr>
				<tr>
					<td class="article">추가배송비</td>
					<td class="conts"><input type="text" name="addprice" class="input_text" value="<?=$r['da_price']?>"> 원</td>
				</tr>
				<tr>
					<td class="conts" colspan="2">
						<?=_DescStr("상세주소는 추가배송비가 적용될 행정구역단위까지 입력해주세요. 배송주소와 지역명이 일치해야 추가배송비가 적용됩니다.")?>
						<?=_DescStr("추가배송비가 0원이면 도서산간지역은 추가되지만 추가배송비가 적용되지는 않습니다.")?>
						<?=_DescStr("오타 및 띄어쓰기를 잘못하였을경우 정상적으로 적용되지 않습니다. 상세주소 수정시 주의해주시기 바랍니다.", "orange")?>
					</td>
				</tr>
				</tbody>
			</table>

			<div class="top_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="저장" value="저장"></span>
					<span class="shop_btn_pack"><span class="blank_3"></span></span>
					<span class="shop_btn_pack"><a href="#none" id="close" class="medium gray">닫기</a></span>
				</div>
			</div>

		</form>
	</div>
</div>

<script type="text/javascript">
// 닫기버튼
$('#close').on('click',function(){ self.opener = self; window.close(); });


</script>