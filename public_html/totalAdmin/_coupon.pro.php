<?PHP

	include "inc.php";


	// - 입력수정 사전처리 ---
	if( in_array($_mode , array("add" , "modify"))) {
		// --사전 체크 ---
		$coName = nullchk($coName , "쿠폰명을 입력하시기 바랍니다.");
		$coPrice = nullchk(rm_str($coPrice) , "쿠폰를 입력하시기 바랍니다.");
		$coLimit = nullchk($coLimit , "쿠폰만료일을 선택하시기 바랍니다.");
		// --사전 체크 ---
	}
	// - 입력수정 사전처리 ---




	// - 모드별 처리 ---
	switch( $_mode ){


		// -- 추가 ---
		case "add":
			$pointIDArray = nullchk($pointIDArray , "지급유저를 선택하시기 바랍니다.");
			$ex	=	explode(",",$pointIDArray);
			foreach( array_filter($ex) as $k=>$v ) {
				if( $v ){
					$sque = "
						insert odtCoupon set
							coID	= '".$v."',
							coName	= '".$coName."',
							coPrice	= '".$coPrice."',
							coUse	= 'N',
							coType	= '이벤트쿠폰',
							coLimit	= '".$coLimit."',
							coRegidate	= now()
					";
					_MQ_noreturn($sque);
				}
			}
			error_loc( "_coupon.list.php?" . enc('d' , $_PVSC) );
			break;
		// -- 추가 ---


		// -- 수정 ---
		case "modify":
			$sque = "
				update odtCoupon set
					coName	=	'".$coName."',
					coPrice	=	'".$coPrice."',
					coLimit	=	'".$coLimit."'
				where 
					coNo='${coNo}'
			";
			_MQ_noreturn( $sque );
			error_loc("_coupon.form.php?_mode=modify&coNo=${coNo}&_PVSC=${_PVSC}");
			break;
		// -- 수정 ---


		// -- 삭제 ---
		case "delete":
			_MQ_noreturn("delete from odtCoupon where coNo='$coNo' ");
			error_loc( "_coupon.list.php?" . enc('d' , $_PVSC) );
			break;
		// -- 삭제 ---


		// 선택삭제
		case "select_delete":
			if(sizeof($chk_id) == 0 ) {
				error_msg("선택된 쿠폰이 없습니다.");
			}
			_MQ_noreturn("delete from odtCoupon where coNo in ('".implode("','" , $chk_id)."') ");

			error_loc_msg("_coupon.list.php?".enc('d' , $_PVSC) , "정상적으로 삭제하였습니다.");
			break;

	}
	// - 모드별 처리 ---

?>
