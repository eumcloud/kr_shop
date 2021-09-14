<?PHP

	$pass_menu = $_REQUEST['pass_menu'] ? $_REQUEST['pass_menu'] : "request";

	// 페이지 표시
	$app_current_link = "/totalAdmin/_request.list.php?pass_menu=" . $pass_menu ;

	include_once("inc.header.php");

	if( $_mode == "modify" ) {
		$row = _MQ(" select * from odtRequest where r_uid='{$_uid}' ");
	}

	if( !$pass_menu ) {
		error_msg("메뉴를 선택하시기 바랍니다.");
	}

?>

<script language='javascript' src='../../include/js/lib.validate.js'></script>


<form name=frm method=post ENCTYPE='multipart/form-data' action=_request.pro.php>
<input type=hidden name=_mode value='<?=$_mode?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type=hidden name=_uid value='<?=$_uid?>'>
<input type=hidden name=pass_menu value='<?=$pass_menu?>'>
<input type=hidden name=_menu value='<?=$pass_menu?>'>

<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
			<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
		</colgroup>
		<tbody>
			<?if( in_array($row['r_menu'] , array("request")) ) {?>								
			<tr>
				<td class="article">회원ID<span class="ic_ess" title="필수"></span></td>
				<td class="conts"><?=showUserInfo($row[r_inid],"")?></td>
			</tr>
			<? } ?>
			<?if( in_array($row['r_menu'] , array("partner")) ) {?>								
			<tr>
				<td class="article">문의자 성명<span class="ic_ess" title="필수"></span></td>
				<td class="conts">
				<input type=text name=_comname value='<?=stripslashes($row['r_comname'])?>' style="width:120px"  maxlength=50 class=input_text hname='문의자 성명' required></td>
			</tr>
			<tr>
				<td class="article">문의자 연락처</td>
				<td class="conts">
				<input type=text name=_tel value='<?=stripslashes($row['r_tel'])?>' class="input_text" style="width:120px"  maxlength=50  hname='문의자 연락처' >
				</td>
			</tr>
			<tr>
				<td class="article">문의자 휴대전화</td>
				<td class="conts">
				<input type=text name=_hp value='<?=stripslashes($row['r_hp'])?>' class="input_text" style="width:120px"  maxlength=50  hname='문의자 휴대전화' >
				</td>
			</tr>
			<tr>
				<td class="article">문의자 이메일</td>
				<td class="conts">
				<input type=text name=_email value='<?=stripslashes($row['r_email'])?>' style="width:200px" maxlength=50 class=input_text hname='문의자 이메일' ></td>
			</tr>
			<?}?>
			<tr>
				<td class="article">제목<span class="ic_ess" title="필수"></span></td>
				<td class="conts"><input type="text" name="_title" class="input_text" style="width:400px" value='<?=stripslashes($row['r_title']) ?>'  hname='제목'/></td>
			</tr>

			<tr>
				<td class="article">문의내용<span class="ic_ess" title="필수"></span></td>
				<td class="conts">
				<textarea name=_content  hname='문의내용'  class="input_text"  style="width:90%;height:200px;"><?=stripslashes($row['r_content'])?></textarea>
				</td>
			</tr>
			<?if( in_array($row['r_menu'] , array("partner")) ) {?>
			<tr>
				<td class="article">문의 첨부파일</td>
				<td class="conts">
					<?=_FileForm( "../../upfiles/normal" , "_file"  , $row['r_file'] )?>
				</td>
			</tr>
			<? } ?>
			<tr>
				<td class="article">답변상태</td>
				<td class="conts">
					<?=_InputRadio( "_status" , array('답변대기','답변완료') , ($row['r_status'] ? $row['r_status'] : "답변대기") , "" , array('답변대기','답변완료') , "")?>
				</td>
			</tr>
			<tr>
				<td class="article">관리자답변</td>
				<td class="conts">
				<textarea name=_admcontent class="input_text" style="width:90%;height:300px;" hname='관리자메모'><?=stripslashes($row['r_admcontent'])?></textarea></td>
			</tr>
			<tr>
				<td class="article">답변 첨부파일</td>
				<td class="conts">
					<?=_FileForm( "../../upfiles/normal" , "_admfile"  , $row['r_admfile'] )?>
				</td>
			</tr>
			<?if( in_array($row['r_menu'] , array("partner")) ) {?>
			<tr>
				<td class="article">이메일발송</td>
				<td class="conts">
					<label><input type="checkbox" name="_sendmail" value="Y" <?=$row['r_status']=='답변대기'?'checked':''?>/> 답변내용 메일 발송하기</label>
					<?=_DescStr("답변내용을 이메일로 발송하려면 체크된 상태로 저장하세요.")?>
				</td>
			</tr>
			<? } ?>
			<tr>
				<td class="article">참고사항</td>
				<td class="conts">문의등록시간 : <?=$row['r_rdate']?></td>
			</tr>


		</tbody> 
	</table>

</div>


<?=_submitBTN("_request.list.php" , "pass_menu={$pass_menu}")?>
</form>


<?PHP
	include_once("inc.footer.php");
?>