<?PHP
	// -- 1차 카테고리 배열 적용 ---
	$arr_parent01 = array();
	$cres = _MQ_assoc("select catecode , catename from odtCategory where cHidden='no' and catedepth='1' order by cateidx asc ");
	foreach( $cres as $k=>$v ){
		$arr_cate01[$v[catecode]] = $v[catename];
	}
	// -- 1차 카테고리 배열 적용 ---
?>


<div style="float:left;display:inline-table;" >
	1차분류 : <?=_InputSelect( "pass_cate01", array_keys($arr_cate01) , "" , "id='pass_cate01' onchange='category_select2(1);' " , array_values($arr_cate01) , "-선택-") ?>&nbsp;&nbsp;&nbsp;
	2차분류 : <?=_InputSelect( "pass_cate02", array() , $app_depth2 , "id='pass_cate02' onchange='category_select2(2);' " , array() , "-선택-") ?>&nbsp;&nbsp;&nbsp;
	3차분류 : <?=_InputSelect( "pass_cate03", array() , "", " " , array() , "-선택-") ?>
</div>
<div style="float:left; padding-left:10px;" >
	<span class="shop_btn_pack" style='margin-right:10px'><a href="#none" class="small blue" onclick="category_add();">선택 카테고리추가</a></span>
</div>


<div style="clear:both; padding-top:10px;" ></div>
<div ID="_product_cateogry_list">
<!-- 상품카테고리 목록 노출 -->
<?PHP
	$_mode2 = "list";
	include_once("_product.inc_category_pro.php");
?>
</div>
<div style="clear:both; padding-bottom:5px;" ></div>



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
            url: "../include/categorysearch.pro.php",
			cache: false,
			dataType: "json",
			type: "POST",
            data: "pass_parent03_no_required=<?=$pass_cate03_no_required?>&pass_parent01=" + $("[name=pass_cate01]").val() + "&pass_parent02=" + $("[name=pass_cate02]").val()+"&pass_idx=" + _idx,
            success: function(data){
                if(_idx == 2) {
					//$("select[name=pass_cate02]").val(apppass_cate03); // 현재정보 적용
					$("select[name=pass_cate03]").find("option").remove().end().append('<option value="">-선택-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=pass_cate03]").append(option_str);
				}
				else if(_idx == 1){
					$("select[name=pass_cate02]").find("option").remove().end().append('<option value="">-선택-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=pass_cate02]").append(option_str);
					$("select[name=pass_cate03]").find("option").remove().end().append('<option value="">-선택-</option>');
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