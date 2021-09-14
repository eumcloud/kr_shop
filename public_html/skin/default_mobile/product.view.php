<?php
// 상품코드 체크
if(!$_GET[pcode]) error_msg("상품 코드가 없습니다.");

// - 임시 옵션 삭제 ---
if($_COOKIE["AuthShopCOOKIEID"]) _MQ_noreturn("delete from odtTmpProductOption where otpo_mid='". $_COOKIE["AuthShopCOOKIEID"] ."'");

// 상품정보 추출
$row_product = get_product_info(${pcode});

// 숨김 체크
if($row_product[p_view]!='Y') { error_msg('판매중인 상품이 아닙니다.'); }

// 상품 수량 체크
if($row_product[stock] < 1) error_msg("품절된 상품입니다.");

// 판매기간 시작 체크
if($row_product[sale_date] > date('Y-m-d') && $row_product['sale_type'] == 'T') error_msg("판매 시작전 상품입니다.");

// 판매기간 종료 체크
if($row_product[sale_enddate] < date('Y-m-d') && $row_product['sale_type'] == 'T') error_msg("판매종료된 상품입니다.");

// 타이틀 - sns 적용
$app_sns_title = str_replace("'","`",$row_product[name]);

// facebook app id
$app_sns_facebook_id = $row_setup[Facebook_id];

// 타이틀 - sns 적용
$app_sns_title = str_replace("'","`",$row_product[name]);

// URL - sns 적용
$app_sns_url = "http://".$_SERVER[HTTP_HOST]."/?pcode=" . $row_product[code];

// 매장설명 - sns 적용
$row_product[short_comment] = rm_enter($row_product[short_comment]);
$app_sns_content = cutstr(str_replace("\t"," ",str_replace("\n"," ",str_replace("'","`",$row_product[short_comment]))) , 30 , "..");

// 매장 로고
$banner_info = info_banner("site_icon_basic",1,"data");
$app_sns_logo = replace_image(IMG_DIR_BANNER.$banner_info[0]['b_img']);

// 상품 이미지
if($row_product['prolist_img2'])
	$app_sns_pro_img = replace_image(IMG_DIR_PRODUCT.$row_product['prolist_img2']);
else
	$app_sns_pro_img = "";



$row_customer = _MQ("select * from odtMember where id = '".$row_product[customerCode]."' and userType ='C'");

// 배송비 정책
if($row_product['setup_delivery'] == 'Y') { // 배송비 조건이 있을경우에만
	switch($row_product['del_type']){
		case "unit":$delivery_info = "개별배송 <em>".number_format($row_product['del_price'])."원</em>"; break;
		case "free": $delivery_info = "무료배송"; break;
		case "normal":
			if($row_customer['com_delprice'] <> 0){
				$delivery_info = "배송비 <em>".number_format($row_customer['com_delprice'])."원</em>";
				if($row_customer['com_delprice_free'] <> 0 ){
					$delivery_info .= "(".number_format($row_customer['com_delprice_free'])."원 이상 무료배송)";
				}
			}else{
				$delivery_info = "무료배송";
			}
		break;
	}
}



// 구매제한정책
if(!$row_product['buy_limit']) {
	$buy_limit_info = "구매제한없음";
} else {
	$buy_limit_info = "주문당 구매제한 : ".number_format($row_product['buy_limit'])."개 ";
}

/* ---------------------- 상품 하단 작은 아이콘 --------------------------*/
unset($product_small_icon_value,$product_small_icon_array,$product_small_icon);
/*$product_small_icon_array[today_open]     = $row_product[sale_date] == date('Y-m-d') ? "<img src='/m/images/ic_topen.gif' alt='오늘오픈' /> " : NULL;	// 오늘 오픈상품
if($row_product[setup_delivery]=='Y') { $product_small_icon_array[free_delivery]  = !$row_product[del_price] ? "<img src='/m/images/ic_free.gif' alt='무료배송' /> " : NULL;}	// 무료배송
$product_small_icon_array[now_use]        = $row_product[isNow] == "Y" ? "<img src='/m/images/ic_use.gif' alt='바로사용' /> " : NULL;	// 바로사용
$product_small_icon_array[today_end]      = $row_product[sale_enddate] == date('Y-m-d') ? "<img src='/m/images/ic_tend.gif' alt='오늘마감' /> " : NULL;	// 오늘 마감상픔
if($row_product[setup_delivery]=='Y') { $product_small_icon_array[today_delivery] = 1 ? "<img src='/m/images/ic_ship.gif' alt='오늘배송' /> " : NULL;}	// 추후작업 : 오늘배송 조건
$product_small_icon_array = array_filter($product_small_icon_array);	// 빈값 제거
foreach($product_small_icon_array as $icon_value) { $product_small_icon_value .= $icon_value; }
$product_small_icon = count($product_small_icon_array) ? $product_small_icon_value : "";	// 상품 하단 아이콘*/
/* ----------------------  // 상품 하단 작은 아이콘 --------------------------*/

