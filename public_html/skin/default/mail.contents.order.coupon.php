<?PHP
/*
쿠폰발급 Mail Content
*/
# 메일상단
$mailing_app_content = '
<div style="margin:40px 50px 50px 50px;">
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

# 주문상품정보 상단
$mailing_app_content .= '
	<dl style="margin-top:30px">
		<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">
			주문상품
		</dt>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
';

# 쿠폰정보 {
if( sizeof( $opr ) > 0 ) {
	foreach( $opr as $opk=>$opv ){

		// 쿠폰상품이 아니면 통과.
		if($opv[op_orderproduct_type] != "coupon") continue;

		/*---- 쿠폰번호 추출 ----*/
		$coupon_list = array();
		$coupon_print="";
		if($opv[op_delivstatus] != "Y") { 
			$coupon_print = "해당상품은 쿠폰이 아직 발급전입니다.";
		} else {
			$coupon_assoc = _MQ_assoc("select opc_expressnum from odtOrderProductCoupon where opc_opuid = '".$opv[op_uid]."'");
			foreach($coupon_assoc as $coupon_key => $coupon_row) {
				$coupon_list[] = $coupon_row[opc_expressnum];
			}
			$coupon_print = is_array($coupon_list) ? implode(", ",$coupon_list) : "";
		}

		$mailing_app_content .= '
		<div style="min-height:120px; position:relative;'.($opk == 0?'':'margin-top:50px;').'">
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
					'.($opv['op_is_addoption']=="Y" ? "추가옵션" : "선택옵션").': '.($opv['op_option1'] ? trim($opv['op_option1'] ." ". $opv['op_option2'] ." ". $opv['op_option3']) : "옵션없음").' ('.$opv['op_cnt'].'개)
				</div>
				<div style="font-family:\'나눔고딕\',\'돋움\'; font-size:15px; font-weight:600; margin:10px 0 0 0">
					유효기간: ~ '.$or['expire'].'일 까지
				</div>
				<div style="font-family:\'나눔고딕\',\'돋움\'; font-size:15px; font-weight:600; margin:10px 0 0 0">
					쿠폰번호: <span style="font-family:\'나눔고딕\',\'돋움\'; font-size:12px;">'.$coupon_print.'</span>
				</div>
				<div style="font-family:\'나눔고딕\',\'돋움\'; font-size:15px; font-weight:600; color:#ff0000; margin:10px 0 0 0">
					상품가격 : '.number_format(($opv['op_pprice'] + $opv['op_poptionprice'])*$opv['op_cnt']).'원
				</div>
			</div>
			<div style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:0px; font-weight:600; color:#555; margin:15px 0 0 0; border-top:1px solid #ddd;'.(!$opv['comment3'] ? "display:none;" : null).'">
				'. $opv['comment3'] .'
			</div>
		</div>
		';
	}
}
# 쿠폰정보 }

# 주문상품정보 하단
$mailing_app_content .= '
		</dd>
	</dl>
';

# 메일하단
$mailing_app_content .= '
	</div>
';
?>