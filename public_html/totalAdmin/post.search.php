<?PHP

	// 팝업형태 적용
	$app_mode = "popup";
	include_once("inc.header.php");

	echo "<style>body{min-width:600px!important;width:600px!important;background-color:#ffffff}</style>";

	// 넘어온 변수 - $post_keyword (동, 읍, 면 검색)
	if($post_keyword) {
		echo "
		<div class='member_ly_pop_post'><div class='zipcode'>
		<table class='TB_zipcode_list' summary='우편번호검색결과'>
			<thead>
				<tr>
					<th scope='col'>우편번호</th>
					<th scope='col'>시/도</th>
					<th scope='col'>시/군/구</th>
					<th scope='col'>동/읍/면</th>
					<th scope='col'>번지</th>
					<th scope='col'>주소선택</th>
				</tr>
			</thead> 
			<tbody> 
		";
		$res = _MQ_assoc("select * from odtZipcode where DONG like '%" . $post_keyword . "%' ");
		foreach( $res as $k=>$v ){
			// 우편번호 분리 ( dddddd -> ddd-ddd)
			preg_match_all('/(...)(...)/',rm_str($v[ZIPCODE]),$_post); 
			//번지
			$app_bunji = trim($v[BUNJI]);
			echo "
				<tr>
					<td>" . $_post[1][0]."-".$_post[2][0] . "</td>
					<td>" . $v[SIDO] . "</td>
					<td>" . $v[GUGUN] . "</td>
					<td>" . $v[DONG] . "</td>
					<td>" . $app_bunji . "</td>
					<td><span class='shop_btn_pack' style='float: none;'><input type='button' class='small white' value='주소선택' onclick=\"javascript:choice_post('". $_post[1][0] ."' , '".$_post[2][0]."' , '". $v[SIDO] ."' , '". $v[GUGUN] ."' , '". $v[DONG] ."');\" /></span></td>
				</tr>				
			";
		}

		echo "
			</tbody> 
		</table></div></div>";

	}

	echo "
		<SCRIPT>
			// 우편번호 넣기
			function choice_post( post1, post2 , sido , gugun , dong ){
				parent.$('.post_form_page').trigger('close');
				parent.$('#_post1').val(post1);
				parent.$('#_post2').val(post2).change();
				parent.$('#_addr1').val(trim(sido +' '+ gugun +' '+ dong));
				parent.$('#_addr2').val('');
				parent.$('#_addr2').focus();
			}
			// 앞뒤 공백자르기 함수
			function trim(str) {
				return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
			}
		</SCRIPT>
	";
?>
