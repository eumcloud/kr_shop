<?php
# LDD010
$app_mode = "popup";
include_once("inc.header.php");
	if ( !$_COOKIE["auth_comid"] ) {
		error_loc("/");
	}
?>

<div class="new_option_box">

	<!-- 상단 {-->
	<!-- 타이틀 {-->
	<div class="title_box">상품옵션 설정</div>
	<!--} 타이틀 -->

	<!-- 안내 및 기능 {-->
	<div class="option_data_top">
		<!-- 안내문구 {-->
		<div class="guide_text"><span class="ic_orange"></span><span class="orange">옵션 정보 입력 후 반드시 [<a href="javascript:document.frm_option.submit();alert('저장하였습니다.');">옵션정보저장</a>] 하시기 바랍니다.</span></div>
		<div class="guide_text"><span class="ic_blue"></span><span class="blue">공급가(추가), 판매가(추가) 항목은 입력 금액을 추가하는 방식으로 적용됩니다. <br/>예) 상품 판매가 : 10,000원 ,판매가(추가) : 5,000원 일 경우 옵션 적용 시 15,000원이 적용됩니다.</span></div>
		<!--} 안내문구 -->

		<!-- 옵션기능 {-->
		<div class="btn_box">
			<span class="shop_btn_pack"><a href="javascript:category_apply('1depth_add','');" class="medium blue" title="" >옵션추가</a></span>
			<span class="shop_btn_pack"><a href="javascript:document.frm_option.submit();alert('저장하였습니다.');" class="medium red" title="" >옵션정보저장</a></span>
		</div>
		<!--} 옵션기능 -->
	</div>
	<!--} 안내 및 기능 -->


	<!-- 엑셀업로드 # LDD011 { -->
	<!-- 안내 및 기능 처음에 닫혀있고 if_open 추가하면 나타나고 새로고침해도 닫으면 닫은채로 열리면 열린채로 그 상태로 되어있도록 {-->
	<div class="option_data_top_type2 if_open">
		<div class="txt_box">엑셀로 일괄등록하기 
			<span class="btn_ctrl">
				<span class="shop_btn_pack btn_close"><a href="javascript:option_excel_toggle('close');" class="medium white" title="" >엑셀등록 닫기</a></span>
				<span class="shop_btn_pack btn_open"><a href="javascript:option_excel_toggle('open');" class="medium gray" title="" >엑셀등록 열기</a></span>
			</span>
		</div>

		<form action="_product_option.excel_pro.php" name="wFrm" method="post" enctype="multipart/form-data">
			<input type="hidden" name="tran_type" value="ins_excel">
			<input type="hidden" name="pass_mode" value="<?=$pass_mode?>">
			<input type="hidden" name="pass_code" value="<?=$pass_code?>">
			<div class="wrapping">

				<!-- 엑셀파일 업로드폼 -->
				<div class="file_upload">
					<div class="input_file_box">
						<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled>
						<div class="fileDiv">
							<input type="button" class="buttonImg" value="파일찾기" />
							<input type="file" name="w_excel_file" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value" />
						</div>
					</div>
				</div>

				<!-- 안내문구 {-->
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">엑셀 일괄등록의 경우, 업로드를 함과 동시에 자동으로 정보저장이 됩니다.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">차수에 맞게 옵션을 등록할 수 있습니다. (<a href="/upfiles/normal/option<?php echo $pass_mode; ?>_sample.xls">샘플엑셀파일</a>을 다운받아서 업로드하세요.)</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">1차, 2차, 3차 옵션의 합산명칭이 같은 경우에는 수정, 다를 경우 추가하게 됩니다.</span></div>
				<div class="guide_text"><span class="ic_orange"></span><span class="orange">엑셀97~2003 버전 파일만 업로드가 가능합니다. 엑셀 2007이상 버전은(xlsx) 다른 이름저장을 통해 97~2003버전으로 저장하여 등록하세요.</span></div>
				<!--} 안내문구 -->

				<!-- 옵션기능 {-->
				<div class="btn_box">
					<span class="shop_btn_pack"><a href="javascript:_product_option_submit();" class="medium blue" title="" >엑셀 일괄등록</a></span>
					<span class="shop_btn_pack"><a href="/upfiles/normal/option<?php echo $pass_mode; ?>_sample.xls" class="medium gray" title="" >샘플파일 다운</a></span>
				</div>
				<!--} 옵션기능 -->
			</div>
		</form>
	</div>
	<!--} 안내 및 기능 -->
	<!-- } 엑셀업로드 # LDD011 -->
	<!--} 상단-->


	<!-- 옵션데이터 {-->
	<div id="span_option_3depth"></div>
	<!--} 옵션데이터 -->



	<!-- 버튼영역 {-->
	<div class="bottom_btn_area">
		<span class="lineup">
			<span class="shop_btn_pack"><a href="javascript:category_apply('1depth_add','');" class="large blue" title="" >옵션추가</a></span>
			<span class="shop_btn_pack"><a href="javascript:document.frm_option.submit();alert('저장하였습니다.');" class="large red" title="" >옵션정보저장</a></span>
		</span>
	</div>
	<!--} 버튼영역 -->
