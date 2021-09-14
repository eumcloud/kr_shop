<?
	// 상품코드 체크
	if(!$_GET[pcode]) error_msg("상품 코드가 없습니다.");

	// - 임시 옵션 삭제 ---
	if($_COOKIE["AuthShopCOOKIEID"]) _MQ_noreturn("delete from odtTmpProductOption where otpo_mid='". $_COOKIE["AuthShopCOOKIEID"] ."'");

	// 상품정보 추출
	$row_product = get_product_info(${pcode});

	// 상품 수량 체크
	if($row_product[stock] < 1) error_msg("품절된 상품입니다.");

	// 타이틀 - sns 적용
	$app_sns_title = str_replace("'","`",$row_product[name]);

	// facebook app id
	$app_sns_facebook_id = $row_setup[Facebook_id];

	// URL - sns 적용
	$app_sns_url = "http://".$_SERVER[HTTP_HOST]."/?pcode=" . $row_product[code];

	// 매장설명 - sns 적용
	$app_sns_content = str_replace("'","`",$row_product[short_comment]);

	// 매장 로고
	$banner_info = info_banner("site_icon_basic",1,"data");
	$app_sns_logo = replace_image(IMG_DIR_BANNER.$banner_info[0][b_img]);

	// 상품 이미지
	if($row_product['prolist_img2']) 
		$app_sns_pro_img = replace_image(IMG_DIR_PRODUCT.$row_product['prolist_img2']);
	else 
		$app_sns_pro_img = "";


	// 배송비 정책
	if(!$row_product[del_price]) {
		$delivery_info = "무료배송";
	} else {
		if($row_product[del_limit]) {
			$delivery_info = "배송비 <em>".number_format($row_product[del_price])."원</em> (".number_format($row_product[del_limit])."원 이상 무료배송)";
		} else {
			$delivery_info = "배송비 <em>".number_format($row_product[del_price])."원</em>";
		}
	}

	// 구매제한정책
	if(!$row_product[buy_limit]) {
		$buy_limit_info = "구매제한없음";
	} else {
		$buy_limit_info = "주문당 구매제한 : ".$row_product[buy_limit]."개 ";
	}

	/* ---------------------- 상품 하단 작은 아이콘 --------------------------*/
	unset($product_small_icon_value,$product_small_icon_array,$product_small_icon);
	$product_small_icon_array[today_open]     = $row_product[sale_date] == date('Y-m-d') ? "<img src='/m/images/ic_topen.gif' alt='오늘오픈' /> " : NULL;	// 오늘 오픈상품 
	if($row_product[sale_type] == 'A') $product_small_icon_array[today_open] = NULL;
	$product_small_icon_array[free_delivery]  = !$row_product[del_price] ? "<img src='/m/images/ic_free.gif' alt='무료배송' /> " : NULL;	// 무료배송
	$product_small_icon_array[now_use]        = $row_product[isNow] == "Y" ? "<img src='/m/images/ic_use.gif' alt='바로사용' /> " : NULL;	// 바로사용
	$product_small_icon_array[today_end]      = $row_product[sale_enddate] == date('Y-m-d') ? "<img src='/m/images/ic_tend.gif' alt='오늘마감' /> " : NULL;	// 오늘 마감상픔
	if($row_product[sale_type] == 'A') $product_small_icon_array[today_end] = NULL;
	$product_small_icon_array[today_delivery] = 1 ? "<img src='/m/images/ic_ship.gif' alt='오늘배송' /> " : NULL;	// 추후작업 : 오늘배송 조건
	$product_small_icon_array = array_filter($product_small_icon_array);	// 빈값 제거
	foreach($product_small_icon_array as $icon_value) $product_small_icon_value .= "<span>".$icon_value."</span>";
	$product_small_icon = sizeof($product_small_icon_array) ? "<div class='ic_tag' style='display:'>".$product_small_icon_value."</div>" : NULL;	// 상품 하단 아이콘
	/* ----------------------  // 상품 하단 작은 아이콘 --------------------------*/

	// text 값 추출
	$row_product = array_merge($row_product , _text_info_extraction( "odtProduct" , $row_product[serialnum] ));

	// 좌표값
	$row_product[coordinate] = $row_product[com_mapx].", ".$row_product[com_mapy];

	$row_customer = _MQ("select * from odtMember where id = '".$row_product[customerCode]."' and userType ='C'");	

