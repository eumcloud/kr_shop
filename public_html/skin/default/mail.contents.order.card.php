<?PHP
/*
카드결제 Mail Content
*/
include(dirname(__FILE__).'/mail.contents.order.online.php'); // 카드 결제를 별도로 사용 하시려면 이줄을 지우고 하단 내용을 사용하세요.
return; // 카드 결제를 별도로 사용 하시려면 이줄을 지우고 하단 내용을 사용하세요.

# 메일상단
$mailing_app_content = '
<div style="margin:40px 50px 50px 50px;">
';

# 메일 본문::주문자 정보
$mailing_app_content .= '
	<dl style="margin-top:30px">
		<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">
			주문정보
		</dt>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0; overflow:hidden">
			<table style="overflow:hidden; width:45%; float:left;">
				<colgroup>
					<col width="100">
					<col width="*">
				</colgroup>
				<tbody>
					<tr>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#999; border-bottom:1px dotted #ddd">고객명</td>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#000; text-align:left; border-bottom:1px dotted #ddd">
							'.$or['ordername'].'
						</td>
					</tr>
					<tr>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#999; border-bottom:1px dotted #ddd">주문번호</td>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#000; text-align:left; border-bottom:1px dotted #ddd">
							<strong style="color:#ed0000">'.$or['ordernum'].'</strong>
						</td>
					</tr>
					'.($or['paymethod'] == 'B'?'
					<tr>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#999; border-bottom:1px dotted #ddd">계좌정보</td>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#000; text-align:left; border-bottom:1px dotted #ddd">
							'.$or['paybankname'].'
						</td>
					</tr>
					':'').'
					<tr>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#999; border-bottom:1px dotted #ddd">최종결제금액</td>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#000; text-align:left; border-bottom:1px dotted #ddd">
							<strong style="color:#ed0000">'.number_format($or[tPrice]).'</strong>원
						</td>
					</tr>
				</tbody>
			</table>
			<table style="overflow:hidden; width:45%; float:right;">
				<colgroup>
					<col width="100">
					<col width="*">
				</colgroup>
				<tbody>
					<tr>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#999; border-bottom:1px dotted #ddd">주문일자</td>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#000; text-align:left; border-bottom:1px dotted #ddd">
							'.str_replace("-", ".", substr($or['orderdate'],0,10)).'
						</td>
					</tr>
					<tr>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#999; border-bottom:1px dotted #ddd">결제수단</td>
						<td style="font:13px \'나눔고딕\',\'돋움\'; padding:10px; color:#000; text-align:left; border-bottom:1px dotted #ddd">
							'.$arr_paymethod_name[$or['paymethod']].'
						</td>
					</tr>
				</tbody>
			</table>
		</dd>
	</dl>
';

# 사용자 정보
if($or['order_type'] == "coupon" || $or['order_type'] == "both") {

	$mailing_app_content .= '
		<dl style="margin-top:30px">
			<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">
				사용자 정보
			</dt>
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				사용하시는 분 : '.$or['username'].'
			</dd>
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				휴대폰번호 : '.phone_print($or['userhtel1'], $or['userhtel2'], $or['userhtel3']).'
			</dd>
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				이메일 : '.$or['useremail'].'
			</dd>
		</dl>
	';
}

# 배송정보
if($or['order_type'] == "product" || $or['order_type'] == "both") {

	$mailing_app_content .= '
		<dl style="margin-top:30px">
			<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">
				배송정보
			</dt>
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				받으시는 분 : '.$or['recname'].'
			</dd>
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				휴대폰번호 : '.phone_print($or['rechtel1'], $or['rechtel2'], $or['rechtel3']).'
			</dd>
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				배송지 주소 : ('.$or['reczip1']."-".$or['reczip2'].") ".$or['recaddress']." ".$or['recaddress1'].'
			</dd>
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				도로명 주소 : '.$or['recaddress_doro'].'
			</dd>
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				새 우편번호 : '.$or['reczonecode'].'
			</dd>
	';
	
	# LDD018
	if($or['delivery_date'] != '0000-00-00') {

		$mailing_app_content .= '
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				배송요청일 : '.$or['delivery_date'].'
			</dd>
		';
	}
	# LDD018

	$mailing_app_content .= '
			<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
				배송메모 : '.$or['comment'].'
			</dd>
		</dl>
	';
}

# 주문상품정보 상단
$mailing_app_content .= '
	<dl style="margin-top:30px">
		<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">
			주문상품
		</dt>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
';

# 상품정보 {
if(sizeof($opr) > 0) {
	foreach($opr as $opk=>$opv) {
		$mailing_app_content .= '
		<div style="min-height:120px; position:relative">
			<span style="float:left">
				<a href="'. rewrite_url($opv['code']) .'" target="_blank">
					<img src="'.product_thumb_img( $opv , '주문확인' ,  'data').'" style="max-width:160px; max-height:110px; " alt="" />
				</a>
			</span>
			<div style="margin-left:180px;">
				<a href="'. rewrite_url($opv['code']) .'" target="_blank" style="text-decoration:none ;font-family:\'나눔고딕\',\'돋움\'; font-size:14px; font-weight:600; color:#000; display:block;">
					'.$opv['name'].'
				</a>
				<div style="font-family:\'나눔고딕\',\'돋움\'; font-size:15px; font-weight:600; margin:10px 0 0 0">
					'.($opv[op_is_addoption]=="Y" ? "추가옵션" : "선택옵션").': '.($opv['op_option1'] ? trim($opv['op_option1'] ." ". $opv['op_option2'] ." ". $opv['op_option3']) : "옵션없음").' ('.$opv['op_cnt'].'개)
				</div>
				<div style="font-family:\'나눔고딕\',\'돋움\'; font-size:15px; font-weight:600; color:#ff0000; margin:10px 0 0 0">
					상품가격 : '.number_format(($opv['op_pprice'] + $opv['op_poptionprice'])*$opv['op_cnt']).'원
				</div>
			</div>
		</div>
		';
	}
}
# 상품정보 }

# 주문상품정보 하단   // 할인상세내역 출력 LCY002 
$mailing_app_content .= '
			<div style="text-align:center; font-family:\'나눔고딕\',\'돋움\'; font-size:15px; font-weight:600; color:#000; margin:10px 0 0 0; border-top:1px dashed #ccc; padding-top:15px; ">
				주문금액: '.number_format($or['tPrice']+$or['sPrice']-$or['dPrice']).' + 배송비 '.number_format($or['dPrice']).'원 - 할인금액 '.number_format($or['sPrice']).'원 = 
				합계금액 : <strong style="color:#ff0000; font-size:17px;">'.number_format($or['tPrice']).'원</strong>
			</div>
			';
if($or['sPrice']>0){
$mailing_app_content .= '
			<div style="text-align:center; font-family:\'나눔고딕\',\'돋움\'; font-size:12px; font-weight:600; color:#000; margin:10px 0 0 0; border-top:1px dashed #ccc; padding-top:15px; ">
				할인상세내역 : 
				포인트 '.number_format($or['gPrice']).'원 + 
				쿠폰 '.number_format($total_cprice).'원  +
				프로모션코드 '.number_format($or['o_promotion_price']).'원  =
				총 할인금액 : <strong style="color:#ff0000; font-size:17px;">'.number_format($or['sPrice']).'원</strong>
			</div>
	';
}
$mailing_app_content .= '
		</dd>
	</dl>
';

# 메일하단
$mailing_app_content .= '
	</div>
';