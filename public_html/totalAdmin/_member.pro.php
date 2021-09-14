<?PHP

	include "inc.php";



	// - 모드별 처리 ---
	switch( $_mode ){
		
	// 회원 정보 수정
		case "modify" :

			$row = _MQ("SELECT * FROM odtMember WHERE serialnum='" . $serialnum . "'");
			$email = trim($email);
			
			if(!$passwd) {
				$passwd = $row[passwd];
				//$repasswd = $row[repasswd];
			}
			else {
				//$repasswd = $passwd;
				if(preg_match_all("/[a-z0-9@!$-_]*$/i",$passwd, $o)<2) { error_msg("비밀번호는 알파벳과 숫자 및 특수문자(@,!,$,-,_)만 입력 가능합니다."); }
				$srow = _MQ("SELECT password('$passwd') as pw ");
				$passwd = $srow[pw];
			}

			$ex_tel = explode("-" , $tel);
			$tel1 = $ex_tel[0];
			$tel2 = $ex_tel[1];
			$tel3 = $ex_tel[2];
			$ex_htel = explode("-" , $htel);
			$htel1 = $ex_htel[0];
			$htel2 = $ex_htel[1];
			$htel3 = $ex_htel[2];

			/* 수정 lcy */
			$ex_birth = explode('-', $birth);
			
			$birthy = $ex_birth[0] ? $ex_birth[0] : $row[birthy];
			$birthy = trim($birthy);

			$birthm = $ex_birth[1] ? $ex_birth[1] : $row[birthm];
			$birthm = trim($birthm);

			$birthd = $ex_birth[2] ? $ex_birth[2] : $row[birthd];
			$birthd = trim($birthd);
			
			//$birthy = $birthy ? $birthy : $row[birthy];
			//$birthm = $birthm ? $birthm : $row[birthm];
			//$birthd = $birthd ? $birthd : $row[birthd];


			$action = $action ? $action : $row[action];

			$que = "
				UPDATE odtMember SET 
					passwd				= '".$passwd."',
					repasswd			= '".$repasswd."',
					email				= '".$email."',
					zip1				= '".$zip1."',
					zip2				= '".$zip2."',
					zonecode			= '".$zonecode."',
					address				= '".$address."',
					address1			= '".$address1."',
					address_doro		= '".$address_doro."',
					tel1				= '".$tel1."',
					tel2				= '".$tel2."',
					tel3				= '".$tel3."',
					htel1				= '".$htel1."',
					htel2				= '".$htel2."',
					htel3				= '".$htel3."',
					job					= '".$job."',
					mailling			= '".$mailling."',
					recomid				= '".$recomid."',
					calendar			= '".$calendar."',
					birthy				= '".$birthy."',
					birthm				= '".$birthm."',
					birthd				= '".$birthd."',
					interest			= '".$interest."',
					marriage			= '".$marriage."',
					weddingy			= '".$weddingy."',
					weddingm			= '".$weddingm."',
					weddingd			= '".$weddingd."',
					finalsch			= '".$finalsch."',
					oname				= '".$oname."',
					ozip1				= '".$ozip1."',
					ozip2				= '".$ozip2."',
					oaddress			= '".$oaddress."',
					oaddress1			= '".$oaddress1."',
					otel1				= '".$otel1."',
					otel2				= '".$otel2."',
					otel3				= '".$otel3."',
					ofax1				= '".$ofax1."',
					ofax2				= '".$ofax2."',
					ofax3				= '".$ofax3."',
					odept				= '".$odept."',
					opost				= '".$opost."',
					mincome				= '".$mincome."',
					course				= '".$course."',
					point				= '".$point."',
					Mlevel				= '".$Mlevel."',
					action				= '".$action."' ,
					sms					= '".$sms."',
					sex					= '".$sex."',
					cancel_bank			= '".$cancel_bank."',
					cancel_bank_name	= '".$cancel_bank_name."',
					cancel_bank_account	= '".$cancel_bank_account."'
				WHERE 
					serialnum='" . $serialnum . "'
			"; //LMH001
			_MQ_noreturn($que);
			error_loc("_member.form.php?_mode=modify&serialnum=${serialnum}&_PVSC=${_PVSC}");
			break;




		// 회원 삭제
		case "delete":
			_MQ_noreturn("delete from odtMember where serialnum = '". $serialnum ."' ");
			error_loc_msg("_member.list.php?".enc('d' , $_PVSC) , "정상적으로 삭제하였습니다.");
			break;



		// 선택회원 삭제
		case "select_delete":
			if(sizeof($chk_id) == 0 ) {
				error_msg("선택된 회원이 없습니다.");
			}
			$sque = " delete from odtMember where userType='B' and isRobot = 'N' and id in ('". implode("','" , array_values($chk_id)) ."') ";
			_MQ_noreturn( $sque );
			error_loc_msg("_member.list.php?".enc('d' , $_PVSC) , "정상적으로 삭제하였습니다.");
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

			$r = _MQ_assoc("select * from odtMember " . $sque );
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