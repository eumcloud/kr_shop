<?php
	include_once dirname(__FILE__)."/../inc.php";

	/*
		사이트에 맞게 수정하세요
	*/


	$sslChk = $_SERVER['HTTPS'] != '' ? true : $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? true : false;

    // Set the output file name.
	define ("SITE_URL", ($sslChk === true ?'https://':'http://').$_SERVER['SERVER_NAME'])); // 대표 사이트 URL
    define ("FREQUENCY", "weekly"); // 크롤링 빈도
	define ("PRIORITY", "0.5"); // 우선순위 (전체고정)

	/* 타입별 URL 정의 */
	define ("PRODUCT_LIST_URL", SITE_URL."/?pn=product.list"); // 상품 카테고리 리스트 (게시판 단위)

	define ("PRODUCT_VIEW_URL", SITE_URL."/?pn=product.view"); // 상품 상세 (게시글단위)

	define ("BOARD_LIST_URL", SITE_URL."/?pn=board.list"); // 게시판 리스트 (게시판 단위)
	define ("BOARD_VIEW_URL", SITE_URL."/?pn=board.view"); // 게시판 뷰 (게시글단위)

	define ("PAGE_URL", SITE_URL."/?pn=pages.view"); // 일반 페이지(게시글단위)

	/* 추가페이지 정의 */
	$arrAddUrl = array();


	class sitemapLib{


			function product($arr = array())
			{

				// 리스트 1뎁스
				$res = _MQ_assoc("select catecode from odtCategory where cHidden='no' and catedepth='1' order by cateidx asc ");
				foreach($res as $k=>$v){
					$loc = htmlentities(PRODUCT_LIST_URL . "&cuid=".$v['catecode']);
					array_push($arr,"<url>");
					array_push($arr,"<loc>".$loc."</loc>");
					array_push($arr,"<changefreq>" . FREQUENCY . "</changefreq>");
					array_push($arr,"</url>");


					$res2 = _MQ_assoc("select catecode from odtCategory where cHidden='no' and catedepth='2' and find_in_set('".$v['catecode']."' , parent_catecode) > 0  order by cateidx asc ");
					foreach($res2 as $sk=>$sv){
						// 리스트 2뎁스
						$loc = htmlentities(PRODUCT_LIST_URL . "&cuid=".$sv['catecode']);
						array_push($arr,"<url>");
						array_push($arr,"<loc>".$loc."</loc>");
						array_push($arr,"<changefreq>" . FREQUENCY . "</changefreq>");
						array_push($arr,"</url>");

						$res3 = _MQ_assoc("select catecode from odtCategory where cHidden='no' and catedepth='3' and find_in_set('".$sv['catecode']."' , parent_catecode) > 0 order by cateidx asc");
						foreach($res3 as $sck=>$scv){

							// 리스트 3뎁스
							$loc = htmlentities(PRODUCT_LIST_URL . "&cuid=".$scv['catecode']);
							array_push($arr,"<url>");
							array_push($arr,"<loc>".$loc."</loc>");
							array_push($arr,"<changefreq>" . FREQUENCY . "</changefreq>");
							array_push($arr,"</url>");
						}


					}
				}

				// 상품
				$res = _MQ_assoc("select code from odtProduct as p where (1) and p_view = 'Y' and if(sale_type = 'T', (CURDATE() BETWEEN sale_date and sale_enddate), sale_type = 'A') order by p_idx asc");
				foreach($res as $k=>$v){
					// 리스트 3뎁스
					$loc = htmlentities(PRODUCT_VIEW_URL . "&pcode=".$v['code']);
					array_push($arr,"<url>");
					array_push($arr,"<loc>".$loc."</loc>");
					array_push($arr,"<changefreq>" . FREQUENCY . "</changefreq>");
					array_push($arr,"</url>");
				}

				return @implode("\n",$arr);
			}


			// 게시판
			function board($arr = array()){

				// 리스트
				$res = _MQ_assoc("select bi_uid from odtBbsInfo where bi_view = 'Y' order by bi_uid desc  ");
				foreach($res as $k=>$v){
					$loc = htmlentities(BOARD_LIST_URL . "&_menu=".$v['bi_uid']);
					array_push($arr,"<url>");
					array_push($arr,"<loc>".$loc."</loc>");
					array_push($arr,"<changefreq>" . FREQUENCY . "</changefreq>");
					array_push($arr,"</url>");

					// 글
					$res2 = _MQ_assoc("select b_uid from odtBbs where b_secret = 'N' order by b_uid desc  ");
					foreach($res2 as $sk=>$sv){
						$loc = htmlentities(BOARD_VIEW_URL . "&_uid=".$sv['b_uid']);
						array_push($arr,"<url>");
						array_push($arr,"<loc>".$loc."</loc>");
						array_push($arr,"<changefreq>" . FREQUENCY . "</changefreq>");
						array_push($arr,"</url>");
					}
					unset($loc);
				}

				return @implode("\n",$arr);
			}

			// 기본페이지
			function page($arr = array())
			{
				// 리스트
				$res = _MQ_assoc("select np_id from smart_normal_page where np_view = 'Y' order by np_uid desc  ");
				foreach($res as $k=>$v){
					$loc = htmlentities(PAGE_URL . "&type=pages&data=".$v['np_id']);
					array_push($arr,"<url>");
					array_push($arr,"<loc>".$loc."</loc>");
					array_push($arr,"<changefreq>" . FREQUENCY . "</changefreq>");
					array_push($arr,"</url>");
					unset($loc);
				}
				return @implode("\n",$arr);
			}

	}