?>            

	<!-- 컨텐츠 -->
	<section id="container_sub">
		
		<!-- 한번감싸기 -->
		<div class="view_area">

		
			<!-- 기본정보 -->
			<div class="view_basic">
				<div class="photo"><img src="<?=replace_image(IMG_DIR_PRODUCT.$row_product[main_img])?>" alt="<?=$row_product[name]?>"></div>
				<div class="info">
					<!-- 설명글 없으면 숨김 -->
					<span class="explain"><?=$row_product[short_comment]?></span>
					<div class="name"><?=$row_product[name]?></div>
				</div>
				<div class="price_info">
					<div class="discount"><?=$row_product[price_per]?><em>%</em></div>
					<div class="price">
						<span class="before"><?=number_format($row_product[price_org])?><em>원</em></span>
						<span class="after"><?=number_format($row_product[price])?><em>원</em></span>
					</div>
					<div class="buyinfo"><b><?=number_format($row_product[saleCnt])?></b>개 구매</div>
				</div>

				<div class="timer_info">
					<span class="icon timer"></span>
					남은시간 <b id="remainDay">0</b>일 <b id="remainHour">0</b>:<b id="remainMin">0</b>:<b id="remainSec">0</b>
				</div>
			</div>
			<!-- // 기본정보 -->

			<!-- sns 공유 -->
			<div class="share_sns">
				<span class="bubble">SNS공유</span>
				<a href="#none" onclick="kakaotalk_share('<?=$app_sns_title?>' , '<?=$row_setup[site_name]?>')"  class="icon ic_kk"></a>
				<a href="#none" onclick="executeKakaoStoryLink();"  class="icon ic_ks"></a>
				<a href="#none" onclick="postToFeed()"  class="icon ic_fb"></a>
				<a href="#none" onclick="sendTwitter('<?=urlencode($app_sns_title)?>')" class="icon ic_tw"></a>
			</div>
			<!-- // sns 공유 -->

			<!-- 옵션선택 -->
			<div class="view_option">

		<?
		// 품절이면
		if( $row_product[stock] < 1 ) {
		?>

			<input type=hidden name=option_select_expricesum ID='option_select_expricesum' value='<?=$row_product[price]?>'>
			<input type=hidden name=option_select_type id='option_select_type' value='nooption'>

			<div class="opt_list">
				<ul>
					<li>
						<span class='updown' style='position:auto!important;right:auto!important' ><a href='#none' onclick="pro_cnt_up()" class='btn_up'><span class='icon ic_plus'></span></a><input type='text' name='option_select_cnt' id='option_select_cnt' class='po_cnt_val' value='0' readonly /><a href='#none' onclick="pro_cnt_down()" class='btn_down'  ><span class='icon ic_minus'></span></a></span>
						<span class='sum' style='float:right!important'>총 상품합계 <b id='smart_po_total_price'><?=number_format($row_product[price])?></b>원							
						</span>
					</li>
				</ul>
			</div>
		<? } else {  // 추가옵션 패치 2014-03-24 // 추가옵션을 출력하기 위해 아래 po_depth_max로 옵션 존재 여부 파악 하도록 변경

			function addoption_print($row_code){
			$sque = " select oto_uid , oto_poptionname, oto_cnt,oto_poptionprice, MAX(oto_depth) as oto_depth_max from odtProductOption where oto_pcode='" . $row_code . "'";
    		$sres_tmp = _MQ_assoc($sque);

			if($sres_tmp[0][oto_depth_max]) {

			$add_options = _MQ_assoc("select * from odtProductAddoption where pao_pcode='{$row_code}' and pao_depth='1'"); 
				echo "<div id='container_shop'>";
				foreach($add_options as $k=>$v) { ?>
					<div class="select">
					<span class="ic_arrow" style="margin-top:0px!important"><span class="icon" style="top:auto!important"></span></span>
						<select name='_add_option_select_<?=$k+1?>' id="add_option_select_<?=$k+1?>_id" class='add_option add_option_chk'>
							<option value=''>옵션을 선택하세요 ( <?=$v[pao_poptionname]?> )</option>
							<? $add_sub_options = _MQ_assoc("select * from odtProductAddoption where pao_pcode='{$row_code}' and pao_depth='2' and pao_parent='{$v[pao_uid]}' order by pao_uid");
							foreach($add_sub_options as $key=>$value) { ?>
							<option value="<?=$value[pao_poptionname]?>" data-uid="<?=$value[pao_uid]?>"><?=$value[pao_poptionname]?></option>
							<? } ?>
						</select>
					</div>
				<? } ?>
				</div>
			<!-- 추가옵션 수량 //-->
			<input type="hidden" name="addoption_cnt" value="<?=sizeof($add_options)?>">

		<? }} ?>

		<?

	    // 옵션정보 불러오기
	    $sque = " select oto_uid , oto_poptionname, oto_cnt,oto_poptionprice  from odtProductOption where oto_pcode='" . $row_product[code] . "' and oto_depth='1' order by oto_uid ";
	    $sres = _MQ_assoc($sque);

		// 옵션타이틀
		$option1_title = (trim($row_product[option1_title]) ? trim($row_product[option1_title]) : ($row_product[option_type_chk] == "1depth" ? "상세옵션을 선택해 주세요" : "1차옵션을 선택하세요") );
		$option2_title = (trim($row_product[option2_title]) ? trim($row_product[option2_title]) : "상위 옵션을 먼저 선택하세요");
		$option3_title = (trim($row_product[option3_title]) ? trim($row_product[option3_title]) : "상위 옵션을 먼저 선택하세요");

	    foreach( $sres as $k=>$sr){
				
				if($row_product[option_type_chk] == "1depth")
					$str_option .= "<option value='".$sr[oto_uid]."'>".$sr[oto_poptionname]." (잔여:".  ($sr[oto_cnt] > 0 ? number_format($sr[oto_cnt])  : "품절").") / +" . number_format($sr[oto_poptionprice]) . "원</option>";
				else
					$str_option .= "<option value='".$sr[oto_uid]."'>".$sr[oto_poptionname]."</option>";
	    }

		if( $row_product[option_type_chk] == "1depth"  && sizeof($sres) > 0){  	// 1차 옵션이 있으면..
		?>
			<div id="container_shop">
				<div class="select">
					<span class="ic_arrow" style="margin-top:0px!important"><span class="icon" style="top:auto!important"></span></span>
					<select name=_option_select1 onchange="option_select('<?=$row_product[code]?>')" ID='option_select1_id'><option value=''><?=$option1_title?></option><?=$str_option?></select>
				</div>
				
			</div>
			<?=addoption_print($row_product[code])?>
			<!-- 옵션열었을때 나옴 -->
			<span onclick="option_select_add('<?=$row_product[code]?>')" class='add_opt'><img src='/m/images/add_opt.gif' alt='옵션추가'></span>
			<div ID='span_seleced_list' class='opt_list'>
				<li>
					<span class='option_name' style='text-align:center;width:100%!important'>구매하실 상품 옵션을 선택해 주시기 바랍니다.</span>
				</li>
			</div>
		<?
    } else if( $row_product[option_type_chk] == "2depth"  && sizeof($sres) > 0){

		?>

			<div id="container_shop">
				<div class="select">
					<span class="ic_arrow" style="margin-top:0px!important"><span class="icon" style="top:auto!important"></span></span>
					<select name=_option_select1 onchange="option_select(1,'<?=$row_product[code]?>')" ID='option_select1_id'><option value=''><?=$option1_title?></option><?=$str_option?></select>
				</div>
				<span ID='span_option2'>
					<div class="select">
					<span class="ic_arrow" style="margin-top:0px!important"><span class="icon" style="top:auto!important"></span></span>
					<select disabled><option value=''><?=$option2_title?></option></select>
					</div>
				</span>
				
			</div>
			<?=addoption_print($row_product[code])?>
			<!-- 옵션열었을때 나옴 -->
			<span onclick="option_select_add('<?=$row_product[code]?>')" class='add_opt'><img src='/m/images/add_opt.gif' alt='옵션추가'></span>
			<div ID='span_seleced_list' class='opt_list'>
				<li>
					<span class='option_name' style='text-align:center;width:100%!important'>구매하실 상품 옵션을 선택해 주시기 바랍니다.</span>
				</li>
			</div>
		<?
    } else if( $row_product[option_type_chk] == "3depth"  && sizeof($sres) > 0){

		?>
			<div id="container_shop">
				<div class="select">
					<span class="ic_arrow" style="margin-top:0px!important"><span class="icon" style="top:auto!important"></span></span>
					<select name=_option_select1 onchange="option_select(1,'<?=$row_product[code]?>')" ID='option_select1_id'><option value=''><?=$option1_title?></option><?=$str_option?></select>
				</div>
				<span ID='span_option2'>
					<div class="select">
					<span class="ic_arrow" style="margin-top:0px!important"><span class="icon" style="top:auto!important"></span></span>
					<select disabled><option value=''><?=$option2_title?></option></select>
					</div>
				</span>
				<span ID='span_option3'>
					<div class="select">
					<span class="ic_arrow" style="margin-top:0px!important"><span class="icon" style="top:auto!important"></span></span>
					<select disabled><option value=''><?=$option3_title?></option></select>
					</div>
				</span>
			</div>
			<?=addoption_print($row_product[code])?>
			<!-- 옵션열었을때 나옴 -->
			<span onclick="option_select_add('<?=$row_product[code]?>')" class='add_opt'><img src='/m/images/add_opt.gif' alt='옵션추가'></span>
			<div ID='span_seleced_list' class='opt_list'>
				<li>
					<span class='option_name' style='text-align:center;width:100%!important'>구매하실 상품 옵션을 선택해 주시기 바랍니다.</span>
				</li>
			</div>

		<?

		} else {	// 옵션이 없으면..
		?>
			<input type=hidden name=option_select_expricesum ID='option_select_expricesum' value='<?=$row_product[price]?>'>
			<input type=hidden name=option_select_type id='option_select_type' value='nooption'>
			<input type=hidden name=product_stock id=product_stock value='<?=$row_product[stock]?>'>

			<div class="opt_list">
				<ul>
					<li>
						<?if($row_product[stock] > 0) {?>
						<span class="name">구매수량</span>
						<span class="updown">
							<a href="javascript:pro_cnt_up()"><span class="icon ic_plus"></span></a>
							<input type="text" name="option_select_cnt" value="1" ID='option_select_cnt' readonly>
							<a href="javascript:pro_cnt_down('')" ><span class="icon ic_minus"></span></a>
						</span>
						<?}else{?>
						<span class='option_name' style='text-align:center;width:100%!important'>품절된 상품입니다.</span>
						<?}?>
					</li>
				</ul>
			</div>
			<script>
			$(document).ready(function() {
				update_sum_price();
			});
			</script>			
		<?
		}} // 추가옵션 패치 2014-03-14 else를 닫기위해 }} 추가
		?>
			</div>
			<!-- // 옵션선택 -->
			
			<div class="sum">총 상품합계 <b id="option_select_expricesum_display">0</b>원</div>
				
		</div>
		
		<!--버튼영역 -->
		<div class="view_btn_area">
			<a  href="#none" onclick="<?=$row_product[stock] < 1 ? "app_soldout()" : "app_submit('".$row_product[code]."','cart')"?>" class="btn_cart">장바구니</a>
			<a href="<? if(!is_login()) { ?>javascript:alert('회원가입 후 이용하실 수 있습니다.');<? } else { ?>/m/product.wish.pro.php?pcode=<?=$row_product[code]?><? } ?>" target='common_frame'  rel='external'  class="btn_wish">찜하기</a>
			<a href="#none" onclick="<?=$row_product[stock] < 1 ? "app_soldout()" : "app_submit('".$row_product[code]."','order')"?>" class="btn_order">구매하기</a>
		</div>
		<!-- //버튼영역 -->

		<!-- 관련상품 -->
		<?include_once("inc.relative.php");?>
		<!-- 관련상품 -->

		<!-- 탭메뉴 -->
		<a name="info"></a>
		<div class="view_tab">
			<ul>
				<li class="on"><a href="#info" rel='external'>상품정보</a></li>
				<li class="off"><a href="#position" rel='external'>위치안내</a></li>
				<li class="off"><a href="#qna" rel='external'>상품문의</a></li>
			</ul>
		</div>
		<!-- // 탭메뉴 -->

		<!-- 해당탭내용 -->
		<div class="view_conts">


