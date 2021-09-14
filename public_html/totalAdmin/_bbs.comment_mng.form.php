<?PHP

	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_bbs.comment_mng.list.php";

	include_once("inc.header.php");

	// - 게시판 종류 배열형태로 추출 ---
	$_ARR_BBS = arr_board();

	if( $_mode == "modify" ) {
		$row = _MQ("
			select bt.*, b.* ,bi.*,  m.name 
			from odtBbsComment as bt
			inner join odtBbs as b on (bt.bt_buid = b.b_uid)
			inner join odtBbsInfo as bi on (bi.bi_uid = b.b_menu)
			left join odtMember as m on (m.id=b.b_inid)
			where bt_uid='{$_uid}'
		");
		$_str = "수정";
		$bt_inid = showUserInfo($row[bt_inid]);
		$bt_writer = $row[bt_writer];

	}
	else {
		$_str = "등록"; 
		$bt_inid = $row_admin[id];
		$bt_writer = "운영자";
	}

?>

<script language='javascript' src='../../include/js/lib.validate.js'></script>

<form name=commentFrm id="commentFrm" method=post ENCTYPE='multipart/form-data' action=_bbs.comment_mng.pro.php>
<input type=hidden name=_mode value='<?=$_mode?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type=hidden name=_uid value='<?=$_uid?>'>




					<div class="form_box_area">

						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>			
									<tr>
										<td class="article">게시물제목<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><b><?="[".$row[bi_name]."] ".strip_tags($row[b_title])?></b></td>
									</tr>
									<tr>
										<td class="article">등록자ID<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><?=$bt_inid?></td>
									</tr>
									<tr>
										<td class="article">등록자이름<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><input type=text name=_writer value='<?=$bt_writer?>' class="input_text" style="width:100px" ></td>
									</tr>	
									<tr>
										<td class="article">댓글내용<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
										<textarea name=_content class="input_text"  style="width:90%;height:100px;"><?=stripslashes($row[bt_content])?></textarea>
										</td>
									</tr>
									<tr>
										<td class="article">참고사항</td>
										<td class="conts">
										등록시간 : <?=$row[bt_rdate]?>
										</td>
									</tr>
								</tbody> 
							</table>
				
					</div>





<?=_submitBTN("_bbs.comment_mng.list.php")?>
</form>

<script>
		$(document).ready(function(){
		// - 회원가입 박스 validate --- 
				$("#commentFrm").validate({
						ignore: ".ignore",
						rules: {
								_writer: {
									required: function() {formSubmitSet(); return true; }
								},
								_content:{ required : true}
						},
						invalidHandler: function(event, validator) {
							// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

						},
						messages: {
								_writer: { 
									required: "등록자 이름을 입력하세요."
								},
								_content: { 
									required: "댓글 내용을 입력하세요."
								}
						},
						submitHandler : function(form) {
							// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.

							form.submit();
						}

				});
		});

</script>
<?PHP
	include_once("inc.footer.php");
?>