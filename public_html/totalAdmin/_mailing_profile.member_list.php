<?PHP
	$app_mode = "popup" ;
	include_once("inc.header.php");


	// 저장한 정보 불러오기 --> $app_profile 로 저장됨
	include_once("../upfiles/normal/mailing.profile.php");
	$ex_app_profile = array_filter(array_unique(explode("," , $app_profile)));

?>
<style>
#header {display:none!important}
.container .aside_first {display:none!important}
.container .aside_second {display:none!important}
.container {background-position:-60%!important}
.title_area, .open_close {display:none}
#footer {display:none}
</style>
<SCRIPT LANGUAGE="JavaScript">
 function selectAll() {
        if( $("input[name=allcheck]").is(":checked") == false ) { 
            $("input[name^=_chk]").attr("checked",false);
        }
        else {
            $("input[name^=_chk]").attr("checked",true);
        }
 }
 function type_select(_val){
	if(_val=="member"){
		$(".hide_feed").show();
	}else{
		$(".hide_feed").hide();
	}
 }
//-->
</SCRIPT>

				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=post action='<?=$_SERVER["PHP_SELF"]?>'>
					<input type=hidden name=mode value=search>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">구분</td>
								<td class="conts">
									<select name="pass_type" onchange="type_select(this.value);">
										<option value="member" <?=($pass_type=="member" ? "selected" : "")?>>회원검색</option>
										<option value="feed" <?=($pass_type=="feed" ? "selected" : "")?>>구독자검색</option>
									</select>
								</td>
								<td class="article">이메일</td>
								<td class="conts"><input type="text" name="pass_email" class="input_text" style="width:100px"  value='<?=$pass_email?>' /></td>
							</tr>
							<tr class="hide_feed" <?=($pass_type=="feed" ? "style='display:none;'" : "")?>>
								<td class="article">아이디</td>
								<td class="conts"><input type="text" name="pass_id" class="input_text" style="width:100px"  value='<?=$pass_id?>' /></td>
								<td class="article">성명</td>
								<td class="conts"><input type="text" name="pass_name" class="input_text" style="width:150px"  value='<?=$pass_name?>' /></td>
							</tr>
							<!-- 휴면회원 검색 적용 -->
							<tr class="hide_feed" <?=($pass_type=="feed" ? "style='display:none;'" : "")?>>
								<td class="article">휴면회원검색</td>
								<td class="conts" colspan=3><?=_InputSelect( "pass_dormancy" , array('Y','N') , $pass_dormancy , "" , array('휴면회원검색','정상회원') , "-선택-")?><?=_DescStr("1년 이상 접속(비로그인) 하지 않는 회원을 검색합니다. ")?></td>
							</tr>
							<!-- 휴면회원 검색 적용 -->
						</tbody> 
					</table>

					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="목록" >전체목록</a></span>
							<?}?>							
						</div>
					</div>
					</form>
				</div>
				<!-- // 검색영역 -->


<form name="frm2" action="_mailing_profile.popup_pro.php"  method=post>

				<!-- 리스트영역 -->
				<div class="content_section_inner">
				<div class="ctl_btn_area">
					<div class='btn_line_up_center'><span class='shop_btn_pack'><input type="submit" class='medium blue' value='입력'></span></div>					
				</div>

					<!-- 리스트 제어버튼영역
					<div class="top_btn_area">
						<span class="shop_btn_pack"><a href="#none" class="small white" title="전체선택" >전체선택</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="#none" class="small white" title="전체선택" >전체선택해제</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->


					<table class="list_TB" summary="리스트기본">
						<!-- <colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
						</colgroup> -->
						<thead>
							<tr>
								<th scope="col" class="colorset"><input type="checkbox" name="allcheck" onclick="selectAll();" /></th>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">이메일</th>
								<th scope="col" class="colorset">성명</th>
								<th scope="col" class="colorset">연락처</th>
							</tr>
						</thead> 
						<tbody> 


<?PHP
	if($mode == "search"){
		if($pass_type == "member"){
			######## 검색 체크
			$s_query = " where userType='B' and isRobot = 'N' and mailling != 'N' and passwd != 'deluser' ";
			if( sizeof($ex_app_profile) > 0 ) {
				$s_query .= " and email not in ('".implode("','" , $ex_app_profile)."') ";
			}
			if( $pass_email !="" ) { $s_query .= " and email like '%${pass_email}%' "; }
			if( $pass_name !="" ) { $s_query .= " and name like '%${pass_name}%' "; }
			if( $pass_id !="" ) { $s_query .= " and id like '%${pass_id}%' "; }

			// 휴면회원 검색 적용
			if( $pass_dormancy == "Y" ) { $s_query .= " AND recentdate < '". strtotime("- 1 year") ."' "; }
			else if( $pass_dormancy == "N" ) { $s_query .= " AND recentdate >= '". strtotime("- 1 year") ."' "; }
			// 휴면회원 검색 적용



			$res = _MQ_assoc(" select * , concat(htel1,'-',htel2,'-',htel3) as htel from odtMember $s_query ");
			
			if(sizeof($res) < 1) echo "<tr><td colspan=10 align=center>내용이 없습니다.</td></tr>";

			foreach($res as $k=>$row){

				$_num = $k + 1 ;

				echo "
									<tr>
										<td><input type='checkbox' name='_chk[{$k}]' value='".$row[email]."' class='cls_inid' /></td>
										<td>${_num}</td>
										<td>" . $row[email] . "</td>
										<td>".$row[name]."</td>
										<td>".$row[htel]."</td>
									</tr>
				";
			}
		}
		elseif($pass_type == "feed"){
			######## 검색 체크
			$s_query = " from feedTable where ft_emailsend != 'N' ";
			if( sizeof($ex_app_profile) > 0 ) {
				$s_query .= " and ft_email not in ('".implode("','" , $ex_app_profile)."') ";
			}
			if( $pass_email !="" ) { $s_query .= " and ft_email like '%${pass_email}%' "; }



			$res = _MQ_assoc(" select * $s_query ");
			
			if(sizeof($res) < 1) echo "<tr><td colspan=10 align=center>내용이 없습니다.</td></tr>";

			foreach($res as $k=>$row){

				$_num = $k + 1 ;

				echo "
									<tr>
										<td><input type='checkbox' name='_chk[{$k}]' value='".$row[ft_email]."' class='cls_inid' /></td>
										<td>${_num}</td>
										<td colspan='10'>" . $row[ft_email] . "</td>
									</tr>
				";
			}
		}
	}
?>

						</tbody> 
					</table>
				<div class="ctl_btn_area">
					<div class='btn_line_up_center'><span class='shop_btn_pack'><input type="submit" class='medium blue' value='입력'></span></div>					
				</div>

			</div>
</form>
