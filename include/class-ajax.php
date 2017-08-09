<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Ebbsmate ebbsmate_AJAX
 *
 * AJAX Handler
 *
 * @class    ebbsmate_AJAX
 * @version  2.4.0
 * @package  Ebbsmate/Classes
 * @category Class
 * @author   Netville
 */
class ebbsmate_AJAX {
	
	/**
	 * ajax handlers
	 */
	public static function init() {
		self::ajax_events();
	}
	
	public static function ajax_events() {
		// AJAX
		add_action( 'wp_ajax_nopriv_pbbs_post_password_chk', array(__CLASS__, 'pbbs_post_password_chk' ));
		add_action( 'wp_ajax_pbbs_comment_edit_chk', array(__CLASS__, 'pbbs_comment_edit_chk' ));
		add_action( 'wp_ajax_nopriv_pbbs_comment_edit_chk', array(__CLASS__, 'pbbs_comment_edit_chk' ));
		add_action( 'wp_ajax_ebbsmate_post_password_chk', array('PavoBBSMateController', 'ebbsmate_post_password_chk' ));
		add_action( 'wp_ajax_nopriv_ebbsmate_post_password_chk', array('PavoBBSMateController', 'ebbsmate_post_password_chk' ));
		add_action( 'wp_ajax_ebbsmate_post_edit_chk', array('PavoBBSMateController', 'ebbsmate_post_edit_chk' ));
		add_action( 'wp_ajax_nopriv_ebbsmate_post_edit_chk', array('PavoBBSMateController', 'ebbsmate_post_edit_chk' ));
		add_action( 'wp_ajax_ebbsmate_get_all_boards', array(__CLASS__, 'ebbsmate_get_all_boards' ));
		
		//AJAX - INSERT POST
		add_action( 'wp_ajax_nopriv_ebbsmate_insert_post', array('PavoBBSMateController', 'ebbsmate_insert_post' ));
		add_action( 'wp_ajax_ebbsmate_insert_post', array('PavoBBSMateController', 'ebbsmate_insert_post' ));
		
		//AJAX - EDIT POST
		add_action( 'wp_ajax_nopriv_ebbsmate_update_post', array('PavoBBSMateController', 'ebbsmate_update_post' ));
		add_action( 'wp_ajax_ebbsmate_update_post', array('PavoBBSMateController', 'ebbsmate_update_post' ));
		
		//AJAX - ATTACH CHECK
		add_action( 'wp_ajax_nopriv_pavoboard_maxfilesize_chk', array('PavoBBSMateController', 'pavoboard_maxfilesize_chk' ));
		add_action( 'wp_ajax_pavoboard_maxfilesize_chk', array('PavoBBSMateController', 'pavoboard_maxfilesize_chk' ));
		
		//AJAX - DELETE POST
		add_action( 'wp_ajax_nopriv_ebbsmate_delete_post', array('PavoBBSMateController', 'ebbsmate_delete_post' ));
		add_action( 'wp_ajax_ebbsmate_delete_post', array('PavoBBSMateController', 'ebbsmate_delete_post' ));
		
		//AJAX - COMMENT
		add_action( 'wp_ajax_nopriv_pavoboard-get-replycomment-form', array('PavoBBSMateController', 'pavoboard_get_replycmt_form' ));
		add_action( 'wp_ajax_pavoboard-get-replycomment-form', array('PavoBBSMateController', 'pavoboard_get_replycmt_form' ));
		
		//AJAX - INSERT COMMENT
		add_action( 'wp_ajax_nopriv_pavoboard_insert_comment', array('PavoBBSMateController', 'pavoboard_insert_comment' ));
		add_action( 'wp_ajax_pavoboard_insert_comment', array('PavoBBSMateController', 'pavoboard_insert_comment' ));
		
		//AJAX - UPDATE COMMENT
		add_action( 'wp_ajax_nopriv_pavoboard_update_comment', array('PavoBBSMateController', 'pavoboard_update_comment' ));
		add_action( 'wp_ajax_pavoboard_update_comment', array('PavoBBSMateController', 'pavoboard_update_comment' ));
		
		//AJAX - DELETE COMMENT
		add_action( 'wp_ajax_nopriv_pavoboard-delete-comment', array('PavoBBSMateController', 'pavoboard_delete_comment' ));
		add_action( 'wp_ajax_pavoboard-delete-comment', array('PavoBBSMateController', 'pavoboard_delete_comment' ));
		
		add_action( 'wp_ajax_ebbsmate_get_taxonomy_template', array('PavoBBSMateAdminController', 'get_taxonomy_template' ));
		add_action( 'wp_ajax_ebbsmate_get_section_list', array('PavoBBSMateAdminController', 'get_section_list' ));
		
		
		//AJAX - STYLE
		add_action( 'wp_ajax_ebbsmate_create_style', array(__CLASS__, 'ebbsmate_create_style' ));
		add_action( 'wp_ajax_ebbsmate_delete_style', array(__CLASS__, 'ebbsmate_delete_style' ));
		add_action( 'wp_ajax_ebbsmate_get_editstyleurl', array(__CLASS__, 'ebbsmate_get_edit_style_url' ));
		add_action( 'wp_ajax_ebbsmate_copy_style', array(__CLASS__, 'ebbsmate_copy_style' ));
				
	}
	
	
	public static function pbbs_post_password_chk() {
	
		if ( isset($_REQUEST) ) {
	
			$newPassword = sanitize_text_field( sanitize_text_field( $_REQUEST['newPassword'] ) );
			$oldPassword = get_comment_meta(sanitize_text_field(  sanitize_text_field( $_REQUEST['postId'] ) ), 'comment_password', true);
				
			if ( $oldPassword  == $newPassword ) {
				echo 1;
			} else {
				echo 0;
			}
		}
		die();
	}
	
