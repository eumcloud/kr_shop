<?PHP

	include "inc.php";


	// - 입력수정 사전처리 ---
	if( in_array($_mode , array("add" ,"select" ,"search", "modify"))) {
		// --사전 체크 ---
		$pointTitle = nullchk($pointTitle , "제목을 입력하시기 바랍니다.");
		$pointPoint = nullchk(rm_str($pointPoint) , "포인트를 입력하시기 바랍니다.");
		$redRegidate = nullchk($redRegidate , "포인트 적립일을 선택하시기 바랍니다.");
		// --사전 체크 ---
	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){


		// -- 추가 ---
		case "add" :
		case "select" :
		case "search":
		     
			$pointIDArray = nullchk($pointIDArray , "지급유저를 선택하시기 바랍니다.");
			$ex	=	explode(",",$pointIDArray);
			foreach( array_filter($ex) as $k=>$v ) {
				if( $v ){
					$sque = "
						insert odtPointLog set
							pointID				=	'".trim($v)."',
							pointTitle		=	'".$pointTitle."',
							pointPoint		=	".$pointPoint.",
							pointStatus		=	'N',
							redRegidate		=	'".$redRegidate."',
							pointRegidate	=	now()
					";
					_MQ_noreturn($sque);
				}
			}

			// 지급예정일에 따른 포인트 지급처리 적용(전체 적용)
			_point_add_all();

			error_loc( "_point.list.php?" . enc('d' , $_PVSC) );
			break;
		// -- 추가 ---


		// -- 수정 ---
		case "modify":
			$sque = "
				update odtPointLog set
					pointTitle		=	'".$pointTitle."',
					pointPoint		=	".$pointPoint.",
					redRegidate		=	'".$redRegidate."'
				where 
					pointNo='${pointNo}'
			";
			_MQ_noreturn( $sque );

			// 지급예정일에 따른 포인트 지급처리 적용(전체 적용)
			_point_add_all();

			error_loc("_point.form.php?_mode=modify&pointNo=${pointNo}&_PVSC=${_PVSC}");
			break;
		// -- 수정 ---


		// -- 삭제 ---
		case "delete":
			$res = _MQ("select * from odtPointLog where pointNo='$pointNo' ");
			if($res[pointStatus] == "Y"){
				_MQ_noreturn(" update odtMember set point = IF( point > ".$res[pointPoint]." , point - ".$res[pointPoint]." , 0 ) WHERE id='".$res[pointID]."' ");
			}
			_MQ_noreturn("delete from odtPointLog where pointNo='$pointNo' ");
			error_loc( "_point.list.php?" . enc('d' , $_PVSC) );
			break;
		// -- 삭제 ---


		// 선택삭제
		case "select_delete":
			if(sizeof($chk_id) == 0 ) {
				error_msg("선택된 포인트가 없습니다.");
			}
		   $res = _MQ_assoc("select * from odtPointLog where pointNo in ('". implode("','" , array_values($chk_id)) ."') ");
			foreach( $res as $k=>$v ){
					if($v[pointStatus] == "Y"){
							_MQ_noreturn(" update odtMember set point = IF( point > ".$v[pointPoint]." , point - ".$v[pointPoint]." , 0 ) WHERE id='".$v[pointID]."' ");
					}
					_MQ_noreturn("delete from odtPointLog where pointNo='" . $v[pointNo] . "' ");
			}

			error_loc_msg("_point.list.php?".enc('d' , $_PVSC) , "정상적으로 삭제하였습니다.");
			break;

	}
	// - 모드별 처리 ---

?>