<?PHP
	// -- 정보제공고시 추출 ---
	$psque = "select * from odtProductReqInfo where pri_value <> '' and pri_pcode='" . $pcode . "' order by pri_uid asc  ";
	$psres = _MQ_assoc($psque);
	if(sizeof($psres) > 0 ) {
		echo "<a class='conts_title'><span class='tx'>전자상거래 등에서의 상품 정보 제공 고시</span><span class='icon ic_open'></span></a><div class='conts_default'><ul>";
		foreach($psres as $psk=>$psv){ echo "<li><b>". stripslashes($psv[pri_key]) ."</b> : ". stripslashes($psv[pri_value]) ."</li>"; }
		echo "</ul></div>";
	}
	// -- 정보제공고시 추출 ---
?>

			<!-- 기본정보 -->
			<a class="conts_title"><span class="tx">제품사용정보</span><span class="icon ic_open"></span></a>
			<div class="conts_default">
				<ul>
					<?=stripslashes(htmlspecialchars_decode($row_product[comment_proinfo]))?>
				</ul>				
			</div>

			<!-- 기본정보 -->
			<a class="conts_title"><span class="tx">업체이용정보</span><span class="icon ic_open"></span></a>
			<div class="conts_default">
				<ul>
					<?=stripslashes(htmlspecialchars_decode($row_product[comment_useinfo]))?>
				</ul>				
			</div>			
			<!-- 아래는 처음에는 내용닫음!! -->

			<!-- 2.상품정보 -->
			<a class="conts_title"><span class="tx">상품상세정보</span><span class="icon ic_close"></span></a>
			<div class="conts_image">
				<?=stripslashes(htmlspecialchars_decode($row_product[comment2]))?>
			</div>
			
		</div>
		<!-- // 해당탭내용 -->



		<!-- 탭메뉴 -->
		<a name="position"></a>
		<div class="view_tab">
			<ul>
				<li class="off"><a  href="#info" rel='external'>상품정보</a></li>
				<li class="on"><a  href="#position" rel='external'>위치안내</a></li>
				<li class="off"><a  href="#qna" rel='external'>상품문의</a></li>
			</ul>
		</div>
		<!-- // 탭메뉴 -->

