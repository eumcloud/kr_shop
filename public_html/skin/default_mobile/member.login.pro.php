<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

	if( !$_mode ) {
		error_msg("잘못된 접근입니다.");
	}

	switch($_mode){

		// - 로그인 ---
		case "login":
			// --사전 체크 ---
			$_id = trim(nullchk($_id , "아이디를 입력해주세요." , "" , "ALT")); // nullchk - alert 형식으로 return
            if($_mode2<>"master_login")
				$_passwd = trim(nullchk($_passwd , "패스워드를 입력해주세요." , "" , "ALT"));// nullchk - alert 형식으로 return
			// --사전 체크 ---

			// 아이디 , 비밀번호를 통한 회원 확인
			$r = _MQ("SELECT * FROM odtMember where id='{$_id}' and userType!='C' ");
			if( sizeof($r) == 0 ) {
				error_alt("회원정보가 없습니다.\\n\\n다시 한번 확인해 주세요.");
			}



			// --- JJC002 - 휴면계정 체크 ---
			if($r['name'] == "휴면전환") {
				error_frame_loc("/?pn=member.sleep.form&_id=" . $_id);
			}
			// --- JJC002 - 휴면계정 체크 ---




			// --- add source ---
			// 관리자 로그인 처리
			if( $_mode2 == "master_login" && is_admin() ){
				// 비밀번호 추출 통한 회원 확인
				$admtmpr = _MQ("SELECT passwd FROM odtMember where id='{$_id}' and userType!='C' ");
				$app_login_password = $admtmpr[passwd];
			}
			else {
				$tmpr = _MQ("select password('". $_passwd ."') as pw ");
				$app_login_password = $tmpr[pw];
			}
			// --- add source ---
			if( !($r[passwd] == $app_login_password && $app_login_password)) {// --- modify source ---
				error_alt("비밀번호가 맞지 않습니다.\\n\\n다시 한번 확인해 주세요.");
			}

			// 아이디 하루동안 저장
			if($login_id_chk == 'Y') {
				samesiteCookie("AuthSDIndividualIDChk", $_id , time() +3600*24*365 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
			} else {
				samesiteCookie("AuthSDIndividualIDChk", "" , time()-3600 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
			}

			// 회원 정보 저장 LCY;
			$cpr = $r;

			// 로그인 로그 기록.
			loginchk_insert($_id,'individual');



			## 최근 방문일 값과 방문횟수 증가 #####################################################
			_MQ_noreturn("UPDATE odtMember SET recentdate=".time().",visitnum=visitnum+1 WHERE id='$_id'");

			## 참여점수 부여 #####################################################
			$isFirst = _MQ("select count(*) as cnt from odtActionLog where acID = '".$_id."' and acTitle ='첫 로그인' and left(acRegidate,10) = CURDATE() ");
			if($isFirst[cnt] < 1 && $row_setup[s_action_login]>0) {
				_MQ_noreturn("insert into odtActionLog set acID= '".$_id."', acTitle ='첫 로그인', acPoint='". $row_setup[s_action_login] ."', acRegidate = now(), ip='".$_SERVER[REMOTE_ADDR]."'");
				_MQ_noreturn("update odtMember set action = action + ". $row_setup[s_action_login] ." where id='".$_id."'");
			}

			// 로그인 쿠키 처리.
			apply_login($r[serialnum],$row_setup[ranDsum],$addSum);



			// --- JJC003 - 묶음배송 :: 장바구니 기록남기기 ---
			_MQ_noreturn("update odtCart set c_cookie='". $_id ."' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");
			samesiteCookie("AuthShopCOOKIEID", $_id , 0 , "/", str_replace("www." , "" , $_SERVER['HTTP_HOST']) );
			// --- JJC003 - 묶음배송 :: 장바구니 기록남기기 ---

			// 장바구니 중복 체크하여 수량 증가
			$r = _MQ_assoc(" select c_uid, c_cookie, c_pcode, c_pouid, sum(c_cnt) as sum, IFNULL(sum(c_point),0) as sum_point, count(*) as cnt from odtCart where c_cookie = '".$_id."' group by c_cookie, c_pcode, c_pouid, c_addoption_parent having cnt > 1 ");
			foreach( $r as $k=>$v ){
				$p = _MQ(" select buy_limit from odtProduct where code = '".$v[c_pcode]."' "); $v[sum] = $v[sum] > $p[buy_limit] ? $p[buy_limit] : $v[sum];
				_MQ_noreturn(" update odtCart set c_cnt = '".$v[sum]."' , c_point = '".$v[sum_point]."' where c_uid = '".$v[c_uid]."' and c_cookie = '".$v[c_cookie]."' ");
				_MQ_noreturn(" delete from odtCart where c_cookie = '".$v[c_cookie]."' and c_pcode  = '".$v[c_pcode]."' and c_pouid = '".$v[c_pouid]."' and c_uid != '".$v[c_uid]."' ");
			}



			// 페이지 이동 --> 단 로그인/회원가입페이지일 경우 메인으로 돌림 -->  모두 팝업이므로 의미없음
			if(!$_move_path){
				$_move_path = "/m/";
			}

		         // 특정일수 마다 로그인페이지 이동 체크 lcy

		       	$cpwd_ck_day = date('Y-m-d',$cpr[cpasswd_ck]); // 비밀번호 변경 갱신일을 날로 변경
			$cpwd_ck =  strtotime($cpwd_ck_day." + ".$row_setup[member_cpw_period]." month"); // 관리자가 설정한 개월 수를 계산하여 초로계산

		         $cpwd_day = date('Y-m-d',$cpwd_ck); // 일 수로 변경

		         if($cpwd_day <= date("Y-m-d",time())){
		         	$_move_path=base64_encode('pn=member.password.form');
		         }  //// 체크 변경 끝



			error_frame_loc("m/?".enc("d",$_move_path));

			break;

		// - 로그아웃 ---
		case "logout":


			// 로그아웃시 임시옵션 비움 2015-11-16 LDD
			if($_COOKIE["AuthShopCOOKIEID"]) {

				_MQ_noreturn("delete from odtTmpProductOption where otpo_mid='". $_COOKIE["AuthShopCOOKIEID"] ."'");
			}
			// 로그아웃시 임시옵션 비움 2015-11-16 LDD

			// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY
			UserLogout();
			// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY

			// 쿠키 적용
			// --- JJC003 - 묶음배송 :: 장바구니 기록남기기 ---
			samesiteCookie("AuthShopCOOKIEID", substr(enc('e' , md5(time().rand(0,9999)."hy shop")),0,15), 0 , "/"  );//장바구니 원복
			// --- JJC003 - 묶음배송 :: 장바구니 기록남기기 ---


			error_frame_loc("/m/");
			break;

		// - 팝업닫기 ---
		case "popup_close":
			if( $uid ) {
				$app_div_name = "event_popup_div_" . $uid;
				// 쿠키 적용
				samesiteCookie("AuthPopupClose_" . $uid , "Y" , time() +3600 * 24 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
				echo "<SCRIPT>parent.document.getElementById('". $app_div_name ."').style.display = 'none';</SCRIPT>";
			}
			break;
		// 아이디 비번 찾기.
		case "find_idpw" :

			// --사전 체크 ---
			$_name 	= trim(nullchk($_name , "이름을 입력해주세요." , "" , "ALT")); // nullchk - alert 형식으로 return
			$_birth = trim(nullchk($_birth , "생년월일을 입력해주세요." , "" , "ALT")); // nullchk - alert 형식으로 return
			$_email = trim(nullchk($_email , "이메일주소를 입력해주세요." , "" , "ALT")); // nullchk - alert 형식으로 return

			$r = _MQ("SELECT *,concat(birthy,'-',birthm,'-',birthd) as birth FROM odtMember where name='{$_name}' and email='{$_email}' having birth='{$_birth}'  ");
			if( sizeof($r) == 0 ) {
				error_alt("일치하는 회원정보가 없습니다.\\n\\n다시 한번 확인해 주세요.");
			}
			$new_passwd = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);

			# 새로운 비밀번호로 변경
			_MQ_noreturn("update odtMember set passwd = password('".$new_passwd."') where serialnum = '".$r[serialnum]."'");

			// - 메일발송 ---
			$email = $r[email];
			if( mailCheck($email) ){
				include_once($_SERVER['DOCUMENT_ROOT']."/pages/mail.contents.find_idpw.php"); // 메일 내용 불러오기 ($mailing_content)
				$_title = "회원님의 아이디 및 임시 비밀번호 입니다.";
				$_title_content = '
				<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$r['name'].'님!</strong> 요청하신 로그인 정보를 알려드립니다.<br />
				고객님의 로그인 정보를 알려드립니다. 비밀번호는 암호화 되어있는 관계로 <br />
				임시비밀번호를 생성해서 보내드리니, 로그인 후에 반드시 변경하길 바랍니다. <br />
				';
				$_content = get_mail_content($_title, $_title_content, $mailling_content);
				mailer( $email , $_title , $_content );
			}
			// - 메일발송 ---

			error_frame_loc_msg("/?pn=member.login.form&path=main","[$r[name]]님의 아이디와 임시 비밀번호를   \\n\\n메일($email)로 전송해 드렸습니다.");

			break;

		}

?>