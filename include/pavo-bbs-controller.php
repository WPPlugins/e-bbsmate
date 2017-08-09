<?php
/*
Plugin Name: PAVO 한국형 게시판
Plugin URI: 
Description: Netville 에서 출시한 한국형 게시판 BETA 버전 입니다.
Version: 1.0.0
Author: 네트빌
Author URI: http://www.netville.co.kr
License:
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'PavoBBSMateController' ) ) {
	die();
}

class PavoBBSMateController {
	
	public function __construct() {
		
		
		
	}
	
	public static function pavoboard_maxfilesize_chk(){
		$board_id	= sanitize_text_field( $_POST['board_id'] );
		$file_size	= sanitize_text_field( $_POST['file_size'] );
		
		uploadfile_size_check();
		
	}
	
	public static function get_bbswidget_load($board_id = 0 , $code_id = 0 , $rows = 5 , $heade_flag = true , $section){
		global $wpdb,$current_user;
		
		//게시판 분류 Role
		$board_section = get_post_meta($board_id, 'ebbsmate_section', true);

		//게시판 관리자
		$board_admin = get_post_meta($board_id, "ebbsmate_admin_ids", true);
		$board_admin = empty($board_admin)? '' :$board_admin;
		
		//분류 항목
		$section_metaquery = array();
		if(!empty($section)){
			$section_metaquery = array(	'key' => 'pavo_section_val', 'value' => $section );
		};
	
		//게시글 목록 가져오기
		$args= array (
				'post_type' => array('ebbspost'),
				'post_status' => 'publish',
				'posts_per_page' => $rows,
				'paged' => 0,
				'orderby' => 'post_date',
				'order' => 'DESC',
				'meta_query' => array(
						array(
								'key' => 'pavo_board_origin',
								'value' => $board_id
						),
						array(
								'relation' => 'OR',
								array(
										'key' => 'pavo_section_val',
										'compare' => 'NOT EXISTS',
								),
								array(
										'key' => 'pavo_section_val',
										'compare' => 'IN',
										'value'     => pavoebbsmate_simple_role_check($current_user, $board_admin, $board_section),
								),
						),$section_metaquery
				)
		);
	
		$wp_query = new WP_Query($args);
		
		$shortcode = '[ebbsmate id="'.$code_id.'"]';
	
		$results = $wpdb->get_results('SELECT ID FROM '.$wpdb->base_prefix."posts WHERE post_content LIKE '%".$shortcode."%' AND post_status = 'publish'");
			
		//커스텀 CSS LOAD
		$board_css_style = get_post_meta($board_id, "ebbsmate_css_style", true);
	
		wp_enqueue_style('ebbsmate-custom-css', PavoBoardMate::$PLUGIN_URL.'css/board/'.$board_css_style.'.css', false);
	
		$return_widget = "<div class='pavoboard-wrapper pavoboard-custom'>";
		$return_widget .= "<table class='pavoboard-table' summary='게시판' id='pavoboard-table'>";
	
		if($heade_flag){
			$return_widget .= "<thead>";
			$return_widget .= "<tr>";
			$return_widget .= "<th class='entry-th-title'><span>제목</span></th>";
			$return_widget .= "<th class='entry-th-writer'><span>작성자</span></th>";
			$return_widget .= "<th class='entry-th-date'><span>작성일 </span></th>";
			$return_widget .= "<th class='entry-th-hit'><span>조회</span></th>";
			$return_widget .= "</tr>";
			$return_widget .= "</thead>";
		}
		$return_widget .= "<tbody>";
	
		//게시글 불러오기 시작
		$index = 0;
		if ( $wp_query->have_posts() ) :
		while ( $wp_query->have_posts() ) : $wp_query->the_post();
		
		$title = ebbsmate_display_prohibited_words($board_id, $wp_query->post->post_title);
		//코맨트 개수 가져오기
		$comments_count = wp_count_comments( $wp_query->post->ID );
		$total_comment_cnt = $comments_count->approved;
	
		//첨부파일 여부 확인
		$total_file_cnt = 0;
		$fileNames=get_post_meta($wp_query->post->ID, "pavo_bbs_file_name", false);
		if(!empty($fileNames) && sizeof($fileNames[0]) > 0) {
			$total_file_cnt = sizeof($fileNames[0]);
		} else {
			$total_file_cnt = 0;
		}
	
		//비밀글 여부 확인
		$secret_flag = get_post_meta($wp_query->post->ID, "pavo_board_secret_flag", true);
	
	
		//파일 첨부했는지 체크
		$board_id = get_post_meta($wp_query->post->ID, "pavo_board_origin", true);
		$file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
		
		//조회수
		$view_count = get_post_meta($wp_query->post->ID, 'pavo_board_view_count', true);
	
		//게시물 url
		$url = "";
		if(!empty($results)){
			$url = get_permalink($results[0]->ID);
			$params = array(
					'action' => "readpost",
					'post_id' => $wp_query->post->ID,
					'bbspaged' => 1,
			);
			$url = add_query_arg( $params, $url );
		}
	
	
		include PavoBoardMate::$PLUGIN_DIR.'templates/front/pavo-bbs-widget.php';
	
		endwhile;
		endif;
		wp_reset_postdata();
	
		$return_widget .= "</tbody></table></div>";
		
		return $return_widget;
	
	}
	
	
	public static function get_bbspage_load($board_id = 0 , $post_id = 0, $action_type = "list" ){
		global $wpdb;
		//echo (int)str_replace('M', '', ini_get('post_max_size'));
		if(!isset($_SESSION)) : session_start(); endif;
		
		$cur_post_id = get_the_ID();
		
		//커스텀 CSS LOAD
		$board_css_style = get_post_meta($board_id, "ebbsmate_css_style", true);
		
		wp_enqueue_style('ebbsmate-custom-css', PavoBoardMate::$PLUGIN_URL.'css/board/'.$board_css_style.'.css', false);
		
		//권한체크
		$role_check = pavoebbsmate_post_role_check($board_id , $post_id , $action_type , empty($_POST['newPassword'])? '' : sanitize_text_field( $_POST['newPassword'] ) );
		
		if(!$role_check["role_check"]){
			include PavoBoardMate::$PLUGIN_DIR.'templates/front/pavo-bbs-error.php';
			return;
		}
		
		//글 삭제시에는 Json 으로 return
		if($action_type == "deletepost"){
			ebbsmate_delete_post($post_id);
			$return_val["message"] = __("게시글이 삭제되었습니다.");
			$return_val["loadpage"] = $load_page;
			die(json_encode($return_val));
		}
		
		$load_page = "list";
		switch($action_type) {
			case 'list'				:	$load_page = "bbs-list"; break;
			case 'readpost'			:	$load_page = "bbs-view"; break;
			case 'insertpost'		:	$load_page = "bbs-write"; break;
			case 'insert'			:	$load_page = "bbs-insert"; break;
			case 'editpost'			:	$load_page = "bbs-edit"; break;
			case 'updatepost'		:	$load_page = "bbs-update"; break;
			case 'deletepost'		:	$load_page = "bbs-list"; break;
// 			case 'deletepost'		:	$load_page = "bbs-delete-confirm"; break;
// 			case 'delete'			:	$load_page = "bbs-delete"; break;
			case 'insertcomment'	:	$load_page = "comment-insert"; break;
			case 'updatecomment'	:	$load_page = "comment-update"; break;
			case 'deletecomment'	:	$load_page = "comment-delete"; break;
		}
		
		include PavoBoardMate::$PLUGIN_DIR.'templates/front/pavo-'.$load_page.'.php';
		
		switch($action_type) {
			case 'list'				:
			case 'readpost'			:
				include PavoBoardMate::$PLUGIN_DIR.'templates/front/pavo-bbs-password-layer.php';
				break;
		}
	}
	
	public static function ebbsmate_post_password_chk(){
		
		$post_id = sanitize_text_field( $_POST['postId'] );
		$password = sanitize_text_field( $_POST['newPassword'] );
		$action_type = "passwordcheck";
		$page_type	= sanitize_text_field( $_POST['type'] );
		$board_id	= sanitize_text_field( $_POST['board_id'] );
		$board_page_id	= sanitize_text_field( $_POST['pavo_board_page_id'] );
	
		$role_check = pavoebbsmate_post_role_check($board_id , $post_id , $action_type , $password);
		
		/*
		if($role_check["role_check"]){
			$load_page = get_permalink( $board_page_id);
			$load_page = $load_page."?action=".$page_type."&post_id=".$post_id;
			$role_check["loadpage"] = $load_page;
		}
		*/
		
		die(json_encode($role_check));
	}
	
	public static function ebbsmate_delete_post(){
		
		$post_id = sanitize_text_field( $_POST['postId'] );
		$password = sanitize_text_field( $_POST['newPassword'] );
		$action_type = "passwordcheck";
		$page_type	= sanitize_text_field( $_POST['type'] );
		$board_id	= sanitize_text_field( $_POST['board_id'] );
		$board_page_id	= sanitize_text_field( $_POST['pavo_board_page_id'] );
		
		$role_check = pavoebbsmate_post_role_check($board_id , $post_id , $action_type , $password);
		
		if($role_check["role_check"]){
			//휴지통 이동 또는 영구 삭제
			$remove_flag = "delete"; //delete
			
			//delete 시에는 첨부파일 같이 삭제
			if($remove_flag == "delete"){
				//관련 댓글 완전삭제
				
				$args = array(
						'post_id' => $post_id, // use post_id, not post_ID
 				);
 
				$comments = get_comments($args);
 
				foreach ($comments as $comment){
 					wp_delete_comment($comment->comment_ID);
 				}
 				
 				
 				//첨부 파일 삭제
 				$pavo_file_path = get_post_meta($post_id, "pavo_bbs_file_path", true);
 				if(!empty($pavo_file_path)){
 					foreach ($pavo_file_path as $file){
 						if(file_exists($file)){
 							unlink($file); 							
 						}
 					}
 				}
 				
				wp_delete_post($post_id);
				
			}else if($remove_flag == "trash"){
				//게시글 휴지통으로 이동
				wp_trash_post($post_id);
				
			}
			
			//목록으로 이동
			$load_page = get_permalink( $board_page_id);
			$role_check["loadpage"] = $load_page;
			$role_check["message"] = __("게시글이 삭제 되었습니다.");
		}
		
		die(json_encode($role_check));
		
		
	}
	
	// 게시글 수정
	public static function ebbsmate_update_post(){
		
		$post_id        = sanitize_text_field( $_REQUEST['pavo_post_cur_id'] );
		$board_id       = sanitize_text_field( $_REQUEST['pavo_post_cur_board_id'] );
		$post_title		= isset($_POST['pavo_board_post_title']) ? ebbsmate_html_purifier(ebbsmate_htmlclear( $_POST['pavo_board_post_title'] )): '';
		$post_content	= isset($_POST['pavo_board_post_content']) ? ebbsmate_html_purifier(trim( $_POST['pavo_board_post_content'] )): '';
		$board_page_id	= sanitize_text_field( $_POST['pavo_board_page_id'] );
		$password    	= empty($_REQUEST['pavo_board_guest_password']) ? '' : sanitize_text_field( $_REQUEST['pavo_board_guest_password'] );
		$notice_flag    = empty($_REQUEST['pavo_board_notice_flag']) ? '' : sanitize_text_field( $_REQUEST['pavo_board_notice_flag'] );
		$secret_flag    = empty($_REQUEST['pavo_board_secret_flag']) ? '' : sanitize_text_field( $_REQUEST['pavo_board_secret_flag'] );
		$deleted_file   = empty($_REQUEST['pavo_board_deleted_file']) ? array() : (array) $_REQUEST['pavo_board_deleted_file'] ;
		$section_val    = empty($_POST['ebbs_section_select']) ? 'all' : sanitize_text_field( $_POST['ebbs_section_select'] );
		
		//본인글 확인
		$role_check = pavoebbsmate_post_role_check($board_id , $post_id , "update" , $password );
		
		if(!$role_check["role_check"]){
			die(json_encode($role_check));
		}
		
		if(empty($notice_flag)) {
			$notice_flag = 0;
		}
		
		if(empty($secret_flag)) {
			$secret_flag = 0;
		}
		
		
		$args = array(
				'ID'            => $post_id,
				'post_title'    => $post_title,
				'post_content'  => $post_content
		);
		
		//첨부파일 사용여부
		$file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
		
		//파일 사이즈 체크
		if($file_attach_flag){
			//현재글의 첨부된 용량 - 삭제된 첨부 용량
			$curpost_attach = get_post_meta($post_id, "pavo_bbs_file_path");
			$curpost_attach_size = 0;
			if($curpost_attach){
				foreach ($curpost_attach[0] as $filepath){
					if(file_exists($filepath)){
						$curpost_attach_size = $curpost_attach_size + (int)filesize($filepath);
					}
				}
			}
			
			$upload_dir = wp_upload_dir();
			$delete_attach_size = 0;
			for($i = 0; $i < sizeof($deleted_file); $i++) {
				$str = str_replace('\\', '/', $upload_dir['basedir']);
						
				$delete_file_path = $str."/ebbsmate_attachments/".$post_id."/".$deleted_file[$i];
				if(file_exists($delete_file_path)){
					$delete_attach_size = $delete_attach_size + filesize($delete_file_path);
				}
			}
			
			$curpost_totalfile_size = $curpost_attach_size - $delete_attach_size;
			
			//추가 첨부파일 정보 가져오기
			$file_attach_size = (int)get_post_meta($board_id, "ebbsmate_attach_size", true);
			$file_attach_size = $file_attach_size*1048576;
				
			$totalfile_size = 0;
			$file_count = empty($_FILES['upload']) ? 0 : count($_FILES['upload']['name']);
			for($i=0; $i < $file_count; $i++) {
				$totalfile_size = $totalfile_size + (int)$_FILES['upload']['size'][$i];
			}
			
			$totalfile_size = $curpost_totalfile_size + $totalfile_size;
				
			if($totalfile_size > $file_attach_size){
				$role_check["role_check"] = false;
				$role_check["message"] = __( "용량이 초과되었습니다.", 'pavoboard' );
					
				die(json_encode($role_check));
			}
		}
		
		wp_update_post($args);
		
		update_post_meta($post_id, "pavo_board_notice_flag", $notice_flag);
		
		//비밀글 여부
		update_post_meta( $post_id, 'pavo_board_secret_flag', $secret_flag );
		
		//5. 분류값
		update_post_meta( $post_id, 'pavo_section_val', $section_val );
		
		if($file_attach_flag){
			//첨부파일 삭제
			$upload_dir = wp_upload_dir();
			
			$pavo_file_path = get_post_meta($post_id, "pavo_bbs_file_path", false);
			$pavo_file_name = get_post_meta($post_id, "pavo_bbs_file_name", false);
			 
			for($i = 0; $i < sizeof($deleted_file); $i++) {
			
				$str = str_replace('\\', '/', $upload_dir['basedir']);
			
				$delete_file_path = $str."/ebbsmate_attachments/".$post_id."/".$deleted_file[$i];

				for($j = 0; $j < sizeof($pavo_file_path[0]); $j++) {

					if($delete_file_path == $pavo_file_path[0][$j]) {
						if(file_exists($pavo_file_path[0][$j])){
							unlink($pavo_file_path[0][$j]);
						}
			
						unset($pavo_file_path[0][$j]);
						unset($pavo_file_name[0][$j]);
			
						$pavo_file_path[0] = array_values($pavo_file_path[0]);
						$pavo_file_name[0] = array_values($pavo_file_name[0]);
			
						update_post_meta($post_id, "pavo_bbs_file_path", $pavo_file_path[0]);
						update_post_meta($post_id, "pavo_bbs_file_name", $pavo_file_name[0]);
					}
				}
			}
			
			////////////////////////첨부파일 등록
			if(sizeof($pavo_file_name) == 0 || sizeof($pavo_file_path) == 0) {
				//첨부파일 저장 폴더 생성
				if (!empty($_FILES['upload']['size'][0]) || !empty($_FILES['upload']['size'][1])) {
					wp_mkdir_p($upload_dir['basedir']."/ebbsmate_attachments/".$post_id."/");
				}
			}
			
			//첨부파일 정보 가져오기
			//Loop through each file
			for($i=0; $i < $file_count; $i++) {
			
				$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
			
				if ($tmpFilePath != ""){
			
					$str = str_replace('\\', '/', $upload_dir['basedir']);
					$newFilePath = $str."/ebbsmate_attachments/".$post_id."/".$_FILES['upload']['name'][$i];
			
					// 2. 파일명 중복 체크
					$newFilePath = $str."/ebbsmate_attachments/".$post_id."/";
					$newFileName = ebbsmate_incrementFileName($str."/ebbsmate_attachments/".$post_id."/", $_FILES['upload']['name'][$i]);
					$newFilePath = $newFilePath.$newFileName;
			
					// 4. 파일 경로 등록
					//////////////////현재 첨부파일 meta 가져오기
					$oldFilePath = get_post_meta($post_id, "pavo_bbs_file_path", false);
					$oldFileName = get_post_meta($post_id, "pavo_bbs_file_name", false);
			
					$oldFilePath[0][sizeof($oldFilePath[0])] = $newFilePath;
					$oldFileName[0][sizeof($oldFileName[0])] = $newFileName;
			
					update_post_meta($post_id, "pavo_bbs_file_path", $oldFilePath[0]);
					update_post_meta($post_id, "pavo_bbs_file_name", $oldFileName[0]);
			
					if(move_uploaded_file($tmpFilePath, $newFilePath)) {
						//첨부 등록 실패
					}
				}
			}
		}
		
		$load_page = get_permalink( $board_page_id );
		$load_page = $load_page."?action=readpost&post_id=".$post_id;
		$role_check["loadpage"] = $load_page;
		
		die(json_encode($role_check));
		
	}
	
	
	// 게시글 등록
	public static function ebbsmate_insert_post(){
		global $wpdb;
		
		//wp_posts 테이블에 INSERT
		$user_id        = get_current_user_id();
		$post_title		= isset($_POST['pavo_board_post_title']) ? ebbsmate_html_purifier(ebbsmate_htmlclear( $_POST['pavo_board_post_title'] )): '';
		$post_content	= isset($_POST['pavo_board_post_content']) ? ebbsmate_html_purifier(trim( $_POST['pavo_board_post_content'] )): '';
		$board_id       = sanitize_text_field( $_POST['pavo_board_id'] );
		$board_page_id	= sanitize_text_field( $_POST['pavo_board_page_id'] );
		$guest_name     = empty($_POST['pavo_board_guest_name']) ? '' : $_POST['pavo_board_guest_name'];
		$guest_password = empty($_POST['pavo_board_guest_password']) ? '' : $_POST['pavo_board_guest_password'];
		$notice_flag    = empty($_POST['pavo_board_notice_flag']) ? 0 : $_POST['pavo_board_notice_flag'];
		$secret_flag    = empty($_POST['pavo_board_secret_flag']) ? 0 : $_POST['pavo_board_secret_flag'];
		$parent_id      = empty($_POST['pavo_parent_post_id']) ? 0 : $_POST['pavo_parent_post_id'];
		$section_val      = empty($_POST['ebbs_section_select']) ? 'all' : $_POST['ebbs_section_select'];
		
		//글쓰기 권한 확인
		$role_check = pavoebbsmate_post_role_check($board_id , 0 , "insert" );
		
		if(!$role_check["role_check"]){
			die(json_encode($role_check));
		}
		
		$args = array(
				'post_title'     => $post_title,
				'post_content'   => $post_content,
				'post_author'	 => get_current_user_id(),
				'post_type'      => 'ebbspost',
				'post_status'    => 'publish',
				'post_parent'    => $parent_id,
				'comment_status' => 'open',
				'ping-status'    => 'open'
		);
		
		
		//첨부파일 사용여부
		$file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
		
		//파일 사이즈 체크
		if($file_attach_flag && !empty($_FILES['upload'])){
			//첨부파일 정보 가져오기
			$file_attach_size = (int)get_post_meta($board_id, "ebbsmate_attach_size", true);
			$file_attach_size = $file_attach_size*1048576;
			
			$totalfile_size = 0;
			for($i=0; $i<count($_FILES['upload']['size']); $i++) {
				$totalfile_size = $totalfile_size + (int)$_FILES['upload']['size'][$i];
			}
			
			if($totalfile_size > $file_attach_size){
				$role_check["role_check"] = false;
				$role_check["message"] = __( "용량이 초과되었습니다.", 'pavoboard' );
					
				die(json_encode($role_check));
			}
		}
		
		
		$post_id = wp_insert_post($args);
		
		//글번호 등록
		$last_id = $wpdb->get_results("SELECT ID FROM $wpdb->posts as p, $wpdb->postmeta as m
					WHERE
					p.id = m.post_id
					and m.meta_key='pavo_board_origin'
					and m.meta_value = $board_id
					ORDER BY ID DESC LIMIT 0 , 1");
		
		
		
		//wp_postmeta 테이블에 post_meta ADD
		//1. 게시글이 속할 게시판
		add_post_meta( $post_id, 'pavo_board_origin', $board_id);
		
		//2. 조회수
		add_post_meta( $post_id, 'pavo_board_view_count', 0 );
		
		//3. 공지글 여부
		add_post_meta( $post_id, 'pavo_board_notice_flag', $notice_flag );
		
		//4. 비밀글 여부
		add_post_meta( $post_id, 'pavo_board_secret_flag', $secret_flag );
		
		//5. 분류값
		add_post_meta( $post_id, 'pavo_section_val', $section_val );
		
		if(get_current_user_id() == 0) {
		
			//비로그인 사용자일경우 게스트용 이름 postmeta ADD
			add_post_meta( $post_id, 'pavo_board_guest_name', $guest_name);
		
			//비로그인 사용자일경우 비밀번호 postmeta ADD
			add_post_meta( $post_id, 'pavo_board_guest_password', $guest_password);
		}
		
		//////////////////////////////////////////첨부파일 저장
		//파일 저장 경로 생성
		
		if($file_attach_flag && !empty($_FILES['upload'])){
			if(count($_FILES['upload']['name']) > 0) {
			
				$upload_dir = wp_upload_dir();
			
				if (!empty($_FILES['upload']['size'][0]) || !empty($_FILES['upload']['size'][1])) {
					wp_mkdir_p($upload_dir['basedir']."/ebbsmate_attachments/".$post_id."/");
				}
			}
			
			$file_path_array = array();
			$file_name_array = array();
			
			//첨부파일 정보 가져오기
			for($i=0; $i<count($_FILES['upload']['name']); $i++) {
			
				$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
			
				if ($tmpFilePath != ""){
					
					$str = str_replace('\\', '/', $upload_dir['basedir']);
			
					// 2. 파일명 중복 체크
					$newFilePath = $str."/ebbsmate_attachments/".$post_id."/";
					$newFileName = ebbsmate_incrementFileName($str."/ebbsmate_attachments/".$post_id."/", $_FILES['upload']['name'][$i]);
					$newFilePath = $newFilePath.$newFileName;
			
					// 4. 파일 경로 등록
					$file_path_array[$i] = $newFilePath;
					update_post_meta($post_id, "pavo_bbs_file_path", $file_path_array);
			
					$file_name_array[$i] = $newFileName;
					update_post_meta($post_id, "pavo_bbs_file_name", $file_name_array);
			
					if(move_uploaded_file($tmpFilePath, $newFilePath)) {
						//첨부등록 실패
						
					}
				}
			}
		}
		
		$secret_flag    = empty($_REQUEST['pavo_board_secret_flag']) ? '' : sanitize_text_field( $_REQUEST['pavo_board_secret_flag'] );
		
		$load_page = get_permalink( $board_page_id );
		//비밀글 등록인경우 목록으로 이동
		if(!$secret_flag){
			$load_page = $load_page."?action=readpost&post_id=".$post_id;
		}
		$role_check["loadpage"] = $load_page;
		
		die(json_encode($role_check));
	}
	
	public static function pavoboard_get_replycmt_form(){
		$board_id	= sanitize_text_field( $_POST['board_id'] );
		$type		= sanitize_text_field( $_POST['type'] );
		$comment_id	= sanitize_text_field( $_POST['comment_id'] );
		$post_id	= sanitize_text_field( $_POST['post_id'] );
		
		//댓글 권한 체크
		$role_check = pavoebbsmate_post_role_check($board_id , $comment_id , "comment-".$type );
		
		if($role_check["role_check"]){
			if($type == "delete"){
				$pv_comment = get_comment($comment_id);
				$comment_author = $pv_comment->user_id;
				if($comment_author > 0 || is_pvbbsadmin($board_id)){
					$role_check["delete_check"] = true;
					$role_check["message"] = __( "댓글을 삭제하시겠습니까?", 'pavoboard' );
					die(json_encode($role_check));
				}
			}
			
			//폼을 전송
			$role_check["reply_from"] = require_once(PavoBoardMate::$PLUGIN_DIR.'templates/front/pavo-comment-replyform.php');
			die();
		}else{
			die(json_encode($role_check));
		}
		
	}
	
	
	// 댓글 , 덧글 등록
	public static function pavoboard_insert_comment(){
		$comment_content	= isset($_POST['pavo_comment_content']) ? ebbsmate_html_purifier(ebbsmate_htmlclear( $_POST['pavo_comment_content'] )): '';
		$parent_post		= sanitize_text_field( $_POST['pavo_comment_post_id'] );
		$board_id			= sanitize_text_field( $_POST['pavo_comment_board_id'] );
		//$board_page			= $_POST['pavo_post_cur_board_page'];
		$type				= sanitize_text_field( $_POST['pavo_comment_type'] );
		$comment_id			= empty($_POST['comment_id'])? 0 : sanitize_text_field( $_POST['comment_id'] );
		$writer				= empty($_POST['pavo_comment_writer'])? '' : sanitize_text_field( $_POST['pavo_comment_writer'] );
		$password			= empty($_POST['pavo_comment_password'])? '' : sanitize_text_field( $_POST['pavo_comment_password'] );
		$user_agent 		= "";
		$time 				= current_time('mysql');
		$user_ip 			= "";
		
		//사용자 IP
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$user_ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$user_ip = $_SERVER['REMOTE_ADDR'];
		}
		
		
		$user_id           = get_current_user_id();
		
		//댓글 권한 체크
		$role_check = pavoebbsmate_post_role_check($board_id , 0 , "comment-".$type );
		
		
		if(empty($comment_content)){
			$role_check["role_check"] = false;
			$role_check["message"] = __("내용을 입력해 주세요.");
		}
		if(!$user_id && empty($writer)){
			$role_check["role_check"] = false;
			$role_check["message"] = __("작성자명 을 입력해 주세요.");
		}
		if(!$user_id && empty($password)){
			$role_check["role_check"] = false;
			$role_check["message"] = __("패스워드를 입력해 주세요.");
		}
		
		
		//댓글 등록
		if($role_check["role_check"]){
			
			$parent_comment_id = $type=="reply" ? sanitize_text_field( $_POST['pavoboard_parent_comment_id'] ): 0;
			
			//현재 사용자 정보 가져오기
			if(!empty($user_id)) {
				$user_info = get_userdata($user_id);
			
				$user_name = $user_info->user_nicename;
				$user_email = $user_info->user_email;
				$user_url = $user_info->user_url;
			} else {
				$user_name = "guest";
				$user_email = "";
				$user_url = "";
			}

			$data = array(
				'comment_post_ID' => $parent_post,
				'comment_author' => $user_name,
				'comment_author_email' => $user_email,
				'comment_author_url' => $user_url,
				'comment_content' => $comment_content,
				//'comment_type' => 'pavo_board_comment',
				'comment_parent' => $parent_comment_id,
				'user_id' => $user_id,
				'comment_author_IP' => $user_ip,
				'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
				'comment_date' => $time,
				'comment_approved' => 1,
			);
			
			$comment_id = wp_insert_comment($data);
			
			// 메타 정보 등록
			if(empty($user_id)) {
				add_comment_meta( $comment_id, 'comment_writer', $writer );
				add_comment_meta( $comment_id, 'comment_password', $password );
			}
			
			$comment = get_comment( $comment_id );
			$depth = ebbsmate_get_comment_depth($comment_id);
			if($comment->user_id == 0) {
				$comment_author = get_comment_meta($comment->comment_ID, "comment_writer", true);
			} else {
				$user_info = get_userdata($comment->user_id);
				$comment_author = $user_info->nickname;
			}
			
			//등록 댓글 전송
			if($depth > 1 ) echo "<ul class='children'>";
			$role_check["reply_from"] = require_once(PavoBoardMate::$PLUGIN_DIR.'templates/front/pavo-comment-single.php');
			if($depth > 1 ) echo "</div>";
			die();
		}else{
			die(json_encode($role_check));
		}
	}
	
	
	//댓글 업데이트
	public static function pavoboard_update_comment(){
		$comment_content	= sanitize_text_field( $_POST['pavo_comment_content'] );
		$parent_post		= sanitize_text_field( $_POST['pavo_comment_post_id'] );
		$board_id			= sanitize_text_field( $_POST['pavo_comment_board_id'] );
		//$board_page			= $_POST['pavo_post_cur_board_page'];
		$type				= sanitize_text_field( $_POST['pavo_comment_type'] );
		$comment_id			= sanitize_text_field( $_POST['pavoboard_comment_id'] );
		$writer				= empty($_POST['pavo_comment_writer'])? '' : sanitize_text_field( $_POST['pavo_comment_writer'] );
		$password			= empty($_POST['pavo_comment_password'])? '' : sanitize_text_field( $_POST['pavo_comment_password'] );
		
		//댓글 권한 체크
		$role_check = pavoebbsmate_post_role_check($board_id , $comment_id , "comment-".$type , $password);
		
		$comment = get_comment( $comment_id );
		
		$comment_author_id = $comment->user_id;
		
		if(empty($comment_content)){
			$role_check["role_check"] = false;
			$role_check["message"] = __("내용을 입력해 주세요.");
		}
		if(!$comment_author_id && empty($writer) && !is_pvbbsadmin($board_id)){
			$role_check["role_check"] = false;
			$role_check["message"] = __("작성자명 을 입력해 주세요.");
		}
		if(!$comment_author_id && empty($password) && !is_pvbbsadmin($board_id)){
			$role_check["role_check"] = false;
			$role_check["message"] = __("패스워드를 입력해 주세요.");
		}
		
		
		//댓글 등록
		if($role_check["role_check"]){
			$args = array(
				'comment_ID'      => $comment_id,
				'comment_content' => $comment_content
			);
			
			wp_update_comment($args);
			
			$user_id           = get_current_user_id();
			if(!$user_id){
				update_comment_meta( $comment_id, 'comment_writer', $writer );
			}
			 
			if($comment->user_id == 0) {
				$comment_author = get_comment_meta($comment_id, "comment_writer", true);
			} else {
				$user_info = get_userdata($comment->user_id);
				$comment_author = $user_info->nickname;
			}
			 
			$role_check["role_result"] = array(
				'comment_type' 		=> $type,
				'comment_content' 	=> nl2br($comment_content),
				'comment_author' 	=> $comment_author,
			);
			 
		}
		die(json_encode($role_check));
	}
	
	//댓글 삭제
	public static function pavoboard_delete_comment(){
		$board_id			= sanitize_text_field( $_POST['pavo_comment_board_id'] );
		$comment_id			= sanitize_text_field( $_POST['pavoboard_comment_id'] );
		$password			= empty($_POST['pavo_comment_password'])? '' : sanitize_text_field( $_POST['pavo_comment_password'] );
		
		//댓글 권한 체크
		$role_check = pavoebbsmate_post_role_check($board_id , $comment_id , "comment-deleted" , $password);
		
		//댓글 삭제
		if($role_check["role_check"]){
			
			$remove_flag = "delete"; //delete
			
			// 댓글의 실제 삭제가 아닌 내용제거 및 메타에 삭제를 추가...
			
			// 자식 글이 있는지 확인
			$args = array(
				'parent' => $comment_id,
			);
			$child_comment = get_comments( $args ); 
			

			//자식글이 있는경우 
			if($child_comment){
				$user_ip 			= "";
				
				//사용자 IP
				if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
					$user_ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
					$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$user_ip = $_SERVER['REMOTE_ADDR'];
				}
				
				$args = array(
					'comment_ID'      	=> $comment_id,
					'user_id'			=> 0,
					'comment_content' 	=> __( "삭제된 댓글 입니다.", 'pavoboard' ),
					'comment_author_IP' => $user_ip,
					'comment_author_url' => '',
					'comment_author_email' => '',
				);
				wp_update_comment($args);
				delete_comment_meta( $comment_id, 'comment_writer');
				add_comment_meta( $comment_id, 'comment_delete_flag' , 'true');
				//비밀번호 임의의 값으로 변경
				add_comment_meta( $comment_id, 'comment_password', ebbsmate_generate_random() );
				
				
				$comment = get_comment( $comment_id );
				$depth = ebbsmate_get_comment_depth($comment_id);
				
				$role_check["reply_from"] = require_once(PavoBoardMate::$PLUGIN_DIR.'templates/front/pavo-comment-single.php');
				die();
				
			}else{
				if($remove_flag == "delete"){
					//완전 삭제
					if(wp_delete_comment($comment_id)){
						$role_check["role_result"] = array(
								'comment_type' 		=> 'delete',
						);
						$role_check["message"] = __( "댓글이 삭제되었습니다.", 'pavoboard' );
					}else{
						$role_check["role_check"] = false;
						$role_check["message"] = __( "댓글 삭제중 오류가 발생했습니다.", 'pavoboard' );
					}
				}else{
					//휴지통으로 이동
					if(wp_trash_comment($comment_id)){
						$role_check["message"] = __( "댓글이 삭제되었습니다.", 'pavoboard' );
					}else{
						$role_check["role_check"] = false;
						$role_check["message"] = __( "댓글 삭제중 오류가 발생했습니다.", 'pavoboard' );
					}
				}
			}
			
		};
		
		die(json_encode($role_check));
		
	}
	
	
	
}