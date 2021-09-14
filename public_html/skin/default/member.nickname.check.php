<?PHP

	include dirname(__FILE__)."/inc.header.php";

	if( !$_nickname ) {
		error_msg("닉네임을 입력하세요.");
	}

	if(strlen(iconv('utf8','euc-kr',$_nickname)) > 10 || strlen(iconv('utf8','euc-kr',$_nickname)) < 4) {
		error_msg("닉네임은 한글(2자~5자), 영문숫자(4자~10자) 이내로 해주시기 바랍니다.");
	}

	$is_nick = _MQ("select count(*) as cnt from odtMember where chatNickName='".$_nickname."'");
	if($is_nick[cnt] > 0) {
		echo "
				<script>
					$(\"input[name=nickCheck1]\",window.parent.document).val('');
				</script>
			";
		error_msg("이미 사용중인 닉네임 입니다.");
	} else {
		echo "
				<script>
					$(\"input[name=nickCheck1]\",window.parent.document).val(1);
				</script>
			";
		error_msg("사용할 수 있는 닉네임 입니다.");		
	}

?>