// 아이콘 정보 배열로 추출
$product_icon = get_product_icon_info_qry("product_name_small_icon");

// 상품 하단 작은 아이콘
$product_small_icon = get_product_icon_info($row_product);
if($row_product['p_icon']) {
	$p_icon_array = explode(",",$row_product['p_icon']);
	foreach($product_icon as $k0 => $v0) {
		if(array_search($v0['pi_uid'],$p_icon_array) !== false)
			$product_small_icon .= "<img src='/upfiles/icon/".$v0['pi_img']."' title='".$v0['pi_title']."'/> ";
	}
}
$product_small_icon = $product_small_icon ? $product_small_icon : NULL;

// text 값 추출
$row_product = array_merge($row_product , _text_info_extraction( "odtProduct" , $row_product[serialnum] ));

// 좌표값
$row_product[coordinate] = $row_product[com_mapx].", ".$row_product[com_mapy];


// 찜하기 체크
$is_wish = is_login() ? _MQ_result("select count(*) from odtProductWish where pw_pcode = '".$row_product['code']."' and pw_inid='".get_userid()."'") : 0;

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---

// SNS 공유를 위한 변수 생성
$sns_url = rewrite_url($pcode);
$sns_fullurl = "http://".$_SERVER['HTTP_HOST']."/".$pcode;
$sns_url = $sns_url ? $sns_url : $sns_fullurl;
$sns_name = $row_product['name']." ".number_format($row_product['price'])."원";
//$sns_image = "http://".$_SERVER['HTTP_HOST'].IMG_DIR_PRODUCT.$row_product['main_img'];
$sns_image = replace_image(IMG_DIR_PRODUCT.$row_product['main_img']);
$sns_desc = trim(str_replace("  "," ",str_replace(":","-",str_replace("\t"," ",str_replace("\r"," ",str_replace("\n"," ",str_replace("'","`",stripslashes(($row_product['hort_comment']?$row_product['short_comment']." - ":"") .$row_company['homepage_title']))))))));

?>
<!-- 상품썸네일 -->
<div class="product_thumb">
	<div class="thumb_box">
		<div class="lineup">
			<div class="thumb_img">
				<span class="upper_ic">
					<?=$product_small_icon?>
				</span>
				<span class="img_box"><? if($row_product['main_img']) { ?><img src="<?=replace_image(IMG_DIR_PRODUCT.$row_product['main_img'])?>" alt="<?=$row_product['name']?>"><? } ?></span>
			</div>
		</div>
	</div>
</div>

<!-- 상품간략정보 -->
<div class="product_info">
	<div class="item_name"><?=$row_product['name']?></div>
	<div class="priceinfo">
		<? if($row_setup['view_social_commerce']=='Y') { if($row_product['price_per']>0) { ?>
		<div class="discount"><span class="num"><?=$row_product['price_per']?></span>%</div>
		<? } else { ?>
		<div class="discount discount_none"></div>
		<? }} ?>
		<div class="price">
			<? if($row_setup['view_social_commerce']=='Y') { ?>
			<span class="before"><del><?=$row_product['price_org']>0?number_format($row_product['price_org']):''?></del></span>
			<? } ?>
			<span class="after"><strong><?=number_format($row_product['price'])?></strong>원</span>
		</div>
	</div>
	<div class="nowtotal">
	<? if($row_setup['view_social_commerce']=='Y' && $row_product['sale_type'] == 'T') { ?>
		<span class="buynow">현재<span class="num"><?=number_format($row_product['saleCnt'])>0?number_format($row_product['saleCnt']):0?></span>개 구매중</span>
		<span class="timer">남은시간<span class="num" id="remainDay">00</span>일<span class="num" id="remainHour">00</span>:<span class="num" id="remainMin">00</span>:<span class="num" id="remainSec">00</span></span>
	<? } ?>
	</div>
