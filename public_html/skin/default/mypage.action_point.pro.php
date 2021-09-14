<?PHP
	include_once(dirname(__FILE__)."/../../include/inc.php");


	$_point = $_POST['_point'];

	// 사전체크
	if ( ereg_replace("[[:space:]]","",$_point) == "" ) 
		error_alt("숫자를 입력해주세요");

	if ( $_point %1000 <> 0 || $_point == 0 ) 
		error_alt("숫자는 천단위로 입력해주세요");

	if ( $_point > $row_member[action] ) 
		error_alt("보유하신 참여점수보다 큰 숫자는 입력할 수 없습니다.");	

	// odtMember - 업데이트
	$sque = "update odtMember set action = action - ${_point} , point = point + ${_point}  where id='".get_userid()."'";
//echo $sque."<br>";
	mysql_query($sque);


	// odtActionLog - 차감
	$sque = "insert into odtActionLog set acID= '".$row_member[id]."', acTitle ='참여점수의 적립금 변환', acPoint='" . ( $_point * -1 ) . "', acRegidate = now(), ip='".$_SERVER[REMOTE_ADDR]."'";
//echo $sque."<br>";
	mysql_query($sque);


	// odtPointLog - 증가
	$sque = "
		insert into odtPointLog set 
			pointID			= '".$row_member[id]."', 
			pointTitle		= '참여점수의 적립금 변환',
			pointPoint		= '" . $_point ."',
			pointResult		= '". $row_member[point] ."',
			pointStatus		= 'Y',
			pointRegidate	= now(),
			redRegidate		= now()
	";
//echo $sque."<br>";
	mysql_query($sque);

	error_frame_reload("적용하였습니다.");

?>