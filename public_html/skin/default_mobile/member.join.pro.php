<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

	##페이지 바로 접속해서 회원가입 요청시 차단
	if(!$_POST[realCheck]) error_alt("잘못된 접근입니다.","back");

	switch($_mode) {
		case "join" :

			#Id의 공백제거
			$id = trim($_id);

			# 생년월일
			$birth = explode("-", $_POST[_birth]);
			$birthy = $birth[0];
			$birthm = $birth[1];
			$birthd = $birth[2];

			# 성별
			$sex = $_POST[_sex];

			#나이
			$age = intVal(date('Y') - $birthy) + 1;

			$_htel = tel_format($_htel_full);
			$_htel = explode('-',$_htel);
			$_htel1 = $_htel[0]; $_htel2 = $_htel[1]; $_htel3 = $_htel[2];

			$_tel = tel_format($_tel_full);
			$_tel = explode('-',$_tel);
			$_tel1 = $_tel[0]; $_tel2 = $_tel[1]; $_tel3 = $_tel[2];


			## 아이디 중복 체크
			$rows = _MQ("SELECT count(id) as cnt FROM odtMember WHERE id='$id' AND secession<>'Y'");
			if($rows[cnt]) {
				error_alt('입력하신 아이디는 이미 사용중입니다.   \\n\\n다시 입력해 주세요.   ');
			}

			## 회원신규 가입시 지급될 초기 포인트 점수
			$point = $row_setup[providepoint];

			if(!$calendar) $calendar = "S";
			if(!$marriage) $marriage = "N";

			$Mlevel = 1;
			$todayTemp = time();
			$visitnum = 1;

			$hID = strtoupper(substr(md5(uniqid(rand())),0,15));

			## 데이타 입력
			$Query = "INSERT INTO odtMember set
						id				= '$id',
						hID				= '$hID',
						passwd			= password('$_passwd'),
						cpasswd         = '".$todayTemp."',
						cpasswd_ck          = '".$todayTemp."',
						name			= '$_name',
						email			= '$_email',
						zip1			= '$_zip1',
						zip2			= '$_zip2',
						zonecode = '$_zonecode',
						address			= '$_address',
						address1		= '$_address1',
						address_doro	= '$_address_doro',
						tel1			= '$_tel1',
						tel2			= '$_tel2',
						tel3			= '$_tel3',
						htel1			= '$_htel1',
						htel2			= '$_htel2',
						htel3			= '$_htel3',
						areakbn			= '$_areakbn',
						mailling		= '$_mailling',
						sms				= '$_sms',
						age				= '$age',
						sex				= '$sex',
						point			= '$point',
						Mlevel			= '$Mlevel',
						birthy			= '$birthy',
						birthm			= '$birthm',
						birthd			= '$birthd',
						job				= '$job',
						repasswd		= '$repasswd',
						recomid			= '$recomid',
						calendar		= '$calendar',
						interest		= '$interest',
						marriage		= '$marriage',
						weddingy		= '$weddingy',
						weddingm		= '$weddingm',
						weddingd		= '$weddingd',
						finalsch		= '$finalsch',
						oname			= '$oname',
						ozip1			= '$ozip1',
						ozip2			= '$ozip2',
						oaddress		= '$oaddress',
						oaddress1		= '$oaddress1',
						otel1			= '$otel1',
						otel2			= '$otel2',
						otel3			= '$otel3',
						ofax1			= '$ofax1',
						ofax2			= '$ofax2',
						ofax3			= '$ofax3',
						odept			= '$odept',
						opost			= '$opost',
						mincome			= '$mincome',
						motive			= '$motive',
						course			= '$course',
						signdate		= '$todayTemp',
						modifydate		= '$todayTemp',
						recentdate		= '$todayTemp',
						m_opt_date          = now(),
						visitnum		= '$visitnum',
						ip				= '$_SERVER[REMOTE_ADDR]',
						kcb_encPsnlInfo	= '$kcb_encPsnlInfo',
						kcb_virtualno	= '$kcb_virtualno',
						kcb_realname	= '$kcb_realname',
						kcb_age			= '$kcb_age',
						kcb_sex			= '$kcb_sex',
						kcb_birthdate	= '$kcb_birthdate',
						kcb_dupinfo		= '$kcb_dupinfo',
						kcb_coinfo1     = '$kcb_coinfo1',
						kcb_coinfo2     = '$kcb_coinfo2'
						,member_agree_privacy   = '". (is_array($join_optional_privacy) ? implode("," , array_filter($join_optional_privacy)) : null) ."'
						";


			$result = _MQ_noreturn($Query);

			if($result) {

				$_MemberInfo = mysql_fetch_array(mysql_query("SELECT serialnum FROM odtMember WHERE id='$id'"));
				apply_login($_MemberInfo[0],$row_setup[ranDsum],$addSum);



				$email 	= $_email;
				$name 	= $_name;

                // - 메일발송 ---
                if( mailCheck($email) ){
                    include_once($_SERVER['DOCUMENT_ROOT']."/pages/mail.contents.join.php"); // 메일 내용 불러오기 ($mailing_content)

                    // 회원가입 시 광고성 정보 수신동의 상태 - 정보 추가 - changeAlert
                    // mailling_content 를 추가로 받아서 return 함
                    // $id 변수를 이용하여 회원정보 추출
                    $_change_alert_file_name = $_SERVER["DOCUMENT_ROOT"] . "/include/addons/changeAlert/changeAlert.mail.contents.join.php";
                    if(@file_exists($_change_alert_file_name)) { include_once($_change_alert_file_name); }

                    $_title = "회원가입을 환영합니다.";
                    $_title_content = '
                    <strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$_name.'님!</strong> 온라인 회원으로 가입되신 것을 환영합니다.<br />
                    저희 웹사이트 이용을 감사드리며 앞으로도 고객님들의 편의를 위해 항상 노력하겠습니다. <br />
                    회원님께서 가입해주신 회원정보는 아래와 같습니다.
                    ';
                    $_content = get_mail_content($_title, $_title_content, $mailling_content);
                    mailer( $email , $_title , $_content );
                }
                // - 메일발송 ---

				// - 문자발송 ---
				$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				$smskbn = "memjoin";	// 문자 발송 유형
				if($row_sms[$smskbn][smschk] == "y") {
					$sms_to		= phone_print($_htel1,$_htel2,$_htel3);
					$sms_from	= $row_company[tel];

					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					// 치환작업
					$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], '', array(
						'{{회원명}}'     => $name,
						'{{회원이메일}}' => $email
					));
					$sms_msg = $arr_sms_msg['msg'];
					$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

				}
				//onedaynet_sms_multisend($arr_send);
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				onedaynet_alimtalk_multisend($arr_send);
				// - 문자발송 ---

				// 회원가입 적립금 지급
				shop_pointlog_insert( $id , "회원가입 적립금 지급" , $row_setup[paypoint_join] , "N" , $row_setup[paypoint_joindate]);

				## 참여점수 입력
				$queP = "insert into odtActionLog set
							acID		= '".$id."',
							acTitle		= '회원가입',
							acPoint		= '". $row_setup[s_action_join] ."',
							ip			= '".$_SERVER[REMOTE_ADDR]."',
							acRegidate	= now()";
				@mysql_query($queP)or die(mysql_error());
				_MQ_noreturn("update odtMember set action = action + ". $row_setup[s_action_join] ." where id='".$id."'");

				error_msg("가입완료");
