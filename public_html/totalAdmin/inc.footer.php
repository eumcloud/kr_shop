
				<div style="height:30px;"></div>

			</div>
		</div>
		<!-- //내용 -->


<?PHP
	if($app_mode <> "popup") {
?>
	</div>
	<!-- // 가운데 -->


	
	<!-- 푸터 -->
	<div id="footer">
		<div class="copyright">Copyright &copy; <?=substr(rm_str($row_setup['licenseNumber']),0,4) ." ". $row_setup['site_name']?>. All Rights Reserved.
		<!--<img src="../images/copyright.png" alt="카피라잇" title="" />--></div>
	</div>
	<!-- // 푸터 -->

<?PHP
	}
?>

</div>

</body>
</html>
<script src="/include/js/jquery.validate.setDefault.js" type="text/javascript"></script>
<SCRIPT LANGUAGE="JavaScript">

	// - radio 선택시 특정값 체크할 경우 ::: 예)개인정보 취급방침과 이용약관에 동의하여야 등록이 가능합니다. ---
	jQuery.validator.addMethod('correctAnswerRadio', function(value, element) {
		var selectedVal = $('input[name='+element+']:checked').val();
		//Correct Value
		if(selectedVal === value){ return true;}
		else{ return false;}
	});
	// - radio 선택시 특정값 체크할 경우 ---

	// - 메뉴접기 ---
	$("#open_close_btn_close").click(function(){
		$('#depth2_leftmenu').removeClass("container").addClass("container_hide");
		$(this).css("display" , "none");
		$(".btn_open").css("display" , "block");
	});
	// - 메뉴접기 ---
	// - 메뉴열기 ---
	$("#open_close_btn_open").click(function(){
		$('#depth2_leftmenu').removeClass("container_hide").addClass("container");
		$(this).css("display" , "none");
		$(".btn_close").css("display" , "block");
	});
	// - 메뉴열기 ---
</SCRIPT>


