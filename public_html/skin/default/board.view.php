<?
	// _mode가 없을 경우 추가를 기본으로 함
	if(!$_uid) error_msg("잘못된 접근입니다.");

	// 게시물 정보 추출
	$post_info = get_post_info($_uid);

	// htmlspecialchars 데이터 적용
	foreach($post_info as $sk=>$sv) {if($sk <> "b_content"){$post_info[$sk] = htmlspecialchars($sv);}}


	// 게시판 정보 추출
	$board_info = get_board_info($post_info['b_menu']);

	// 게시판 아이디
	$b_menu = $board_info['bi_uid'];

	// 노출여부 확인
	if($board_info['bi_view'] == "N" && !is_admin()) error_msg("해당 게시물은 비공개 상태로 볼 수는 권한이 없습니다.\\n궁금하신 점이 있으시다면\\n고객센터(". $row_company['tel'] .")로 문의주시기 바랍니다.");


	/* 보기 권한 체크 */
	$is_auth = is_admin() || $_COOKIE["auth_request_".$post_info['b_uid']] || (is_login() && get_userid() == $post_info['b_inid']) ? true : false; // 권한
	if($post_info['b_secret'] == "Y" && !$is_auth) {error_msg("다른 회원이 작성한 글입니다. 권한이 없습니다.");}
	/* 보기 권한 체크 */


	// 내용 보기 권한체크
	if($board_info['bi_auth_view'] && $board_info['bi_auth_view'] > $row_member['Mlevel']) {
		switch($board_info['bi_auth_list']) {
			case "2" :	error_msg("회원전용 게시판입니다. 로그인 하신 후 다시 확인하시기 바랍니다.");
			case "9" :	error_msg("관리자만 접근할수 있습니다.\\n궁금하신 점이 있으시다면\\n고객센터(". $row_company['tel'] .")로 문의주시기 바랍니다.");
			default  :	error_msg("접근권한이 없습니다.\\n궁금하신 점이 있으시다면\\n고객센터(". $row_company['tel'] .")로 문의주시기 바랍니다.");
		}
	}


	// 권한이 있는지 체크
	$is_auth = is_admin() || $_COOKIE["auth_request_".$post_info['b_uid']] || (is_login() && get_userid() == $post_info['b_inid']) ? true : false;

	// 2중 권한 체크 - 답글일 경우 > 부모글을 쓴 고객은 답글을 볼 수 있어야 함. - 2015-03-18
	if($post_info['b_depth'] > 1) {
		$sr = _MQ(" select b_uid , b_inid from odtBbs where b_uid = '". $post_info['b_relation'] ."' ");
		$is_auth = $_COOKIE["auth_request_".$sr['b_uid']] || (is_login() && get_userid() == $sr['b_inid']) ? true : false;
	}

	if($post_info['b_secret'] == "Y" && !$is_auth) { error_msg("해당글에 대한 권한이 없습니다."); }

	// 삭제 버튼
	if($is_auth) { $delete_event = "bbs_delete(".$post_info['b_uid'].")"; }
	else { $delete_event = "delete_auth(".$post_info['b_uid'].")"; }



	// 글정보
	$r = $post_info;
	if(!$b_menu) $b_menu = $r['b_menu'];

	// 조회수 증가
	_MQ_noreturn(" update odtBbs set b_hit = b_hit + 1 where b_uid = '".$_uid."' ");

	$_dir = dirname(__FILE__)."/../../upfiles/bbs";

	include dirname(__FILE__)."/cs.header.php";
