	// - 옵션 선택 체크 ---
	function option_select(depth,code) {

		// 추가옵션 패치 2014-03-24
		if( depth == "1" ) {
			var tmp_addoption_cnt = $("input[name=addoption_cnt]").val(); // 추가옵션 수량
			var tmp_cnt = 0; // 체크된 추가옵션 수량
			$(".add_option_chk").each(function(index){
				if( $(this).val() ) {
					tmp_cnt ++;
				}
			});
			if( tmp_addoption_cnt != tmp_cnt ){ // 추가옵션수량 만큼 선택
				//alert("앞선 옵션을 선택해주세요.");
				//return false;
			}
		}
		// 추가옵션 패치 끝

		var depth_next = depth * 1 + 1;
		var select_var = $("#option_select"+depth+"_id option:selected").val();
		var app_var = "";
		if( $("select").is("#option_select1_id") == true ){
			app_var = $("#option_select1_id option:selected").val() ;
		}
		if(select_var) {
			$.ajax({
				url: "./pages/option_select.php",
				cache: false,
				type: "POST",
				data: { "code" : code , "depth" : depth , "uid" : select_var , "uid1" : app_var } ,
				success: function(data){
					$("#span_option" + depth_next ).html(data);
				}
			});
		}
	}
	// - 옵션 선택 체크 ---


	// - 옵션 추가 ---
	function option_select_add(code) {
		var _trigger = true;
		if( $("select").is("#option_select1_id") == true && !($("#option_select1_id option:selected").val() != "") ){
			_trigger = false;
		}
		if( $("select").is("#option_select2_id") == true && !($("#option_select2_id option:selected").val() != "") ){
			_trigger = false;
		}
		if( $("select").is("#option_select3_id") == true && !($("#option_select3_id option:selected").val() != "") ){
			_trigger = false;
		}
		if( _trigger ) {
			$.ajax({
				url: "./pages/option_select_add.php",
				cache: false,
				type: "POST",
				data: {"code" : code , "uid1" : $("#option_select1_id option:selected").val() , "uid2" : $("#option_select2_id option:selected").val() , "uid3" : $("#option_select3_id option:selected").val() },
				success: function(data){
					if(data == "error1") {
						alert('잘못된 접근입니다.');
					}
					else if(data == "error2") {
						alert('이미 선택한 옵션입니다.');
					}
					else if(data == "error3") {
						alert('선택 옵션의 재고량이 부족합니다.');
					}
					else if(data == "error4") {
						alert('재고량이 부족합니다.');
					}
					else {
						$("#span_seleced_list").html(data);

						// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
						$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
					}
				}
			});
		}
		else {
			alert('옵션을 선택해 주세요');
		}
	}
	// - 옵션 추가 ---

	// - 옵션 추가 - 옵션 없을 경우 ---
	function nooption_select_add(code) {
		$.ajax({
			url: "./pages/option_select_add.php",
			cache: false,
			type: "POST",
			data: { "code" : code , "option_type_chk" : "none" , "add_uid1" : $("#add_option_select_1_id option:selected").attr('data-uid') , "add_uid2" : $("#add_option_select_2_id option:selected").attr('data-uid') , "add_uid3" : $("#add_option_select_3_id option:selected").attr('data-uid') , "add_uid54" : $("#add_option_select_4_id option:selected").attr('data-uid') , "add_uid5" : $("#add_option_select_5_id option:selected").attr('data-uid') , "add_uid6" : $("#add_option_select_6_id option:selected").attr('data-uid') , "add_uid7" : $("#add_option_select_7_id option:selected").attr('data-uid') , "add_uid8" : $("#add_option_select_8_id option:selected").attr('data-uid') , "add_uid9" : $("#add_option_select_9_id option:selected").attr('data-uid') , "add_uid10" : $("#add_option_select_10_id option:selected").attr('data-uid') },
			success: function(data){
				if(data == "error1") {
					alert('잘못된 접근입니다.');
				}
				else if(data == "error2") {
					alert('이미 선택한 옵션입니다.');
				}
				else if(data == "error4") {
					alert('재고량이 부족합니다.');
				}
				else {
					$("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
				}
			}
		});
	}
	// - 옵션 추가 - 옵션 없을 경우 ---

	// - 옵션 삭제 ---
	function option_select_del(uid,code) {
		$.ajax({
			url: "./pages/option_select_del.php",
			cache: false,
			type: "POST",
			data: { "code" : code , "uid" : uid },
			success: function(data){
				if(data == "error1") {
					alert('잘못된 접근입니다.');
				}
				else if(data == "error4") {
					alert('재고량이 부족합니다.');
				}
				else {
					$("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
				}
			}
		});
	}
	// - 옵션 삭제 ---

	// - 옵션 수량수정 ::: _type : up/down ---
	function option_select_update(_type , uid,code) {
		$.ajax({
			url: "./pages/option_select_update.php",
			cache: false,
			type: "POST",
			data: { "_type" : _type , "code" : code , "uid" : uid , "cnt" : $("#input_cnt_" + uid).val() },
			success: function(data){
				if(data == "error1") {
					alert('잘못된 접근입니다.');
				}
				else if(data == "error3") {
					alert('선택 옵션의 재고량이 부족합니다.');
				}
				else if(data == "error4") {
					alert('재고량이 부족합니다.');
				}
				else if(data == "error5") {
					alert('구매제한 개수가 초과 되었습니다.');
				}
				else {
					$("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
				}
			}
		});
	}
	// - 옵션 수량수정 ---

	function pro_cnt_up(max_cnt) {
		cnt = $("#option_select_cnt").val()*1;

        if(max_cnt>0){
            if(cnt>=max_cnt){
                alert(max_cnt + "개 까지 구매가능합니다.");
                return false;
             }
        }

		if(cnt >= ($("#product_stock").val()*1)) 
			alert("재고량이 부족합니다.");
		else 		
			$("#option_select_cnt").val(cnt+1);
		
		update_sum_price();
	}
	function pro_cnt_down() {

		cnt = $("#option_select_cnt").val()*1;
		if(cnt > 1) $("#option_select_cnt").val(cnt-1);

		update_sum_price();
	}       
	function update_sum_price() {
		var sumprice = 0;
		sumprice = String($("#option_select_expricesum").val()*$("#option_select_cnt").val());
		if(sumprice == "NaN") sumprice = "0";
		$("#option_select_expricesum_display").html(sumprice.comma());
	}

// - 추가옵션 추가 ---
	function add_option_select_add(code , uid) {
		if( uid ) {
			$.ajax({
				url: "./pages/add_option_select_add.php",
				cache: false,
				type: "POST",
				data: {"code" : code , "uid" : uid },
				success: function(data){
					if(data == "error1") {
						alert('잘못된 접근입니다.');
					}
					else if(data == "error2") {
						alert('이미 선택한 옵션입니다.');
					}
					else if(data == "error3") {
						alert('선택 옵션의 재고량이 부족합니다.');
					}
					else if(data == "error4") {
						alert('재고량이 부족합니다.');
					}
					else if(data == "error6") {
						alert('상세옵션을 먼저 선택해 주시기 바랍니다.');
					}
					else {
						$("#span_seleced_list").html(data);

						// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
						$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
					}
				}
			});
		}
	}
	// - 추가옵션 추가 ---

	// - 추가옵션 수량수정 ::: _type : up/down ---
	function add_option_select_update(_type , uid,code) {
		$.ajax({
			url: "./pages/add_option_select_update.php",
			cache: false,
			type: "POST",
			data: { "_type" : _type , "code" : code , "uid" : uid , "cnt" : $("#input_cnt_add_" + uid).val() },
			success: function(data){
				if(data == "error1") {
					alert('잘못된 접근입니다.');
				}
				else if(data == "error3") {
					alert('선택 옵션의 재고량이 부족합니다.');
				}
				else if(data == "error4") {
					alert('재고량이 부족합니다.');
				}
				else if(data == "error5") {
					alert('구매제한 개수가 초과 되었습니다.');
				}
				else {
					$("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
				}
			}
		});
	}
	// - 추가옵션 수량수정 ---