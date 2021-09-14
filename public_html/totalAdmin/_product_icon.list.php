<?PHP
	include_once("inc.header.php");

	// 아이콘 유형 설정
	if( $_type ) {
		$_type_name = $arr_product_icon_type[$_type];
		$app_pass_type = "<input type=hidden name=pass_type value='{$_type}'>";
	}
	else {
		$_type_name = "";
		$app_pass_type = "유형 : " . _InputSelect( "pass_type" , array_keys($arr_product_icon_type) , $pass_type , "" , array_values($arr_product_icon_type) , "-유형선택-");
	}

	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);

?>
				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=post action='<?=$PHP_SELF?>' autocomplete='off' >
					<input type=hidden name=mode value=search>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">아이콘구분</td>
								<td class="conts"><?=_InputSelect( "pass_type" , array_keys($arr_product_icon_type) , $pass_type , "" , array_values($arr_product_icon_type) , "-선택-")?></td>
								<td class="article">아이콘타이틀</td>
								<td class="conts"><input type=text name=pass_title class=input_text value="<?=$pass_title?>"></td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == search) {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="전체목록" >전체목록</a></span>
							<?}?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_product_icon.form.php?_mode=add" class="medium red" title="아이콘등록" >아이콘등록</a></span>

						</div>
					</div>

					<?=_DescStr("
						<B>자동사용 아이콘은 다음과 같습니다.</B><br>
						<img src='/pages/images/upper_ic_2.gif' alt='오늘오픈' /> , 
						<img src='/pages/images/upper_ic_5.gif' alt='마감임박' /> ,
						<img src='/pages/images/upper_ic_4.gif' alt='무료배송' />
					");?>
					<?/* 베스트 아이콘 삭제 2017-08-29 SSJ
						<img src='/pages/images/upper_ic_1.gif' alt='베스트' /> , 
						<img src='/pages/images/upper_ic_2.gif' alt='오늘오픈' /> , 
						<img src='/pages/images/upper_ic_3.gif' alt='마감임박' /> ,
						<img src='/pages/images/upper_ic_4.gif' alt='무료배송' />
					*/?>

</form>
				</div>
				<!-- // 검색영역 -->



				<!-- 리스트영역 -->
				<div class="content_section_inner">
					

					<!-- 리스트 제어버튼영역
					<div class="top_btn_area">
						<span class="shop_btn_pack"><a href="#none" class="small white" title="전체선택" >전체선택</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="#none" class="small white" title="전체선택" >전체선택해제</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->


					<table class="list_TB" summary="리스트기본">
						<colgroup>
							<col width="120px"/><col width="300px"/><col width="*"/><col width="150px"/><col width="200px"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">구분</th>
								<th scope="col" class="colorset">아이콘이름</th>
								<th scope="col" class="colorset">아이콘이미지</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 

<?PHP

	// 검색 체크
	$s_query = " where 1 ";
	if( $_type !="" ) { $s_query .= " and pi_type='${_type}' "; }
	if( $pass_type !="" ) { $s_query .= " and pi_type='${pass_type}' "; }

	$listmaxcount = 10 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtProductIcon $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from odtProductIcon {$s_query} ORDER BY pi_idx asc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small white' onclick='location.href=(\"_product_icon.form.php?_type={$_type}&_mode=modify&_uid=$v[pi_uid]&_PVSC=${_PVSC}\");'></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_product_icon.pro.php?_mode=delete&_uid=$v[pi_uid]&_PVSC=${_PVSC}\");'></span>";

		$_num = $TotalCount - $count - $k ;

		$app_src = "../upfiles/icon/$v[pi_img]";
		if( $v[pi_img] ) {
			$app_title = "";
			if( $v[pi_title] ) {
				$app_title = " title='$v[pi_title]' ";
			}
			$app_product_icon = "<img src='$app_src' ${app_title}>";
		}
		else {
			$app_product_icon = "&nbsp;";
		}


		echo "
							<tr>
								<td>".${_num}."</td>
								<td>" . $arr_product_icon_type[$v[pi_type]] . "</td>
								<td>$v[pi_title]</td>
								<td>${app_product_icon}</td>
								<td>
									<div class='btn_line_up_center'>
										".$_mod."
										<span class='shop_btn_pack'><span class='blank_3'></span></span>
										".$_del."
									</div>
								</td>
							</tr>
		";

	}

?>

						</tbody> 
					</table>


					<!-- 페이지네이트 -->
					<div class="list_paginate">			
						<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=")?>
					</div>
					<!-- // 페이지네이트 -->

			</div>

<?PHP
	include_once("inc.footer.php");
?>