<?
	$bv = _MQ(" select * from odtPopup where p_view='Y' and p_sdate <= CURDATE( ) and p_edate >= CURDATE( ) ORDER BY p_idx asc , p_uid desc limit 1 ");
	if( $_COOKIE["AuthPopupClose_" . $bv[p_uid]] <> "Y" && $bv[p_uid]) {

		$_img = IMG_DIR_BANNER . $bv[p_img];
		if($bv[p_link]) {

			// 링크가 http 를 포함하지 않을경우 자동으로 m을 붙인다.
			//if(!preg_match("/http/",$bv[p_link])) $bv[p_link] = "/m/".$bv[p_link];

			$img_and_link = "<a href='".$bv[p_link]."' target='".$bv[p_target]."' rel='external'><img src='". $_img ."' alt='".$bv[p_title]."' /></a>";
		} else {
			$img_and_link = "<img src='". $_img ."' alt='".$bv[p_title]."' />";
		}

?>

<!-- 관리자설정 기본팝업창 -->
<div class="popup" id="event_popup_div_<?=$bv[p_uid]?>">
	<div class="img">
		<?=$img_and_link?>
	</div>
	<div class="btn_area">
		<label><input type="checkbox" onclick="common_frame.location.href=('/pages/member.login.pro.php?_mode=popup_close&uid=<?=$bv[p_uid]?>')"/>오늘하루 창닫기</label>
		<a href="#none" onclick="popup_display('<?=$bv[p_uid]?>')" title="닫기" class="btn_close"><span class="shape"></span></a>
	</div>
</div>
<!-- / 관리자설정 기본팝업창 -->

<script>
function popup_display( uid ){
	$("#event_popup_div_" + uid).hide();
}
</script>

<?
	}
?>