</div>

<? if($row_product['coupon_title'] && $row_product['coupon_price']>0 ){ ?>
<!-- 제품상세 쿠폰정보 -->
<div class="product_coup">

	<div class="coupon_view_icon">
		<div class="white_box">
			<span class="coupon_ti">적용가능쿠폰</span>
			<span class="coupon_name"><?=stripslashes($row_product['coupon_title'])?></span>
		</div>
		<div class="color_box">
			<span class="edge1"></span><span class="edge2"></span>
			<span class="coupon_discount"><?=$row_product['coupon_price']?>%</span>
			<span class="coupon_price">(<strong><?=number_format(floor($row_product['price']*$row_product['coupon_price']/100))?></strong>원 할인)</span>
		</div>
	</div>

</div>
<? } ?>

<!-- 옵션선택 및 가격 -->
<div class="product_opt">
	<!-- 옵션선택 -->
	<div class="view_opt">
	<? $no_option = false;
		function addoption_print($row_code){
			GLOBAL $row_product; if($row_product['setup_delivery']=='Y') {
			$sque = " select oto_uid , oto_poptionname, oto_cnt,oto_poptionprice, MAX(oto_depth) as oto_depth_max from odtProductOption where oto_pcode='" . $row_code . "'";
			$sres_tmp = _MQ_assoc($sque);

			if($sres_tmp[0][oto_depth_max]) {
			$add_options = _MQ_assoc("select * from odtProductAddoption where pao_pcode='{$row_code}' and pao_depth='1' and pao_view = 'Y' order by pao_sort asc, pao_uid asc ");
			foreach($add_options as $k=>$v) { ?>
			<li>
				<div class="pv_select">
					<span class="ic_arrow"></span>
					<select name='_add_option_select_<?=$k+1?>' id="add_option_select_<?=$k+1?>_id" class='add_option add_option_chk' onchange="add_option_select_add('<?=$row_code?>' , this.value)">
						<option value=''>추가옵션: <?=$v[pao_poptionname]?></option>
						<? $add_sub_options = _MQ_assoc("select * from odtProductAddoption where pao_pcode='{$row_code}' and pao_depth='2' and pao_parent='{$v[pao_uid]}' order by pao_uid");
						foreach($add_sub_options as $key=>$value) { ?>
						<option value="<?=$value[pao_uid]?>" data-uid="<?=$value[pao_uid]?>"><?=$value[pao_poptionname]?> (잔여:<?=($value[pao_cnt] > 0 ? number_format($value[pao_cnt])  : "품절") ?>) / <?=($value[pao_poptionprice] < 0 ? "" : "+") ?><?=number_format($value[pao_poptionprice]) ?>원</option>
						<? } ?>
					</select>
				</div>
			</li>
			<? } ?>
			<!-- 추가옵션 수량 //-->
			<input type="hidden" name="addoption_cnt" value="<?=count($add_options)?>">
		<? }}} ?>

		<?
		// 옵션정보 불러오기
		$sque = " select oto_uid , oto_poptionname, oto_cnt,oto_poptionprice  from odtProductOption where oto_pcode='" . $row_product[code] . "' and oto_depth='1' and oto_view = 'Y' order by oto_sort asc, oto_uid asc  ";
		$sres = _MQ_assoc($sque);

		// 옵션타이틀
		$option1_title = (trim($row_product[option1_title]) ? trim($row_product[option1_title]) : ($row_product[option_type_chk] == "1depth" ? "상세옵션을 선택해 주세요" : "1차옵션을 선택하세요") );
		$option2_title = (trim($row_product[option2_title]) ? trim($row_product[option2_title]) : "상위 옵션을 먼저 선택하세요");
		$option3_title = (trim($row_product[option3_title]) ? trim($row_product[option3_title]) : "상위 옵션을 먼저 선택하세요");

		foreach( $sres as $k=>$sr){
			if($row_product[option_type_chk] == "1depth") {
				$str_option .= "<option value='".$sr[oto_uid]."'>".$sr[oto_poptionname]." (잔여:".  ($sr[oto_cnt] > 0 ? number_format($sr[oto_cnt])  : "품절").") / " . ($sr[oto_poptionprice] < 0 ? "" : "+") . number_format($sr[oto_poptionprice]) . "원</option>";
			} else { $str_option .= "<option value='".$sr[oto_uid]."'>".$sr[oto_poptionname]."</option>"; }
		}
		if( $row_product[option_type_chk] == "1depth"  && count($sres) > 0){ // 1차 옵션이 있으면
		?>
		<ul><li>
		<div class="pv_select">
			<span class="ic_arrow"></span>
			<select name=_option_select1 ID='option_select1_id' onchange="option_select_add('<?=$row_product[code]?>')"><option value=''><?=$option1_title?></option><?=$str_option?></select>
		</div></li>
		<?=addoption_print($row_product[code])?>
		</ul>
		<? } else if( $row_product[option_type_chk] == "2depth"  && count($sres) > 0){ ?>
		<ul><li>
		<div class="pv_select">
			<span class="ic_arrow"></span>
			<select name=_option_select1 onchange="option_select(1,'<?=$row_product[code]?>')" ID='option_select1_id'><option value=''><?=$option1_title?></option><?=$str_option?></select>
		</div></li><li>
		<span ID='span_option2' style="display:block; margin-bottom: 3px;">
			<div class="pv_select">
			<span class="ic_arrow"></span>
			<select disabled><option value=''><?=$option2_title?></option></select>
			</div>
		</span></li>
		<?=addoption_print($row_product[code])?>
		</ul>
		<? } else if( $row_product[option_type_chk] == "3depth"  && count($sres) > 0){ ?>
		<ul><li>
		<div class="pv_select">
			<span class="ic_arrow"></span>
			<select name=_option_select1 onchange="option_select(1,'<?=$row_product[code]?>')" ID='option_select1_id'><option value=''><?=$option1_title?></option><?=$str_option?></select>
		</div></li><li>
		<span ID='span_option2' style="display:block; margin-bottom: 3px;">
			<div class="pv_select">
			<span class="ic_arrow"><span class="icon"></span></span>
			<select disabled><option value=''><?=$option2_title?></option></select>
			</div>
		</span></li><li>
		<span ID='span_option3' style="display:block; margin-bottom: 3px;">
			<div class="pv_select">
			<span class="ic_arrow"><span class="icon"></span></span>
			<select disabled><option value=''><?=$option3_title?></option></select>
			</div>
		</span></li>
		<?=addoption_print($row_product[code])?>
		</ul>
		<? } else { $no_option = true; // 옵션이 없으면 ?>
		<input type="hidden" name="option_select_expricesum" ID="option_select_expricesum" value="<?=$row_product['price']?>">
		<input type="hidden" name="option_select_type" id="option_select_type" value="nooption">
		<input type="hidden" name="product_stock" id="product_stock" value="<?=$row_product['stock']?>">
		<script>$(document).ready(function() { update_sum_price(); });</script>
		<? } ?>
	</div><!-- .view_opt -->
	<!-- // 옵션선택 -->

	<div class="view_price">
		<? if($no_option == true) { ?>
		<ul>
			<li>
				<? if($row_product['stock'] > 0) { ?>
				<div class="opt_none">구매수량</div>
				<div class="btn_updown_num">
					<a href="#none" onclick="pro_cnt_down('');return false;" class="btn_minus"></a>
					<input type="text" name="option_select_cnt" value="1" ID="option_select_cnt" readonly/>
					<a href="#none" onclick="pro_cnt_up(<?=$row_product['buy_limit']?>);return false;" class="btn_plus"></a>
				</div>
				<? } ?>
			</li>
		</ul>
		<? } else { ?>
		<div ID='span_seleced_list' class='opt_list'></div>
		<? } ?>
	</div><!-- .view_price -->

	<!-- 토탈금액 -->
	<div class="view_total">
		<div class="total_wrap">
			<div class="text">상품금액</div>
			<div class="total_price">
				<span id="option_select_expricesum_display" style="font-size:inherit;color:inherit;font-family:inherit;font-weight:inherit;">0</span><em>원</em>
			</div>
		</div>
	</div>

	<!-- 버튼영역 -->
	<?php
	// LDD019
	if($row_setup['none_member_buy'] == 'Y' || is_login()) { // 비회원 구매 가능 이거나 로그인 상태의 경우

		$BuyBT = 'app_submit(\''.$pcode.'\',\'order\'); return false;';
		$CartBT = 'app_submit(\''.$pcode.'\',\'cart\'); return false;';
	}
	else { // 비회원 구매 불가능

		$CartBT = $BuyBT = 'login_alert(\''.$_PVSC.'\'); return false;';
	}

	# 재고부족
	if($row_product['stock'] <= 0) {

		$CartBT = $BuyBT = 'app_soldout(); return false;';
	}
	?>
	<div class="product_btn">
		<div class="btn_left">
			<span class="inner"><a href="#none" onclick="<?php echo $CartBT; ?>" class="btn_cart">장바구니</a></span>
			<span class="inner"><a href="#none" onclick="<?php echo $BuyBT; ?>" class="btn_order">바로구매</a></span>
		</div>

		<div class="btn_right">
			<!--  이미 찜했을 경우 btn_on -->
			<a href="#none" onclick="return false;" data-code="<?=$row_product['code']?>" class="btn_wish ajax_wish <?=$is_wish?'btn_wish_hit':''?>">
				<span class="ic">
					<img src="/m/images/ic_wish_off.png" alt="찜하기" class="off">
					<img src="/m/images/ic_wish_hit.png" alt="찜하기" class="on">
				</span>
			</a>
		</div>
	</div>
	<?php // LDD019 ?>

