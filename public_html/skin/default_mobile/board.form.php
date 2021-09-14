<?
if(!$_menu) error_msg("게시판 아이디가 없습니다.");

// 게시판 정보 추출
$board_info = get_board_info($_menu);

// 게시판 아이디
$b_menu = $board_info['bi_uid'];

// 노출여부 확인
if($board_info['bi_view'] == "N" && !is_admin()) { error_msg("비공개 게시판입니다."); }

// 권한체크
if(!is_admin()) {
	if( $_mode == "reply" ) {
		if($board_info['bi_auth_reply'] && $board_info['bi_auth_reply'] > $row_member['Mlevel']) {
			switch($board_info['bi_auth_reply']) {
				case "2" :	error_msg("회원전용 게시판입니다. 로그인 하시기 바랍니다.");
				case "9" :	error_msg("관리자만 접근할수 있습니다.");
				default  :	error_msg("접근권한이 없습니다");
			}
		}
	}
	else {
		if($board_info['bi_auth_write'] && $board_info['bi_auth_write'] > $row_member['Mlevel']) {
			switch($board_info['bi_auth_write']) {
				case "2" :	error_msg("회원전용 게시판입니다. 로그인 하시기 바랍니다.");
				case "9" :	error_msg("관리자만 접근할수 있습니다.");
				default  :	error_msg("접근권한이 없습니다");
			}
		}
	}
}

// 글작성 모드
$_mode = $_mode ? $_mode : "add";
if($_mode == "modify") {
	$r = _MQ("select * from odtBbs where b_uid = '".$_uid."'");
	$_writer = $r['b_writer'];

    // -- 웹 취약점 보완 패치 -- 2019-09-16 {
    $is_auth = is_admin() || $_COOKIE["auth_request_".$r[b_uid]] ? true : false;
    if(!$is_auth && $r['b_writer_type'] == 'member') { // 비회원의 경우 제외
            if(!is_login() || $r[b_inid] <> get_userid()) error_msg("회원님께서 작성하신 글이 아닙니다.");
    }
    // -- 웹 취약점 보완 패치 -- 2019-09-16 }

}
else if($_mode == "reply") {
	$r = _MQ("select * from odtBbs where b_uid = '".$_uid."'");
	$_writer = is_login() ? $row_member['name'] : "";

	//답변글형식으로 변환
	$r['b_title'] = "[Re]".$r['b_title'];
	$r['b_content'] = "-----------------------------------------------------\n☞".($r['b_writer'])."님의 글입니다.\n-----------------------------------------------------\n".$r['b_content']."\n-----------------------------------------------------";
}
else {
	$_writer = is_login() ? $row_member['name'] : "";
}

