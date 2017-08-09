<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function get_user_roles($boardId=0, $type ,$postId = 0){	
	global $current_user;
	$user_role = get_current_wprole();
	$user_role = empty($user_role) ? 'guest' : $user_role;
	
	//게시판 관리자 롤
	$bbs_role = get_post_meta($boardId, "ebbsmate_admin_ids", true);	
	$bbs_roles = explode(",", $bbs_role);
		
	$is_bbsadmin = in_array($current_user->user_login, $bbs_roles);
	
	if( current_user_can('manage_options') || ($is_bbsadmin && !empty($current_user->user_login)) ) {
		return true;
	}

	$pbbs_board_p = get_post_meta($boardId, 'ebbsmate_'.$type.'_role', true);
		
	$return_val = false;

	switch($type) {
		case 'read'		:
			if (is_array($pbbs_board_p) && in_array($user_role, $pbbs_board_p)) {
				$return_val = true;
			}
			$return_val = pavoebbsmate_section_rolecheck_post($boardId ,$postId);
			break;
		case 'list'		:
		case 'write'	:
		case 'comment'	:
		case 'notice'	:
			if (is_array($pbbs_board_p) && in_array($user_role, $pbbs_board_p)) {
				$return_val = true;
			}
			break;
		case 'edit'		:
		case 'delete'	:
			global $post;
			$author_id=$post->post_author;
			//로그인 사용자 작성글중 본인글이 아닌경우 false;
			if(is_array($pbbs_board_p) && in_array($user_role, $pbbs_board_p)){
				//본인 글에만 수정 삭제버튼 노출
				if($current_user->ID ==  $author_id){
					$return_val = true;
				}
			}
			break;
	}
	
	return $return_val;
}

function is_pvbbsadmin($boardId=0){
	global $current_user;
	
	$user_role = get_current_wprole();
	
	$user_role = empty($user_role) ? 'guest' : $user_role;
	
	if($user_role == 'guest'){
		return false;
	}
	
	//게시판 관리자 롤
	$bbs_role = get_post_meta($boardId, "ebbsmate_admin_ids", true);
	$bbs_roles = explode(",", $bbs_role);
	
	//mabager
	if( current_user_can('manage_options') || in_array($current_user->user_login, $bbs_roles) ) return true;
}

function get_current_wprole(){
	global $current_user;
	$user_roles = $current_user->roles;
	return array_shift($user_roles);
}

function pavoebbsmate_password_check($post_id, $password){
	//패스워드 암호화 예정..
	
	$set_pwd = get_post_meta($post_id, 'pavo_board_guest_password', true);
	return ($password == $set_pwd) ? true : false;
}

function pavoebbsmate_section_rolecheck($boardId ,$section_permission = array()){
	global $current_user;
	
	//게시판 관리자 롤
	$bbs_role = get_post_meta($boardId, "ebbsmate_admin_ids", true);
	$bbs_roles = explode(",", $bbs_role);
	
	$is_bbsadmin = in_array($current_user->user_login, $bbs_roles);
	
	if( in_array("administrator" , $current_user->roles) || ($is_bbsadmin && !empty($current_user->user_login))) {
		return true;
	}
	
	if( empty($section_permission) ){
		return true;
	}
	
	foreach ($current_user->roles as $role){
		if(empty($section_permission) || in_array($role, $section_permission)){
			return true;
			break;
		}
	}
	
	return false;
}

function pavoebbsmate_section_rolecheck_post($boardId ,$postId){
	global $current_user;

	$return_val = true;
	//항목별 권한 설정 여부
	$board_section_flag = get_post_meta($boardId, 'ebbsmate_section_flag', true);
	$post_section = get_post_meta($postId, 'pavo_section_val', true);
	
	//게시판 분류 활성화 && 전체 글이 아님 && 분류값이 비어있지 않음
	if($board_section_flag && $post_section != 'all' && !empty($post_section)){
		$return_val = false;
		
		$board_section = get_post_meta($boardId, 'ebbsmate_section', true);
		
		//해당 분류의 권한체크가 없는경우
		if( empty($board_section[$post_section]['permission']) ){
			return true;
		}
		
		foreach ($current_user->roles as $role){
			//사용자 role 중에 게시판 해당 분류 권한이 있는지 확인
			if(empty($board_section[$post_section]['permission']) || in_array($role , $board_section[$post_section]['permission'])){
				$return_val = true;
				break;
			}
		}
	}
	
	
	/*
	$return_val = false;
	//항목별 권한 설정 여부
	$board_section_flag = get_post_meta($boardId, 'ebbsmate_section_flag', true);
	if(!$board_section_flag) return true;
	$post_section = get_post_meta($postId, 'pavo_section_val', true);
	
	//게시판 분류 활성화 && 전체 글이 아님 && 분류값이 비어있지 않음
	if($board_section_flag && $post_section != 'all' && !empty($post_section)){
		$board_section = get_post_meta($boardId, 'ebbsmate_section', true);
		
		if( empty($board_section[$post_section]['permission']) ){
			return true;
		}
	
		foreach ($current_user->roles as $role){
			//사용자 role 중에 게시판 해당 분류 권한이 있는지 확인
			if(empty($board_section[$post_section]['permission']) || in_array($role , $board_section[$post_section]['permission'])){
				$return_val = true;
				break;
			}
		}
	}
	*/

	return $return_val;
}


