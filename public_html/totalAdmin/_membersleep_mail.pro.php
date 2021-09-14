<?php

	//JJC002

	include "inc.php";


	// - 모드별 처리 ---
	switch( $_mode ){



		// -- 메일 발송을 위한 데이터 저장 ---
		case "send":

			// --- 사전 체크 ---
			$_cnt = nullchk($_cnt , "회원을 선택하시기 바랍니다.");
			if( $_cnt == 0 ) {error_msg("회원을 선택하시기 바랍니다.");}
			// --- 사전 체크 ---

			// --- 200명 단위로 끊어서 arr_profile 저장 ---
			// 오늘 등록한 해당 메일은은 같은 _brother_key가 되도록 함
			// _brother_key는 md5처리함
			$_brother_key = md5( "sleep" . $_cnt . date("YmdH")); 
			$arr_profile = array();// 메일링 회원 이메일 200개 단위 저장 배열
			if( $_cnt > 0 ){
				$chk_cnt = 0;
				$ex = array_filter(explode("," , enc('d' , $_pass_data)));
				foreach($ex as $k=>$v){
					// 무조건 내림처리
					$app_key = floor ( $chk_cnt / 200 );
					$app_2key = $chk_cnt % 200 ;
					$arr_profile[$app_key][$app_2key] = $v; // 배열에 메일 저장 (_brother_key에 의해 200개씩 끊어지게 됨)
					$chk_cnt ++;
				}
			}
			// --- 200명 단위로 끊어서 arr_profile 저장 ---

			// --- arr_profile 배열 정보를 odtMailingProfile에 저장 ---
			foreach($arr_profile as $k=>$v){
				$que = "
					insert odtMailingProfile set 
						 mp_brother = '" . $_brother_key . "'
						,mp_email = '". implode("," , $v) ."'
						,mp_status = 'N'
						,mp_rdate = now()
						,mp_type = 'sleep'
				";
				_MQ_noreturn($que);
			}
			// --- arr_profile 배열 정보를 odtMailingProfile에 저장 ---

			error_loc_msg("_membersleep_mail.form.php?_mduid=" .$_mduid."&". enc('d' , $_PVSC) , "메일 발송 데이터를 정상적으로 저장하였습니다.\\n\\n메일발송 버튼을 눌러 최종 발송 처리 하시기 바랍니다.");
			break;
		// -- 메일 발송을 위한 데이터 저장 ---


		// 발송 처리
		case "sendpro":

			if(!$_uid) error_alt("잘못된 접근입니다.");

			// - 데이터 추출 ---
			$r = _MQ_assoc(" select * from odtMailingProfile where mp_status='N' and mp_uid = '".$_uid."' ");
			foreach( $r as $k=>$v ){

				// -- 메일 발송전 데이터를 발송으로 처리함... 메일 발송 에러시 무한 루프 돌 수 있기 때문 ---
				$sque = " update odtMailingProfile set mp_status='Y' , mp_sdate=now() where mp_uid='" . $v[mp_uid] . "' ";
				_MQ_noreturn($sque);


				// -- 변수 준비
				$_title = $arr_member_sleep_title ;
				$_header = "MIME-Version: 1.0\r\n" ; 
				$_header .= "Content-Type: text/html; charset=utf-8\r\n";
				$_header .= "Reply-To:".$row_company[email]."\r\n";

				// -- 이메일 데이터 분리 ---
				$ex_app_profile = array_filter(explode("," , $v['mp_email']));
				$sr = _MQ_assoc("select * from odtMemberSleep where serialnum in ('". implode("' , '" , $ex_app_profile) ."')");
				foreach($sr as $sk=>$sv){

					// -- 발송됨으로 처리 ---
					_MQ_noreturn(" update odtMemberSleep set ms_sendchk='Y' where serialnum='" . $sv[serialnum] . "' ");

					$_email = $sv['email']; // -> 이메일
					$_name = $sv['name'];
					$_sitename = $row_setup['site_name'];
					$_recentdate = date("Y-m-d" , $sv['recentdate']);

					include(dirname(__FILE__)."/../pages/mail.contents.sleep.php");

					// -- 준비할 변수 1. $_title : 제목
					// -- 준비할 변수 2. $_content : 내용
					// -- 준비할 변수 3. $_rdate : 보내는 날짜 (YYYY.MM.DD)
					// -- 준비할 변수 4. $_email : 보내는 메일
					$_title_content = '
					안녕하세요. <strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$_name.'님!</strong> '.$_sitename.'에서 알려드립니다.<br />
					<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$_name.'님</strong>께서는 <strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$_sitename.'</strong> 사이트 <br />
					장기 미사용으로 인하여 휴면계정으로 전환됨을 알려드립니다. <br />
					';
					$app_content = get_mail_content($_title, $_title_content, $mailling_content);


					// 네이버 깨짐현상으로 인해
					if( preg_match("/@naver.com/i" , $_email) ){
						$app_header = "From: \"".$row_setup[site_name]."\" <".$row_company[email]."> \r\n" . $_header;
					}
					else {
						$app_header = "From: \"" . "=?UTF-8?B?".base64_encode($row_setup[site_name])."?="  . "\" <".$row_company[email]."> \r\n" . $_header;
					}

					// -- 메일 발송
					@mail($_email, '=?UTF-8?B?'.base64_encode($_title).'?=', $app_content, $app_header ,  "-f".$row_company[email]);

				}

			}

			error_frame_loc_msg("_membersleep_mail.form.php?_mduid=" .$_mduid."&". enc('d' , $_PVSC),"발송되었습니다.");

			break;

	}
	// - 모드별 처리 ---

?>