	public static function pbbs_comment_edit_chk() {
		if ( isset($_REQUEST) ) {
	
			global $wpdb;
	
			$result = false;
			$comment_user_id = get_current_user() ? get_current_user_id() : 0;
			$post_id = sanitize_text_field( sanitize_text_field( $_REQUEST['postId'] ) );
				
			//게시판 관리자, 전체 관리자 목록 불러옴
			$user_query = new WP_User_Query( array(
					'meta_query' => array(
							'relation' => 'OR',
							array(
									'key' => $wpdb->get_blog_prefix() . 'capabilities',
									'value' => 'ebbsmate_admin',
									'compare' => 'like'
							),
							array(
									'key' => $wpdb->get_blog_prefix() . 'capabilities',
									'value' => 'administrator',
									'compare' => 'like'
							)
					)
			) );
				
			//현재 사용자가 관리자인지 체크
			if ( ! empty( $user_query->results ) ) {
				foreach ( $user_query->results as $user ) {
					if($user->ID == $comment_user_id) {
						$result = true;
					}
				}
			}
				
			//관리자 ID에 해당하지 않을 경우에
			if($result == false) {
				//글쓴이 아이디와 현재 사용자 ID가 같은지 체크
				$author_id = get_comment(intval($post_id))->user_id;
	
				if($author_id == $comment_user_id) {
					$result = true;
				}
			}
				
			/* if($result == true) {
			 echo 1;
				} else {
				echo 0;
				} */
	
			echo 1;
		}
	
		die();
	}
	
	//게시판 목록 가져오기
	public static function ebbsmate_get_all_boards() {
		global $wpdb;
	
		$sql = "select p.post_title as board_title, m.meta_value as board_id
		from $wpdb->posts p, $wpdb->postmeta m
		where p.ID = m.post_id
		and p.post_type = 'ebbsboard'
		and p.post_status = 'publish'
		and m.meta_key = 'ebbsmate_board_id'
		order by p.post_date desc;";
	
		$result = $wpdb->get_results($sql);
		$contents = "<select id='ebbsmate_board_id' style='width:205px; margin-top:10px; margin-left:10px;'>";
	
		foreach($result as $option) {
			$contents = $contents."<option value='".$option->board_id."'>".$option->board_title."</option>";
		}
	
		$contents = $contents."</select>";
	
		echo $contents;
	
		die();
	}
	
	
	// create style css
	public static function ebbsmate_create_style(){
		
		$default_dir = PavoBoardMate::$PLUGIN_DIR. "css/board/";
		
		$index = 0;
		$file_list = array();
		
		if (is_dir($default_dir)) {
			if ($dh = opendir($default_dir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file == "." || $file == "..") continue;
					$file_list[$index] = $file;
					$index++;
				}
				closedir($dh);
			}
		}
		$return_val = array(
			'error_info'	=> false
		);
		
		$file_name = strtolower( sanitize_text_field( $_POST['css_file_name'] ) );
		$file_name = str_replace(" ", "_", $file_name);
		$file_name = str_replace("+", "_", $file_name);
		
		$action_style = sanitize_text_field( sanitize_text_field( $_POST['css_style_action'] ) );
		if($action_style != 'editstyle'){
			// 파일명 미 입력
			if(empty($file_name)){
				$return_val['error_info'] = true;
				$return_val['message'] = ("생성할 파일명을 입력해 주세요.");
			}
			
			
			//동일한 파일 존재
			if(!$return_val['error_info'] && in_array($file_name.".css" , $file_list)){
				$return_val['error_info'] = true;
				$return_val['message'] = $file_name.__(" 파일은 이미 존재 합니다.");
			}
		}
		
		$return_val['file_name'] = $file_name;
		
