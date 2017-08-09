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


if ( ! class_exists( 'PavoBBSMateAdminController' ) ) {
	die();
}

class PavoBBSMateAdminController {
	
	public function __construct() {
		//AJAX - INSERT POST
	}
	
	
	public static function get_taxonomy_template(){
		
		$taxonomy_text = sanitize_text_field( $_POST['taxonomy_text'] );
		$taxonomy_text = preg_replace("/\s+/", "", $taxonomy_text);
		$taxonomys =array_unique(explode(',' , $taxonomy_text));
		
		global $wp_roles;
		$roles = $wp_roles->role_names;
		
		$key = array_search('Administrator', $roles);
		
		if(!empty($key)) {
			unset($roles[$key]);
		}
		
		$html = "";
		$index = (int)sanitize_text_field( $_POST['index'] );
		foreach ($taxonomys as $taxonomy){
			if(empty($taxonomy)) continue;
		$html .= "<div class='ebbs-metabox closed'>";
		$html .= "<h3><a class='remove_row delete' href='#'>".__('Remove')."</a><div title='Click to toggle' class='handlediv'></div>";
			
		$html .= sprintf("<strong>%s</strong></h3>", $taxonomy);
		$html .= "<div class='ebbs_variable_attributes'>";
		$html .= "<div class='data'>";
		
		foreach ($roles as $key=>$role) {
			$html .= sprintf("<label for='ebbsmate_itemized_%s'>%s<input type='checkbox' value='%s' id ='ebbsmate_itemized_%s' name='ebbsmate_section[%s][permission][]'></label>", $index."_".$key , translate_user_role($role), $key , $index."_".$key, $taxonomy);
		}
		
		$html .= "</div></div>";
		$html .= sprintf("<input type='hidden' name='ebbsmate_section[%s][title]' value='%s'/></div>" ,$taxonomy , $taxonomy);
		$html .= "</div>";
		
		$index++;
		}
		
		$template["template"] = $html;
		
		die(json_encode($template));
	}
	
	
	static function load_taxonomy_template($sectionArray){
		
		global $wp_roles;
		$roles = $wp_roles->role_names;
		
		$key = array_search('Administrator', $roles);
		
		if(!empty($key)) {
			unset($roles[$key]);
		}
		
		$html = "";
		$index = 0;
		foreach ($sectionArray as $section){
			if(empty($section['permission'])){
				$section['permission'] = array();
			}
			
			$html .= "<div class='ebbs-metabox closed'>";
			$html .= "<h3><a class='remove_row delete' href='#'>".__('Remove')."</a><div title='Click to toggle' class='handlediv'></div>";
				
			$html .= sprintf("<strong>%s</strong></h3>", $section['title']);
			$html .= "<div class='ebbs_variable_attributes'>";
			$html .= "<div class='data'>";
		
			foreach ($roles as $key=>$role) {
				$html .= sprintf("<label for='ebbsmate_itemized_%s'>%s<input type='checkbox' value='%s' id ='ebbsmate_itemized_%s' name='ebbsmate_section[%s][permission][]' %s></label>", $index."_".$key , translate_user_role($role), $key , $index."_".$key, $section['title'], in_array($key, $section['permission']) ? "checked='checked'" : "");
			}
		
			$html .= "</div></div>";
			$html .= sprintf("<input type='hidden' name='ebbsmate_section[%s][title]' value='%s'/></div>" ,$section['title'] ,$section['title']);
			$html .= "</div>";
			
			$index++;
		}
		
		return $html;
	}
	
	
	static function get_section_list(){
		global $current_user;
		
		$board_id = sanitize_text_field( $_POST['board_id'] );
		 
		//게시판 분류 Role
		$board_section = get_post_meta($board_id, 'ebbsmate_section', true);
		//게시판 관리자
		$board_admin = get_post_meta($board_id, "ebbsmate_admin_ids", true);
		$board_admin = empty($board_admin)? '' :$board_admin;
		$section_list = pavoebbsmate_simple_role_check($current_user, $board_admin, $board_section);
		
		$html = '분류가 설정되지 않은 게시판 입니다.';
		if(!empty($section_list)){
			$html = "<select id='pavo_section_list' name='ebbs_section_select'>";
			$html .= "<option value='all'>전체</option>";
			foreach ($section_list as  $section){
				$html .= sprintf("<option value='%s'>%s</option>",$section,$section);
			}
			$html .= "</select>";
		}
		
		$return_val = array();
		$return_val['template'] = $html;
		die(json_encode($return_val));
	}
	
	
}

