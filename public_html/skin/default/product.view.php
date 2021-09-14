<?php
// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---

// BEST 추천상품 갯수
$best_limit = 20;

// 상품코드 체크
if(!$_GET['pcode']) { error_msg("상품 코드가 없습니다."); }

// - 임시 옵션 삭제 ---
if($_COOKIE["AuthShopCOOKIEID"]) _MQ_noreturn("delete from odtTmpProductOption where otpo_mid='". $_COOKIE["AuthShopCOOKIEID"] ."'");

// 상품정보 추출
$row_product = get_product_info($pcode);

// 숨김상품은 보이지 않게처리
if($row_product['p_view'] == 'N' && !$_COOKIE["auth_adminid"] && !$_COOKIE["auth_comid"]) error_msg('판매중인 상품이 아닙니다.');

// 판매기간 시작 체크
if($row_product['sale_date'] > date('Y-m-d') && $row_product['sale_type'] == 'T') error_msg("판매 시작전 상품입니다.");

// 판매기간 종료 체크
if($row_product['sale_enddate'] < date('Y-m-d') && $row_product['sale_type'] == 'T') error_msg("판매종료된 상품입니다.");

// 상품 수량 체크
if($row_product['stock'] < 1) { error_msg("품절된 상품입니다."); }



// 공급업체 정보
$row_customer = _MQ("select * from odtMember where id = '".$row_product['customerCode']."' and userType ='C'");

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
if(!$row_product['buy_limit']) { $buy_limit_info = "구매제한없음"; }
else { $buy_limit_info = "주문당 구매제한 : ".number_format($row_product['buy_limit'])."개 "; }

// 상품 하단 작은 아이콘
$product_small_icon = get_product_icon_info($row_product);

// 아이콘 정보 배열로 추출
$product_icon = get_product_icon_info_qry("product_name_small_icon");

// 상품 하단 작은 아이콘 - 추가
if($row_product['p_icon']) {
	$p_icon_array = explode(",",$row_product['p_icon']);
	foreach($product_icon as $k0 => $v0) {
		if(array_search($v0['pi_uid'],$p_icon_array) !== false) {
			$product_small_icon .= "<img src='/upfiles/icon/".$v0['pi_img']."' title='".$v0['pi_title']."'> ";
		}
	}
}
$product_small_icon = $product_small_icon ? $product_small_icon : "";

// text 값 추출
$row_product = array_merge($row_product , _text_info_extraction( "odtProduct" , $row_product['serialnum'] ));

// 좌표값
$row_product['coordinate'] = $row_product['com_mapx'].", ".$row_product['com_mapy'];

// 부제목 정리
$row_product['short_comment'] = rm_enter($row_product['short_comment']);

// 찜하기 체크
$is_wish = is_login() ? _MQ_result("select count(*) from odtProductWish where pw_pcode = '".$row_product['code']."' and pw_inid='".get_userid()."'") : 0;


// SNS 공유를 위한 변수 생성
$sns_url = rewrite_url($pcode);
$sns_fullurl = "http://".$_SERVER['HTTP_HOST']."/".$pcode;
$sns_url = $sns_url ? $sns_url : $sns_fullurl;
$sns_name = $row_product['name']." ".number_format($row_product['price'])."원";
//$sns_image = "http://".$_SERVER['HTTP_HOST'].IMG_DIR_PRODUCT.$row_product['main_img'];
$sns_image = replace_image(IMG_DIR_PRODUCT.$row_product['main_img']);
$sns_desc = trim(str_replace("  "," ",str_replace(":","-",str_replace("\t"," ",str_replace("\r"," ",str_replace("\n"," ",str_replace("'","`",stripslashes(($row_product['hort_comment']?$row_product['short_comment']." - ":"") .$row_company['homepage_title']))))))));


include_once dirname(__FILE__)."/product.view.email.php";
?>


