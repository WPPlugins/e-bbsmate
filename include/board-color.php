<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//스타일값 불러오기
$ebbsmate_css_content = "";

if( $action == 'editstyle' ){
	//CSS 파일 불러오기
	$css_file_name = $_GET['filename'];
	$plugin_dir = PavoBoardMate::$PLUGIN_DIR. "css/board/".$css_file_name.".css";
	
	$lines = file($plugin_dir, FILE_IGNORE_NEW_LINES);
	
	//공통설정 - 포인트 색상
	$point_color = ebbsmate_get_style_value($lines[37], "border-color: ");
	
	//말머리 설정 - 말머리 테두리 라운드
	//$headline_round_px = ebbsmate_get_style_value($lines[23], "border-radius: ");
	
	//말머리 설정 - 테두리 색상
	//$headline_border_color = ebbsmate_get_style_value($lines[22], "border-color: ");
	
	//말머리 설정 - 테두리 두께
	//$headline_border_width = ebbsmate_get_style_value($lines[20], "border-width: ");
	//$headline_border_width = explode(" ", $headline_border_width);
	
	//말머리 설정 - 배경 색상
	$headline_background_color = ebbsmate_get_style_value($lines[16], "background-color: ");
	
	//말머리 설정 - 배경 색상 투명도
	//$headline_bg_opacity = ebbsmate_get_style_value($lines[26], "opacity: ");
	
	//말머리 설정 - 배경 이미지
	//$headline_image_url = ebbsmate_get_style_value($lines[17], "background-image: ");
	
	//말머리 설정 - 이미지 스타일
	/* $headline_image_align = ebbsmate_get_style_value($lines[19], "background-position: ");
	 if(empty($headline_image_align)) {
	 $headline_image_align = "repeat";
	} */
	
	//헤더 설정 - 헤더 배경 색상
	$header_background_color = ebbsmate_get_style_value($lines[53], "background: ");
	
	//헤더 설정 - 헤더 구분선 색상
	$header_division_color = ebbsmate_get_style_value($lines[60], "border-left-color: ");
	
	//헤더 설정 - 헤더 테두리 색상
	$header_border_color = ebbsmate_get_style_value($lines[52], "border-color: ");
	
	//헤더 설정 - 헤더 테두리 두께
	$header_border_width = ebbsmate_get_style_value($lines[51], "border-width: ");
	
	//공지 설정 - 공지 배경 색상
	$notice_background_color = ebbsmate_get_style_value($lines[66], "background: ");
	
	//공지 설정 - 공지 아이콘 글씨 색상
	$notice_icon_color = ebbsmate_get_style_value($lines[80], "color: ");
	
	//공지 설정 - 공지 아이콘 라운드
	$notice_icon_border_radius = ebbsmate_get_style_value($lines[77], "border-radius: ");
	
	//공지 설정 - 공지 아이콘 배경 색상
	$notice_icon_background_color = ebbsmate_get_style_value($lines[76], "background: ");
	
	//공지 설정 - 공지 제목 색상
	$notice_title_color = ebbsmate_get_style_value($lines[73], "color: ");
	
	//공지 설정 - 공지 텍스트 꾸미기 (진하게)
	$notice_font_bold = ebbsmate_get_style_value($lines[72], "font-weight: ");
	
	//공지 설정 - 공지 텍스트 꾸미기 (기울임)
	$notice_font_italic = ebbsmate_get_style_value($lines[71], "font-style: ");
		
	//공지 설정 - 공지 텍스트 꾸미기 (밑줄)
	$notice_font_underline = ebbsmate_get_style_value($lines[70], "text-underline: ");
	
	//텍스트 설정 - 폰트 설정
	$basic_font_family = ebbsmate_get_fontname(ebbsmate_get_style_value($lines[7], "font-family: "));
		
	//텍스트 설정 - 말머리 텍스트 색상
	$headline_font_color = ebbsmate_get_style_value($lines[25], "color: ");
	
	//텍스트 설정 - 헤더 텍스트 색상
	$header_font_color = ebbsmate_get_style_value($lines[61], "color: ");
	
	//텍스트 설정 - 헤더 텍스트 사이즈
	$header_font_size = ebbsmate_get_style_value($lines[56], "font-size: ");
	
	//텍스트 설정 - 헤더 텍스트 꾸미기(진하게)
	$header_font_bold = ebbsmate_get_style_value($lines[59], "font-weight: ");
	
	//텍스트 설정 - 헤더 텍스트 꾸미기(기울임)
	$header_font_italic = ebbsmate_get_style_value($lines[58], "font-style: ");
	
	//텍스트 설정 - 헤더 텍스트 꾸미기(밑줄)
	$header_font_underline = ebbsmate_get_style_value($lines[57], "text-decoration: ");
	
	//텍스트 설정 - 링크 텍스트 색상
	$link_font_color = ebbsmate_get_style_value($lines[90], "color: ");
	
	//텍스트 설정 - 링크 텍스트 꾸미기(진하게)
	$link_font_bold = ebbsmate_get_style_value($lines[89], "font-weight: ");
	
	//텍스트 설정 - 링크 텍스트 꾸미기(기울임)
	$link_font_italic = ebbsmate_get_style_value($lines[88], "font-style: ");
	
	//텍스트 설정 - 링크 텍스트 꾸미기(밑줄)
	$link_font_underline = ebbsmate_get_style_value($lines[87], "text-decoration: ");
	
	//라인 설정 - 라인 스타일
	$post_line_style = ebbsmate_get_style_value($lines[43], "border-style: ");
	
	//라인 설정 - 라인 색상
	$post_line_color = ebbsmate_get_style_value($lines[44], "border-color: ");
	
	//라인 설정 - 마우스 오버 색상
	$post_mouseover_color = ebbsmate_get_style_value($lines[104], "background-color: ");
	
	//라인 설정 - 텍스트 간격
	$post_line_margin = ebbsmate_get_style_value($lines[45], "padding-top: ");
	
	//버튼 설정 - 버튼 라운드
	$button_round_px = ebbsmate_get_style_value($lines[100], "border-radius: ");
	
	//버튼 설정 - 버튼 테두리 색상
	$button_border_color = ebbsmate_get_style_value($lines[96], "border-color: ");
	
	//버튼 설정 - 버튼 배경 색상
	$button_bg_color = ebbsmate_get_style_value($lines[95], "background: ");
	
	//버튼 설정 - 버튼 텍스트 색상
	$button_text_color = ebbsmate_get_style_value($lines[97], "color: ");
	
	// 분류 설정 - 분류 버튼 라운드
	$section_tab_radius_px = ebbsmate_get_style_value($lines[111], "border-radius: ");
	
	// 분류 설정 - 테두리 컬러
	$section_tab_border_color  = ebbsmate_get_style_value($lines[110], "border-color: ");
	
	// 분류 설정 - 배경 색상
	$section_tab_bg_color  = ebbsmate_get_style_value($lines[108], "background: ");
	
	// 분류 설정 - 텍스트 색상
	$section_tab_text_color = ebbsmate_get_style_value($lines[113], "color: ");
	
	// 분류 설정 - 활성탭 배경색상
	$section_activetab_bg_color = ebbsmate_get_style_value($lines[125], "background: ");
	
	// 분류 설정 - 활성탭 텍스트 색상
	$section_activetab_text_color = ebbsmate_get_style_value($lines[126], "color: ");
	
	// CUSTOM CSS
	for ($i = 129; $i < count($lines); $i++) {
		$ebbsmate_css_content .= str_replace(" ", '&nbsp;', $lines[$i]) . "\n";
	}
	
	
}elseif ( $action == 'newstyle' ){

	$current_theme_name = wp_get_theme();
	
	switch ($current_theme_name->Name){
		case 'Avada' : 
		case 'Avada Child' : 
			//공통설정 - 포인트 색상
			$point_color = "#a0ce4e";
			//말머리 설정 - 말머리 테두리 라운드
			$headline_round_px = "0px";
			//말머리 설정 - 테두리 색상
			$headline_border_color = "#ededed";
			//말머리 설정 - 테두리 두께
			$headline_border_width = "1px 0px 1px 0px";
			//말머리 설정 - 배경 색상
			$headline_background_color = "#f9f9f9";
			//말머리 설정 - 배경 이미지
			$headline_image_url = "";
			//말머리 설정 - 이미지 스타일
			$headline_image_align = "left top";
			$headline_image_repeat = "repeat";
			//헤더 설정 - 헤더 배경 색상
			$header_background_color = "#a0ce4e";
			//헤더 설정 - 헤더 구분선 색상
			$header_division_color = "#a0ce4e";
			//헤더 설정 - 헤더 테두리 색상
			$header_border_color = "#a0ce4e";
			//헤더 설정 - 헤더 테두리 두께
			$header_border_width = "0px";
			//공지 설정 - 공지 배경 색상
			$notice_background_color = "#eff1f3";
			//공지 설정 - 공지 아이콘 글씨 색상
			$notice_icon_color = "#a0ce4e";
			//공지 설정 - 공지 아이콘 라운드
			$notice_icon_border_radius = "0";
			//공지 설정 - 공지 아이콘 배경 색상
			$notice_icon_background_color = "#EFF1F3";
			//공지 설정 - 공지 제목 색상
			$notice_title_color = "#ffffff";
			//공지 설정 - 공지 텍스트 꾸미기 (진하게)
			$notice_font_bold = "bold";
			//공지 설정 - 공지 텍스트 꾸미기 (기울임)
			$notice_font_italic = "";
			//공지 설정 - 공지 텍스트 꾸미기 (밑줄)
			$notice_font_underline = "";
			//텍스트 설정 - 폰트 설정
			$basic_font_family = ebbsmate_get_fontname("\"Malgun Gothic\", Dotum, Gulim, Helvetica, Arial, sans-serif;");
			//텍스트 설정 - 말머리 텍스트 색상
			$headline_font_color = "#a0ce4e";
			//텍스트 설정 - 헤더 텍스트 색상
			$header_font_color = "#fff";
			//텍스트 설정 - 헤더 텍스트 사이즈
			$header_font_size = "";
			//텍스트 설정 - 헤더 텍스트 꾸미기(진하게)
			$header_font_bold = "bold";
			//텍스트 설정 - 헤더 텍스트 꾸미기(기울임)
			$header_font_italic = "";
			//텍스트 설정 - 헤더 텍스트 꾸미기(밑줄)
			$header_font_underline = "";
			//텍스트 설정 - 링크 텍스트 색상
			$link_font_color = "#333";
			//텍스트 설정 - 링크 텍스트 꾸미기(진하게)
			$link_font_bold = "";
			//텍스트 설정 - 링크 텍스트 꾸미기(기울임)
			$link_font_italic = "";
			//텍스트 설정 - 링크 텍스트 꾸미기(밑줄)
			$link_font_underline = "";
			//라인 설정 - 라인 스타일
			$post_line_style = "solid";
			//라인 설정 - 라인 색상
			$post_line_color = "#e8e8e8";
			//라인 설정 - 마우스 오버 색상
			$post_mouseover_color = "#f8f8f8";
			//라인 설정 - 텍스트 간격
			$post_line_margin = "10px";
			//버튼 설정 - 버튼 라운드
			$button_round_px = "0px";
			//버튼 설정 - 버튼 테두리 색상
			$button_border_color = "#a0ce4e";
			//버튼 설정 - 버튼 배경 색상
			$button_bg_color = "#a0ce4e";
			//버튼 설정 - 버튼 글자 색상
			$button_text_color = "#fff";
			//분류
			$section_tab_radius_px = "0px";
			// 분류 설정 - 테두리 컬러
			$section_tab_border_color  = "#c4c0c4";
			// 분류 설정 - 배경 색상
			$section_tab_bg_color  = "#ffffff";
			// 분류 설정 - 텍스트 색상
			$section_tab_text_color = "#000000";
			// 분류 설정 - 활성 탭 배경 색상
			$section_activetab_bg_color = "#3c4148";
			// 분류 설정 - 활성 탭 텍스트 색상
			$section_activetab_text_color = "#ffffff";
			break;
		case 'Jupiter' :
		case 'Jupiter Child' :
			//공통설정 - 포인트 색상
			$point_color = "#f97352";
			//말머리 설정 - 말머리 테두리 라운드
			$headline_round_px = "0px";
			//말머리 설정 - 테두리 색상
			$headline_border_color = "#ededed";
			//말머리 설정 - 테두리 두께
			$headline_border_width = "1px 0px 1px 0px";
			//말머리 설정 - 배경 색상
			$headline_background_color = "#f7f7f7";
			//말머리 설정 - 배경 이미지
			$headline_image_url = "";
			//말머리 설정 - 이미지 스타일
			$headline_image_align = "left top";
			$headline_image_repeat = "no repeat";
			//헤더 설정 - 헤더 배경 색상
			$header_background_color = "#3c4148";
			//헤더 설정 - 헤더 구분선 색상
			$header_division_color = "#3c4148";
			//헤더 설정 - 헤더 테두리 색상
			$header_border_color = "#3c4148";
			//헤더 설정 - 헤더 테두리 두께
			$header_border_width = "0px";
			//공지 설정 - 공지 배경 색상
			$notice_background_color = "#f0f0f0";
			//공지 설정 - 공지 아이콘 글씨 색상
			$notice_icon_color = "#f97352";
			//공지 설정 - 공지 아이콘 라운드
			$notice_icon_border_radius = "0px";
			//공지 설정 - 공지 아이콘 배경 색상
			$notice_icon_background_color = "#f0f0f0";
			//공지 설정 - 공지 제목 색상
			$notice_title_color = "#ffffff";
			//공지 설정 - 공지 텍스트 꾸미기 (진하게)
			$notice_font_bold = "bold";
			//공지 설정 - 공지 텍스트 꾸미기 (기울임)
			$notice_font_italic = "";
			//공지 설정 - 공지 텍스트 꾸미기 (밑줄)
			$notice_font_underline = "";
			//텍스트 설정 - 폰트 설정
			$basic_font_family = ebbsmate_get_fontname("\"Malgun Gothic\", Dotum, Gulim, Helvetica, Arial, sans-serif;");
			//텍스트 설정 - 말머리 텍스트 색상
			$headline_font_color = "#777777";
			//텍스트 설정 - 헤더 텍스트 색상
			$header_font_color = "#fff";
			//텍스트 설정 - 헤더 텍스트 사이즈
			$header_font_size = "";
			//텍스트 설정 - 헤더 텍스트 꾸미기(진하게)
			$header_font_bold = "bold";
			//텍스트 설정 - 헤더 텍스트 꾸미기(기울임)
			$header_font_italic = "";
			//텍스트 설정 - 헤더 텍스트 꾸미기(밑줄)
			$header_font_underline = "";
			//텍스트 설정 - 링크 텍스트 색상
			$link_font_color = "#333";
			//텍스트 설정 - 링크 텍스트 꾸미기(진하게)
			$link_font_bold = "";
			//텍스트 설정 - 링크 텍스트 꾸미기(기울임)
			$link_font_italic = "";
			//텍스트 설정 - 링크 텍스트 꾸미기(밑줄)
			$link_font_underline = "";
			//라인 설정 - 라인 스타일
			$post_line_style = "solid";
			//라인 설정 - 라인 색상
			$post_line_color = "#e8e8e8";
			//라인 설정 - 마우스 오버 색상
			$post_mouseover_color = "#f8f8f8";
			//라인 설정 - 텍스트 간격
			$post_line_margin = "10px";
			//버튼 설정 - 버튼 라운드
			$button_round_px = "0";
			//버튼 설정 - 버튼 테두리 색상
			$button_border_color = "#b3b3b3";
			//버튼 설정 - 버튼 배경 색상
			$button_bg_color = "#b3b3b3";
			//버튼 설정 - 버튼 글자 색상
			$button_text_color = "#fff";
			//분류
			$section_tab_radius_px = "0px";
			// 분류 설정 - 테두리 컬러
			$section_tab_border_color  = "#c4c0c4";
			// 분류 설정 - 배경 색상
			$section_tab_bg_color  = "#ffffff";
			// 분류 설정 - 텍스트 색상
			$section_tab_text_color = "#000000";
			// 분류 설정 - 활성 탭 배경 색상
			$section_activetab_bg_color = "#3c4148";
			// 분류 설정 - 활성 탭 텍스트 색상
			$section_activetab_text_color = "#ffffff";
			break;
		case 'Flatsome' : 
		case 'Flatsome Child' : 
			//공통설정 - 포인트 색상
			$point_color = "#627f9a";
			//말머리 설정 - 말머리 테두리 라운드
			$headline_round_px = "0px";
			//말머리 설정 - 테두리 색상
			$headline_border_color = "#627f9a";
			//말머리 설정 - 테두리 두께
			$headline_border_width = "2px 2px 2px 2px";
			//말머리 설정 - 배경 색상
			$headline_background_color = "#fff";
			//말머리 설정 - 배경 이미지
			$headline_image_url = "";
			//말머리 설정 - 이미지 스타일
			$headline_image_align = "left top";
			$headline_image_repeat = "no-repeat";
			//헤더 설정 - 헤더 배경 색상
			$header_background_color = "#627f9a";
			//헤더 설정 - 헤더 구분선 색상
			$header_division_color = "#627f9a";
			//헤더 설정 - 헤더 테두리 색상
			$header_border_color = "#627f9a";
			//헤더 설정 - 헤더 테두리 두께
			$header_border_width = "0px";
			//공지 설정 - 공지 배경 색상
			$notice_background_color = "#fff";
			//공지 설정 - 공지 아이콘 글씨 색상
			$notice_icon_color = "#fff";
			//공지 설정 - 공지 아이콘 라운드
			$notice_icon_border_radius = "0px";
			//공지 설정 - 공지 아이콘 배경 색상
			$notice_icon_background_color = "#627f9a";
			//공지 설정 - 공지 제목 색상
			$notice_title_color = "#627f9a";
			//공지 설정 - 공지 텍스트 꾸미기 (진하게)
			$notice_font_bold = "bold";
			//공지 설정 - 공지 텍스트 꾸미기 (기울임)
			$notice_font_italic = "";
			//공지 설정 - 공지 텍스트 꾸미기 (밑줄)
			$notice_font_underline = "";
			//텍스트 설정 - 폰트 설정
			$basic_font_family = ebbsmate_get_fontname("\"Malgun Gothic\", Dotum, Gulim, Helvetica, Arial, sans-serif;");
			//텍스트 설정 - 말머리 텍스트 색상
			$headline_font_color = "#627f9a";
			//텍스트 설정 - 헤더 텍스트 색상
			$header_font_color = "#fff";
			//텍스트 설정 - 헤더 텍스트 사이즈
			$header_font_size = "";
			//텍스트 설정 - 헤더 텍스트 꾸미기(진하게)
			$header_font_bold = "bold";
			//텍스트 설정 - 헤더 텍스트 꾸미기(기울임)
			$header_font_italic = "";
			//텍스트 설정 - 헤더 텍스트 꾸미기(밑줄)
			$header_font_underline = "";
			//텍스트 설정 - 링크 텍스트 색상
			$link_font_color = "#333";
			//텍스트 설정 - 링크 텍스트 꾸미기(진하게)
			$link_font_bold = "";
			//텍스트 설정 - 링크 텍스트 꾸미기(기울임)
			$link_font_italic = "";
			//텍스트 설정 - 링크 텍스트 꾸미기(밑줄)
			$link_font_underline = "";
			//라인 설정 - 라인 스타일
			$post_line_style = "solid";
			//라인 설정 - 라인 색상
			$post_line_color = "#e8e8e8";
			//라인 설정 - 마우스 오버 색상
			$post_mouseover_color = "#f8f8f8";
			//라인 설정 - 텍스트 간격
			$post_line_margin = "10px";
			//버튼 설정 - 버튼 라운드
			$button_round_px = "0px";
			//버튼 설정 - 버튼 테두리 색상
			$button_border_color = "#627f9a";
			//버튼 설정 - 버튼 배경 색상
			$button_bg_color = "#627f9a";
			//버튼 설정 - 버튼 글자 색상
			$button_text_color = "#fff";
			//분류
			$section_tab_radius_px = "0px";
			// 분류 설정 - 테두리 컬러
			$section_tab_border_color  = "#c4c0c4";
			// 분류 설정 - 배경 색상
			$section_tab_bg_color  = "#ffffff";
			// 분류 설정 - 텍스트 색상
			$section_tab_text_color = "#000000";
			// 분류 설정 - 활성 탭 배경 색상
			$section_activetab_bg_color = "#3c4148";
			// 분류 설정 - 활성 탭 텍스트 색상
			$section_activetab_text_color = "#ffffff";
			break;
		default :
			//공통설정 - 포인트 색상
			$point_color = "#333";
			//말머리 설정 - 말머리 테두리 라운드
			$headline_round_px = "5px";
			//말머리 설정 - 테두리 색상
			$headline_border_color = "#e2e2e2";
			//말머리 설정 - 테두리 두께
			$headline_border_width = "1px 1px 1px 1px";
			//말머리 설정 - 배경 색상
			$headline_background_color = "#f6f6f6";
			//말머리 설정 - 배경 이미지
			$headline_image_url = "";
			//말머리 설정 - 이미지 스타일
			$headline_image_align = "left top";
			$headline_image_repeat = "repeat";
			//헤더 설정 - 헤더 배경 색상
			$header_background_color = "#fcfcfc";
			//헤더 설정 - 헤더 구분선 색상
			$header_division_color = "#e0e0e0";
			//헤더 설정 - 헤더 테두리 색상
			$header_border_color = "#A9A9A9";
			//헤더 설정 - 헤더 테두리 두께
			$header_border_width = "1px";
			//공지 설정 - 공지 배경 색상
			$notice_background_color = "#F1F1F1";
			//공지 설정 - 공지 아이콘 글씨 색상
			$notice_icon_color = "#fff";
			//공지 설정 - 공지 아이콘 라운드
			$notice_icon_border_radius = "3px";
			//공지 설정 - 공지 아이콘 배경 색상
			$notice_icon_background_color = "#aaa";
			//공지 설정 - 공지 제목 색상
			$notice_title_color = "#000";
			//공지 설정 - 공지 텍스트 꾸미기 (진하게)
			$notice_font_bold = "";
			//공지 설정 - 공지 텍스트 꾸미기 (기울임)
			$notice_font_italic = "";
			//공지 설정 - 공지 텍스트 꾸미기 (밑줄)
			$notice_font_underline = "";
			//텍스트 설정 - 폰트 설정
			$basic_font_family = ebbsmate_get_fontname("\"Malgun Gothic\", Dotum, Gulim, Helvetica, Arial, sans-serif;");
			//텍스트 설정 - 말머리 텍스트 색상
			$headline_font_color = "#424242";
			//텍스트 설정 - 헤더 텍스트 색상
			$header_font_color = "#424242";
			//텍스트 설정 - 헤더 텍스트 사이즈
			$header_font_size = "10px";
			//텍스트 설정 - 헤더 텍스트 꾸미기(진하게)
			$header_font_bold = "bold";
			//텍스트 설정 - 헤더 텍스트 꾸미기(기울임)
			$header_font_italic = "";
			//텍스트 설정 - 헤더 텍스트 꾸미기(밑줄)
			$header_font_underline = "";
			//텍스트 설정 - 링크 텍스트 색상
			$link_font_color = "#333";
			//텍스트 설정 - 링크 텍스트 꾸미기(진하게)
			$link_font_bold = "";
			//텍스트 설정 - 링크 텍스트 꾸미기(기울임)
			$link_font_italic = "";
			//텍스트 설정 - 링크 텍스트 꾸미기(밑줄)
			$link_font_underline = "";
			//라인 설정 - 라인 스타일
			$post_line_style = "solid";
			//라인 설정 - 라인 색상
			$post_line_color = "#cccccc";
			//라인 설정 - 마우스 오버 색상
			$post_mouseover_color = "#f8f8f8";
			//라인 설정 - 텍스트 간격
			$post_line_margin = "10px";
			//버튼 설정 - 버튼 라운드
			$button_round_px = "3px";
			//버튼 설정 - 버튼 테두리 색상
			$button_border_color = "#a9a9a9";
			//버튼 설정 - 버튼 배경 색상
			$button_bg_color = "#fff";
			//버튼 설정 - 버튼 글자 색상
			$button_text_color = "#000";
			//분류
			$section_tab_radius_px = "0px";
			// 분류 설정 - 테두리 컬러
			$section_tab_border_color  = "#c4c0c4";
			// 분류 설정 - 배경 색상
			$section_tab_bg_color  = "#ffffff";
			// 분류 설정 - 텍스트 색상
			$section_tab_text_color = "#000000";
			// 분류 설정 - 활성 탭 배경 색상
			$section_activetab_bg_color = "#3c4148";
			// 분류 설정 - 활성 탭 텍스트 색상
			$section_activetab_text_color = "#ffffff";
			break;
	}
}
?>