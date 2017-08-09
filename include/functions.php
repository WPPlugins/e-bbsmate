<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//금칙어 목록 설정
function ebbsmate_display_prohibited_words($board_id, $content) {	
	$words_flag = get_post_meta($board_id, 'ebbsmate_word_filtering', true);

	if(!empty($words_flag)) {
		$ebbsmate_settings = get_option ( 'ebbsmate_settings' );
		$prohibited_words = $ebbsmate_settings ['prohibited_words'];
		
		$words_array = explode(',', $prohibited_words);
	
		foreach ($words_array as $single_word) {			
			$pos = strpos($content, $single_word);
			if ($pos !== false) {
				//금칙어가 있을 경우 매치하는 단어의 글자수를 가져옴
				$replace = "";
				$len = mb_strlen($single_word, "UTF-8");
						
				for($i = 0; $i < $len; $i++) {
					$replace = $replace."○";
				}
	
				$content = str_replace($single_word, $replace, $content);
			}
		}
	}	
	return $content;
}

//글자수 자르기
function ebbsmate_custom_exceprt($string = "") {
	if(mb_strlen($string) > 15) {
		$string = mb_substr($string, 0, 15);
		$string .= "...";
	}

	return $string;
}

function ebbsmate_get_font_bak() {

	$ebbsmate_settings = get_option ( 'ebbsmate_settings' );
	$font              = $ebbsmate_settings ['bbs_font'];
	
	switch ($font) {
		case "jejumyeongjo":
			$result = "font-family: 'Jeju Myeongjo', serif;";
			break;
		case "kopubbatang":
			$result = "font-family: 'KoPub Batang', serif;";
			break;
		case "nanumbrushscript":
			$result = "font-family: 'Nanum Brush Script', cursive;";
			break;
		case "nanumgothic":
			$result = "font-family: 'Nanum Gothic', sans-serif;";
			break;
		case "nanumcoding":
			$result = "font-family: 'Nanum Gothic Coding', monospace;";
			break;
		case "nanummyeongjo":
			$result = "font-family: 'Nanum Myeongjo', serif;";
			break;
		case "nanumpenscript":
			$result = "font-family: 'Nanum Pen Script', cursive;";
			break;
		case "notosanskr":
			$result = "font-family: 'Noto Sans KR', sans-serif;";
			break;
		case "hanna":
			$result = "font-family: 'Hanna', sans-serif;";
			break;
		case "jejugothic":
			$result = "font-family: 'Jeju Gothic', sans-serif;";
			break;
		case "jejuhallasan":
			$result = "font-family: 'Jeju Hallasan', cursive;";
			break;
		default:
			$result = "";
			break;
	}
	
	return $result;
}

function ebbsmate_get_font($font) {
	
	switch ($font) {
		case "":
			$result = "font-family: \"Malgun Gothic\", Dotum, Gulim, Helvetica, Arial, sans-serif;";
		case "jejumyeongjo":
			$result = "font-family: \"Jeju Myeongjo\", serif;";
			break;
		case "kopubbatang":
			$result = "font-family: \"KoPub Batang\", serif;";
			break;
		case "nanumbrushscript":
			$result = "font-family: \"Nanum Brush Script\", cursive;";
			break;
		case "nanumgothic":
			$result = "font-family: \"Nanum Gothic\", sans-serif;";
			break;
		case "nanumcoding":
			$result = "font-family: \"Nanum Gothic Coding\", monospace;";
			break;
		case "nanummyeongjo":
			$result = "font-family: \"Nanum Myeongjo\", serif;";
			break;
		case "nanumpenscript":
			$result = "font-family: \"Nanum Pen Script\", cursive;";
			break;
		case "notosanskr":
			$result = "font-family: \"Noto Sans KR\", sans-serif;";
			break;
		case "hanna":
			$result = "font-family: \"Hanna\", sans-serif;";
			break;
		case "jejugothic":
			$result = "font-family: \"Jeju Gothic\", sans-serif;";
			break;
		case "jejuhallasan":
			$result = "font-family: \"Jeju Hallasan\", cursive;";
			break;
		default:
			$result = "font-family: \"Malgun Gothic\", Dotum, Gulim, Helvetica, Arial, sans-serif;";
			break;
	}

	return $result;
}

