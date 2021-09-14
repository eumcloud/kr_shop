<?PHP


	// 적용메일
	// _ordernum ==> 주문번호
	// _type ==> card (주문내역 : 카드결제)
	// _type ==> online (주문내역 : 무통장입금)
	// _type ==> payconfirm (주문내역 : 결제확인)
	// _type ==> delivery (주문내역 : 상품발송)
	// 각 _type에 따른 mailing_app_content 불러오기



	// - 주문내역 ---
	$oque = " select * from odtOrder where ordernum='" . $_ordernum . "' ";
	$or = _MQ($oque);
	// - 주문내역 ---


	// - 주문상품내역 ---
	$opque = "
		select op.* , p.name, p.prolist_img2 , p.code
		from odtOrderProduct as op
		inner join odtProduct as p on ( p.code=op.op_pcode )
		where op_oordernum='" . $_ordernum . "'
	";
	$opr = _MQ_assoc($opque);
	// - 주문상품내역 ---

	
	// 주문 할인 상세 내역 관련 LCY002   : 적용된 사항 => card, online, delivery
	    $cque = "
	        select
	            cl_price
	        from odtOrderCouponLog
	        where cl_oordernum='".$_ordernum."'
	    ";
	    $cres = _MQ_assoc($cque);
	    $total_cprice=0;
	    if(count($cres)>0){
		    foreach($cres as $ck=>$cv){
		        $total_cprice+=$cv['cl_price'];
		    }
	    }

	switch( $_type ){
		case "card"			: include($_SERVER['DOCUMENT_ROOT'] . "/pages/mail.contents.order.card.php"); break;
		case "online"		: include($_SERVER['DOCUMENT_ROOT'] . "/pages/mail.contents.order.online.php"); break;
		case "payconfirm"	: include($_SERVER['DOCUMENT_ROOT'] . "/pages/mail.contents.order.payconfirm.php"); break;
		case "delivery"		: include($_SERVER['DOCUMENT_ROOT'] . "/pages/mail.contents.order.delivery.php"); break;	// 해당주문의 배송상품에 대해서만 발송된다.
		case "coupon"		: include($_SERVER['DOCUMENT_ROOT'] . "/pages/mail.contents.order.coupon.php"); break;	// 해당주문의 쿠폰상품에 대해서만 발송된다.
	}



?>