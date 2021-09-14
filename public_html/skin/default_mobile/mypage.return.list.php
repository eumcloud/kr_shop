<?
	// 로그인 체크
	member_chk();

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---

	$page_title = "교환/반품내역";
	include dirname(__FILE__)."/mypage.header.php";
?>	
<div class="common_page">
<div class="common_inner common_full">

	<div class="cm_mypage_list list_posting">
		<?
		$listmaxcount = 20; // 미입력시 20개 출력
		if( !$listpg ) {$listpg = 1 ;}
		$count = $listpg * $listmaxcount - $listmaxcount;

		$s_query = " and rr_member ='".get_userid()."' ";

		$res = _MQ(" select count(*) as cnt from odtRequestReturn where 1 ".$s_query." ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$request_assoc = _MQ_assoc("select * from odtRequestReturn where 1 ".$s_query." order by rr_rdate desc limit ".$count.", ".$listmaxcount." ");

		if( count($request_assoc)==0 ) {
		?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<!-- // 내용없을경우 모두공통 -->
		<? } else { ?>
		<ul>
			<?
				foreach($request_assoc as $k=>$v) {
					switch($v['rr_status']) {
						case "Y": $request_status = "<span class='red'>".$arr_return_status[$v['rr_status']]."</span>"; break;
						case "N": $request_status = "<span class='dark'>".$arr_return_status[$v['rr_status']]."</span>"; break;
						case "R": $request_status = "<span class='light'>".$arr_return_status[$v['rr_status']]."</span>"; break;
					}
					switch($v['rr_type']) {
						case "R": $request_type = "<span class='green'>".$arr_return_type[$v['rr_type']]."</span>"; break;
						case "E": $request_type = "<span class='sky'>".$arr_return_type[$v['rr_type']]."</span>"; break;
					}

					// 상품정보 추출
					$option_res = _MQ_assoc(" select * from odtOrderProduct where op_oordernum = '".$v['rr_ordernum']."' and op_uid in ('".$v['rr_opuid']."') order by op_uid asc ");
					$option_info = $option_res[0]; $option_name = $option_info['op_pname'];
					$option_name .= count($option_res) > 1 ? " 외 ".number_format(count($option_res)-1)." 건" : "";
			?>
			<li class="toggle_target">
				<a href="/m/?pn=mypage.return.view&uid=<?=$v['rr_uid']?>&_PVSC=<?=$_PVSC?>" class="upper_link"></a>
				<div class="date"><?=date('Y.m.d',strtotime($v['rr_rdate']))?></div>
				<!-- 여기클릭하면 열리기 -->
				<div class="title">
					<?=strip_tags($option_name)?>
					<span class="shape"></span>
				</div>
				<div class="double_icon">
					<span class="texticon_pack"><?=$request_type?></span>
					<span class="texticon_pack"><?=$request_status?></span>
				</div>
			</li>
			<? } ?>
		</ul>
		<? } ?>
	</div><!-- .cm_mypage_list -->

	<!-- 페이지네이트 -->
	<div class="cm_paginate">
		<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>
	<!-- // 페이지네이트 -->

</div><!-- .layout_fix -->
</div><!-- .common_page -->