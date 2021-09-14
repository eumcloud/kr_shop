<div class="comment">
	<div class="comment_top">댓글<?=is_login()?'쓰기':'보기'?></div>
	<div class="comment_form">
		<form method="post" name="bbs_talk_frm_<?=$r['b_uid']?>" onsubmit="return bbs_talk_add('<?=$r['b_uid']?>')">
		<!-- 비회원등록 못할때 이부분 없어짐 -->
		<!-- <div class="input">
			<input type="text" name="" class="input_design" placeholder="작성자 이름" />
		</div> -->
		<? if(is_login() && $board_info[bi_auth_comment]<>"" && ($board_info[bi_auth_comment] <= ($row_member[Mlevel]?$row_member[Mlevel]:0) ) ) { // 비회원작성시 권한체크 수정 ?>
		<div class="textarea">
			<textarea cols="" rows="" name="bbs_talk_content" id="bbs_talk_content_<?=$r['b_uid']?>" class="textarea_design" placeholder="댓글을 입력하세요."></textarea>
			<input type="submit" name="" class="btn_ok" value="등록" />
		</div>
		<? } else if(!is_login()) { ?>
		<div class="textarea">
			<textarea class="textarea_design" placeholder="로그인하시면 댓글을 쓸 수 있습니다." onclick="login_alert('<?=$_PVSC?>')" readonly>로그인하시면 댓글을 쓸 수 있습니다.</textarea>
			<input type="button" name="" class="btn_ok" value="등록" onclick="login_alert('<?=$_PVSC?>')"/>
		</div>
		<? } else { ?>
		<div class="textarea">
			<textarea class="textarea_design" placeholder="댓글쓰기 권한이 없습니다." readonly>댓글쓰기 권한이 없습니다.</textarea>
			<input type="button" name="" class="btn_ok" value="등록" onclick="alert('댓글쓰기 권한이 없습니다.');return false;"/>
		</div>
		<? } ?>
		</form>
	</div>

	<!-- 댓글리스트 -->
	<div ID="talk_list_<?=$r['b_uid']?>"></div>
	<!-- 댓글리스트 -->

</div>

<script>
// - 댓글 등록 ---
function bbs_talk_add(buid) {
<? if( !is_login() ) { echo 'alert("먼저 로그인 하세요");';	} else { ?>
	// 댓글 내용 변수화
	$_content = $("#bbs_talk_content_"+buid);
	var app_content = $_content.val();

	if(  app_content == '' ){
		alert('댓글을 작성해 주시기 바랍니다.');
		$("#bbs_talk_content_"+buid).focus();
	} else {
		$.ajax({
			url: "/m/board.view.comment.pro.php",
			cache: false, type: "POST",
			data: "_mode=add&_buid="+ buid +"&bbs_talk_content=" + encodeURIComponent(app_content) ,
			success: function(data){ $_content.val(""); bbs_talk_view(buid); }
		});
	}
<? } ?>
	return false;
}
// - 댓글 등록 ---

// - 댓글 보기 ---
function bbs_talk_view(buid) {
	if(!buid) { buid = "<?=$r['b_uid']?>"; }
	$.ajax({
		url: "/m/board.view.comment.pro.php",
		cache: false, type: "POST",
		data: "_mode=view&_buid=" + buid,
		success: function(data){ $("#talk_list_" + buid).html(data); }
	});
}
// - 댓글 보기 ---


// - 댓글 삭제 ---
function bbs_talk_del(buid , uid) {
<? if( !is_login() ) { echo 'alert("먼저 로그인 하세요");'; } else { ?>
	if(confirm("정말 삭제하시겠습니까?")) {
		$.ajax({
			url: "/m/board.view.comment.pro.php",
			cache: false, type: "POST",
			data: "_mode=delete&uid=" + uid ,
			success: function(data){ alert('정상적으로 삭제하였습니다.'); bbs_talk_view(buid); }
		});
	}
<? } ?>
}
// - 댓글 삭제 ---

$(document).ready(function() { bbs_talk_view(); });
</script>				