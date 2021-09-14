<?php
include_once('inc.php');
$fileName = 'product';
$toDay = date('Y-m-d', time());

header( "Content-type: application/vnd.ms-excel; charset=utf-8" ); 
header( "Content-Disposition: attachment; filename=$fileName-$toDay.xls" ); 
header( "Content-Description: PHP4 Generated Data" ); 
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");
$pr = _MQ_assoc(" select * from `odtProduct` where customerCode='". $com[id] ."' ");
?>
<table border="1">
	<thead>
		<tr>
			<th>상품코드(신규등록시 생략)</th>
			<th style="background-color:#F79646; color:#fff">상품명</th>
			<th style="background-color:#F79646; color:#fff">1차 분류</th>
			<th style="background-color:#F79646; color:#fff">2차 분류</th>
			<th style="background-color:#F79646; color:#fff">3차 분류</th>
			<th style="background-color:#F79646; color:#fff">상품공급업체 아이디</th>
			<th style="background-color:#F79646; color:#fff">담당MD 이름</th>
			<th style="background-color:#F79646; color:#fff">판매설정(상시, 기간)</th>
			<th style="background-color:#F79646; color:#fff">판매시작일(상시의 경우 제외)</th>
			<th style="background-color:#F79646; color:#fff">판매종료일(상시의 경우 제외)</th>
			<th style="background-color:#F79646; color:#fff">업체정산형태(공급가/수수료)</th>
			<th style="background-color:#F79646; color:#fff">매입가격(공급가격)/수수료(%제외)</th>
			<th style="background-color:#F79646; color:#fff">상품구분(배송상품/쿠폰상품)</th>
			<th style="background-color:#92D050">배송구분(일반/개별/무료)</th>
			<th style="background-color:#92D050">개별배송비(개별배송일 경우 필수)</th>
			<th>정상가격</th>
			<th style="background-color:#F79646; color:#fff">판매가격</th>
			<th style="background-color:#F79646; color:#fff">할인률</th>
			<th style="background-color:#F79646; color:#fff">상품 상세설명 - 엔터제외</th>
			<th style="background-color:#F79646; color:#fff">상품 상세설명(모바일) - 엔터제외</th>
			<th style="background-color:#F79646; color:#fff">주문확인서 주의사항 - 엔터제외</th>
			<th style="background-color:#F79646; color:#fff">상품이미지(메인: 480 x 490)</th>
			<th style="background-color:#F79646; color:#fff">상품이미지(정사각형: 330 x 330)</th>
			<th style="background-color:#F79646; color:#fff">상품이미지(직사각형: 489 x 330)</th>
			<th>상품노출(Y/N)</th>
			<th>노출순서</th>
			<th>추천상품(Y/N)</th>
			<th>상품쿠폰 - 쿠폰명</th>
			<th>상품쿠폰 - 할인률</th>
			<th>1차 옵션 타이틀</th>
			<th>2차 옵션 타이틀</th>
			<th>3차 옵션 타이틀</th>
			<th>중복구매 불가여부(Y/N)</th>
			<th>적립금(%제외 / 없을경우 0)</th>
			<th>재고량</th>
			<th>1회 최대 구매량</th>
			<th>현 판매량</th>
			<th>관련상품 지정방식(수동/자동)</th>
			<th>수동 관련상품(상품코드별 구분은 /)</th>
			<th>간략 상세정보 - 엔터제외</th>
			<th>상품 사용 정보 - 엔터제외</th>
			<th>업체 이용 정보 - 엔터제외</th>
			<th>지도주소 (공급업체 주소를 사용할 경우 "공급업체"를 기입)</th>
			<th>쿠폰사용만료일</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($pr as $k=>$v) {

			// 기타 코드
			$v = array_merge($v, _text_info_extraction( "odtProduct" , $v['serialnum'] ));

			// 카테고리 정보 추출
			$FirstCate = _MQ(" select `pct_cuid` from `odtProductCategory`  where `pct_pcode` = '{$v['code']}' order by `pct_uid` asc ");
			$Data = _MQ(" select `catecode`, `parent_catecode` from `odtCategory` where `catecode` = '{$FirstCate['pct_cuid']}' ");
			$code = array();
			$code[] = $Data['catecode'];
			$code = @array_merge($code, explode(',', $Data['parent_catecode']));
			@asort($code); // value 값으로 asc 정렬
			$CateInfo = _MQ_assoc(" select * from `odtCategory` where `catecode` in ('".implode("','", $code)."') order by `catedepth` asc ");
		?>
		<tr>
			<td><?php echo $v['code']; ?></td>
			<td><?php echo $v['name']; ?></td>
			<td><?php echo $CateInfo[0]['catename']; ?></td>
			<td><?php echo $CateInfo[1]['catename']; ?></td>
			<td><?php echo $CateInfo[2]['catename']; ?></td>
			<td><?php echo $v['customerCode']; ?></td>
			<td><?php echo $v['md_name']; ?></td>
			<td><?php echo ($v['sale_type'] == 'T'?'기간':'상시'); ?></td>
			<td><?php echo $v['sale_date']; ?></td>
			<td><?php echo $v['sale_enddate']; ?></td>
			<td><?php echo $v['comSaleType']; ?></td>
			<td><?php echo ($v['comSaleType'] == '수수료'?$v['commission']:$v['purPrice']); ?></td>
			<td><?php echo ($v['setup_delivery'] == 'Y'?'배송상품':'쿠폰상품'); ?></td>
			<td>
				<?php echo ($v['del_type'] == 'normal'?'일반':($v['del_type'] == 'unit'?'개별':'무료')); ?>
			</td>
			<td><?php echo $v['del_price']; ?></td>
			<td><?php echo $v['price_org']; ?></td>
			<td><?php echo $v['price']; ?></td>
			<td><?php echo $v['price_per']; ?></td>
			<td><?php echo rm_enter(htmlspecialchars($v['comment2'])); ?></td>
			<td><?php echo rm_enter(htmlspecialchars($v['comment2_m'])); ?></td>
			<td><?php echo rm_enter(htmlspecialchars($v['comment3'])); ?></td>
			<td><?php echo $v['main_img']; ?></td>
			<td><?php echo $v['prolist_img']; ?></td>
			<td><?php echo $v['prolist_img2']; ?></td>
			<td><?php echo $v['p_view']; ?></td>
			<td><?php echo $v['pro_idx']; ?></td>
			<td><?php echo $v['bestview']; ?></td>
			<td><?php echo $v['coupon_title']; ?></td>
			<td><?php echo $v['coupon_price']; ?></td>
			<td><?php echo $v['option1_title']; ?></td>
			<td><?php echo $v['option2_title']; ?></td>
			<td><?php echo $v['option3_title']; ?></td>
			<td><?php echo ($v['ipDistinct']==1?'Y':'N'); ?></td>
			<td><?php echo $v['point']; ?></td>
			<td><?php echo $v['stock']; ?></td>
			<td><?php echo $v['buy_limit']; ?></td>
			<td><?php echo $v['saleCnt']; ?></td>
			<td><?php echo ($v['relation_auto'] == 'Y'?'자동':'수동'); ?></td>
			<td><?php echo $v['p_relation']; ?></td>
			<td><?php echo rm_enter(htmlspecialchars($v['short_comment'])); ?></td>
			<td><?php echo rm_enter(htmlspecialchars($v['comment_proinfo'])); ?></td>
			<td><?php echo rm_enter(htmlspecialchars($v['comment_useinfo'])); ?></td>
			<td><?php echo $v['com_juso']; ?></td>
			<td><?php echo $v['expire']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>