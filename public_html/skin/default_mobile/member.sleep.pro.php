<?PHP


    include(dirname(__FILE__)."/../../include/inc.php");

    if( !$_mode ) { error_msg("잘못된 접근입니다."); }



    switch( $_mode ){

        // ---- 메일을 통한 휴면 회원 인증 처리 ----
        case "auth": 

            //금일§아이디§이메일
            $_app_auth = onedaynet_decode( $auth );
            $ex = explode("§" , $_app_auth);
            $_id = $ex[1];
            $email = $ex[2];

            if( date("Y-m-d") <> $ex[0] ) { error_loc_msg("/m/?pn=member.login.form" , "재 인증 받으시기 바랍니다."); }

            $r = _MQ(" select * from odtMemberSleep where `id`='". addslashes(trim($_id)) ."' and `email`='". addslashes(trim($email)) ."' ");

            if( sizeof($r) == 0 ) {error_loc_msg("/" , "일치하는 회원정보가 없습니다.\\n\\n다시 한번 확인해 주세요.");}

            member_sleep_return( $_id );

            error_loc_msg("/m/?pn=member.login.form" , "인증을 완료하였습니다.\\n\\n새로 로그인 하시면 정상적으로 서비스를 이용하실 수 있습니다.");

            break;
        // ---- 메일을 통한 휴면 회원 인증 처리 ----





        // --- 휴면 회원 인증을 위한 메일 발송 ----
        case "send": 

            if( !$_id ) { error_msg("잘못된 접근입니다."); }

            $r = _MQ("select * from odtMemberSleep where id='". $_id ."'");
            if( sizeof($r) == 0 ) {error_alt("일치하는 회원정보가 없습니다.\\n\\n다시 한번 확인해 주세요.");}

            // - 메일발송 ---
            $email = $r['email'];
            if( mailCheck($email) ){

                $_name = $r['name'];
                $_sitename = $row_setup['site_name'];
                $_app_auth = date("Y-m-d") . "§" . $_id . "§" . $email;
                $_AUTH_URL = "http://" . $_SERVER['HTTP_HOST'] . "/m/member.sleep.pro.php?_mode=auth&auth=" . onedaynet_encode( $_app_auth ) ;

                include_once($_SERVER['DOCUMENT_ROOT']."/pages/mail.contents.sleep_send.php"); // 메일 내용 불러오기 ($mailing_content)
                $_title = "휴면계정 인증을 위한 메일을 발송해드립니다.";
                $_title_content = '
                안녕하세요. <strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$_name.'님!</strong> '.$_sitename.'에서 알려드립니다.<br />
                '.$_name.'님께서 휴면계정을 풀기 위한 인증을 요청해주셨습니다. <br />
                인증을 위해 아래 링크를 클릭하시기 바랍니다. <br />
                인증 URL 클릭 후 정상적으로 인증을 마치신 후 로그인하시면 정상적으로 서비스를 이용하실 수 있습니다.
                ';
                $_content = get_mail_content($_title, $_title_content, $mailling_content);
                mailer( $email , $_title , $_content );
            }
            // - 메일발송 ---

            error_alt("[$r[name]]님의 메일(" . $email . ")로 인증메일을 전송해드렸습니다. \\n\\n발송된 이메일을 통해 인증을 진행해주시기 바랍니다.");

            break;
        // --- 휴면 회원 인증을 위한 메일 발송 ----


    }


?> 