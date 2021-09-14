<?PHP

	// 메모리 무제한 풀기
	ini_set('memory_limit','-1');

	include_once("inc.php");

	switch( $_mode ){


		// 매2년마다 수신동의 발송설정
		case "setup" :
			$sque = " update odtSetup set  s_2year_opt_use = '".$_2year_opt_use."' where serialnum = 1";
			_MQ_noreturn($sque);
			error_loc("/totalAdmin/_addons.php?pass_menu=2yearOpt/_2year_opt.form");
			break;


		// 메일설정
		case "mailsetup" :
			$sque = " update odtSetup set  s_2year_opt_title = '". addslashes($_2year_opt_title) ."' , s_2year_opt_content_top = '". addslashes($_2year_opt_content_top) ."' where serialnum = 1";
			_MQ_noreturn($sque);
			error_loc("/totalAdmin/_addons.php?pass_menu=2yearOpt/_2year_opt.form");
			break;




		// 메일발송 - iframe 이미로 넘길 필요 없음.
		case "send" :
			$_type = $_type ? $_type : "email"; // 타입없을 경우 기본 이메일 발송

			if( $row_setup['s_2year_opt_use'] == "Y" ) {

				$mr_row = _MQ_assoc("
					select
						ol.ol_uid ,
						m.htel1 , m.htel2 , m.htel3 , m.id , m.name , m.email
					from odt2yearOptLog as ol
					inner join odtMember as m on (m.id = ol.ol_mid and m.userType='B')
					where
						ol.ol_status='N'
					order by ol.ol_uid asc
					limit 0, 10
				");
				foreach($mr_row as $k=>$v){

					// - 문자발송 ---
					if( in_array($_type , array("sms" , "both"))  ) {
						$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
						$smskbn = "2year_opt";	// 문자 발송 유형
						if($row_sms[$smskbn]['smschk'] == "y") {
							$sms_to		= phone_print($v['htel1'],$v['htel2'],$v['htel3']);
							$sms_from	= $row_company['tel'];

							//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
							// 치환작업
							$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext']);
							$sms_msg = $arr_sms_msg['msg'];
							$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
							//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

						}
						//onedaynet_sms_multisend($arr_send);
						//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
						onedaynet_alimtalk_multisend($arr_send);
					}
					// - 문자발송 ---

					// - 메일발송 ---
					if( in_array($_type , array("email" , "both"))  ) {
						$_id = $v['id'];// 아이디 설정
						$_name = $v['name'];// 이름 설정
						$_email = $v['email'];// 이메일 설정
						if( mailCheck($_email) ){
							// ==> 메일 내용 불러오기 ($_title , $_title_content , $_2year_opt_content)
							include(dirname(__FILE__)."/mail.contents.2yearOpt.php");
							$_content = $_2year_opt_content ;
							mailer( $_email , $_title , $_content );
						}
					}
					// - 메일발송 ---

					//발송후 업데이트
					_MQ_noreturn(" update odt2yearOptLog set ol_sdate=now() , ol_status='Y' where ol_uid='". $v['ol_uid'] ."' "); // 로그 발송처리

				}

				// JJC : 수정 : 2021-05-17
				//$mr_cnt = _MQ(" select count(*) as cnt from odt2yearOptLog where ol_status='N'  "); // 수신동의 2년 지난 -  회원 갯수 체크
				$mr_cnt = _MQ(" select count(*) as cnt from odt2yearOptLog INNER join odtMember on (id = ol_mid and userType='B') where ol_status='N'  "); // 수신동의 2년 지난 -  회원 갯수 체크
				echo $mr_cnt['cnt'];

			}
			break;

		}

?>