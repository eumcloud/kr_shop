<?
	# 카드결제에 필요한 셋팅
    /**************************
     * 1. 라이브러리 인클루드 *
     **************************/
    require_once(PG_DIR."/inicis/libs/INILib.php");

    $row_setup[P_SID] = $row_setup[P_SID]?$row_setup[P_SID]:$row_setup[P_ID];
    $_pg_mid = $r[paymethod]=='V'?$row_setup[P_SID]:$row_setup[P_ID];

		/***************************************
     * 2. INIpay50 클래스의 인스턴스 생성  *
     ***************************************/
    $inipay = new INIpay50;

    /**************************
     * 3. 암호화 대상/값 설정 *
     **************************/
    $inipay->SetField("inipayhome", PG_DIR."/inicis");       // 이니페이 홈디렉터리(상점수정 필요)
    $inipay->SetField("type", "chkfake");      // 고정 (절대 수정 불가)
    $inipay->SetField("debug", false);        // 로그모드("true"로 설정하면 상세로그가 생성됨.)
    $inipay->SetField("enctype","asym"); 			//asym:비대칭, symm:대칭(현재 asym으로 고정)
    $inipay->SetField("admin", "1111"); 				// 키패스워드(키발급시 생성, 상점관리자 패스워드와 상관없음)
    $inipay->SetField("checkopt", "false"); 		//base64함:false, base64안함:true(현재 false로 고정)

		//필수항목 : mid, price, nointerest, quotabase
		//추가가능 : INIregno, oid
		//*주의* : 	추가가능한 항목중 암호화 대상항목에 추가한 필드는 반드시 hidden 필드에선 제거하고 
		//          SESSION이나 DB를 이용해 다음페이지(INIsecureresult.php)로 전달/셋팅되어야 합니다.
    $inipay->SetField("mid",$_pg_mid);            // 상점아이디
    $inipay->SetField("price", $r[tPrice]);                // 가격
    $inipay->SetField("nointerest", "no");             //무이자여부(no:일반, yes:무이자)
    $inipay->SetField("quotabase", iconv("utf-8","euckr","선택:일시불:2개월:3개월:6개월"));//할부기간

    /********************************
     * 4. 암호화 대상/값을 암호화함 *
     ********************************/
    $inipay->startAction();

    /*********************
     * 5. 암호화 결과  *
     *********************/
 		if( $inipay->GetResult("ResultCode") != "00" ) 
		{
			echo $inipay->GetResult("ResultMsg");
			exit(0);
		}

    /*********************
     * 6. 세션정보 저장  *
     *********************/
		$_SESSION['INI_MID'] = $_pg_mid;	//상점ID
		$_SESSION['INI_ADMIN'] = "1111";			// 키패스워드(키발급시 생성, 상점관리자 패스워드와 상관없음)
		$_SESSION['INI_PRICE'] = $r[tPrice];     //가격 
		$_SESSION['INI_RN'] = $inipay->GetResult("rn"); //고정 (절대 수정 불가)
		$_SESSION['INI_ENCTYPE'] = $inipay->GetResult("enctype"); //고정 (절대 수정 불가)


		// PG사에 맞게 변수 설정

		if($r[paymethod] == "C") $gopaymethod = "Card";
		if($r[paymethod] == "L") $gopaymethod = "DirectBank";
		if($r[paymethod] == "V") $gopaymethod = "VBank";
//		if($r[o_paymethod] == "H") $gopaymethod = "HPP";
//		if($r[o_paymethod] == "E") $gopaymethod = "VBank";


