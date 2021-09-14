<?PHP
	include "inc.php";
?>


<div class=zipcode>
	<form id=frm_post method=post name=frm_post action=/include/post.search.php >
	<a id=post_form_page_close_x title=닫기 class="closex close sprited" href="#none"></a>
	<div class=guideTX style="font-size:12px;padding:2px;color:gray">찾고자하시는 지역의 동이나 읍/면의 이름을 공백없이 입력하신후 검색을 누르세요</div>
	<div class=form><INPUT class=input name=post_keyword><input type="submit" value="검색" class="btn"></div>
	<div class=result style="WIDTH: 610px"><iframe height=400 frameBorder=0 width=610 name=post_search_frame></iframe></div>
	<form>
</div>