<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
  #map_canvas { height: 100% }
</style>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
	function initialize() {

		var latlng = new google.maps.LatLng(<?=$row_product[coordinate]?>);
		var myOptions = {
			zoom: 15,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		var marker_0 = new google.maps.Marker({
			position: new google.maps.LatLng(<?=$row_product[coordinate]?>),
			map : map,
			title : '<?=$row_product[name]?>'
		});


		var infowindow_0 = new google.maps.InfoWindow({ content : '<?=$row_customer[cName]?>' });

		infowindow_0.open(map,marker_0);

	}
	jQuery(document).ready(function($) {
		initialize();
	});	
</script>

		<!-- 해당탭내용 -->
		<article class="view_conts">
			
			<!-- 위치안내 -->
			<a class="conts_title"><span class="tx">위치안내</span></a>
			<div class="conts_default">

				<div class="map"  id="map_canvas" style="width:100%; height:250px;<?=!$row_product[com_mapx] || !$row_product[com_mapy] ? ";display:none" : NULL;?>"></div>

				<div class="map_info">
					<ul>
						<li class="name"><?=$row_customer[cName]?></li>
						<li><b>주소</b><?=$row_product[com_juso]?></li>
						<li><b>연락처</b><?=phone_print($row_customer[tel1],$row_customer[tel2],$row_customer[tel3])?></li>
					</ul>
				</div>
		
			</div>
		</article>
		<!-- // 해당탭내용 -->



		<!-- 탭메뉴 -->
		<a name="qna"></a>
		<div class="view_tab">
			<ul>
				<li class="off"><a  href="#info" rel='external'>상품정보</a></li>
				<li class="off"><a  href="#position" rel='external'>위치안내</a></li>
				<li class="on"><a href="#qna" rel='external'>상품문의</a></li>
			</ul>
		</div>
		<!-- // 탭메뉴 -->


		<!-- 해당탭내용 -->
		<article class="view_conts">
			<? include dirname(__FILE__)."/product.talk.form.php";?>
		</article>
		<!-- // 해당탭내용 -->



	</section>
	<!-- 컨텐츠 -->

<script>
$(document).ready(function(){
	var endDttm = '<?=str_replace("-","",$row_product[sale_enddate]).$row_product[sale_enddateh].$row_product[sale_enddatem]?>';
	endDttm += '00';
	var startDttm = '<?=date('YmdHis')?>';
	var endDate = new Date(endDttm.substring(0,4),endDttm.substring(4,6) -1 ,endDttm.substring(6,8),endDttm.substring(8,10),endDttm.substring(10,12),endDttm.substring(12,14));
	
	var startDate = new Date(startDttm.substring(0,4),startDttm.substring(4,6) -1,startDttm.substring(6,8),
							startDttm.substring(8,10),startDttm.substring(10,12),startDttm.substring(12,14));
	periodDate = (endDate - startDate)/1000;

	if(endDate > startDate){
		remainTime(periodDate);
	}else{
        $('#remainDay').html('00');
        $('#remainHour').html('00');
        $('#remainMin').html('00');
        $('#remainSec').html('00');
    }
});

var count = 0;

function remainTime(periodDate){

	var day  = Math.floor(periodDate / 86400);
	var hour = Math.floor((periodDate - day * 86400 )/3600); 
	var min  = Math.floor((periodDate - day * 86400 - hour * 3600)/60);
	var sec  = Math.floor(periodDate - day * 86400 - hour * 3600 - min * 60); 

	if(day > 0) {
		(day<10) ? $('#remainDay').html('0'+day) : $('#remainDay').html(day);
	}
	else {
		$('#remainDay').html('00');
	}
	if(day > 0 || (day == 0 && hour > 0)) {
		(hour<10) ? $('#remainHour').html('0'+hour) : $('#remainHour').html(hour);
	}
	else {
		$('#remainHour').html('00');
	}
	
	if(day > 0 || (day == 0 && hour > 0) || (day == 0 && hour == 0 && min > 0)) {
		(min<10) ? $('#remainMin').html('0'+min) : $('#remainMin').html(min);
	}
	else {
		$('#remainMin').html('00');
	}
	if(day > 0 || (day == 0 && hour > 0) || (day == 0 && hour == 0 && min > 0) || (day == 0 && hour == 0 && min == 0 && sec > 0)) {
		(sec<10) ? $('#remainSec').html('0'+sec) : $('#remainSec').html(sec);
	}
	else {
		$('#remainSec').html('00');
	}
	
	periodDate = periodDate -1;



	setTimeout(function(){remainTime(periodDate)}, 1000);
	return;
}
		function option_select_check() {

			var sum = 0;
			obj = $(".po_cnt_val");
			for(i=0;i<obj.length;i++) {
				sum += parseInt(obj[i].value);
			}
			if(sum > 0) return true;
			return false;
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
		// - 상품 구매하기 - 

		// 상품 옵션 갯수 조절
		function smart_op_cnt(type,uid,code) {

			cnt = parseInt($("#po_cnt_"+uid).val());

			$.ajax({
					url: "/m/option_select_pro.php",
					cache: false,
					type: "POST",
					data: "_type="+type+"&uid="+uid+"&code="+code+"&cnt="+cnt,
					success: function(data){
						if(data == "error1") alert("잘못된 접근입니다.");
						else if(data == "error3") alert("선택 옵션의 재고량이 부족합니다.");
						else {
							if(type == "up") $("#po_cnt_"+uid).val(++cnt);
							if(type == "down" && cnt > 0) $("#po_cnt_"+uid).val(--cnt);
							$("#smart_po_total_price").html(data);
						}
					}
			});

		}

		function pro_cnt_up() {

			<?if($pro_info[p_stock] < 1) {?>alert('품절된 상품입니다.');return;<?}?>

			cnt = $("#option_select_cnt").val()*1;
			$("#option_select_cnt").val(cnt+1);
			
			update_sum_price();
		}
		function pro_cnt_down() {

			<?if($pro_info[p_stock] < 1) {?>alert('품절된 상품입니다.');return;<?}?>

			cnt = $("#option_select_cnt").val()*1;
			if(cnt > 1) $("#option_select_cnt").val(cnt-1);

			update_sum_price();
		}		
		function update_sum_price() {
			var sumprice = 0;
			sumprice = String($("#option_select_expricesum").val()*$("#option_select_cnt").val());
			if(sumprice == "NaN") sumprice = "0";
			$("#smart_po_total_price").html(sumprice.comma());
		}

		function status_box_on_off(idx) {

			className = $(".view_conts .title"+idx+" .icon").attr("class");
			patt=/ic_open/g;
			if(patt.test(className)) { // 열린상태이면
				$(".view_conts .title"+idx+" .icon").removeClass("ic_open");
				$(".view_conts .title"+idx+" .icon").addClass("ic_close");
				$(".view_conts .content"+idx+"").hide();				
			} else {
				$(".view_conts .title"+idx+" .icon").removeClass("ic_close");
				$(".view_conts .title"+idx+" .icon").addClass("ic_open");
				$(".view_conts .content"+idx+"").show();
			}

		}
</script>

<!-- 페이스북 초기화 -->
<div id="fb-root" style="display: none;"></div>
<script>
	window.fbAsyncInit = function() {
		FB.init({
			appId				: '<?=$app_sns_facebook_id?>', // App ID
			status				: true, // check login status
			cookie				: true, // enable cookies to allow the server to access the session
			xfbml				: true,  // parse XFBML
			oauth				: true
		});
	};
  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/ko_KR/all.js";
     ref.parentNode.insertBefore(js, ref);
	 d.getElementsByTagName('head')[0].appendChild(js);
   }(document));