</div><!-- .product_opt -->

<!-- sns 공유 -->
<div class="share_sns">
	<span class="lineup">
		<a href="#none" onclick="return false;" id="kakao-link-btn" class="btn"><img src="/m/images/ic_kkt.png" alt="카카오톡"></a>
		<a href="#none" onclick="executeKakaoStoryLink();return false;" class="btn"><img src="/m/images/ic_kks.png" alt="카카오스토리"></a>
		<a href="#none" onclick="sendSNS('twitter');return false;" class="btn"><img src="/m/images/ic_tw.png" alt="트위터"></a>
		<a href="#none" onclick="sendSNS('facebook');return false;" class="btn"><img src="/m/images/ic_fb.png" alt="페이스북"></a>
	</span>
</div>

<!-- 상품정보탭 -->
<div class="product_tab">
	<a href="#none" onclick="return false;" data-tab="info" class="tab_toggle hit">상품설명</a>
	<a href="#none" onclick="return false;" data-tab="detail" class="tab_toggle">상세정보</a>
	<a href="#none" onclick="return false;" data-tab="qna" class="tab_toggle">상품문의</a>
</div>
<script>
$(document).ready(function(){
	$('.tab_toggle').on('click',function(){
		var tab = $(this).data('tab');
		$('.tab_target').hide(); $('#tab_'+tab).show();
		$('.tab_toggle').removeClass('hit'); $(this).addClass('hit');
		if(tab=='info') { initialize(); }
	});
});
</script>

