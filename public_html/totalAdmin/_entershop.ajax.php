<?PHP
	include "inc.php";

	$r = _MQ("select count(*) as cnt from odtMember where id='".$id."'");
	if( $r[cnt] > 0 ) {
		echo "
			<script>
				obj = parent.document.getElementById('searchinnerHTML');
				obj.innerHTML = '<span style=color:red;>사용불가ID</span>';
			</script>
		";
	}
	else {
		echo "
			<script>
				obj = parent.document.getElementById('searchinnerHTML');
				obj.innerHTML = '<span style=color:5AAC5A;>사용가능ID</span>';
			</script>
		";
	}
	exit;
?>