<!-- 팝업 -->
<?PHP
		// - 팝업 ---
		echo "<div ID='event_popup' style='POSITION: absolute; LEFT:50%; margin-left:-630px; HEIGHT: 0px; TOP:0px; width:0px; Z-INDEX: 999; display:block;'>";

		$bres = _MQ_assoc(" select * from odtPopup where p_view='Y' and p_sdate <= CURDATE( ) and p_edate >= CURDATE( ) ORDER BY p_idx asc , p_uid desc ");
		foreach( $bres as $k=>$v ){

			if( $_COOKIE["AuthPopupClose_" . $v[p_uid]] <> "Y" ) {
				$_img = IMG_DIR_BANNER . $v[p_img];
				$_s = @getimagesize(".".$_img);
				$app_div_name = "event_popup_div_" . $v[p_uid];

				echo "
					<div id='". $app_div_name ."'>
						<div class='popup' style='POSITION: absolute;Z-INDEX: 101; LEFT:".$v[p_left]."px;TOP:".$v[p_top]."px;'>
							<div class='img'>
								<A HREF='".$v[p_link]."' target='".$v[p_target]."'><img src='". $_img ."' alt='".$v[p_title]."' /></a>
							</div>
							<div class='btn_area' style='width:".($_s[0] <= 300 ? 300 : $_s[0])."px;'>
								<label><input type='checkbox' onclick=\"common_frame.location.href=('/pages/member.login.pro.php?_mode=popup_close&uid=".$v[p_uid]."');popup_display('". $app_div_name ."');\" />오늘하루 창닫기</label>
								<a href='#none' onclick=\"popup_display('". $app_div_name ."');return false;\" title='닫기' class='btn_close'><!-- <img src='/pages/images/pop_close.png' alt='닫기' /> --></a>
							</div>
						</div>
					</div>
				";
			}
		}

		echo "</div>";
		// - 팝업 ---
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup_display( divname ){
		$("#" + divname).css("display" , "none");
	}
//-->
</SCRIPT>
<!-- 팝업 -->
