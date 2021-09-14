<?PHP

	include "./inc.php";


	if($tran_type == "ins_excel") {

        // -- 이전 엑셀 업로드데이터 삭제 ---
        _MQ_noreturn(" delete from odtExpressTmpTable where partnerCode = '" . $com[id] . "' ");


		require_once "../include/reader.php";

		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('utf-8');

		$data->read($_FILES[w_excel_file][tmp_name]);

		error_reporting(E_ALL ^ E_NOTICE);


		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

			$r = $data->sheets[0]['cells'][$i];
			$_opuid = trim($r[1]);
			$_ordernum = trim($r[2]);
			$_expressname = trim($r[7]);
			$_expressnum = trim($r[8]);

			if( $_opuid && $_expressname && ($_expressnum || $_expressname == '[자체배송]')){
				$que = "
					insert odtExpressTmpTable set
						ordernum	= '". $_ordernum ."',
						opuid = '". $_opuid ."',
						express = '". $_expressname ."',
						expressNum = '". $_expressnum ."',
						partnerCode	= '" . $com[id] . "',
						regidate = now()
				";
				_MQ_noreturn($que);
				order_status_update($_ordernum);
			}
		}


		error_loc("_order2.list.php?isExcel=Y&" . enc("d" , $_PVSC));

	}
?>