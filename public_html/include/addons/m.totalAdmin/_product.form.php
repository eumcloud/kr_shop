<?php

	// 페이지 표시
	$app_current_link = "/totalAdmin/_product.list.php";
	include dirname(__FILE__)."/wrap.header.php";


	$app_dir = "../../../upfiles/product";


	if($code) {

		$que = "
			select 
				p.*
			from odtProduct as p
			where p.code = '".$code."'
		";
		$row = _MQ($que);

		// - 텍스트 정보 추출 ---
		$row = array_merge($row , _text_info_extraction( "odtProduct" , $row['serialnum'] ));
		/*
		comment2 longtext NOT NULL, //상품 상세설명
		comment_proinfo text NOT NULL COMMENT '상품사용정보',
		comment_useinfo text NOT NULL COMMENT '업체이용정보',
		comment3 text NOT NULL,//주문확인서 주의사항
		p_relation text COMMENT '관련상품 코드 - 구분자 /',
		*/
		// - 텍스트 정보 추출 ---

		// 입점업체 정보 추출
		$que_customer = "select * from odtMember where id='". $row['customerCode'] ."' and userType='C' ";
		$row_customer = _MQ($que_customer);

	} 
	else {
		error_msg("잘못된 접근입니다.");
	}

	if($row['sale_date']) $sale_date = $row['sale_date'];
	else if($calDate) $sale_date = $calDate;
	else $sale_date = date('Y-m-d');

	if($row['sale_enddate'] && $row['sale_enddate']<>'0000-00-00') $sale_enddate = $row['sale_enddate'];
	else $sale_enddate = date('Y-m-d',strtotime($sale_date)+3600*24);

	$sale_dateh = $row['sale_dateh'];
	$sale_datem = $row['sale_datem'];
	$sale_enddateh = $row['sale_enddateh'];
	$sale_enddatem = $row['sale_enddatem'];
	if(!$sale_dateh) $sale_dateh = $row_setup['changeTime'];
	if(!$sale_enddateh) $sale_enddateh = $row_setup['changeTime'];
	if(!$sale_datem) $sale_datem = "00";
	if(!$sale_enddatem) $sale_enddatem = "00";

	// 아이콘 정보 배열로 추출
	$product_icon = get_product_icon_info_qry("product_name_small_icon");

?>


