<?PHP
include "inc.php";

// 사전체크
$_delprice		= rm_str($_delprice);
$_delprice_free	= rm_str($_delprice_free);


# LDD007 {
if(!$_product_auto_on) $_product_auto_on = 'N';
$s_que = "";
$s_que .= ", `s_product_auto_C` = '".$_product_auto_C."' ";
$s_que .= ", `s_product_auto_L` = '".$_product_auto_L."' ";
$s_que .= ", `s_product_auto_B` = '".$_product_auto_B."' ";
$s_que .= ", `s_product_auto_G` = '".$_product_auto_G."' ";
$s_que .= ", `s_product_auto_V` = '".$_product_auto_V."' ";
$s_que .= ", `s_coupon_auto_C` = '".$_coupon_auto_C."' ";
$s_que .= ", `s_coupon_auto_L` = '".$_coupon_auto_L."' ";
$s_que .= ", `s_coupon_auto_B` = '".$_coupon_auto_B."' ";
$s_que .= ", `s_coupon_auto_G` = '".$_coupon_auto_G."' ";
$s_que .= ", `s_coupon_auto_V` = '".$_coupon_auto_V."' ";
$s_que .= ", `s_product_auto_on` = '".$_product_auto_on."' ";
# } LDD007

# LDD018 {
$s_que .= ", `reserv_del_use` = '".$reserv_del_use."' ";
$s_que .= ", `reserv_del_term_min` = '".$reserv_del_term_min."' ";
$s_que .= ", `reserv_del_term_max` = '".$reserv_del_term_max."' ";
# LDD018 }

# 추가배송비 설정 추가 2017-05-19 :: SSJ {
$s_que .= ", s_del_addprice_use = '".$_del_addprice_use."' ";
$s_que .= ", s_del_addprice_use_normal = '".$_del_addprice_use_normal."' ";
$s_que .= ", s_del_addprice_use_unit = '".$_del_addprice_use_unit."' ";
$s_que .= ", s_del_addprice_use_free = '".$_del_addprice_use_free."' ";
# 추가배송비 설정 추가 2017-05-19 :: SSJ }

$que = "update odtSetup set 
				s_delprice							= '$_delprice', 
				s_delprice_free					= '$_delprice_free',
				s_del_company						= '$_del_company',
				s_product_commission = '" . $_product_commission . "', 
				s_product_notice = '" . $_product_notice . "'
				{$s_que}
				where serialnum = 1"; // LDD007 {$s_que} 추가

_MQ_noreturn($que);

error_loc("_config.delivery.form.php");