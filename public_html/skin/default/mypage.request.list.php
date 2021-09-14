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
				foreach($request_assoc as $k=>$v) {
					if($v['r_status'] == "답변완료") { $request_status = "<span class='red'>답변완료</span>"; }
					else { $request_status = "<span class='light'>답변대기</span>"; }
					if($v['r_menu'] == "request") { $request_type = "<span class='sky'>1:1문의</span>"; }
					else { $request_type = "<span class='green'>제휴/광고</span>"; }
			?>
			<tr class="toggle_target <?=$_uid==$v['r_uid']?'open_full':''?>" id="request_<?=$v['r_uid']?>">
				<td class=""><span class="texticon_pack"><?=$request_type?></span></td>
				<td class=""><span class="texticon_pack"><?=$request_status?></span></td>
				<td class="title">
					<!-- 1줄제한 -->
					<a href="#none" onclick="return false;" data-uid="<?=$v['r_uid']?>" class="toggle_btn my_posting"><?=cutstr(strip_tags(nl2br($v['r_title'])),60)?></a>

					<!-- 내용보기되면 보이는 부분 -->
					<div class="open_conts">
						<div class="conts_title"><?=strip_tags(nl2br($v['r_title']))?></div>
						<?=nl2br(htmlspecialchars(stripslashes($v['r_content'])))?>
						<? if($v['r_file']) { ?>
						<!-- 첨부파일 필요할 경우 -->
						<div class="file_down">
							<span class="opt">첨부파일</span>
							<div class="value">
								<a href="../upfiles/normal/<?=$v['r_file']?>" class="link"><?=$v['r_file']?> (File Size : 
								<?=number_format(round(@filesize(dirname(__FILE__)."/../../upfiles/normal/".$v['r_file'])/1024,2))?>kb)</a>
							</div>
						</div>
						<!-- / 첨부파일 필요할 경우 -->
						<? } ?>

						<? if($v['r_admcontent']) { ?>
						<!-- 댓글 (없으면 안나옴) 이 div반복  -->
						<div class="reply">
							<div class="conts_txt">
								<span class="admin">
									<span class="name">운영자</span><span class="bar"></span><span class="date"><?=date('Y-m-d',strtotime($v['r_admdate']))?></span>
								</span>
								<?=nl2br(htmlspecialchars(stripslashes($v['r_admcontent'])))?>
								<? if($v['r_admfile']) { ?>
								<!-- 첨부파일 필요할 경우 -->
								<div class="file_down">
									<span class="opt">첨부파일</span>
									<div class="value">
										<a href="../upfiles/normal/<?=$v['r_admfile']?>" class="link"><?=$v['r_admfile']?> (File Size : 
										<?=number_format(round(@filesize(dirname(__FILE__)."/../../upfiles/normal/".$v['r_admfile'])/1024,2))?>kb)</a>
									</div>
								</div>
								<!-- / 첨부파일 필요할 경우 -->
								<? } ?>
							</div>
						</div>
						<!-- / 댓글 -->
						<? } ?>
					</div>
					<!-- / 내용보기되면 보이는 부분 -->
				</td>					
				<td>
					<span class="button_pack btn_open_conts"><a href="#none" onclick="return false;" data-uid="<?=$v['r_uid']?>" class="toggle_btn btn_sm_white">펼쳐보기</a></span>
					<span class="button_pack btn_close_conts"><a href="#none" onclick="return false;" data-uid="<?=$v['r_uid']?>" class="toggle_btn btn_sm_black">내용닫기</a></span>
				</td>
				<td class="date"><?=date('Y.m.d',strtotime($v['r_rdate']))?></td>	
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