<?
$display_area	= "search_product_list_area";	// 노출시킬 class 명
$listmaxcount	= "N";							// 페이지당 노출갯수
$hit_num_use	= "Y";							// 인기순위 아이콘 노출여부(1위~3위)
$list_type		= "none";						// 목록 유형
$pagenate_use	= "N";							// 페이징 사용여부
$order_field	= "pro_idx";					// 정렬 필드명
$order_sort		= "asc";						// 정렬 방식
$event_type		= "product_search";				// 이벤트 요소
$thema			= "";							// 테마 이름
$search_keyword	= trim(stripslashes(htmlspecialchars($_GET['keyword'])));				// 검색키워드
$cuid			= "";
$q_price 			= "";
include dirname(__FILE__)."/ajax.product.list.php";
$cuid			= $_GET['cuid'];				// 카테고리
$q_price			= $_GET['q_price'];				// 가격대
?>

<!-- 통합검색 -->
<div class="cm_comb_search">
	<div class="layout_fix">
		
		<!-- 검색폼 -->
		<form action="/" method="get" name="search_page_form">
			<input type="hidden" name="pn" value="product.search.list"/>
			<div class="search_form">
				<span class="lineup">
					<span class="txt">SEARCH</span>
					<!-- 지우기전에는 검색한 단어가 살아있도록 -->
					<input type="text" name="keyword" value="<?=$search_keyword?>" class="input_design" id="search_page_input" placeholder="검색어를 입력해주세요." />
					<button type="submit" class="btn_search">통합검색</button>
				</span>
			</div>
		</form>
		<script>
		$(document).ready(function(){
			$('form[name=search_page_form]').on('submit',function(){
				if( $('#search_page_input').val() == '' ) { alert('검색어를 입력하세요.'); $('#search_page_input').focus(); return false; }
			});
		});
		</script>
		<!-- / 검색폼 -->

		<? if( count($assoc) > 0 ) { // 검색결과가 있을 경우 ?>
		<!-- 검색결과 (없으면 div안보임) -->
		<div class="search_result">
			<strong>&quot;<?=$search_keyword?>&quot;</strong>에 관한 검색 결과, <strong>총 <?=number_format(count($assoc))?>개</strong> 상품이 검색되었습니다.
		</div>
		<!-- / 검색결과 -->

		<!-- 검색된 상품이 있는 카테고리 노출 (없으면 div안보임) -->
		<?
			// 검색된 상품당 cuid 추출
			$cuid_array = array();
			foreach($assoc as $k=>$v) {
				$_uid = _MQ_assoc("select pct_cuid from odtProductCategory where pct_pcode = '".$v['code']."'");
				foreach($_uid as $kk=>$vv) {
					$_cat = _MQ(" select parent_catecode from odtCategory where catecode = '".$vv['pct_cuid']."' "); $_parent = explode(',',$_cat['parent_catecode']);
					$_category = _MQ(" select catecode, catename from odtCategory where catecode = '".$_parent[1]."' and catedepth = 2 ");
					$cuid_array[$_category['catecode']][$v['code']] += 1;
				}
			}
			arsort($cuid_array);
		?>
		<div class="search_category">
			<!-- 디자인용보더-->
			<div class="line line1"></div>
			<div class="line line2"></div>
			<div class="line line3"></div>
			<div class="line line4"></div>
			<div class="line line5"></div>
			
			<!-- 상품이있는 2차 카테고리 a반복 -->
			<a href="/?pn=<?=$pn?>&keyword=<?=$keyword?>&cuid=" class="ctg <?=!$cuid?'hit':''?>">전체보기</a>
			<? foreach($cuid_array as $k=>$v) { $category_info = get_category_info($k); ?>
			<a href="/?pn=<?=$pn?>&keyword=<?=$keyword?>&cuid=<?=$k?>" class="ctg <?=$cuid==$k?'hit':''?>"><?=$category_info['catename']?></a>
			<? } ?>
		</div>
		<!-- / 검색된 상품이 있는 카테고리 노출 -->

		<!-- ● 조건검색 (없으면 div안보임) -->
		<div class="condition">

			<!-- 조건검색:가격대 -->
			<? $price_range = array('10000','50000','100000','200000','500000'); sort($price_range); ?>
			<span class="title">가격대 검색</span>
			<div class="choice_box">
				<ul>
					<li><a href="/?pn=<?=$pn?>&keyword=<?=$keyword?>&cuid=<?=$cuid?>" class="one <?=!$q_price?'hit':''?>"><span class="icon"></span>가격대 전체</a></li>
					<? foreach($price_range as $k=>$v) { ?>
					<li><a href="/?pn=<?=$pn?>&keyword=<?=$keyword?>&cuid=<?=$cuid?>&q_price=<?=$v?>" class="one <?=$q_price==$v?'hit':''?>"><span class="icon"></span><?=($v>9999&&$v%10000==0)?($v/10000)."만":number_format($v)?>원 이하</a></li>
					<? } ?>
				</ul>
			</div>
			<!-- / 조건검색:가격대 -->

		</div>
		<!-- / 조건검색 -->
	</div>

	<!-- 상품리스트 -->
	<?
	$list_type = "type1"; // 목록 유형
	?>
	<div class="sub_list">

		<!-- 서브 탭 -->
		<div class="sub_tab" style='display:none'>
			<!-- 해당 탭일 경우 btn_hit 추가 -->
			<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>','pro_idx','asc','<?=$search_keyword?>','<?=$q_price?>');return false;" class="product_list_tab btn_tab btn_hit">
				<span class="dot d_left"></span><span class="dot d_right"></span>추천 베스트
			</a>
			<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>','sale_date','desc','<?=$search_keyword?>','<?=$q_price?>');return false;" class="product_list_tab btn_tab">
				<span class="dot d_left"></span><span class="dot d_right"></span>신규상품
			</a>
			<? if($row_setup[view_social_commerce]=='Y') { ?>
			<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>','sale_enddate','asc','<?=$search_keyword?>','<?=$q_price?>');return false;" class="product_list_tab btn_tab">
				<span class="dot d_left"></span><span class="dot d_right"></span>마감임박
			</a>
			<? } ?>
			<!-- 개발 가능한지 문의 후 작업 -->
			<!-- <a href="" class="btn_tab">
				<span class="dot d_left"></span>
				<span class="dot d_right"></span>
				바로사용
			</a> -->
		</div>
		<div class="item_list_area">
			<div class="layout_fix <?=$display_area?>">
				<? include dirname(__FILE__)."/ajax.product.list.php"; ?>
			</div>
		</div>
	</div>
	<script>
	$(document).ready(function(){
		$(".product_list_tab").on('click',function() {
			$(".product_list_tab").removeClass("btn_hit");
			$(this).addClass("btn_hit");
		});
	});
	</script>
	<? } else { // 검색 결과가 없을 경우 ?>
	<!-- 내용없을경우 모두공통 -->
	<div class="cm_no_conts">
		<div class="no_icon"></div>
		<div class="gtxt">
			<dl>
				<dt>입력하신 단어로 검색된 결과가 없습니다.</dt>
				<dd>오타가 없는 정확한 검색어인지 확인해주세요.</dd>
				<dd>보다 일반적인 검색어나 띄어쓰기를 다르게 해서 다시 검색해보세요.</dd>
				<dd>조건검색을 했다면, 해당조건이 맞지 않을 수 있으니 다른조건으로 검색해보세요.</dd>  
			</dl>
		</div>
	</div>
	<!-- // 내용없을경우 모두공통 -->

	<!-- 타이틀 -->
	<div class="group_title">
		<span class="txt_box">다른 고객이 많이 찾은 상품</span>
	</div>
	<!-- / 타이틀 -->
	</div>
		
	<?
	$list_type = "type1";
	$event_type = "search_none_suggest";
	?>
	<div class="sub_list">
		<div class="item_list_area">
			<div class="layout_fix">
				<? include dirname(__FILE__)."/ajax.product.list.php"; ?>
			</div>
		</div>
	</div>
	<? } // 검색 결과가 없을 경우  ?>
</div>