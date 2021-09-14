<?PHP

	include "inc.php";


	if(!strcmp($Form,"DataDelete")) {
		if($mode == "ROUTE") {
			## 접속경로 데이타를 삭제한다. ###################################
			_MQ_noreturn("DELETE FROM  odtCounterRoute");
			error_loc_msg("_config.counter.form.php" , "접속경로 데이타가 모두 삭제되었습니다.");
		}
		else if($mode == "ALL") {
			_MQ_noreturn("DELETE FROM  odtCounterRoute");
			_MQ_noreturn("DELETE FROM  odtCounterData");
			_MQ_noreturn("DELETE FROM  odtCounterPerson");
			_MQ_noreturn("DELETE FROM  odtCounter");
			_MQ_noreturn("UPDATE odtCounterConfig SET Total_Num = 0 WHERE serialnum = '1'");

			error_loc_msg("_config.counter.form.php" , "작업을 잘 완료 하였습니다.");
		}
	}


	else {
		if($mode == "ROUTE") $comment = "접속경로별";
		else if($mode == "ALL") $comment = "모든";

		echo "
			<script language=\"javascript\">
				if(confirm(\"$comment 통계 자료에 대해 정말 초기화 하시겠습니까?   \"))
					self.location.replace('?Form=DataDelete&mode=$mode')
				else
					self.location.replace('_config.counter.form.php')
			</script>";

		exit;
	}
?>


