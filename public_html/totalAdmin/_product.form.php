<?PHP
// 페이지 표시
$app_current_link = "/totalAdmin/_product.list.php";
include_once("inc.header.php");


if(!$code) {

    ## 코드 생성.
    $code = shop_pcode_create();
}
else {

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

	<!-- 검색영역 -->
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="120px"/>
				<col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">상품노출</td>
					<td class="conts">
						<?=_InputRadio("p_view", array('N', "Y"), ($row['p_view']?$row['p_view']:"Y"), "", array('숨김', "노출") )?>
					</td>
				</tr>
				<tr>
					<td class="article">추천상품</td>
					<td class="conts">
						<?=_InputRadio("bestview", array('N', "Y"), ($row['bestview']?$row['bestview']:"N"), "", array('미적용', "적용") )?>
					</td>
				</tr>
				<tr>
					<td class="article">상품분류<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<?PHP
						/*// - 상품 카테고리 분류
						if($row[c_parent]){
							$ex = explode("," , $row[c_parent]);
							$app_depth1 = $ex[0];
							$app_depth2 = $ex[1];
						}
						//	$select_mode = "readonly";
						include_once("../../include/category.inc.php");*/
						// _code : 반드시 상품코드 있어야 함
						include_once("_product.inc_category_form.php");
						?>
						<?=_DescStr("선택하신 2차분류에 의해 테마를 선택할 수 있으며, 동일한 테마가 다른 2차분류에 있을 경우 중복 적용됩니다.")?>
					</td>
				</tr>

				<?PHP
				// - 카테고리별 테마 ---
				$ex_thema = explode("," , $row[thema]);


				// - 선택 2차 카테고리 추출 ---
				$arr_depth2 = array();
				$que = "
					select ct2.catecode as ct2_catecode
					from odtProductCategory as pct
					left join odtCategory as ct3 on (ct3.catecode = pct.pct_cuid and ct3.catedepth=3)
					left join odtCategory as ct2 on (substring_index(ct3.parent_catecode , ',' ,-1) = ct2.catecode and ct2.catedepth=2)
					where
						pct.pct_pcode='". $code ."'
				";
				$res = _MQ_assoc($que);
				foreach( $res as $k=>$v ){
					$arr_depth2[$v['ct2_catecode']]++;
				}
				// - 선택 2차 카테고리 추출 ---


				// - 2차 테마 추출 ---
				$arr_cate2 = array();
				$res = _MQ_assoc("select catecode,lineup , catename from odtCategory where cHidden='no' and catedepth='2' order by cateidx asc");
				foreach( $res as $k=>$v ){
					$arr_cate2[$v['catecode']] = array("lineup"=>$v['lineup'] , "catename"=>$v[catename]);
				}
				// - 2차 테마 추출 ---


				if( sizeof($arr_cate2) > 0 ){
					foreach($arr_cate2 as $k=>$v){
						if( $v['lineup'] ) {
							echo "
								<tr class='cls_thema cls_category_uid_". $k ."' ". (in_array($k , array_keys($arr_depth2)) ? "" : " style='display:none;' ") .">
									<td class='article'><U>". $v['catename'] ."</U><br>상품테마선택</td>
									<td class='conts'>
							";
							$ex_cate_thema = explode("," , $v['lineup']);
							foreach($ex_cate_thema as $sk=>$sv){
								echo "<label><input type='checkbox' name='_thema[]' value='". $sv ."' ". ( in_array($sv , $ex_thema) ? "checked" : "" ) . " data-realthema='". $sv ."' > ". $sv ."</label> &nbsp; &nbsp; ";
							}
							echo "
									</td>
								</tr>
							";
						}
					}
				}
				// - 카테고리별 테마 ---
				?>
				<tr>
					<td class="article">노출순위</td>
					<td class="conts"><input type="text" class="input_text" name="pro_idx" size="5" style="text-align:right" value="<?=isset($row['pro_idx'])?$row['pro_idx']: 999;?>">위<?=_DescStr("설정한 순위는 상품리스트에서 기본적으로 보여지는 순서(추천순)를 나타냅니다. 숫자는 낮을 수록 상단에 위치합니다. (숫자1부터)")?></td>
				</tr>
				<?php// # LDD016 ?>
				<tr>
					<td class="article">판매설정</td>
					<td class="conts">
						<?=_InputRadio("sale_type", array('T', "A"), ($row['sale_type']?$row['sale_type']:"T"), " class='sale_type_value'", array('기간판매', "상시판매") )?>
					</td>
				</tr>
				<tr class="sale_type"<?php echo ($row['sale_type'] == 'A'?' style="display:none"':''); ?>>
					<td class="article">판매일<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						판매시작일 :
						<input type="text" name="sale_date" ID="sale_date" value='<?=$row['sale_date']?>' class="input_text" style="width:80px;cursor:pointer;" readonly /><!-- 일 -->
						<input type="hidden" name="sale_dateh" ID="sale_dateh" value='<?=sprintf("%02d" , $row['sale_dateh'])?>' class="input_text" style="width:20px;"  /><!-- 시 -->
						<input type="hidden" name="sale_datem" ID="sale_datem" value='<?=sprintf("%02d" , $row['sale_datem'])?>' class="input_text" style="width:20px;"  /><!-- 분 -->
						~
						판매종료일 :
						<input type="text" name="sale_enddate" ID="sale_enddate" value='<?=$row['sale_enddate']?>' class="input_text" style="width:80px;cursor:pointer;" readonly /><!-- 일 -->
						<input type="hidden" name="sale_enddateh" ID="sale_enddateh" value='<?=($row['sale_enddateh'] ? sprintf("%02d" , $row['sale_enddateh']) : "23")?>' class="input_text" style="width:20px;"  /><!-- 시 -->
						<input type="hidden" name="sale_enddatem" ID="sale_enddatem" value='<?=($row['sale_enddatem'] ? sprintf("%02d" , $row['sale_enddatem']) : "59")?>' class="input_text" style="width:20px;"  /><!-- 분 -->
						<?=_DescStr("판매 시작일이 최근일수록 상품리스트에서 신규상품 정렬 시 상단에 위치합니다.")?>
						<?=_DescStr("판매 종료일이 최근일수록 상품리스트에서 마감임박 정렬 시 상단에 위치합니다.")?>
					</td>
				</tr>
				<?php// # LDD016 ?>
                <tr>
					<td class="article">상품코드<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<input type="text" name="code" class="proName" size="40" value="<?=$row['code'] ? $row['code'] : $code;?>" readonly>
						<!-- LDD004 { -->
						<?php if($_mode == 'modify') { ?>
						<span class='shop_btn_pack' style="float:none;"><input type="button" class="input_small white" style="cursor: pointer; height:30px;" onclick="window.open('/?pn=product.view&pcode=<?php echo $code; ?>', '_blank');" value="상품 바로가기"/></span>
						<?php } ?>
						<!-- } LDD004 -->
                    </td>
				</tr>
                <tr>
					<td class="article">상품명<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<input type="text" name="name" class="proName" size="40" value="<?=$row['name']?>">
						<?=_DescStr("<B>특수문자 제외</B>")?>
                    </td>
				</tr>
                <tr>
					<td class="article">상품 공급업체<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<?PHP
						// - 공급업체 ---
						$arr_customer = arr_company();
						echo _InputSelect( "customerCode" , array_keys($arr_customer) , $row['customerCode'] , " id='customerCode' " , array_values($arr_customer) , "-공급업체-");
						?>
						<!-- LDD001 { -->
						<span class='shop_btn_pack' style="float:none;"><input type="button" id="customer_link" class="input_small gray" style="cursor: pointer;" value="공급업체 바로가기"/></span>
						<script>
						$(function() {
							$('#customer_link').on('click', function() {

								var customerCode = $('#customerCode option:selected').val();
								if(customerCode == '') return alert('공급업체가 선택되지 않았습니다.');
								window.open('./_entershop.form.php?_mode=modify&serialnum=&customerCode='+customerCode+'&_PVSC=', '_blank');
							});
						});
						</script>
						<!-- } LDD001 -->
                    </td>
				</tr>

                <tr>
					<td class="article">담당MD<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<?PHP
						// - MD정보 ---
						$arr_mdlist = arr_mdlist();
						echo _InputSelect( "md_name" , $arr_mdlist , ($row['md_name'] ? $row['md_name'] : $arr_mdlist[0]) , " id='md_name' " , "" , "-담당MD-");
						?>
                    </td>
				</tr>

				<tr>
					<td class="article">상품아이콘</td>
					<td class="conts">
						<?php
						$r2 = $product_icon;
						$pi_uid_array = explode(",",$row['p_icon']);
						foreach($r2 as $k2 => $v2) {
							$checked = @array_search($v2['pi_uid'],$pi_uid_array) === false ? NULL : " checked ";
							echo "<label><input type='checkbox' name='_icon[]' value='".$v2['pi_uid']."' ".$checked."><img src='../upfiles/icon/".$v2['pi_img']."' title = '".$v2['pi_title']."'></label>&nbsp;&nbsp;&nbsp;";
						}
						?>
					</td>
				</tr>

				<tr>
					<td class="article">업체정산형태<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<label><input type="radio" name="comSaleType" value="공급가" onclick='saleType(this.form)' <?=$row['comSaleType'] == "공급가" || !$row['comSaleType'] ? "checked" : NULL;?>> 공급가</label>
						&nbsp;&nbsp;
						<label><input type="radio" name="comSaleType" value="수수료" onclick='saleType(this.form)' <?=$row['comSaleType'] == "수수료" ? "checked" : NULL;?>>수수료</label>
						<!-- 공급가 선택 시 노출 -->
						<div ID="comSaleTypeTr1" style='display:<?=( ($row['comSaleType'] == "공급가" || !$row['comSaleType'] ) ?"":"none")?>'>
							매입가격 (공급가격) : <input type="text" name="purPrice" class="input_text number_style" size="10" style='text-align:right;' value="<?=$row['purPrice']?>"> 원
						</div>
						<!-- 수수료 선택 시 노출 -->
						<div ID="comSaleTypeTr2" style='display:<?=( ($row['comSaleType'] == "수수료" ) ?"":"none")?>'>
							수수료 : <input type="text" name="commission" class="input_text number_style" size="3" style='text-align:right;' value="<?=$row['commission'] ? $row['commission'] : $row_setup['s_product_commission'];?>"> %
						</div>
                    </td>
				</tr>

				<tr>
					<td class="article">배송기능</td>
					<td class="conts">
						<?PHP
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
					        echo "&nbsp;&nbsp;(적용주문이 있어서 수정할 수 없습니다.)<br>";
							echo "<input type=\"hidden\" name=\"setup_delivery\" value=\"{$row['setup_delivery']}\">";
					    }
					    else {
					        echo "<label><input type=\"checkbox\" name=\"setup_delivery\" value=\"Y\" onclick='setup_delivery_chk()' ";
					        echo ($row['setup_delivery']=="Y")?"checked":"";
					        echo "> 배송기능적용</label><br>";
					    }
						?>
						<?=_DescStr("실물 상품을 판매하기 위해 배송기능을 적용하고자 할 경우 사용합니다.")?>
						<?=_DescStr("배송기능 적용 시 해당 상품의 주문정보는 쿠폰기능을 대체하여 택배송장번호와 배송정보로 변경됩니다.")?>
						<?=_DescStr("<b>주문이 있을 경우 수정할 수 없습니다.</b>")?>

						<div ID="setup_delivery_apply" style='display:<?=($row['setup_delivery']=="Y" ?"":"none")?>'>
							<br>

							<label><input type="radio" name="del_type" value="normal" <?=$row['del_type'] == "normal" || !$row['del_type'] ? "checked" : NULL;?>>일반(입점업체 배송정책을 따름)</label>
							&nbsp;&nbsp;&nbsp;
							<label><input type="radio" name="del_type" value="unit" <?=$row['del_type'] == "unit" ? "checked" : NULL;?>>개별배송</label>
							&nbsp;&nbsp;&nbsp;
							<label><input type="radio" name="del_type" value="free" <?=$row['del_type'] == "free" ? "checked" : NULL;?>>무료배송</label>
							<?=_DescStr("개별배송일 경우 상품구매개수당 개별배송비용이 추가됩니다.")?>
							<?=_DescStr("무료배송 선택시 상품에 무료배송 아이콘이 노출됩니다.")?>


							<div ID="del_type_unit" style='display:<?=($row['del_type']=="unit" ?"":"none")?>'>
								<br>&nbsp;개별배송비 : &nbsp;<input type="text" class="input_text number_style" name="del_price" size=10 style="text-align:right" <?echo ($row['setup_delivery']=="Y")?"":"disabled";?> value="<?=$row['del_price']?>">원
							</div>


							<div ID="del_type_normal" style='display:<?=($row['del_type']=="normal" ?"":"none")?>'>
								<?if(sizeof($row_customer) > 0 ):?>
								<br>&nbsp;업체 기본배송비 : <U><?=($row_customer['com_delprice'] ? number_format($row_customer['com_delprice']) . "원" : "0원")?></U>
								<br>&nbsp;업체 무료배송비 : <U><?=($row_customer['com_delprice_free'] ? number_format($row_customer['com_delprice_free']) . "원" : "무조건 배송비 부과")?></U>
								<br>&nbsp;업체 지정택배사 : <U><?=($row_customer['com_del_company'] ? $row_customer['com_del_company'] : "-미정-")?></U>
								<?endif;?>
							</div>


							<input type="hidden" name="del_limit" value="<?=isset($row['del_limit'])?$row['del_limit']:$row_setup['s_delprice_free']?>"><?// 무료배송가 사용하지 않음?>

						</div>
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
					</td>
				</tr>
                <tr>
					<td class="article">정상가격</td>
					<td class="conts">
						<input type="text" name="price_org" class="input_text number_style" size="10" style="text-align:right;" value="<?=$row['price_org']?>"> 원
                    </td>
				</tr>
                <tr>
					<td class="article">판매가격<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<input type="text" name="price" class="input_text number_style" size="10" style="text-align:right;" value="<?=$row['price']?>"> 원
                    </td>
				</tr>
                <tr>
					<td class="article">할인율<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<input type="text" name="price_per" class="input_text number_style" size="3" style='text-align:right;' value="<?=$row['price_per']?>"> %
						<span class='shop_btn_pack' style="float:none;"><input type="button" id="price_per_calc" class="input_small gray" style="cursor: pointer;" value="자동계산"/></span>
						<script>
						// 할인율 자동계산
						$(document).ready(function(){
							$('#price_per_calc').on('click',function(){
								var o = $('input[name=price_org]').val().replace(/,/g,'')*1, p = $('input[name=price]').val().replace(/,/g,'')*1;
								if(o==0) { var o2 = 1; } else { o2 = o; }
								var result = Math.round((o-p)*100/o2) ; if(result < 0) { result = 0; }
								$('input[name=price_per]').val(parseInt(result));
							});
						});
						</script>
                    </td>
				</tr>
                <tr>
					<td class="article">상품쿠폰</td>
					<td class="conts">
						쿠폰명 <input type="text" name="coupon_title" class="input_text" size="20" value="<?=$row['coupon_title']?>"> /
						할인율 <input type="text" name="coupon_price" class="input_text number_style" size="3" style='text-align:right;' value="<?=$row['coupon_price']?>"> %
                    </td>
				</tr>
                <tr>
					<td class="article">옵션타이틀</td>
					<td class="conts">
						1차 옵션 타이틀 : <input type="text" name="option1_title" class="input_text" size="30"  value="<?=$row['option1_title']?>"><br>
						2차 옵션 타이틀 : <input type="text" name="option2_title" class="input_text" size="30"  value="<?=$row['option2_title']?>"><br>
						3차 옵션 타이틀 : <input type="text" name="option3_title" class="input_text" size="30"  value="<?=$row['option3_title']?>">
                    </td>
				</tr>
				<tr>
					<td class="article">옵션설정</td>
					<td class="conts">
					<?PHP
					echo _InputRadio( "option_type_chk" , array('nooption','1depth','2depth','3depth'), ($row['option_type_chk'] ? $row['option_type_chk'] : "nooption") , " class='option_type_chk' " , array('옵션사용안함','1차옵션','2차옵션','3차옵션'));
					echo _DescStr("<B>옵션설정은 상품 최초 등록 시에만 설정 하시고 이후에는 수정을 가급적 피하시는게 좋습니다</B>.","orange");
					?>
					</td>
				</tr>
				<tr>
					<td class="article">상품옵션</td>
					<td class="conts">
						<span class="shop_btn_pack" style='margin-right:10px'><a href="#none" onclick="javascript:option_popup('<?=$code?>');" class="small blue">옵션창 열기</a></span>
						<?=_DescStr("주문 내역이 있는 상품의 옵션은 변경하지 마시기 바랍니다.","orange");?>
					</td>
				</tr>

				<!-- 추가옵션 패치 2014-03-24 -->
				<tr>
					<td class="article">추가옵션</td>
					<td class="conts">
						<span class="shop_btn_pack" style='margin-right:10px'><a href="#none" onclick="javascript:addoption_popup('<?=$code?>');" class="small blue">추가옵션창 열기</a></span>
						<?=_DescStr("주문 내역이 있는 추가옵션은 변경하지 마시기 바랍니다.","orange");?>
						<?=_DescStr("추가옵션은 배송상품에만 적용됩니다.","orange");?>
					</td>
				</tr>
				<!-- 추가옵션 패치 끝 -->
				<tr>
					<td class="article">정보제공고시</td>
					<td class="conts">
						<span class="shop_btn_pack" style='margin-right:10px'><a href="javascript:reqinfo_popup();" class="small blue">정보제공고시 관리창 열기</a></span>
						<?=_DescStr("상품에 필요한 정보제공고시 항목: 내용으로 등록하며, 등록된 내용은 상품 상세페이지에 노출됩니다.","orange");?>
					</td>
				</tr>
                <tr>
					<td class="article">중복구매</td>
					<td class="conts">
						<label><input type="hidden" name="guestDisabled" value="1"><input type="checkbox" name="ipDistinct" value="1" <?=$row['ipDistinct'] ? "checked" : NULL;?>> 중복구매불가</label>
					</td>
				</tr>
                <tr>
					<td class="article">적립금</td>
					<td class="conts"><input type="text" name="point" class="input_text" size="3" style='text-align:right;' value="<?=isset($row['point']) ? $row['point'] : "0";?>"> %</td>
				</tr>
                <tr>
					<td class="article">재고량</td>
					<td class="conts"><input type="text" name="stock" class="input_text number_style" value="<?=isset($row['stock']) ? $row['stock'] : "10000";?>" size="6" style="text-align:right;" > 개</td>
				</tr>
                <tr>
					<td class="article">1회 최대 구매량</td>
					<td class="conts"><input type="text" name="buy_limit" class="input_text number_style" value='<?=isset($row['buy_limit']) ? $row['buy_limit'] : "5";?>' size="3">개</td>
				</tr>
                <tr>
					<td class="article">현 판매량</td>
					<td class="conts"><input type="text" name="saleCnt" class="input_text number_style" value="<?=isset($row['saleCnt']) ? $row['saleCnt'] : "0";?>" size="5" style="text-align:right;" > 개</td>
				</tr>
                <tr>
					<td class="article">관련상품 지정</td>
					<td class="conts">
						<?php // LDD012 { ?>
						<div style="margin-bottom: 10px;">
							<?=_InputRadio("relation_auto" , array('N','Y'), ($row['relation_auto'] ? $row['relation_auto'] : "N") , " class='relation_auto_mode' " , array('수동지정','자동지정')); ?>
						</div>
						<div class="relation_use"<?php echo ($row['relation_auto'] == 'Y'?'style="display:none;"':null); ?>>
							<div class='btn_line_up_center' style="margin-bottom: 5px;">
								<span class='shop_btn_pack'><input type=button value='관련상품정보삭제' class='input_small gray'  onclick="delField(document.frm.p_relation);"></span>
								<span class='shop_btn_pack'><span class='blank_3'></span></span>
								<span class='shop_btn_pack'><input type=button value='관련상품등록/수정' class='input_small blue' onclick="relationWin('_product.relation.php', '<?=$code?>');"></span>
							</div>
							<textarea name="p_relation" class="input_text" style="width:100%;height:50px;" readonly onclick="relationHelp();"><?=(stripslashes($row['p_relation']))?></textarea>
							<?=_DescStr("* 입력예: 상품코드1/상품코드2/상품코드3 (상품코드의 구분을 / 로 하시기 바랍니다.)")?>
						</div>
						<?=_DescStr("* <b>수동지정:</b> 관리자가 지정한 상품을 출력 합니다.")?>
						<?=_DescStr("* <b>자동지정:</b> 동일 카테고리의 상품 10개를 랜덤하게 출력 합니다.")?>
						<?php // } LDD012 ?>
                    </td>
				</tr>
                <tr>
					<td class="article">간략 상세정보</td>
					<td class="conts">
						<textarea name="short_comment" class="input_text" style="width:100%;height:60px;" ><?=stripslashes($row['short_comment'])?></textarea>
                    </td>
				</tr>
                <tr>
					<td class="article">상품 사용 정보</td>
					<td class="conts">
						<textarea name="comment_proinfo" class="input_text" style="width:100%;height:150px;" geditor><?=stripslashes($row['comment_proinfo'])?></textarea>
                    </td>
				</tr>
                <tr>
					<td class="article">업체 이용 정보</td>
					<td class="conts">
						<textarea name="comment_useinfo" class="input_text" style="width:100%;height:150px;" geditor><?=stripslashes($row['comment_useinfo'])?></textarea>
                    </td>
				</tr>
                <tr>
					<td class="article">상품 상세설명<span class="ic_ess" title="필수"></span><br><?=_DescStr("크기 : " . ($arr_product_size["상품상세"][0] > 0 ? $arr_product_size["상품상세"][0] : "-") . " x " . ($arr_product_size["상품상세"][1] > 0 ? $arr_product_size["상품상세"][1] : "-"))?></td>
					<td class="conts">
						<textarea name="comment2" class="input_text" style="width:100%; height:400px;" geditor><?=stripslashes($row['comment2'])?></textarea>
                    </td>
				</tr>
				<?php // LDD005 { ?>
                <tr>
					<td class="article">상품 상세설명<br>(모바일)</td>
					<td class="conts">
						<textarea name="comment2_m" class="input_text" style="width:100%; height:400px;" geditor><?=stripslashes($row['comment2_m'])?></textarea>
                    </td>
				</tr>
				<?php // } LDD005 ?>
                <tr>
					<td class="article">주문확인서 주의사항<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<textarea name="comment3" class="input_text" style="width:100%; height:150px;" geditor><?=stripslashes($row['comment3'] ? $row['comment3'] : $row_setup['s_product_notice'] )?></textarea>
						<?=_DescStr("5줄 이내로 입력하시기 바랍니다.")?>
						<?=_DescStr("쿠폰에 들어갈 주의사항입니다.")?>
                    </td>
				</tr>
               <tr><!-- LMH003 -->
               	<td class="article">지도 및 주소<span class="ic_ess" title="필수"></span></td>
               	<td class="conts">
               	<?
				if($row[com_mapx] && $row[com_mapy]) {
					$coordinate = $row[com_mapx].", ".$row[com_mapy];
					$google_key_multi = explode("§" , $row_setup[s_google_key]);
				?>
               	<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true&key=<?php echo $google_key_multi[0]; ?>"></script>
               	<script type="text/javascript">
               		function initialize() {
               			var latlng = new google.maps.LatLng(<?=$coordinate?>);
               			var myOptions = {
               				zoom: 18, center: latlng, disableDefaultUI: false, scrollwheel: false,
               				mapTypeId: google.maps.MapTypeId.ROADMAP
               			};
               			var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
               			var marker_0 = new google.maps.Marker({
               				draggable: true, position: new google.maps.LatLng(<?=$coordinate?>), map : map
               			});
               			google.maps.event.addListener(marker_0, 'dragend', function (event) {
               				document.getElementById("com_mapx").value = this.getPosition().lat();
               				document.getElementById("com_mapy").value = this.getPosition().lng();
               			});
               		}
               		$(document).ready(function(){ initialize(); });
               	</script>
               	<div id="map_canvas" style="width:600px;height:338px;margin-bottom:5px;"></div>
               	<? } ?>
               		<label><input type="checkbox" name="company_addr_insert_check" value="Y" onclick="company_addr_insert()"> 공급업체 주소 입력</label><br>
               		주소 : <input type="text" name="com_juso" id="com_juso" value="<?=$row[com_juso]?>" size="85" class="input_text"/>
               		<div style="margin: 5px 0;">
               		X좌표 : <input type="text" name="com_mapx" id="com_mapx" class="input_text" size="25" value="<?=$row[com_mapx]?>"/>&nbsp;&nbsp;&nbsp;
               		Y좌표 : <input type="text" name="com_mapy" id="com_mapy" class="input_text" size="25" value="<?=$row[com_mapy]?>"/>
               		</div>
               		<?=_DescStr("주소를 등록하시고 저장을 하시면 지도를 확인할 수 있습니다 (사용자페이지 업체정보 아래 표시됨).")?>
               		<?=_DescStr("주소 등록시 좌표는 자동으로 등록되며, 지도위치를 변경하시려면 X, Y 좌표를 삭제하신 후 주소 변경 후 수정하시면 됩니다.")?>
               		<?=_DescStr("주소 등록시 주변 경관을 설명하는 문구(OO주유소 근처, 교차로 부근 등)를 입력할 경우 좌표 검색이 되지 않을 수 있으니 주의하시기 바랍니다.")?>
               		<?=_DescStr("좌표를 입력하면 지도가 표시됩니다. 세밀한 조정을 원하시면 빨간 마커를 드래그하여 원하는 위치에 놓으시면 됩니다.")?>
               	</td>
               </tr>

                <tr style="display:none;">
					<td class="article">rss 추가 정보</td>
					<td class="conts">
						지역명 : <input type="text" name="rssarea1" size="10" class="input_text" value="<?=$row['rssarea1']?>"> /
						위치 : <input type="text" name="rssarea2" size="10" class="input_text" value="<?=$row['rssarea2']?>"><br>
						<?=_DescStr("예) 서울  /  홍대")?><br>
						카테고리 : <input type="text" name="rsscate" size="10" class="input_text" value="<?=$row['rsscate']?>"><br>
						<?=_DescStr("예) 맛집 , 공연 , LIFE , 여행 , 미분류 : 카테고리는 1개만 적어주시기 바랍니다.")?>
                    </td>
				</tr>

                <tr>
					<td class="article">쿠폰사용만료일<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<input id="id_expire" type="text" name="expire" size="10" class="input_text" readonly style="cursor:pointer" value="<?=(($row['expire'] && $row['expire']!="0000-00-00") ? $row['expire'] : date("Y-m-d" , strtotime("+30 day")))?>">
                    </td>
				</tr>
                <tr>
					<td class="article">메인<span class="ic_ess" title="필수"></span><br>
						<?=_DescStr("크기 : " . ($arr_product_size["메인"][0] > 0 ? $arr_product_size["메인"][0] : "-") . " x " . ($arr_product_size["메인"][1] > 0 ? $arr_product_size["메인"][1] : "-"))?>
					</td>
					<td class="conts">
						<?=_PhotoHybridForm( "../upfiles/product" , "main_img"  , (strpos($row['main_img'], '//') !== false?$row['main_img']:str_replace("/upfiles/product/" , "" , $row['main_img'])))?>
						<?=_DescStr("상세페이지에 노출되는 대표 이미지 입니다.")?>
                    </td>
				</tr>
                <tr>
					<td class="article">정사각형목록<span class="ic_ess" title="필수"></span>
						<?=_DescStr("크기 : " . ($arr_product_size["정사각형목록"][0] > 0 ? $arr_product_size["정사각형목록"][0] : "-") . " x " . ($arr_product_size["정사각형목록"][1] > 0 ? $arr_product_size["정사각형목록"][1] : "-"))?>
					</td>
					<td class="conts">
						<?=_PhotoHybridForm( "../upfiles/product" , "prolist_img"  , (strpos($row['prolist_img'], '//') !== false?$row['prolist_img']:str_replace("/upfiles/product/" , "" , $row['prolist_img'])))?>
						<?=_DescStr("일반목록 이미지 및 기타 썸네일 이미지에 적용됩니다.")?>
                    </td>
				</tr>
                <tr>
					<td class="article">직사각형목록<span class="ic_ess" title="필수"></span>
						<?=_DescStr("크기 : " . ($arr_product_size["직사각형목록"][0] > 0 ? $arr_product_size["직사각형목록"][0] : "-") . " x " . ($arr_product_size["직사각형목록"][1] > 0 ? $arr_product_size["직사각형목록"][1] : "-"))?>
					</td>
					<td class="conts">
						<?=_PhotoHybridForm( "../upfiles/product" , "prolist_img2"  , (strpos($row['prolist_img2'], '//') !== false?$row['prolist_img2']:str_replace("/upfiles/product/" , "" , $row['prolist_img2'])))?>
						<?=_DescStr("<b>모바일 상세페이지</b>와 <b>여행/레져형 이미지</b> 및 기타 썸네일 이미지에 적용됩니다.")?>
                    </td>
				</tr>

				<tr>
					<td class="article">썸네일 이미지</td>
					<td class="conts">
						<?=_DescStr("자동적용된 이미지를 노출합니다.")?>
						<?PHP
						// - 썸네일 항목 지정 ---
						$arr_producttmp_size = array("장바구니" => "prolist_img" , "최근본상품" => "prolist_img2" , "주문확인" => "prolist_img2");
						if( sizeof($arr_producttmp_size) > 0 ) {
							$app_w = sizeof($arr_producttmp_size) / 100;
							$app_dir = "../upfiles/product";

							echo "<table width=700 border=1><tr>";
							foreach( $arr_producttmp_size as $k=>$v ){
								echo "<th width='". $app_w ."'>". $k . "</th>";
							}
							echo "</tr><tr>";
							foreach( $arr_producttmp_size as $k=>$v ){
								$appv = $arr_product_size[$k];


							$thumb_image = '';
							if(file_exists($app_dir . "/" . app_thumbnail($k, $row)) ){ // 썸네일 정보가 있다면

										$thumb_image = $app_dir ."/" . app_thumbnail($k, $row);

							}else{ // 썸네일 정보가 없다면

										if(preg_match("/(http|https):\/\/([0-9a-z-.\/@~?&=_]+)/i",$row[$v])){ // 외부이미지 일경우

											$thumb_image = $row[$v];

										}else{ // 아무정보도 없을 시 원본 이미지를 출력

											$thumb_image = $app_dir ."/".$row[$v];
										}

							}
								echo "<th><img src='".$thumb_image."' style='max-width:300px;'></th>";

							}
							echo "</tr></table>";
						}
						// - 썸네일 항목 지정 ---
						?>
                    </td>
				</tr>

			</tbody>
		</table>
	</div>
	<!-- // 검색영역 -->

	<!-- 버튼영역 -->
	<div class="bottom_btn_area">
		<div class="btn_line_up_center">
			<span class="shop_btn_pack">
				<input type="submit" name="" class="input_large red" value="등록하기">
				<input type="button" name="" class="input_large gray" value="목록보기" onclick="location.href=('_product.list.php?<?=enc("d" , $_PVSC)?>');">
				<? if($code) { ?><input type="button" name="" class="input_large gray" value="복사하기" id="copy"><? } ?>
			</span>
		</div>
	</div>
	<!-- 버튼영역 -->
