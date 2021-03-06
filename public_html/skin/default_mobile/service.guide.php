<?
	$page_title = "이용안내";
	include dirname(__FILE__)."/cs.header.php";
?>
	
<!-- 서브고정 -->
<div class="common_page">
<div class="common_inner common_full">
	
<div class="cm_fulltext">
<dl>
<dt>회원 가입 안내</dt>
<dd><pre>① 저희 쇼핑몰은 기본적으로 회원제로 운영하고 있습니다. 운영방침에 따라 비회원구매가 가능한 경우가 있습니다. 
② 회원 가입은 월회비, 연회비등 어떠한 비용도 청구하지 않으며 100% 무료로 진행됩니다. 
③ 회원 가입 시 가입 환영 축하금으로 <strong><?=number_format($row_setup['paypoint_join'])?>원</strong>의 적립금이 지급됩니다. 
④ 구매 시 적립된 적립금은 <strong><?=number_format($row_setup['paypoint'])?>원</strong>부터 현금처럼 사용할 수 있습니다. 
⑤ 적립금 제도는 온라인으로 가입한 회원에게만 적용되는 멤버쉽 혜택입니다. </pre></dd>
<dt>적립금 제도</dt>
<dd><pre>① 저희 쇼핑몰은 상품에 따라 일정 비율로 적립금이 자동으로 적립됩니다.
② 적립금 100원은 현금 100원과 같습니다. 
③ 적립금은 <strong><?=number_format($row_setup['paypoint'])?>원</strong> 이상 되면 사용할 수 있고 한 주문 당 <strong><?=number_format($row_setup['paypoint_limit'])?>원</strong> 이 넘는 금액의 적립금은 사용할 수 없습니다. </pre></dd>
<dt>상품 주문 방법</dt>
<dd><pre>저희 쇼핑몰에서 상품을 주문하는 방법은 크게 7단계입니다. 
① 상품검색
② 바로구매 혹은 장바구니에 담기
③ 회원으로 로그인 후 주문 (정책에 따라 비회원 구매 가능)
④ 주문서 작성 
⑤ 결제방법 선택 및 결제 
⑥ 주문 완료 (주문번호) 
⑦ 마이페이지 주문내역 확인 (배송확인)</pre></dd>
<dt>주문확인 및 배송조회</dt>
<dd><pre>쇼핑몰에서 주문을 하셨을 경우 마이페이지의 주문내역에서 주문상황을 바로 확인 하실 수 있습니다. 
주문단계에 따라 주문취소/환불 등이 가능한 경우가 있고 불가능한 경우가 있으니 이에 대한 안내는 1:1온라인문의나 고객센터(<?=$row_company['tel']?>)를 이용해주세요.
정책에 따라 비회원 구매가 가능한 경우에는 주문번호를 꼭 기억하고 계셔야 확인이 가능합니다.
현재 배송은 <strong><?=$row_setup['s_del_company']?></strong> 택배 서비스를 이용하고 있습니다. 
주문상태가 배송으로 바뀌면 택배 운송장번호로 택배사 홈페이지 배송추적을 통해 정확한 배송상태를 추적하실 수 있습니다.</pre></dd>
<dt>안전한 대금 결제 시스템</dt>
<dd><pre>저희 쇼핑몰은 안전한 전자 결제 서비스(PG)에 가입이 되어있으며, 가능한 결제 수단은 주문결제 단계에서 확인할 수 있습니다.
무통장 입금은 PC뱅킹, 인터넷뱅킹, 텔레뱅킹 혹은 가까운 은행에서 직접 입금하시면 되고, 신용카드 결제는 안전한 전자결제 서비스를 이용하므로 보안문제는 걱정하지 않으셔도 되며, 고객님의 이용내역서에는 전자결제 서비스 업체명으로 기록됩니다. 

이용 가능한 국내 발행 신용카드 
- 국내발행 모든 신용카드 

이용 가능한 해외 발생 신용카드 
- VISA Card, MASTER Card, AMEX Card 

무통장 입금 가능 은행 
- 주문 시 무통장 입금을 선택할 경우 가능한 은행 목록을 확인 하실 수 있습니다.
- 무통장 입금시의 송금자 이름은 주문 시 입력하신 주문자의 실명이어야 합니다. </pre></dd>
<dt>배송기간 및 배송방법</dt>
<dd><pre>무통장 입금의 경우는 입금하신 날로부터, 신용카드로 구매하신 경우에는 구매하신 날로부터 2-3일 이내에(최장 7일이내) 입력하신 배송처로 주문상품이 도착하게 됩니다. 
주문하신 상품에 따라 배송기간이 조금 상이할 수 있으니 자세한 사항은 상품상세페이지에 명시되어있는 배송관련 내용을 참조해주세요.
현재 배송은 <strong><?=$row_setup['s_del_company']?></strong> 택배 서비스를 이용하고 있습니다. </pre></dd>
<dt>주문취소, 교환 및 환불</dt>
<dd><pre>쇼핑몰은 소비자의보호를 위해서 규정한 제반 법규를 준수합니다. 
주문 취소는 미결제인 상태에서는 고객님이 직접 취소하실 수가 있습니다. 결제후 취소는 저희 고객센터(<?=$row_company['tel']?>)로 문의해 주시기 바랍니다. 
무통장 입금의 경우 일정기간동안 송금을 하지 않으면 자동 주문 취소가 되고, 구매자가 원하는 경우 인터넷에서 바로 취소 하실 수도 있으며, 송금을 하신 경우에는 환불조치 해드립니다. 
카드로 결제하신 경우, 승인 취소가 가능하면 취소을 해드리지만 승인 취소가 불가능한 경우 해당 금액을 모두 송금해 드립니다. 
이때 승인취소는 카드사에 따라 2-3일 정도 소요될 수 있습니다.
반송을 하실 때에는 주문번호, 회원정보를 메모하여 보내주시면 보다 신속한 처리에 도움이 됩니다.
상품에 문제가 있거나 교환/환불을 원하는 경우 고객센터(<?=$row_company['tel']?>)로 먼저 연락해주시고, 반송주소로 주문번호, 회원정보(이름,휴대폰 번호 등), 반품사유 등을 메모하여 반송해 주시면 상품 대금은 예치되거나 고객센터(<?=$row_company['tel']?>)를 통해 환불조치 됩니다. (반송료는 고객 부담 입니다.)

반송주소 : 하단 카피라잇부분의 사업장소재지(주소)를 참고해 주십시오.</pre></dd>
</dl>
</div>

</div><!-- .common_inner -->
</div><!-- .common_page -->