<!-- 상품설명 및 지도 출력 -->
<div class="product_detail tab_target" id="tab_info">
	<!-- 상품설명 이미지 -->
	<div class="detail_img">
		<div class="editor">
			<?=stripslashes(htmlspecialchars_decode($row_product['comment2_m']?$row_product['comment2_m']:$row_product['comment2']))?>
		</div>
	</div>

	<? if(rm_str($row_product['coordinate'])>0) { ?>
	<!-- 찾아오시는 길 -->
	<style type="text/css">#map_canvas { width:100%; height:100%; }</style>
	<div class="detail_map">
		<div class="title"><img src="/m/images/ic_location.png" alt="업체찾아오시는 길"><em>업체 찾아오시는 길</em></div>
		<div class="map_area" id="map_canvas" style="<?=!$row_product['com_mapx']||!$row_product['com_mapy']?"display:none;":""?>"></div>
		<div class="location_info">
			<dl>
				<dt><?=$row_customer['cName']?></dt>
				<dd>
					<div class="opt">주소</div>
					<div class="conts"><?=$row_product['com_juso']?></div>
				</dd>
				<dd>
					<div class="opt">연락처</div>
					<div class="conts"><?=phone_print($row_customer['tel1'],$row_customer['tel2'],$row_customer['tel3'])?></div>
				</dd>
				<!-- <dd>
					<div class="opt">오시는 길</div>
					<div class="conts">등록한 내용이 들어갑니다.</div>
				</dd> -->
			</dl>
		</div>
	</div>
	<?php
	$google_key_multi = explode("§" , $row_setup[s_google_key]);
	?>
	<script type="text/javascript" src="//maps.googleapis.com/maps/api/js??key=<?php echo $google_key_multi[0]; ?>"></script>
	<script type="text/javascript">
		function initialize() {
			var latlng = new google.maps.LatLng(<?=$row_product['coordinate']?>);
			var myOptions = { zoom: 15, center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP };
			var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			var marker_0 = new google.maps.Marker({
				position: new google.maps.LatLng(<?=$row_product['coordinate']?>),
				map : map, title : '<?=$row_product[name]?>'
			});
			var infowindow_0 = new google.maps.InfoWindow({ content : '<?=$row_customer[cName]?>' });
			infowindow_0.open(map,marker_0);
		}
		$(document).ready(function(){ initialize(); });
	</script>
	<? } ?>
