
<!-- 오늘마감 타이머 -->
<div class="todayclose_top">
	<b>마감까지 남은시간!!</b>
	<span class="time" id="remainHour">00</span><span class="unit">시간</span>
	<span class="time" id="remainMin">00</span><span class="unit">분</span>
	<span class="time" id="remainSec">00</span><span class="unit">초</span>
</div>

<!-- 1차 2차 카테고리 -->
<div class="page_title_area">
	<ul>
		<li>
			<!-- 1차 카테고리 -->
			<div class="select">
				<span class="ic_arrow"></span>
				<select name="category1" onchange="cate1_select()">
					<option value="">전체보기</option>
					<?
					$cate1_assoc = _MQ_assoc("select * from odtCategory where catedepth=1 and cHidden='no' order by cateidx asc");
					foreach($cate1_assoc as $cate1_key => $cate1_row) {
					?>
					<option value="<?=$cate1_row[catecode]?>" <?=$cate1_row[catecode] == $_GET[cate1] ? "selected" : NULL;?>><?=$cate1_row[catename]?></option>
					<?
					}
					?>
				</select>
			</div>
		</li>
		<li class="arrow"><img src="/m/images/slide_arrow2.png" alt="" /></li>
		<li>
			<!-- 2차 카테고리 -->
			<div class="select">
				<span class="ic_arrow"></span>
				<select name="category2" onchange="cate2_select()">
					<option value="">전체보기</option>
					<?
					if($_GET[cate1]) {
						$cate2_assoc = _MQ_assoc("select * from odtCategory where catedepth=2 and find_in_set('$_GET[cate1]',parent_catecode) and cHidden='no' order by cateidx asc");
						foreach($cate2_assoc as $cate2_key => $cate2_row) {
						?>
						<option value="<?=$cate2_row[catecode]?>" <?=$cate2_row[catecode] == $_GET[cate2] ? "selected" : NULL;?>><?=$cate2_row[catename]?></option>
						<?
						}
					}
					?>
				</select>
			</div>
		</li>
	</ul>
</div>
<!-- // 1차 2차 카테고리 -->

<script>
function cate1_select() {
	cate1 = $("select[name=category1] option:selected").val();
	location.href="/?pn=product.todayclose&cate1="+cate1;
}
function cate2_select() {
	cate1 = $("select[name=category1] option:selected").val();
	cate2 = $("select[name=category2] option:selected").val();
	location.href="/?pn=product.todayclose&cate1="+cate1+"&cate2="+cate2;
}
</script>

<?
	if($_GET[cate1]) { $cate1_name = _MQ("select catename from odtCategory where catedepth=1 and cHidden='no' and catecode='$_GET[cate1]'"); } else { $cate1_name[catename] = '전체'; }
	if($_GET[cate2]) { $cate2_name = _MQ("select catename from odtCategory where catedepth=2 and cHidden='no' and catecode='$_GET[cate2]'"); } else { $cate2_name[catename] = '전체'; }
	$cuid = $_GET[cate2]?$_GET[cate2]:$_GET[cate1];
?>

<!-- 타이틀 (셀렉트박스없이 div이대로 사용) -->
<div class="page_title_area">
	<span class="icon"></span>
	<strong><?=$cate1_name[catename]?><img src="/m/images/slide_arrow2.png" alt="화살표" /><?=$cate2_name[catename]?><em><!-- 카테고리 상품갯수 --></em></strong>
</div>

<?
$cuid			= $cuid;							// 카테고리
$display_area	= "todayclose_product_list_area"; 	// 노출시킬 class 명
$listmaxcount	= "N";							// 페이지당 노출갯수
$list_type		= "type2";						// 목록 유형
$pagenate_use	= "N";							// 페이징 사용여부
$hit_num_use	= "Y";							// 인기순위 아이콘 노출여부
$event_type		= "today_close"; 				// 이벤트요소
$order_field	= "pro_idx";					// 정렬 필드명
$order_sort		= "asc";						// 정렬 방식
$thema			= "";							// 테마 이름
?>


<section id="container">

		<!-- 아이템리스트 -->
		<div class="item_list item_list_area <?=$display_area?>">
		<?
		include dirname(__FILE__)."/ajax.product.list.php";
		?>
		</div>
		<!-- //아이템리스트 -->

</section>


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
