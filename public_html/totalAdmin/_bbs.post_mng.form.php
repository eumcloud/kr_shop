<?PHP
// 메뉴 지정 변수
$app_current_link = "/totalAdmin/_bbs.post_mng.list.php";

include_once("inc.header.php");


// - 게시판 종류 배열형태로 추출 ---
$_ARR_BBS = arr_board("Y");

if($_mode == "modify") {

	$row = _MQ("
		select b.* ,bi.*,  m.name from odtBbs as b
		inner join odtBbsInfo as bi on (bi.bi_uid = b.b_menu)
		left join odtMember as m on (m.id=b.b_inid)
		where b_uid='{$_uid}'
	");
	$_str = "수정";
	$b_inid = $row['b_writer_type'] == "member" ? showUserInfo($row['b_inid']) : "비회원";
	$b_writer = $row['b_writer'];

}
// - 답변 ---
else if( $_uid && $_mode == "reply" ) {

	$row = _MQ("
		select b.* ,bi.*,  m.name from odtBbs as b
		inner join odtBbsInfo as bi on (bi.bi_uid = b.b_menu)
		left join odtMember as m on (m.id=b.b_inid)
		where b_uid='{$_uid}'
	");

	$_str = "답글";
	$b_inid = $row_admin['id'];
	$b_writer = $row_admin['name'];

	$row['b_title'] = "[Re]" . $row['b_title'];
	$row['b_content'] = "----------------------------------------<br>☞ " . $row['b_writer'] . "님의 글입니다.<br>----------------------------------------<br>" . $row['b_content'] . "<br>----------------------------------------";

}
else {
	$_str = "등록";
	$b_inid = $row_admin['id'];
	$b_writer = $row_admin['name'];
}
?>
<form name="postFrm" id="postFrm" method="post" enctype="multipart/form-data" action="_bbs.post_mng.pro.php">
	<input type="hidden" name="_mode" value="<?=$_mode?>"/>
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"/>
	<input type="hidden" name="_uid" value="<?=$_uid?>"/>

	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200"/>
				<col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">게시판종류<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<select name="_menu" id="_menu_select">
							<option value="">- 게시판 선택 -</option>
							<?php foreach($_ARR_BBS as $k=>$v) { ?>
							<option value="<?=$k?>" data-type="<?=$v['type']?>" <?=$row['b_menu']==$k?'selected':''?>><?=$v['name']?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr id="_category" style="display:<?=$row['bi_list_type']=='faq'?'':'none'?>">
					<td class="article">카테고리</td>
					<td class="conts">
						<?=_InputSelect( "_category" , array_values($arr_board_category['faq']), ($row[b_category] ? $row[b_category] : "") , "" , array_values($arr_board_category['faq']) , '-카테고리선택-') ?>
					</td>
				</tr>
				<tr>
					<td class="article">등록자ID<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><?=$b_inid?></td>
				</tr>
				<tr>
					<td class="article">등록자이름<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><input type=text name="_writer" value='<?=$b_writer?>' class="input_text" style="width:100px" ></td>
				</tr>
				<tr id="_date_input" style="display:<?=in_array($row['bi_list_type'], array("event","event_thumb"))?NULL:"none";?>">
					<td class="article">이벤트 기간</td>
					<td class="conts">
						시작일 : <input type=text name="_sdate" ID="_sdate" value='<?=$row['b_sdate'] ? $row['b_sdate'] : date('Y-m-d')?>'  maxlength="10" class="input_text" readonly style="cursor:pointer;width:70px">
						~
						종료일 : <input type=text name="_edate" ID="_edate" value='<?=$row['b_edate'] ? $row['b_edate'] : date('Y-m-d')?>' maxlength="10" class="input_text" readonly style="cursor:pointer;width:70px">
						<?=_DescStr("이벤트 기간이 지나면 자동으로 이벤트 마감으로 표시됩니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">공지로 지정</td>
					<td class="conts">
					<label><input type="checkbox" name="_notice" value="Y" <?=$row['b_notice'] == "Y"?"checked":NULL;?>> 공지글로 지정합니다.</label>
					</td>
				</tr>
				<tr>
					<td class="article">비밀글</td>
					<td class="conts">
					<label><input type="checkbox" name="_secret" value="Y" <?=$row['b_secret'] == "Y"?"checked":NULL;?>> 비밀글로 지정합니다.</label>
					</td>
				</tr>
				<tr>
					<td class="article">베스트</td>
					<td class="conts">
					<label><input type="checkbox" name="_bestview" value="Y" <?=$row['b_bestview'] == "Y"?"checked":NULL;?>> 베스트글로 지정합니다.</label>
					<?=_DescStr("공지사항과 별개로 선택적 노출을 위해 설정합니다 (예: 고객센터 메인의 자주묻는질문 TOP5).")?>
					</td>
				</tr>
				<tr>
					<td class="article"><?=$row['bi_list_type']  == "faq"?"질문": "제목";?> </td>
					<td class="conts">
					<input type="text" name="_title" value="<?=stripslashes($row['b_title'])?>" class="input_text" style="width:400px" >
					</td>
				</tr>
				<tr>
					<td class="article"><?=$row['bi_list_type']  == "faq"?"답변": "내용";?><span class="ic_ess" title="필수"></span></td>
					<td class="conts">
					<textarea name="_content" class="input_text"  style="width:90%;height:350px;" geditor><?=stripslashes($row['b_content'])?></textarea>
					</td>
				</tr>
				<tr>
					<td class="article">첨부파일</td>
					<td class="conts">
						<?=_FileForm( "..".IMG_DIR_BOARD, "_file", $row['b_file'])?>
						<?=_DescStr("zip 파일만 등록 가능합니다 (2MB 이하).")?>
					</td>
				</tr>
				<tr id="_thumb_input">
					<td class="article">썸네일</td>
					<td class="conts">
						<?=_PhotoForm( "..".IMG_DIR_BOARD, "_thumb", $row['b_thumb'])?>
						<?=_DescStr("목록에 표시될 썸네일을 입력합니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">참고사항</td>
					<td class="conts">
						등록시간 : <?=$row['b_rdate']?><br>
						조회수 : <?=number_format($row['b_hit'])?><br>
						댓글수 : <?=number_format($row['b_talkcnt'])?>
					</td>
				</tr>


			</tbody>
		</table>

	</div>

	<?=_submitBTN("_bbs.post_mng.list.php")?>
</form>


<link rel="stylesheet" href="/include/js/jquery/jqueryui/jquery-ui.min.css" type="text/css">
<script src="../../include/js/lib.validate.js"></script>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script src="../../include/js/jquery/jquery.formatCurrency-1.4.0.min.js"></script>
<script>
$(function() {
	$("#_sdate").datepicker({ changeMonth: true, changeYear: true });
	$("#_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
	$("#_sdate").datepicker( "option",$.datepicker.regional["ko"] );

	$("#_edate").datepicker({ changeMonth: true, changeYear: true });
	$("#_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
	$("#_edate").datepicker( "option",$.datepicker.regional["ko"] );
});

var filed_on_off_setting = function() {
	$("#_date_input").hide(); $('#_thumb_input').hide(); $('#_category').hide();

	if($("#_menu_select option:selected").data('type') == "event" ) {
		$("#_date_input").show();
	}
	if($("#_menu_select option:selected").data('type') == "event_thumb" ) {
		$("#_date_input").show(); $('#_thumb_input').show();
	}
	if($("#_menu_select option:selected").data('type') == "news" ) {
		$('#_thumb_input').show();
	}
	if($("#_menu_select option:selected").data('type') == "gallery" ) {
		$('#_thumb_input').show();
	}
	if($("#_menu_select option:selected").data('type') == "faq" ) {
		$('#_category').show();
	}
}

$(document).ready(function(){

	filed_on_off_setting();
	$("#_menu_select").change(filed_on_off_setting);

// - 회원가입 박스 validate ---
	$("#postFrm").validate({
		ignore: ".ignore",
		rules: {
			_menu: {
				required: function() {formSubmitSet(); return true; }
			},
			_writer:{ required : true},
			_title:{ required : true}
		},
		invalidHandler: function(event, validator) {
			// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
		},
		messages: {
			_menu : {
				required: "게시판을 선택 하세요."
			},
			_writer: {
				required: "등록자 이름을 입력하세요."
			},
			_title: {
				required: "글 제목을 입력하세요."
			}
		},
		submitHandler : function(form) {
			// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
			form.submit();
		}
	});
});

</script>
<?PHP include_once("inc.footer.php"); ?>