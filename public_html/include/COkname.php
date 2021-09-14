<?
class COkName
{

    ##IPIN용 변수
    var $TESTMODE = 0;  //테스트모드 : 실사용모드일떄는 1이 아닌 다른값으로 수정 요망
    var $SYSTEMBIT = "64";   //KCB BIT 32,64 --> 콘솔상에서 확인하는 방법 - # getconf LONG_BIT
    var $Folder = "";
    var $idpCode   = "V";					// 고정값. KCB기관코드
    var $oknamepath = "";
    var $keypath = ""; // 키파일이 생성될 위치. 웹서버에 해당파일을 생성할 권한 필요.
    var $logpath = "";  // 로그파일을 남기는 경우 로그파일이 생성될 경로
    var $reserved1 = "0";			//reserved1
    var $reserved2 = "0";			//reserved2
    var $exe;

    var $pubkey = "";        //Return Value
    var $sig = "";           //Return Value
    var $curtime = "";       //Return Value

    var $idpUrl;
    var $returnUrl;
    var $cpCode;     // 회원사 코드 (회원사 아이디)
    var $EndPointURL;
    var $option;
    var $cmd;

    ##아이핀결과값해석용
    var $option3;
    var $cmd3;


    ##실명인증용 변수
    var $qryBrcCd = "x";    //고정값
    var $qryBrcNm = "x";    //고정값
    var $qryId = "u1234";                   // 쿼리ID, 고정값 
    var $qryRsnCd = "01";                   // 조회사유  회원가입 : 01, 회원정보수정 : 02, 회원탈회 : 03, 성인인증 : 04, 기타 : 05
    var $qryDt = "";               // 현재일자 20101101 과 같이 숫자8자리 입력되어야함.
    var $qryIP = "x";                       // *** 회원사 IP,   $_SERVER["SERVER_ADDR"] 사용가능.
    var $qryDomain = "test.co.kr";          // *** 회원사 도메인, $_SERVER["SERVER_NAME"] 사용가
    var $option2;
    var $cmd2;

    function Set_BIT($Param1) {
        $this->SYSTEMBIT = $Param1;
    }

    //Creator
    function COkName($ID,$bit = "64",$testmode="0") {
        $this->TESTMODE = $testmode;
        $this->Folder = $_SERVER[DOCUMENT_ROOT]."/../nameCheck";
        $this->Set_BIT($bit);

        $this->Folder = $_SERVER[DOCUMENT_ROOT]."/../nameCheck";
        $this->oknamepath = $_SERVER[DOCUMENT_ROOT]."/../nameCheck";

        $this->keypath = $this->oknamepath."/key/okname.key"; // 키파일이 생성될 위치. 웹서버에 해당파일을 생성할 권한 필요.
        $this->logpath = $this->oknamepath."/log/";  // 로그파일을 남기는 경우 로그파일이 생성될 경로

        //IPIN용변수
        $this->idpUrl    = "https://ipin.ok-name.co.kr/tis/ti/POTI90B_SendCertInfo.jsp";
        $this->returnUrl = "";
        $this->cpCode    = $ID; // 회원사 코드 (회원사 아이디)
        $this->EndPointURL = "http://www.allcredit.co.kr/KcbWebService/OkNameService";// 운영 서버
        $this->option = "UCL";// Option
        $this->exe =  $this->oknamepath."/okname".$this->SYSTEMBIT;        //실행파일경로 

        //실명인증용변수
        $this->qryIP = $_SERVER["SERVER_ADDR"];       // *** 회원사 IP,   $_SERVER["SERVER_ADDR"] 사용가능.

        $Rows2 = mysql_fetch_array(mysql_query("SELECT * FROM odtCompany WHERE serialnum='1'"));
        $this->qryDomain = $Rows2[homepage]; // $_SERVER["SERVER_NAME"];   // *** 회원사 도메인, $_SERVER["SERVER_NAME"] 사용가능.
        $this->option2 = "UL";   // *** 회원사 도메인, $_SERVER["SERVER_NAME"] 사용가능.

        //Test MODE
        if ( $testmode == "1" ) {   $this->Set_TestMode();  }

        $exist=file_exists($this->exe);
        if(!$exist) {
            $this->msg("KCB 인증콤포넌트(".$this->SYSTEMBIT."BIT)가 없습니다. 서비스를 이용할 수 없습니다.");
            return false;
        }
        return true;
    }

