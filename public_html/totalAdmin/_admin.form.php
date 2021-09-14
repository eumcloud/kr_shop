<?PHP

	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_admin.list.php";

	include_once("inc.header.php");

	$app_cpname = "";
	if( $_mode == "modify" ) {
		$que = "  select * from odtAdmin where serialnum='". $serialnum ."' ";
		$r = _MQ($que);
	}
?>


<form name=frm method=post action=_admin.pro.php enctype='multipart/form-data' >
<input type=hidden name=_mode value='<?=($_mode ? $_mode : "add")?>'>
<input type=hidden name=serialnum value='<?=$serialnum?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type="hidden" name="curr_id" value="<?=$r[id]?>"/>

					<div class="form_box_area">

						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<?php if($_mode=="modify"){ ?>
										<tr>
											<td class="article">쇼핑몰관리자아이디<span class="ic_ess" title="필수"></span></td>
											<td class="conts"><strong><?=$r[id]?></strong><input type="hidden" name="id" value="<?=$r[id]?>"/></td>
										</tr>
									<?php }else{ ?>
										<tr>
											<td class="article">쇼핑몰관리자아이디<span class="ic_ess" title="필수"></span></td>
											<td class="conts"><input type=text name=id value='<?=$r[id]?>' size=20 maxlength=20 class=input_text></td>
										</tr>
									<?php } ?>
									<tr>
										<td class="article">쇼핑몰관리자비번<?=($_mode == "modify" ? "" : "<span class='ic_ess' title='필수'></span>")?></td>
										<td class="conts">
											비밀번호 : <input type=password name=passwd value='' size=30 maxlength=30 class=input_text><br>
											비번확인 : <input type=password name=repasswd value='' size=30 maxlength=30 class=input_text>
										</td>
									</tr>
									<tr>
										<td class="article">쇼핑몰관리자명<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><input type=text name=name value='<?=$r[name]?>' size=30 class=input_text></td>
									</tr>
									<tr>
										<td class="article">휴대폰번호<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<input type=text name=htel value='<?=$r[htel]?>' size=30 class=input_text>
											<?=_DescStr("하이픈(-)을 포함하여 입력하시기 바랍니다.")?>
										</td>
									</tr>
									<tr>
										<td class="article">이메일<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><input type=text name=email value='<?=$r[email]?>' size=30 class=input_text></td>
									</tr>
								</tbody> 
							</table>
				
					</div>

					<?=_submitBTN("_admin.list.php")?>

				</form>



<?PHP
	include_once("inc.footer.php");
?>




<SCRIPT LANGUAGE="JavaScript">

    $(document).ready(function(){
		// -  validate --- 
        $("form[name=frm]").validate({
			ignore: "input[type=text]:hidden",
            rules: {
				id: { required: true},//쇼핑몰관리자아이디
<?if( $_mode == "modify" ) {?>
                repasswd: { equalTo: "input[name=passwd]" },// 비번확인
<?}else {?>
                passwd: { required: true },// 비밀번호
                repasswd: { required: true , equalTo: "input[name=passwd]" },// 비번확인
<?}?>
				name: { required: true},//쇼핑몰관리자명
				htel: { required: true, phone:true},//휴대폰번호
				email: { required: true, email:true}//이메일
            },
            messages: {
				id: { required: "쇼핑몰관리자아이디를 입력하시기 바랍니다."},//쇼핑몰관리자아이디
<?if( $_mode == "modify" ) {?>
                repasswd: { equalTo: "비밀번호가 다릅니다." },
<?}else {?>
                passwd: { required: "비밀번호를 입력하시기 바랍니다." },// 비밀번호
                repasswd: { required: "비번확인을 입력하시기 바랍니다." , equalTo: "비밀번호가 다릅니다." },// 비번확인
<?}?>
				name: { required: "쇼핑몰관리자명을 입력하시기 바랍니다."},//쇼핑몰관리자명
				htel: { required: "휴대폰번호를 입력하시기 바랍니다.", phone : "휴대폰번호가 올바르지 않습니다."},//휴대폰번호
				email: { required: "이메일을 입력하시기 바랍니다.", email : "이메일이 올바르지 않습니다."},//이메일
            }
        });
		// - validate --- 
	});

</SCRIPT>