</form>

<script language="JavaScript" src="_product.js"></script>
<link rel="stylesheet" href="/include/js/jquery/jqueryui/jquery-ui.min.css" type="text/css">
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
$(document).ready(function(){

	$('.sale_type_value').on('click', function() {

		var checked = $('.sale_type_value:checked').val();
		if(checked == 'A') $('.sale_type').hide();
		else $('.sale_type').show();
	});

	// 상품복사
	$('#copy').on('click',function(e){
		e.preventDefault();
		var c = confirm('상품을 복사하시겠습니까?');
		if(c){ location.href='_product.copy.php?pcode=<?=$code?>'; }
	});

	// -  validate ---
    $("form[name=frm]").validate({
		ignore: "input[type=text]:hidden",
        rules: {
			sale_date: {
				required: function() {

					return ($('.sale_type_value:checked').val() == 'T'?true:false);
				}
			},//판매시작일
			sale_dateh: {
				required: function() {

					return ($('.sale_type_value:checked').val() == 'T'?true:false);
				}
			},//판매시작일
			sale_datem: {
				required: function() {

					return ($('.sale_type_value:checked').val() == 'T'?true:false);
				}
			},//판매시작일
			sale_enddate: {
				required: function() {

					return ($('.sale_type_value:checked').val() == 'T'?true:false);
				}
			},//판매종료일
			sale_enddateh: {
				required: function() {

					return ($('.sale_type_value:checked').val() == 'T'?true:false);
				}
			},//판매종료일
			sale_enddatem: {
				required: function() {

					return ($('.sale_type_value:checked').val() == 'T'?true:false);
				}
			},//판매종료일
			code: { required: true},//상품코드
			name: { required: true},//상품명
			customerCode: { required: true},//상품 공급업체
			md_name: { required: true},//담당MD
			purPrice:{required: function() { return ($("input[name=comSaleType]").filter(function() {if (this.checked) return this;}).val() == "공급가") ? true : false } },//업체정산형태 - 공급가 --
			commission:{required: function() { return ($("input[name=comSaleType]").filter(function() {if (this.checked) return this;}).val() == "수수료") ? true : false } },//업체정산형태 - 수수료
			price: { required: true},//판매가격
			price_per: { required: true},//할인율
			//comment2: { required: true},//상품 상세설명
			//comment3: { required: true},//주문확인서 주의사항
			main_img:{
				accept:"gif|jpg|png" ,
				required: function() {return ($("input[name=main_img_OLD]").val() == undefined ) ? true : ($("input[name=main_img_OLD]").val()!="") ? false : true}
			},//메인1
			prolist_img:{
				accept:"gif|jpg|png" ,
				required: function() {return ($("input[name=prolist_img_OLD]").val() == undefined ) ? true : ($("input[name=prolist_img_OLD]").val()!="") ? false : true}
			},//정사각형목록
			prolist_img2:{
				accept:"gif|jpg|png" ,
				required: function() {return ($("input[name=prolist_img2_OLD]").val() == undefined ) ? true : ($("input[name=prolist_img2_OLD]").val()!="") ? false : true}
			},//직사각형목록
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
			comment2: { required: "상품 상세설명을 입력하시기 바랍니다."},//상품 상세설명
			comment3: { required: "주문확인서 주의사항을 입력하시기 바랍니다."},//주문확인서 주의사항
			main_img:{
				accept:"이미지는 gif , jpg, png만 등록가능하십니다." ,
				required: "메인 이미지를 입력하시기 바랍니다."
			},//메인1
			prolist_img:{
				accept:"이미지는 gif , jpg, png만 등록가능하십니다." ,
				required: "정사각형목록을 입력하시기 바랍니다."
			},//정사각형목록
			prolist_img2:{
				accept:"이미지는 gif , jpg, png만 등록가능하십니다." ,
				required: "직사각형목록을 입력하시기 바랍니다."
			},//직사각형목록
			expire: { required: "쿠폰사용만료일을 입력하시기 바랍니다."}//쿠폰사용만료일
        },
        submitHandler : function(form) {
    		tinyMCE.triggerSave();
    		formSubmitSet();	// .number_style 의 콤마를 제거한다.
    		form.submit();

        }
    });
	// - validate ---

	// - 테마 체크박스 선택시 이벤트
	// -- 동일 테마가 있다면 동일하게 체크 적용
	$(".cls_thema input[type='checkbox']").click(function(){
		if (this.checked) { $("input[data-realthema='"+this.value+"']").attr("checked" , true); }
		else { $("input[data-realthema='"+this.value+"']").attr("checked" , false); }
	});

	// 관련상품 지정 # LDD012
	$('.relation_auto_mode').on('click', function() {

		var value = $('.relation_auto_mode:checked').val();
		if(!value) value = 'N';

		if(value == 'Y') $('.relation_use').hide();
		else $('.relation_use').show();
	});
});

function reqinfo_popup() {
	window.open("_product_reqinfo.popup.php?pass_code=<?=$code?>","","width=1200,height=600,scrollbars=yes");
}

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
			url: "_product.get_company_addr.php",
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

<?PHP include_once("inc.footer.php"); ?>