


	<!-- 푸터 -->
	<div class="footer">

		<div class="btn_go_box">
			<ul>
				<li><a href="/" target="_blank" class="btn ic_home">내홈페이지</a></li>
				<li><a href="/totalAdmin/?_pcmode=chk&<?=str_replace('_mobilemode=chk','',$_SERVER['QUERY_STRING'])?>" class="btn ic_pc">PC버전보기</a></li>
				<li><a href="logout.php" class="btn ic_logout">로그아웃</a></li>
			</ul>
		</div>
		<div class="copyright">Copyright &copy; <?=substr(rm_str($row_setup['licenseNumber']),0,4) ." ". $row_setup['site_name']?>. All Rights Reserved.</div>
	</div>
	<!-- /푸터 -->

</div>
</body>
<?php

	include dirname(__FILE__)."/inc.footer.php";

?>