$page_title = $board_info['bi_name'];
include dirname(__FILE__)."/cs.header.php";
?>
<div class="common_page">
<div class="common_inner common_full">

	<?=$board_info['bi_html_footer']?>

	<form name="board_form" id="board_form" method="post" action="/pages/board.pro.php" enctype="multipart/form-data" target="common_frame">
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"/>
	<input type="hidden" name="_menu" value="<?=$_menu?>"/>
	<input type="hidden" name="_mode" value="<?=$_mode?>"/>
	<input type="hidden" name="_uid" value="<?=$_uid?>"/>
	<? if( $r['b_file'] && @filesize(dirname(__FILE__)."/../../upfiles/bbs/".$r['b_file']) >0 ) { ?>
	<input type="hidden" name="_file_OLD" value="<?=$r['b_file']?>"/>
	<? } ?>
	<div class="cm_board_form">
		<ul>
			<li class="ess">
				<span class="opt">글제목</span>
				<div class="value"><input type="text" name="_title" class="input_design" placeholder="제목을 입력해주세요." value="<?=$r['b_title']?>"/></div>
			</li>
			<!-- 2칸으로 쓰고싶을때 클래스값 double -->
			<li class="ess <?=!is_login()?'double':''?>">
				<span class="opt">작성자</span>
				<div class="value"><input type="text" name="_writer" class="input_design" placeholder="이름을 입력해주세요." value="<?=$_writer?>"/></div>
			</li>
			<? if( !is_login() ){ ?>
			<li class="ess double">
				<span class="opt">비밀번호</span>
				<div class="value"><input type="password" name="_passwd" class="input_design" placeholder="비밀번호를 입력해주세요." value=""/></div>
			</li>
			<? } ?>
			<? if( in_array($board_info['bi_list_type'],array('event','event_thumb')) ) { ?>
			<li class="">
				<span class="opt">이벤트기간</span>
				<div class="value">
					<div class="input_wrap"><span class="upper_txt">시작</span><input type="text" name="_sdate" readonly class="input_design input_date" id="_sdate" value="<?=$r['b_sdate']?$r['b_sdate']:date('Y-m-d')?>" /></div>
					<div class="input_wrap"><span class="upper_txt">종료</span><input type="text" name="_edate" readonly class="input_design input_date" id="_edate" value="<?=$r['b_edate']?$r['b_edate']:date('Y-m-d',strtotime('+ 1 month'))?>" /></div>
				</div>
			</li>
			<? } ?>
			<? if( in_array($board_info['bi_list_type'],array('event_thumb','gallery','news')) ) { ?>
			<li class="">
				<!-- 일반다른 첨부파일은 모바일에서 사용안함 -->
				<span class="opt">목록이미지</span>
				<div class="value">
					<?
						unset($_thumb);
						if( $r['b_thumb'] && @filesize(dirname(__FILE__)."/../../upfiles/bbs/".$r['b_thumb']) >0 ) {
							$_thumb = "../upfiles/bbs/".$r['b_thumb'];
						}
					?>
					<!-- 카메라호출버튼 ◆◆◆◆◆ 웹에서는 카메라 촬영이 제한적일 수 있음 -->
					<label class="btn_photo_box">
						<input type="file" name="_thumb" id="input_file" accept=".jpg,.gif,.jpeg,.png" class="input_photo" />
						<span class="upper_txt">이미지 올리기</span>
					</label>
					<a href="" class="btn_photo_del"></a>
					<!-- 업로드 이미지 미리보기 -->
					<div class="photo_box">
						<div class="photo_inner"><img src="<?=$_thumb?>" id="img_preview" class="img_preview" style="<?=!$_thumb?'display:none;':''?>"/></div>
					</div>
					<? if($_thumb) { ?>
					<!-- 파일삭제시 -->
					<input type="hidden" name="_thumb_OLD" value="<?=$r['b_thumb']?>"/>
					<label><input type="checkbox" name="_thumb_DEL" value="Y"> 첨부 이미지삭제</label>
					<? } ?>
					<div class="tip_txt">
						<dl>
							<dd>이미지 파일만 등록 가능합니다 (최대 2MB).</dd>
						</dl>
					</div>
				</div>
			</li>
			<? } ?>
			<!-- <li class="">
				<span class="opt">선택항목</span>
				<div class="value">
					<label><input type="radio" name="" />선택항목</label>
					<label><input type="radio" name="" />선택항목</label>
				</div>
			</li> -->
			<!-- 에디터 들어갈 자리 -->
			<li class="ess editor">
				<div class="value"><!-- 에디터 혹은 --><textarea cols="" rows="" geditor name="_content" class="textarea_design"><?=$r['b_content']?></textarea>
					<div class="tip_txt">
						<dl>
							<dt>글 등록 시 주민번호, 계좌번호와 같은 개인정보 입력은 삼가 주시기 바랍니다.</dt>
						</dl>
					</div>
				</div>
			</li>
			<?if($board_info['bi_secret_use'] == "Y") {?>
			<li class="<?=is_admin()?'double':''?>">
				<span class="opt">비밀글</span>
				<div class="value">
					<!-- <input type="password" name="" class="input_design" placeholder="숫자 혹은 영문 4글자 이상" style="width:150px" /> -->
					<label><input type="checkbox" name="_secret" value="Y" <?=$r['b_secret']=="Y" || !$r['b_secret'] ?"checked":""?>/>비밀글로 등록합니다.</label>
				</div>
			</li>
			<? } ?>
			<? if(is_admin()) { ?>
			<li class="<?=$board_info['bi_secret_use'] == "Y"?'double':''?>">
				<span class="opt">공지사항</span>
				<div class="value">
					<label><input type="checkbox" name="_notice" value="Y" <?=$r['b_notice']=="Y"?"checked":""?>/>공지사항으로 등록합니다.</label>
				</div>
			</li>
			<? } ?>
			<? require_once dirname(__FILE__)."/inc.recaptcha.php"; ?>
		</ul>
	</div><!-- .cm_board_form -->

	<? if( !is_login() && $_mode != 'modify' ) { ?>
	<!-- 비회원일경우 약관동의하기 필요하면 사용 -->
	<div class="cm_step_agree">
		<div class="text_box"><textarea cols="" rows="" name="" readonly><?=stripslashes($row_company['privacyinfo2'])?></textarea></div>
		<div class="agree_check">
		<label><input type="checkbox" name="order_agree" id="order_agree" class="" value="Y" /> 위 방침을 읽고 동의합니다.</label>
		</div>
	</div>
	<!-- / 동의하기 -->
	<? } ?>

	<!-- 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><a href="<?=$_GET['_PVSC']?"/m/?".enc('d',$_PVSC):"/m/?pn=board.list&_menu=".$_menu?>" class="btn_lg_black">취소<span class="edge"></span></a></span></li>
			<li><span class="button_pack"><a href="#none" onclick="return false;" id="board_submit" class="btn_lg_color">작성완료<span class="edge"></span></a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->
	</form>

	<?=$board_info['bi_html_footer']?>
</div><!-- .common_inner -->
</div><!-- .common_page -->


<link rel="stylesheet" href="/include/js/jquery/jqueryui/jquery-ui.min.css" type="text/css">
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
$(function() {
	$("#_sdate").datepicker({ changeMonth: true, changeYear: true });
	$("#_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
	$("#_sdate").datepicker( "option",$.datepicker.regional["ko"] );

	$("#_edate").datepicker({ changeMonth: true, changeYear: true });
	$("#_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
	$("#_edate").datepicker( "option",$.datepicker.regional["ko"] );
});
$("#board_submit").click(function() {
	$("#board_form").submit();
});
$(document).ready(function(){
	$("#board_form").validate({
		ignore: "input[type=text]:hidden",
		rules: {
			<? if(!is_login()&& $_mode != 'modify'){ ?>order_agree: { required: true },<? } ?>
			<? if(!is_login()) { ?>_passwd: { required: true, minlength: 4 },<? } ?>
			_writer:	{ required : true },
			_title:		{ required : true },
			_content:	{ required : true }
		},
		messages: {
			<? if(!is_login()&& $_mode != 'modify'){ ?>order_agree: { required: "개인정보 수집방침에 동의해주시기 바랍니다." },<? } ?>
			<? if(!is_login()) { ?>_passwd: { required: "비밀번호를 입력하세요.", minlength: "비밀번호는 최소 4글자 입니다." },<? } ?>
			_writer : { required: "작성자명을 입력하세요" },
			_title :	{ required: "제목을 입력하세요." },
			_content: { required: "내용을 입력하세요." }
		},
		submitHandler : function(form) {
			form.submit();
		}
	});
});
</script>