</div>

<!-- 정보제공고시 및 기본정보/이용정보 출력 -->
<div class="product_detail tab_target" id="tab_detail" style="display:none;">
	<div class="detail_info">
		<?
		$psres = _MQ_assoc("select * from odtProductReqInfo where pri_value <> '' and pri_pcode='" . $pcode . "' order by pri_uid asc  ");
		if(count($psres) > 0 ) {
		?>
		<div class="detail_tit">전자상거래 등에서의 상품 정보 제공 고시</div>
		<table summary="" class="information">
			<colgroup>
				<col width="70px"/><col width="*"/>
			</colgroup>
			<tbody>
				<? foreach($psres as $psk=>$psv){ ?>
				<tr>
					<td class="opt"><?=stripslashes($psv['pri_key'])?></td>
					<td class="conts"><?=stripslashes($psv['pri_value'])?></td>
				</tr>
				<? } ?>
			</tbody>
		</table>
		<? } ?>

		<div class="detail_tit">상품 사용 정보</div>
		<div class="item_info"><div class="editor"><?=stripslashes(htmlspecialchars_decode($row_product['comment_proinfo']))?></div></div>

		<div class="detail_tit">업체 이용 정보</div>
		<div class="item_info"><div class="editor"><?=stripslashes(htmlspecialchars_decode($row_product['comment_useinfo']))?></div></div>
	</div>
</div>

<!-- 상품문의 출력 -->
<div class="product_detail tab_target" id="tab_qna" style="display:none;">
	<div class="detail_conts">
	<? include dirname(__FILE__)."/product.talk.form.php";?>
	</div>
</div>




<!-- SNS: 페이스북 -->
<div id="fb-root" style="display: none;"></div>

<script src="/m/js/option_select.js" type="text/javascript"></script>
<!-- SNS: 카카오 -->
<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type="text/javascript" src="/include/js/kakao.link.js"></script>
<script>
var $allVideos = $("iframe[src^='https://player.vimeo.com'], iframe[src^='//player.vimeo.com'], iframe[src^='https://www.youtube.com'], iframe[src^='//www.youtube.com']"), newWidth = $(".detail_img").width()*1;
$(document).ready(function(){
	var endDttm = '<?=str_replace("-","",$row_product[sale_enddate]).$row_product[sale_enddateh].$row_product[sale_enddatem]?>';
	endDttm += '00';
	var startDttm = "<?=date('YmdHis')?>";
	var endDate = new Date(endDttm.substring(0,4),endDttm.substring(4,6) -1 ,endDttm.substring(6,8),endDttm.substring(8,10),endDttm.substring(10,12),endDttm.substring(12,14));
	var startDate = new Date(startDttm.substring(0,4),startDttm.substring(4,6) -1,startDttm.substring(6,8),startDttm.substring(8,10),startDttm.substring(10,12),startDttm.substring(12,14));
	periodDate = (endDate - startDate)/1000;
	if(endDate > startDate){ remainTime(periodDate); }
	else { $('#remainDay').html('00'); $('#remainHour').html('00'); $('#remainMin').html('00'); $('#remainSec').html('00');	}

	$('.detail_img').imagesLoaded().done(function(){
		newWidth = $('.detail_img').width()*1;
		$allVideos.each(function() {
			var $el = $(this);
			$el.attr('data-aspectRatio', this.height / this.width);
			$el.width(newWidth).height(newWidth * $el.attr('data-aspectRatio')*1);
			$el.removeAttr('height').removeAttr('width');
		});
	});
});
$(window).resize(function() {
	newWidth = $('.detail_img').width()*1;
	$allVideos.each(function() { var $el = $(this); $el.width(newWidth).height(newWidth * $el.attr('data-aspectRatio')*1); });
}).resize();

