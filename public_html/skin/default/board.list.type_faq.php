<!-- FAQ일때 검색부분 -->
<div class="cm_faq_search">
	<div class="inner_box">

		<div class="gtxt_box">
			<div class="telnumber"><?=$row_company['tel']?></div>
			사이트 이용에 관한 궁금한 내용을 먼저 검색해보세요.<br/>
			원하는 정보가 없거나 더 자세한 내용을 원하는 분들은<br/>
			<b><a href="/?pn=mypage.request.form">1:1온라인문의</a></b>를 이용하거나, <b>고객센터</b>로 연락주십시오.
		</div>

		<div class="search_form">
			<form name="bbs_search" method="get">
			<input type="hidden" name="pn" value="board.list"/>
			<input type="hidden" name="_menu" value="<?=$b_menu?>"/>
			<div class="input_box"><input type="text" name="search_word" value="<?=stripslashes($_GET['search_word'])?>" class="input_design" placeholder="궁금한점을 간단하게 검색해 보세요." /></div>
			<input type="submit" name="" class="btn_search" title="검색하기" value="" />
			<? if($search_word) { ?>
			<!-- 검색한 후 전체로 돌아갈때 -->
			<a href="/?pn=<?=$pn?>&_menu=<?=$_menu?>" class="btn_viewall">전체보기</a>
			<? } ?>
			</form>
			<script>
			$(document).ready(function(){
				$('form[name=bbs_search]').on('submit',function(){
					var search_word = $('input[name=search_word]').val().replace(/ /g,'');
					if( search_word=='' ) { alert('검색어를 입력하세요.'); $('input[name=search_word]').focus(); return false; }
				});
			});
			</script>
		</div>

	</div>
</div>	
<!-- / FAQ일때 검색부분 -->

<!-- 카테고리있을경우 게시판메뉴 -->
<div class="cm_board_tab">
	<a href="/?pn=board.list&_menu=faq&_category=" class="tabmenu <?=!$_category?'hit':''?>">전체보기</a>
	<? foreach($arr_board_category['faq'] as $k=>$v) { ?>
	<a href="/?pn=board.list&_menu=faq&_category=<?=$v?>" class="tabmenu <?=$_category==$v?'hit':''?>"><?=$v?></a>
	<? } ?>
</div>
<!-- //카테고리있을경우 게시판메뉴 -->


<div class="cm_board_faq">
<?
// 검색 체크
$s_query = " where b_menu='".$b_menu."' ";

if(trim($_GET['search_word'])) { 
	$s_query .= " and ( ";
	$search_tmp = explode(' ',$_GET['search_word']); $s_query_array = array();
	foreach($search_tmp as $skk=>$skv) {
		$s_query_array[] = " replace(b_title,' ','') like '%".$skv."%' or replace(b_content,' ','') like '%".$skv."%' ";
	}
	$s_query .= implode(' or ',$s_query_array);
	$s_query .= " ) ";
}
if( $_GET['_category'] ) { $s_query .= " and b_category = '".$_category."' "; }

$listmaxcount = 20 ;
if( !$listpg ) {$listpg = 1 ;}
$count = $listpg * $listmaxcount - $listmaxcount;

$res = _MQ(" select count(*)  as cnt from odtBbs {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount / $listmaxcount);

$res = _MQ_assoc("select * from odtBbs {$s_query} ORDER BY  ".($_uid ? "b_uid=".$_uid." desc," : "")." b_uid desc limit $count , $listmaxcount  ");
if( count($res)==0 ) { ?><div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div><? }
else {
?>
	<ul>
<?
foreach($res as $k=>$v){

	$_num = $TotalCount - $count - $k ;
	//$v['b_title'] = stripslashes(htmlspecialchars($v['b_title']));
	//$v['b_content'] = stripslashes(htmlspecialchars($v['b_content']));

	// 검색어 하이라이트
	/*$searh_tmp = explode(' ',$_GET['search_word']); if( count($search_tmp)==0 ) { $search_tmp = array($_GET['search_word']); }
	foreach($search_tmp as $stk=>$stv) {
		$v['b_title'] = str_replace($stv,"<b style='color:#222;background-color:yellow'>".$stv."</b>",$v['b_title']);
		$v['b_content'] = str_replace($stv,"<b style='color:#222;background-color:yellow'>".$stv."</b>",$v['b_content']);
	}*/
?>
	<li class="faq_item" id="faq_<?=$v['b_uid']?>">
		<a href="#none" onclick="faq_view('<?=$v['b_uid']?>');return false;" class="link_box">
			<span class="state"><span class="state_icon">Q</span></span>
			<span class="question"><!-- <span class="category">[개인회원]</span> --><?=$v['b_title']?></span>
		</a>
		<div class="answer"><?=$v['b_content']?></div>
	</li>
<? } ?>
	</ul>
<? } ?>
</div><!-- .cm_board_faq -->


<!-- 페이지네이트 -->
<div class="cm_paginate">	
	<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
</div>
<!-- // 페이지네이트 -->




			

<script>
	var now_uid;	// 현재열려있는 uid를 저장한다.
	function faq_view(uid) {
		// uid값이 없으면 리턴.
		if(!uid) return;

		// 닫기
		$(".faq_item").removeClass("open");

		// 열려있는걸 다시 클릭했을때는 닫기만 처리한다.
		if(now_uid == uid) {this.now_uid = 0;return;}

		// 열기
		$("#faq_"+uid).addClass("open");
		this.now_uid = uid;
	}
	$(document).ready(function() {
		faq_view(<?=$_uid?>);
	});
</script>
