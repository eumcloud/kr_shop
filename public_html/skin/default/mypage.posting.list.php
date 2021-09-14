<?
	// 로그인 체크
	member_chk();

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---

	include dirname(__FILE__)."/mypage.header.php";
?>	
<div class="common_page">
<div class="layout_fix">

	<div class="cm_mypage_list list_posting">
		<?
		$listmaxcount = 20; // 미입력시 20개 출력
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
		<table summary="내가쓴글목록">
		<colgroup>
			<col width="100"/><col width="100"/><col width="*"/><col width="100"/><col width="100"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col">작성항목</th>
				<th scope="col">답변여부</th>
				<th scope="col">작성내용</th>	
				<th scope="col">보기</th>
				<th scope="col">작성일자</th>
			</tr>
		</thead> 
		<tbody>
			<?
				foreach($post_assoc as $k=>$v) {
					$p_info = _MQ("select code,name from odtProduct where code = '".$v['ttProCode']."'");
					$reply_info = _MQ_assoc("select * from odtTt where ttIsReply = '1' and ttSNo = '".$v['ttNo']."' order by ttRegidate desc");
					if(count($reply_info) > 0) { $posting_status = "<span class='red'>답변완료</span>"; }
					else { $posting_status = "<span class='light'>답변대기</span>"; }
			?>
			<tr class="toggle_target <?=$_uid==$v['ttNo']?'open_full':''?>" id="request_<?=$v['ttNo']?>">
				<td class=""><span class="texticon_pack"><span class="sky">상품문의</span></span></td>
				<td class=""><span class="texticon_pack"><?=$posting_status?></span></td>
				<td class="title">
					<!-- 상품명 : 1줄제한 -->
					<a href="<?=rewrite_url($p_info['code'])?>" class="btn_item" target="_blank" title="<?=$p_info['name']?>"><?=cutstr($p_info['name'],50)?></a>
					<!-- 내가쓴글 : 2줄제한 -->
					<a href="#none" onclick="return false;" data-uid="<?=$v['ttNo']?>" class="toggle_btn my_posting"><?=cutstr(strip_tags(stripslashes($v['ttContent'])),100)?></a>

					<!-- 내용보기되면 보이는 부분 -->
					<div class="open_conts">
						<?=nl2br(htmlspecialchars(stripslashes($v['ttContent'])))?>
						<br/><br/>

						<? foreach($reply_info as $rk=>$rv) { ?>
						<!-- 댓글 (없으면 안나옴) 이 div반복  -->
						<div class="reply">
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
					<!-- / 내용보기되면 보이는 부분 -->
				</td>					
				<td>
					<span class="button_pack btn_open_conts"><a href="#none" onclick="return false;" data-uid="<?=$v['ttNo']?>" class="toggle_btn btn_sm_white">펼쳐보기</a></span>
					<span class="button_pack btn_close_conts"><a href="#none" onclick="return false;" data-uid="<?=$v['ttNo']?>" class="toggle_btn btn_sm_black">내용닫기</a></span>
				</td>
				<td class="date"><?=date('Y.m.d',strtotime($v['ttRegidate']))?></td>	
			</tr>
			<? } ?>
		</tbody>
		</table>
		<? } ?>
	</div><!-- .cm_mypage_list -->

	<!-- 페이지네이트 -->
	<div class="cm_paginate">	
		<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>
	<!-- // 페이지네이트 -->

</div><!-- .layout_fix -->
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