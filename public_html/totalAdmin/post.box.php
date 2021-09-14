
<!-- 우편번호 -->
<div class="member_ly_pop post_form_page">
	
	<div class="zipcode">
	<form name="frm_post" id="frm_post" method=post target="post_search_frame" action="/include/post.search.php">
	
		<a  href='#none' id='post_form_page_close_x'   class="closex  close" title="닫기" ></a>
		<div class="timg"></div>

		<div class="guideTX">찾고자하시는 지역의 동이나 읍/면의 이름을 공백없이 입력하신후 검색을 누르세요</div>
		
		<div class="form">
			<input type="text" name="post_keyword" class="input" />
			<span class="shop_btn_pack"><input type="submit" class="input_30 dark" value="검색하기" style="width:100px;" /></span>
		</div>

		<div class="result" style="width:610px;">
			<iframe name="post_search_frame" width=610 height=400 frameborder=0 ></iframe>
		</div>
	<form>
	</div>

</div>
<!-- // 우편번호 -->


<script>
	// - 주소찾기 박스 open ---
	function post_view(){
		// -- 박스 노출 ---
		$('.post_form_page').lightbox_me({
			centered: true, 
			closeEsc: false,
			onLoad: function() { 
				$('.post_form_page').find('input:first').focus()
			}
		});
		// -- 박스 노출 ---
	}
	// - 주소찾기 박스 open ---
</script>
