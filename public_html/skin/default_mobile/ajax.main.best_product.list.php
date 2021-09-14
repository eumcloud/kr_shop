<?php
	include dirname(__FILE__)."/../../include/inc.php";

	$listmaxcount = 4; // 한페이지당 출력될 상품수

	$s_query = "";
	$s_order = " order by saleCnt desc, pro_idx asc  ";

	if($cuid) {
		// 카테고리 정보
		$category_info = get_category_info($cuid);	

		if( $category_info['catedepth'] == 3 ) { 
			$s_query .= " and (select count(*) from odtProductCategory as pct where pct.pct_pcode=p.code and pct.pct_cuid='".$cuid."') > 0 "; 
		}
		else { 
			$s_query .= " 
				and (
					select 
						count(*)
					from odtProductCategory as pct 
					left join odtCategory as c on (c.catecode = pct.pct_cuid)
					where 
						pct.pct_pcode=p.code and 
						find_in_set('" . $cuid . "' , c.parent_catecode)>0
				) > 0 
			"; 
		}
	}

	$s_query .= " and if(sale_type = 'T', (CURDATE() BETWEEN sale_date and sale_enddate), sale_type = 'A')";
	$s_limit = " limit 0, ".$listmaxcount;
	$assoc = _MQ_assoc("select * from odtProduct as p where p_view='Y' and bestview='Y' ".$s_query . $s_order . $s_limit);

	$product_icon = get_product_icon_info_qry("product_name_small_icon");

	foreach($assoc as $key => $row) {

		$image				= replace_image(IMG_DIR_PRODUCT.$row['prolist_img']); // 상품이미지
		$name				= cutstr($row['name'],17); // 상품명
		$subname			= $row['short_comment'] ? "<dd>".$row['short_comment']."</dd>" : NULL; // 보조상품명
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
		//$app_link			= "/?pn=product.view&pcode=".$row['code']."&cuid=".($_POST['cuid'] ? $_POST['cuid'] : $cuid)."&sub_cuid=".$_POST['sub_cuid']; // 상세페이지 링크
		$app_link			= rewrite_url($row['code'], "cuid=".($_POST['cuid'] ? $_POST['cuid'] : $cuid)."&sub_cuid=".$_POST['sub_cuid']); // 상세페이지 링크
		$wish_link			= is_login() ? " href='/pages/product.wish.pro.php?mode=simple&pcode=".$row['code']."' " : " onclick='login_alert()' ";	// 찜하기 링크
		$is_wish			= is_login() ? _MQ_result("select count(*) from odtProductWish where pw_pcode = '".$row['code']."' and pw_inid='".get_userid()."'") : 0;
		$wish_img			= $is_wish ? "ic_wish_on.gif" : "ic_wish_off.gif";
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
			<!-- <span class="ranking">TOP<br />1</span> -->
			<span class="upper_ic">
				<?=$product_small_icon?>
			</span>
			<? if( $coupon_chk ) { ?><span class="upper_coup"><img src="/m/images/coupon_list_upper.png" alt="" /></span><? } ?>
			<? if( $soldout_soon ) { ?><span class="soldsoon"><span class="ic_box">매진임박</span></span><? } ?>
			<div class="img_box"><? if($row['prolist_img']){ ?><img src="<?=$image?>" alt="<?=$row['name']?>"/><? } ?></div>
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
					<div class="after"><span class="num"><?=number_format($price)?></span><!-- 원 --></div>
				</div>
			</div>
		</div>
	</div>
</li>
<? } ?>
