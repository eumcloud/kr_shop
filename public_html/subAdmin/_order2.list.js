

//택배사 일괄선택
function expressSetFun(frm) {
    res = frm.expressSet.value;
    if(!res) {
        alert('택배사를 선택하세요.');
        frm.expressSet.focus();
        return;
    }
    obj = frm.elements['expressname[]'];
    obj2 = frm.elements['setTmp[]'];
    for(i=0;i<obj.length;i++) {
        if(obj2[i].value == '') obj[i].value = res;
    }
}











	// 엑셀저장
	function saveExcel(fileTemp) {
		//선택된주문번호를 가져옴
		var	checkitems = $("input[name=OpUid\\[\\]]:checked");
		if(checkitems.length == 0) {
			alert ("저장하고자하는 주문내역을 선택하세요.");
			return;
		}
		frm = document.OderAllDelete;
		orgAction = frm.action
		frm.action = fileTemp;
		frm.submit();
		frm.action = orgAction;
	}
	// - 검색엑셀 ---
	 function search_excel_send(fileTemp) {
		 if($('input[name=_seachcnt]').val()*1 > 0 ){
			$("input[name=_mode]").val("search_excel");
			frm = document.OderAllDelete;
			orgAction = frm.action
			frm.action = fileTemp;
			frm.submit();
			frm.action = orgAction;
		 }
		 else {
			 alert('1건 이상 검색시 엑셀다운로드가 가능합니다.');
		 }
	 }
	 // - 검색엑셀 ---
	

	// - 쿠폰번호생성 ---
	function createCpNum(uniqid){
		var siteCode =  uniqid.toUpperCase();
		var errcnt = 0;     //오류횟수
		var okcnt=0;        //생성횟수
		var passnum = 0;    //넘어감횟수

		//선택된주문번호를 가져옴
		var	checkitems = $("input[name=OpUid\\[\\]]:checked");
		if(checkitems.length == 0) {
			alert ("먼저 쿠폰번호를 생성하고자 하는 주문 항목을 선택하세요.   ");
			return;
		}
		//데이터오류확인(쿠폰상품의 경우 이미지와/주의사항이 반드시 등록되어야한다)
		var war1 = $("input:checked[name=OpUid[]][warning=Y]").length;
		if(war1 > 0) { alert("쿠폰이미지가 혹은 주의사항이 등록이되어있지 않은 상품이 있습니다."); return; }

		//선택된 주문번호의 쿠폰번호를 생성
		checkitems.each(function() {
			var code = $(this).val();   //주문번호를 쿠폰번호로 사용한다.
			var idx = 0;

			//선택된 주문번호의 쿠폰번호를 생성한다.인덱스는 000 형태로사용(최대값999)
			$("input[name='expressnum[]'][op_uid='"+code+"']").each(function() {
				var offset = idx;
				if($(this).val()=="") { 
					if($(this).attr("ordertype")=="product") {
						passnum = passnum + 1;
					}
					else {
						$(this).val( shop_couponnum_create); //쿠폰번호가 없는경우에만 생성한다.
						okcnt = okcnt + 1;
					}
				} else {
					if($(this).attr("ordertype")=="product") {
						passnum = passnum + 1;
					} 
					else {
						errcnt = errcnt + 1;
					}
				}
				idx++;
			});
		});
		alert(
			"번호생성완료\n\n" + okcnt + "건의 쿠폰번호가 생성되었습니다.\n" + 
			errcnt + "건의 쿠폰번호는 이미 존재하여 생성되지 않았습니다.\n" + 
			passnum + "건은 배송정보이므로 쿠폰번호가 생성되지 않았습니다."
		);
	}
	// - 쿠폰번호생성 ---

	// - 랜덤번호생성 ---
	function shop_couponnum_create() {
		var _chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		var _limit = 5;
		var _code = "";
		for( var i=0; i<3; i++ ) {
			if( i != 0 ) { _code += "-" ; }
			var _list = _chars.split('');
			var _len = _list.length, j = 0;
			do {
				j++;
				var index = Math.floor(Math.random() * _len);
				_code += _list[index];
			} while(j < _limit);
		}
		return _code ;
	}
	// - 랜덤번호생성 ---

	// - 전체선택 / 해제 ---
	function selectAll() {
		if($("input[name=allchk]").is(':checked')){
			$('.class_uid').attr('checked',true);
		}
		else {
			$('.class_uid').attr('checked',false);
		}
	}
	// - 전체선택 / 해제 ---




	// -  쿠폰번호 일괄발송 // 송장번호 일괄발급 ---
	function express(force) {
		var title1 = "먼저 발송(배송)처리를 하고자 하는 주문 항목을 선택하세요.   ";
		var title2 = "일괄처리할 주문이 100건 이상인 경우에는\n\n원활한 쿠폰발송을 위하여 1~2분 간격으로\n\n" + 
					 "100건씩 나누어 처리하시기 바랍니다.\n\n쿠폰번호(배송번호)가 없는 주문은 발행되지 않습니다.\n\n" +
					 "배송상품의 경우 택배회사가 없다면 발행되지 않습니다.\n\n주문을 발행처리 하시겠습니까?";
		if(force=="Y") {    //재발행일경우
			var title1 = "먼저 재발송(재배송)처리를 하고자 하는 주문 항목을 선택하세요.   ";
			var title2 = "일괄처리할 주문이 100건 이상인 경우에는\n\n원활한 발송을 위하여 1~2분 간격으로\n\n" +
					"100건씩 나누어 처리하시기 바랍니다.\n\n쿠폰번호(배송번호)가 없는 주문은 발행되지 않습니다.\n\n" +
					"배송상품의 경우 택배회사가 없다면 발행되지 않습니다.\n\n주문을 재발행 하시겠습니까?";
		}

		var	checkitems = $("input[name=OpUid\\[\\]]:checked");
		if(checkitems.length == 0) {
			alert (title1);
			return;
		}

		frm = document.OderAllDelete;
		if(!confirm(title2)) return false;
		orgAction = frm.action;
		orgTarget = frm.target;

		frm.action = "_order2.express.php?force=" + force;
		frm.target = "common_frame";
		frm.submit();
		frm.action = orgAction;
		frm.target = orgTarget;
	}
	// - 쿠폰번호발송 ---


	// 사용 , 미사용 여부
	function f_check2(uid , type) {
		common_frame.location.href= ("_order2.pro.php?_mode=coupon_use&uid=" + uid + "&type="+type+"&_PVSC=" + $("input[name=_PVSC]").val() );
	}

	// 배송정보엑셀적용
	function excel_insert(){
		frm = document.OderAllDelete;
		orgAction = frm.action;
		orgTarget = frm.target;
		frm.action = "_order2.delivery_excel_form.php";
		frm.target = "_self";
		frm.submit();
		frm.action = orgAction;
		frm.target = orgTarget;
	}