		if( !$return_val['error_info'] ){
			$style_file = ebbsmate_Style::ebbsmate_create_style($file_name);
			if (file_exists($style_file) && $action_style != 'editstyle') {
				$return_val['message'] = $file_name.__("파일이 생성되었습니다.");
				$return_val['redirect'] = admin_url('admin.php?page=ebbsmate_style_config');
			}else if (file_exists($style_file) && $action_style == 'editstyle') {
				$return_val['message'] = $file_name.__("파일이 수정 되었습니다.");
				if(!empty($_POST['board_id'])){
					// 게시판 수정페이지로 이동
					$return_val['redirect'] = admin_url('admin.php?page=ebbsmate_board')."&&action=editboard&board_id=".sanitize_text_field( $_POST['board_id'] );
				}else{
					// 스타일 수정 페이지로 이동
					$return_val['redirect'] = admin_url('admin.php?page=ebbsmate_style_config')."&action=editstyle&filename=".$file_name."&settings-updated=true";
				}
				
			} else {
				$return_val['error_info'] = true;
				$return_val['message'] = $file_name.__("파일 생성중 오류가 발생하였습니다.");
			}
		}
		
		die(json_encode($return_val));
	}
	
	// delete style css
	public static function ebbsmate_delete_style(){
		
		$return_val = array(
			'error_info'	=> false
		);
		
		$file_name = strtolower( sanitize_text_field( $_POST['file_name'] ) );
		
		$file_path = PavoBoardMate::$PLUGIN_DIR. "css/board/".$file_name.".css";
		
		if(file_exists($file_path)){
			unlink($file_path);
			$return_val['message'] = $file_name.__(" 파일이 삭제되었습니다.");
			$return_val['redirect'] = admin_url('admin.php?page=ebbsmate_style_config');
		}else{
			$return_val['error_info'] = true;
			$return_val['message'] = $file_name.__(" 파일이 존재하지 않습니다.");
		}
		
		die(json_encode($return_val));
	}
	
	
	public static function ebbsmate_get_edit_style_url(){
		$file_name = sanitize_text_field( $_POST['file_name'] );
		$board_id = sanitize_text_field( $_POST['board_id'] );
		
		$return_val = array(
			'error_info'	=> false
		);
		if(empty($file_name) || empty($board_id)){
			$return_val['error_info'] = true;
			$return_val['message'] = __("잘못된 접근 입니다.");
		}
		
		$url = admin_url('admin.php?page=ebbsmate_style_config')."&action=editstyle&board_id=".$board_id."&filename=".$file_name;
		
		$return_val['redirect'] = $url;
		
		die(json_encode($return_val));
	}
	
	public static function ebbsmate_copy_style(){
		$copy_file = strtolower(sanitize_text_field( $_POST['copy_file'] ) );
		$orig_file = sanitize_text_field( $_POST['orig_file'] );
		
		$return_val = array(
			'error_info'	=> false
		);
		
		$file_name = strtolower(sanitize_text_field( $_POST['copy_file'] ));
		$orig_file = sanitize_text_field( $_POST['orig_file'] );
		
		$file_name = str_replace(" ", "_", $file_name);
		$file_name = str_replace("+", "_", $file_name);
		
		// 파일명 미 입력
		if(empty($file_name)){
			$return_val['error_info'] = true;
			$return_val['message'] = ("생성할 파일명을 입력해 주세요.");
		}
			
		$default_dir = PavoBoardMate::$PLUGIN_DIR. "css/board/";
		
		$index = 0;
		$file_list = array();
		
		if (is_dir($default_dir)) {
			if ($dh = opendir($default_dir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file == "." || $file == "..") continue;
					$file_list[$index] = $file;
					$index++;
				}
				closedir($dh);
			}
		}
			
		//동일한 파일 존재
		if(!$return_val['error_info'] && in_array($file_name.".css" , $file_list)){
			$return_val['error_info'] = true;
			$return_val['message'] = $file_name.__(" 파일은 이미 존재 합니다.");
		}
		
		
		// 원본 파일 확인
		if(!$return_val['error_info'] && !file_exists($default_dir.$orig_file.".css")){
			$return_val['error_info'] = true;
			$return_val['message'] = ("원본 파일이 존재하지 않습니다.");
		}
		
		
		// file copy
		if(!$return_val['error_info']){
			$original_file 	= $default_dir.$orig_file.".css";
			$target_file 	= $default_dir.$file_name.".css";
			
			if(!copy($original_file, $target_file)) {
				$return_val['error_info'] = true;
				$return_val['message'] = ("오류가 발생하였습니다.");
			} else if(file_exists($target_file)) { 
				$return_val['message'] = $file_name.__("파일이 생성되었습니다.");
				
				$html = "<tr data-filename='".$file_name."' class='ebbs-style-list'>
				<td class='post-title page-title column-title'>
				<strong>
				<a title='' class='row-title' href='".admin_url('admin.php?page=ebbsmate_style_config')."&action=editstyle&filename=".$file_name."'>".$file_name.".css</a>
				</strong></td>
				<td>-</td>
				<td>
				<input type='button' value='편집' class='button style_edit'>
				<input type='button' value='복사' class='button style_copy'>
				<input type='button' value='삭제' class='button style_delete'>
				</td>
				</tr>
				";
				
				$return_val['new_file'] = $html;
			}
		}
		
		die(json_encode($return_val));
	}
	
}

ebbsmate_AJAX::init();