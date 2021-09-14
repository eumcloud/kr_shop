<?PHP
	include_once("inc.header.php");

?>



<form name="frm" method=post action=_config.mail.pro.php ENCTYPE='multipart/form-data' onsubmit="return mail_submit()" target="common_frame">
<input type="hidden" name=_mail_checking id=_mail_checking value='0'>

					<!-- 검색영역 -->
					<div class="form_box_area">

						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
								
									<tr>
										<td class="article">원데이넷 아이디<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><input type="text" name="amail_id" id="_mailid" class="input_text" onchange="id_change()" style="width:120px; float:left" value='<?=$row_setup[amail_id]?>' />
										
										<span class="shop_btn_pack" style="margin-left:5px"><a href="#none" onclick="idchk_onedaynet()" class="small blue" title="원데이넷 아이디 확인" >원데이넷 아이디 확인</a></span>
										
										<div style="clear:both;"><?=_DescStr("<normal ID='__idchk_onedaynet'>계정정보를 입력하세요</normal>")?></div>
										</td>
									</tr>
									<tr>
										<td class="article">원데이넷 패스워드<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><input type="password" name="amail_pw"  id="_mailpw" class="input_text" style="width:120px" value='<?=$row_setup[amail_pw]?>' />
										<?=_DescStr("원데이넷 패스워드를 입력하세요.")?>
										</td>
									</tr>

									<tr>
										<td class="article">사용여부<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><?=_InputRadio( "amail_chk" , array('Y','N') , $row_setup[amail_chk] ? $row_setup[amail_chk] : "N" , "" , array('활성화','비활성화') , "")?></td>
									</tr>
								</tbody> 
							</table>
				
					</div>
					<!-- // 검색영역 -->

<?=_submitBTNsub()?>

</form>

					<div class="form_box_area" >
						<img src="./images/Amail_01_1215.jpg">
					</div>

<script>
	function mail_submit(frm) {
		if(!$("#_mail_checking").val()) {
			alert("원데이넷 아이디 확인 버튼을 눌러 인증하시기 바랍니다.");
			return false;
		}
	}

	function id_check_ok() {
		$("#_mail_checking").val("1");
	}
	function id_check_fail() {
		$("#_mail_checking").val("0");
	}
	function id_change() {
		$("#__idchk_onedaynet").html("<span style=\"color:#FF0000\">아이디 변경을 원하시면 아이디 확인 버튼을 눌러 인증하시기 바랍니다.</span>");
		id_check_fail();
	}

	function idchk_onedaynet(){
		if(!$("#_mailid").val() || !$("#_mailpw").val()) {
			alert($("#_mailid").val() + $("#_mailpw").val() + "원데이넷 아이디와 비밀번호를 입력하세요");
			id_check_fail();
			return;
		}
		$.ajax({
			url: "_config.idchk_onedaynet.php",
			cache: false,
			type: "POST",
			data: "_id_onedaynet=" + $("#_mailid").val() + "&_pw_onedaynet=" + $("#_mailpw").val() ,
			success: function(data){
				if(data == "no") {
					$("#__idchk_onedaynet").html("<span style=\"color:#FF0000\">존재하지 않는 회원으로 적용할 수 없습니다.</span>");
					id_check_fail();
				}
				else if(data == "yes") {
					$("#__idchk_onedaynet").html("<span style=\"color:#00AA00\">존재하는 회원으로 적용이 가능합니다.</span>");
					id_check_ok();
				}
			}
		});
	}
</script>
<?PHP
	include_once("inc.footer.php");
?>