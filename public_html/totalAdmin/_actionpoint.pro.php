<?PHP

	include "inc.php";


	// - 입력수정 사전처리 ---
	if( in_array($_mode , array("add" , "modify"))) {
		// --사전 체크 ---
		$acTitle = nullchk($acTitle , "제목을 입력하시기 바랍니다.");
		$acPoint = nullchk($acPoint , "엑션포인트를 입력하시기 바랍니다.");
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
						insert odtActionLog set
							acID				=	'".$v."',
							acTitle		=	'".$acTitle."',
							acPoint		=	".$acPoint.",
							acRegidate	=	now(),
							ip				=  '". $_SERVER[REMOTE_ADDR] ."'
					";
					_MQ_noreturn($sque);
					// 회원DB - action 수치 추가
					_MQ_noreturn(" update odtMember set action = action + ".$acPoint." where id='".$v."' ");
				}
			}
			error_loc( "_actionpoint.list.php?" . enc('d' , $_PVSC) );
			break;
		// -- 추가 ---


		// -- 수정 ---
		case "modify":
			$sque = "
				update odtActionLog set
					acTitle		=	'".$acTitle."',
					acPoint		=	".$acPoint."
				where 
					acNo='${acNo}'
			";
			_MQ_noreturn( $sque );
			error_loc("_actionpoint.form.php?_mode=modify&acNo=${acNo}&_PVSC=${_PVSC}");
			break;
		// -- 수정 ---


		// -- 삭제 ---
		case "delete":

			// 회원DB - action 수치 감소
			$r = _MQ("select * from odtActionLog where acNo='$acNo'");
			_MQ_noreturn(" update odtMember set action = action - ".$r[acPoint]." where id='".$r[acID]."' ");

			_MQ_noreturn("delete from odtActionLog where acNo='$acNo' ");

			error_loc( "_actionpoint.list.php?" . enc('d' , $_PVSC) );
			break;
		// -- 삭제 ---



		// 선택삭제
		case "select_delete":
			if(sizeof($chk_id) == 0 ) {
				error_msg("선택된 액션포인트가 없습니다.");
			}
			$res = _MQ_assoc("select * from odtActionLog where acNo in ('". implode("','" , array_values($chk_id)) ."') ");
			foreach( $res as $k=>$v ){
				_MQ_noreturn(" update odtMember set action = IF( action > ".$v[acPoint]." , action - ".$v[acPoint]." , 0 ) where id='".$v[acID]."' ");
				_MQ_noreturn("delete from odtActionLog where acNo='" . $v[acNo] . "' ");
			}

			error_loc_msg("_actionpoint.list.php?".enc('d' , $_PVSC) , "정상적으로 삭제하였습니다.");
			break;

	}
	// - 모드별 처리 ---

?>