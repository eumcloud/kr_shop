<?
if(!$_menu) error_msg("게시판 아이디가 없습니다.");

// 게시판 정보 추출
$board_info = get_board_info($_menu);

// 게시판 아이디
$b_menu = $board_info['bi_uid'];

// 노출여부 확인
if($board_info['bi_view'] == "N" && !is_admin()) { error_msg("해당 게시판은 비공개 상태로 볼 수는 권한이 없습니다.\\n궁금하신 점이 있으시다면\\n고객센터(". $row_company['tel'] .")로 문의주시기 바랍니다."); }

// 권한체크
if($board_info['bi_auth_list'] && $board_info['bi_auth_list'] > $row_member['Mlevel']) {
	switch($board_info['bi_auth_list']) {
		case "2" :	error_msg("회원전용 게시판입니다. 로그인 하신 후 다시 확인하시기 바랍니다.");
		case "9" :	error_msg("관리자만 접근할수 있습니다.\\n궁금하신 점이 있으시다면\\n고객센터(". $row_company['tel'] .")로 문의주시기 바랍니다.");
		default  :	error_msg("접근권한이 없습니다.\\n궁금하신 점이 있으시다면\\n고객센터(". $row_company['tel'] .")로 문의주시기 바랍니다.");
	}
}

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---

$page_title = $board_info['bi_name'];
include dirname(__FILE__)."/cs.header.php";
?>
<div class="common_page">
<div class="common_inner common_full">

	<?
		echo $board_info['bi_html_header'];

		// 검색 체크
		$s_query = " where b_menu='".$b_menu."' ";

		if($_GET['search_word']) {
			$s_query .= " and ( ";
			$search_tmp = explode(' ',$_GET['search_word']); $s_query_array = array();
			foreach($search_tmp as $skk=>$skv) { $s_query_array[] = " replace(b_writer,' ','') like '%".trim($skv)."%' "; }
			foreach($search_tmp as $skk=>$skv) { $s_query_array[] = " replace(b_title,' ','') like '%".trim($skv)."%' "; }
			foreach($search_tmp as $skk=>$skv) { $s_query_array[] = " replace(b_content,' ','') like '%".trim($skv)."%' "; }
			$s_query .= implode(' or ',$s_query_array);
			$s_query .= ") ";
			$s_query .= " and ( IF(b_secret = 'Y' and b_writer_type = 'guest', 1 , 0) = 0 ) ";
			if( is_login() ) { $s_query .= " and ( IF(b_secret = 'Y' and b_writer_type = 'member', b_inid, '".get_userid()."') = '".get_userid()."' ) "; }
		}
		if( $_GET['_category'] ) { $s_query .= " and b_category = '".$_category."' "; }

		$listmaxcount = $board_info['bi_listmaxcnt'] ? $board_info['bi_listmaxcnt'] : 20;	// 미입력시 20개 출력.
		if( !$listpg ) {$listpg = 1 ;}
		$count = $listpg * $listmaxcount - $listmaxcount;

		$res = _MQ(" select count(*)  as cnt from odtBbs ".$s_query." ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		// 질문답변 게시판일 경우 답글 숨김 (글보기시 답변내용으로 노출)
		if( $board_info['bi_list_type']=='qna' ) {
			$res = _MQ_assoc("
				select
					*
				from odtBbs ".$s_query." and b_depth = 1
				ORDER BY b_notice='Y' desc , b_uid desc , b_depth asc
				limit ".$count.", ".$listmaxcount."
			");
		} else {
			$res = _MQ_assoc("
				select
					* ,
					CASE b_depth WHEN 1 THEN b_uid ELSE b_relation END as b_orderuid
				from odtBbs ".$s_query."
				ORDER BY b_notice='Y' desc , b_orderuid desc , b_depth asc
				limit ".$count.", ".$listmaxcount."
			");
		}

		// 게시판 스타일
		switch($board_info['bi_list_type']) {
			case "event"		: include dirname(__FILE__)."/board.list.type_event.php"; break;
			case "event_thumb"	: include dirname(__FILE__)."/board.list.type_event_thumb.php";	break;
			case "faq"			: include dirname(__FILE__)."/board.list.type_faq.php";	break;
			case "gallery"		: include dirname(__FILE__)."/board.list.type_gallery.php";	break;
			case "news"			: include dirname(__FILE__)."/board.list.type_news.php";	break;
			case "qna"			: include dirname(__FILE__)."/board.list.type_qna.php";	break;
			case "board"		: include dirname(__FILE__)."/board.list.type_board.php"; break;
			default				: include dirname(__FILE__)."/board.list.type_board.php"; break;

		}

		echo $board_info['bi_html_footer'];
	?>

</div><!-- .common_inner -->
</div><!-- .common_page -->


<!-- 비밀번호입력 -->
<div class="cm_ly_pop_tp secret_ly_pop" style="display:none;">
	<!--  레이어팝업 공통타이틀 영역 -->
	<div class="title_box">비밀글 열람하기</div>
	<!-- 하얀색박스공간 -->
	<div class="inner_box">
		<!-- 설명글 -->
		<div class="top_txt">
			<strong>글 등록 시 입력한 비밀번호를 입력해주세요</strong>
		</div>

		<!-- 폼들어가는곳 -->
		<form name="secret_frm" action="/pages/board.auth.php" method="post" target="common_frame">
		<input type="hidden" name="_uid" class="_uid" value=""/>
		<input type="hidden" name="_mode" class="_mode" value="view"/>
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
function secret_auth(uid){ $('.secret_ly_pop').lightbox_me({ centered: true, closeEsc: true, onLoad: function() { $('.secret_ly_pop input[name=passwd]').val('').focus(); $('.secret_ly_pop ._uid').val(uid); } }); }
</script>