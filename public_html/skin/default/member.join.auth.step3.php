<?PHP
	include(dirname(__FILE__)."/../../include/inc.php");

   /* ============================================================================== */
    /* =   인증데이터 수신 및 복호화 페이지                                         = */
    /* = -------------------------------------------------------------------------- = */
    /* =   해당 페이지는 반드시 가맹점 서버에 업로드 되어야 하며                    = */
    /* =   가급적 수정없이 사용하시기 바랍니다.                                     = */
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   라이브러리 파일 Include                                                  = */
    /* = -------------------------------------------------------------------------- = */

    require $_SERVER['DOCUMENT_ROOT'] . "/../auth/kcp/lib/ct_cli_lib.php";

    /* = -------------------------------------------------------------------------- = */
    /* =   라이브러리 파일 Include END                                               = */
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   null 값을 처리하는 메소드                                                = */
    /* = -------------------------------------------------------------------------- = */
    function f_get_parm_str( $val )
    {
        if ( $val == null ) $val = "";
        if ( $val == ""   ) $val = "";
        return  $val;
    }
    /* ============================================================================== */

    $home_dir      = $_SERVER['DOCUMENT_ROOT'] . "/../auth/kcp"; // ct_cll 절대경로 ( bin 전까지 )

    $site_cd       = "";
    $ordr_idxx     = "";

    $cert_no       = "";
    $cert_enc_use  = "";
    $enc_info      = "";
    $enc_data      = "";
    $req_tx        = "";

    $enc_cert_data = "";
    $cert_info     = "";

    $tran_cd       = "";
    $res_cd        = "";
    $res_msg       = "";

    $dn_hash       = "";
	/*------------------------------------------------------------------------*/
    /*  :: 전체 파라미터 남기기                                               */
    /*------------------------------------------------------------------------*/

	// request 로 넘어온 값 utf-8로 변환
    foreach($_POST as $nmParam => $valParam){
		$_POST[$nmParam] = iconv('euc-kr', 'utf-8', $valParam);

	}

    // request 로 넘어온 값 처리
    foreach($_POST as $nmParam => $valParam)
    {

        if ( $nmParam == "site_cd" )
        {
            $site_cd = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "ordr_idxx" )
        {
            $ordr_idxx = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "res_cd" )
        {
            $res_cd = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "cert_enc_use" )
        {
            $cert_enc_use = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "req_tx" )
        {
            $req_tx = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "cert_no" )
        {
            $cert_no = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "enc_cert_data" )
        {
            $enc_cert_data = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "dn_hash" )
        {
            $dn_hash = f_get_parm_str ( $valParam );
       }

        // 부모창으로 넘기는 form 데이터 생성 필드
        //$sbParam .= "<input type='hidden' name='" . $nmParam . "' value='" . f_get_parm_str( $valParam ) . "'/>";
    }

    $ct_cert = new C_CT_CLI;
    $ct_cert->mf_clear();

    // 결과 처리

    if( $cert_enc_use == "Y" )
    {
        if( $res_cd == "0000" )
        {
            // dn_hash 검증
            // KCP 가 리턴해 드리는 dn_hash 와 사이트 코드, 주문번호 , 인증번호를 검증하여
            // 해당 데이터의 위변조를 방지합니다
             $veri_str = $site_cd.$ordr_idxx.$cert_no; // 사이트 코드 + 주문번호 + 인증거래번호

            if ( $ct_cert->check_valid_hash ( $home_dir , $dn_hash , $veri_str ) != "1" )
            {
                // 검증 실패시 처리 영역

                echo "dn_hash 변조 위험있음";
                // 오류 처리 ( dn_hash 변조 위험있음)
            }

            // 가맹점 DB 처리 페이지 영역

            //echo "========================= 리턴 데이터 ======================="       ."<br>";
            //echo "사이트 코드            :" . $site_cd                                 ."<br>";
            //echo "인증 번호              :" . $cert_no                                 ."<br>";
            //echo "암호된 인증정보        :" . $enc_cert_data                           ."<br>";

            // 인증데이터 복호화 함수
            // 해당 함수는 암호화된 enc_cert_data 를
            // site_cd 와 cert_no 를 가지고 복화화 하는 함수 입니다.
            // 정상적으로 복호화 된경우에만 인증데이터를 가져올수 있습니다.
            $opt = "1" ; // 복호화 인코딩 옵션 ( UTF - 8 사용시 "1" )
            $ct_cert->decrypt_enc_cert( $home_dir , $site_cd , $cert_no , $enc_cert_data , $opt );

            //echo "========================= 복호화 데이터 ====================="       ."<br>";
            //echo "복호화 이동통신사 코드 :" . $ct_cert->mf_get_key_value("comm_id"    )."<br>"; // 이동통신사 코드
            //echo "복호화 전화번호        :" . $ct_cert->mf_get_key_value("phone_no"   )."<br>"; // 전화번호
            //echo "복호화 이름            :" . $ct_cert->mf_get_key_value("user_name"  )."<br>"; // 이름
            //echo "복호화 생년월일        :" . $ct_cert->mf_get_key_value("birth_day"  )."<br>"; // 생년월일
            //echo "복호화 성별코드        :" . $ct_cert->mf_get_key_value("sex_code"   )."<br>"; // 성별코드
            //echo "복호화 내/외국인 정보  :" . $ct_cert->mf_get_key_value("local_code" )."<br>"; // 내/외국인 정보
            //echo "복호화 CI              :" . $ct_cert->mf_get_key_value("ci_url"         )."<br>"; // CI
            //echo "복호화 DI              :" . $ct_cert->mf_get_key_value("di_url"         )."<br>"; // DI 중복가입 확인값
            //echo "복호화 WEB_SITEID      :" . $ct_cert->mf_get_key_value("web_siteid" )."<br>"; // WEB_SITEID
            //echo "복호화 결과코드        :" . $ct_cert->mf_get_key_value("res_cd"     )."<br>"; // 암호화된 결과코드
            //echo "복호화 결과메시지      :" . $ct_cert->mf_get_key_value("res_msg"    )."<br>"; // 암호화된 결과메시지

            $comm_id = $ct_cert->mf_get_key_value("comm_id"); // 이동통신사 코드
            $phone_no = $ct_cert->mf_get_key_value("phone_no"); // 전화번호
            $phone_no = tel_format($phone_no); // 전화번호 포멧 변경
            $user_name = $ct_cert->mf_get_key_value("user_name"); // 이름
            $birth_day = $ct_cert->mf_get_key_value("birth_day"); // 생년월일
			$birth_day = date('Y-m-d', strtotime($birth_day)); // 생년월일 포멧 변경
            $sex_code = $ct_cert->mf_get_key_value("sex_code"); // 성별코드
			$sex_code = $sex_code == '01' ? 'M' : 'F'; // 성별코드 포멧 변경
            $local_code = $ct_cert->mf_get_key_value("local_code"); // 내/외국인 정보
            $ci_url = $ct_cert->mf_get_key_value("ci_url"); // CI
            $di_url = $ct_cert->mf_get_key_value("di_url"); // DI 중복가입 확인값
            $web_siteid = $ct_cert->mf_get_key_value("web_siteid" ); // WEB_SITEID
            $res_cd = $ct_cert->mf_get_key_value("res_cd"); // 암호화된 결과코드
            $res_msg = $ct_cert->mf_get_key_value("res_msg"); // 암호화된 결과메시지

			// 부모창으로 넘기는 form 데이터 생성 필드
			$sbParam .= "<input type='hidden' name='res_cd' value='" . $res_cd . "'/>";
			$sbParam .= "<input type='hidden' name='res_msg' value='" . $res_msg . "'/>";
			$sbParam .= "<input type='hidden' name='phone_no' value='" . $phone_no . "'/>";
			$sbParam .= "<input type='hidden' name='user_name' value='" . $user_name . "'/>";
			$sbParam .= "<input type='hidden' name='birth_day' value='" . $birth_day . "'/>";
			$sbParam .= "<input type='hidden' name='sex_code' value='" . $sex_code . "'/>";
			$sbParam .= "<input type='hidden' name='ci_url' value='" . $ci_url . "'/>";
			$sbParam .= "<input type='hidden' name='di_url' value='" . $di_url . "'/>";
			$sbParam .= "<input type='hidden' name='up_hash' value='" . f_get_parm_str( $_POST['up_hash'] ) . "'/>";

			// 위변조 체크를 위한 로그 저장 - 성공시에만 저장
			$que = "
				insert into odtIndividualAuthLog set
					inl_ordr_idxx = '". $ordr_idxx ."'
					, inl_site_cd = '". $site_cd ."'
					, inl_cert_no = '". $cert_no ."'
					, inl_enc_cert_data = '". $enc_cert_data ."'
					, inl_rdate = now()
				on duplicate key update
					inl_site_cd = '". $site_cd ."'
					, inl_cert_no = '". $cert_no ."'
					, inl_enc_cert_data = '". $enc_cert_data ."'
					, inl_rdate = now()
			";
			_MQ_noreturn($que);

			// 위변조 체크용 변수 셋팅
			echo "
				<script>
					opener.document.join_frm._ordr_idxx.value = '". $ordr_idxx ."'
					opener.window.scAuthR = '". $res_cd ."';
					opener.window.scAuthN = '". $user_name ."';
					opener.window.scAuthB = '". $birth_day ."';
					opener.window.scAuthS = '". $sex_code ."';
					opener.window.scAuthH = '". $phone_no ."';
					opener.window.scAuthM = '". $res_msg ."';
				</script>
			";

        }
        else/*if( res_cd.equals( "0000" ) != true )*/
        {
           // 인증실패
		   	// 위변조 체크용 변수 셋팅
			echo "
				<script>
					opener.document.join_frm._ordr_idxx.value = ''
					opener.window.scAuthR = '9999';
					opener.window.scAuthN = '';
					opener.window.scAuthB = '';
					opener.window.scAuthS = '';
					opener.window.scAuthH = '';
					opener.window.scAuthM = '';
				</script>
			";
        }
    }
    else/*if( cert_enc_use.equals( "Y" ) != true )*/
    {
        // 암호화 인증 안함 => 인증취소 시
		error_msgPopup_s('본인인증을 취소하였습니다.');
    }
    $ct_cert->mf_clear();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>*** KCP Online Payment System [PHP Version] ***</title>
        <script type="text/javascript">
            window.onload=function()
            {
                try
                {
                    opener.auth_data( document.form_auth ); // 부모창으로 값 전달

                    window.close();// 팝업 닫기
                }
                catch(e)
                {
                    alert(e); // 정상적인 부모창의 iframe 를 못찾은 경우임
                }
            }
        </script>
    </head>
    <body oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;">
        <form name="form_auth" method="post">
            <?php echo $sbParam; ?>
        </form>
    </body>
</html>