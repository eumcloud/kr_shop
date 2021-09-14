<?php

	# LDD007
	include_once("inc.php");

	foreach ($OpUid as $k=>$v) {

		$r = array();
		$r = _MQ(" select `s_uid` from `odtOrderSettleComplete` where `s_uid` = '{$v}' ");
		$r = array_merge($r , _text_info_extraction( "odtOrderSettleComplete" , $r['s_uid'] ));
		$opuid = explode(',', $r['s_opuid']);
		foreach($opuid as $kk=>$vv) {
			_MQ_noreturn(" update `odtOrderProduct` set `op_settlementstatus` = 'ready' where `op_uid` = '{$vv}' ");
		}

		order_settlement_status_opuid(array_values($opuid));//2015-08-19 추가 - 정준철

		_MQ_noreturn("delete from `odtOrderSettleComplete` where `s_uid` = '{$v}' ");
	}

	error_frame_reload_nomsg() ; // 부모창 reload

?>