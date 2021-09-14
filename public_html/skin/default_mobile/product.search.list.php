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
$q_price			= "";
include dirname(__FILE__)."/ajax.product.list.php";
$cuid			= $_GET['cuid'];				// 카테고리
$q_price		= $_GET['q_price'];				// 가격대
?>
<div class="cm_comb_search">

	<!-- 검색폼 -->
	<form action="/m/" method="get" name="search_page_form" role="search">
		<input type="hidden" name="pn" value="product.search.list"/>
		<div class="search_form">
			<div class="txt">SEARCH</div>
			<!-- 지우기전에는 검색한 단어가 살아있도록 -->
			<div class="input_box">
				<input type="search" name="keyword" value="<?=$search_keyword?>" class="input_design" placeholder="검색어를 입력해주세요." />
				<button type="submit" class="btn_search">통합검색</button>
			</div>
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
		<strong>&quot;<?=$search_keyword?>&quot;</strong>에 대한 검색 결과<br/><strong>총 <?=number_format(count($assoc))?>개</strong> 상품이 검색되었습니다.
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
		<span class="txt">카테고리별 상품보기</span>		
		<!-- 상품이있는 2차 카테고리 -->
		<div class="select">
			<span class="shape"></span>
			<select name="search_category">
				<option value="" <?=!$cuid?'selected':''?>>전체보기</option>
				<? foreach($cuid_array as $k=>$v) { $category_info = get_category_info($k); ?>
				<option value="<?=$k?>" <?=$cuid==$k?'selected':''?>><?=$category_info['catename']?></option>
				<? } ?>
			</select>
		</div>
		<script>
		$(document).ready(function(){
			$('select[name=search_category]').on('change',function(){
				var cuid = $(this).val();
				if( cuid == '' ) { location.href = '/m/?pn=<?=$pn?>&keyword=<?=$keyword?>&cuid='; }
				else { location.href = '/m/?pn=<?=$pn?>&keyword=<?=$keyword?>&cuid='+cuid; }
			});
		});
		</script>		
	</div>
	<!-- / 검색된 상품이 있는 카테고리 노출 -->

	<!-- ● 조건검색 (없으면 div안보임) -->
	<form>
	<div class="condition">
		<!-- 조건검색:가격대 -->
		<? $q_price = $_GET['q_price']==''?'0':$q_price; $price_range = array('10000','50000','100000','200000','500000'); sort($price_range); ?>
		<div class="title">가격대 검색</div>
		<div class="choice_box">
			<ul>
				<li><label class="one"><input type="radio" name="search_price" value="0" <?=$q_price=='0'?'checked':''?>/><span class="txt">가격대 전체</span></label></li>
				<? foreach($price_range as $k=>$v) { ?>
				<li><label class="one"><input type="radio" name="search_price" value="<?=$v?>" <?=$q_price==$v?'checked':''?>/><span class="txt"><?=($v>9999&&$v%10000==0)?($v/10000)."만":number_format($v)?>원 이하</span></label></li>
				<? } ?>
			</ul>
		</div>
		<!-- / 조건검색:가격대 -->
		<script>
		$(document).ready(function(){
			/*setTimeout(function(){
				$('input[name=search_price]').each(function(){
					if($(this).val()=='<?=$q_price?>') { $(this).prop('checked',true); }
				});
			},300);
			<? if($q_price==0) { ?>
			setTimeout(function(){
				$('input[name=search_price]:eq(0)').prop('checked',true);
			},300);
			<? } ?>*/
			$('input[name=search_price]').on('click',function(){
				var q_price = $(this).val();
				/*if( q_price == '' ) { location.href = '/m/?pn=<?=$pn?>&keyword=<?=$keyword?>&cuid=<?=$cuid?>&q_price='; }
				else { location.href = '/m/?pn=<?=$pn?>&keyword=<?=$keyword?>&cuid=<?=$cuid?>&q_price='+q_price; }*/
				location.href = '/m/?pn=<?=$pn?>&keyword=<?=$keyword?>&cuid=<?=$cuid?>&q_price='+q_price;
			});
		});
		</script>
	</div>
	</form>
	<!-- / 조건검색 -->
	
	<? $list_type = "type1"; // 목록 유형 ?>
	<div class="item_inner">
		<div class="sub_list">

			<!-- 상단타이틀 및 정렬선택 -->
			<div class="list_top">
				<div class="list_arrange">
					<? if($list_type!='type3') { ?>
					<!-- 해당 타입일 때 type_hit 추가 -->
					<!-- 상품리스트가 여행/레저형일 때는 btn_type은 display:none -->
					<!-- 썸네일형 -->
					<a href="#none" onclick="return false;" class="btn_type <?=$list_type=='type1'?'type_hit':''?>" data-type="type1">
						<img src="/m/images/ic_thumb_off.gif" alt="" class="off"/>
						<img src="/m/images/ic_thumb_hit.gif" alt="" class="hit"/>
					</a>
					<!-- 리스트형-->
					<a href="#none" onclick="return false;" class="btn_type <?=$list_type=='type2'?'type_hit':''?>" data-type="type2">
						<img src="/m/images/ic_list_off.gif" alt="" class="off"/>
						<img src="/m/images/ic_list_hit.gif" alt="" class="hit"/>
					</a>
					<? } ?>
					<!-- <div class="ar_select" style="display:none;">
						<span class="ic_arrow"></span>
						<select name="toggleOption">
							<option value="pro_idx">추천 베스트</option>
							<option value="sale_date">신규상품</option>
							<option value="sale_enddate">마감임박</option>
						</select>
					</div> -->
				</div>
			</div>
		<script>
		// .sub_list 레이아웃 이 끝나는 곳 바로 밑에 있던 스크립트 들을 이곳으로 가져옴  2015-11-16 LCY100
		var field = '<?=$order_field?>', sort = '<?=$order_sort?>', this_type = '<?=$list_type?>';
		$(document).ready(function() {
			$('select[name=toggleOption]').on('change',function(){
				field = $(this).val(), sort = 'asc';
				if( field == 'sale_date' ) { sort = 'desc'; }

				product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>',this_type,'<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>',field,sort,'<?=$search_keyword?>','<?=$price?>');
			});
			$('.btn_type').on('click',function(){
				this_type = $(this).data('type');
				$('.btn_type').removeClass('type_hit'); $(this).addClass('type_hit');

				product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>',this_type,'<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>',field,sort,'<?=$search_keyword?>','<?=$price?>');
			});
		});
		</script>	
			<div class="<?=$display_area?>"><? include dirname(__FILE__)."/ajax.product.list.php"; ?></div>

		</div><!-- .sub_list -->


	</div>

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

	<!-- 상품리스트 들어가는 박스 -->
	<div class="item_inner">
	<?
	$list_type = "type1";
	$event_type = "search_none_suggest";
	?>
	<div class="sub_list">
		<div class="item_list_area">
			<? include dirname(__FILE__)."/ajax.product.list.php"; ?>
		</div>
	</div>
	</div>
	<!-- / 상품리스트 들어가는 박스 -->

	<? } // 검색 결과가 없을 경우 끝 ?>

</div><!-- .cm_comb_search -->