<?PHP

	$app_mode = "popup";
	include_once("inc.header.php");
	echo "<script type='text/javascript' src='_totadm_menu.js'></script>";


	//-----------------------------------------------------------------------------
	// 1차 코드목록
	//-----------------------------------------------------------------------------
	if ("code_list1" == $status)
	{
		echo "
		<div class='content_section_inner'>
			<table class='list_TB' summary='리스트기본'>
				<colgroup><col width='*'/><col width='120px'/></colgroup>
				<tbody>
		";
		$que  = " SELECT m2_code1, m2_name1 FROM m_adm_menu WHERE m2_code2 = '' ORDER BY m2_seq ";
		$res = _MQ_assoc($que);
		foreach($res as $k=>$r){
			echo "
				<tr>
					<td 
						onClick=\"
							parent.document.PUBLIC_FORM.m2_code1.value='" . $r[m2_code1] . "'; 
							parent.document.PUBLIC_FORM.m2_code2.value=''; 
							f_change_set();
							parent.list2.location.href='_totadm_menu.pro.php?status=code_list2&code1=" . $r[m2_code1] . "';
						\"
						class='app_tr'
						style='cursor:pointer; text-align:left;'
					>" . $r[m2_name1]."</td>
					<td>
						<div class='btn_line_up_center'>
							<span class='shop_btn_pack'><input type='button' name='' class='input_small gray' value='△' onClick=\"f_vup('" . $r[m2_code1] . "', '')\" alt='상위로 이동'></span>
							<span class='shop_btn_pack'><span class='blank_1'></span></span>
							<span class='shop_btn_pack'><input type='button' name='' class='input_small gray' value='▽' onClick=\"f_vdown('" . $r[m2_code1] . "', '')\" alt='하위로 이동'></span>
							<span class='shop_btn_pack'><span class='blank_1'></span></span>
							<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='수정' onClick=\"f_add('1', '" . $r[m2_code1] . "', '');\"></span>
						</div>
					</td>
				</tr>
			";
		}
		echo "</tbody></table></div>";
	}

	//-----------------------------------------------------------------------------
	// 2차 코드목록
	//-----------------------------------------------------------------------------
	if ("code_list2" == $status)
	{
		$code1 = trim($code1);
		echo "
		<div class='content_section_inner'>
			<table class='list_TB' summary='리스트기본'>
				<colgroup><col width='*'/><col width='120px'/></colgroup>
				<tbody>
		";

		$que  = " SELECT * FROM m_adm_menu WHERE m2_code1 = '" . $code1 . "' AND m2_code2 != '' ORDER BY m2_seq ";
		$res = _MQ_assoc($que);
		foreach($res as $k=>$r){
			echo "
				<tr>
					<td 
						onClick=\"
							parent.document.PUBLIC_FORM.m2_code2.value='" . $r[m2_code2] . "'; 
							f_change_set();
						\"
						class='app_tr'
						style='cursor:pointer; text-align:left;'
					>" . $r[m2_name2]."</td>
					<td>
						<div class='btn_line_up_center'>
							<span class='shop_btn_pack'><input type='button' name='' class='input_small gray' value='△' onClick=\"f_vup('" . $code1 . "', '" . $r[m2_code2] . "')\" alt='상위로 이동'></span>
							<span class='shop_btn_pack'><span class='blank_1'></span></span>
							<span class='shop_btn_pack'><input type='button' name='' class='input_small gray' value='▽' onClick=\"f_vdown('" . $code1 . "', '" . $r[m2_code2] . "')\" alt='하위로 이동'></span>
							<span class='shop_btn_pack'><span class='blank_1'></span></span>
							<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='수정' onClick=\"f_add('2', '" . $code1 . "', '" . $r[m2_code2] . "');\" ></span>
						</div>
					</td>
				</tr>
			";
		}
		echo "</tbody></table></div>";
	}


	//-----------------------------------------------------------------------------
	// 메뉴 추가
	//-----------------------------------------------------------------------------
	if ("menu_add" == $status)
	{
		$m2_code1 = trim($m2_code1);
		$m2_code2 = trim($m2_code2);

		$Query  = " SELECT * FROM m_adm_menu WHERE m2_code1 = '$m2_code1'   ";
		if ($m2_code2) { $Query .= " AND m2_code2 = '$m2_code2' ";  }
		//echo $Query;
		$Record = _MQ($Query);
		$m2_name1 = trim($Record[m2_name1]);
		$m2_name2 = trim($Record[m2_name2]);

		echo "
<form name='PUBLIC_FORM' method='post' action='" . $_SERVER[PHP_SELF] . "'>
<input type='hidden' name='status' value='$status' />
<input type='hidden' name='KBN'    value='$KBN' />
					<div class='form_box_area'>
						<table class='form_TB' summary='검색항목'>
								<colgroup>
									<col width='120px'/><col width='*'/>
								</colgroup>
								<tbody>
		";

		if (1 == $KBN)
		{
			echo "
									<tr>
										<td class='article'>1차 메뉴</td>
										<td class='conts'><input type=text name=m2_name1 value=\"" . $m2_name1 . "\" size=30 maxlength=50 class=input_text onfocus='gf_GetFocus(this);' onblur='gf_LostFocus(this);' ><input type='hidden' name='m2_code1' value='" . $m2_code1 . "' /></td>
									</tr>
			";
		}
		else if (2 == $KBN)
		{
			echo "
									<tr>
										<td class='article'>1차 메뉴</td>
										<td class='conts'><input type=text name=m2_name1 value=\"" . $m2_name1 . "\" size=30 class=input_text readonly ><input type='hidden' name='m2_code1' value='" . $m2_code1 . "' /></td>
									</tr>
									<tr>
										<td class='article'>2차 메뉴</td>
										<td class='conts'><input type=text name=m2_name2 value=\"" . $m2_name2 . "\" size=30 maxlength=50 class=input_text onfocus='gf_GetFocus(this);' onblur='gf_LostFocus(this);' ><input type='hidden' name='m2_code2' value='" . $m2_code2 . "' /></td>
									</tr>
			";
		}

		echo "
								</tbody> 
							</table>
					</div>

					<!-- 버튼영역 -->
					<div class='bottom_btn_area'>
						<div class='btn_line_up_center'>
							<span class='shop_btn_pack'>
								<input type='button' name='' class='input_large red' value='저장' onClick='f_add_Save()'>
								<input type='button' name='' class='input_large blue' value='삭제' onClick='f_add_Del()'>
								<input type='button' name='' class='input_large gray' value='닫기' onClick='self.close();'>
							</span>
						</div>
					</div>
					<!-- 버튼영역 -->
</form>
		";
	}

	//-----------------------------------------------------------------------------
	// 메뉴추가 처리
	//-----------------------------------------------------------------------------
	if ("menu_add_tran" == $status)
	{
		$m2_code1 = trim($m2_code1);
		$m2_code2 = trim($m2_code2);

		$m2_name1 = trim($m2_name1);
		$m2_name2 = trim($m2_name2);

		if (2 == $KBN && $m2_code1 && $m2_code2)
		{
			$Query  = " UPDATE m_adm_menu SET m2_name1 = '$m2_name1', m2_name2 = '$m2_name2' WHERE m2_code1 = '$m2_code1' AND m2_code2 = '$m2_code2'    ";
			_MQ_noreturn($Query);
		}
		else if (1 == $KBN && $m2_code1)
		{
			$Query  = " UPDATE m_adm_menu SET m2_name1 = '$m2_name1' WHERE m2_code1 = '$m2_code1' ";
			_MQ_noreturn($Query);
		}
		else
		{
			if (1 == $KBN)
			{
				$Query  = " SELECT MAX(m2_code1) as maxm2_code FROM m_adm_menu WHERE m2_code1 != '' AND m2_code2 = '' ";
				$Record = _MQ($Query);
				$iMax   = $Record[maxm2_code] + 1;
				$m2_code1 = sprintf("%02d", $iMax);

				$Query  = " SELECT MAX(m2_seq) as maxseq FROM m_adm_menu WHERE m2_code1 != '' AND m2_code2 = '' ";
				$Record = _MQ($Query);
				$jMax   = $Record[maxseq] + 1;
				$m2_seq = sprintf("%02d", $jMax);

				if (20 == $m2_code1)
				{
					echo "
					<script>
						alert('1차 메뉴는 20개 이상 등록 불가 합니다');
						history.back();
					</script>";
					exit;
				}
			}
			else if (2 == $KBN)
			{
				$Query  = " SELECT MAX(m2_code2) as maxm2_code FROM m_adm_menu WHERE m2_code1 = '$m2_code1' AND m2_code2 != ''    ";
				$Record = _MQ($Query);
				$iMax   = $Record[maxm2_code] + 1;
				$m2_code2 = sprintf("%02d", $iMax);


				$Query  = " SELECT MAX(m2_seq) as maxseq FROM m_adm_menu WHERE  m2_code1 = '$m2_code1' AND m2_code2 != ''     ";
				$Record = _MQ($Query);
				$jMax   = $Record[maxseq] + 1;
				$m2_seq = sprintf("%02d", $jMax);
			}

			// 메뉴추가 ///////////////////////////////////////////////////////////
			$Query  = " INSERT INTO m_adm_menu (m2_code1, m2_code2, m2_name1, m2_name2, m2_seq) VALUES ('$m2_code1', '$m2_code2', '$m2_name1', '$m2_name2', '$m2_seq')    ";
			_MQ_noreturn($Query);

			/*
			$sQuery  = " SELECT * FROM odtAdmin ORDER BY id  ";
			$sResult = _MQ_assoc($sQuery);
			foreach ( $sResult as $k=> $sRecord )
			{
				$Query  = " INSERT INTO m_menu_set (m15_id, m15_code1, m15_code2, m15_vkbn, m15_skbn, m15_dkbn, m15_udate, m15_uid) VALUES ('$sRecord[id]', '$m2_code1', '$m2_code2', 'Y', 'Y', 'Y', sysdate(), '".$row_admin[id]."')  ";
				_MQ_noreturn($Query);
			}
			*/

			echo "
			<script>
				if      (1 == $KBN) { opener.list1.location.reload(true); opener.list2.location.href='_totadm_menu.pro.php';   }
				else if (2 == $KBN) { opener.list2.location.reload(true);   }
				self.close();
			</script>";
			exit;
		}

		echo "
		<script>
			opener.location.reload(true);
			self.close();
		</script>";
		exit;
	}

	//-----------------------------------------------------------------------------
	// 메뉴삭제 처리
	//-----------------------------------------------------------------------------
	if ("menu_del_tran" == $status)
	{
		$m2_code1 = trim($m2_code1);
		$m2_code2 = trim($m2_code2);

		$Query  = " DELETE FROM m_adm_menu WHERE m2_code1 = '$m2_code1' ";
		if ($m2_code2) { $Query .= " AND m2_code2 = '$m2_code2' ";  }
		//echo $Query;exit;
		_MQ_noreturn($Query);

		/*
		// 메뉴삭제후에는 관리자 메뉴설정사항도 삭제 처리 /////////////////////////
		$Query  = " DELETE FROM m_menu_set WHERE m15_code1 = '$m2_code1'    ";
		if ($m2_code2) { $Query .= " AND m15_code2 = '$m2_code2' ";     }
		_MQ_noreturn($Query);
		*/

		echo "
		<script>
			opener.location.reload(true);
		   self.close();
		</script>";
		exit;
	}



	//-----------------------------------------------------------------------------
	// 상위로 순서변경
	//-----------------------------------------------------------------------------
	if ("view_up" == $status)
	{
		if ($code1 && $code2)
		{
			// 2차 메뉴 상위로 순서 변경 //////////////////////////////////////////
			$Query  = " SELECT MIN(m2_seq) as minseq FROM m_adm_menu WHERE m2_code1 = '$code1' AND m2_code2 != '' ";
			$Record = _MQ($Query);
			$minseq = $Record[minseq];

			$Query  = " SELECT m2_seq FROM m_adm_menu WHERE m2_code1 = '$code1' AND m2_code2 = '$code2' ";
			$Record = _MQ($Query);
			$m2_seq = $Record[m2_seq];

			if ($minseq == $m2_seq)
			{
				echo "<script>alert('더이상 상위로 변경하실수 없습니다');</script>";
			}
			else
			{
				// 바로 상위에 있는 순서값을 +1 시키고 해당 레코드의 순서값을 -1 시킨다 
				$Query  = " SELECT m2_code2 FROM m_adm_menu WHERE m2_code1 = '$code1' AND m2_code2 != '' AND m2_seq < '$m2_seq' ORDER BY m2_seq desc limit 1 ";
				$Record = _MQ($Query);
				$jcode2 = $Record[m2_code2];

				$Query  = " UPDATE m_adm_menu SET m2_seq = m2_seq + 1 WHERE m2_code1 = '$code1' AND m2_code2 = '$jcode2' ";
				_MQ_noreturn($Query);

				$Query  = " UPDATE m_adm_menu SET m2_seq = m2_seq - 1 WHERE m2_code1 = '$code1' AND m2_code2 = '$code2' ";
				_MQ_noreturn($Query);
			}

			echo "
			<script>
				parent.list2.location.reload(true);
			</script>";
		}
		else
		{
			// 1차 메뉴 상위로 순서 변경 //////////////////////////////////////////
			$Query  = " SELECT MIN(m2_seq) as minseq FROM m_adm_menu WHERE m2_code1 != '' AND m2_code2 = '' ";
			$Record = _MQ($Query);
			$minseq = $Record[minseq];

			$Query  = " SELECT m2_seq FROM m_adm_menu WHERE m2_code1 = '$code1' AND m2_code2 = ''  ";
			$Record = _MQ($Query);
			$m2_seq = $Record[m2_seq];

			if ($minseq == $m2_seq)
			{
				echo "<script>alert('더이상 상위로 변경하실수 없습니다');</script>";
			}
			else
			{
				// 바로 상위에 있는 순서값을 +1 시키고 해당 레코드의 순서값을 -1 시킨다 
				$Query  = " SELECT m2_code1 FROM m_adm_menu WHERE m2_code1 != '' AND m2_code2 = '' AND m2_seq < '$m2_seq' ORDER BY m2_seq desc limit 1 ";
				$Record = _MQ($Query);
				$jcode1 = $Record[m2_code1];

				$Query  = " UPDATE m_adm_menu SET m2_seq = m2_seq + 1 WHERE m2_code1 = '$jcode1' AND m2_code2 = ''  ";
				_MQ_noreturn($Query);

				$Query  = " UPDATE m_adm_menu SET m2_seq = m2_seq - 1 WHERE m2_code1 = '$code1' AND m2_code2 = ''   ";
				_MQ_noreturn($Query);
			}

			echo "
			<script>
				parent.list1.location.reload(true);
			</script>";
		}

		exit;
	}

	//-----------------------------------------------------------------------------
	// 하위로 순서변경
	//-----------------------------------------------------------------------------
	if ("view_down" == $status)
	{
		if ($code1 && $code2)
		{
			// 2차 메뉴 하위로 순서 변경 //////////////////////////////////////////
			$Query  = " SELECT MAX(m2_seq) as maxseq FROM m_adm_menu WHERE m2_code1 = '$code1' AND m2_code2 != '' ";
			$Record = _MQ($Query);
			$maxseq = $Record[maxseq];

			$Query  = " SELECT m2_seq FROM m_adm_menu WHERE m2_code1 = '$code1' AND m2_code2 = '$code2' ";
			$Record = _MQ($Query);
			$m2_seq = $Record[m2_seq];

			if ($maxseq == $m2_seq)
			{
				echo "<script>alert('더이상 하위로 변경하실수 없습니다');</script>";
			}
			else
			{
				// 바로 하위로 있는 순서값을 -1 시키고 해당 레코드의 순서값을 +1 시킨다 
				$Query  = " SELECT m2_code2 FROM m_adm_menu WHERE m2_code1 = '$code1' AND m2_code2 != '' AND m2_seq > '$m2_seq' ORDER BY m2_seq asc limit 1";
				$Record = _MQ($Query);
				$jcode2 = $Record[m2_code2];

				$Query  = " UPDATE m_adm_menu SET m2_seq = m2_seq - 1 WHERE m2_code1 = '$code1' AND m2_code2 = '$jcode2'    ";
				_MQ_noreturn($Query);

				$Query  = " UPDATE m_adm_menu SET m2_seq = m2_seq + 1 WHERE m2_code1 = '$code1' AND m2_code2 = '$code2'     ";
				_MQ_noreturn($Query);
			}

			echo "
			<script>
				parent.list2.location.reload(true);
			</script>";
		}
		else
		{
			// 1차 메뉴 하위로 순서 변경 //////////////////////////////////////////
			$Query  = " SELECT MAX(m2_seq) as maxseq FROM m_adm_menu WHERE m2_code1 != '' AND m2_code2 = ''  ";
			$Record = _MQ($Query);
			$maxseq = $Record[maxseq];

			$Query  = " SELECT m2_seq FROM m_adm_menu WHERE m2_code1 = '$code1' AND m2_code2 = ''  ";
			$Record = _MQ($Query);
			$m2_seq = $Record[m2_seq];

			if ($maxseq == $m2_seq)
			{
				echo "<script>alert('더이상 하위로 변경하실수 없습니다');</script>";
			}
			else
			{
				// 바로 하위로 있는 순서값을 -1 시키고 해당 레코드의 순서값을 +1 시킨다 
				$Query  = " SELECT m2_code1 FROM m_adm_menu WHERE m2_code1 != '' AND m2_code2 = '' AND m2_seq > '$m2_seq' ORDER BY m2_seq asc limit 1 ";
				$Record = _MQ($Query);
				$jcode1 = $Record[m2_code1];

				$Query  = " UPDATE m_adm_menu SET m2_seq = m2_seq - 1 WHERE m2_code1 = '$jcode1' AND m2_code2 = ''  ";
				_MQ_noreturn($Query);

				$Query  = " UPDATE m_adm_menu SET m2_seq = m2_seq + 1 WHERE m2_code1 = '$code1' AND m2_code2 = ''   ";
				_MQ_noreturn($Query);
			}

			echo "
			<script>
				parent.list1.location.reload(true);
			</script>";
		}

		exit;
	}


	//-----------------------------------------------------------------------------
	// 메뉴정보 추출해서 넘긴다
	//-----------------------------------------------------------------------------
	if ("menu_set" == $status)
	{
		$Query  = " SELECT * FROM m_adm_menu WHERE m2_code1 = '$code1' AND m2_code2 = '$code2'  ";
		//echo $Query;
		$Record = _MQ($Query);
		$m2_link = trim($Record[m2_link]);
		$m2_vkbn = trim($Record[m2_vkbn]);

		echo "
		<script>
			parent.document.PUBLIC_FORM.m2_link.value = '$m2_link';
			
			if ('y' == '$m2_vkbn') { parent.document.PUBLIC_FORM.m2_vkbn[0].checked = true;  }
			else                   { parent.document.PUBLIC_FORM.m2_vkbn[1].checked = true;  }

		</script>
		";

		exit;
	}

	//-----------------------------------------------------------------------------
	// 메뉴정보 수정
	//-----------------------------------------------------------------------------
	if ("menu_save" == $status)
	{
		$Query  = " UPDATE m_adm_menu SET m2_vkbn = '$m2_vkbn', m2_link = '$m2_link' WHERE m2_code1 = '$code1' AND m2_code2 = '$code2'  ";
		_MQ_noreturn($Query);

		echo "<script>alert('저장 되었습니다');</script>";
		exit;
	}

?>