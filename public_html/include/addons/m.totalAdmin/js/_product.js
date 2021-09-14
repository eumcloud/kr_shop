	// - 배송기능적용 ---
    function setup_delivery_chk(){
        if(document.frm.setup_delivery.checked == true) {
            document.frm.del_price.disabled = false;
            document.frm.del_limit.disabled = false;
			$("#setup_delivery_apply").css("display" , "block");
        }
        else {
            document.frm.del_price.disabled = true;
            document.frm.del_limit.disabled = true;
			$("#setup_delivery_apply").css("display" , "none");
        }
    }
	// - 배송기능적용 ---

	// - 3단 카테고리 선택 ---
	function category_select(_idx) {

		var  app_p1 = $("select[name=pass_parent01]").val();
        var  app_p2 = $("select[name=pass_parent02]").val();
		
		$.ajax({
            url: "/include/categorysearch.pro.php",
			cache: false,
			dataType: "json",
			type: 'POST',
            data: "pass_parent01=" + app_p1 + "&pass_parent02=" + app_p2 + "&pass_idx=" + _idx ,
			success: function(data){
				if(_idx == 2) {
					//$("select[name=pass_parent02]").val(app_cuid); // 현재정보 적용
					$("select[name=cateCode]").find("option").remove().end().append('<option value="">-3차분류-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=cateCode]").append(option_str);

					// - 1차 카테고리 선택 시 - 테마 적용 ---
					appProductThema(app_p2);

				}
				else if(_idx == 1){
					$("select[name=pass_parent02]").find("option").remove().end().append('<option value="">-2차분류-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=pass_parent02]").append(option_str);
					$("select[name=cateCode]").find("option").remove().end().append('<option value="">-3차분류-</option>');

					// - 1차 카테고리 선택 시 - 상품노출형태에 따른 항목 노출 적용 ---
					//appProductType(app_p1);

				}
			}
		});
	}
	// - 3단 카테고리 선택 ---

	// - 업체 정산 형태 ---
    function saleType(frm) {
        if(frm.comSaleType[0].checked == true) {
            document.getElementById('comSaleTypeTr1').style.display='';
            document.getElementById('comSaleTypeTr2').style.display='none';
        } else {
            document.getElementById('comSaleTypeTr2').style.display='';
            document.getElementById('comSaleTypeTr1').style.display='none';
        }
    }
	// - 업체 정산 형태 ---

	// - 관련상품지정 ---
    function delField(objTemp) {
        objTemp.value='';
    }
    function relationWin(url , code) {
        window.open( url + '?code='+ code +'&relation_procode='+document.frm.p_relation.value,'relation', 'width=600, height=700, scrollbars=yes');
    }
    function relationHelp() {
        alert('위 관련상품등록/수정 버튼을 이용하여 입력하시기 바랍니다.   ');
    }
	// - 관련상품지정 ---


	// - 옵션설정 체크시 옵션창 노출여부 ---
	var option_check = function() {
		if( $(".option_type_chk").filter(function() {if (this.checked) return this;}).val() == "nooption") {
			$(".option_area").hide();
		} else {
			$(".option_area").show();
		}
	}
	$(".option_type_chk").click(option_check);
	// - 옵션설정 체크시 옵션창 노출여부 ---


	// - 옵션열기 ---
	function option_popup(pass_code) {
		pass_mode = $(".option_type_chk").filter(function() {if (this.checked) return this;}).val();
		if(pass_mode == "nooption" || pass_mode == undefined) {
			alert("1차~3차 옵션을 선택하세요");
			return;
		}
		window.open("_product_option.form.php?pass_mode="+pass_mode+"&pass_code=" + pass_code ,"","width=1064,height=500,scrollbars=yes");
	}
	// - 옵션열기 ---


	/* 추가옵션 패치 2014-03-24 */
	function addoption_popup(code) {
		window.open("_product_addoption.popup.php?pass_code=" + code,"addoption","width=1064,height=500,scrollbars=yes");
	}
	/* 추가옵션 패치 끝 */



   $(document).ready(function() {
		option_check();
   });

