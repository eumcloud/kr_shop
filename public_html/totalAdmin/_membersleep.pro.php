<?PHP

	//JJC002

	include "inc.php";



	// - 모드별 처리 ---
	switch( $_mode ){

		// 회원 휴면풀기
		case "return":
			$r = _MQ("select id from odtMemberSleep where serialnum = '". $serialnum ."' ");
			member_sleep_return( $r['id'] );
			error_loc_msg("_membersleep.list.php?".enc('d' , $_PVSC) , "정상적으로 휴면을 풀었습니다.");
			break;



		// 선택회원 휴면풀기
		case "select_return":
			if(sizeof(array_filter($chk_id)) == 0 ) {
				error_msg("선택된 회원이 없습니다.");
			}
			foreach(array_filter($chk_id) as $k=>$v){
				member_sleep_return( $v );
			}
			error_loc_msg("_membersleep.list.php?".enc('d' , $_PVSC) , "정상적으로 휴면을 풀었습니다.");
			break;




		// 회원 삭제
		case "delete":
			_MQ_noreturn("delete from odtMemberSleep where serialnum = '". $serialnum ."' ");
			error_loc_msg("_membersleep.list.php?".enc('d' , $_PVSC) , "정상적으로 삭제하였습니다.");
			break;



		// 선택회원 삭제
		case "select_delete":
			if(sizeof($chk_id) == 0 ) {
				error_msg("선택된 회원이 없습니다.");
			}
			$sque = " delete from odtMemberSleep where userType='B' and isRobot = 'N' and id in ('". implode("','" , array_values($chk_id)) ."') ";
			_MQ_noreturn( $sque );
			error_loc_msg("_membersleep.list.php?".enc('d' , $_PVSC) , "정상적으로 삭제하였습니다.");
			break;





		case "select_excel" :
		case "search_excel" :

			if($_mode == "select_excel") {
				if(sizeof($chk_id) == 0 ) {
					error_msg("선택된 회원이 없습니다.");
				}
				$sque = " where userType='B' and isRobot = 'N' and id in ('". implode("','" , array_values($chk_id)) ."') ";
			}
			else if($_mode == "search_excel") {
				$sque = enc("d" , $_search_que);
			}

			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;"); 
			header("Content-Disposition: attachment; filename=회원_" . date('YmdHis') . ".xls");

			echo "
				<table border=1>
					<tr>
						<td>아이디</td>
						<td>이름</td>
						<td>이메일</td>
						<td>이메일수신여부</td>
						<td>전화번호</td>
						<td>휴대폰</td>
						<td>문자수신여부</td>
						<td>새 우편번호</td>
						<td>우편번호</td>
						<td>지번주소</td>
						<td>도로명주소</td>
						<td>가입일</td>
					</tr>
			";

			$r = _MQ_assoc("select * from odtMemberSleep " . $sque );
			foreach($r as $k => $v) {
				echo "
					<tr>
						<td>". $v[id] ."</td>
						<td>". $v[name] ."</td>
						<td>". $v[email] ."</td>
						<td>". $v[mailling] ."</td>
						<td>". $v[tel1]. ($v[tel2] ? "-" . $v[tel2] : "") . ($v[tel3] ? "-" . $v[tel3] : "") ."</td>
						<td>". $v[htel1]. ($v[htel2] ? "-" . $v[htel2] : "") . ($v[htel3] ? "-" . $v[htel3] : "") ."</td>
						<td>". $v[sms] ."</td>
						<td>". $v[zonecode] ."</td>
						<td>". $v[zip1] ."-". $v[zip2] ."</td>
						<td>". $v[address] ." ". $v[address1] ."</td>
						<td>". $v[address_doro] ."</td>
						<td>" . date("Y-m-d H:i:s",$v[signdate]) . "</td>
					</tr>
				";
			}
			echo "</table>";
			exit;

			break;

	}


?>