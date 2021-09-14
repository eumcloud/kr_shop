<? if(count($assoc) < 1) { ?>
	<? if($event_type=='product_search') { ?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts">
			<div class="no_icon"></div>
			<div class="gtxt">
				<dl>
					<dt>검색된 결과가 없습니다.</dt>
					<dd>오타가 없는 정확한 검색어인지 확인해주세요.</dd>
					<dd>보다 일반적인 검색어나 띄어쓰기를 다르게 해서 다시 검색해보세요.</dd>
					<dd>조건검색을 했다면, 해당조건이 맞지 않을 수 있으니 다른조건으로 검색해보세요.</dd>  
				</dl>
			</div>
		</div>
		<!-- // 내용없을경우 모두공통 -->
	<? } else { ?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<!-- // 내용없을경우 모두공통 -->
	<? } ?>
<?
} else {

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---

	// 아이콘 정보 배열로 추출
	$product_icon = get_product_icon_info_qry("product_name_small_icon");

	foreach($assoc as $key => $row) {

		$image				= replace_image(IMG_DIR_PRODUCT.$row['prolist_img']); // 상품이미지
		$name				= cutstr($row['name'],2000); // 상품명
		$subname			= $row['short_comment'] ? $row['short_comment'] : NULL; // 보조상품명
		$subname			= cutstr($subname,38);
		$top_icon			= $key < 3 && $hit_num_use == "Y" ? "<div class='upper_rank'><b>TOP<br/>".($key+1)."위</b></div>" : NULL; // 3개까지만 출력
		if( $row[sale_type] == 'A' ) { // 상시판매 상품
			$soldout		= $row['stock'] < 1 ? true : false;
			if( !$soldout ){ $soldout_soon = $row['stock'] < $row_setup['s_main_close_cnt'] ? true : false; } // 판매종료일 2일 남거나 재고 50개 이하일 경우
			else { $soldout_soon = false; }
		} else { // 기간판매 상품
			$soldout			= $row['stock'] < 1 || ($row['sale_enddate']." ".$row['sale_enddateh'].":".$row['sale_enddatem'].":00") < date('Y-m-d H:i:s') ? true : false;
			if( !$soldout ){ $soldout_soon = $row['sale_enddate'] <= date('Y-m-d',strtotime("+".$row_setup['s_main_close_day']." days")) || $row['stock'] < $row_setup['s_main_close_cnt'] ? true : false; } // 판매종료일 2일 남거나 재고 50개 이하일 경우
			else { $soldout_soon = false; }
		}
		$sale_percent		= $row['price_per']; // 할인율
		$price_org			= $row['price_org']; // 기존가격
		$price				= $row['price']; // 할인가격(실판매가)
		$sale_count			= $row['saleCnt']; // 판매갯수
		$app_link			= rewrite_url($row['code'], "cuid=".($_GET['cuid'] ? $_GET['cuid'] : $cuid)."&sub_cuid=".$_GET['sub_cuid']); // 상세페이지 링크
		$is_wish			= is_login() ? _MQ_result("select count(*) from odtProductWish where pw_pcode = '".$row['code']."' and pw_inid='".get_userid()."'") : 0;
		$coupon_chk			= trim($row['coupon_title'])!=''&&$row['coupon_price']>0 ? true : false;
			
		// 상품 하단 작은 아이콘
		$product_small_icon = get_product_icon_info($row);
		if($row['p_icon']) {
			$p_icon_array = explode(",",$row['p_icon']);
			foreach($product_icon as $k0 => $v0) {
				if(array_search($v0['pi_uid'],$p_icon_array) !== false)
					$product_small_icon .= "<img src='/upfiles/icon/".$v0['pi_img']."' title='".$v0['pi_title']."'> ";
			}
		}
		$product_small_icon = $product_small_icon ? $product_small_icon : NULL;

?>

<!-- 상품하나 -->
<div class="item_box">
	<? if( $soldout ) { ?>
	<!-- 품절 -->
	<div class="soldout">
		<div class="lineup">
			<span class="tit"><span class="inner">SOLDOUT</span></span>
			<div class="txt">현재 이 상품은<br /><em>품절</em> 되었습니다.</div>
		</div>
	</div>
	<? } ?>

	<a href="<?=$app_link?>" class="alink" title="<?=$row['name']?>"><img src="/pages/images/blank.gif" alt="" /></a>
	
	<!-- 상품랭킹 -->
	<!-- 1~5까지는 top_box, 그 이하는 other_box -->
	<? if($hit_num_use=='Y') { if($key<5) { ?>
		<div class="ranking top_box"><?=$key+1?></div>
	<? } else if($key<10) { ?>
		<div class="ranking other_box"><?=$key+1?></div>
	<? }} ?>

	<!-- 메인노출 상품아이콘 -->
	<div class="upper_ic">
		<?=$product_small_icon?>
	</div>
	
	<!-- 상품썸네일 -->
	<div class="thumb">
		<? if( $soldout_soon ) { ?>
		<!-- 매진임박 -->
		<div class="soldout_soon">
			<span class="lineup">
				<img src="/pages/images/ic_clock.png" alt="매진임박" />
				<span class="txt">매진임박</span>
			</span>
		</div>
		<? } ?>
		<? if( $coupon_chk ) { ?>
		<div class="upper_coup"><img src="/pages/images/coupon_list_upper.png" alt="<?=$row['coupon_title']?>" title="<?=$row['coupon_title']?>"/></div>
		<? } ?>
		<div class="img_box"><? if($row['prolist_img']){ ?><img src="<?=$image?>" alt="<?=$row['name']?>"/><? } ?></div>
	</div>
	<!-- 한줄제한 -->
	<div class="item_name"><?=$name?></div>
	<!-- 정보 -->
	<div class="info">
		<? if($row_setup['view_social_commerce']=='Y') { if($sale_percent>0) { ?>
		<div class="discount"><?=$sale_percent?><em>%</em></div>
		<? } else { ?>
		<div class="discount discount_none"></div>
		<? }} ?>
		<div class="price">
			<? if($row_setup['view_social_commerce']=='Y' && $price_org>0) { ?>
			<div class="before"><del><?=number_format($price_org)?></del><span class="kor">원</span></div>
			<? } ?>
			<div class="after"><?=number_format($price)?><span class="kor">원</span></div>
		</div>
	</div>
	
	<!-- 찜하기 및 상품아이콘 -->
	<div class="bottom">
		<? if( is_login() ) { ?>
		<!-- 찜하기 -->
		<!-- 찜완료시 btn_wish_hit 추가 -->
		<a href="#none" data-code="<?=$row['code']?>" class="btn_wish ajax_wish <?=$is_wish?'btn_wish_hit':''?>" title="찜하기">
			<img src="/pages/images/ic_wish.png" alt="찜하기" class="off"/>
			<img src="/pages/images/ic_wish_hit.png" alt="찜완료" class="hit"/>
		</a>
		<? } ?>
	</div>
</div>

<?
	}
}
?>

