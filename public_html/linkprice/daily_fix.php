<?php
    // DataBase 접속

    // LINKPRICE 를 통해서 신청된 접수내역 QUERY
    // QUERY 조건  : 구매일자=yyyymmdd and 제휴사=링크프라이스
    // SELECT 컬럼 : 구매시간, LPINFO 쿠키, 구매자ID, 구매자이름, 주문번호, 상품코드, 주문수량, 결제금액
    //ex) $query = "select * from 링크프라이스 실적테이블 where 날짜 like '$yyyymmdd%' and LPINFO is not null";
	
	include dirname(__FILE__)."/../include/inc.php";

    $yyyymmdd = $yyyymmdd ? $yyyymmdd : date("Ymd");

	$sql = "select * from TLINKPRICE where YYYYMMDD = '".addslashes($yyyymmdd)."' ";
	$result = _MQ_assoc($sql);
	foreach($result as $k=>$row){

		// 인코딩
		//foreach( $row as $sk=>$sv ){$row[$sk] = ICONV("UTF-8" , "EUC-KR" , $row[$sk]);}

        $line  = $row["hhmiss"]."\t";
        $line .= $row["LPINFO"]."\t";
        $line .= $row["id"]."(".$row["name"].")"."\t";
        $line .= $row["order_code"]."\t";
        $line .= $row["product_code"]."\t";
        $line .= $row["item_count"]."\t";
        $line .= $row["price"]."\t";
        $line .= $row["category_code"]."\t\t";
        $line .= $row["product_name"]."\t";

        if ( $total != 1 )
        {
            $line .= $row["remote_addr"]."\n";
            echo $line;
        }
        // 만약 데이터의 마지막 값이면 줄 바꿈(\n)을 하지 않는다.
        else
        {
            $line .= $row["remote_addr"];
            echo $line;
        }

        $total--;
    }

    // DataBase 접속 끊기
?>