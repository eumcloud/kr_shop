<div class="cm_board_event">
<? if( count($res)==0 ) { ?>
	<!-- 내용없을경우 모두공통 -->
	<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<!-- // 내용없을경우 모두공통 -->
<? } else { ?>
	<ul>
	<?
		$_dir = dirname(__FILE__)."/../../upfiles/bbs";
		foreach($res as $k=>$v) {

			// htmlspecialchars 데이터 적용
			foreach($v as $sk=>$sv) {$v[$sk] = htmlspecialchars($sv);}

			$comment_cnt = $board_info['bi_comment_use']=="Y" ? true : false; // 댓글

			/* 검색어 하이라이트 */
			$app_title = stripslashes($v['b_title']);
			$_title = cutstr( $app_title ,50);
			$searh_tmp = explode(' ',$_GET['search_word']); if( count($search_tmp)==0 ) { $search_tmp = array($_GET['search_word']); }
			foreach($search_tmp as $stk=>$stv) {
				$_title = str_replace($stv,"<b style='color:#222;background-color:yellow'>".$stv."</b>",$_title);
			}
			/* 검색어 하이라이트 끝 */

			/* 상태값 추출 */
			$is_notice = $v['b_notice']=="Y" ? true : false; // 공지사항
			$is_secret = $v['b_secret']=="Y" ? true : false; // 비밀글
			$is_file = $v['b_file']&&@filesize($_dir."/".$v['b_file'])>0 ? true : false; // 첨부파일
			$is_new = (time() - strtotime($v['b_rdate'])< (60*60*24*$board_info['bi_newicon_view'])) ? true : false; // 새글
			$is_reply = $v['b_depth']==2 ? true : false; // 답글
			$is_auth = is_admin() || $_COOKIE["auth_request_".$v['b_uid']] || (is_login() && get_userid() == $v['b_inid']) ? true : false; // 권한
			if($v['b_depth'] > 1) {
				$sr = _MQ(" select b_uid , b_inid from odtBbs where b_uid = '". $v['b_relation'] ."' ");
				$is_auth = $_COOKIE["auth_request_".$sr['b_uid']] || (is_login() && get_userid() == $sr['b_inid']) ? true : false; // 권한(답글일 경우 2중체크)
			}
			/* 상태값 추출 끝 */

			/* 링크값 생성 */
			if($v['b_secret'] == "Y") {
				if($is_auth) { $onclick_event = "location.href='/?pn=board.view&_menu=".$_menu."&_uid=" . $v['b_uid'] . "&_PVSC=".$_PVSC."'"; } 
				else { 
					if($v['b_passwd']) { $onclick_event = "secret_auth('".$v['b_uid']."')"; }
					else { $onclick_event = "alert('다른 회원이 작성한 글입니다. 권한이 없습니다.')"; }
				}
			} else {
				$onclick_event = "location.href='/?pn=board.view&_menu=".$_menu."&_uid=" . $v['b_uid'] . "&_PVSC=".$_PVSC."'";
			}
			/* 링크값 생성 끝 */

			if($v['b_sdate'] <= date('Y-m-d') && $v['b_edate'] >= date('Y-m-d')) { $icon = "red"; $status="진행중"; }
			else { $icon = "light"; $status="마감됨"; }
	?>
		<? if($is_notice) { ?>
		<!-- 공지사항 체크 -->
		<li class="notice">
			<a href="#none" onclick="<?=$onclick_event?>;return false;" class="upper_link" title="<?=$app_title?>"></a>
			<span class="texticon_pack"><span class="black">공지</span></span>
			<div class="title"><?=$app_title?></div>
		</li>
		<!-- // 공지사항 체크 -->
		<? } else { ?>
		<li class="">
			<a href="#none" onclick="<?=$onclick_event?>;return false;" class="upper_link" title="<?=$app_title?>"></a>
			<div class="title"><?=$_title?></div>
			<? if($comment_cnt) { ?><span class="comment"><?=number_format($v['b_talkcnt'])?></span><? } ?>
			<span class="date"><?=date('Y.m.d',strtotime($v['b_sdate']))." ~ ".date('Y.m.d',strtotime($v['b_edate']))?></span>
			<span class="texticon_pack checkicon"><span class="<?=$icon?>"><?=$status?></span></span>
		</li>
		<? } ?>
	<? } ?>
	</ul>
<? } ?>
</div><!-- .cm_board_event -->

<!-- 페이지네이트 -->
<div class="cm_paginate">
	<?=pagelisting_mobile($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>

	<?if($board_info['bi_uid']!='event' && (is_admin() || $board_info['bi_auth_write']==0 || (is_login() && ($board_info['bi_auth_write'] <= $row_member['Mlevel'])))) {?>
		<!-- 버튼 있을 경우 -->
		<a href="/m/?pn=board.form&_menu=<?=$_menu?>" title="글쓰기" class="cm_btn_write"></a>
	<? } ?>
</div>
<!-- // 페이지네이트 -->

<!-- 게시판목록 하단검색 -->
<form name="bbs_search" role="search" action="/m/" method="get">
<input type="hidden" name="pn" value="board.list"/>
<input type="hidden" name="_menu" value="<?=$b_menu?>"/>
<input type="hidden" name="search_name" value="Y"/>
<input type="hidden" name="search_title" value="Y"/>
<input type="hidden" name="search_content" value="Y"/>
<div class="cm_board_search">	
	<input type="search" name="search_word" class="input_search" value="<?=stripslashes($_GET['search_word'])?>" placeholder="게시물 검색" />
	<span class="right_btn">
		<input type="submit" name="" class="btn_search" value="" title="검색하기" />
		<? if($_GET['search_word']) { ?>
		<!-- 검색한 후 전체로 돌아갈때 -->
		<a href="/?pn=<?=$pn?>&_menu=<?=$_menu?>" class="btn_viewall" />검색 초기화</a>
		<? } ?>
	</span>
</div>
</form>
<!-- / 게시판목록 하단검색 -->