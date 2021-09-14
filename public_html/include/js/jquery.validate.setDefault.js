	
		/* ---------- jquery validator 경고창 띄우기 (jquery validate 공통) ---------- */
		jQuery.validator.setDefaults({
			onkeyup:false,
			onclick:false,
			onfocusout:false,
			showErrors:function(errorMap, errorList){
				if(errorList != "") {	// 에러가 있을때만 alert 호출
					alert(errorList[0].message);
					$("input[name='"+$(errorList[0].element).attr('name')+"']").focus();
				}
			}
		});
		/* ---------- // jquery validator 경고창 띄우기 (jquery validate 공통) ---------- */