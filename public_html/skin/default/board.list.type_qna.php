<div class="cm_board_normal">

<? if( count($res)==0 ) { ?>
	<!-- 내용없을경우 모두공통 -->
	<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<!-- // 내용없을경우 모두공통 -->
<? } else { ?>
	<table summary="게시판목록">

		<colgroup>
			<col width="10%"/><col width="*"/><col width="10%"/><col width="10%"/>
		</colgroup>

		<thead>
			<tr>
				<th scope="col">번호</th>
				<th scope="col">제목</th>
				<th scope="col">작성자</th>
				<th scope="col">작성일</th>
			</tr>
		</thead> 

		<tbody>
		<?

			$_dir = dirname(__FILE__)."/../../upfiles/bbs";
			foreach($res as $k=>$v) {

				// htmlspecialchars 데이터 적용
				foreach($v as $sk=>$sv) {$v[$sk] = htmlspecialchars($sv);}

				$_num = $TotalCount - $count - $k; // 순번
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

				if($v['b_sdate'] <= date('Y-m-d') && $v['b_edate'] >= date('Y-m-d')) { $icon = "red"; $status="이벤트진행"; }
				else { $icon = "light"; $status="이벤트종료"; }

				/* 답변여부 체크 */
				$reply_r = _MQ_result("select count(*) from odtBbs where b_depth > '1' and b_relation = '".$v['b_uid']."'");
				$icon = "dark"; $status = "답변대기"; if( $reply_r > 0 ) { $icon = "red"; $status = "답변완료"; }
		?>
			<? if($is_notice) { ?>
			<!-- 공지사항 체크 -->
			<tr class="notice">
				<td><!-- 공지사항 --><span class="texticon_pack"><span class="dark">공지</span></span></td>
				<td class="title" colspan="3"><a href="#none" onclick="<?=$onclick_event?>;return false;" class="link" title="<?= $app_title ?>"><?=cutstr( $app_title ,75)?></a></td>
			</tr>
			<!-- // 공지사항 체크 -->
			<? } else { ?>
			<tr>
				<td><?=number_format($_num)?></td>
				<td class="title">
					<span class="texticon_pack"><span class="<?=$icon?>"><?=$status?></span></span>
					<a href="#none" onclick="<?=$onclick_event?>;return false;" class="link" title="<?= $app_title ?>">
						<!-- <span class="category">[일반사항]</span> --><?=$_title?></a>
					<!-- 게시물에 관한 아이콘 (필요에 따라 사용) -->
					<? if($is_new) { ?><span class="icon"><img src="/pages/images/cm_images/board_ic_new.gif" alt="새글" /></span><? } ?>
					<? if($is_secret) { ?><span class="icon"><img src="/pages/images/cm_images/board_ic_secret.gif" alt="비밀글" /></span><? } ?>
					<? if($is_file) { ?><span class="icon"><img src="/pages/images/cm_images/board_ic_file.gif" alt="파일첨부" /></span><? } ?>
					<!-- <span class="icon"><img src="/pages/images/cm_images/board_ic_photo.gif" alt="이미지첨부" /></span> -->
					<? if($comment_cnt) { ?><span class="icon"><img src="/pages/images/cm_images/board_ic_renum.gif" alt="댓글수" /><span class="countnum"><?=number_format($v['b_talkcnt'])?></span></span><? } ?>
				</td>
				<td><?=$v['b_writer']?></td>
				<td><?=date('Y.m.d',strtotime($v['b_rdate']))?></td>
			</tr>
			<? } ?>
		<? } ?>
		</tbody>
	</table>
<? } ?>

</div><!-- .cm_board_normal -->

<!-- 페이지네이트 -->
<div class="cm_paginate">
	<?if($board_info['bi_uid']!='notice' && (is_admin() || $board_info['bi_auth_write']==0 || (is_login() && ($board_info['bi_auth_write'] <= $row_member['Mlevel'])))) {?>
	<!-- 버튼 있을 경우 -->
	<div class="btn_area"><span class="button_pack"><a href="/?pn=board.form&_menu=<?=$_menu?>" class="btn_md_color">글쓰기</a></span></div>
	<? } ?>
	<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
</div>
<!-- // 페이지네이트 -->

<!-- 게시판목록 하단검색 -->
<form name="bbs_search">
<input type="hidden" name="pn" value="board.list">
<input type="hidden" name="_menu" value="<?=$b_menu?>">
<div class="cm_board_search">
	<span class="lineup">
	
		<span class="check_box">
			<label><input type="checkbox" name="search_name" value="Y" <?=$_GET['search_name']=="Y"?"checked":""?>/>이름</label>
			<label><input type="checkbox" name="search_title" value="Y" <?=(!$_GET['search_name']&&!$_GET['search_title']&&!$_GET['search_content'])||$_GET['search_title']=="Y"?"checked":""?>/>제목</label>
			<label><input type="checkbox" name="search_content" value="Y" <?=$_GET['search_content']=="Y"?"checked":""?>/>내용</label>
		</span>
		
		<input type="search" name="search_word" class="input_search" value="<?=stripslashes($_GET['search_word'])?>" />
		<input type="submit" name="" class="btn_search" value="" title="검색하기" />
		<? if($_GET['search_word']) { ?>
		<!-- 검색한 후 전체로 돌아갈때 -->
		<a href="/?pn=<?=$pn?>&_menu=<?=$_menu?>" class="btn_viewall" />검색 초기화</a>
		<? } ?>

	</span>
</div>
</form>
<!-- / 게시판목록 하단검색 -->