//				error_msgall("[$id]님의 $row_setup[site_name] 회원가입을 축하드립니다.   ");
//				echo "<script>parent.location.href='/?pn=member.login.form';</script>";
			}
			else {
				error_alt("회원가입이 정상적으로 진행되지 않았습니다.   \\n\\n다시한번 시도해 주시기 바랍니다.   ");
			}

		break;

		case "modify" :

			// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY
			$id = $_id = get_userid();
			// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY

			#Id의 공백제거
			// $id = trim($_id); // -- 사용자 로그인 정보 보안 강화 처리 :: 기존소스 주석처리 -- 2019-05-20 LCY

			# 생년월일
			$birth = explode("-", $_POST[_birth]);
			$birthy = $birth[0];
			$birthm = $birth[1];
			$birthd = $birth[2];

			# 성별
			$sex = $_POST[_sex];

			#나이
			$age = intVal(date('Y') - $birthy) + 1;

			$_htel = tel_format($_htel_full);
			$_htel = explode('-',$_htel);
			$_htel1 = $_htel[0]; $_htel2 = $_htel[1]; $_htel3 = $_htel[2];

			$_tel = tel_format($_tel_full);
			$_tel = explode('-',$_tel);
			$_tel1 = $_tel[0]; $_tel2 = $_tel[1]; $_tel3 = $_tel[2];

			# 현재날짜 => 시간초
			$datetime = time();