?>



						<form name=ini id="ini_form" method=post action="/pages/shop.order.result_inicis.pro.php" target="common_frame" onSubmit="return pay(this)"> 
						<input type="hidden" name="gopaymethod"		value="<?=$gopaymethod?>">
						<input type="hidden" name="paymethod"		value="<?=$gopaymethod?>">
						<input type="hidden" name="goodname"		value="<?=$app_product_name?>">
						<input type="hidden" name="buyername"		value="<?=$r[ordername]?>">
						<input type="hidden" name="buyeremail"		value="<?=$r[orderemail]?>">
						<input type="hidden" name="buyertel"		value="<?=phone_print($r[orderhtel1],$r[orderhtel2],$r[orderhtel3])?>"	>
						<!-- <input type="hidden" name="price"			value="<?=$r[tPrice]?>"	>
						<input type="hidden" name="mid"				value="<?=$_pg_mid?>"    >
						<input type="hidden" name="nointerest"		value="no">
						<input type="hidden" name="quotabase"		value="선택:일시불"> -->
		<?
		if($r[paymethod] == "V") { $_virtual_due_date = date('Ymd', time() + ($row_setup[P_V_DATE] * 86400));
		?>
						<input type="hidden" name="acceptmethod"	value="HPP(2):OCB:va_receipt:Vbank(<?=$_virtual_due_date?>):useescrow">
		<?
		} else {
		?>

						<input type="hidden" name="acceptmethod"	value="HPP(2):Card(0):OCB:VBank:DirectBank:receipt:cardpoint<?=$gopaymethod=='DirectBank'?':useescrow':''?>">
		<?
		}
		?>


						<input type="hidden" name=oid size=40		value="<?=$ordernum?>">

						<?/* 기타설정 */?>
						<input type=hidden name=currency size=20 value="WON">

						<?/*
						플러그인 좌측 상단 상점 로고 이미지 사용
						이미지의 크기 : 90 X 34 pixels
						플러그인 좌측 상단에 상점 로고 이미지를 사용하실 수 있으며,
						주석을 풀고 이미지가 있는 URL을 입력하시면 플러그인 상단 부분에 상점 이미지를 삽입할수 있습니다.
						*/?>
						<input type=hidden name=ini_logoimage_url  value="">


						<?/*
						좌측 결제메뉴 위치에 이미지 추가
						이미지의 크기 : 단일 결제 수단 - 91 X 148 pixels, 신용카드/ISP/계좌이체/가상계좌 - 91 X 96 pixels
						좌측 결제메뉴 위치에 미미지를 추가하시 위해서는 담당 영업대표에게 사용여부 계약을 하신 후
						주석을 풀고 이미지가 있는 URL을 입력하시면 플러그인 좌측 결제메뉴 부분에 이미지를 삽입할수 있습니다.
						*/?>
						<input type=hidden name=ini_menuarea_url value="http://<?=$_SERVER[HTTP_HOST]."/upfiles/normal".$r[s_glbimg]?>">


						<?/*
						플러그인에 의해서 값이 채워지거나, 플러그인이 참조하는 필드들
						삭제/수정 불가
						uid 필드에 절대로 임의의 값을 넣지 않도록 하시기 바랍니다.
						*/?>
		
						<input type=hidden name=ini_encfield value="<?php echo($inipay->GetResult("encfield")); ?>">
						<input type=hidden name=ini_certid value="<?php echo($inipay->GetResult("certid")); ?>">
		
						<input type=hidden name=quotainterest value="">
						<input type=hidden name=cardcode value="">
						<input type=hidden name=cardquota value="">
						<input type=hidden name=rbankcode value="">
						<input type=hidden name=reqsign value="DONE">
						<input type=hidden name=encrypted value="">
						<input type=hidden name=sessionkey value="">
						<input type=hidden name=uid value=""> 
						<input type=hidden name=sid value="">
						<input type=hidden name=version value=4000>
						<input type=hidden name=clickcontrol value="">

						</form>




		<script language=javascript src="http://plugin.inicis.com/pay61_secuni_cross.js"></script>

		<script language=javascript>
		StartSmartUpdate();	// 플러그인 설치(확인)
		</script>
		<script language="JavaScript" type="text/JavaScript">
		var openwin;

		function pay(frm)
		{
			// MakePayMessage()를 호출함으로써 플러그인이 화면에 나타나며, Hidden Field
			// 에 값들이 채워지게 됩니다. 일반적인 경우, 플러그인은 결제처리를 직접하는 것이
			// 아니라, 중요한 정보를 암호화 하여 Hidden Field의 값들을 채우고 종료하며,
			// 다음 페이지인 INIsecureresult.php로 데이터가 포스트 되어 결제 처리됨을 유의하시기 바랍니다.

			if(document.ini.clickcontrol.value == "enable")
			{
				
				if(document.ini.goodname.value == "")  // 필수항목 체크 (상품명, 상품가격, 구매자명, 구매자 이메일주소, 구매자 전화번호)
				{
					alert("상품명이 빠졌습니다. 필수항목입니다.");
					return false;
				}
				else if(document.ini.buyername.value == "")
				{
					alert("구매자명이 빠졌습니다. 필수항목입니다.");
					return false;
				} 
				else if(document.ini.buyeremail.value == "")
				{
					alert("구매자 이메일주소가 빠졌습니다. 필수항목입니다.");
					return false;
				}
				else if(document.ini.buyertel.value == "")
				{
					alert("구매자 전화번호가 빠졌습니다. 필수항목입니다.");
					return false;
				}
				else if(ini_IsInstalledPlugin() == false) // 플러그인 설치유무 체크
				{
					alert("\n이니페이 플러그인 128이 설치되지 않았습니다. \n\n안전한 결제를 위하여 이니페이 플러그인 128의 설치가 필요합니다. \n\n다시 설치하시려면 Ctrl + F5키를 누르시거나 메뉴의 [보기/새로고침]을 선택하여 주십시오.");
					return false;
				}
				else
				{

								 
					if (MakePayMessage(frm))
					{
						disable_click();
						return true;
					}
					else
					{
						if( IsPluginModule() ) //plugin타입 체크
						{
						alert("결제를 취소하셨습니다.");
						}
						return false;
					}
				}
			}
			else
			{
				return false;
			}
		}


		function enable_click()
		{
			document.ini.clickcontrol.value = "enable"
		}

		function disable_click()
		{
			document.ini.clickcontrol.value = "disable"
		}

		function focus_control()
		{
			if(document.ini.clickcontrol.value == "disable")
				openwin.focus();
		}

		function ini_submit()
		{
			$("#ini_form").trigger("submit");
		}

		$(document).ready(function() {

		 enable_click();
		 
		 $("body").focus(function() {
			 focus_control();
		 });

		});
//-->
</script>