<div class="product_view">
	<div class="view_wrap">

		<!-- 상품썸네일 -->
		<div class="thumb_area">
			<div class="thumb_img">
				<div class="upper_ic">
					<?=$product_small_icon?>
				</div>
				<img src="<?=replace_image(IMG_DIR_PRODUCT.$row_product['main_img'])?>" alt="<?=$row_product['name']?>" />
			</div>

			<div class="share_sns">
				<a href="#none" onclick="sendSNS('facebook');return false;"><img src="/pages/images/ic_fb.gif" alt="페이스북 공유" /></a>
				<a href="#none" onclick="sendSNS('twitter');return false;"><img src="/pages/images/ic_tw.gif" alt="트위터 공유" /></a>
				<a href="#none" onclick="sendMail();return false;"><img src="/pages/images/ic_email.gif" alt="이메일 공유" /></a>
			</div>
		</div>

		<!-- 상품정보 -->
		<div class="info_box">
			<div class="item_name">
				<div class="sub_name"><?=$row_product['short_comment']?></div>
				<div class="name"><?=$row_product['name']?></div>
			</div>


			<div class="price_info">
				<!-- 할인율이 0일경우 사라짐 -->
				<? if($row_setup['view_social_commerce']=='Y') { if($row_product['price_per']>0) { ?>
				<div class="discount"><?=$row_product['price_per']?><em>%</em></div>
				<? } else { ?>
				<div class="discount discount_none"></div>
				<? }} ?>
				<div class="price">
					<? if($row_setup['view_social_commerce']=='Y' && $row_product['price_org']>0) { ?>
					<div class="before"><del><?=number_format($row_product['price_org'])?></del></div>
					<? } ?>
					<div class="after"><?=number_format($row_product['price'])?><span class="kor">원</span></div>
				</div>
			</div>

			<? if($row_setup['view_social_commerce']=='Y' && $row_product['sale_type'] == 'T') { ?>
			<!-- 구매개수 및 남은시간(없을 경우 사라짐) -->
			<div class="buy_info">
				<div class="now_buy">현재<strong><?=number_format($row_product['saleCnt']>0?$row_product['saleCnt']:0)?></strong>개 구매</div>
				<div class="timer">남은시간<strong id="remainDay">00</strong>일<strong id="remainHour">00</strong>:<strong id="remainMin">00</strong>:<strong id="remainSec">00</strong></div>
			</div>
			<? } ?>

			<? if( $row_product['coupon_title'] && $row_product['coupon_price']>0 ) { ?>
			<!-- 상품쿠폰이 있을경우 -->
			<div class="view_item_coupon">
				<span class="txt_icon">COUPON</span>
				<label class="one_coupon"><!-- <input type="checkbox" name="" /> --><?=stripslashes($row_product['coupon_title'])?> (<strong><?=$row_product['coupon_price']?>%</strong> 할인 : <strong><?=number_format( floor( $row_product['price'] * $row_product['coupon_price'] / 100 ) )?></strong>원)</label>
			</div>
			<? } ?>

			<!-- 옵션선택 -->
			<?
			// 추가옵션 출력 함수
			function addoption_print($row_code){
				GLOBAL $row_product; if($row_product['setup_delivery']=='Y') {
				$add_options = _MQ_assoc("select * from odtProductAddoption where pao_pcode='".$row_code."' and pao_depth='1' and pao_view = 'Y' order by pao_sort asc, pao_uid asc ");
				if(count($add_options) > 0){ ?>
				<div class="guide_text">추가옵션을 선택해주세요.</div>
				<? foreach($add_options as $k=>$v){ ?>
				<select name='_add_option_select_<?=$k+1?>' id="add_option_select_<?=$k+1?>_id" class='add_option add_option_chk' onchange="add_option_select_add('<?=$row_code?>',this.value)">
					<option value="">옵션을 선택하세요 ( <?=$v['pao_poptionname']?> )</option>
					<?
						$add_sub_options = _MQ_assoc("select * from odtProductAddoption where pao_pcode='".$row_code."' and pao_depth='2' and pao_parent='".$v['pao_uid']."'");
						foreach($add_sub_options as $key=>$value) {
					?>
					<option value="<?=$value['pao_uid']?>"><?=$value['pao_poptionname']?> (잔여:<?=($value['pao_cnt']>0?number_format($value['pao_cnt']):"품절")?>) / <?=($value['pao_poptionprice']<0?"":"+")?><?=number_format($value['pao_poptionprice'])?>원</option>
					<? } ?>
				</select>
				<? }} ?>
				<input type="hidden" name="addoption_cnt" value="<?=count($add_options)?>">
			<? }} ?>

			<div class="option">
				<div class="guide_text">
					<?php if(in_array($row_product['option_type_chk'], array('1depth', '2depth', '3depth'))) { ?>
						상세옵션을 선택해주세요. [<?=$buy_limit_info?><?=($delivery_info <> '' ? ', '.$delivery_info: '')?>]
					<?php } else { ?>
						<?=$buy_limit_info?><?=($delivery_info <> '' ? ', '.$delivery_info: '')?>
					<?php } ?>
				</div>
				<?
				    // 옵션정보 불러오기
				    $sque = "select oto_uid,oto_poptionname,oto_cnt,oto_poptionprice from odtProductOption where oto_pcode='".$row_product['code']."' and oto_depth='1' and oto_view = 'Y' order by oto_sort asc, oto_uid asc ";
				    $sres = _MQ_assoc($sque);

					// 옵션타이틀
					$option1_title = (trim($row_product['option1_title']) ? trim($row_product['option1_title']) : ($row_product['option_type_chk'] == "1depth" ? "상세옵션을 선택해주세요" : "1차옵션을 선택하세요") );
					$option2_title = (trim($row_product['option2_title']) ? trim($row_product['option2_title']) : "상위 옵션을 먼저 선택하세요");
					$option3_title = (trim($row_product['option3_title']) ? trim($row_product['option3_title']) : "상위 옵션을 먼저 선택하세요");

				    foreach( $sres as $k=>$sr){
						if($row_product['option_type_chk'] == "1depth") {
							$str_option .= "<option value='".$sr['oto_uid']."'>".$sr['oto_poptionname']." (잔여: ".($sr['oto_cnt']>0?number_format($sr['oto_cnt']):"품절").") / ".($sr['oto_poptionprice']<0?"":"+").number_format($sr['oto_poptionprice'])."원</option>";
						} else {
							$str_option .= "<option value='".$sr['oto_uid']."'>".$sr['oto_poptionname']."</option>";
						}
				    }

				    if( $row_product['option_type_chk'] == "1depth" && count($sres) > 0){
						echo "
							<select name='_option_select1' ID='option_select1_id' onchange=\"option_select_add('".$row_product['code']."')\">
								<option value=''>".$option1_title."</option>".$str_option."
							</select>";
						addoption_print($row_product['code']);
						echo "<div class='option_list' ID='span_seleced_list'></div>";
				    } else if( $row_product['option_type_chk'] == "2depth" && count($sres) > 0){
						echo "
							<select name='_option_select1' ID='option_select1_id' onchange=\"option_select(1,'".$row_product['code']."');\">
								<option value=''>".$option1_title."</option>".$str_option."
							</select>
							<span ID='span_option2' style='display:block;'><select disabled class='add_option'><option>".$option2_title."</option></select></span>";
						addoption_print($row_product['code']);
						echo "<div class='option_list' ID='span_seleced_list'></div>";
				    } else if( $row_product['option_type_chk'] == "3depth" && count($sres) > 0){
						echo "
							<select name='_option_select1' ID='option_select1_id' onchange=\"option_select(1,'".$row_product['code']."');\">
								<option value=''>".$option1_title."</option>".$str_option."
							</select>
							<span ID='span_option2' style='display:block;'><select disabled class='add_option'><option>".$option2_title."</option></select></span>
							<span ID='span_option3' style='display:block;'><select disabled class='add_option'><option>".$option3_title."</option></select></span>";
						addoption_print($row_product['code']);
						echo "<div class='option_list' ID='span_seleced_list'></div>
						";
					} else {
				        echo "
							<div class='option_list'>
								<ul>
									<li>
										".
										(
										$row_product['stock'] < 1
										?
											"<span class='option_name'>품절된 상품입니다.</span>"
										:
											"<span class='option_name'>구매수량</span>
											<span class='updown_box'>
												<input type='text' name='option_select_cnt' class='updown_input' value='1' ID='option_select_cnt' readonly/>
												<span class='updown'><a href='#none' onclick=\"pro_cnt_up(".$row_product['buy_limit'].")\" class='btn_up' title='더하기'></a><a href='#none' onclick=\"pro_cnt_down('')\"  class='btn_down' title='빼기'></a></span>
											</span>
											<span class='option_price'>".number_format($row_product['price']*1)."원</span>"
										)."
									</li>
								</ul>
							</div>

							<input type=hidden name=product_stock id=product_stock value='".$row_product['stock']."'/>
							<input type=hidden name=option_select_expricesum ID='option_select_expricesum' value='".$row_product['price']."'/>
							<input type=hidden name=option_select_type id='option_select_type' value='nooption'/>

							<script>
							$(document).ready(function() { update_sum_price(); });
							</script>
						";
				    }

				    // 내용 노출 부분
					echo "";

				?>
			</div>

			<!-- 총 상품금액 -->
			<div class="total_sum">
				<div class="price_txt">
					총 상품금액 <span class="num" id="option_select_expricesum_display">0</span>원
				</div>
			</div>

			<!-- 구매버튼 -->
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
			<div class="btn_area">
				<a href="#none" onclick="<?php echo $BuyBT; ?>" class="btn_order">바로구매<!-- <img src="/pages/images/btn_buy.gif" alt="바로구매" /> --></a>
				<a href="#none" onclick="<?php echo $CartBT; ?>" class="btn_cart">장바구니<!-- <img src="/pages/images/btn_cart.gif" alt="장바구니 담기" /> --></a>
				<a href="#none" onclick="return false;" data-code="<?=$row_product['code']?>" class="ajax_wish <?=$is_wish?'btn_wish_hit':''?> btn_wish">찜<!-- <img src="/pages/images/btn_wish.gif" alt="찜하기" /> --></a>
			</div>
			<?php // } LDD019 ?>
		</div>

	</div> <!-- .view_wrap -->