function ebbsmate_get_fontname($cssfont){

	$font_list = array(
		"default" 			=> "Malgun Gothic",
		"jejumyeongjo" 		=> "Jeju Myeongjo",
		"kopubbatang" 		=> "KoPub Batang",
		"nanumbrushscript" 	=> "Nanum Brush Script",
		"nanumgothic"		=> "Nanum Gothic",
		"nanumcoding"		=> "Nanum Gothic Coding",
		"nanummyeongjo"		=> "Nanum Myeongjo",
		"nanumpenscript"	=> "Nanum Pen Script",
		"notosanskr"		=> "Noto Sans KR",
		"hanna"				=> "Hanna",
		"jejugothic"		=> "Jeju Gothic",
		"jejuhallasan"		=> "Jeju Hallasan",
	);
	
	
	$font = "default";
	foreach ($font_list as $fontname => $fonttext ){
		if(strpos($cssfont, $fonttext)){
			$font = $fontname;
		}
	}
	
	return $font;
}

function ebbsmate_incrementFileName($file_path,$filename){
	$array = explode(".", $filename);
	$file_ext = end($array);
	$root_name = str_replace(('.'.$file_ext),"",$filename);
	$file = $file_path.$filename;
	$i = 1;
	while(file_exists($file)){
		$file = $file_path.$root_name.'('.$i.')'.'.'.$file_ext;
		$i++;
	}
	return str_replace($file_path,"",$file);
}

function ebbsmate_get_the_author($post_id) {
	
	$user_id = get_post_field( 'post_author', $post_id );
	
	if($user_id == 0) {
		$user_nicename = get_post_meta($post_id, "pavo_board_guest_name", true);
	} else {
		$user_info = get_userdata($user_id);
		$user_nicename = $user_info->user_nicename;
	}

	return $user_nicename;
}

function ebbsmate_get_post_author_template($post_id , $url){
	$user_id = get_post_field( 'post_author', $post_id );
	
	$user_layer = "";
	if($user_id != 0) {
		$user_info = get_userdata($user_id);
		$user_nicename = $user_info->user_nicename;
		
		$search_userid_url = $url."?search_option=id&search_text=".$user_id;
		$user_layer = "<span class='user-profile'>";
		$user_layer .= "<a href=''>".$user_nicename."</a></span>";
		$user_layer .="<div class='writer_layer'>";
		$user_layer .="<ul class='innerList'>";
		$user_layer .="<li class='toparrow'></li>";
		$user_layer .= apply_filters( 'ebbsmate_author_template_before', '', $user_id );
		$user_layer .="<li class='item'><a href='".$search_userid_url."'title=''>게시글 보기</a></li>";
		$user_layer .= apply_filters( 'ebbsmate_author_template_after', '', $user_id );
		$user_layer .="</ul></div>";
	}else{
		$user_layer = "<span>";
		$user_layer .= get_post_meta($post_id, "pavo_board_guest_name", true) . "</span>";
	}
	
	return $user_layer;
}

function ebbsmate_get_post_author_template_mobile($post_id , $url){
	$user_id = get_post_field( 'post_author', $post_id );

	$user_layer = "";
	if($user_id != 0) {
		$user_info = get_userdata($user_id);
		$user_nicename = $user_info->user_nicename;
		
		$search_userid_url = $url."?search_option=id&search_text=".$user_id;

		$user_layer = "<li class='m_writer_layer'>작성자 :<span><a href=''>".$user_nicename."</a></span>";
		$user_layer .="<div class='writer_layer'>";
		$user_layer .="<ul class='innerList'>";
		$user_layer .="<li class='toparrow'></li>";
		$user_layer .= apply_filters( 'ebbsmate_author_template_before', '', $user_id );
		$user_layer .="<li class='item'><a href='".$search_userid_url."'title=''>게시글 보기</a></li>";
		$user_layer .= apply_filters( 'ebbsmate_author_template_after', '', $user_id );
		$user_layer .="</ul></div></li>";
	}else{
		$user_layer .= "작성자 : ".get_post_meta($post_id, "pavo_board_guest_name", true);
	}

	return $user_layer;
}


function ebbsmate_get_the_author_admin($post_id) {

	$user_id = get_post_field( 'post_author', $post_id );

	if($user_id == 0) {
		$user_nicename = get_post_meta($post_id, "pavo_board_guest_name", true);
		$user_nicename .= " (방문자)";
	} else {
		$user_info = get_userdata($user_id);
		$user_nicename = $user_info->user_nicename;
	}

	return $user_nicename;
}

function ebbsmate_print_no_page() {
	echo "존재하지 않는 게시글 ID 입니다.";
}

