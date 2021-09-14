<!-- 오늘마감 탑영역 -->
<div class="todayclose_top">
	<div class="layout_fix">
		
		<div class="timer">
			<span class="h"  id="remainHour">00</span>
			<span class="m"  id="remainMin">00</span>
			<span class="s"  id="remainSec">00</span>
		</div>
		<img src="/pages/images/todayciose_top.png" alt="오늘마감" />
		
	</div>
</div>

<div class="todayclose_category">

	<a href="/?pn=product.todayclose" class="btn_all">전체보기<span class="out"></span></a>
	
	<?
	$depth1_assoc = _MQ_assoc("select * from odtCategory where catedepth=1 and cHidden='no'  order by cateidx asc");
	foreach($depth1_assoc as $depth1_key => $depth1_row) {

		unset($depth2_html);
		$depth2_assoc = _MQ_assoc("select * from odtCategory where catedepth=2 and parent_catecode ='".$depth1_row[catecode]."' and cHidden='no' order by cateidx asc");
		foreach($depth2_assoc as $depth2_key => $depth2_row) 
			$depth2_html .= "<a href='/?pn=product.todayclose&cuid=".$depth2_row[catecode]."'>".$depth2_row[catename]."</a>";

		echo "
			<div class='ctg_box'>
				<div class='over'>
					".$depth2_html."
				</div>
				<a href='/?pn=product.todayclose&cuid=".$depth1_row[catecode]."' class='btn_ctg'>".$depth1_row[catename]."<span class='out'></span></a>
			</div>
			";
	}
	?>

</div>
<div style="clear:both;"></div> <!-- 마우스오버때문에 overflow:hidden 줄수없어서 추가됨 -->
<!-- 오늘마감 탑영역 -->




<?
$cuid			= $_GET[cuid];							// 카테고리
$display_area	= "todayclose_product_list_area"; 	// 노출시킬 class 명
$listmaxcount	= 999;							// 페이지당 노출갯수
$list_type		= "type1";						// 목록 유형
$pagenate_use	= "N";							// 페이징 사용여부
$hit_num_use	= "Y";							// 인기순위 아이콘 노출여부
$event_type		= "today_close"; 				// 이벤트요소
$order_field	= "pro_idx";					// 정렬 필드명
$order_sort		= "asc";						// 정렬 방식
$thema			= "";							// 테마 이름
?>
<!-- 리스트상단 -->
<div class="sub_main_product">
	<div class="title"><?=$category_total_info[depth1_catename] ? $category_total_info[depth1_catename] : "전체보기"?>
			<?if($category_total_info[depth2_catename]) {?>
			<em>&gt;</em><?=$category_total_info[depth2_catename]?>
			<?}?>
	</div>
	<div class="arrange" style="display:none;">
		<a  href="#none" class="product_list_tab hit">추천순</a>
	</div>
</div>
<!-- // 리스트상단 -->

<!-- 상품리스트 @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
<div class="main_item">
	<div class="layout_fix">
		<!-- <span class="<?=$display_area?>"> -->
			<?
			include dirname(__FILE__)."/ajax.product.list.php";
			?>			
		<!-- </span>		 -->
		
		<!-- 모든상품리스트 공통 
		<div class="contents_none" style="margin:70px 0; clear:both;"><b>검색된 상품이 없습니다.<br/>빠른 시일내에 업데이트 하도록 하겠습니다 </b></div>
		-->

	</div>
</div>
<!-- // 상품리스트 @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->

<script>
$(document).ready(function(){
	var endDttm = "<?=date('Ymd2359')?>";
	endDttm += '99';
	var startDttm = '<?=date('YmdHis')?>';
	var endDate = new Date(endDttm.substring(0,4),endDttm.substring(4,6) -1 ,endDttm.substring(6,8),endDttm.substring(8,10),endDttm.substring(10,12),endDttm.substring(12,14));
	
	var startDate = new Date(startDttm.substring(0,4),startDttm.substring(4,6) -1,startDttm.substring(6,8),
							startDttm.substring(8,10),startDttm.substring(10,12),startDttm.substring(12,14));
	periodDate = (endDate - startDate)/1000;

	if(endDate > startDate){
		remainTime(periodDate);
	}else{
//        $('#remainDay').html('00');
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

	// if(day > 0) {
	// 	(day<10) ? $('#remainDay').html('0'+day) : $('#remainDay').html(day);
	// }
	// else {
	// 	$('#remainDay').html('00');
	// }
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
</script>