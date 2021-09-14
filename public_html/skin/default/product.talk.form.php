<?
	$code = $row_product['code'];
	if(!is_login()) {
		$talk_content = "<div class='textarea_box'><textarea name='_content' id='_content' class='textarea_design' onclick=\"login_alert('".$_PVSC."')\">로그인을 하시면 글을 등록하실 수 있습니다.</textarea></div>";
	} else {
		$talk_content = "<div class='textarea_box'><textarea name='_content' id='_content' class='textarea_design' placeholder='문의 내용을 입력해주세요.'></textarea></div>";
	}
?>
<div class="cm_shop_inner">

	<!-- 등록폼 -->
	<div class="form_area">
		<div class="inner">
			<form name="talk_frm" method="post">
			
			<!-- 비회원폼 가능하면 나옴  -->
			<!-- <div class="none_member">
				<input type="text" name="" class="input_design" placeholder="이름" />
				<input type="password" name="" class="input_design" placeholder="비밀번호(4자이상)" />
			</div> -->			
			<!-- <div class="form_title"><input type="text" name="" class="input_design" placeholder="제목을 입력해주세요."/></div> -->

			<div class="form_conts">
				<?=$talk_content?>
				<? if(is_login()){ ?>
				<input type="button" class="btn_ok" onclick="talk_add()" value="등록"/>
				<? } else { ?>
				<input type="button" class="btn_ok" onclick="login_alert('<?=$_PVSC?>')" value="등록"/>
				<? } ?>
			</div>

			</form>
		</div>
	</div>

	<!-- 글리스트 -->
	<div id="ID_talk_list"></div>

</div> <!-- .cm_shop_inner -->


<script>
function talk_add() {

	if(!$("#_content").val()) {	alert("내용을 입력하세요."); $("#_content").focus(); return; }
	if(!confirm("글을 등록하시겠습니까?")) return false;

	$.ajax({
		url: "/pages/product.talk.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=add&pcode=<?=$pcode?>&_content=" + encodeURIComponent($("#_content").val()) ,
		success: function(data){ talk_view(); $("#_content").val(''); }
	});
	return false;
}

var old_view_ttNo = "";
function view_reply_form(ttNo) {
	$(".reply_form").hide();
	if(old_view_ttNo == ttNo) { $("#reply_form_"+ttNo).hide(); old_view_ttNo = ""; } 
	else { $("#reply_form_"+ttNo).show(); old_view_ttNo = ttNo; }	
}

function talk_reply_add(ttNo) {
	if(!$("#_content_"+ttNo).val()) { alert("내용을 입력하세요."); $("#_content_"+ttNo).focus(); return; }
	if(!confirm("글을 등록하시겠습니까?")) return false;
	$.ajax({
		url: "/pages/product.talk.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=reply&ttNo="+ttNo+"&pcode=<?=$pcode?>&_content=" + encodeURIComponent($("#_content_"+ttNo).val()) ,
		success: function(data){ talk_view(); $("#_content_"+ttNo).val(''); }
	});
	return false;
}

// 갯수 추출
function talk_get_cnt() {
	$.ajax({
		url: "/pages/product.talk.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=getcnt&pcode=<?=$pcode?>",
		success: function(data){
			//$(".talk_cnt").html(data);
		}
	});
}

// 리뷰 삭제
function talk_del(uid) {
	if(confirm("정말 삭제하시겠습니까?")) {
		$.ajax({
			url: "/pages/product.talk.pro.php",
			cache: false,
			type: "POST",
			data: "_mode=delete&uid=" + uid ,
			success: function(data){
				if( data == "no data" ) {
					alert('본인이 등록하신 글이 아닙니다.');
				}
				else if( data == "is reply" ) {
					alert('댓글이 있으므로 삭제가 불가합니다.');
				}
				else {
					alert('정상적으로 삭제하였습니다.');
					talk_view();
				}
			}
		});
	}
}

// 리뷰 보기
function talk_view(listpg) {
	if(listpg == undefined) listpg = 1;
	$.ajax({
		url: "/pages/product.talk.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=view&pcode=<?=$pcode?>&listpg="+listpg,
		success: function(data){
			$("#ID_talk_list").html(data);
		}
	});
	talk_get_cnt();
}

var old_talk_id;
function talk_show(id) {
	$("#ID_talk_list .list_area li").removeClass('open');

	// 열려있는걸 다시 클릭했을때는 닫기만 처리한다.
	if(old_talk_id == id) {this.old_talk_id = 0;return;}
	$("#"+id).addClass('open');
	old_talk_id = id;
}

// 댓글 목록 onload -> loading
$(document).ready(function() { talk_view(); });
</script>