<form name="frm" method="post" ENCTYPE="multipart/form-data" action="_product.pro.php">
<input type="hidden" name="_mode" value="<?=($_mode ? $_mode : 'add')?>">
<input type="hidden" name="code" value="<?=$code?>">
<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">
<input type="hidden" name="save_chk" hname="옵션정보저장"><!-- // save_chk가 0 초과면 no save 상태이므로 옵션저정해야 함 -->
<input type="hidden" name="v_color" value="<?=$row['v_color']?>"/>


	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container">

		<!-- ●●●●● 데이터폼 -->
		<div class="data_form">


			<!-- 테이블형 div colspan 하고 싶으면 ul에 if_full -->
			<div class="like_table">

				<ul>
					<li class="opt ">상품노출</li>
					<li class="value"><?=_InputRadio_totaladmin("p_view", array('N', "Y"), ($row['p_view']?$row['p_view']:"Y"), "", array('숨김', "노출") )?></li>
				</ul>

				<ul>
					<li class="opt ">추천상품</li>
					<li class="value"><?=_InputRadio_totaladmin("bestview", array('N', "Y"), ($row['bestview']?$row['bestview']:"N"), "", array('미적용', "적용") )?></li>
				</ul>

				<?php
					// --- 분류선택 & 선택한분류 & 테마선택 부분 ---
					// _code : 반드시 상품코드 있어야 함
					include_once("_product.inc_category_form.php");
				?>

				<ul>
					<li class="opt ">노출순위</li>
					<li class="value">
						<input type="text" name="pro_idx" class="input_design" placeholder="노출순위" style="width:50px;" value="<?=$row['pro_idx']?$row['pro_idx']: 999;?>"/><span class="txt_back">위</span>
						<?=_DescStr_mobile_totaladmin("설정한 순위는 상품리스트에서 기본적으로 보여지는 순서(추천순)를 나타냅니다. 숫자는 낮을 수록 상단에 위치합니다. (숫자1부터)")?>
					</li>
				</ul>

				<ul>
					<li class="opt ess">상품노출</li>
					<li class="value"><?=_InputRadio_totaladmin("sale_type", array('T', "A"), ($row['sale_type']?$row['sale_type']:"T"), " class='sale_type_value'", array('기간판매', "상시판매") )?></li>
				</ul>

				<ul class="sale_type"<?php echo ($row['sale_type'] == 'A'?' style="display:none"':''); ?>>
					<li class="opt ess">판매일</li>
					<li class="value">
						<div class="input_wrap"><span class="upper_txt">시작</span><input type="text" name="sale_date" ID="sale_date" value='<?=$row['sale_date']?>' class="input_design input_date" placeholder="날짜선택" readonly /></div>
						<input type="hidden" name="sale_dateh" ID="sale_dateh" value='<?=sprintf("%02d" , $row['sale_dateh'])?>' /><!-- 시 -->
						<input type="hidden" name="sale_datem" ID="sale_datem" value='<?=sprintf("%02d" , $row['sale_datem'])?>' /><!-- 분 -->

						<div class="input_wrap"><span class="upper_txt">종료</span><input type="text" name="sale_enddate" ID="sale_enddate" value='<?=$row['sale_enddate']?>' class="input_design input_date" placeholder="날짜선택" readonly /></div>
						<input type="hidden" name="sale_enddateh" ID="sale_enddateh" value='<?=($row['sale_enddateh'] ? sprintf("%02d" , $row['sale_enddateh']) : "23")?>' /><!-- 시 -->
						<input type="hidden" name="sale_enddatem" ID="sale_enddatem" value='<?=($row['sale_enddatem'] ? sprintf("%02d" , $row['sale_enddatem']) : "59")?>' /><!-- 분 -->

						<?=_DescStr_mobile_totaladmin("판매 시작일이 최근일수록 상품리스트에서 신규상품 정렬 시 상단에 위치합니다.")?>
						<?=_DescStr_mobile_totaladmin("판매 종료일이 최근일수록 상품리스트에서 마감임박 정렬 시 상단에 위치합니다.")?>

					</li>
				</ul>

				<ul>
					<li class="opt ess">상품코드</li>
					<li class="value">
						<input type="text" name="code" class="input_design" value="<?=$row['code'] ? $row['code'] : $code;?>" readonly/>
					</li>
				</ul>

				<ul>
					<li class="opt ess">상품명</li>
					<li class="value">
						<input type="text" name="name" class="input_design" placeholder="상품명을 입력하세요." value="<?=$row['name']?>" />
						<?=_DescStr_mobile_totaladmin("<B>특수문자 제외</B>")?>
					</li>
				</ul>

				<ul>
					<li class="opt ess">상품 공급업체</li>
					<li class="value">
						<div class="select">
							<span class="shape"></span>
							<?php
								// - 공급업체 ---
								$arr_customer = arr_company();
								echo _InputSelect( "customerCode" , array_keys($arr_customer) , $row['customerCode'] , " id='customerCode' " , array_values($arr_customer) , "-공급업체-");
							?>
						</div>
					</li>
				</ul>

				<ul>
					<li class="opt ess">담당MD</li>
					<li class="value">
						<div class="select">
							<span class="shape"></span>
							<?php
								// - MD정보 ---
								$arr_mdlist = arr_mdlist();
								echo _InputSelect( "md_name" , $arr_mdlist , ($row['md_name'] ? $row['md_name'] : $arr_mdlist[0]) , " id='md_name' " , "" , "-담당MD-");
							?>
						</div>
					</li>
				</ul>

				<ul >
					<li class='opt '>상품아이콘</li>
					<li class='value'>
						<?php
							$r2 = $product_icon;
							$arr_icon = array();
							$pi_uid_array = explode(",",$row['p_icon']);
							foreach($r2 as $k2 => $v2) {$arr_icon[$v2['pi_uid']] = "<img src='/upfiles/icon/".$v2['pi_img']."' title = '".$v2['pi_title']."'>";}
							echo _InputCheckbox_totaladmin( "_icon" , array_keys($arr_icon) , array_values($pi_uid_array) , "" , array_values($arr_icon) )
						?>
					</li>
				</ul>

				<ul>
					<li class="opt ess">정산형태</li>
					<li class="value"><?=_InputRadio_totaladmin("comSaleType", array('공급가', "수수료"), ($row['comSaleType']?$row['comSaleType']:"공급가"), " onclick='saleType(this.form)' ", array() )?></li>
				</ul>

				<ul ID="comSaleTypeTr1" style='display:<?=( ($row['comSaleType'] == "공급가" || !$row['comSaleType'] ) ?"":"none")?>'>
					<li class="opt ess">공급가격</li>
					<li class="value"><input type="text" name="purPrice" class="input_design " value="<?=$row['purPrice']?>" style="width:200px;" /><span class="txt_back">원</span></li>
				</ul>

				<ul ID="comSaleTypeTr2" style='display:<?=( ($row['comSaleType'] == "수수료" ) ?"":"none")?>'>
					<li class="opt ess">수수료</li>
					<li class="value"><input type="text" name="commission" class="input_design" placeholder="수수료" style="width:50px;" value="<?=$row['commission'] ? $row['commission'] : $row_setup['s_product_commission'];?>"/><span class="txt_back">%</span></li>
				</ul>

				<ul>
					<li class="opt ess">배송기능</li>
					<li class="value">
						<?php
							// -- 주문이 있을 경우 수정할 수 없음 ---
							$app_cnt = 0;
							if(sizeof($row) > 0 ) {
								$srow = _MQ(" select count(*) as cnt from odtOrderProduct where op_pcode='" . $row['code'] . "' ");
								$app_cnt = $srow['cnt'];
							}
							if( $app_cnt > 0 ) {
								if( $row['setup_delivery'] =="Y" ) {
									echo "<b>배송기능적용</b>";
								}
								else {
									echo "<b>쿠폰판매</b>";
								}
								echo "<br>(적용주문이 있어서 수정할 수 없습니다.)<br>";
								echo "<input type=\"hidden\" name=\"setup_delivery\" value=\"{$row['setup_delivery']}\">";
							}
							else {
								echo "<label><input type=\"checkbox\" name=\"setup_delivery\" value=\"Y\" onclick='setup_delivery_chk()' ";
								echo ($row['setup_delivery']=="Y")?"checked":"";
								echo "> 배송기능적용</label>";
							}
						?>
						<?=_DescStr_mobile_totaladmin("실물 상품을 판매하기 위해 배송기능을 적용하고자 할 경우 사용합니다.")?>
						<?=_DescStr_mobile_totaladmin("배송기능 적용 시 해당 상품의 주문정보는 쿠폰기능을 대체하여 택배송장번호와 배송정보로 변경됩니다.")?>
						<?=_DescStr_mobile_totaladmin("<b>주문이 있을 경우 수정할 수 없습니다.</b>")?>
					</li>
				</ul>

				<ul ID="setup_delivery_apply" style='display:<?=($row['setup_delivery']=="Y" ?"":"none")?>'>
					<li class="opt ess">배송정책</li>
					<li class="value">
						<?=_InputRadio_totaladmin("del_type", array("normal", "unit", "free"), ($row['del_type']?$row['del_type']:"normal"), " ", array("일반(입점업체 배송정책을 따름)" , "개별배송" , "무료배송") )?>
						<?=_DescStr_mobile_totaladmin("개별배송일 경우 상품구매갯수당 개별배송비용이 추가됩니다.")?>
						<?=_DescStr_mobile_totaladmin("무료배송 선택시 상품에 무료배송 아이콘이 노출됩니다.")?>
					</li>
				</ul>

				<ul ID="del_type_unit" style='display:<?=($row['del_type']=="unit" ?"":"none")?>'>
					<li class="opt ">개별배송비</li>
					<li class="value"><input type="text" name="del_price" class="input_design" <?echo ($row['setup_delivery']=="Y")?"":"disabled";?> value="<?=$row['del_price']?>" style="width:200px;" /><span class="txt_back">원</span></li>
				</ul>

				<?if(sizeof($row_customer) > 0 ):?>
				<ul ID="del_type_normal" style='display:<?=($row['del_type']=="normal" ?"":"none")?>'>
					<li class="opt ">업체설정<br>(참조용)</li>
					<li class="value">
						<?=_DescStr_mobile_totaladmin("업체 기본배송비 : " . ($row_customer['com_delprice'] ? number_format($row_customer['com_delprice']) . "원" : "-미정-") , "orange")?>
						<?=_DescStr_mobile_totaladmin("업체 무료배송비 : " . ($row_customer['com_delprice_free'] ? number_format($row_customer['com_delprice_free']) . "원" : "-미정-") , "orange")?>
						<?=_DescStr_mobile_totaladmin("업체 지정택배사 : " . ($row_customer['com_del_company'] ? number_format($row_customer['com_del_company']) . "원" : "-미정-") , "orange")?>
					</li>
				</ul>
				<?endif;?>

				<input type="hidden" name="del_limit" value="<?=isset($row['del_limit'])?$row['del_limit']:$row_setup['s_delprice_free']?>"><?// 무료배송가 사용하지 않음?>
				<script>
					// 배송형태 체크시 노출 - 개별배송비
					$(document).ready(function(){
						$("input[name='del_type']").on('click',function(){
							if($(this).filter(function() {if (this.checked) return this;}).val() == "normal") { $("#del_type_normal").show(); }
							else { $("#del_type_normal").hide(); }
							if($(this).filter(function() {if (this.checked) return this;}).val() == "unit") { $("#del_type_unit").show(); }
							else { $("#del_type_unit").hide(); }
						});
					});
				</script>

				<ul >
					<li class="opt ">정상가격</li>
					<li class="value"><input type="text" name="price_org" class="input_design" value="<?=$row['price_org']?>" style="width:200px;" /><span class="txt_back">원</span></li>
				</ul>

				<ul >
					<li class="opt ess">판매가격</li>
					<li class="value"><input type="text" name="price" class="input_design" value="<?=$row['price']?>" style="width:200px;" /><span class="txt_back">원</span></li>
				</ul>

				<ul >
					<li class="opt ess">할인율</li>
					<li class="value">
						<input type="text" name="price_per" class="input_design" value="<?=$row['price_per']?>" style="width:50px;" /><span class="txt_back">% &nbsp;</span>
						<span class="button_pack"><a href="#none" id="price_per_calc" class="btn_md_black">자동계산</a></span>
						<script>
							// 할인율 자동계산
							$(document).ready(function(){
								$('#price_per_calc').on('click',function(){
									var o = $('input[name=price_org]').val().replace(/,/g,'')*1, p = $('input[name=price]').val().replace(/,/g,'')*1;
									if(o==0) { var o2 = 1; } else { o2 = o; }
									var result = (o-p)/o2*100; if(result < 0) { result = 0; }
									$('input[name=price_per]').val(parseInt(result));
								});
							});
						</script>
					</li>
				</ul>

				<ul >
					<li class="opt ">상품쿠폰<br>(쿠폰명)</li>
					<li class="value"><input type="text" name="coupon_title" class="input_design" value="<?=$row['coupon_title']?>" /></li>
				</ul>

				<ul >
					<li class="opt ">상품쿠폰<br>(할인율)</li>
					<li class="value"><input type="text" name="coupon_price" class="input_design" value="<?=$row['coupon_price']?>" style="width:50px;" /><span class="txt_back">%</span></li>
				</ul>

				<ul>
					<li class="opt ">옵션타이틀</li>
					<li class="value">
						<input type="text" name="option1_title" class="input_design" placeholder="1차 옵션 타이틀" value="<?=$row['option1_title']?>" />
						<input type="text" name="option2_title" class="input_design" placeholder="2차 옵션 타이틀" value="<?=$row['option2_title']?>" />
						<input type="text" name="option3_title" class="input_design" placeholder="3차 옵션 타이틀" value="<?=$row['option3_title']?>" />
					</li>
				</ul>

				<ul>
					<li class="opt ">옵션설정</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt ">추가설정</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt ">정보제공고시</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt ">중복구매</li>
					<li class="value">
						<label><input type="checkbox" name="ipDistinct" value="1" <?=$row['ipDistinct'] ? "checked" : NULL;?> />중복구매불가</label>
						<input type="hidden" name="guestDisabled" value="1">
					</li>
				</ul>

				<ul >
					<li class="opt ">적립금</li>
					<li class="value">
						<input type="text" name="point" class="input_design" value="<?=isset($row['point']) ? $row['point'] : "0";?>" style="width:50px;" /><span class="txt_back">%</span>
					</li>
				</ul>

				<ul >
					<li class="opt ">재고량</li>
					<li class="value"><input type="text" name="stock" class="input_design" value="<?=isset($row['stock']) ? $row['stock'] : "10000";?>" style="width:200px;" /><span class="txt_back">개</span></li>
				</ul>

				<ul >
					<li class="opt ">1회 최대 구매량</li>
					<li class="value"><input type="text" name="buy_limit" class="input_design" value="<?=isset($row['buy_limit']) ? $row['buy_limit'] : "5";?>" style="width:200px;" /><span class="txt_back">개</span></li>
				</ul>

				<ul >
					<li class="opt ">현판매량</li>
					<li class="value"><input type="text" name="saleCnt" class="input_design" value="<?=isset($row['saleCnt']) ? $row['saleCnt'] : "0";?>" style="width:200px;" /><span class="txt_back">개</span></li>
				</ul>

				<ul>
					<li class="opt ">관련상품 지정</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul class="if_full">
					<li class="opt ess">간략 상세정보</li>
					<li class="value"><textarea cols="" rows="" class="textarea_design" name="short_comment"><?=stripslashes($row['short_comment'])?></textarea></li>
				</ul>

				<ul>
					<li class="opt ">상품 사용 정보</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt ">업체 이용 정보</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt ">상품 상세설명</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt ">상품 상세설명<br>(모바일)</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt ">주문확인서 주의사항</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt ess">지도 및 주소</li>
					<li class="value">
						<label><input type="checkbox" name="company_addr_insert_check" value="Y" onclick="company_addr_insert()" />공급업체 주소 입력</label>
						<input type="text" name="com_juso" id="com_juso" value="<?=$row[com_juso]?>" class="input_design" placeholder="주소를 입력하시기 바랍니다." />
						<input type="text" name="com_mapx" id="com_mapx" value="<?=$row[com_mapx]?>" class="input_design" placeholder="X좌표값이 입력됩니다." />
						<input type="text" name="com_mapy" id="com_mapy" value="<?=$row[com_mapy]?>" class="input_design" placeholder="Y좌표값이 입력됩니다." />
						<?=_DescStr_mobile_totaladmin("주소를 등록하시고 저장을 하시면 지도를 확인할 수 있습니다 (사용자페이지 업체정보 아래 표시됨).")?>
						<?=_DescStr_mobile_totaladmin("주소 등록시 좌표는 자동으로 등록되며, 지도위치를 변경하시려면 X, Y 좌표를 삭제하신 후 주소 변경 후 수정하시면 됩니다.")?>
						<?=_DescStr_mobile_totaladmin("주소 등록시 주변 경관을 설명하는 문구(OO주유소 근처, 교차로 부근 등)를 입력할 경우 좌표 검색이 되지 않을 수 있으니 주의하시기 바랍니다.")?>
						<?=_DescStr_mobile_totaladmin("좌표를 입력하면 지도가 표시됩니다. 세밀한 조정을 원하시면 빨간 마커를 드래그하여 원하는 위치에 놓으시면 됩니다.")?>
					</li>
				</ul>

				<ul>
					<li class="opt ess">쿠폰사용<br>만료일</li>
					<li class="value"><input id="id_expire" type="text" name="expire" class="input_design input_date" placeholder="" readonly value="<?=(($row['expire'] && $row['expire']!="0000-00-00") ? $row['expire'] : date("Y-m-d" , strtotime("+30 day")))?>" /></li>
				</ul>

				<ul>
					<li class="opt ">메인</li>
					<li class="value">
						<div class="photo_preview"><div class="photo_inner"><?=($row['main_img'] ? "<img src='". replace_image(IMG_DIR_PRODUCT.$row['main_img']) . "'>" : "")?></div></div>
						<?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?>
					</li>
				</ul>

				<ul>
					<li class="opt ">정사각형<br>목록</li>
					<li class="value">
						<div class="photo_preview"><div class="photo_inner"><?=($row['prolist_img'] ? "<img src='". replace_image(IMG_DIR_PRODUCT.$row['prolist_img']) . "'>" : "")?></div></div>
						<?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?>
					</li>
				</ul>

				<ul>
					<li class="opt ">직사각형<br>목록</li>
					<li class="value">
						<div class="photo_preview"><div class="photo_inner"><?=($row['prolist_img2'] ? "<img src='". replace_image(IMG_DIR_PRODUCT.$row['prolist_img2']) . "'>" : "")?></div></div>
						<?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?>
					</li>
				</ul>


			</div>

		</div>
		<!-- / 데이터폼 -->
	</div>
	<!-- / 내용들어가는 공간 -->



	<!-- ●●●●●●●●●● 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><input type="submit" class="btn_lg_red" value="수정"></span></li>	
			<li><span class="button_pack"><a href="_product.list.php?<?=enc('d' , $_PVSC)?>" class="btn_lg_white">목록으로</a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->
