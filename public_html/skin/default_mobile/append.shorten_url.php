<?PHP
	header("Content-Type:text/html;charset=utf-8"); 

	include_once(dirname(__FILE__)."/../../include/inc.php");

	// url 축소하기 적용--------------------------------------
	//$org_url = "http://".$_SERVER[HTTP_HOST]."/?pn=product.view&pcode=".$_GET['pcode'];
	$org_url = rewrite_url($_GET['pcode']);
	$app_shorten_url = get_shortURL_2($org_url);
	$app_shorten_url = $app_shorten_url?$app_shorten_url:$org_url;

	$que = "insert into odtSnsLog set
					sl_pcode			=	'".$_GET[pcode]."',
					sl_type				=	'".$_GET[type]."',
					sl_ip				=	'".$_SERVER[REMOTE_ADDR]."',
					sl_rdate			=	now()";

	$res = mysql_query($que);

	switch($_GET['type']){

			// 트위터
			case "twitter":
					echo "<script>location.href=('http://twitter.com/intent/tweet?text=" . $_GET['text'] . "' + ' ' + encodeURIComponent('${app_shorten_url}'));</script>";
					break;

			// 페이스북
			case "facebook":
					echo "<script>location.href=('http://www.facebook.com/sharer.php?u=${app_shorten_url}&t=".$_GET['t']."');</script>";
					break;
			
	}

?>