</div> <!-- .product_view -->


<div class="product_detail">
	<div class="detail_left">

		<?php
		# LDD012
		//$relation = str_replace("/",",",$row_product['p_relation']);
		$relation = relation_list($pcode);
		$relation_assoc = _MQ_assoc("select *,(select pct_cuid from odtProductCategory where pct_pcode = code order by pct_uid asc limit 1) as cuid from odtProduct where p_view='Y' and if(sale_type = 'T', (CURDATE() BETWEEN sale_date and sale_enddate), sale_type = 'A') and find_in_set(code,'".$relation."') and stock > 0");
		if(count($relation_assoc)>0) {
		?>
		<!-- 관련상품/하나도 등록되지 않은 경우 display:none -->
		<div class="detail_top">
			<div class="title">
				<span class="txt"><strong>BEST</strong> 관련상품</span>

				<? if(count($relation_assoc)>5) { ?>
				<span class="btn_nate">
					<span class="num"><em id="relation_cnt">1</em>/<?=count($relation_assoc)?></span>
					<a href="#none" onclick="return false;" id="relation_prev">
						<img src="/pages/images/view_btn_prev.gif" alt="이전" class="off" />
						<img src="/pages/images/view_btn_prev_over.gif" alt="이전" class="over" />
					</a>
					<a href="#none" onclick="return false;" id="relation_next">
						<img src="/pages/images/view_btn_next.gif" alt="다음" class="off" />
						<img src="/pages/images/view_btn_next_over.gif" alt="다음" class="over" />
					</a>
				</span>
				<? } ?>
			</div>

			<!-- 아이템이 등록된 개수대로 나오기 -->
			<div class="item_area">
				<div id="relation_slider" style="height:185px;overflow:hidden;">
				<? foreach($relation_assoc as $k=>$v) { ?>
					<a href="<?=rewrite_url($v['code'])?>" class="item" title="<?=$v['name']?>">
						<span class="thumb"><img src="<?=replace_image(IMG_DIR_PRODUCT.$v['prolist_img'])?>" alt="<?=$v['name']?>" /></span>
						<span class="info">
							<!-- 제목한줄제한 -->
							<span class="name"><?=cutstr($v['name'],14)?></span>
							<span class="price"><em><?=number_format($v['price'])?></em>원</span>
						</span>
					</a>
				<? } ?>
				</div>
			</div>
		</div>
		<script>
		var relation_slider = '';
		$(window).ready(function(){
			relation_slider = $('#relation_slider').bxSlider({
				<? if(count($relation_assoc)>5) { ?>
				auto: true, autoHover: true, speed: 500,
				slideSelector: '.item', easing: 'easeInOutCubic', useCSS: false,
				slideMargin: 18, slideWidth: 141,
				minSlides: 5, maxSlides: 5, moveSlides: 1,
				pager: false, controls: false,
				onSlideBefore: function($slideElement, oldIndex, newIndex){ $('#relation_cnt').text(newIndex+1); },
				onSlideAfter: function($slideElement, oldIndex, newIndex){ relation_slider.startAuto(); }
				<? } else { ?>
				auto: false, slideSelector: '.item', slideMargin: 18, slideWidth: 141, minSlides: 5, maxSlides: 5, moveSlides: 1, pager: false, controls: false
				<? } ?>
			});
		});
		$(document).ready(function(){
			$('#relation_prev').on('click',function(){ relation_slider.goToPrevSlide(); });
			$('#relation_next').on('click',function(){ relation_slider.goToNextSlide(); });
		});
		</script>
		<? } ?>

		<!-- 상세정보탭 -->
		<div class="detail_tab">
			<div class="group_tab">
				<a href="#none" onclick="return false;" data-tab="tab01" class="product_tab_toggle product_tab01 hit">상품정보</a>
				<a href="#none" onclick="return false;" data-tab="tab02" class="product_tab_toggle product_tab02 off">상품문의</a>
			</div>
		</div>
		<script>
		// 탭 화면 전환
		$(document).ready(function(){
			$('.product_tab_toggle').on('click',function(){
				var tab = $(this).data('tab');
				$('.detail_conts').hide(); $('#'+tab).show();
				$('.detail_tab a').addClass('off').removeClass('hit');
				$('.product_'+tab).addClass('hit').removeClass('off');
				$('html, body').animate({ scrollTop: $('.detail_tab').offset().top - 20 }, 500, 'easeInOutCubic');
			});
		});
		</script>

		<!-- 상품정보 -->
		<div class="detail_conts" id="tab01">

			<? if($row_product['comment_proinfo']!=''||$row_product['comment_useinfo']!='') { ?>
			<!-- 상품사용정보 및 이용정보 / 등록하지 않았을 경우에는 display:none -->
			<div class="detail_guide <?=($row_product['comment_proinfo']==''||$row_product['comment_useinfo']=='')?'if_full':''?>">
				<? if($row_product['comment_proinfo']!='') { ?>
				<dl>
					<dt>상품사용 정보</dt>
					<dd class="editor"><?=$row_product['comment_proinfo']?stripslashes($row_product['comment_proinfo']):"<span style='color:#aaa;'>상품사용 정보가 없습니다.</span>"?></dd>
				</dl>
				<? } ?>
				<? if($row_product['comment_useinfo']!='') { ?>
				<dl>
					<dt>업체이용 정보</dt>
					<dd class="editor"><?=$row_product['comment_useinfo']?stripslashes($row_product['comment_useinfo']):"<span style='color:#aaa;'>업체이용 정보가 없습니다.</span>"?></dd>
				</dl>
				<? } ?>
			</div>
			<? } ?>

			<!-- 상품이미지 등록 -->
			<div class="detail_img"><div class="editor"><?=stripslashes(htmlspecialchars_decode($row_product['comment2']))?></div></div>

			<?
				$psres = _MQ_assoc("select * from odtProductReqInfo where pri_value != '' and pri_pcode='".$row_product['code']."' order by pri_uid asc");
				if(count($psres) > 0 ) {
			?>
			<!-- 상품고시정보 (없으면 안보임) -->
			<div class="inner_notify">
				<div class="title_box">상품정보제공 고시</div>
				<!-- 관리자에서 관리자가 등록한 대로 li두개씩 ul묶어 반복 -->
				<div class="data_box">
					<!--디자인보더 -->
					<div class="border"></div>
					<ul>
					<? foreach($psres as $psk=>$psv) { ?>
						<li>
							<span class="opt"><?=stripslashes($psv['pri_key'])?></span>
							<div class="value"><?=stripslashes($psv['pri_value'])?></div>
						</li>
					<? } if($psk>0&&$psk%2==0) { echo "</ul><ul>"; } ?>
					</ul>
				</div>
			</div>
			<? } ?>
			<!-- / 상품고시정보 -->

			<? if(rm_str($row_product['coordinate'])>0) { ?>
			<!-- 찾아오시는 길 -->
			<div class="datail_map">
				<div class="view_map_tit">업체이용정보</div>
				<div class="map_area" id="map_canvas" style="width:760px;height:390px;"></div><!-- 760*390 -->
				<div class="map_info">
					<dl>
						<dt><?=stripslashes($row_customer['cName'])?></dt>
						<dd>
							<div class="opt">주소</div>
							<div class="conts"><?=stripslashes($row_product['com_juso'])?></div>
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
			<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php echo $google_key_multi[0]; ?>"></script>
			<script type="text/javascript">
				function initialize() {
					var latlng = new google.maps.LatLng(<?=$row_product['coordinate']?>);
					var myOptions = { zoom: 15, center: latlng, disableDefaultUI: false, scrollwheel: false, mapTypeId: google.maps.MapTypeId.ROADMAP };
					var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
					var marker_0 = new google.maps.Marker({ position: new google.maps.LatLng(<?=$row_product['coordinate']?>), map : map, title : '<?=$row_product[name]?>' });
					var infowindow_0 = new google.maps.InfoWindow({ content : '<?=$row_customer[cName]?>' });
					infowindow_0.open(map,marker_0);
				}
				$(document).ready(function(){ initialize(); });
			</script>
			<? } ?>

			<!-- 안내 -->
			<div class="view_guide">

				<div class="guide_txt">
					<dl>
						<dt>본 <b>상품에 대해 궁금하신 점</b>이 있으면 <b>상품문의</b>를 이용해주세요.</dt>
						<dd>상품을 주문하기 전에 궁금하신 점이 있으면 <a href="/?pn=mypage.request.form">1:1온라인문의</a>나 <a href="#none" onclick="return false;" class="product_tab_toggle" data-tab="tab02">상품문의</a>를 남겨주세요.</dd>
						<dd>환불/취소에 대한 문의는 고객센터 <a href="/?pn=mypage.request.form">1:1온라인문의</a>를 이용해주세요.</dd>
						<dd>상품과 관계없는 글이나 광고, 비방글은 사전 고지없이 삭제될수 있습니다.</dd>
						<dd>글을 쓰실 때에는 노출되기 쉬운 개인정보는 절대 남기지 말아주세요.</dd>
					</dl>
				</div>

				<div class="view_btn_area">
					<a href="#none" onclick="return false;" data-tab="tab02" class="product_tab_toggle btn btn_sp">상품문의</a>
					<a href="/?pn=mypage.request.form" class="btn">1:1온라인문의</a>
				</div>

				<div id="care">


