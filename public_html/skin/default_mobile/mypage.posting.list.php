<?
	// 로그인 체크
	member_chk();

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---

	$page_title = "상품문의내역";
	include dirname(__FILE__)."/mypage.header.php";
?>	
<div class="common_page">
<div class="common_inner common_full">

	<div class="cm_mypage_list list_posting">
		<?
		$listmaxcount = 10; // 미입력시 20개 출력
		if( !$listpg ) {$listpg = 1 ;}
		$count = $listpg * $listmaxcount - $listmaxcount;

		$s_query = " and ttID ='".get_userid()."' and ttIsReply != '1' ";

		$res = _MQ(" select count(*)  as cnt from odtTt where 1 ".$s_query." ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$post_assoc = _MQ_assoc(" select * from odtTt where 1 ".$s_query." order by ttRegidate desc limit ".$count.", ".$listmaxcount." ");

		if( count($post_assoc)==0 ) {
		?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<!-- // 내용없을경우 모두공통 -->
		<? } else { ?>
		<ul>
			<?
				foreach($post_assoc as $k=>$v) {
					$p_info = _MQ("select code,name from odtProduct where code = '".$v['ttProCode']."'");
					$reply_info = _MQ_assoc("select * from odtTt where ttIsReply = '1' and ttSNo = '".$v['ttNo']."' order by ttRegidate desc");
					if(count($reply_info) > 0) { $posting_status = "<span class='red'>답변완료</span>"; }
					else { $posting_status = "<span class='light'>답변대기</span>"; }
			?>
			<li class="if_relative toggle_target <?=$_uid==$v['ttNo']?'open_full':''?>" id="request_<?=$v['ttNo']?>">
				<a href="#none" onclick="return false;" data-uid="<?=$v['ttNo']?>" class="toggle_btn upper_link"></a>
				<div class="date"><?=date('Y.m.d',strtotime($v['ttRegidate']))?></div>
				<!-- 여기클릭하면 열리기 -->
				<div class="title">						
					<?=strip_tags(stripslashes($v['ttContent']))?>
					<span class="shape"></span>
				</div>
				<div class="double_icon">
					<span class="texticon_pack"><span class="sky">상품문의</span></span>
					<span class="texticon_pack"><?=$posting_status?></span>
				</div>

				<!-- 위에서 "open_full"값이 생기면 보임 -->
				<div class="open_box">
					<div class="conts_txt">
						<dl>
							<dd><?=nl2br(htmlspecialchars(stripslashes($v['ttContent'])))?></dd>
						</dl>
						<!-- 내글일때는 나타남  -->
						<span class="button_pack"><a href="#none" onclick="return false;" data-uid="<?=$v['ttNo']?>" class="toggle_btn btn_sm_black">내용닫기</a></span>
					</div>

					<? foreach($reply_info as $rk=>$rv) { ?>
					<!-- 댓글 -->
					<div class="reply">
						<span class="shape_ic"></span>
						<div class="conts_txt">
							<span class="admin">
								<span class="name">운영자</span><span class="bar"></span><span class="date"><?=date('Y-m-d',strtotime($rv['ttRegidate']))?></span>
							</span>						
							<?=nl2br(htmlspecialchars(stripslashes($rv['ttContent'])))?>
						</div>
					</div>
					<!-- / 댓글 -->
					<? } ?>

				</div>

			</li>
				<li class="if_relative">
					<!--  관련상품명바로가기 -->
					<a href="<?=rewrite_url($p_info['code'])?>" class="upper_link" target="_blank"></a>
					<div class="relative_item">
						<span class="shape"></span>
						<span class="front_txt">관련상품</span><?=$p_info['name']?>
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