var count = 0;
function remainTime(periodDate){
	var day  = Math.floor(periodDate / 86400);
	var hour = Math.floor((periodDate - day * 86400 )/3600);
	var min  = Math.floor((periodDate - day * 86400 - hour * 3600)/60);
	var sec  = Math.floor(periodDate - day * 86400 - hour * 3600 - min * 60);
	if(day > 0) { (day<10) ? $('#remainDay').html('0'+day) : $('#remainDay').html(day); }
	else { $('#remainDay').html('00'); }
	if(day > 0 || (day == 0 && hour > 0)) { (hour<10) ? $('#remainHour').html('0'+hour) : $('#remainHour').html(hour); }
	else { $('#remainHour').html('00'); }
	if(day > 0 || (day == 0 && hour > 0) || (day == 0 && hour == 0 && min > 0)) { (min<10) ? $('#remainMin').html('0'+min) : $('#remainMin').html(min); }
	else { $('#remainMin').html('00'); }
	if(day > 0 || (day == 0 && hour > 0) || (day == 0 && hour == 0 && min > 0) || (day == 0 && hour == 0 && min == 0 && sec > 0)) {	(sec<10) ? $('#remainSec').html('0'+sec) : $('#remainSec').html(sec); }
	else { $('#remainSec').html('00'); }
	periodDate = periodDate -1;
	setTimeout(function(){remainTime(periodDate)}, 1000);
	return;
}

function option_select_check() {
	var sum = 0;
	obj = $(".po_cnt_val");
	for(i=0;i<obj.length;i++) { sum += parseInt(obj[i].value); }
	if(sum > 0) { return true; } else { return false; }
}

function order(code,_type) {
	if(!option_select_check()) {
		alert("옵션을 선택하세요");
		return false;
	} else {

		if($('#add_option_select_1_id').val()) { var add_option_select_1 = $('#add_option_select_1_id').val(); } else { var add_option_select_1 = ''; }
		if($('#add_option_select_2_id').val()) { var add_option_select_2 = $('#add_option_select_2_id').val(); } else { var add_option_select_2 = ''; }
		if($('#add_option_select_3_id').val()) { var add_option_select_3 = $('#add_option_select_3_id').val(); } else { var add_option_select_3 = ''; }
		if($('#add_option_select_4_id').val()) { var add_option_select_4 = $('#add_option_select_4_id').val(); } else { var add_option_select_4 = ''; }
		if($('#add_option_select_5_id').val()) { var add_option_select_5 = $('#add_option_select_5_id').val(); } else { var add_option_select_5 = ''; }
		if($('#add_option_select_6_id').val()) { var add_option_select_6 = $('#add_option_select_6_id').val(); } else { var add_option_select_6 = ''; }
		if($('#add_option_select_7_id').val()) { var add_option_select_7 = $('#add_option_select_7_id').val(); } else { var add_option_select_7 = ''; }
		if($('#add_option_select_8_id').val()) { var add_option_select_8 = $('#add_option_select_8_id').val(); } else { var add_option_select_8 = ''; }
		if($('#add_option_select_9_id').val()) { var add_option_select_9 = $('#add_option_select_9_id').val(); } else { var add_option_select_9 = ''; }
		if($('#add_option_select_10_id').val()) { var add_option_select_10 = $('#add_option_select_10_id').val(); } else { var add_option_select_10 = ''; }

		if($("#option_select_type").val() == "nooption") {
			location.href = ('/m/shop.cart.pro.php?mode=add&pcode='+code+'&pass_type=' + _type  + '&add_option_select_1=' + add_option_select_1 + '&add_option_select_2=' + add_option_select_2 + '&add_option_select_3=' + add_option_select_3 + '&add_option_select_4=' + add_option_select_4 + '&add_option_select_5=' + add_option_select_5 + '&add_option_select_6=' + add_option_select_6 + '&add_option_select_7=' + add_option_select_7 + '&add_option_select_8=' + add_option_select_8 + '&add_option_select_9=' + add_option_select_9 + '&add_option_select_10=' + add_option_select_10 + '&option_select_type='+$("#option_select_type").val()+'&option_select_cnt=' + $("#option_select_cnt").val());
		} else {
			location.href = ('/m/shop.cart.pro.php?mode=add&pcode='+code+'&pass_type=' + _type  + '&add_option_select_1=' + add_option_select_1 + '&add_option_select_2=' + add_option_select_2 + '&add_option_select_3=' + add_option_select_3 + '&add_option_select_4=' + add_option_select_4 + '&add_option_select_5=' + add_option_select_5 + '&add_option_select_6=' + add_option_select_6 + '&add_option_select_7=' + add_option_select_7 + '&add_option_select_8=' + add_option_select_8 + '&add_option_select_9=' + add_option_select_9 + '&add_option_select_10=' + add_option_select_10 );
		}
	}
}

