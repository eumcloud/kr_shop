<?PHP

	// 페이지 표시
	$app_current_link = "/totalAdmin/_mailing_data.list.php";

	include_once("inc.header.php");
	if( $_mode == "modify" ) {
		$row = _MQ("select * from odtMailingData where md_uid='${_uid}' ");
		$_str = "수정";
	}
	else { $_str = "등록"; }
?>

<script language='javascript' src='../include/js/lib.validate.js'></script>


<form name=frm method=post ENCTYPE='multipart/form-data' action=_mailing_data.pro.php>
<input type=hidden name=_uid value='<?=$_uid?>'>
<input type=hidden name=_mode value='<?=$_mode?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">

					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
                                    <tr>
                                        <td class="article">메일링 제목</td>
                                        <td class="conts"><input type=text size=100 class=input_text name=_title value="<?=stripslashes($row[md_title])?>" hname="메일링 제목" required>
                                        <?=_DescStr("광고성, 이벤트성 메일을 발송시 메일제목 앞에 <b style='color:#01f'>'(광고)'</b> 문구를 반드시 붙이셔야합니다. ")?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="article">메일링 타입</td>
                                        <td class="conts">
                                            <label style="margin:0 5px;"><input type="radio" name="_adchk" value="Y" <?=($row['md_adchk'] == 'Y' ? 'checked':'' )?>>광고성,이벤트성</label>
                                            <label><input type="radio" name="_adchk" value="Y" <?=($row['md_adchk'] == 'N' || $row['md_adchk'] == '' ? 'checked':'' )?>>일반</label>
                                        </td>
                                    </tr>
									<tr>
										<td class="article">메일링 내용<br><br><b>(이미지 폭 730px 이하로 등록)</b></td>
										<td class="conts"><textarea name="_content"  style="width:98%;height:300px;" geditor ><?=stripslashes($row[md_content])?></textarea></td>
									</tr>
									<tr>
										<td class="article">참고사항</td>
										<td class="conts">등록시간 : <?=$row[md_rdate]?></td>
									</tr>
								</tbody>
						</table>
					</div>

<?=_submitBTN("_mailing_data.list.php")?>
</form>

<?PHP
	include_once("inc.footer.php");
?>


<SCRIPT>
	$(document).ready(function() {
		//alert($('#app_cnt' , opener.document ).val());
	});
</SCRIPT>