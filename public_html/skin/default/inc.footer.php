		<script src="/include/js/jquery.validate.setDefault.js" type="text/javascript"></script>

		<?
		if( preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT']) ) {
		?>
			<div id="backToMobile">
			<a href="<?='/?_mobilemode=chk&'.str_replace('_pcmode=chk','',$_SERVER[QUERY_STRING])?>">모바일 버전으로 돌아가기</a>
			</div>
		<? } ?>
		
	</body>
</html>