<div class="product_contents" id="view5_content" style="margin-top:15px;">
<table border="1" cellspacing="1" border-color="#CDDDE9" width="100%"  cellpadding="0" class="table_information" style="margin-top:15px;">
						<colgroup>
							<col width="100">
							<col width="110">
							<col width="">
						</colgroup>
						<tbody><tr>
							<th rowspan="5">배송안내</th>
							<td class="title">배송업체명</td>
							<td>
								<ul>
									<li>주식회사 호호랩스 </li>

								</ul>
							</td>
						</tr>
						<tr>
							<td class="title">상품 및 배송문의</td>
							<td>
								<ul>
									<li>070-4633-1725</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td class="title">반품처</td>
							<td>
								<ul>
									<li>고객센터 문의</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td class="title">배송기간</td>
							<td>
								<ul>
									<li>업체 상품 출고일로부터령1~3일 이내 수령 가능(토,일 공휴일제외). 도서산간 지역 7일이내 수령</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td class="title">배송비</td>
							<td>
								<ul>
									<li>3,000원 (단, 제주 및 일부 도서 산간지역의 경우 고객 부담으로 추가배송비가 발생할 수 있습니다,)</li>
								</ul>
							</td>
						</tr>
					</tbody></table>

										<table border="1" cellspacing="1" border-color="#CDDDE9" width="100%" class="table_information" style="margin-top:15px;">
						<colgroup>
							<col width="100">
							<col width="110">
							<col width="">
						</colgroup>
						<tbody><tr>
							<th rowspan="4">결제안내</th>
							<td class="title">결제방식</td>
							<td>
								<ul>
								 	<li>무통장입금, 실시간 계좌이체, 신용카드, 전용몰포인트, 회원보관금, 핸드폰 소액결제</li>
								 </ul>
							</td>
						</tr>
										
						<tr>
							<td class="title">고액결제</td>
							<td>
								<ul>
									<li>고액결제의 경우, 고객의 결제 안전을 위하여 신용카드 부정사용등 비정상적인 주문으로 판단될 경우, 그 주문 및 결제를<br>제한할 수 있습니다.</li>
								</ul>
							</td>
						</tr>
					</tbody></table>
				</div>
        
        
        <div class="product_contents" id="view6_content">
					<table border="1" cellspacing="1" border-color="#CDDDE9"  width="100%" class="table_information" style="margin-top:15px;">
						<colgroup>
							<col width="100">
							<col width="110">
							<col width="">
						</colgroup>
						<tbody><tr>
							<th rowspan="5">교환/반품<br>안내</th>
							<td class="title">교환/반품<br>기간</td>
							<td>
								<ul>
									<li class="bp">고객님의 단순변심으로 인한 교환·반품은, 실제 상품등을 수령하신 날부터 7일이내. 단, 상품안내 페이지에 표시된 교환/반품
