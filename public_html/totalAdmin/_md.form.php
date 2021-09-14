<?PHP

	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_md.list.php";

	include_once("inc.header.php");

	if($_mode == "modify") {
        $row = _MQ(" SELECT * FROM odtMD WHERE mdNo='" . $mdNo . "' ");
	}
?>


<form name=frm method=post action=_md.pro.php enctype='multipart/form-data' >
<input type=hidden name=_mode value='<?=($_mode ? $_mode : "add")?>'>
<input type=hidden name=mdNo value='<?=$mdNo?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">

					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">MD명<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='mdName' size=30 class='input_text' value="<?=$row[mdName]?>"></td>
									</tr>
									<tr>
										<td class="article">아이디</td>
										<td class="conts"><input type=text name='mdID' size=30 class='input_text' value="<?=$row[mdID]?>"></td>
									</tr>
									<tr>
										<td class="article">닉네임<span class='ic_ess' title='필수'></span></td>
										<td class="conts"><input type=text name='mdNick' size=30 class='input_text' value="<?=$row[mdNick]?>"></td>
									</tr>
									<tr>
										<td class="article">특이사항</td>
										<td class="conts"><input type=text name='mdUnique' size=60 class='input_text' value="<?=$row[mdUnique]?>"></td>
									</tr>
									<tr>
										<td class="article">목표</td>
										<td class="conts"><input type=text name='mdAim' size=60 class='input_text' value="<?=$row[mdAim]?>"></td>
									</tr>
									<tr>
										<td class="article">사진</td>
										<td class="conts"><?=_PhotoForm( "../upfiles/member" , "mdImg"  , $row[mdImg] )?></td>
									</tr>
								</tbody> 
							</table>
				
					</div>

					<?=_submitBTN("_md.list.php")?>

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
				mdName: { required: true },// MD명
				mdNick: { required: true }// 닉네임
            },
            messages: {
				mdName: { required: "MD명을 입력하시기 바랍니다." },// MD명
				mdNick: { required: "닉네임을 입력하시기 바랍니다." }// 닉네임
            }
        });
		// - validate --- 
	});

</SCRIPT>
