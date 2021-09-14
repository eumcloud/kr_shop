<?PHP
	/*** 추가옵션 가격, 수량 적용을 위한 DB 수정작업 ****/
	include "inc.php";

	echo "<h3>:: 추가옵션 가격, 수량 적용을 위한 DB 수정작업 ::</h3>";
	// 추가옵션 테이블 수정 -----------------------------------------------------------
	echo "<h5 style='background:#CFE0FF;padding:10px;'> 수정테이블 : odtProductAddoption</h5>";
	// 칼럽이 존재하는지 체크 - 존재하지 않으면 칼럼추가
	$chk_res = _MQ(" select * from odtProductAddoption where 1 limit 1 ");
	// pao_poptionprice 칼럼추가
	echo "<div>  ■ 추가항목 : pao_poptionprice</div>";
	if(!in_array("pao_poptionprice",array_keys($chk_res))) {
		_MQ_noreturn(" alter table  odtProductAddoption add  pao_poptionprice int( 10 ) not null default  '0' comment  '옵션가격(추가)'  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 추가되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 존재하는 항목입니다.</div><br>";
	}

	// pao_poptionpurprice 칼럼추가
	echo "<div>  ■ 추가항목 : pao_poptionpurprice</div>";
	if(!in_array("pao_poptionpurprice",array_keys($chk_res))) {
		_MQ_noreturn(" alter table  odtProductAddoption add  pao_poptionpurprice int( 10 ) not null default  '0' comment  '옵션공급가격(추가)'  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 추가되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 존재하는 항목입니다.</div><br>";
	}

	// pao_cnt 칼럼추가
	echo "<div>  ■ 추가항목 : pao_cnt</div>";
	if(!in_array("pao_cnt",array_keys($chk_res))) {
		_MQ_noreturn(" alter table  odtProductAddoption add  pao_cnt int( 10 ) unsigned not null default  '0' comment  '옵션 수량 - 재고'  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 추가되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 존재하는 항목입니다.</div><br>";
	}

	// pao_salecnt 칼럼추가
	echo "<div>  ■ 추가항목 : pao_salecnt</div>";
	if(!in_array("pao_salecnt",array_keys($chk_res))) {
		_MQ_noreturn(" alter table  odtProductAddoption add  pao_salecnt int( 10 ) not null default  '0' comment  '판매개수'  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 추가되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 존재하는 항목입니다.</div><br>";
	}


	// 임시 옵션테이블 수정 -----------------------------------------------------------
	echo "<h5 style='background:#CFE0FF;padding:10px;'> 수정테이블 : odtTmpProductOption</h5>";

	// 칼럽이 존재하는지 체크 - 존재하지 않으면 칼럼추가
	$chk_res = _MQ(" select * from odtTmpProductOption where 1 limit 1 ");
	// otpo_is_addoption 칼럼추가
	echo "<div>  ■ 추가항목 : otpo_is_addoption</div>";
	if(!in_array("otpo_is_addoption",array_keys($chk_res))) {
		_MQ_noreturn(" alter table  odtTmpProductOption add  otpo_is_addoption enum( 'Y' , 'N' ) not null default  'N' comment  '추가옵션여부'  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 추가되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 존재하는 항목입니다.</div><br>";
	}

	echo "<div>  ■ 삭제항목 : otpo_poptionname_add1, otpo_poptionname_add2, otpo_poptionname_add3, otpo_poptionname_add4, otpo_poptionname_add5, otpo_poptionname_add6, otpo_poptionname_add7, otpo_poptionname_add8, otpo_poptionname_add9, otpo_poptionname_add10</div>";
	if(in_array("otpo_poptionname_add1",array_keys($chk_res))) {
		_MQ_noreturn(" ALTER TABLE  odtTmpProductOption DROP otpo_poptionname_add1 , DROP otpo_poptionname_add2 , DROP  otpo_poptionname_add3 , DROP otpo_poptionname_add4 , DROP  otpo_poptionname_add5 , DROP  otpo_poptionname_add6 , DROP  otpo_poptionname_add7 , DROP  otpo_poptionname_add8 ,   DROP otpo_poptionname_add9 , DROP otpo_poptionname_add10  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 삭제되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 삭제된 항목입니다.</div><br>";
	}


	// 장바구니 테이블 수정 -----------------------------------------------------------
	echo "<h5 style='background:#CFE0FF;padding:10px;'> 수정테이블 : odtCart</h5>";

	// 칼럽이 존재하는지 체크 - 존재하지 않으면 칼럼추가
	$chk_res = _MQ(" select * from odtCart where 1 limit 1 ");
	// otpo_is_addoption 칼럼추가
	echo "<div>  ■ 추가항목 : c_is_addoption</div>";
	if(!in_array("c_is_addoption",array_keys($chk_res))) {
		_MQ_noreturn(" alter table  odtCart add  c_is_addoption enum( 'Y' , 'N' ) not null default  'N' comment  '추가옵션여부'  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 추가되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 존재하는 항목입니다.</div><br>";
	}

	echo "<div>  ■ 삭제항목 : c_add_option1, c_add_option2, c_add_option3, c_add_option4, c_add_option5, c_add_option6, c_add_option7, c_add_option8, c_add_option9, c_add_option10</div>";
	if(in_array("c_add_option1",array_keys($chk_res))) {
		_MQ_noreturn(" ALTER TABLE odtCart   DROP c_add_option1,   DROP c_add_option2,   DROP c_add_option3,   DROP c_add_option4,   DROP c_add_option5,   DROP c_add_option6,   DROP c_add_option7,   DROP c_add_option8,   DROP c_add_option9,   DROP c_add_option10  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 삭제되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 삭제된 항목입니다.</div><br>";
	}


	// 주문상품 테이블 수정 -----------------------------------------------------------
	echo "<h5 style='background:#CFE0FF;padding:10px;'> 수정테이블 : odtOrderProduct</h5>";

	// 칼럽이 존재하는지 체크 - 존재하지 않으면 칼럼추가
	$chk_res = _MQ(" select * from odtOrderProduct where 1 limit 1 ");
	// otpo_is_addoption 칼럼추가
	echo "<div>  ■ 추가항목 : op_is_addoption</div>";
	if(!in_array("op_is_addoption",array_keys($chk_res))) {
		_MQ_noreturn(" alter table  odtOrderProduct add  op_is_addoption enum( 'Y' , 'N' ) not null default  'N' comment  '추가옵션여부'  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 추가되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 존재하는 항목입니다.</div><br>";
	}

	echo "<div>  ■ 삭제항목 : op_add_option1, op_add_option2, op_add_option3, op_add_option4, op_add_option5, op_add_option6, op_add_option7, op_add_option8, op_add_option9, op_add_option10, op_add_option1_name, op_add_option2_name, op_add_option3_name, op_add_option4_name, op_add_option5_name, op_add_option6_name, op_add_option7_name, op_add_option8_name, op_add_option9_name, op_add_option10_name</div>";
	if(in_array("op_add_option1",array_keys($chk_res))) {
		_MQ_noreturn(" ALTER TABLE odtOrderProduct   DROP op_add_option1,   DROP op_add_option2,   DROP op_add_option3,   DROP op_add_option4,   DROP op_add_option5,   DROP op_add_option6,   DROP op_add_option7,   DROP op_add_option8,   DROP op_add_option9,   DROP op_add_option10,   DROP op_add_option1_name,   DROP op_add_option2_name,   DROP op_add_option3_name,   DROP op_add_option4_name,   DROP op_add_option5_name,   DROP op_add_option6_name,   DROP op_add_option7_name,   DROP op_add_option8_name,   DROP op_add_option9_name,   DROP op_add_option10_name  ");
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 정상적으로 삭제되었습니다.</div><br>";
	}else{
		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;→ 이미 삭제된 항목입니다.</div><br>";
	}

	exit;


?>