//			## 닉네임 중복 체크
//			$rows = _MQ("SELECT count(chatNickName) as nickname FROM odtMember WHERE chatNickName='$_nickName' AND id!='$id' AND secession<>'Y'");
//			if($rows[nickname]) {
//				error_alt('입력하신 닉네임은 이미 사용중입니다.   \\n\\n다시 입력해 주세요.   ');
//			}

			// 비밀번호가 있을때만 처리.
			if($_POST[_passwd]) {
				if($_POST[_passwd] != $_POST[_repasswd]) error_alt('입력하신 비밀번호가 서로 다릅니다.');
				$password_query = "passwd			= password('".$_POST[_passwd]."'),
							 cpasswd = '".$datetime."',
							 cpasswd_ck = '".$datetime."',
							";
			} else {
				$password_query = "";
			}

            // 정보수정 시 광고성 정보 수신동의 상태 - 정보 추가 - changeAlert
            // 자체로 메일발송함.
            // $id 변수를 이용하여 회원정보 추출
            // $_mailling / $_sms 정보 있어야 함.
            $_change_alert_file_name = $_SERVER["DOCUMENT_ROOT"] . "/include/addons/changeAlert/changeAlert.mail.contents.modify.php";
            if(@file_exists($_change_alert_file_name)) { include_once($_change_alert_file_name); }

            ## 데이타 입력
            $Query = "UPDATE odtMember set
						".$password_query."
						email			= '$_email',
						zip1			= '$_zip1',
						zip2			= '$_zip2',
						zonecode = '$_zonecode',
						address			= '$_address',
						address1		= '$_address1',
						address_doro	= '$_address_doro',
						modifydate			= '". time() ."',
						tel1			= '$_tel1',
						tel2			= '$_tel2',
						tel3			= '$_tel3',
						htel1			= '$_htel1',
						m_opt_date          = now(),
						htel2			= '$_htel2',
						htel3			= '$_htel3',
						areakbn			= '$_areakbn',
						mailling		= '$_mailling',
						sms				= '$_sms',
						age				= '$age',
						sex				= '$sex',
						chatNickName	= '$_nickName',
						birthy          = '$birthy',
						birthm          = '$birthm',
						birthd          = '$birthd'
						,member_agree_privacy   = '". (is_array($join_optional_privacy) ? implode("," , array_filter($join_optional_privacy)) : null) ."'
						WHERE
						id				= '$id'";


			$result = _MQ_noreturn($Query);

			if($result) {
				error_frame_loc_msg("/?pn=mypage.modify.form","회원정보가 수정되었습니다.");
			}
			else {
				error_alt("정보수정이 정상적으로 진행되지 않았습니다.   \\n\\n다시한번 시도해 주시기 바랍니다.   ");
			}


		break;


		// 회원탈퇴
		case "delete":
			// 비밀번호 체크
			$r = _MQ("select count(*) as cnt from odtMember where id='". get_userid() ."' and passwd = password('".$leave_pw."') ");

			if($r[cnt] < 1) error_frame_reload("비밀번호가 일치하지 않습니다.");

			$sque = "update odtMember set
						name='탈퇴한회원',
						email='',
						zip1 = '',
						zip2 = '',
						zonecode = '',
						address = '',
						address1 = '',
						address_doro = '',
						tel1 = '',
						tel2 = '',
						tel3 = '',
						htel1 = '',
						htel2 = '',
						htel3 = '',
						birthy='',
						birthm='',
						birthd='',
                        age='',
                        sex='',
						passwd='deluser',
                        repasswd='deluser',
                        cpasswd='0',
                        cpasswd_ck='0',
						deldate = now(),
						kcb_encPsnlInfo = '',
						kcb_virtualno = '',
						kcb_realname = '',
						kcb_age = '',
						kcb_sex = '',
						kcb_birthdate = ''
						,member_agree_privacy   = ''
						where id='".get_userid()."' and passwd = password('".$leave_pw."')";

			_MQ_noreturn($sque);

			samesiteCookie("auth_memberid","",0,"/");
			samesiteCookie("auth_memberid_sess","",0,"/");

			// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY
			UserLogout();
			// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY


			error_frame_loc_msg("/m/" , "정상적으로 탈퇴처리하였습니다.\\n\\n그동안 이용해 주셔서 감사합니다.");
			break;


			// 비밀번호 변경 안내 페이지에서 비밀번호를 변경했을 경우 2015-11-02 lcy  추가
			case 'cpw' :

				if($_passwd=='') { error_alt("현재 비밀번호를 입력하세요."); }
				if($_cpasswd=='') { error_alt("새 비밀번호를 입력하세요."); }
				if($_recpasswd=='') { error_alt("새 비밀번호를 한번더  입력하세요."); }
				if(preg_match_all("/[a-z0-9@!$-_]*$/i",$_cpasswd, $o)<2) { error_alt("비밀번호는 알파벳과 숫자 및 특수문자(@,!,$,-,_)만 입력 가능합니다."); }
				if($_cpasswd!=$_recpasswd){ error_alt("재입력된 비밀번호가 서로 다릅니다."); }

				$r = _MQ("select count(*) as cnt from odtMember where id='". get_userid() ."' and passwd = password('".$_passwd."') ");
				if($r['cnt'] < 1) { error_alt("현재 비밀번호가 일치하지 않습니다."); }


				$next_day = time();  // 갱신일을 오늘 시간초로 ;;

				_MQ_noreturn("UPDATE odtMember SET passwd = password('".$_cpasswd."'), cpasswd='".$next_day."', cpasswd_ck='".$next_day."',
				modifydate='".$next_day."'  WHERE id='".$row_member[id]."'");


				error_frame_loc_msg("/m/", "정상적으로 비밀번호가 변경되었습니다.");


			break;

	// - 모드별 처리 ---


	}


?>
?>