<?PHP
	// *** 결제확인 시 --> 제휴마케팅 처리. ***

	// - 주문정보 추출 ---
	$osr = get_order_info($_ordernum);
	
	// 제휴마케팅 정보 추출
	$arr_cinfo = array();
	$cres = mysql_query("select * from odtClick ");
	while($row = mysql_fetch_array($cres)){
		foreach($row as $k=>$v){
			$arr_cinfo[$row[sc_type]][$k] = $v;
		}
	}


	// ----- 아이라이크클릭 -- 추가된 odtOrderProduct 테이블로 인한 수정 (2011-07-13 : onedaynet)
	if($arr_cinfo["아이라이크클릭"][sc_use] == "Y") 
	{
		$c_ValueFromClick = $_COOKIE["c_ValueFromClick"];

		if ( $c_ValueFromClick != NULL )
		{

			$sres = mysql_query("
				select op.* , (select pct_cuid from odtProductCategory as pct where pct.pct_pcode=op.op_pcode order by pct_uid asc limit 1 ) as cateCode
				from odtOrderProduct as op 
				where op.op_oordernum='$osr[ordernum]' 
			");
			while( $sr = mysql_fetch_assoc($sres) ){
				
				$iPayMethodArray = array("C" => "C","L" => "O");

				$iBuyNo     = $osr[ordernum];
				$iCCode     = $sr[cateCode];
				$iPCode     = $sr[op_pcode];
				$iPopt      =   "";
				$iPName     = urlencode(iconv("utf-8","euckr",$sr[op_pname]));
				$iPNum      =   $sr[op_cnt];
				$iPrice     =   ($sr[op_pprice]+$sr[op_poptionprice]) * $sr[op_cnt];
				$iBuyType   =   $iPayMethodArray[$osr[paymethod]];
				$iMemberID= $osr[orderid];
				$iUserName= urlencode(iconv("utf-8","euckr",$osr[ordername]));

				@mysql_query("insert into odtILikeClickLog set
											`ordernum`      = '".$iBuyNo."',
											`proCode`           =   '".$iPCode."',
											`option`            =   '".$iPopt."',
											`proName`           =   '".$sr[op_pname]."',
											`proCnt`            =   '".$iPNum."',
											`price`             =   '".$iPrice."',
											`id`                    =   '".$iMemberID."',
											`ordername`     =   '".$osr[ordername]."',
											`cookie`            =   '".$c_ValueFromClick."',
											`regidate`      =   now()");


				echo "<SCRIPT LANGUAGE='JavaScript' src='http://www.ilikeclick.com/tracking/sale/v1_Sale.php?MID=".$arr_cinfo["아이라이크클릭"][sc_id]."&BUYNO=".$iBuyNo."&CCODE=".$iCCode."&PCODE=".$iPCode."&POPT=".$iPopt."&PNAME=".$iPName."&PNUM=".$iPNum."&PRICE=".$iPrice."&BUYTYPE=".$iBuyType."&MEMBER_ID=".$iMemberID."&USERNAME=".$iUserName."&ValueFromClick=".$c_ValueFromClick."'></SCRIPT>";

			}
		}
	}
	// ----- 아이라이크클릭 -- 추가된 odtOrderProduct 테이블로 인한 수정 (2011-07-13 : onedaynet)
	


	// ----- 링크프라이스 -- 추가된 odtOrderProduct 테이블로 인한 수정 (2011-07-13 : onedaynet)
	if($arr_cinfo["링크프라이스"][sc_use] == "Y")
	{
		if (isset($_COOKIE["LPINFO"]))
		{
			
			$iMemberID= $osr[orderid];
			$iUserName= urlencode(iconv("utf-8","euckr",$osr[ordername]));
			$iBuyNo     = $osr[ordernum];

			$sres = mysql_query("
				select op.* , (select pct_cuid from odtProductCategory as pct where pct.pct_pcode=op.op_pcode order by pct_uid asc limit 1 ) as cateCode
				from odtOrderProduct as op 
				where op.op_oordernum='$osr[ordernum]' 
			");
			for($i=1; $sr = mysql_fetch_assoc($sres) ; $i++){

				$iPayMethodArray = array("C" => "C","L" => "O");

				$iCCode     = 'mobile'; // 모바일일 경우 - moblie이라고 적용하여야 함.
				$iPCode     = $sr[op_pcode] . "_" . $i;
				$iPopt      =   "";
				$iPName     = urlencode(iconv("utf-8","euckr",$sr[op_pname]));
				$iPNum      =   $sr[op_cnt];
				$iPrice     =   ($sr['op_pprice']+$sr['op_poptionprice']) * $sr['op_cnt'];
				$iBuyType   =   $iPayMethodArray[$osr[paymethod]];

				$ymd = date("Ymd");
				$his = date("His");
				@mysql_query("
					insert into TLINKPRICE
					(
						lpinfo, yyyymmdd, hhmiss,
						order_code, product_code, item_count, price, product_name, category_code,
						id, name, remote_addr
					)
					values
					(
						'".$_COOKIE["LPINFO"]."', '$ymd', '$his',
						'$iBuyNo', '$iPCode', $iPNum, $iPrice, '$sr[op_pname]', '$iCCode',
						'$iMemberID', '$osr[ordername]', '" . $_SERVER[REMOTE_ADDR] . "'
					)
				");

				$p_cd_ar[]   = $iPCode;
				$it_cnt_ar[] = $iPNum;
				$c_cd_ar[]   = $iCCode;
				$sales_ar[]  = $iPrice;
				$p_nm_ar[]   = $iPName;

			}


			// ----- 할인이 있을 경우 추가적용 -----
			if($osr['sPrice'] > 0 ) {

				$iCCode = 'mobile'; // 할인처리
				$iPCode = $osr['ordernum'] . "_discount";
				$iPopt      =   "";
				$iPName     = urlencode(iconv("utf-8","euckr", '할인적용' ));
				$iPNum      =  1;
				$iPrice     =   $osr['sPrice'] * -1 ;
				$iBuyType   =   $iPayMethodArray[$osr['paymethod']];

				$ymd = date("Ymd");
				$his = date("His");
				@mysql_query("
					insert into TLINKPRICE(lpinfo, yyyymmdd, hhmiss, order_code, product_code, item_count, price, product_name, category_code, id, name, remote_addr ) 
					values ( '".$_COOKIE["LPINFO"]."', '$ymd', '$his','$iBuyNo', '$iPCode', $iPNum, $iPrice, '할인적용', '$iCCode','$iMemberID', '$osr[ordername]', '" . $_SERVER[REMOTE_ADDR] . "' )
				");

				$p_cd_ar[]   = $iPCode;
				$it_cnt_ar[] = $iPNum;
				$c_cd_ar[]   = $iCCode;
				$sales_ar[]  = $iPrice;
				$p_nm_ar[]   = $iPName;

			}


				$linkprice_url = "http://service.linkprice.com/lppurchase.php";     // 수정하시면 안됩니다.
				$linkprice_url.= "?a_id=".$_COOKIE["LPINFO"];                                  // 수정하시면 안됩니다.
				$linkprice_url.= "&m_id=" . $arr_cinfo["링크프라이스"][sc_id];                           // 수정하시면 안됩니다.
				$linkprice_url.= "&mbr_id=".$iMemberID."(".$iUserName.")";   // $id = 사용자 ID값, $name = 사용자 이름값, 만약 둘 중 없는 값이 있다면 존재하는 값만을 넣어주시기 바랍니다.
				$linkprice_url.= "&o_cd=".$iBuyNo;                              // $order_code = 주문번호값 입니다.
				$linkprice_url.= "&p_cd=".implode("||", $p_cd_ar);                  // 수정하시면 안됩니다. $p_cd_ar은 위의 장바구니 처리 부분에서 생성된 값입니다.
				$linkprice_url.= "&it_cnt=".implode("||", $it_cnt_ar);              // 수정하시면 안됩니다. $it_cnt_ar은 위의 장바구니 처리 부분에서 생성된 값입니다.
				$linkprice_url.= "&sales=".implode("||", $sales_ar);                // 수정하시면 안됩니다. $sales_ar은 위의 장바구니 처리 부분에서 생성된 값입니다.
				$linkprice_url.= "&c_cd=".implode("||", $c_cd_ar);                  // 수정하시면 안됩니다. $c_cd_ar은 위의 장바구니 처리 부분에서 생성된 값입니다.
				$linkprice_url.= "&p_nm=".implode("||", $p_nm_ar);                  // 수정하시면 안됩니다. $p_nm_ar은 위의 장바구니 처리 부분에서 생성된 값입니다.

				require_once($_SERVER['DOCUMENT_ROOT'] ."/linkprice/lpbase64.php");

				$code = "04287";				// 암호화 코드 값 (encryption code) --- 업체에 따라 고유한 값을 가짐
				$pad = "voJ03DxehYB4y25pXLQsFgikmcH.jEArMSaUzK69ZPOIVwfbNl8Tnqu7CGR*dWt1";				// Encryption pad  --- 업체에 따라 고유한 값을 가짐

				$linkprice_url = lp_url_trt($linkprice_url, $code, $pad);		// 실적 암호화 (Encryption)
				$linkprice_tag = "<script type=\"text/javascript\" src=\"".$linkprice_url."\"> </script>";

				echo $linkprice_tag;

		}
	}
	// ----- 링크프라이스 -- 추가된 odtOrderProduct 테이블로 인한 수정 (2011-07-13 : onedaynet)
?>
