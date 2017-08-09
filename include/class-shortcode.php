<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * ebbsmate_Shortcode class
 *
 * @class       WC_Shortcodes
 * @version     1.0.1
 * @category    Class
 * @author      jukebox
 */
class ebbsmate_Shortcode {
	
	public static function init() {
		
		$shortcodes = array(
				'mypost'			=> __CLASS__ . '::my_post',
		);
		
		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( "ebbsmate_{$shortcode}" , $function );
		}
	}
	
	public static function my_post(){
		global $wp, $wpdb, $current_user;
		
		$paged = 1;
		
		$mypost_list = array (
			'post_type' => array('ebbspost'),
			'post_status' => 'publish',
			'posts_per_page' => 5,
			//'paged' => $paged,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'author' => $current_user->ID
		);
		

		$myposts = new WP_Query($mypost_list);
		
		$return_html = "<div class='pavoboard-wrapper pavoboard-custom'>";
		$return_html .= "<table class='pavoboard-table' summary='게시판' id='pavoboard-table'>";
		$return_html .= "<thead>";
		$return_html .= "<tr>";
		$return_html .= "<th class='entry-th-board'><span>게시판</span></th>";
		$return_html .= "<th class='entry-th-title'><span>제목</span></th>";
		$return_html .= "<th class='entry-th-date'><span>작성일 </span></th>";
		$return_html .= "<th class='entry-th-hit'><span>조회</span></th>";
		$return_html .= "</tr>";
		$return_html .= "</thead>";
		$return_html .= "<tbody>";
		
		if ( $myposts->have_posts() ) :
			while ( $myposts->have_posts() ) : $myposts->the_post();
		
				//게시판 ID
				$board_id = get_post_meta($myposts->post->ID, 'pavo_board_origin', true);
				
				//게시판명 가져오기
				$boardname_sql = "select post_title from $wpdb->posts where ID = '".$board_id."'";
				
				$result = $wpdb->get_row($boardname_sql);
				$board_name = !empty($result->post_title)? $result->post_title : '게시판 없음';
				
				//조회수
				$view_count = get_post_meta($myposts->post->ID, 'pavo_board_view_count', true);
				
				//제목
				$title = ebbsmate_display_prohibited_words($board_id, $myposts->post->post_title);
				
				//댓글 수
				$comments_count = wp_count_comments( $myposts->post->ID );
				$total_comment_cnt = $comments_count->approved;
				
				//비밀글 여부 확인
				$secret_flag = get_post_meta($myposts->post->ID, "pavo_board_secret_flag", true);
				
				
				//첨부파일 여부 확인
				$total_file_cnt = 0;
				$fileNames=get_post_meta($myposts->post->ID, "pavo_bbs_file_name", false);
				if(!empty($fileNames) && sizeof($fileNames[0]) > 0) {
					$total_file_cnt = sizeof($fileNames[0]);
				} else {
					$total_file_cnt = 0;
				}
				
				//게시물 url
				$post_url = get_permalink($myposts->post->ID);
				$params = array(
						'action' => "readpost",
						'post_id' => $myposts->post->ID,
						'bbspaged' => 1,
				);
				$post_url = add_query_arg( $params, $post_url );
				
				
				include PavoBoardMate::$PLUGIN_DIR.'templates/front/shortcode/ebbsmate-mypost.php';
				
			endwhile;
			
			$return_html .= "</tbody></table></div>";
			
			$current_url = home_url(add_query_arg(array(),$wp->request));
			
			//페이징
			$return_html .= $current_url;
			
			$return_html .= "<div class='paging-area'>";
			$return_html .= "<div id='pagingBar' class='pagingNav up'>";
			
			
		endif;
		
		
		//화면에 5페이지씩 표시
		$posts_per_page = 5;
		
		$count_posts = wp_count_posts('ebbspost','publish');
		$totalCnt = $count_posts->publish;
		$pageCnt = intval($totalCnt/$posts_per_page);
		
		if($totalCnt%$posts_per_page <= 0) {
			$max_page = $pageCnt;
		} else {
			$pageCnt = $pageCnt + 1;
			$max_page = $pageCnt;
		}
		
		//화면에 5페이지씩 표시
		$screenCnt = 5;
		
		$minPage = ((ceil($paged / $screenCnt) -1) * $screenCnt) + 1;
		$maxPage = ceil($paged / $screenCnt) * $screenCnt;
		
		for($page = $minPage; $page <= $maxPage; $page++) {
			if($paged == $page && $pageCnt != 0) {
				$return_html .= "<strong>". $page ."</strong>";
			} else if($page<= $max_page) {
				$return_html .= "<a title=". $page ." href=''>". $page ."</a>";
			}
		}
		
		$return_html .= "</div></div>";
			              			
		wp_reset_postdata();
		
		return $return_html;
	}
	
	
	
	
	
}