function pavoebbsmate_post_role_check($boardId=0 ,$post_id = 0 , $action_type = "readpost", $password="" , $pwchk = false){
	global $current_user;
	
	$return_val = array(
		'role_check'	=> true,
	);
	
	if('publish' != get_post_status($boardId) ){
		$return_val["role_check"] = false;
		$action_type = "none_board";
	}
	
	if($return_val["role_check"] && is_pvbbsadmin($boardId)){
		return $return_val;
	}
	
	if($return_val["role_check"]){
		switch($action_type) {
			//게시글 보기 권한 체크
			case 'list'		:
				$return_val["role_check"] = get_user_roles($boardId, "list" ,$post_id);
				
				$pv_post = get_post($post_id);
				if(empty($pv_post)){
					$return_val["role_check"] = false;
					$action_type = "none_post";
					break;
				}
				
				break;
			//게시글 보기 권한 체크
			case 'readpost'		:
				$return_val["role_check"] = get_user_roles($boardId, "read" ,$post_id);
				
				$pv_post = get_post($post_id);
				if(empty($pv_post)){
					$return_val["role_check"] = false;
					$action_type = "none_post";
					break;
				}
				
				//비밀글인경우 본인글인지 확인
				$secret_flag = get_post_meta($post_id, 'pavo_board_secret_flag', true);
				if($secret_flag){
					$post_author = $pv_post->post_author;
					
					if($post_author > 0){
						$return_val["role_check"] = ($current_user->ID ==  $post_author) ? true : false;
						if(!$return_val["role_check"]){
							$action_type = "secret_check";
						}
					}else{
						if(empty($password)){
							$return_val["role_check"] = false;
						}else{
							$guest_secret = pavoebbsmate_password_check($post_id , $password);
							if(!$guest_secret) $return_val["role_check"] = false;
						}
					}
				}
				break;
				
			//게시글 작성 권한 체크
			case 'insertpost'	:
			case 'insert'		:
				// 글쓰기 권한 확인
				$return_val["role_check"] = get_user_roles($boardId, "write");
				break;
				
			// 게시글 수정 권한 체크
			case 'editpost'		:
	
				// 글쓰기 권한 확인
				$return_val["role_check"] = get_user_roles($boardId, "write");
				if(!$return_val["role_check"]){
					break ;
				}
				
				$pv_post = get_post($post_id);
				if(empty($pv_post)){
					$return_val["role_check"] = false;
					$action_type = "none_post";
					break;
				}
				
				//비밀글인경우 본인글인지 확인
				$secret_flag = get_post_meta($post_id, 'pavo_board_secret_flag', true);
				$post_author = $pv_post->post_author;
					
				if($post_author > 0){
					// 방문자 게시글 이외에 자신의 글인지 확인
					$return_val["role_check"] = ($current_user->ID ==  $post_author) ? true : false;
				}else{
					if($secret_flag){
						if(empty($password)){
							$return_val["role_check"] = false;
						}else{
							$guest_secret = pavoebbsmate_password_check($post_id , $password);
							if(!$guest_secret) $return_val["role_check"] = false;
						}
					}else{
						$return_val["role_check"] = true;
					}
				}
				
				break;
				
			// 게시글 삭제 권한 체크
			// 비접속자 글인경우 비밀번호 체크
			case 'update'		:
			case 'deletepost'	:
			case 'delete'		:
				
				// 글쓰기 권한 확인
				$return_val["role_check"] = get_user_roles($boardId, "write");
				
				if(!$return_val["role_check"]){
					break ;
				}
					
				$pv_post = get_post($post_id);
				if(empty($pv_post)){
					$return_val["role_check"] = false;
					$action_type = "none_post";
					break;
				}
				
				$post_author = $pv_post->post_author;
					
				if($post_author > 0){
					// 방문자 게시글 이외에 자신의 글인지 확인
					$return_val["role_check"] = ($current_user->ID ==  $post_author) ? true : false;
				}else{
					//방문자 글일경우 비밀번호 확인
					if(!empty($password)){
						$set_pwd = get_post_meta($post_id, 'pavo_board_guest_password', true);
							
						if($password == $set_pwd){
							$return_val["role_check"] = true;
						}else{
							$action_type = "passwordcheck";
							$return_val["role_check"] = false;
						}
					}else{
						$return_val["role_check"] = false;
							
					}
				}
				
				break;
			case 'insertcomment'	:
			case 'updatecomment'	:
			case 'deletecomment'	:
				// 댓글쓰기 권한 확인
				$return_val["role_check"] = get_user_roles($boardId, "comment");
				break;
			case 'passwordcheck'	:
				$postPassword = get_post_meta($post_id, 'pavo_board_guest_password', true);
				if($postPassword != $password){
					$return_val["role_check"] = false;
				}
				break;
			case 'comment-write'	:
			case 'comment-insert'	:
				// 댓글 권한 확인
				$return_val["role_check"] = get_user_roles($boardId, "comment");
				break;
			case 'comment-edit'		:
			case 'comment-delete'	:
				$return_val["role_check"] = get_user_roles($boardId, "comment");
				$pv_comment = get_comment($post_id);
				
				if(empty($pv_comment)){
					$return_val["role_check"] = false;
					$action_type = "none_comment";
					break;
				}
				
				$comment_author = $pv_comment->user_id;
				
				if($comment_author > 0){
					// 방문자 게시글 이외에 자신의 글인지 확인
					$return_val["role_check"] = ($current_user->ID ==  $comment_author) ? true : false;
				}else{
					$return_val["role_check"] = true;
				}
				
				break;
			case 'comment-update'	:
			case 'comment-deleted'	:
				$return_val["role_check"] = get_user_roles($boardId, "comment");
				
				$pv_comment = get_comment($post_id);
				
				if(empty($pv_comment)){
					$return_val["role_check"] = false;
					$action_type = "none_comment";
					break;
				}
				
				$comment_author = $pv_comment->user_id;
				
				if($comment_author > 0){
					// 방문자 게시글 이외에 자신의 글인지 확인
					$return_val["role_check"] = ($current_user->ID ==  $comment_author) ? true : false;
				}else{
					//방문자 글일경우 비밀번호 확인
					if(!empty($password)){
						$set_pwd = get_comment_meta($post_id, 'comment_password', true);
					
						if($password == $set_pwd){
							$return_val["role_check"] = true;
						}else{
							$action_type = "passwordcheck";
							$return_val["role_check"] = false;
							$return_val["role_pw"] = $set_pwd;
							
						}
					}else{
						$return_val["role_check"] = false;
					
					}
				}
				
				break;
		}
	}
	
	//다른 board 에서의 요청
	if(!empty($post_id) && $return_val["role_check"]){
		$post_parent = get_post_meta($post_id, "pavo_board_origin", true);
	
		if($post_parent != $boardId){
			$return_val["role_check"] = false;
			$action_type = "access_path_error";
		}
	}
	
	if(!$return_val["role_check"]){
		$message = "";
		switch($action_type) {
			case 'list'				:	$return_val["message"] = __("목록 보기 권한이 없습니다."); break;
			case 'readpost'			:	$return_val["message"] = __("게시글 보기 권한이 없습니다."); break;
			case 'insertpost'		:
			case 'insert'			:	$return_val["message"] = __("게시물 작성 권한이 없습니다."); break;
			case 'editpost'			:	
			case 'update'			:	
			case 'updatepost'		:	$return_val["message"] = __("게시물 수정 권한이 없습니다."); break;
			case 'deletepost'		:	
			case 'delete'			:	$return_val["message"] = __("게시물 삭제 권한이 없습니다."); break;
			case 'insertcomment'	:	$return_val["message"] = __("댓글 등록 권한이 없습니다."); break;
			case 'updatecomment'	:	$return_val["message"] = __("댓글 수정 권한이 없습니다."); break;
			case 'deletecomment'	:	$return_val["message"] = __("댓글 삭제 권한이 없습니다."); break;
			case 'passwordcheck'	:	$return_val["message"] = __("비밀번호가 일치하지 않습니다."); break;
			case 'secret_check'		:	$return_val["message"] = __("비밀글은 본인과 관리자만 열람할수 있습니다."); break;
			case 'none_post'		:	$return_val["message"] = __("해당글이 존재하지 않습니다."); break;
			case 'comment-write'	:
			case 'comment-insert'	:	$return_val["message"] = __("댓글 등록 권한이 없습니다."); break;
			case 'comment-edit'		:
			case 'comment-update'	:	$return_val["message"] = __("댓글 수정 권한이 없습니다."); break;
			case 'none_comment'		:	$return_val["message"] = __("해당글이 존재하지 않습니다."); break;
			case 'access_path_error':	$return_val["message"] = __("접근 경로가 올바르지 않습니다."); break;
			case 'none_board'		:	$return_val["message"] = __("게시판이 존재하지 않습니다."); break;
			default					:	$return_val["message"] = __("권한이 없습니다."); break;
		}
		
	}
	
	return $return_val;
}


/**
 * 해당 게시판 분류중 읽기 가능한 리스트
 * 
 * @param $current_user
 * @param $board_admin
 * @param $board_section
 */
function pavoebbsmate_simple_role_check($current_user , $board_admin , $sections){

	$bbs_roles = explode(",", $board_admin);
	$is_bbsadmin = in_array($current_user->user_login, $bbs_roles);
	$use_section = array('all');
	$sections = empty($sections) ? array() : $sections;
	foreach ($sections as $section){
		if( in_array("administrator" , $current_user->roles) || ($is_bbsadmin && !empty($current_user->user_login)) || empty($section['permission']) ) {
			array_push($use_section,$section['title']);
		}else{
			foreach ($section['permission'] as $permission){
				if(in_array($permission ,$current_user->roles))
					array_push($use_section,$section['title']);
			}
		}
	}
	
	$use_section = array_unique($use_section);
	
	return $use_section;
}
?>