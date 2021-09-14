<?PHP
	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_bbs.board.list.php";

	include_once("inc.header.php");

	if( $_mode == "modify" ) {
		$row = _MQ("select * from odtBbsInfo where bi_uid='{$_uid}'");
		$_str = "수정";
	} else {
		$_str = "등록"; 
		$app_info = "관리자";
	}
?>

<script language='javascript' src='../../include/js/lib.validate.js'></script>

<form name="bbsFrm" id="bbsFrm" method="post" ENCTYPE="multipart/form-data" action="_bbs.board.pro.php">
<input type="hidden" name="valitmp" value=""/>
<input type="hidden" name="_mode" value="<?=$_mode?>"/>
<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"/>

	<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 기본설정</div>
	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
				<colgroup>
					<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
				</colgroup>
				<tbody>
				
					<tr>
						<td class="article">게시판아이디<span class="ic_ess" title="필수"></span></td>
						<td class="conts"><input type=text name="_uid" class="input_text <?=$_mode == "modify" ? "ignore" : NULL;?>" style="width:100px" value='<?=$row[bi_uid]?>' <?=$_mode == "modify" ? "readonly" : NULL;?>>
						<?=$_mode == "modify" ? _DescStr("게시판 아이디는 수정할 수 없습니다.") : NULL;?>
						</td>
					</tr>
					<tr>
						<td class="article">게시판이름<span class="ic_ess" title="필수"></span></td>
						<td class="conts"><input type=text name="_name" class="input_text" style="width:150px" value='<?=$row[bi_name]?>'></td>
					</tr>
					<tr>
						<td class="article">게시판유형</td>
						<td class="conts">
							<?=_InputRadio( "_list_type" , array_keys($arrBoardType), ($row[bi_list_type] ? $row[bi_list_type] : "board") , "" , array_values($arrBoardType) , '') ?>
							<?=$_mode=='modify'?_DescStr("게시판 유형을 변경할 경우 특성에 따라 일부 기능에 제한이 있을 수 있습니다."):''?>
						</td>
					</tr>
					<tr>
						<td class="article">노출여부</td>
						<td class="conts"><?=_InputRadio( "_view" , array('Y','N'), ($row[bi_view] ? $row[bi_view] : "Y") , "" , array('노출','숨김') , '') ?></td>
					</tr>
				</tbody> 
			</table>

	</div>

	<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 권한 및 기타 기능 설정</div>
	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
			</colgroup>
			<tbody>

				<tr>
					<td class="article">권한설정</td>
					<td class="conts">
					<?=_InputSelect( "_auth_list" , array('0','1','9'), $row[bi_auth_list] , "" , array('제한없음','일반회원','관리자') , ' - 목록보기 - ') ?>
					<?=_InputSelect( "_auth_view" , array('0','1','9'), $row[bi_auth_view] , "" , array('제한없음','일반회원','관리자') , ' - 내용보기 - ') ?>
					<?=_InputSelect( "_auth_write" , array('0','1','9'), $row[bi_auth_write] , "" , array('제한없음','일반회원','관리자') , ' - 글쓰기 - ') ?>
					<?=_InputSelect( "_auth_reply" , array('0','1','9'), $row[bi_auth_reply] , "" , array('제한없음','일반회원','관리자') , ' - 답글쓰기 - ') ?>
					<?=_InputSelect( "_auth_comment" , array('1','9'), $row[bi_auth_comment] , "" , array('일반회원','관리자') , ' - 댓글쓰기 - ') ?>
					<?=_DescStr("공지사항, 자주묻는질문, 이벤트는 위 설정과 관계없이 관리자만 등록/답변 할 수 있습니다.")?>
					<?=_DescStr("이벤트(썸네일), 갤러리 유형 게시판은 위 설정과 관계없이 답글쓰기가 불가능합니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">댓글기능</td>
					<td class="conts"><?=_InputRadio( "_comment_use" , array('Y','N'), ($row[bi_comment_use] ? $row[bi_comment_use] : "N") , "" , array('사용','사용안함') , '') ?></td>
				</tr>
				<tr>
					<td class="article">비밀글기능</td>
					<td class="conts"><?=_InputRadio( "_secret_use" , array('Y','N'), ($row[bi_secret_use] ? $row[bi_secret_use] : "N") , "" , array('사용','사용안함') , '') ?></td>
				</tr>
				<tr style="display:none">
					<td class="article">파일업로드기능</td>
					<td class="conts"><?=_InputRadio( "_file_upload_use" , array('Y','N'), ($row[bi_file_upload_use] ? $row[bi_file_upload_use] : "N") , "" , array('사용','사용안함') , '') ?></td>
				</tr>
				<tr style="display:none">
					<td class="article">업로드파일 용량제한</td>
					<td class="conts"><input type=text name="_file_size_limit" class="input_text number_style" maxlength=8 style="width:80px" value='<?=$row[bi_file_size_limit]?>'>
					<?=_DescStr(" Byte 단위로 입력하되 최대값은 2Mb를 넘을수 없습니다. (예: 1Mb일 경우 1,000,000 을 입력)")?>
					</td>
				</tr>
				<tr>
					<td class="article">페이지당 게시물 개수</td>
					<td class="conts">
					<input type=text name="_listmaxcnt" class="input_text number_style" maxlength=3 style="width:40px" value='<?=$row[bi_listmaxcnt]?>'>
					<?=_DescStr("미입력시 20개씩 출력")?></td>
				</tr>
				<tr>
					<td class="article">new 아이콘 노출기간(일)</td>
					<td class="conts"><input type=text name="_newicon_view" class="input_text number_style" maxlength=2 style="width:40px" value='<?=$row[bi_newicon_view]?>'>
					<?=_DescStr("미입력시 사용안함")?></td>
				</tr>
			</tbody> 
		</table>

	</div>

	<div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ HTML 설정</div>
	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
				<colgroup>
					<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
				</colgroup>
				<tbody>

					<tr>
						<td class="article">상단디자인 HTML</td>
						<td class="conts">
						<textarea name=_html_header class="input_text"  style="width:90%;height:200px;"><?=stripslashes($row[bi_html_header])?></textarea>
						</td>
					</tr>
					<tr>
						<td class="article">하단디자인 HTML</td>
						<td class="conts">
						<textarea name=_html_footer class="input_text"  style="width:90%;height:200px;"><?=stripslashes($row[bi_html_footer])?></textarea>
						</td>
					</tr>
				</tbody> 
			</table>

	</div>


	<?=_submitBTN("_bbs.board.list.php")?>
</form>



<script>
$(document).ready(function(){
// - 회원가입 박스 validate --- 
	$("#bbsFrm").validate({
		ignore: ".ignore",
		rules: {
			valitmp : {required: function() {formSubmitSet(); return false; }},
			_uid: {
				required: true,
				remote: "_bbs.board.pro.php?_mode=duplication_check"  
			},
			_name:{ required : true},
		},
		invalidHandler: function(event, validator) {
			// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

		},
		messages: {
			valitmp : "",
			_uid : { 
				required: "게시판 아이디를 입력하세요.",
				remote: "이미 존재하는 게시판 아이디 입니다."
			},
			_name: { 
				required: "게시판 이름을 입력하세요."
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