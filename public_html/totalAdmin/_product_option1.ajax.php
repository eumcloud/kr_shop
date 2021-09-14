<?php
# LDD010
include_once("inc.php");

// string 만들기
switch( $pass_mode ){
	// 1차옵션 삭제
	case "1depth_del":

		// 순번재조정
		$r = _MQ(" select * from odtProductOption where oto_uid='" . $pass_uid . "' ");
		_MQ_noreturn(" update odtProductOption set oto_sort=oto_sort-1  where oto_pcode='" . $r['oto_pcode'] . "' and oto_depth='". $r['oto_depth'] ."' and oto_parent='" . $r['oto_uid'] . "' and oto_sort > '". $r['oto_sort'] ."' ");

		_MQ_noreturn("delete from odtProductOption where oto_uid='{$pass_uid}'"); // 삭제
	break;

	// 1차 옵션 추가
	case "1depth_add":

		// 순번추출
		$r = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='1' ");
		$max_sort = $r['max_sort'] + 1;
		_MQ_noreturn("
			insert odtProductOption set
				oto_pcode='{$pass_code}',
				oto_poptionname='',
				oto_depth='1',
				oto_sort='". $max_sort ."'
		");// 항목추가 - 1차
	break;
}

//1차 추출
$save_chk = 0;
$que = " select * from odtProductOption where oto_pcode='{$pass_code}' and oto_depth='1' order by oto_sort asc , oto_uid asc ";
$res = _MQ_assoc($que);
if(sizeof($res) <= 0) die('<!-- 내용없을경우 모두공통 --><div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 옵션정보가 없습니다.</div></div><!-- // 내용없을경우 모두공통 -->');
?>
<div class="option_data_box">
	<form action="_product_option.pro.php" name="frm_option" id="frm_option" target="common_frame"  method="post">
		<input type="hidden" name="pass_code" value="<?php echo $pass_code; ?>">
		<input type="hidden" name="pass_type" value="">
		<input type="hidden" name="pass_depth" value="">
		<input type="hidden" name="pass_uid" value="">

		<ul>
			<?php
			foreach($res as $k=>$r) {

				if($r['oto_poptionname'] == "" || !$r['oto_poptionname']) $save_chk ++;
			?>
			<li class="depth_1<?=$r['oto_view'] == 'Y'?null:' if_option_hide';?>">
				<div class="opt">
					<div class="txt">1차 옵션</div>
				</div>

				<div class="value">
					<!-- 이름입력 -->
					<span class="wrapping_name">
						<span class="txt_box">1차 옵션명</span>
						<input type="text" name="oto_info[<?=$r['oto_uid']?>][oto_poptionname]" class="input_design" placeholder="1차 옵션명입력" value="<?=$r['oto_poptionname']?>" />
					</span>
					<!-- 값 입력 -->
					<span class="wrapping_num">
						<span class="txt_box">공급가(추가)</span>
						<input type="text" name="oto_info[<?=$r['oto_uid']?>][oto_poptionpurprice]" class="input_design input_price" placeholder="0" value="<?=$r['oto_poptionpurprice']?>" />
					</span>
					<span class="wrapping_num">					
						<span class="txt_box">판매가(추가)</span>
						<input type="text" name="oto_info[<?=$r['oto_uid']?>][oto_poptionprice]" class="input_design input_price" placeholder="0" value="<?=$r['oto_poptionprice']?>" />
					</span>
					<span class="wrapping_num">
						<span class="txt_box">수량(재고)</span>
						<input type="text" name="oto_info[<?=$r['oto_uid']?>][oto_cnt]" class="input_design input_number" placeholder="0" value="<?=$r['oto_cnt']?>" />
					</span>
					<span class="wrapping_num">
						<span class="txt_box">판매</span>
						<input type="text" class="input_design input_number" placeholder="0" value="<?=$r['oto_salecnt']?>" readonly="readonly" disabled />
					</span>
					<!-- 순서변경 버튼 -->
					<span class="btn_updown">
						<span class="shop_btn_pack"><a href="javascript:f_sort('U' , '1', '<?=$r['oto_uid']?>' );" class="small white" title="위로" ><span class="shapeup"></span></a></span>
						<span class="shop_btn_pack"><a href="javascript:f_sort('D' , '1', '<?=$r['oto_uid']?>' );" class="small white" title="아래로" ><span class="shapedw"></span></a></span>
					</span>
					<!-- 삭제버튼 -->
					<span class="btn_delete"><span class="shop_btn_pack"><a href="javascript:category_apply('1depth_del', '<?=$r['oto_uid']?>');" class="small gray" title="해당 옵션을 삭제합니다." >삭제</a></span></span>
					<!-- 끼워넣기버튼 -->
					<span class="btn_add"><span class="shop_btn_pack"><a href="javascript:f_insert('1', '<?=$r['oto_uid']?>');" class="small white" title="바로 아래로 옵션을 추가합니다.">끼워넣기</a></span></span>
					<!-- 숨기기체크 -> 숨김시 li. 추가 -->
					<label class="btn_hide" title="<?=$r['oto_view'] == 'Y' ? "옵션 숨기기" : '옵션 보이기';?>"><input type="checkbox" class="btn_hide_input" name="oto_info[<?=$r['oto_uid']?>][oto_view]" value="1" <?=$r['oto_view'] == 'Y' ? "checked" : NULL;?> /></label>
				</div>
			</li>
			<?php } ?>
		</ul>
		<input type="hidden" name="no_save_num" value="<?php echo $save_chk; ?>">
	</form>
</div>

<script>
// 옵션 숨기기 효과 {
$(function() {

	//category_apply();
    $('.btn_hide_input').on('click', function() {

        var checked = $(this).is(':checked');
        if(checked === true) {

            $(this).closest('li').removeClass('if_option_hide');
            $(this).closest('label').attr('title', '옵션 숨기기');
            $('.ui-tooltip-content').html('옵션 숨기기');
        }
        else {

            $(this).closest('li').removeClass('if_option_hide').addClass('if_option_hide');
            $(this).closest('label').attr('title', '옵션 보이기');
            $('.ui-tooltip-content').html('옵션 보이기');
        }

        category_apply();
    });
});
// } 옵션 숨기기 효과
</script>