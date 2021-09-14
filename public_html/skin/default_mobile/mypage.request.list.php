<?
	// 로그인 체크
	member_chk();

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---

	$page_title = "1:1상담내역";
	include dirname(__FILE__)."/mypage.header.php";
?>	
<div class="common_page">
<div class="common_inner common_full">

	<div class="cm_mypage_list list_posting">
		<?
		$listmaxcount = 10; // 미입력시 20개 출력
		if( !$listpg ) {$listpg = 1 ;}
		$count = $listpg * $listmaxcount - $listmaxcount;

		$s_query = " and r_inid ='".get_userid()."' ";

		$res = _MQ(" select count(*) as cnt from odtRequest where 1 ".$s_query." ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$request_assoc = _MQ_assoc("select * from odtRequest where 1 ".$s_query." order by r_rdate desc limit ".$count.", ".$listmaxcount." ");

		if( count($request_assoc)==0 ) {
		?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<!-- // 내용없을경우 모두공통 -->
		<? } else { ?>
		<ul>
			<?
				foreach($request_assoc as $k=>$v) {
					if($v['r_status'] == "답변완료") { $request_status = "<span class='red'>답변완료</span>"; }
					else { $request_status = "<span class='light'>답변대기</span>"; }
					if($v['r_menu'] == "request") { $request_type = "<span class='sky'>1:1문의</span>"; }
					else { $request_type = "<span class='green'>제휴/광고</span>"; }
			?>
			<li class="toggle_target <?=$_uid==$v['r_uid']?'open_full':''?>" id="request_<?=$v['r_uid']?>">
				<a href="#none" onclick="return false;" data-uid="<?=$v['r_uid']?>" class="toggle_btn upper_link"></a>
				<div class="date"><?=date('Y.m.d',strtotime($v['r_rdate']))?></div>
				<!-- 여기클릭하면 열리기 -->
				<div class="title">						
					<?=strip_tags(nl2br($v['r_title']))?>
					<span class="shape"></span>
				</div>
				<div class="double_icon">
					<span class="texticon_pack"><?=$request_type?></span>
					<span class="texticon_pack"><?=$request_status?></span>
				</div>

				<!-- 위에서 "open_full"값이 생기면 보임 -->
				<div class="open_box">
					<div class="conts_txt">
						<dl>
							<dt><?=strip_tags(nl2br($v['r_title']))?></dt>
							<dd><?=nl2br(htmlspecialchars(stripslashes($v['r_content'])))?></dd>
						</dl>
						<!-- 내글일때는 나타남  -->
						<span class="button_pack"><a href="#none" onclick="return false;" data-uid="<?=$v['r_uid']?>" class="toggle_btn btn_sm_black">내용닫기</a></span>
					</div>
					<? if($v['r_admcontent']) { ?>
					<!-- 댓글 -->
					<div class="reply">
						<span class="shape_ic"></span>
						<div class="conts_txt">
							<span class="admin">
								<span class="name">운영자</span><span class="bar"></span><span class="date"><?=date('Y-m-d',strtotime($v['r_admdate']))?></span>
							</span>
							<?=nl2br(htmlspecialchars(stripslashes($v['r_admcontent'])))?>
							<? if($v['r_admfile']) { ?>
							<!-- 첨부파일 필요할 경우 -->
							<div class="file_down">
								<?=$v['r_admfile']?> (File Size : <?=number_format(round(@filesize(dirname(__FILE__)."/../../upfiles/normal/".$v['r_admfile'])/1024,2))?>kb)
								<br/>첨부파일은 PC에서 확인 가능합니다.
							</div>
							<!-- / 첨부파일 필요할 경우 -->
							<? } ?>
						</div>
					</div>
					<!-- / 댓글 -->
					<? } ?>
				</div>
			</li>
			<? } ?>
		</ul>
		<? } ?>
	</div><!-- .cm_mypage_list -->

	<!-- 페이지네이트 -->
	<div class="cm_paginate">
		<?=pagelisting_mobile($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>
	<!-- // 페이지네이트 -->

</div><!-- .common_inner -->
</div><!-- .common_page -->

<script>
$(document).ready(function(){
	$('.toggle_btn').on('click',function(){
		var uid = $(this).data('uid');
		if( $('#request_'+uid).hasClass('open_full') ) {
			$('.toggle_target').removeClass('open_full');
		} else {
			$('.toggle_target').removeClass('open_full');
			$('#request_'+uid).addClass('open_full');
		}
	});
});
</script>