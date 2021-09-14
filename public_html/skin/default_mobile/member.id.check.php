<?PHP

	//include dirname(__FILE__)."/inc.header.php";
	include dirname(__FILE__)."/../../include/inc.php";
	echo "<script src='/include/js/jquery-1.11.2.min.js'></script>";

	$_id = ($_GET[_id] ? $_GET[_id] : ( $_POST[_id] ? $_POST[_id] : $_id ));

	if( !$_id ) {
		error_alt($_id);
	}
	$id = trim($_id);
	if(preg_match('/\s/',$id)) { error_alt("아이디는 공백을 포함할 수 없습니다."); }
	$id_replace = preg_replace("/[a-z0-9-_]/i",'',$id);
	if( strlen(trim($id_replace))>0 ) { error_alt("아이디는 알파벳과 숫자 및 특수문자(-,_)만 입력 가능합니다."); }
	if( strlen($id)<4 || strlen($id)>12 ) { error_alt("아이디는 4자 이상 12자 이내로 해주시기 바랍니다."); }

	$is_id = _MQ("select count(*) as cnt from odtMember where id='".$_id."'");
	if($is_id[cnt] > 0) {
		echo "
				<script>
					$(\"input[name=idCheck1]\",window.parent.document).val('');
				</script>
			";
		error_alt("이미 사용중인 아이디 입니다.");
	} else {
		echo "
				<script>
					$(\"input[name=idCheck1]\",window.parent.document).val(1);
				</script>
			";
		error_alt("사용할 수 있는 아이디 입니다.");		
	}

?>