</script>
<script type="text/javascript" src="/include/js/kakao.link.js"></script>
<script>
		// 카카오톡
		function kakaotalk_share(message , appname){
			kakao.link("talk").send({   
				msg: message,
				url: '<?=$app_sns_url?>',
				appid: '<?=$_SERVER[HTTP_HOST]?>',
				appver: '2.0',
				appname: appname,
				type: 'link'
			});
		}

		// 카카오 스토리
		function executeKakaoStoryLink(){
			kakao.link("story").send({   
				post : "<?=$app_sns_url?>",
				appid : "<?=$_SERVER['HTTP_HOST']?>",
				appver : "1.0",
				appname : "<?=$row_setup[site_name]?>",
				urlinfo : JSON.stringify({
					title:"<?=$app_sns_title?>",
					desc:"<?=$app_sns_content?>", 
					imageurl:["<?=$app_sns_pro_img ? $app_sns_pro_img : $app_sns_logo?>"], 
					type:"article"
				})
			});
		}

		function cate_change(obj) {
			location.href="/?pn=product.list&cuid="+obj.value;
		}

		function sendTwitter(title) {
				var wp = window.open("/m/append.shorten_url.php?type=twitter&pcode=<?=$pcode?>&text=" + encodeURIComponent(title), 'twitter', '');
				if ( wp ) {
						wp.focus();
				}
		}
		function sendMe2Day(title,tag) {
				var wp = window.open("/m/append.shorten_url.php?type=me2day&pcode=<?=$pcode?>&_body=" + encodeURIComponent(title) + "&_tags=" + encodeURIComponent(tag), 'me2Day', '');
				if ( wp ) {
						wp.focus();
				}
		}
		// 페이스북
		function postToFeed() {
			// calling the API ...
			var obj = {
				method: 'feed',
				link: '<?=$app_sns_url?>',
				picture: "<?=$app_sns_pro_img ? $app_sns_pro_img : $app_sns_logo?>",
				name: '<?=$app_sns_title?>',
				description: '<?=$app_sns_content?>'
			};
			function callback(response) {
				document.getElementById('msg').innerHTML = "Post ID: " + response['post_id'];
			}
			FB.ui(obj, callback);
		}

</script>

<script src="/m/js/option_select.js" type="text/javascript"></script>