<?PHP
	include_once("inc.header.php");

	$today			= date('Y-m-d');
	$seven_days = date('Y-m-d',strtotime("-7 days"));
	$one_month_time	= strtotime("-1 month");
	$one_month	= date('Y-m-d', $one_month_time );

	$graph_speed = 2000;
?>

<div class="bar_num">0</div>
			<!-- 리스트영역 -->
			<div class="content_section_inner">

				<!-- SMS 에러로그 *5 -->
				<?php
				$SMSUser = onedaynet_sms_user();
				if($SMSUser['code'] == 'U00') {
					insert_sms_send_log(array());
					$SMSLogInterval = 3;
					$SMSErrorLog = _MQ_assoc("select * from odtSMSLog where date(`rdate`) between date_format(date_add(now(), interval -{$SMSLogInterval} day), '%Y-%m-%d') and curdate() order by idx desc limit 0, 5");
					if(count($SMSErrorLog) > 0) {
				?>
				<div class="main_box_area">
					<!-- 내부 그룹타이틀 -->
					<div class="group_title">
						<span class="icon"></span><span class="title">SMS 에러로그</span>
						<span class="btn_area">
							<span class="shop_btn_pack"><a href="_sms.log.php" class="small gray" title="로그상세 보기" >로그상세 보기</a></span>
						</span>
					</div>
					<!-- 내부 그룹타이틀 -->

					<!-- 데이터 출력 -->
					<table class="last_TB"></table>
					<table class="last_TB" summary="게시판현황">
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<thead>
							<tr>
								<th>발생일</th>
								<th>로그 메시지</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($SMSErrorLog as $k=>$v) { ?>
							<tr>
								<td><?php echo $v['rdate']; ?></td>
								<td style="text-align:left;"><span style="color: #009cff; font-weight:800;"><?php echo $v['msg']; ?></span></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<!-- 데이터 출력 -->
				</div>
				<?php }} ?>
				<!-- // SMS 에러로그 *5 -->


                 <?php
                    // 2016-05-24 ::: 매 2년마다 수신동의 설정 ----- 수신동의한지 2년이 넘은 회원 체크하여 - odt2yearOptLog 에 데이터 등록
                    $_2year_opt_file_name = $_SERVER["DOCUMENT_ROOT"] . "/include/addons/2yearOpt/main.2year_opt.php";
                    if(@file_exists($_2year_opt_file_name)) { include_once($_2year_opt_file_name); }
                ?>


				<?
				// 주문건수 추출
				$order_cnt_month = _MQ("select count(*) as val from odtOrder where paystatus='Y' and canceled='N' and left(orderdate,10) >= '".date('Y-m-d',strtotime("-30 day"))."'  ");
				$order_cnt_week = _MQ("select count(*) as val from odtOrder where paystatus='Y' and canceled='N' and left(orderdate,10) >= '".date('Y-m-d',strtotime("-7 day"))."'  ");
				$order_cnt_day = _MQ("select count(*) as val from odtOrder where paystatus='Y' and canceled='N' and left(orderdate,10) = CURDATE() ");

				// 주문건수 평균
				$order_aver_month = $order_cnt_month[val] ;/// 30;
				$order_aver_week = $order_cnt_week[val] ;/// 7;
				$order_aver_day = $order_cnt_day[val] ;/// 1;

				// max값 추출
				$order_aver_max = max($order_aver_month,$order_aver_week,$order_aver_day);

				// 주문건수 그래프 %
				$order_per_month = @($order_aver_month / $order_aver_max)*100;
				$order_per_week = @($order_aver_week / $order_aver_max)*100;
				$order_per_day	= @($order_aver_day / $order_aver_max)*100;

				$graph_script .= "$('#order_per_month').animate({height: '".($order_per_month ? $order_per_month : 2)."%'},".$graph_speed.");\n";
				$graph_script .= "$('#order_per_week').animate({height: '".($order_per_week ? $order_per_week : 2)."%'},".$graph_speed.");\n";
				$graph_script .= "$('#order_per_day').animate({height: '".($order_per_day ? $order_per_day : 2)."%'},".$graph_speed.");\n";


				// 매출 총액 추출
				$sales_sum_month = _MQ("select sum(tPrice) as val from odtOrder where paystatus='Y' and canceled='N' and left(orderdate,10) >= '".date('Y-m-d',strtotime("-30 day"))."'  ");
				$sales_sum_week = _MQ("select sum(tPrice) as val from odtOrder where paystatus='Y' and canceled='N' and left(orderdate,10) >= '".date('Y-m-d',strtotime("-7 day"))."'  ");
				$sales_sum_day = _MQ("select sum(tPrice) as val from odtOrder where paystatus='Y' and canceled='N' and left(orderdate,10) = CURDATE() ");

				// 매출총액 평균
				$sales_aver_month = $sales_sum_month[val] ;/// 30;
				$sales_aver_week = $sales_sum_week[val] ;/// 7;
				$sales_aver_day = $sales_sum_day[val] ;/// 1;

				// max값 추출
				$sales_aver_max = max($sales_aver_month,$sales_aver_week,$sales_aver_day);

				// 매출총액 그래프 %
				$sales_per_month = @($sales_aver_month / $sales_aver_max)*100;
				$sales_per_week = @($sales_aver_week / $sales_aver_max)*100;
				$sales_per_day	= @($sales_aver_day / $sales_aver_max)*100;

				$graph_script .= "$('#sales_per_month').animate({height: '".($sales_per_month ? $sales_per_month : 2)."%'},".$graph_speed.");\n";
				$graph_script .= "$('#sales_per_week').animate({height: '".($sales_per_week ? $sales_per_week : 2)."%'},".$graph_speed.");\n";
				$graph_script .= "$('#sales_per_day').animate({height: '".($sales_per_day ? $sales_per_day : 2)."%'},".$graph_speed.");\n";


				// 가입현황 추출
				$join_sum_month = _MQ("select count(*) as val from odtMember where userType='B' and isRobot = 'N' and signdate >= ".strtotime("-30 day")." ");
				$join_sum_week = _MQ("select count(*) as val from odtMember where  userType='B' and isRobot = 'N' and signdate >= ". strtotime("-7 day")." ");
				$join_sum_day = _MQ("select count(*) as val from odtMember where  userType='B' and isRobot = 'N' and  signdate >= ". time()." ");

				// 가입현황 평균
				$join_aver_month = $join_sum_month[val] ;// / 30;
				$join_aver_week = $join_sum_week[val] ;// / 7;
				$join_aver_day = $join_sum_day[val] ; /// 1;

				// max값 추출
				$join_aver_max = max($join_aver_month,$join_aver_week,$join_aver_day);

				// 가입현황 그래프 %
				$join_per_month = @($join_aver_month / $join_aver_max)*100;
				$join_per_week = @($join_aver_week / $join_aver_max)*100;
				$join_per_day	= @($join_aver_day / $join_aver_max)*100;

				$graph_script .= "$('#join_per_month').animate({height: '".($join_per_month ? $join_per_month : 2)."%'},".$graph_speed.");\n";
				$graph_script .= "$('#join_per_week').animate({height: '".($join_per_week ? $join_per_week : 2)."%'},".$graph_speed.");\n";
				$graph_script .= "$('#join_per_day').animate({height: '".($join_per_day ? $join_per_day : 2)."%'},".$graph_speed.");\n";

				// 방문현황 추출
				$counter_sum_month = _MQ("select sum(Visit_Num) as val from odtCounterData where TIMESTAMP(concat(Year,'-',Month,'-',Day)) >= '".date('Y-m-d',strtotime("-29 day"))."' and TIMESTAMP(concat(Year,'-',Month,'-',Day)) <=  '".date('Y-m-d')."'");
				$counter_sum_week = _MQ("select sum(Visit_Num) as val from odtCounterData where TIMESTAMP(concat(Year,'-',Month,'-',Day)) >= '".date('Y-m-d',strtotime("-6 day"))."' and TIMESTAMP(concat(Year,'-',Month,'-',Day)) <= '".date('Y-m-d')."'");
				$counter_sum_day = _MQ("select sum(Visit_Num) as val from odtCounterData where TIMESTAMP(concat(Year,'-',Month,'-',Day)) = '".date('Y-m-d')."'");

				// 가입현황 평균
				$counter_aver_month = $counter_sum_month[val] ;/// 30;
				$counter_aver_week = $counter_sum_week[val] ;/// 7;
				$counter_aver_day = $counter_sum_day[val] ;/// 1;

				// max값 추출
				$counter_aver_max = max($counter_aver_month,$counter_aver_week,$counter_aver_day);

				// 가입현황 그래프 %
				$counter_per_month = @($counter_aver_month / $counter_aver_max)*100;
				$counter_per_week = @($counter_aver_week / $counter_aver_max)*100;
				$counter_per_day	= @($counter_aver_day / $counter_aver_max)*100;

				$graph_script .= "$('#counter_per_month').animate({height: '".($counter_per_month ? $counter_per_month : 2)."%'},".$graph_speed.");\n";
				$graph_script .= "$('#counter_per_week').animate({height: '".($counter_per_week? $counter_per_week : 2)."%'},".$graph_speed.");\n";
				$graph_script .= "$('#counter_per_day').animate({height: '".($counter_per_day ? $counter_per_day : 2)."%'},".$graph_speed.");\n";

				?>

				<!-- 메인그래프 2013-07-30  -->
				<div class="main_total_graph">

					<!-- 항목설명 -->
					<div class="article">
						<ul>
							<li><span class="bar_black"></span>최근한달</li>
							<li><span class="bar_blue"></span>최근일주일</li>
							<li><span class="bar_red"></span>오늘현재</li>
						</ul>
					</div>
					
					

					<!-- 그래프표시 -->
					<div class="graph_box">
						
						<div class="bar_set bar_set1">
							<ul>
								<li><span class="bar_black" style="height:0%;" id="order_per_month" cnt="<?=$order_cnt_month[val]?>건"></span></li>
								<li><span class="bar_blue" style="height:0%;" id="order_per_week" cnt="<?=$order_cnt_week[val]?>건"></span></li>
								<li><span class="bar_red" style="height:0%;" id="order_per_day" cnt="<?=$order_cnt_day[val]?>건"></span></li>
							</ul>							
						</div>

						<div class="bar_set bar_set2">
							<ul>
								<li><span class="bar_black" style="height:0%;" id="sales_per_month" cnt="<?=number_format($sales_sum_month[val])?>원"></span></li>
								<li><span class="bar_blue" style="height:0%;" id="sales_per_week" cnt="<?=number_format($sales_sum_week[val])?>원"></span></li>
								<li><span class="bar_red" style="height:0%;" id="sales_per_day" cnt="<?=number_format($sales_sum_day[val])?>원"></span></li>
							</ul>							
						</div>

						<div class="bar_set bar_set3">
							<ul>
								<li><span class="bar_black" style="height:0%;" id="join_per_month" cnt="<?=number_format($join_sum_month[val])?>명"></span></li>
								<li><span class="bar_blue" style="height:0%;" id="join_per_week" cnt="<?=number_format($join_sum_week[val])?>명"></span></li>
								<li><span class="bar_red" style="height:0%;" id="join_per_day" cnt="<?=number_format($join_sum_day[val])?>명"></span></li>
							</ul>							
						</div>

						<div class="bar_set bar_set4">
							<ul>
								<li><span class="bar_black" style="height:0%;" id="counter_per_month" cnt="<?=number_format($counter_sum_month[val])?>회"></span></li>
								<li><span class="bar_blue" style="height:0%;" id="counter_per_week" cnt="<?=number_format($counter_sum_week[val])?>회"></span></li>
								<li><span class="bar_red" style="height:0%;" id="counter_per_day" cnt="<?=number_format($counter_sum_day[val])?>회"></span></li>
							</ul>							
						</div>

					</div>

					<!-- 항목표시 -->
					<div class="graph_article">
						<ul>
							<li>주문현황</li>
							<li>매출현황</li>
							<li>가입현황</li>
							<li>방문현황</li>
						</ul>
					</div>

				</div>
				<!-- // 메인그래프 -->



				<?php // LDD018 { ?>
				<div class="main_box_area">
					<!-- 내부 그룹타이틀 -->
					<div class="group_title">
						<span class="icon"></span><span class="title">예약배송현황</span>
						<span class="btn_area"><span class="shop_btn_pack"><a href="_order2.reserve_list.php" class="small gray" title="더보기" >예약배송현황 보기</a></span></span>
					</div>
					<!-- 내부 그룹타이틀 -->
					<table class="last_TB">
						<thead>
							<tr>
								<th>내일<br><strong style="color:#ff0000">(~ <?php echo date('Y-m-d', strtotime('+1day', time())); ?>)</strong></th>
								<th>3일간<br>(~ <?php echo date('Y-m-d', strtotime('+3day', time())); ?>)</th>
								<th>7일간<br>(~ <?php echo date('Y-m-d', strtotime('+3day', time())); ?>)</th>
								<th>15일간<br>(~ <?php echo date('Y-m-d', strtotime('+15day', time())); ?>)</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="height:30px; color:#ff0000; font-weight:bold;"><?php echo number_format(reserve_order(1)); ?>건</td>
								<td style="height:30px; text-align:center;"><b><?php echo number_format(reserve_order(3)); ?></b>건</td>
								<td style="height:30px; text-align:center;"><b><?php echo number_format(reserve_order(7)); ?></b>건</td>
								<td style="height:30px; text-align:center;"><b><?php echo number_format(reserve_order(15)); ?></b>건</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php // } LDD018 ?>


			<!-- 메인추출 -->
				<div class="main_box_area">


<?php
			// 오늘
			$or_today = _MQ_assoc("select paystatus , canceled , IF(orderstatus_step = '결제확인' AND paystatus2 = 'Y' AND order_type != 'coupon' ,1,0) AS del_stats, orderstatus_step  , IFNULL(SUM(tPrice),0) as tPrice , count(*) as cnt  from odtOrder where  DATE(orderdate) = '".$today."' group by paystatus , canceled , orderstatus_step ");
			foreach($or_today as $k => $v) {
				
				$order_status['today'][$v['orderstatus_step']]+= $v['cnt']; 
				
				# -- 발송대기인 상품이 있다면
				if($v['del_stats'] == 1) { 
					$order_status['today']['발송대기']+= $v['cnt']; 
				}
				# -- 발송대기인 상품이 있다면


				if($v['paystatus'] == 'Y' && $v['canceled'] != 'Y'){
					$order_status['today']["총매출액"] += $v['tPrice']; 
				}
			}

			// 일주일
			$or_today = _MQ_assoc("select paystatus , canceled , IF(orderstatus_step = '결제확인' AND paystatus2 = 'Y' AND order_type != 'coupon' ,1,0) AS del_stats, orderstatus_step ,  IFNULL(SUM(tPrice),0) as tPrice , count(*) as cnt  from odtOrder where  DATE(orderdate) >= '".$seven_days."' group by paystatus , canceled ,orderstatus_step  ");
			foreach($or_today as $k => $v) {
				$order_status['seven_days'][$v['orderstatus_step']]+= $v['cnt']; 

				# -- 발송대기인 상품이 있다면
				if($v['del_stats'] == 1) { 
					$order_status['seven_days']['발송대기']+= $v['cnt']; 
				}
				# -- 발송대기인 상품이 있다면

				
				if($v['paystatus'] == 'Y' && $v['canceled'] != 'Y'){

					$order_status['seven_days']["총매출액"] += $v['tPrice'];
				}
			}
	
			// 한달
			$or_today = _MQ_assoc("select paystatus , canceled , IF(orderstatus_step = '결제확인' AND paystatus2 = 'Y' AND order_type != 'coupon' ,1,0) AS del_stats, orderstatus_step , IFNULL(SUM(tPrice),0) as tPrice , count(*) as cnt  from odtOrder where  DATE(orderdate) >= '".$one_month."' group by paystatus , canceled , orderstatus_step");
			foreach($or_today as $k => $v) {
				$order_status['one_month'][$v['orderstatus_step']]+= $v['cnt']; 

				# -- 발송대기인 상품이 있다면
				if($v['del_stats'] == 1) { 
					$order_status['one_month']['발송대기']+= $v['cnt']; 
				}
				# -- 발송대기인 상품이 있다면

				if($v['paystatus'] == 'Y' && $v['canceled'] != 'Y'){
					$order_status['one_month']["총매출액"] += $v['tPrice']; 
				}
			}
?>


					<!-- 내부 그룹타이틀 -->
					<div class="group_title">
						<span class="icon"></span><span class="title">쇼핑몰현황</span>
						<span class="btn_area"><span class="shop_btn_pack"><a href="_order.list.php?menu_idx=21" class="small gray" title="더보기" >주문관리 보기</a></span></span>
					</div>
					<!-- 내부 그룹타이틀 -->

					<table class="last_TB" summary="쇼핑몰현황">
						<colgroup>
							<col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/>
						</colgroup>
						<thead>
							<th>기간</th>
							<?foreach(array_keys($arr_o_status) as $k=>$v){ echo "<th>". $v ."</th>"; }?>
							<th>총매출액</th>
						</thead>
						<tbody> 
							<tr>
								<td>오늘현재</td>
								<?foreach(array_keys($arr_o_status) as $k=>$v){ echo "<td><B>". number_format($order_status['today'][$v]) ."</B> 건</td>"; }?>
								<td><b class="hit"><?=number_format($order_status['today']["총매출액"])?></b> 원</td>
							</tr>
							<tr>
								<td>최근일주일</td>
								<?foreach(array_keys($arr_o_status) as $k=>$v){ echo "<td><B>". number_format($order_status['seven_days'][$v]) ."</B> 건</td>"; }?>
								<td><b class="hit"><?=number_format($order_status['seven_days']["총매출액"])?></b> 원</td>
							</tr>
							<tr>
								<td>최근한달</td>
								<?foreach(array_keys($arr_o_status) as $k=>$v){ echo "<td><B>". number_format($order_status['one_month'][$v]) ."</B> 건</td>"; }?>
								<td><b class="hit"><?=number_format($order_status['one_month']["총매출액"])?></b> 원</td>
							</tr>
						</tbody> 
					</table>

				</div>
				<!-- // 메인추출 -->
				


				<!-- 메인추출 -->
				<div class="main_box_area">

						<!-- 내부 그룹타이틀 -->
						<div class="group_title">
							<span class="icon"></span><span class="title">회원현황</span>
							<span class="btn_area">
								<span class="shop_btn_pack" style="padding-right:5px;"><a href="_member.list.php" class="small gray" title="회원관리 보기" >회원관리 보기</a></span>
								<span class="shop_btn_pack"><a href="_membersleep.list.php" class="small gray" title="휴면전화회원 보기" >휴면전화회원 보기</a></span><?//JJC002 ?>
							</span>
						</div>
						<!-- 내부 그룹타이틀 -->
<?PHP

	$r = _MQ_assoc("select left(FROM_UNIXTIME(signdate),10) as m_signdate , left(FROM_UNIXTIME(modifydate),10) as m_recentdate , left(deldate,10) as m_deldate, name from odtMember where signdate >= '".$one_month_time."' || modifydate >= '".$one_month_time."' || left(deldate,10) >= '".$one_month."' ");
	foreach($r as $k => $v) {

		// 오늘
		if($v[m_signdate]>=$today) $member_status['today']["join_member"]++; 
		if($v[m_recentdate]>=$today) $member_status['today']["visit_member"]++; 
		if($v[m_deldate]>=$today && $v[name] == "탈퇴한회원") $member_status['today']["out_member"]++; 

		// 최근일주일
		if($v[m_signdate]>=$seven_days) $member_status['seven_days']["join_member"]++; 
		if($v[m_recentdate]>=$seven_days) $member_status['seven_days']["visit_member"]++; 
		if($v[m_deldate]>=$seven_days && $v[name] == "탈퇴한회원") $member_status['seven_days']["out_member"]++; 


		// 최근한달
		if($v[m_signdate]>=$one_month) $member_status['one_month']["join_member"]++; 
		if($v[m_recentdate]>=$one_month) $member_status['one_month']["visit_member"]++; 
		if($v[m_deldate]>=$one_month && $v[name] == "탈퇴한회원") $member_status['one_month']["out_member"]++; 

	}

	//JJC002 
	$r = _MQ_assoc("select left(ms_rdate,10) as msrdate, name , ms_sendchk from odtMemberSleep where left(ms_rdate,10) >= '".$one_month."' ");
	foreach($r as $k => $v) {
		if($v['msrdate']>=$today) $member_status['today']["sleep_member"]++; 
		if($v['msrdate']>=$seven_days) $member_status['seven_days']["sleep_member"]++; 
		if($v['msrdate']>=$one_month) $member_status['one_month']["sleep_member"]++; 

		if($v['msrdate']>=$today && $v['ms_sendchk'] == "N") $member_status['today']["nosend_member"]++; 
		if($v['msrdate']>=$seven_days && $v['ms_sendchk'] == "N") $member_status['seven_days']["nosend_member"]++; 
		if($v['msrdate']>=$one_month && $v['ms_sendchk'] == "N") $member_status['one_month']["nosend_member"]++; 
	}

?>
						
						<table class="last_TB" summary="회원현황">
							<colgroup>
								<col width="5%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/>
							</colgroup>
							<thead>
								<th>기간</th>
								<th>신규가입회원</th>
								<th>방문회원</th>
								<th>탈퇴회원</th>
								<th>휴면전환회원 (메일미발송)</th>
							</thead>
							<tbody> 
								<tr>
									<td>오늘현재</td>
									<td><b><?=number_format($member_status['today']["join_member"])?></b> 명</td>
									<td><b><?=number_format($member_status['today']["visit_member"])?></b> 명</td>
									<td><b><?=number_format($member_status['today']["out_member"])?></b> 명</td>
									<td><b><?=number_format($member_status['today']["sleep_member"])?></b> 명 (<b><?=number_format($member_status['today']["nosend_member"])?></b>명)</td><?//JJC002 ?>
								</tr>
								<tr>
									<td>최근일주일</td>
									<td><b><?=number_format($member_status['seven_days']["join_member"])?></b> 명</td>
									<td><b><?=number_format($member_status['seven_days']["visit_member"])?></b> 명</td>
									<td><b><?=number_format($member_status['seven_days']["out_member"])?></b> 명</td>
									<td><b><?=number_format($member_status['seven_days']["sleep_member"])?></b> 명 (<b><?=number_format($member_status['seven_days']["nosend_member"])?></b>명)</td><?//JJC002 ?>
								</tr>
								<tr>
									<td>최근한달</td>
									<td><b><?=number_format($member_status['one_month']["join_member"])?></b> 명</td>
									<td><b><?=number_format($member_status['one_month']["visit_member"])?></b> 명</td>
									<td><b><?=number_format($member_status['one_month']["out_member"])?></b> 명</td>
									<td><b><?=number_format($member_status['one_month']["sleep_member"])?></b> 명 (<b><?=number_format($member_status['one_month']["nosend_member"])?></b>명)</td><?//JJC002 ?>
								</tr>
							</tbody> 
						</table>
				</div>


				<!-- 메인추출 -->
				<div class="main_box_area">

						<!-- 내부 그룹타이틀 -->
						<div class="group_title">
							<span class="icon"></span><span class="title">문의 및 게시판현황</span>
						</div>
						<!-- 내부 그룹타이틀 -->
						
						<table class="last_TB" summary="게시판현황">
							<colgroup>
								<col width="5%"/><col width="10%"/><col width="10%"/><col width="10%"/>
							</colgroup>
							<thead>
								<th>항목</th>
								<th>오늘게시물</th>
								<th>최근7일게시물</th>
								<th>댓글/답변대기</th>
							</thead>
							<tbody>

<?PHP
	// - 게시판 ---
	$arr_bbs = array();
	$que = " select * from odtBbsInfo where bi_view = 'Y' ";
	$r = _MQ_assoc($que);
	foreach($r as $k=>$v){

		$sr = _MQ("select count(*) as cnt_today from odtBbs where b_menu='". $v[bi_uid] ."' and left(b_rdate,10)='". $today ."' ");
		$cnt_today = $sr[cnt_today];

		$sr = _MQ("select count(*) as cnt_total from odtBbs where b_menu='". $v[bi_uid] ."' and left(b_rdate,10)>='". $seven_days ."' ");
		$cnt_total = $sr[cnt_total];
		// 댓글형일 시 
		$sr = _MQ("select sum(b_talkcnt) as cnt_talk from odtBbs where b_menu='". $v[bi_uid] ."'  ");
		$cnt_talk = $sr[cnt_talk];

		$arr_bbs[$v[bi_uid]] = array("today"=>$cnt_today , "total"=>$cnt_total, "talk"=>$cnt_talk );

	}
	// - 게시판 ---

	// - 문의하기 ---
	$arr_request = array();
	$que = " select  r_menu  from odtRequest group by r_menu ";
	$r = _MQ_assoc($que);
	foreach($r as $k=>$v){

		$sr = _MQ("select count(*) as cnt_today from odtRequest where r_menu='". $v[r_menu] ."' and left(r_rdate,10)='". $today ."' ");
		$cnt_today = $sr[cnt_today];

		$sr = _MQ("select count(*) as cnt_total from odtRequest where r_menu='". $v[r_menu] ."' and left(r_rdate,10)>='". $seven_days ."' ");
		$cnt_total = $sr[cnt_total];

		// -- LCY -- 2016-04-19 답변형일 시 추가 
		$sr = _MQ("select count(*) as cnt_talk from odtRequest where r_menu='". $v[r_menu] ."' and r_status = '답변대기'  ");
		$cnt_talk = $sr[cnt_talk];

		$arr_request[$v[r_menu]] = array("today"=>$cnt_today , "total"=>$cnt_total , "talk"=>$cnt_talk);

	}
	// - 문의하기 ---

    

    

	// - 게시판 ---
	foreach($arrBoardMenu as $k=>$v){
		echo "
			<tr>
				<td>". $v ."</td>
				<td><b>" . number_format($arr_bbs[$k][today]) ."</b> 건</td>
				<td><b>" . number_format($arr_bbs[$k][total]) ."</b> 건</td>
				<td><b>" . number_format($arr_bbs[$k][talk]) ."</b> 건</td>
			</tr>
		";
	}
	// - 게시판 ---

	// - 문의하기 ---
    foreach($arrRequestMenu as $k=>$v){
		echo "
			<tr>
				<td>". $v ."</td>
				<td><b>" . number_format($arr_request[$k][today]) ."</b> 건</td>
				<td><b>" . number_format($arr_request[$k][total]) ."</b> 건</td>
				<td><b>" . number_format($arr_request[$k][talk]) ."</b> 건</td>
			</tr>
		";
	}
	// - 문의하기 ---

    
?>
							</tbody> 
						</table>
				

				</div>
				<!-- // 메인추출 -->

				<!-- 메인배너(절대경로로) -->
				<div class="main_box_area">
					<a href="http://gobeyond.co.kr/" target="_blank" class="fl_left"><img src="http://www.onedaynet.co.kr/images_new/totaladmin_bn_gobeyond.gif" alt="" title="상상너머 바로가기" /></a>
					<a href="http://www.onedaynet.co.kr/" target="_blank" class="fl_right"><img src="http://www.onedaynet.co.kr/images_new/totaladmin_bn_onedaynet.gif" alt="" title="원데이넷 바로가기" /></a>
				</div>
				<!-- // 메인배너 -->


	</div>
	<!-- // 리스트및폼 -->



<?PHP
	include_once("inc.footer.php");
?>

<script>
	$(document).ready(function() {

		<?=$graph_script?>

		$(document).mousemove(function(e){
			$(".bar_num").css({left:e.pageX-15,top:e.pageY-63});
		}); 

		$(".bar_num").hover(
			function() {
				$(".bar_num").show();
			},
			function() {
				$(".bar_num").hide();
			}
		);
			
		$(".graph_box span").hover(
			function(e) {
				$(".bar_num").text($(this).attr("cnt"));
				$(".bar_num").show();
			},
			function() {
				$(".bar_num").hide();
			}
		);
	});
</script>