</form>



<?php

	include dirname(__FILE__)."/wrap.footer.php";

?>



<script language="JavaScript" src="./js/_product.js"></script>
<link rel="stylesheet" href="/include/js/jquery/jqueryui/jquery-ui.min.css" type="text/css">
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
	$(document).ready(function(){

		// -  validate --- 
		$("form[name=frm]").validate({
			ignore: "input[type=text]:hidden",
			rules: {
				sale_date: {required: function() {return ($('.sale_type_value:checked').val() == 'T'?true:false);}},//판매시작일
				sale_dateh: { required: function() {return ($('.sale_type_value:checked').val() == 'T'?true:false);}},//판매시작일
				sale_datem: { required: function() {return ($('.sale_type_value:checked').val() == 'T'?true:false);}},//판매시작일
				sale_enddate: {required: function() {return ($('.sale_type_value:checked').val() == 'T'?true:false);}},//판매종료일
				sale_enddateh: { required: function() {return ($('.sale_type_value:checked').val() == 'T'?true:false);}},//판매종료일
				sale_enddatem: { required: function() {return ($('.sale_type_value:checked').val() == 'T'?true:false);}},//판매종료일
				code: { required: true},//상품코드
				name: { required: true},//상품명
				customerCode: { required: true},//상품 공급업체
				md_name: { required: true},//담당MD
				purPrice:{required: function() { return ($("input[name=comSaleType]").filter(function() {if (this.checked) return this;}).val() == "공급가") ? true : false } },//업체정산형태 - 공급가 -- 
				commission:{required: function() { return ($("input[name=comSaleType]").filter(function() {if (this.checked) return this;}).val() == "수수료") ? true : false } },//업체정산형태 - 수수료
				price: { required: true},//판매가격
				price_per: { required: true},//할인율
				expire: { required: true}//쿠폰사용만료일
			},
			messages: {
				sale_date: { required: "판매시작일을 입력하시기 바랍니다."},//판매시작일
				sale_dateh: { required: "판매시작시간을 입력하시기 바랍니다."},//판매시작일
				sale_datem: { required: "판매시작분을 입력하시기 바랍니다."},//판매시작일
				sale_enddate: { required: "판매종료일을 입력하시기 바랍니다."},//판매종료일
				sale_enddateh: { required: "판매종료시간을 입력하시기 바랍니다."},//판매종료일
				sale_enddatem: { required: "판매종료분을 입력하시기 바랍니다."},//판매종료일
				code: { required: "상품코드를 입력하시기 바랍니다."},//상품코드
				name: { required: "상품명을 입력하시기 바랍니다."},//상품명
				customerCode: { required: "상품 공급업체를 선택하시기 바랍니다."},//상품 공급업체
				md_name: { required: "담당MD를 선택하시기 바랍니다."},//담당MD
				purPrice:{required: "업체정산형태 - 공급가를 입력하시기 바랍니다." },//업체정산형태 - 공급가
				commission:{required: "업체정산형태 - 수수료를 입력하시기 바랍니다." },//업체정산형태 - 수수료
				price: { required: "판매가격을 입력하시기 바랍니다."},//판매가격
				price_per: { required: "할인율을 입력하시기 바랍니다."},//할인율
				expire: { required: "쿠폰사용만료일을 입력하시기 바랍니다."}//쿠폰사용만료일
			},
			submitHandler : function(form) {
				form.submit();
			}
		});
		// - validate --- 

		// 상품노출 체크
		$('.sale_type_value').on('click', function() {
			var checked = $('.sale_type_value:checked').val();
			if(checked == 'A') $('.sale_type').hide();
			else $('.sale_type').show();
		});

		// - 테마 체크박스 선택시 이벤트
		// -- 동일 테마가 있다면 동일하게 체크 적용
		$(".cls_thema input[type='checkbox']").click(function(){
			if (this.checked) { $("input[data-realthema='"+this.value+"']").attr("checked" , true); } 
			else { $("input[data-realthema='"+this.value+"']").attr("checked" , false); }
		});
	});

	$(function() {
		$("#sale_date").datepicker({changeMonth: true, changeYear: true });
		$("#sale_date").datepicker( "option", "dateFormat", "yy-mm-dd" );
		$("#sale_date").datepicker( "option",$.datepicker.regional["ko"] );

		$("#sale_enddate").datepicker({changeMonth: true, changeYear: true });
		$("#sale_enddate").datepicker( "option", "dateFormat", "yy-mm-dd" );
		$("#sale_enddate").datepicker( "option",$.datepicker.regional["ko"] );

		$("#id_expire").datepicker({changeMonth: true, changeYear: true });
		$("#id_expire").datepicker( "option", "dateFormat", "yy-mm-dd" );
		$("#id_expire").datepicker( "option",$.datepicker.regional["ko"] );
	});

	function company_addr_insert() { // LMH003
		var is_check = $("input[name=company_addr_insert_check]:checked").val();
		var id = $("#customerCode").val();
		if(is_check == "Y") {
			if(id == undefined || !id) {
				alert('공급업체를 선택하세요');
				return;
			}
			$.ajax({
				url: "/totalAdmin/_product.get_company_addr.php",
				cache: false,
				type: "POST",
				data: "id=" + id ,
				dataType: 'JSON',
				success: function(data){
					console.log(data);
					$("#com_juso").val(data['juso']);
					$("#com_mapx").val(data['mapx']);
					$("#com_mapy").val(data['mapy']);
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
	}
</script>