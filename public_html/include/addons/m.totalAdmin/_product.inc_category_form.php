<?PHP

	// 상품정보 - $row 로 넘겨옴


	// -- 1차 카테고리 배열 적용 ---
	$arr_parent01 = array();
	$cres = _MQ_assoc("select catecode , catename from odtCategory where cHidden='no' and catedepth='1' order by cateidx asc ");
	foreach( $cres as $k=>$v ){
		$arr_cate01[$v[catecode]] = $v[catename];
	}
	// -- 1차 카테고리 배열 적용 ---
?>

				<ul>
					<li class="opt ess">분류 선택</li>
					<li class="value">
						<div class="select">
							<span class="shape"></span>
							<?=_InputSelect( "pass_cate01", array_keys($arr_cate01) , "" , "id='pass_cate01' onchange='category_select2(1);' " , array_values($arr_cate01) , "-1차분류-") ?>
						</div>
						<div class="select">
							<span class="shape"></span>
							<?=_InputSelect( "pass_cate02", array() , $app_depth2 , "id='pass_cate02' onchange='category_select2(2);' " , array() , "-2차분류-") ?>
						</div>
						<div class="select">
							<span class="shape"></span>
							<?=_InputSelect( "pass_cate03", array() , "", " " , array() , "-3차분류-") ?>
						</div>
						<span class="button_pack"><a href="#none" onclick="category_add();" class="btn_md_blue">선택 카테고리추가</a></span>
						<?=_DescStr_mobile_totaladmin("선택하신 2차분류에 의해 테마를 선택할 수 있으며, 동일한 테마가 다른 2차분류에 있을 경우 중복 적용됩니다.")?>
					</li>
				</ul>

				<ul>
					<li class="opt ">선택한 분류</li>
					<li class="value">
						<div class="multi_info" style="clear:both;">
							<dl ID="_product_cateogry_list">
								<?php
									$_mode2 = "list";
									include_once("_product.inc_category_pro.php");
								?>
							</dl>
						</div>
					</li>
				</ul>





<?php

	// - 카테고리별 테마 ---
	$ex_thema = explode("," , $row[thema]);

	// - 선택 2차 카테고리 추출 ---
	$arr_depth2 = array();
	$que = "
		select ct2.catecode as ct2_catecode
		from odtProductCategory as pct 
		left join odtCategory as ct3 on (ct3.catecode = pct.pct_cuid and ct3.catedepth=3)
		left join odtCategory as ct2 on (substring_index(ct3.parent_catecode , ',' ,-1) = ct2.catecode and ct2.catedepth=2)
		where 
			pct.pct_pcode='". $code ."'
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		$arr_depth2[$v['ct2_catecode']]++;
	}
	// - 선택 2차 카테고리 추출 ---


	// - 2차 테마 추출 ---
	$arr_cate2 = array();
	$res = _MQ_assoc("select catecode,lineup , catename from odtCategory where cHidden='no' and catedepth='2' order by cateidx asc");
	foreach( $res as $k=>$v ){
		$arr_cate2[$v['catecode']] = array("lineup"=>$v['lineup'] , "catename"=>$v[catename]);
	}
	// - 2차 테마 추출 ---

	if( sizeof($arr_cate2) > 0 ){
		foreach($arr_cate2 as $k=>$v){
			if( $v['lineup'] ) {
				$ex_cate_thema = explode("," , $v['lineup']);
				echo "
					<ul class='cls_thema cls_category_uid_". $k ."' ". (in_array($k , array_keys($arr_depth2)) ? "" : " style='display:none;' ") .">
						<li class='opt '>". $v['catename'] ."</U><br>테마선택</li>
						<li class='value'>" . _InputCheckbox_totaladmin( "_thema" , array_values($ex_cate_thema) , array_values($ex_thema) , "" , array() ) . "</li>
					</ul>
				";
			}
		}
	}
	// - 카테고리별 테마 ---
?>





<SCRIPT LANGUAGE="JavaScript">
	// - 카테고리 목록 ---
	function category_list() {
		$.ajax({
			url: "_product.inc_category_pro.php",
			type: "POST",
			data: "_mode2=list&code=<?=$code?>",
			success: function(data){
				$("#_product_cateogry_list").html(data);
				appProductThema();
			}
		});
	}
	// - 카테고리 목록 ---
	// - 카테고리 삭제 ---
	function category_delete(catecode) {
		if( confirm('정말 삭제하시겠습니까?') ){
			if( catecode ){
				$.ajax({
					url: "_product.inc_category_pro.php",
					type: "POST",
					data: "_mode2=delete&code=<?=$code?>&catecode=" + catecode ,
					success: function(data){
						category_list();
					}
				});
			}
			else {
				alert("3차 카테고리를 선택하시기 바랍니다.");
			}
		}
	}
	// - 카테고리 삭제 ---
	// - 카테고리 추가 ---
	function category_add() {
		var app_catecode = $("select[name=pass_cate03]").val();
		if( app_catecode ){
			$.ajax({
				url: "_product.inc_category_pro.php",
				type: "POST",
				data: "_mode2=add&code=<?=$code?>&catecode=" + app_catecode,
				success: function(data){
					category_list();
				}
			});
		}
		else {
			alert("3차 카테고리를 선택하시기 바랍니다.");
		}
	}
	// - 카테고리 추가 ---
	// - 카테고리 선택 ---
	function category_select2(_idx) {
        $.ajax({
            url: "/include/categorysearch.pro.php",
			cache: false,
			dataType: "json",
			type: "POST",
            data: "pass_parent03_no_required=<?=$pass_cate03_no_required?>&pass_parent01=" + $("[name=pass_cate01]").val() + "&pass_parent02=" + $("[name=pass_cate02]").val()+"&pass_idx=" + _idx,
            success: function(data){
                if(_idx == 2) {
					//$("select[name=pass_cate02]").val(apppass_cate03); // 현재정보 적용
					$("select[name=pass_cate03]").find("option").remove().end().append('<option value="">-3차분류-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=pass_cate03]").append(option_str);
				}
				else if(_idx == 1){
					$("select[name=pass_cate02]").find("option").remove().end().append('<option value="">-2차분류-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=pass_cate02]").append(option_str);
					$("select[name=pass_cate03]").find("option").remove().end().append('<option value="">-1차분류-</option>');
				}
            }
		});
	}
	// - 카테고리 선택 ---

	// - 카테고리 선택에 따른 - 테마 적용 ---
	function appProductThema(){
		$(".cls_thema").css("display" ,  "none" ); // 전체테마 닫기
		$("input[name^=chk_cate2]").each( function( index ) {
			$(".cls_category_uid_" + $(this).val() ).css("display" ,  "" ); // 선택 테마 열기
		});
		
	}
	// - 카테고리 선택에 따른 - 테마 적용 ---
</SCRIPT>