<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Ebbsmate ebbsmate_Style
 *
 * AJAX Handler
 *
 * @class    ebbsmate_Style
 * @version  1.0.0
 * @package  Ebbsmate/Classes
 * @category Class
 * @author   Netville
 */
class ebbsmate_Style {
	
	
	public static function ebbsmate_create_style($file_name) {
		//공통설정 - 포인트 색상
		$point_color = sanitize_text_field( $_POST['pavoboard_point_color'] );
		
		//헤더 설정 - 헤더 배경 색상
		$header_background_color = sanitize_text_field( $_POST['header_background_color'] );
		
		//헤더 설정 - 헤더 구분선 색상
		$header_division_color = sanitize_text_field( $_POST['header_division_color'] );
		
		//헤더 설정 - 헤더 테두리 색상
		$header_border_color = sanitize_text_field( $_POST['header_border_color'] );
		
		//헤더 설정 - 헤더 테두리 두께
		$header_border_width = sanitize_text_field( $_POST['header_border_width'] );
		
		//공지 설정 - 공지 배경 색상
		$notice_background_color = sanitize_text_field( $_POST['notice_background_color'] ); 
		
		//공지 설정 - 공지 아이콘 글씨 색상
		$notice_icon_color = sanitize_text_field( $_POST['notice_icon_color'] );
		
		//공지 설정 - 공지 아이콘 라운드
		$notice_icon_border_radius = sanitize_text_field( $_POST['notice_icon_border_radius'] );
		
		//공지 설정 - 공지 아이콘 배경 색상
		$notice_icon_background_color = sanitize_text_field( $_POST['notice_icon_background_color'] );
		
		//공지 설정 - 공지 아이콘 배경 투명도
		//$notice_icon_bg_opacity = $_POST['notice_icon_bg_opacity'];
		
		//공지 설정 - 공지 제목 색상
		$notice_title_color = sanitize_text_field( $_POST['notice_title_color'] );
		
		//공지 설정 - 공지 텍스트 꾸미기 (진하게)
		if(!empty($_POST['notice_font_bold'])) {
			$notice_font_bold = sanitize_text_field( $_POST['notice_font_bold'] );
		} else {
			$notice_font_bold = "";
		}
		
		//공지 설정 - 공지 텍스트 꾸미기 (기울임)
		if(!empty($_POST['notice_font_italic'])) {
			$notice_font_italic = sanitize_text_field( $_POST['notice_font_italic'] );
		} else {
			$notice_font_italic = "";
		}
		
		//공지 설정 - 공지 텍스트 꾸미기 (밑줄)
		if(!empty($_POST['notice_font_underline'])) {
			$notice_font_underline = sanitize_text_field( $_POST['notice_font_underline'] );
		} else {
			$notice_font_underline = "";
		}
		
		//텍스트 설정 - 폰트 설정
		$basic_font = sanitize_text_field( $_POST['basic_font'] );
		$basic_font_family = ebbsmate_get_font($basic_font);
		
		//텍스트 설정 - 말머리 텍스트 색상
		//$headline_font_color = $_POST['headline_font_color'];
		
		//텍스트 설정 - 헤더 텍스트 색상
		$header_font_color = sanitize_text_field( $_POST['header_font_color'] );
		
		//텍스트 설정 - 헤더 텍스트 사이즈
		$header_font_size = sanitize_text_field( $_POST['header_font_size'] );
		
		//텍스트 설정 - 헤더 텍스트 꾸미기(진하게)
		if(!empty($_POST['header_font_bold'])) {
			$header_font_bold = sanitize_text_field( $_POST['header_font_bold'] );
		} else {
			$header_font_bold = "";
		}
		
		//텍스트 설정 - 헤더 텍스트 꾸미기(기울임)
		if(!empty($_POST['header_font_italic'])) {
			$header_font_italic = sanitize_text_field( $_POST['header_font_italic'] );
		} else {
			$header_font_italic = "";
		}
		
		//텍스트 설정 - 헤더 텍스트 꾸미기(밑줄)
		if(!empty($_POST['header_font_underline'])) {
			$header_font_underline = sanitize_text_field( $_POST['header_font_underline'] );
		} else {
			$header_font_underline = "";
		}
		
		//텍스트 설정 - 링크 텍스트 색상
		$link_font_color = sanitize_text_field( $_POST['link_font_color'] );
		
		//텍스트 설정 - 링크 텍스트 꾸미기(진하게)
		if(!empty($_POST['link_font_bold'])) {
			$link_font_bold = sanitize_text_field( $_POST['link_font_bold'] );
		} else {
			$link_font_bold = "";
		}
		
		//텍스트 설정 - 링크 텍스트 꾸미기(기울임)
		if(!empty($_POST['link_font_italic'])) {
			$link_font_italic = sanitize_text_field( $_POST['link_font_italic'] );
		} else {
			$link_font_italic = "";
		}
		
		//텍스트 설정 - 링크 텍스트 꾸미기(밑줄)
		if(!empty($_POST['link_font_underline'])) {
			$link_font_underline = sanitize_text_field( $_POST['link_font_underline'] );
		} else {
			$link_font_underline = "";
		}
		
		//라인 설정 - 라인 스타일
		$post_line_style = sanitize_text_field( $_POST['line_title_bottom'] );
		
		//라인 설정 - 라인 색상
		$post_line_color = sanitize_text_field( $_POST['post_line_color'] );
		
		//라인 설정 - 마우스 오버 색상
		$post_mouseover_color = sanitize_text_field( $_POST['post_mouseover_color'] );
		
		//라인 설정 - 텍스트 간격
		$post_line_margin = sanitize_text_field( $_POST['post_line_margin'] );
		
		//버튼 설정 - 버튼 라운드
		$button_round_px = sanitize_text_field( $_POST['button_round_px'] );
		
		//버튼 설정 - 버튼 테두리 색상
		$button_border_color = sanitize_text_field( $_POST['button_border_color'] );
		
		//버튼 설정 - 버튼 배경 색상
		$button_bg_color = sanitize_text_field( $_POST['button_bg_color'] );
		
		//버튼 설정 - 버튼 글자 색상
		$button_text_color = sanitize_text_field( $_POST['button_text_color'] );
		
		//분류
		$section_tab_radius_px = sanitize_text_field( $_POST['section_tab_radius_px'] );
		
		// 분류 설정 - 테두리 컬러
		$section_tab_border_color  = sanitize_text_field( $_POST['section_tab_border_color'] );
		
		// 분류 설정 - 배경 색상
		$section_tab_bg_color  = sanitize_text_field( $_POST['section_tab_bg_color'] );
		
		// 분류 설정 - 텍스트 색상
		$section_tab_text_color = sanitize_text_field( $_POST['section_tab_text_color'] );
		
		// 분류 설정 - 활성 탭 배경 색상
		$section_activetab_bg_color = sanitize_text_field( $_POST['section_activetab_bg_color'] );
		
		// 분류 설정 - 활성 탭 텍스트 색상
		$section_activetab_text_color = sanitize_text_field( $_POST['section_activetab_text_color'] );
		
		//커스텀 CSS 설정
		$pavoboard_custom_css = "";
		if(!empty($_POST['pavoboard_custom_css'])) {
			$pavoboard_custom_css = sanitize_text_field( $_POST['pavoboard_custom_css'] );
			$pavoboard_custom_css = stripslashes($pavoboard_custom_css);
		}
		
		$ebbsmate_custom_css_contents = "@charset 'utf-8';
		
/* font */
div.pavoboard-wrapper.pavoboard-custom,
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table tbody tr td.pavoboard-list-date,
div.pavoboard-wrapper.pavoboard-custom .pagingNav a,
div.pavoboard-wrapper.pavoboard-custom .noti-td .noti-icon , div.pavoboard-wrapper.pavoboard-custom div.setting-content.preview{
	".$basic_font_family."
}
div.pavoboard-wrapper.pavoboard-custom {
	font-size: 12px;
	color: #424242;
}
		
/* board-head */
/* div.pavoboard-wrapper.pavoboard-custom .head-st {
	background-color: ;
	background-image: ;
	background-repeat: ;
	background-position: ;
	border-width: ;
	border-style: ;
	border-color: ;
	border-radius: ;
	font-size: ;
	color: ;
	opacity: ;
} */
		
/* board */
div.pavoboard-wrapper.pavoboard-custom span.entry_comment_count ,
div.pavoboard-wrapper.pavoboard-custom span.icon-reply,
div.pavoboard-wrapper.pavoboard-custom .pagingNav strong,
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table tbody tr td.pavoboard-list-title a:hover {
	color: ".$point_color.";
}
div.pavoboard-wrapper.pavoboard-custom .pagingNav strong {
	border-color: ".$point_color.";
}
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table tbody tr td.pavoboard-list-title a:hover {
	opacity: 0.8;
}
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table tbody tr td {
	border-style: ".$post_line_style.";
	border-color: ".$post_line_color.";
	padding-top: ".$post_line_margin.";
	padding-bottom: ".$post_line_margin.";
}
		
/* board-head */
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table thead tr th {
	border-width: ".$header_border_width.";
	border-color: ".$header_border_color.";
	background: ".$header_background_color.";
}
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table thead tr th span {
	font-size: ".$header_font_size.";
	text-decoration: ".$header_font_underline.";
	font-style: ".$header_font_italic.";
	font-weight: ".$header_font_bold.";
	border-left-color: ".$header_division_color.";
	color: ".$header_font_color.";
}
		
/* board-noti */
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table tbody tr td.noti-td {
	background: ".$notice_background_color.";
	/* opacity: ; */
}
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table > tbody > tr > td.pavoboard-list-title.noti-td > a {
	text-decoration: ".$notice_font_underline.";
	font-style: ".$notice_font_italic.";
	font-weight: ".$notice_font_bold.";
	color: ".$notice_title_color.";
}
div.pavoboard-wrapper.pavoboard-custom .noti-td .noti-icon {
	background: ".$notice_icon_background_color.";
	border-radius: ".$notice_icon_border_radius.";
	font-size: 12px;
	font-weight: bold;
	color: ".$notice_icon_color.";
	/* opacity: ; */
}
		
/* board-body*/
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table > tbody > tr > td.pavoboard-list-title > a {
	font-size: 13px;
	text-decoration: ".$link_font_underline.";
	font-style: ".$link_font_italic.";
	font-weight: ".$link_font_bold.";
	color: ".$link_font_color.";
}
		
/* button */
div.pavoboard-wrapper.pavoboard-custom .pavoboard-button {
	background: ".$button_bg_color.";
	border-color: ".$button_border_color.";
	color: ".$button_text_color.";
	font-size: 12px;
	font-weight: bold;
	border-radius: ".$button_round_px.";
}
		
div.pavoboard-wrapper.pavoboard-custom table#pavoboard-table tbody tr:hover {
	background-color: ".$post_mouseover_color.";
}
		
div.pavoboard-wrapper .ebbsmate-section-wrapper a.section_tab{
	background: ".$section_tab_bg_color.";
    border: 1px solid;
	border-color: ".$section_tab_border_color.";
    border-radius: ".$section_tab_radius_px.";
    box-shadow: none;
    color: ".$section_tab_text_color.";
    cursor: pointer;
    display: inline-block;
    font-size: 11px !important;
    line-height: 11px;
    padding: 7px 10px 6px !important;
    text-decoration: none;
    vertical-align: top;
    width: auto;
}
		
div.pavoboard-wrapper .ebbsmate-section-wrapper a.section_tab.active {
    background: ".$section_activetab_bg_color.";
    color: ".$section_activetab_text_color.";
}
/* 위 CSS는 수정 불가합니다. 여기 아래서부터 추가 CSS를 입력할 수 있습니다. */
".$pavoboard_custom_css;
		
		$plugin_dir = PavoBoardMate::$PLUGIN_DIR. "css/board/".$file_name.".css";
		$fp = fopen($plugin_dir,"w");
		fwrite($fp, $ebbsmate_custom_css_contents);
		fclose($fp);
		
		return $plugin_dir;
	}
}