    ##실행방식을 테스트상태로 전환
    function Set_TestMode() {
        //아이핀용 추가변수
        $this->TESTMODE = 1;
        $this->idpUrl    = "https://tipin.ok-name.co.kr:8443/tis/ti/POTI90B_SendCertInfo.jsp";
        $this->returnUrl = ""; //Return page
        $this->cpCode    = "P00000000000";		// 회원사 코드 (회원사 아이디)
        $this->EndPointURL = "http://tallcredit.kcb4u.com:9088/KcbWebService/OkNameService";//EndPointURL, 테스트 서버
        $this->option = "UCL";// Option
    
        //실명인증용테스트변수
        $this->qryIP = "x";                       // *** 회원사 IP,   $_SERVER["SERVER_ADDR"] 사용가능.
        $this->qryDomain = "test.co.kr";          // *** 회원사 도메인, $_SERVER["SERVER_NAME"] 사용가
        $this->option2 = "ULD";   // *** 회원사 도메인, $_SERVER["SERVER_NAME"] 사용가능.
    }

    ##Returl URL 설정
    function Set_RetURL($param1) {
        $this->returnUrl = $param1;
    }

    ##옵션값설정
    function Set_Option($param1) {
        $this->option = $param1;
    }

    ##메세지표시
    function msg($param1) {
        echo "<script>alert('$param1');</script>";
    }

    function info() {
        $buffer = "returl->".$this->returnUrl;
        $buffer .= "\\ncpCode->".$this->cpCode;
        $buffer .= "\\nEndPointURL->".$this->EndPointURL;
        $buffer .= "\\noption->".$this->option;

        $buffer .= "\\nqryIP->".$this->qryIP;
        $buffer .= "\\nqryDomain->".$this->qryDomain;
        $buffer .= "\\noption2->".$this->option2;
        $this->msg($buffer);
    }

    ##명령실행(아이핀 인증키 호출용)
    function Exec_Ipin() {
        $this->cmd = "$this->exe $this->keypath $this->cpCode \"{$this->reserved1}\" \"{$this->reserved2}\" $this->EndPointURL $this->logpath $this->option";
        //echo "#cmd->".$cmd."<br>";
        //$this->msg($cmd);
        exec($this->cmd, $out, $ret);

        $this->pubkey=$out[0];
        $this->sig=$out[1];
        $this->curtime=$out[2];

        return $out;

    }

    function Get_pubkey() {
        return $this->pubkey;
    }

    ##명령실행(아이핀 결과값 해석용)
    function Exec_IpinResult($encPsnlInfo,$WEBPUBKEY,$WEBSIGNATURE) {

    $cpubkey = $WEBPUBKEY;       //server publickey
	$csig = $WEBSIGNATURE;    //server signature
	$encdata = $encPsnlInfo;     //PERSONALINFO
	$this->option3 = "USL";
		
	// 명령어
	$this->cmd3 = "$this->exe $this->keypath $this->cpCode $this->EndPointURL $cpubkey $csig $encdata $this->logpath $this->option3";

    //echo "#cmd->".$cmd."<br>";
    //$this->msg($cmd);
    exec($this->cmd3, $out, $ret);
    return $out;

    }

    ##명령실행-실명인증실행(이름,주민등록번호)
    function Exec_Name($name,$ssn,$qryKndCd="1") {
        // 모듈호출명령어
        $this->qryDt = date("Ymd");               // 현재일자 20101101 과 같이 숫자8자리 입력되어야함.
        $this->cmd2="{$this->exe} \"{$name}\" \"{$ssn}\" $this->cpCode $this->qryBrcCd $this->qryBrcNm $this->qryId $qryKndCd $this->qryRsnCd $this->qryIP $this->qryDomain $this->qryDt $this->EndPointURL $this->option2";

        //$this->msg($this->cmd2);

        exec($this->cmd2, $out, $ret);

        $buf = "";
        foreach($out as $a => $b) {
            $buf = $buf.chr(16).chr(19).$b;    //외부출력문자출력
        }

        $result ="";
        ##결과값 확인
        if($ret <=200)  { $result=sprintf("B%03d", $ret); }
        else            { $result=sprintf("S%03d", $ret); }

//$this->msg("inner result=".$result);

        return $result;
    }