기간이 7일보다 긴 경우에는 그 기간</li>
									<li class="bp">고객님이 받으신 상품 등의 내용이 표시·광고 내용과 다르거나 계약내용과 다르게 이행된 경우에는 상품 등을 수령한 날부터
3개월 이내, 그 사실을 안 날 또는 알 수 있었던 날부터 30일 이내</li>
									<li class="bp">- 식품 7일 이내, 의류·보석 15일 이내, 그 밖의 일반 상품 30일 이내 교환 반품 가능 <br>
- 주문제작 상품 등 일부 상품은 교환·반품 기준이 상이할 수 있습니다.</li>
									<li>전자상거래법에 따른 교환·반품 규정이 상품공급업체가 개별적으로 지정한 교환ㆍ반품 조건 보다 우선 합니다.</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td class="title">교환/반품 <br>배송비</td>
							<td>
								<ul>
									<li class="bp">고객님의 단순변심으로 인하여 교환ㆍ반품을 하시는 경우에는 상품등의 반환에 필요한 비용을 고객님이 부담하셔야 합니다.<br>
- 예상 반품비 : 3,000원, 예상 교환비 : 3,000원 (주문 상품을 1개씩 각각 반품ㆍ교환 시 상품별로 발생하는 비용임)<br>
- 고객님께서 직접 택배로 발송하실 경우 택배비는 본인 부담입니다.<br>
- 중·대형 가전, 가구등 설치상품의 경우 상품, 지역, 설치비 등에 따라 반품·교환 비용이 상이할 수 있습니다.<br>
- 회원등급에 따라 반품·교환 비용이 상이할 수 있습니다.<br>
- 정확한 반품·교환비는 반품·교환 접수 시 또는 고객센터로 문의 시 확인 가능합니다.</li>
									<li>고객님이 받으신 상품등의 내용이 표시·광고 내용과 다르거나 계약내용과 다르게 이행되어 교환 ㆍ반품을 하시는 경우에는,