</div>

<script>
function category_apply(mode , uid) {

	if(document.frm_option) {

		document.frm_option.submit();
	}

	<?php
	// 차수 확인 --> 큰 오류가 발생할 수 있으므로 -- 차수 옵션은 한번 적용시 수정할 수 없게 봉인하여야 함
	if($pass_mode == "1depth") echo "var app_url = '_product_option1.ajax.php';";
	if($pass_mode == "2depth") echo "var app_url = '_product_option2.ajax.php';";
	if($pass_mode == "3depth") echo "var app_url = '_product_option3.ajax.php';";
	?>

	setTimeout(function() {
		$.ajax({
			url: app_url,
			cache: false,
			type: "POST",
			data: "app_mode=popup&pass_code=<?=$pass_code?>&pass_mode=" + mode + "&pass_uid=" + uid,
			success: function(data){
				if(data == "is_subcategory") {
					alert('하위 카테고리가 존재하여 삭제할 수 없습니다.');
				}
				else {

					$("#span_option_3depth").html(data);
				}
			}
		});
	}, 50);
}

// 엑셀저장 # LDD011
function _product_option_submit() {

	if(confirm("엑셀일괄등록을 실행하시겠습니까?")) {

		document.wFrm.submit();
	}
}

// 옵션 엑셀 업로드 동작 설정[C] # LDD011
function option_excel_toggle(Action) {

	option_excel_status(Action);
}

// 옵션엑셀업로드 펼치기 제어[M] # LDD011
function option_excel_status(Action) {

	var Target = $('.option_data_top_type2');
	
	if(!Action) Action = getCookie('option_excel_open');
	if(Action == 'open') Target.removeClass('if_open');
	else Target.removeClass('if_open').addClass('if_open');

	document.cookie = 'option_excel_open='+Action+';';
}

// 지정된 이름의 쿠키를 가져온다. # LDD011
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

// 옵션 SORTing
// _type - U : up , D :down
// _depth - 1, 2, 3 
// _uid  -옵션 고유번호
function f_sort(_type , _depth , _uid) {
	if( _type && _depth && _uid ) {

		$("input[name='pass_type']").val(_type);
		$("input[name='pass_depth']").val(_depth);
		$("input[name='pass_uid']").val(_uid);
		document.frm_option.submit();
		category_apply();
	}
	else {
		alert("잘못된 접근입니다. 관리자에게 문의하세요.");
	}
}

// 옵션 삽입
// _depth - 1, 2, 3 
// _uid  -옵션 고유번호
function f_insert(_depth , _uid) {
	if( _depth && _uid ) {
		$("input[name='pass_type']").val("insert");
		$("input[name='pass_depth']").val(_depth);
		$("input[name='pass_uid']").val(_uid);
		document.frm_option.submit();
		category_apply();
	}
	else {
		alert("잘못된 접근입니다. 관리자에게 문의하세요.");
	}
}


$(document).ready(function() {

	option_excel_status(); // # LDD011
	category_apply();
});
</script>

<?php include_once("inc.footer.php"); ?>