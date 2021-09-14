<?PHP
	$app_mode = "popup" ;
	include_once("inc.header.php");

	if(sizeof($_chk) > 0 ) {
		$arr_chk = array_filter(array_unique($_chk));
	} else {
		error_msg("발송할 이메일이 선택되지 않았습니다.");
	}

	// 저장한 정보 불러오기
	include_once("../upfiles/normal/mailing.profile.php");
	$ex_app_profile = explode("," , $app_profile);

	
	// 추가한 이메일과 합함
	$plus_profile = array_filter(array_unique(array_merge($ex_app_profile , $arr_chk)));

	// 이메일 총 개수
	$plus_profile_cnt = sizeof($plus_profile);

	if($plus_profile_cnt > 100) {
		//error_msg("최대 100명에게만 발송할 수 있습니다.");
	}

	$fp = fopen("../upfiles/normal/mailing.profile.php", "w");
	fputs($fp,"<?PHP\n\t\$app_profile = '".implode("," , $plus_profile)."';?>");
	fclose($fp);

	echo "
	<SCRIPT>
		$(document).ready(function() {
			$('input[name=_cnt]' , opener.document ).val('". $plus_profile_cnt ."');
			$('#app_cnt' , opener.document ).html('". number_format($plus_profile_cnt) ."');
			self.close();
		});
	</SCRIPT>
	";
	exit;

?>