교환ㆍ반품 배송비는 무료입니다.</li>
								</ul>
							</td>
						</tr>
					</tbody></table>
<table width="100%" border="1" cellspacing="1" cellpadding="0" class="table_information" style="margin-top:15px;">
						<colgroup>
							<col width="100">
							<col width="110">
							<col width="">
						</colgroup>
						<tbody><tr>
							<th rowspan="5">교환/반품<br>불가안내</th>
							<td class="title">교환/반품<br>불가사유</td>
							<td>
								<ul>
									<li class="bp">전자상거래 등에서 소비자보호에 관한 법률에 따라 다음의 경우 청약철회가 제한될 수 있습니다.</li>
									<li class="bp">포장을 개봉하여 사용하거나 또는 설치가 완료되어 상품등의 가치가 훼손된 경우에는 교환/반품이 불가하오니 이 점 양해하여
주시기 바랍니다. 단, 상품의 내용을 확인하기 위하여 포장을 개봉한 경우에는 교환/반품이 가능합니다.</li>
									<li class="bp">고객님의 단순변심으로 인한 교환/반품 요청이 상품을 수령한 날로부터 7일을 경과한 경우.(롯데홈쇼핑 상품은 30일, 의류/보석 15일)</li>
									<li class="bp">고객님의 책임 있는 사유로 상품 등의 가치가 심하게 파손되거나 훼손된 경우.</li>
									<li class="bp">고객님의 사용 또는 일부 소비에 의하여 상품 등의 가치가 현저히 감소된 경우.
