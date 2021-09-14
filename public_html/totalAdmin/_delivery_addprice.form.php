<?PHP

	// 팝업형태 적용
	$app_mode = "popup";

	include_once("inc.header.php");

	if ( !$_COOKIE["auth_adminid"] ) {
		error_loc("/");
	}


	if( $_mode == "modify" ) {
		$que = "  select * from odtDeliveryAddprice where da_uid='". $_uid ."' ";
		$r = _MQ($que);
		$r_post = explode("-",$r[da_post]);
	}
?>

<div style="padding:20px;">
<div class="form_box_area" style="margin:0!important;">
<form name="frm" method="post" action="_delivery_addprice.pro.php" enctype="multipart/form-data" autocomplete="off" onsubmit="return frm_submit(this);" style="margin:0;padding:0;">
	<input type="hidden" name="_mode" value='<?=$_mode?>'>
	<input type="hidden" name="_uid" value='<?=$_uid?>'>
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">
	<input type="hidden" id="_addr_doro" value=""/>
	<input type="hidden" id="_addr2" value=""/>

	<table class="form_TB">
		<colgroup>
			<col width="70px"/><col width="*"/>
		</colgroup>
		<tbody>
		<tr>
			<td class="article">주소</td>
			<td class="conts">
				<input type="text" name="da_post1" id="_post1" style="width:50px;" class="input_text" readonly value="<?=$r_post[0]?>"> - <input type="text" id="_post2" name="da_post2" class="input_text" style="width:50px;" readonly value="<?=$r_post[1]?>">
				<input type="text" name="da_zone" id="_zonecode" style="width:80px;" class="input_text" readonly value="<?=$r[da_zone]?>"/>
				<span class="shop_btn_pack" style="margin-right: 5px;"><a class="small red" href="#none" onclick="new_post_view();return false;" title="우편번호찾기">우편번호찾기</a></span>
				<p style="margin-top: 5px;"><input type="text" name="da_addr" style="width:450px;" class="input_text" id="_addr1" readonly value="<?=$r[da_addr]?>"></p>
			</td>
		</tr>
		<tr>
			<td class="article">추가금액</td>
			<td class="conts"><input type="text" name="da_price" class="input_text" value="<?=$r[da_price]?>"> 원</td>
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
<? include_once dirname(__FILE__)."/../newpost/newpost.search.php"; // 배송지정보 끝 ?>
<script type="text/javascript">

$('#find_post').on('click',function(){ $('input[name=post_keyword]').focus(); });
$('#close').on('click',function(){ self.opener = self; window.close(); });
function frm_submit(f) {
	if( f.da_price.value == "" ) { 
		alert('추가금액을 입력하여 주십시요');
		f.da_price.focus();
		return false;
	}

	return true;
}

</script>