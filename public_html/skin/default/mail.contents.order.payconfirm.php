<?php
/*
결제확인처리
*/

# 메일 본문::주문자 정보
$mailing_app_content = '
<div style="margin:40px 50px 50px 50px;">
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
</div>
';