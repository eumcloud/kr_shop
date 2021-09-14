<?
	// 로그인 체크
	member_chk();

	$listmaxcount = 20; // 미입력시 20개 출력
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$s_query = "
	from odtProductWish as pw 
	left join odtProduct as p on ( p.code=pw.pw_pcode ) 
	where pw.pw_inid='". get_userid() ."'";

	$res = _MQ(" select count(*) as cnt ".$s_query." ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$pwque = "
		select 
			pw.*, p.* ,
			(select count(*) from odtProductWish as pw2 where pw2.pw_pcode=pw.pw_pcode) as cnt_product_wish
		".$s_query."
		order by pw_uid desc limit ".$count.", ".$listmaxcount."
	";
	$pwr = _MQ_assoc($pwque);

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---

	include dirname(__FILE__)."/mypage.header.php";
?>	
<div class="common_page">
<div class="layout_fix">

	<!-- 리스트 제어버튼영역 -->
	<div class="cm_mypage_ctrl">
		<span class="btn">
			<span class="button_pack">
				<a href="#none" onclick="return false;" class="select_none btn_sm_white">선택해제</a>
				<a href="#none" onclick="return false;" class="select_all btn_sm_white">전체선택</a>
				<a href="#none" onclick="return false;" class="select_delete btn_sm_black">선택삭제</a>
			</span>
		</span>
	</div>
	<!-- // 리스트 제어버튼영역 -->

	<div class="cm_mypage_wish">
	<?
		if( count($pwr)==0 ){
	?>
	<!-- 내용없을경우 모두공통 -->
	<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">찜한 상품이 없습니다.</div></div>
	<!-- // 내용없을경우 모두공통 -->
	<? } else { ?>
	<ul>
		<? foreach($pwr as $k=>$v) { ?>
		<li id="wish_<?=$v['code']?>">
			<div class="wish_box">
				<dl>
					<dt><a href="<?=rewrite_url($v['code'])?>" class="thumb" title="<?=$v['name']?>" target="_blank"><? if($v['prolist_img']){ ?><img src="<?=replace_image(IMG_DIR_PRODUCT.$v['prolist_img'])?>" alt="<?=$v['name']?>" title="" /><? } ?></a></dt>
					<dd><!-- 2줄제한 --><a href="<?=rewrite_url($v['code'])?>" class="title" title="<?=$v['name']?>" target="_blank"><?=cutstr($v['name'],36)?></a></dd>
					<dd>
						<span class="price"><?=number_format($v['price'])?>원</span>
						<label><input type="checkbox" name="_wish" value="<?=$v['code']?>"/></label><span class="button_pack"><a href="#none" onclick="return false;" data-code="<?=$v['code']?>" class="ajax_wish_del btn_sm_white">찜삭제</a></span>
					</dd>
				</dl>
			</div>
		</li>
		<? } ?>
	</ul>
	<? } ?>
	</div><!-- .cm_mypage_wish -->

	<!-- 페이지네이트 -->
	<div class="cm_paginate">	
		<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>
	<!-- // 페이지네이트 -->

</div><!-- .layout_fix -->
</div><!-- .common_page -->

<script>
//찜하기 버튼 설정
var select_all = '', code = '', loop = 0, t = '', i = 0;
$(document).ready(function(){
	$('.select_all').on('click',function(){ $('input[name=_wish]').each(function(){ $(this).prop('checked',true); }); });
	$('.select_none').on('click',function(){ $('input[name=_wish]').each(function(){ $(this).prop('checked',false); }); });
	$('.select_delete').on('click',function(){ 
		i = $('input[name=_wish]:checked').length;
		if( i < 1 ) { alert('상품을 먼저 선택하세요.'); return false; }
		$('input[name=_wish]:checked').each(function(index,element){ code = $(this).val(); t = 'all'; loop = index; wish_delete(); });
	});
	$('body').delegate('.ajax_wish_del','click',function(){ code = $(this).data('code'); t = 'single'; wish_delete(); });
});
function wish_delete(){
	$.ajax({
		data: {'mode':'delete','code':code},
		type: 'POST',
		cache: false,
		url: '/pages/ajax.product.wish.php',
		success: function(data) {
			if(t=='single') { location.reload(); }
			if(t=='all') { if(loop+1==i) { location.reload(); } }
		}
	});
}
</script>