(예: 화장품, 식품, 사용한 가전제품으로 재판매가 불가능한 경우.)</li>
									<li class="bp">시간이 경과되어 재판매가 곤란할 정도로 상품 등의 가치가 상실된 경우.
(예: 계절의류, 냉난방기기 등 계절상품)</li>
									<li class="bp">배송된 상품이 하자없음을 확인한 후 설치가 완료된 상품의 경우.
(예: 가전제품, 가구, 헬스기기 등)</li>
									<li class="bp">고객님의 요청에 따라 개별적으로 주문 제작되는 상품의 경우.
(예: 수제화, 귀금속 등)</li>
									<li class="bp">구매하신 상품의 구성품이 누락된 경우.
(예 : 화장품 세트 상품, 의류에 부착되는 액세사리, 가전제품의 부속품, 사은품 등)
단, 그 구성품이 훼손없이 회수가 가능한 경우에는 교환/반품이 가능합니다.</li>
									<li class="bp">복제가 가능한 상품 등의 포장을 훼손한 경우.
(예 : 도서, DVD, CD 등 복제 가능한 상품)</li>
									<li class="bp">기타, '전자상거래 등에서의 소비자보호에 관한 법률'이 정하는 청약철회 제한사유에 해당되는 경우.</li>
									<li>재화등의 불만처리 및 소비자와 사업자 간 분쟁 처리 사항은 이용약관 내용을 확인해 주시기 바랍니다.</li>
								</ul>
							</td>
						</tr>
					</tbody></table>




									<table border="1" cellspacing="1" border-color="#CDDDE9" width="100%" class="table_information" style="margin-top:15px;">
						<colgroup>
							<col width="100">
							<col width="110">
							<col width="">
						</colgroup>
						<tbody><tr>
														<th width="11%">유의사항</th>
							<td width="89%">
								<ul>
								 	<li>전용몰 의 교환 및 반품이 불가능한 경우를 우선적으로 준수합니다.</li>
<li>일부 가전 상품은 제조사의 불량판정을 거친 후 처리됩니다. (삼성전자,LG전자,대우전자,아이리버,캐논 등)</li>
<li>전자상거래등에서의 소비자보호에 관한 법률이 정하는 철약철회 제한에 해당하는 경우는 불가합니다.</li>

								 </ul>
							</td>
						</tr>
					</tbody></table>
				<!-- end_careful -->
				</div>
        
        </div>
			</div>

		</div> <!-- .detail_conts -->

		<!-- 내부게시판 -->
		<div class="detail_conts" id="tab02" style="display:none;">
			<!-- 안내 -->
			<div class="view_guide">
				<div class="guide_txt">
					<dl>
						<dt>본 <b>상품에 대해 궁금하신 점</b>이 있으면 <b>상품문의</b>를 이용해주세요.</dt>
						<dd>상품을 주문하기 전에 궁금하신 점이 있으면 <a href="/?pn=mypage.request.form">1:1온라인문의</a>나 <a href="#none" onclick="return false;" class="product_tab_toggle" data-tab="tab02">상품문의</a>를 남겨주세요.</dd>
						<dd>환불/취소에 대한 문의는 고객센터 <a href="/?pn=mypage.request.form">1:1온라인문의</a>를 이용해주세요.</dd>
						<dd>상품과 관계없는 글이나 광고, 비방글은 사전 고지없이 삭제될수 있습니다.</dd>
						<dd>글을 쓰실 때에는 노출되기 쉬운 개인정보는 절대 남기지 말아주세요.</dd>
					</dl>
				</div>
			</div>

			<div class="view_common">
				<? include_once dirname(__FILE__)."/product.talk.form.php";?>
			</div>
		</div> <!-- .detail_conts -->

	</div> <!-- .detail_left -->

	<div class="detail_right">
		<div class="title"><strong>BEST</strong> 추천상품</div>
		<div class="item_area">
		<? $best_assoc = _MQ_assoc("select * from odtProduct where bestview='Y' and stock > 0 and p_view='Y' and if(sale_type = 'T', (CURDATE() BETWEEN sale_date and sale_enddate), sale_type = 'A') and code != '".$row_product['code']."' order by pro_idx asc limit ".$best_limit); ?>
		<? if(count($best_assoc)==0){ ?>
		<!-- 추천상품 없을경우 -->
		<div class="item_none"><img src="/pages/images/cm_images/contents_none_s.png" alt="등록된 상품이 없습니다." /><div class="txt">현재 등록된<br/>추천상품이 없습니다.</div></div>
		<? } ?>
		<? foreach($best_assoc as $bestk=>$bestv) { ?>
		<a href="<?=rewrite_url($bestv['code'])?>" class="box" title="<?=$bestv['name']?>">
			<span class="thumb"><img src="<?=replace_image(IMG_DIR_PRODUCT.$bestv['prolist_img'])?>" alt="<?=$bestv['name']?>" /></span>
			<span class="info">
				<!-- 최대 2줄제한 -->
				<span class="name"><?=cutstr($bestv['name'],25)?></span>
				<? if($row_setup[view_social_commerce]=='Y') { ?>
				<span class="buy"><em><?=number_format($bestv['saleCnt'])?></em>개 구매</span>
				<? } ?>
			</span>
		</a>
		<? } ?>
		</div>
	</div> <!-- .detail_right -->

