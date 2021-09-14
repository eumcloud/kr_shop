<?PHP
// 메뉴 지정 변수
$app_current_link = "/totalAdmin/_normalpage.list.php";

include_once("inc.header.php");

if( $_mode == "modify" ) {
	$que = "  select * from odtNormalPage where np_uid='". $_uid ."' ";
	$r = _MQ($que);
}
else {
	// 순위 지정
	$r = _MQ("  select ifnull(max(np_idx),0) as max_idx from odtNormalPage ");
	$r['np_idx'] = $r['max_idx'] + 10;
}
?>


<form name=frm method=post action=_normalpage.pro.php enctype='multipart/form-data' >
	<input type=hidden name=_mode value='<?=$_mode?>'>
	<input type=hidden name=_uid value='<?=$_uid?>'>
	<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/>
				<col width="*"/>
			</colgroup>
			<tbody>
				<?php if($_mode == "modify") { ?>
				<tr>
					<td class="article">페이지 아이디</td>
					<td class="conts">
						<input type="hidden" name="_mail_checking" id="_mail_checking" value="Y">
						<B><?=$r[np_id]?></B>
						<input type="hidden" name="_id" value="<?=$r[np_id]?>" />
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td class="article">페이지 아이디<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<input type="hidden" name="_mail_checking" id="_mail_checking" value="">
						<span class="shop_btn_pack" >
							<input type="text" name="_id" class="input_text" style="width:150px" value="<?=$r[np_id]?>" onchange="id_change()" />	
							<a href="#none" onclick="id_chk()" class="small blue" title="페이지 아이디 확인" >페이지 아이디 확인</a>
						</span>																					
						<div style="clear:both; padding-top:2px;"><?=_DescStr("<normal ID='__idchk_onedaynet'>페이지에 지정되는 고유한 아이디를 입력하시기 바랍니다. 페이지아이디는 영숫자만 허용합니다.</normal>")?></div>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td class="article">페이지 노출</td>
					<td class="conts">
						<?=_InputRadio( "_view", array('Y','N'), ($r['np_view']?$r['np_view']:"Y"), "", array('노출','숨김'), "")?>
					</td>
				</tr>
				<tr>
					<td class="article">페이지 순위<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<input type="text" name="_idx" class="input_text" style="width:20px" value="<?=$r[np_idx]?>" placeholder="노출순위"/>순위&nbsp;&nbsp;
						<?=_DescStr("낮은 순위가 먼저 나오며, 순위가 같을 경우 먼저 최근 등록한 순으로 나옵니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">페이지명<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<input type=text name=_title value='<?=$r['np_title']?>' size="100" maxlength="150" class="input_text">
					</td>
				</tr>
                <tr>
					<td class="article">페이지 내용<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<textarea name="_content" class="input_text" style="width:100%; height:400px;" geditor><?=stripslashes($r['np_content'])?></textarea>
                    </td>
				</tr>
				<?php // LDD005 { ?>
                <tr>
					<td class="article">페이지 모바일 내용<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<textarea name="_content_m" class="input_text" style="width:100%; height:400px;" geditor><?=stripslashes($r['np_content_m'])?></textarea>
                    </td>
				</tr>
				<?php // } LDD005 ?>
			</tbody> 
		</table>
	</div>
	<?=_submitBTN("_normalpage.list.php")?>
</form>



<script language="javascript">
$(document).ready(function() {
	// -  validate --- 
    $("form[name=frm]").validate({
		ignore: "input[type=text]:hidden",
        rules: {
			_id: { required: true, alphanumeric:true},//페이지 아이디
			_mail_checking: { required: true},//페이지 아이디 체크
			_view: { required: true},//페이지 노출
			_idx: { required: true},//페이지 순위
			_title: { required: true},//페이지명
			_content: { required: true}//페이지 내용
        },
        messages: {
			_id: { required: "페이지 아이디를 입력하시기 바랍니다.", alphanumeric : "페이지 아이디는 영숫자만 가능합니다."},//페이지 아이디
			_mail_checking: { required: "페이지 아이디를 확인하시기 바랍니다.."},//페이지 아이디 체크
			_view: { required: "페이지 노출을 선택하시기 바랍니다."},//페이지 노출
			_idx: { required: "페이지 순위를 입력하시기 바랍니다."},//페이지 순위
			_title: { required: "페이지명을 입력하시기 바랍니다."},//페이지명
			_content: { required: "페이지 내용을 입력하시기 바랍니다."}//페이지 내용
        }
    });
	// - validate --- 
});

function id_change() {

	$("#__idchk_onedaynet").html("<span style=\"color:#FF0000\">아이디 변경을 원하시면 아이디 확인 버튼을 눌러 인증하시기 바랍니다.</span>");
	$("#_mail_checking").val("");
}

function id_chk() {

	if(!$("input[name=_id]").val()) {

		alert($("input[name=_id]").val() + "페이지 아이디를 입력하세요");
		$("#_mail_checking").val("");

		return;
	}

	$.ajax({
		url: "_normalpage.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=idchk&_id=" + $("input[name=_id]").val() ,
		success: function(data){
			if(data == "no") {

				$("#__idchk_onedaynet").html("<span style=\"color:#FF0000\">중복되는 아이디로 적용이 불가합니다.</span>");
				$("#_mail_checking").val("");
			}
			else if(data == "yes") {

				$("#__idchk_onedaynet").html("<span style=\"color:#00AA00\">중복되지 않는 아이디로 적용이 가능합니다.</span>");
				$("#_mail_checking").val("Y");
			}
		}
	});
}
</script>

<?PHP include_once("inc.footer.php"); ?>