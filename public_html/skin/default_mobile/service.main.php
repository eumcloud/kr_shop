<? 
	$page_title = "고객센터";
	include dirname(__FILE__)."/cs.header.php"; 
?>

<div class="common_page">
	<div class="common_inner common_full">

	
	<!-- ●●●●●●●●●● 고객센터메인 -->
	<div class="cm_service_main">
		
		<!-- 첫번째단락 -->
		<div class="first_area">
			
			<!-- 고객센터안내, FAQ검색 -->
			<div class="guide_faq">
				<dl>
					<dt>무엇을 도와드릴까요?</dt>
					<dd>고객님께서 사이트를 이용하시면서 가장 궁금하신 부분을 <br/>FAQ를 통해 간편하게 해결할 수 있습니다.</dd>
					<dd>그래도 해결되지 않는 점이 있다면 <a href="/m/?pn=mypage.request.form">1:1온라인문의</a> 혹은 <br/>고객센터에 연락주시면 빠르게 답변드리겠습니다.</dd>
				</dl>
				
				<!-- FAQ검색 -->
				<div class="faq_search">
				<form name="bbs_search" method="get" role="search" action="/m/">
					<input type="hidden" name="pn" value="board.list"/>
					<input type="hidden" name="_menu" value="faq"/>
					<div class="input_box"><input type="search" name="search_word" value="" class="input_design" placeholder="궁금한 점을 간단하게 검색해 보세요." /></div>
					<button type="submit" class="btn_search">FAQ검색</button>
				</form>
				<script>
				$(document).ready(function(){
					$('form[name=bbs_search]').on('submit',function(){
						if( $('input[name=search_word]').val()=='' ) { alert('검색어를 입력하세요.'); $('input[name=search_word]').focus(); return false; }
					});
				});
				</script>
				</div>
			</div>
			<!-- 고객센터안내, FAQ검색 -->
			
			<!-- 전화번호, 1:1문의 -->
			<div class="inquiry_box">
				<dl>
					<dt><a href="tel:<?=$row_company['tel']?>"><?=$row_company['tel']?></a></dt>
					<dd><span class="opt">상담시간 : </span><?=nl2br(stripslashes($row_company['officehour']))?></dd>
					<dd><span class="opt">대표메일 : </span><?=$row_company['email']?></dd>
					<dd><span class="opt">팩스번호 : </span><?=$row_company['fax']?></dd>
				</dl>
				<div class="btn_inquiry_box"><a href="/m/?pn=mypage.request.form" class="btn_inquiry">1:1온라인문의</a></div>
			</div>
			<!-- / 전화번호, 1:1문의 -->

		</div>
		<!-- / 첫번째단락 -->
		

		<!-- 바로가기 -->
		<div class="quick_btn">
			<div class="title_box">
				<span class="txt"><strong>Quick Menu</strong>클릭한번으로 쉽고 빠르게</span>
			</div>
			<ul>
				<li>
					<a href="/m/?pn=mypage.modify.form" class="btn_go">
						<span class="img_box"><img src="/m/images/cm_images/service_main_btn1.png" alt="" /></span>
						나의정보수정
					</a>
				</li>
				<li>
					<a href="/m/?pn=mypage.request.form" class="btn_go">
						<span class="img_box"><img src="/m/images/cm_images/service_main_btn2.png" alt="" /></span>
						1:1온라인문의
					</a>
				</li>
				<li>
					<a href="/m/?pn=board.list&_menu=faq" class="btn_go">
						<span class="img_box"><img src="/m/images/cm_images/service_main_btn5.png" alt="" /></span>
						자주묻는질문
					</a>
				</li>
				<li>
					<a href="/m/?pn=service.page.view&pageid=company" class="btn_go">
						<span class="img_box"><img src="/m/images/cm_images/service_main_btn3.png" alt="" /></span>
						회사소개
					</a>
				</li>		
				<li>
					<a href="?pn=service.guide" class="btn_go">
						<span class="img_box"><img src="/m/images/cm_images/service_main_btn6.png" alt="" /></span>
						이용안내
					</a>
				</li>
				<li>
					<a href="/m/?pn=service.partner.form" class="btn_go">
						<span class="img_box"><img src="/m/images/cm_images/service_main_btn4.png" alt="" /></span>
						제휴/광고문의
					</a>
				</li>
			</ul>
		</div>
		<!-- / 바로가기 -->
		
		
		<!-- 최근게시물 (모두 한줄제한)  -->
		<div class="recent_board">
			<ul>
				<li>
					
					<!-- 자주묻는질문 -->
					<div class="board_faq">
						<div class="title_box">
							<a href="/m/?pn=board.list&_menu=faq" class="link">자주묻는질문 TOP 5</a>
						</div>
						<?
						$res = _MQ_assoc("
							select 
								* ,
								CASE b_depth WHEN 1 THEN b_uid ELSE b_relation END as b_orderuid
							from odtBbs where b_menu = 'faq' and b_bestview = 'Y'
							ORDER BY b_notice='Y' desc , b_orderuid desc , b_depth asc 
							limit 5
						");
						if( count($res) > 0 ) {
						?>
						<!-- 5개까지 : 글이 없으면 div전체 안보임 -->
						<div class="list_box">
							<dl>
								<? foreach($res as $k=>$v) { ?>
								<dd>
									<a href="/m/?pn=board.list&_menu=faq&_uid=<?=$v['b_uid']?>" class="upper_link" title="<?=$v['b_title']?>"></a>
									<div class="title"><span class="icon">Q</span><?=$v['b_title']?></div>
								</dd>
								<? } ?>
							</dl>	
						</div>
						<? } else { ?>
						<!-- 내용없을경우 모두공통 -->
						<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
						<!-- // 내용없을경우 모두공통 -->
						<? } ?>
					</div>
				
				</li>
				<li>
					
					<!-- 공지사항,이벤트 -->
					<div class="board_notice">
						<div class="title_box">
							<a href="#none" onclick="return false;" data-tab="notice" class="service_tab_toggle tab hit">공지사항</a>
							<a href="#none" onclick="return false;" data-tab="event" class="service_tab_toggle tab">이벤트</a>
						</div>

						<!-- 5개까지 : 글이 없으면 div전체 안보임 -->
						<div class="service_tab_box" id="tab_notice">
						<?
							$res = _MQ_assoc("
								select 
									* ,
									CASE b_depth WHEN 1 THEN b_uid ELSE b_relation END as b_orderuid
								from odtBbs where b_menu = 'notice'
								ORDER BY b_notice='Y' desc , b_orderuid desc , b_depth asc 
								limit 5
							");
							if( count($res) > 0 ) {
						?>
							<div class="list_box">
								<dl>
								<? foreach($res as $k=>$v) { ?>
									<dd>
										<a href="/m/?pn=board.view&_uid=<?=$v['b_uid']?>" class="upper_link" title="<?=$v['b_title']?>"></a>
										<div class="title">
											<span class="date"><?=date('y-m-d',strtotime($v['b_rdate']))?></span>
											<?=$v['b_title']?>
										</div>
									</dd>
								<? } ?>
								</dl>
							</div>
						<? } else { ?>
						<!-- 내용없을경우 모두공통 -->
						<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
						<!-- // 내용없을경우 모두공통 -->
						<? } ?>
						</div>
						<div class="service_tab_box" id="tab_event" style="display:none;">
						<?
							$res = _MQ_assoc("
								select 
									* ,
									CASE b_depth WHEN 1 THEN b_uid ELSE b_relation END as b_orderuid
								from odtBbs where b_menu = 'event' and b_notice!='Y'
								ORDER BY b_orderuid desc , b_depth asc 
								limit 5
							");
							if( count($res) > 0 ) {
						?>
							<div class="list_box">
								<dl>
								<? foreach($res as $k=>$v) { ?>
									<dd>
										<a href="/m/?pn=board.view&_menu=event&_uid=<?=$v['b_uid']?>" class="upper_link" title="<?=$v['b_title']?>"></a>
										<div class="title">
											<span class="date"><?=date('y-m-d',strtotime($v['b_sdate']))?>~<?=date('y-m-d',strtotime($v['b_edate']))?></span>
											<?=$v['b_title']?>
										</div>
									</dd>
								<? } ?>
								</dl>
							</div>
						<? } else { ?>
						<!-- 내용없을경우 모두공통 -->
						<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
						<!-- // 내용없을경우 모두공통 -->
						<? } ?>
						</div>
					</div>
				
				</li>
			</ul>
		</div>
		<script>
		$(document).ready(function(){
			$('.service_tab_toggle').on('click',function(){
				var tab = $(this).data('tab'); $('.service_tab_toggle').removeClass('hit'); $(this).addClass('hit');
				$('.service_tab_box').hide(); $('#tab_'+tab).show();
			});
		});
		</script>
		<!-- / 최근게시물 -->
	
	</div>
	<!-- / 고객센터메인 -->

</div><!-- .common_inner -->
</div><!-- .common_page -->