</div> <!-- .product_detail -->

<script>
$(document).ready(function(){
	var endDttm = '<?=str_replace("-","",$row_product[sale_enddate]).$row_product[sale_enddateh].$row_product[sale_enddatem]?>'; endDttm += '00';
	var startDttm = '<?=date('YmdHis')?>';
	var endDate = new Date(endDttm.substring(0,4),endDttm.substring(4,6) -1 ,endDttm.substring(6,8),endDttm.substring(8,10),endDttm.substring(10,12),endDttm.substring(12,14));
	var startDate = new Date(startDttm.substring(0,4),startDttm.substring(4,6) -1,startDttm.substring(6,8),startDttm.substring(8,10),startDttm.substring(10,12),startDttm.substring(12,14));
	periodDate = (endDate - startDate)/1000;
	if(endDate > startDate){ remainTime(periodDate); } else { $('#remainDay').html('00'); $('#remainHour').html('00'); $('#remainMin').html('00'); $('#remainSec').html('00'); }
});

var count = 0;
function remainTime(periodDate){
	var day  = Math.floor(periodDate / 86400);
	var hour = Math.floor((periodDate - day * 86400 )/3600);
	var min  = Math.floor((periodDate - day * 86400 - hour * 3600)/60);
	var sec  = Math.floor(periodDate - day * 86400 - hour * 3600 - min * 60);
	if(day > 0) { (day<10) ? $('#remainDay').html('0'+day) : $('#remainDay').html(String(day).comma());	} else { $('#remainDay').html('00'); }
	if(day > 0 || (day == 0 && hour > 0)) { (hour<10) ? $('#remainHour').html('0'+hour) : $('#remainHour').html(hour); } else { $('#remainHour').html('00'); }
	if(day > 0 || (day == 0 && hour > 0) || (day == 0 && hour == 0 && min > 0)) { (min<10) ? $('#remainMin').html('0'+min) : $('#remainMin').html(min); } else { $('#remainMin').html('00'); }
	if(day > 0 || (day == 0 && hour > 0) || (day == 0 && hour == 0 && min > 0) || (day == 0 && hour == 0 && min == 0 && sec > 0)) { (sec<10) ? $('#remainSec').html('0'+sec) : $('#remainSec').html(sec); } else { $('#remainSec').html('00'); }
	periodDate = periodDate -1;
	setTimeout(function(){remainTime(periodDate)}, 1000);
	return;
}

function sendSNS(type) {
	var url = '<?=$sns_url?>', fullurl = '<?=$sns_fullurl?>', title = '<?=$sns_name?>', image = '<?=$sns_image?>', desc = '<?=$sns_desc?>';
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

function sendMail() {
	$('#share_email').lightbox_me({
		centered: true, closeEsc: false,
		onLoad: function() { document.getElementById("share_email_form").reset(); }
	});
}

function talk_area_view() {
	$(".product_info").hide();
	$(".product_info_tab").removeClass("hit");
	$(".product_info_tab img").attr("src",$(".product_info_tab img").attr("src").replace(/_on/g,"_off"));

	$(".product_talk").show();
	$(".product_talk_tab").addClass("hit");
	$(".product_talk_tab img").attr("src",$(".product_talk_tab img").attr("src").replace(/_off/g,"_on"));

}
function info_area_view() {
	$(".product_talk").hide();
	$(".product_talk_tab").removeClass("hit");
	$(".product_talk_tab img").attr("src",$(".product_talk_tab img").attr("src").replace(/_on/g,"_off"));

	$(".product_info").show();
	$(".product_info_tab").addClass("hit");
	$(".product_info_tab img").attr("src",$(".product_info_tab img").attr("src").replace(/_off/g,"_on"));
}
</script>
<script src="/pages/js/option_select.js" type="text/javascript"></script>