function ebbsmate_vcount_dupl_chk($board_id , $post_id) {
	
	$cur_view_count = get_post_meta($post_id, 'pavo_board_view_count', true);
	$cur_view_count = (!$cur_view_count || empty($cur_view_count)) ? 0 : $cur_view_count;
	$cur_user_id = get_current_user_id();
	
	if($cur_user_id){
		$cur_view_user = get_post_meta($post_id, 'ebbsmate_post_view_users', true);
		
		if(empty($cur_view_user) || !in_array ($cur_user_id,$cur_view_user)) {
			
			
			if(empty($cur_view_user)){
				$cur_view_user = array($cur_user_id);
			}else{
				array_push($cur_view_user, $cur_user_id);
			}
			update_post_meta($post_id, 'ebbsmate_post_view_users', $cur_view_user);
			update_post_meta($post_id, 'pavo_board_view_count', $cur_view_count+1 );
		}
		return;
	}
		
	if(!isset($_SESSION)) : session_start(); endif;
	
	$current_key = $board_id."_".$post_id;
	
	if(isset($_SESSION['view_list'])){
		$view_list = $_SESSION['view_list'];
		$view_list_array = explode(";", $view_list);
		$count_flag = true;
		
		foreach ($view_list_array as $id){
			if($id == $current_key){
				$count_flag = false;
				break;
			}
		}
		
		//조회수 증가 , 세션 추가
		if($count_flag){
			update_post_meta($post_id, 'pavo_board_view_count', $cur_view_count+1 );
			$_SESSION['view_list'] .= ";".$current_key;
		}
		
	} else {
		update_post_meta($post_id, 'pavo_board_view_count', $cur_view_count+1 );
		
		$_SESSION['view_list'] = ";".$board_id."_".$post_id;
	}
}

function ebbsmate_update_vcount($post_id) {
	//조회수 1 증가처리
	$cur_view_user = get_post_meta($post_id, 'ebbsmate_post_view_users', true);
	$view_user_array = array ();
	$cntFlag = false;
	if (!empty($cur_view_user)) { //현재글 조회한 사용자가 있을때
	
		//현재 접속 사용자 조회 여부 확인
		if(!in_array (get_current_user_id(),$cur_view_user)) {
			$cntFlag = true;
		}
	} else {	//현재글 조회한 사용자가 없을때
		array_push($view_user_array, get_current_user_id());
		update_post_meta($post_id, 'ebbsmate_post_view_users', $view_user_array);
		$cntFlag = true;
	}
	
	$cur_view_count = get_post_meta($post_id, 'pavo_board_view_count', true);
	
	if($cntFlag) {
		$cur_view_count = $cur_view_count + 1;
		update_post_meta($post_id, 'pavo_board_view_count', $cur_view_count);
	}	
}


function ebbsmate_generate_random($length = 10) {

	if(!is_int($length) || ($length < 6)) {
		$length = 6;
	}
	$arr = array_merge(range('A', 'Z'), range('z', 'a'), range(1, 9));


	$rand = false;
	for($i=0; $i<$length; $i++) {
		$rand .= $arr[mt_rand(0,count($arr)-1)];
	}
	return $rand;
}

function ebbsmate_get_style_value($linenum, $attr) {
	
	$linenum = str_replace(" !important", "", $linenum);
	$linenum = preg_replace('/\s+/', '', $linenum, 1);
	
	$index = strpos($linenum, ";");
	$start = strlen($attr);
	$length = $index - $start;
	$result = substr($linenum, $start, $length);
			
	return $result;
}

function ebbsmate_get_current_theme() {
	$theme_name = get_current_theme();
	
	if(strpos('Avada', $theme_name) !== false || strpos('Avada Child', $theme_name) !== false) {
		$theme_css_class = "custom-avada";
	} else if(strpos('Jupiter', $theme_name) !== false) {
		$theme_css_class = "custom-jupiter";
	} else if(strpos('Flatsome', $theme_name) !== false) {
		$theme_css_class = "custom-flatsome";
	} else {
		$theme_css_class = "default";
	}
	
	return $theme_css_class;
}

function ebbsmate_get_current_theme_file() {
	$theme_name = get_current_theme();
	
	if(strpos('Avada', $theme_name) !== false || strpos('Avada Child', $theme_name) !== false) {
		$theme_file = "custom_avada.css";
	} else if(strpos('Jupiter', $theme_name) !== false) {
		$theme_file = "custom_jupiter.css";
	} else if(strpos('Flatsome', $theme_name) !== false) {
		$theme_file = "custom_flatsome.css";
	} else {
		$theme_file = "board_default.css";
	}

	return $theme_file;
}

?>