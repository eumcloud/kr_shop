<?
					$sque = "update odtOrder set paystatus='Y' , paydate = now() , orderstatus_step='결제확인' , orderstep='finish' , ordersau = '' where ordernum='". $ordernum ."' ";
					_MQ_noreturn($sque);

					// 결제가 확인되었을 경우 - 포인트 쿠폰 - 적용
					// 제공변수 : $_ordernum
					$_ordernum = $ordernum;
					include(dirname(__FILE__)."/shop.order.pointadd_pro.php");

					// 쿠폰상품은 티켓을 발행한다.
					// 제공변수 : $_ordernum
					$_ordernum = $ordernum;
					include(dirname(__FILE__)."/shop.order.couponadd_pro.php");

					// 상품 재고 차감 및 판매량 증가
					$_ordernum = $ordernum;
					include(dirname(__FILE__)."/shop.order.salecntadd_pro.php");

					// 결제완료 문자발송
					$_ordernum = $ordernum;
					include(dirname(__FILE__)."/shop.order.sms_send.php");

					// 제휴마케팅 처리
					$_ordernum = $ordernum;
					include(dirname(__FILE__)."/shop.order.aff_marketing_pro.php");

					// - 메일발송 ---
					$_oemail = $osr[orderemail];
					if( mailCheck($_oemail) ){
						$_ordernum = $ordernum;
						$_type = "card"; // 결제확인처리
						include("shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
						$_title = "[".$row_setup['site_name']."] 주문하신 상품의 결제가 성공적으로 완료되었습니다!";
						//$_title_img = "images/mailing/title_order.gif";
						$_title_content = '<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님이 주문하신 내역입니다.</strong>';
						$_content = $mailing_app_content;
						$_content = get_mail_content($_title,$_title_content,$_content);
						mailer( $_oemail , $_title , $_content );

						if($osr[order_type] == "coupon" || $osr[order_type] == "both") {
							$_type = "coupon"; // 쿠폰발송
							include("shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
							$_title = "[".$row_setup['site_name']."] 주문하신 상품의 쿠폰이 발송되었습니다.";
							$_title_content = '
							<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님</strong>께서 주문하신 쿠폰이 발송되었습니다. <br />
							저희 사이트에서 구매해주셔서 감사합니다. 보다 나은 상품과 큰 만족을 위해 최선을 다하겠습니다.
							';
							$_content = $mailing_app_content;
							$_content = get_mail_content($_title, $_title_content, $_content);
							mailer( $_oemail , $_title , $_content );						
						}
					}
					// - 메일발송 ---

					// 주문상태 업데이트
					order_status_update($ordernum);					

?>