    ##명령실행-실명인증실행(이름,주민등록번호)
    function Exec_IpinName($name,$ssn,$qryKndCd="1") {
        // 모듈호출명령어
        $this->qryDt = date("Ymd");               // 현재일자 20101101 과 같이 숫자8자리 입력되어야함.
        $this->option3 = "UNLD";
        $this->cmd2="{$this->exe} \"{$name}\" \"{$ssn}\" $this->cpCode $this->qryBrcCd $this->qryBrcNm $this->qryId $qryKndCd $this->qryRsnCd $this->qryIP $this->qryDomain $this->qryDt $this->EndPointURL $this->logpath $this->option3";

        
// $this->msg($this->cmd2);
        exec($this->cmd2, $out, $ret);

        $ci = "";
        $di = "";

        $buf = "";
        foreach($out as $a => $b) {
//$this->msg("b=".$b);
            //$buf = $buf.chr(16).chr(19).$b;    //외부출력문자출력
        }

        if($ret == 0) {    
            $arr_ci = explode(":",$out[27]);
            $arr_di = explode(":",$out[28]);

            $ci = trim($arr_ci[sizeof($arr_ci)-1]);
            $di = trim($arr_di[sizeof($arr_di)-1]);
        }

        $result ="";
        ##결과값 확인
        if($ret <=200)  { $result=sprintf("B%03d", $ret); }
        else            { $result=sprintf("S%03d", $ret); }

//$this->msg("inner result=".$result);
        return $result.'|'.$ci.'|'.$di;
    }

    ##pCode보기
    function show_id() {
        $this->msg("cpCode=".$this->cpCode);
    }

    ##실명인증 qryRsnCd 변경
    function Set_qryRsnCd($param1) {
        $this->qryRsnCd = $param1;
    }

    ##IPIN 명령어 반환
    function Get_cmd() {
        return $this->cmd;
    }

    ##실명인증 명령어 반환
    function Get_cmd2() {
        return $this->cmd2;
    }

    ##실명인증결과창 명령어 반환
    function Get_cmd3() {
        return $this->cmd3;
    }

    ##ReturnForm - IPIN -
    function Make_Inform() {
        echo "<form name='kcbInForm' method='post' >
          <input type='hidden' name='IDPCODE' value='$this->idpCode' />
          <input type='hidden' name='IDPURL' value='$this->idpUrl' />
          <input type='hidden' name='CPCODE' value='$this->cpCode' />
          <input type='hidden' name='CPREQUESTNUM' value='$this->curtime' />
          <input type='hidden' name='RETURNURL' value='$this->returnUrl' />
          <input type='hidden' name='WEBPUBKEY' value='$this->pubkey' />
          <input type='hidden' name='WEBSIGNATURE' value='$this->sig' />
        </form>
        <form name='kcbOutForm' method='post'>
          <input type='hidden' name='encPsnlInfo' />
          <input type='hidden' name='virtualno' />
          <input type='hidden' name='dupinfo' />
          <input type='hidden' name='realname' />
          <input type='hidden' name='cprequestnumber' />
          <input type='hidden' name='age' />
          <input type='hidden' name='sex' />
          <input type='hidden' name='nationalinfo' />
          <input type='hidden' name='birthdate' />
          <input type='hidden' name='coinfo1' />
          <input type='hidden' name='coinfo2' />
          <input type='hidden' name='ciupdate' />
          <input type='hidden' name='cpcode' />
          <input type='hidden' name='authinfo' />
          <input type='hidden' name='realCheck' value=''/>
          <input type='hidden' name='resinum1' value=''/>
          <input type='hidden' name='resinum2' value=''/>
          <input type='hidden' name='authtype' value=''/>
        </form>";
    }

    ##
    function Make_RunScript() {
    if ($this->TESTMODE == "1") {
        $ActionUrl = "https://tipin.ok-name.co.kr:8443/tis/ti/POTI01A_LoginRP.jsp";
    } else {
        $ActionUrl = "https://ipin.ok-name.co.kr/tis/ti/POTI01A_LoginRP.jsp";
    }
    echo "
       <script language='javascript'>
            function certKCBIpin() {
                var popupWindow = window.open( '', 'kcbPop', 'left=200, top=100, status=0, width=450, height=550' );
                document.kcbInForm.target = 'kcbPop';
                document.kcbInForm.action = '$ActionUrl';
                document.kcbInForm.submit();
                popupWindow.focus();
            }
        </script>";
    }


    function Add_Fileds($tabname,$add_fields,$add_types,$add_values) {
        $add_field_list = explode(",",$add_fields);
        $add_field_type = explode(",",$add_types);
        $add_field_value = explode(",",$add_values);
        $Query = "show columns from $tabname where Field in ($add_fields)";
        $Result = @mysql_query($Query);
        $RecordCount = @mysql_num_rows($Result);
        if ( !$RecordCount ) $RecordCount = 0;
        if ( $RecordCount != count($add_field_list) )    //해당필드를 추가함
        {
            for ( $i=0; $i <= count($add_field_list)-1; $i++)
            {
                $Field = str_replace("'","",$add_field_list[$i]);
                $Query = " ALTER TABLE $tabname ADD $Field $add_field_type[$i] default '$add_field_value[$i]' ";
                @mysql_query($Query);
            }
        }
    }
}
?>