?>
<div class="common_page">
<div class="layout_fix">

	<?=$board_info['bi_html_header']?>

	<div class="cm_board_view">

		<!-- 글제목 -->
		<div class="post_title">
			<?
			if(in_array($board_info['bi_list_type'],array('event','event_thumb')) && $r['b_notice']!='Y') {
				if($r['b_sdate'] <= date('Y-m-d') && $r['b_edate'] >= date('Y-m-d')) { $icon = "red"; $status="이벤트진행"; }
				else { $icon = "light"; $status="이벤트종료"; }
			?>
			<!-- 이벤트일때만 나옴 -->
			<span class="texticon_pack checkicon"><span class="<?=$icon?>"><?=$status?></span></span>
			<? } ?>
			<div class="txt"><?=stripslashes($r['b_title'])?></div>
		</div>

		<!-- 글 기본정보 -->
		<div class="post_info">
			<span class="one_tx"><span class="opt">작성자</span><?=$r['b_writer']?></span>
			<span class="one_tx"><span class="opt">작성일</span><?=date('Y.m.d',strtotime($r['b_rdate']))?></span>
			<span class="one_tx"><span class="opt">조회수</span><?=number_format($r['b_hit']+1)?></span>
			<? if(in_array($board_info['bi_list_type'],array('event','event_thumb')) && $r['b_notice']!='Y') { ?>
			<span class="one_tx"><span class="opt">이벤트 기간</span><span class="bar"></span><strong><?=date('Y.m.d',strtotime($r['b_sdate']))." ~ ".date('Y.m.d',strtotime($r['b_edate']))?></strong></span>
			<? } ?>
		</div>


		<!-- 글내용 -->
		<div class="post_conts editor">
		<?
			if($r['b_img1_loc'] == "top" && $r['b_img1']) {
				if( @filesize($_dir."/".$r['b_img1']) > 0 ) { echo "<p><img src='".IMG_DIR_BOARD.$r['b_img1']."' alt=''/></p>"; }
			}
			if($r['b_img2_loc'] == "top" && $r['b_img2']) {
				if( @filesize($_dir."/".$r['b_img2']) > 0 ) { echo "<p><img src='".IMG_DIR_BOARD.$r['b_img2']."' alt=''/></p>"; }
			}

			if( $r['b_thumb'] && @filesize($_dir."/".$r['b_thumb']) > 0 ) {
				echo "<p><img src='".IMG_DIR_BOARD.$r['b_thumb']."' alt=''/></p>";
			}

			echo "<div>".stripslashes($r[b_content])."</div>";

			if($r['b_img1_loc'] == "bottom" && $r['b_img1']) {
				if( @filesize($_dir."/".$r['b_img1']) > 0 ) { echo "<p><img src='".IMG_DIR_BOARD.$r['b_img1']."' alt=''/></p>"; }
			}
			if($r['b_img2_loc'] == "bottom" && $r['b_img2']) {
				if( @filesize($_dir."/".$r['b_img2']) > 0 ) { echo "<p><img src='".IMG_DIR_BOARD.$r['b_img2']."' alt=''/></p>"; }
			}
		?>
		</div>

		<?
		if( $board_info['bi_list_type']=='qna' ) { // 질문답변 게시판일 경우 답글을 여기에 출력
			$rep = _MQ_assoc(" select * from odtBbs where b_relation = '".$r['b_uid']."' order by b_uid asc ");
			if( count($rep) > 0 ) { foreach($rep as $repk=>$repv) {
		?>
		<!-- 관리자답변 -->
		<div class="admin_answer">
			<span class="admin_title">고객님 문의에 대한 답변입니다.</span>
			<span class="admin_date">답변일 : <?=date('Y.m.d',strtotime($repv['b_rdate']))?></span>
			<div class="admin_conts"><?=stripslashes($repv['b_content'])?></div>
		</div>
		<!-- / 관리자답변 -->
		<? }}} ?>

		<? if( $r['b_file'] && @filesize(dirname(__FILE__)."/../../upfiles/bbs/".$r['b_file']) > 0 ) { ?>
		<!-- 첨부파일 필요할 경우 -->
		<div class="file_down">
			<span class="opt">첨부파일</span>
			<div class="value">
				<a href="../upfiles/bbs/<?=$r['b_file']?>" class="link"><?=$r['b_file']?> (File Size : <?=number_format(round(@filesize(dirname(__FILE__)."/../../upfiles/bbs/".$r['b_file'])/1024,2))?>kb)</a>
			</div>
		</div>
		<!-- / 첨부파일 필요할 경우 -->
		<? } ?>

		<? if($board_info['bi_comment_use'] == "Y"){ include "board.view.comment.php"; } // 댓글 활성화 ?>

		<!-- 이전글 다음글 -->
		<div class="nextprev">
			<ul>
				<li class="prev">
					<?
					$prevr = _MQ(" select b_title , b_uid from odtBbs where b_menu='".$b_menu."' and b_notice!='Y' and b_uid>'".$_uid."' and b_relation=0 ORDER BY b_uid asc limit 0 , 1");
					$prev_title = count($prevr)>0 ? cutstr(stripslashes(htmlspecialchars($prevr['b_title'])),35) : "이전글이 없습니다.";
					$prev_link = count($prevr)>0 ? "/?pn=board.view&_menu=".$b_menu."&_uid=".$prevr['b_uid']."&_PVSC=".$_PVSC : "#none";
					?>
					<span class="opt pv">이전글</span>
					<div class="value"><a href="<?=$prev_link?>" <?=$prev_link=='#none'?"onclick='return false;'":""?> class="link"><?=$prev_title?></a></div>
				</li>
				<li class="next">
					<?
					$nextr = _MQ(" select b_title , b_uid from odtBbs where b_menu='".$b_menu."' and b_notice!='Y' and b_uid<'".$_uid."' and b_relation=0 ORDER BY b_uid desc limit 0 , 1");
					$next_title = count($nextr)>0 ? cutstr(stripslashes(htmlspecialchars($nextr['b_title'])),35) : "다음글이 없습니다.";
					$next_link = count($nextr)>0 ? "/?pn=board.view&_menu=".$b_menu."&_uid=".$nextr['b_uid']."&_PVSC=".$_PVSC : "#none";
					?>
					<span class="opt nx">다음글</span>
					<div class="value"><a href="<?=$next_link?>" <?=$next_link=='#none'?"onclick='return false;'":""?> class="link"><?=$next_title?></a></div>
				</li>
			</ul>
		</div>
		<!-- / 이전글 다음글 -->

	</div><!-- .cm_board_view -->

	<!-- 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><a href="/?<?=$_PVSC?enc('d',$_PVSC):"pn=board.list&_menu=".$b_menu?>" class="btn_md_white">목록으로</a></span></li>
			<?if($is_auth || $r['b_inid'] == get_userid()) {?>
			<li><span class="button_pack"><a href="/?pn=board.form&_mode=modify&_menu=<?=$r['b_menu']?>&_uid=<?=$r['b_uid']?>&_PVSC=<?=$_PVSC?>" class="btn_md_black">수정</a></span></li>
			<li><span class="button_pack"><a href="#none" onclick="<?=$delete_event?>;return false;" class="btn_md_black">삭제</a></span></li>
			<? } ?>
			<? if( !($board_info['bi_auth_reply'] && $board_info['bi_auth_reply'] > $row_member['Mlevel']) && $r['b_depth'] == 1 && in_array($board_info['bi_list_type'],array('board','qna')) ){ ?>
			<li><span class="button_pack"><a href="/?pn=board.form&_mode=reply&_menu=<?=$r['b_menu']?>&_uid=<?=$r['b_uid']?>&_PVSC=<?=$_PVSC?>" class="btn_md_color">답글쓰기</a></span></li>
			<? } ?>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->


	<?=$board_info['bi_html_footer']?>

