<style>	
/* 관련상품 ----------------------- */
#container_sub .related_tit {margin-top:30px; height:45px; border-top:3px solid #ff4200; background:-webkit-gradient(linear, left bottom, left top, to(#fff), from(#eeeeee)); 
font-weight:bold; line-height:45px; color:#333; font-size:14px; letter-spacing:-1px; text-indent:10px}
#container_sub .related_tit b {color:#ff4200}

#container_sub .related_pd {padding:15px 10px; position:relative; overflow:hidden; text-align:center; background:#fff; border-bottom:1px solid #d4d2cd}
#container_sub .related_pd .arrow_left {position:absolute; top:45%; left:0; display:block;}
#container_sub .related_pd .arrow_left img {height:45px}
#container_sub .related_pd .arrow_right {position:absolute; top:45%; right:0; display:block;}
#container_sub .related_pd .arrow_right img {height:45px}

#container_sub .item_s_area {overflow:hidden; text-align:center}
#container_sub .related_pd .item_box_s {float:left; width:30%; overflow:hidden; padding:10px 5px; text-align:center}
#container_sub .related_pd .item_box_s .thumb {overflow:hidden;}
#container_sub .related_pd .item_box_s .thumb img {width:100%; float:left}
#container_sub .related_pd .item_box_s .pdname {padding-top:5px; text-align:Center; display:block; color:#333; letter-spacing:-1px; font-size:11px;
width:100%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
#container_sub .related_pd .item_box_s .pdprice {text-align:center; color:#333; font-weight:bold}


#container_sub .related_pd .roll_btn span {display:inline-block; background:#d9d9d9; border-radius:90px; width:8px; height:8px; padding:1px; margin:5px 1px 10px 1px;}
#container_sub .related_pd .roll_btn .hit {background:#ff4200;}
</style>	

<?
$relation = str_replace("/",",",$row_product[p_relation]);
$relation_assoc = _MQ_assoc("select *,(select pct_cuid from odtProductCategory where pct_pcode = code order by pct_uid asc limit 1) as cuid from odtProduct where p_view='Y' and if(sale_type = 'T', (CURDATE() BETWEEN sale_date and sale_enddate), sale_type = 'A') and find_in_set(code,'".$relation."')");
if(sizeof($relation_assoc) > 0) {
?>	

		
		<!-- 관련상품 타이틀 -->
		<div class="related_tit">
			<b>BEST</b> 관련상품
		</div>

		<div class="related_pd">

			<!-- 슬라이드화살표 -->
			<!-- <a href="" class="arrow_left"><img src="../images/arrow_left.png" alt="이전상품" /></a>
			<a href="" class="arrow_right"><img src="../images/arrow_right.png" alt="다음상품" /></a> -->


			<!-- 상품묶기 -->
			<span class="lineup">
				<span class="item_s_area">
					<div class="relation_bxslider">
					<?
					// 버튼 html 저장변수
					$app_bxslider_btn = "";
					foreach($relation_assoc as $relation_key => $relation_row) {
						$relation_link = rewrite_url($relation_row[code], "cuid=".$relation_row['cuid']);
						$relation_name = cutstr($relation_row[name],12,"");
						$app_bxslider_btn .= "<span class='".($relation_key>0 ? "off" : "hit")."'></span>";
					?>
					<!-- 상품하나 -->
					<a href="<?=$relation_link?>" class="item_box_s">
						<span class="thumb"><!-- 이미지/상품 없을 경우 --><!-- <span class="no_img"></span> --><img src="<?=replace_image(IMG_DIR_PRODUCT.$relation_row['prolist_img'])?>" alt="<?=$relation_name?>"></span>
						<span class="pdname"><?=$relation_name?></span>
						<span class="pdprice"><?=number_format($relation_row[price])?>원</span>
					</a>
					<?
					}
					?>
					</div>
				</span>
			</span>

			<div class="roll_btn rolling_bxslider_btn">
				<?for($i=0; $i<ceil(count($relation_assoc)/3); $i++){?>
					<span class='<?=($i>0 ? "off" : "hit")?>'></span>
				<?}?>

			</div>

		</div>

		<script>
			<?if(count($relation_assoc)>3){?>
				var app_width = ($(window).width()-50)/3;
				var slider = $('.relation_bxslider').bxSlider({
					controls: false,
					auto: true,
					maxSlides:9,
					minSlides:3,
					moveSlides: 3,
					slideWidth: app_width,
					pager: false,
					onSliderLoad: function(){
						$(".bx-wrapper").css({"margin":"0","max-width":(app_width*3+30)+"px"});
					},
					onSlideNext: function($slideElement, oldIndex, newIndex){ 
						pager_chk(newIndex);
					},
					onSlidePrev: function($slideElement, oldIndex, newIndex){ 
						pager_chk(newIndex);
					}
				});


				$(window).resize(function(){
					app_width = ($(window).width()-50)/3;

					slider.reloadSlider({
						controls: false,
						auto: true,
						maxSlides:9,
						minSlides:3,
						moveSlides: 3,
						slideWidth: app_width,
						pager: false,
						onSliderLoad: function(){
							$(".bx-wrapper").css({"margin":"0","max-width":(app_width*3+30)+"px"});
						},
						onSlideNext: function($slideElement, oldIndex, newIndex){ 
							pager_chk(newIndex);
						},
						onSlidePrev: function($slideElement, oldIndex, newIndex){ 
							pager_chk(newIndex);
						}
					});
				});

				function pager_chk(_idx){
					if(_idx<1){ _idx = 0; }
					$(".rolling_bxslider_btn span").removeClass("hit").addClass("off");
					$(".rolling_bxslider_btn span:nth("+_idx+")").removeClass("off").addClass("hit");
				}
			<?}?>
		</script>	


<?
}
?>