function sendSNS(type) {
	var url = '<?=$sns_url?>';
	var fullurl = '<?=$sns_fullurl?>';
	var title = '<?=$sns_name?>';
	var image = '<?=$sns_image?>';
	var desc = '<?=$sns_desc?>';
	if(type=='facebook') { postToFeed(title, desc, fullurl, image); }
	else if(type=='twitter') {
		var wp = window.open("http://twitter.com/intent/tweet?text=" + encodeURIComponent(title + " " + desc) + " " + encodeURIComponent(url), 'twitter', 'width=550,height=256');
		if(wp) { wp.focus(); }
	}
	$.ajax({
		data: {'pcode':'<?=$pcode?>','type':type},
		type: 'GET', cache: false, url: '/pages/ajax.sns.update.php',
		success: function(data) { return true; },
		error:function(request,status,error){ alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error); }
	});
}

<?php if($app_sns_facebook_id) { ?>
	window.fbAsyncInit = function(){
	FB.init({
		appId: '<?=$row_setup[Facebook_id]?>', status: true, cookie: true, xfbml: true });
	};
	(function(d, debug){var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];if   (d.getElementById(id)) {return;}js = d.createElement('script'); js.id = id; js.async = true;js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";ref.parentNode.insertBefore(js, ref);}(document, /*debug*/ false));
	function postToFeed(title, desc, url, image){
	var obj = {method: 'feed',link: url, picture: image,name: title,description: desc};
	function callback(response){}
	FB.ui(obj, callback);
	}
<?php } ?>

<?php if($row_setup['kakao_api']) { ?>
// 카카오톡
Kakao.init('<?=$row_setup[kakao_api]?>');

// 2018-06-20 SSJ : 카카오링크 v2업그레이드
$('#kakao-link-btn').on('click', function(){

	var url = '<?=$sns_url?>';
	var fullurl = '<?=$sns_fullurl?>';
	var title = '<?=$sns_name?>';
	var image = '<?=$sns_image?>';
	var desc = '<?=$sns_desc?>';

	try {
		if(typeof Kakao != 'object') {
			Kakao.init('<?php echo $row_setup['kakao_api']; ?>');
		}
		Kakao.Link.sendDefault({
			objectType: 'feed',
			content: {
				title: title,
				description: desc,
				imageUrl: image,
				imageWidth: 470, // 없으면 이미지가 찌그러짐
				imageHeight: 470, // 없으면 이미지가 찌그러짐
				link: {
					mobileWebUrl: url,
					webUrl: url
				}
			},
			buttons: [
				{
					title: '<?php echo $row_setup['site_name']; ?>',
					link: {
						mobileWebUrl: url,
						webUrl: url
					}
				}
			],
			installTalk: true,
			fail: function(err) {
				alert(JSON.stringify(err));
			}
		});
	} catch(e) {
		alert('카카오톡으로 공유 할 수 없는 상태 입니다.');
	};
});

// 카카오스토리
function executeKakaoStoryLink(){
	Kakao.Story.share({
		url: '<?=$sns_fullurl?>',
		text: '<?=$sns_desc?>'
	});
}
<?php } ?>
</script>