</div><!-- .layout_fix -->
</div><!-- .common_page -->


<!-- 비밀번호입력 -->
<div class="cm_ly_pop_tp secret_ly_pop" style="width:550px;display:none;">
	<!--  레이어팝업 공통타이틀 영역 -->
	<div class="title_box">비밀글 삭제하기<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<!-- 하얀색박스공간 -->
	<div class="inner_box">
		<!-- 설명글 -->
		<div class="top_txt">
			본 게시물을 삭제하기 위해서는 비밀번호가 필요합니다.<br/><strong>글 등록 시 입력한 비밀번호를 입력해주세요</strong>
		</div>

		<!-- 폼들어가는곳 -->
		<form name="secret_frm" action="/pages/board.auth.php" method="post" target="common_frame">
		<input type="hidden" name="_uid" class="_uid" value=""/>
		<input type="hidden" name="_mode" class="_mode" value="delete"/>
		<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"/>
		<div class="form_box">
			<ul>
				<li>
					<span class="opt">비밀번호</span>
					<div class="value"><input type="password" name="passwd" class="input_design icon_password" value="" placeholder="게시물 비밀번호" /></div>
				</li>
			</ul>
		</div>
		<!-- / 폼들어가는곳 -->

		<!-- ●●●●●●●●●● 가운데정렬버튼 -->
		<div class="cm_bottom_button">
			<ul>
				<li><span class="button_pack"><a href="#none" onclick="return false;" title="" class="close btn_md_black">닫기</a></span></li>
				<li><span class="button_pack"><a href="#none" onclick="secret_submit();return false;" title="" class="btn_md_color">확인</a></span></li>
			</ul>
		</div>
		</form>
		<!-- / 가운데정렬버튼 -->
	</div>
	<!-- / 하얀색박스공간 -->
</div>
<!-- // 비밀번호입력 -->
<script>
function secret_submit() { document.secret_frm.submit(); }
function bbs_delete(buid) { if(!confirm("글을 삭제하시겠습니까?")) { return false; } location.href="/pages/board.pro.php?_mode=delete&_uid="+buid+"&_PVSC=<?=$_PVSC?>"; }
function delete_auth(uid){ $('.secret_ly_pop').lightbox_me({ centered: true, closeEsc: true, onLoad: function() { $('.secret_ly_pop input[name=passwd]').val('').focus(); $('.secret_ly_pop ._uid').val(uid); }	}); }
</script>
