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

	echo "<div class='list_thumb'><ul>";
	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---

	// 아이콘 정보 배열로 추출
	$product_icon = get_product_icon_info_qry("product_name_small_icon");

	foreach($assoc as $key => $row) {

		$image				= replace_image(IMG_DIR_PRODUCT.$row['prolist_img']); // 상품이미지
		$name				= cutstr($row['name'],20); // 상품명
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
		//$app_link			= "/m/?pn=product.view&pcode=".$row['code']."&cuid=".($_GET['cuid'] ? $_GET['cuid'] : $cuid)."&sub_cuid=".$_GET['sub_cuid']; // 상세페이지 링크
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
<li>
	<div class="item_box">
		<? if( $soldout ) { ?>
		<!-- 품절 -->
		<div class="soldout">
			<div class="lineup">
				<span class="tit">SOLDOUT</span>
				<div class="txt">현재 이 상품은<br /><em>품절</em> 되었습니다.</div>
			</div>
		</div>
		<? } ?>
		<!-- 상품링크 여기에 -->
		<a href="<?=$app_link?>" class="alink"></a>
		<!-- 썸네일 -->
		<div class="thumb">
			<? if($hit_num_use=='Y') { if($key<5) { ?>
			<span class="ranking">TOP<br/><?=$key+1?></span>
			<? }} ?>
			<span class="upper_ic">
				<?=$product_small_icon?>
			</span>
			<? if( $coupon_chk ) { ?><span class="upper_coup"><img src="/m/images/coupon_list_upper.png" alt="" /></span><? } ?>
			<? if( $soldout_soon ) { ?><span class="soldsoon"><span class="ic_box">매진임박</span></span><? } ?>
			<div class="img_box" style="<?=$row['v_color']?'background-color:#'.$row['v_color']:''?>"><? if($row['prolist_img']){ ?><img class="lazy" data-original="<?=$image?>" alt="<?=$row['name']?>"/><? } ?></div>
		</div>
		<!-- 상품정보 -->
		<div class="info">
			<div class="tit"><?=$row['name']?></div>
			<div class="price_box">
				<? if($row_setup['view_social_commerce']=='Y') { if($sale_percent>0) { ?>
				<div class="discount"><strong><?=$sale_percent?></strong>%</div>
				<? } else { ?>
				<div class="discount discount_none"></div>
				<? }} ?>
				<div class="price">
					<? if($row_setup['view_social_commerce']=='Y' && $price_org>0) { ?>
					<div class="before"><del><?=number_format($price_org)?></del></div>
					<? } ?>
					<div class="after"><span class="num"><?=number_format($price)?></span></div>
				</div>
			</div>
		</div>
	</div>
</li>
<?
	} echo "</ul></div>";
}
?>