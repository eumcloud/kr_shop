<?php
/*
추천 Mail Content
 */
$mailling_content = '
<div style="background:#f1f1f1; color:#666; font-family:\'나눔고딕\',\'돋움\'; font-size:17px; text-align:center; line-height:1.5; padding:30px 20px; letter-spacing:-1px; border-bottom:1px solid #ddd">
	'.nl2br(strip_tags($toContent)).'
</div>
<div style="margin:40px 50px 50px 50px;">
	<dl style="margin-top:30px">
		<!-- 내용작은 타이틀 -->
		<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">상품추천</dt>
		<!-- 내용항목 반복 -->
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
			
			<div style="min-height:120px; position:relative">
				<span style="float:left">
					<a href="'.rewrite_url($row_product['code']).'" target="_blank">
						<img src="'.replace_image(IMG_DIR_PRODUCT.$row_product['prolist_img']).'" style="max-width:160px; max-height:120px; " alt="" />
					</a>
				</span>
				<div style="margin-left:180px; ">
					<a href="'.rewrite_url($row_product['code']).'" target="_blank" style="text-decoration:none ;font-family:\'나눔고딕\',\'돋움\'; font-size:14px; font-weight:600; color:#000; display:block;">
						'.$row_product['name'].'
					</a>
					<div style="font-family:\'나눔고딕\',\'돋움\'; font-size:15px; font-weight:600; color:#ff0000; margin:10px 0 0 0">
						상품가격 : '.number_format($row_product['price']).'원
					</div>
				</div>
			</div>
		</dd>
	</dl>
</div>
';