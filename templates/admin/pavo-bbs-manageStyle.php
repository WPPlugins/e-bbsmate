<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! empty( $_GET['settings-updated'] ) ) : ?>
<div id="message" class="updated notice is-dismissible"><p>
<?php
_e('CSS 파일이 수정되었습니다.');
?>
</p></div>
<?php 
endif;

$preview_file = dirname(__FILE__).'/pavo-bbs-style-preview.php';

$action = empty($_GET['action'])? "" : $_GET['action'];
$board_id = empty($_GET['board_id'])? "" : $_GET['board_id'];

include PavoBoardMate::$PLUGIN_DIR.'include/board-color.php';

if($action == "editstyle"){
	wp_enqueue_style('ebbsmate-custom-css', PavoBoardMate::$PLUGIN_URL.'css/board/'.$css_file_name.'.css');
}
?>
<div class="pavobbs-wrap wrap">
	<div class="head-area">
		<div class="pavoboard-logo">
            <a href="https://www.netville.co.kr/"><img src="<?php echo PavoBoardMate::$PLUGIN_URL?>images/ebbsmate_logo.png"></a>
        </div>	
	</div>
	<form id="ebbsmate_style_form" method="POST" action="<?php echo admin_url('admin.php?page=ebbsmate_style_config')."&action=insertstyle"?>">
	<div class="pavoboard-wrapper pavoboard-custom">
		<div class="pavoboard-preview">
			<div class="settings-panel">
				<div class="setting-title"><?php _e("CSS 파일명","ebbsmate") ?></div>
				<div class="setting-content">
				<?php if($action == "editstyle"){?>
					<span style="margin-right:30px;"><?php echo $css_file_name?>.css</span>
					<input type="hidden" name="css_file_name" value="<?php echo $css_file_name?>"/>
				<?php }else{?>
					<span style="margin-right: 30px;"><input type="text" id="ebbsmate_css_file_name" name="css_file_name" placeholder="파일명 입력(알파벳 소문자만)" maxlength="30" size="30"/>.css</span>
				<?php }?>
					<input class="create-style" type="button" value="<?php _e("스타일 저장하기","ebbsmate") ?>" class="button">
				<?php if(empty($board_id)){?>
				    <input class="create-cancel" type="button" onclick="window.location='<?php echo admin_url('admin.php?page=ebbsmate_style_config')?>';" value="<?php _e("취소","ebbsmate") ?>" class="button">
				<?php }else{?>
					<input class="create-cancel" type="button" onclick="window.location='<?php echo admin_url('admin.php?page=ebbsmate_board')."&action=editboard&board_id=".$board_id ?>';" value="<?php _e("취소","ebbsmate") ?>" class="button">
				<?php }?>
				</div>
			</div>
		<div class="settings-panel">
			<div class="setting-title"><?php _e("스타일 미리보기","ebbsmate") ?></div>
			<div class="setting-content preview">
			<?php require_once($preview_file);?>
			</div>
		</div>
		</div> <!-- E:pavoboard-preview -->
		
		<div class="pavoboard-dashboard" style="font-family: '돋움',Dotum,Gulim,Helvetica,Arial,sans-serif;">
			<div class="settings-panel preface-setting active">
				<div class="setting-title">
					<?php _e("공통 설정","ebbsmate")?>
					<div style="float:right" class="handlediv" title="토글하려면 클릭하세요"><br></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("포인트 색상","ebbsmate") ?></label>
						<p><?php _e("목록 마우스 오버, 페이지 목록 , 댓글 갯수, 답글 아이콘의 공통 색상 설정 입니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div id="point_color_colpick" class="color-box pavobbs-style" style_target="point_color" style_type="point_color"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="pavoboard_point_color" style="width: 35%;" value="<?php echo $point_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="settings-panel button-setting">
				<div class="setting-title">
					<?php _e("분류 설정","ebbsmate") ?>
					<div style="float:right" class="handlediv" title="토글하려면 클릭하세요"><br></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("분류 탭 라운드","ebbsmate") ?></label>
						<p><?php _e("버튼의 라운드를 설정할수 있습니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<span style="margin-right: 20px; font-weight: bold">좁게</span>
						<div class="pavo-style-slider" min-data="0" max-data="15" start-data="<?php echo $section_tab_radius_px?>" style="width: 60%; display: inline-block;"></div>
						<span style="margin-left: 7px; font-weight: bold">넓게</span>
						<span class="pavo-style-slider-value" style_type="border-radius" style_target="div.ebbsmate-section-wrapper a.section_tab" style="margin-left: 160px;"></span>
					</div>
					<input type="hidden" name="section_tab_radius_px" value="<?php echo $section_tab_radius_px?>">
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("분류 탭 테두리 색상","ebbsmate") ?></label>
						<p><?php _e("분류 탭의 테두리 색상을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="border-color" style_target="ebbsmate-section-wrapper a.section_tab"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="section_tab_border_color" style="width: 35%;" value="<?php echo $section_tab_border_color ?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("분류 탭 배경 색상","ebbsmate") ?></label>
						<p><?php _e("버튼의 배경 색상을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="background" style_target="ebbsmate-section-wrapper a.section_tab"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="section_tab_bg_color" style="width: 35%;" value="<?php echo $section_tab_bg_color ?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("분류 탭 글자 색상","ebbsmate") ?></label>
						<p><?php _e("버튼의 글자 색상을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="color" style_target="ebbsmate-section-wrapper a.section_tab"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="section_tab_text_color" style="width: 35%;" value="<?php echo $section_tab_text_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("분류 활성탭 배경 색상","ebbsmate") ?></label>
						<p><?php _e("버튼의 배경 색상을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="background" style_target="ebbsmate-section-wrapper a.section_tab.active"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="section_activetab_bg_color" style="width: 35%;" value="<?php echo $section_activetab_bg_color ?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("분류 활성탭 글자 색상","ebbsmate") ?></label>
						<p><?php _e("버튼의 글자 색상을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="color" style_target="ebbsmate-section-wrapper a.section_tab.active"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="section_activetab_text_color" style="width: 35%;" value="<?php echo $section_activetab_text_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="settings-panel preface-setting">
				<div class="setting-title">
					<?php _e("헤더 설정","ebbsmate") ?>
					<div style="float:right" class="handlediv" title="토글하려면 클릭하세요"><br></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("헤더 배경 색상","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_target="header_thead th" style_type="background"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="header_background_color" style="width: 35%;" value="<?php echo $header_background_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("헤더 구분선 색상","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_target="header_thead span" style_type="border-left-color"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="header_division_color" style="width: 35%;" value="<?php echo $header_division_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("헤더 테두리 색상","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_target="header_thead th" style_type="border-color"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="header_border_color" style="width: 35%;" value="<?php echo $header_border_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("헤더 테두리 두께","ebbsmate") ?></label>
						<p><?php _e("게시판 헤더의 테두리 두께를 설정할 수 있습니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<span style="margin-right: 20px; font-weight: bold">좁게</span>
						<div class="pavo-style-slider" min-data="0" max-data="30" start-data="<?php echo $header_border_width?>" style="width: 60%; display: inline-block;"></div>
						<span style="margin-left: 7px; font-weight: bold">넓게</span>
						<span class="pavo-style-slider-value" style_type="border-width" style_target=".header_thead th" style="margin-left: 160px;"></span>
					</div>
					<input type="hidden" name="header_border_width" value="<?php echo $header_border_width?>">
					<div class="clear"></div>
				</div>
			</div>
			<div class="settings-panel preface-setting">
				<div class="setting-title">
					<?php _e("공지 설정","ebbsmate") ?>
					<div style="float:right" class="handlediv" title="토글하려면 클릭하세요"><br></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("공지 배경 색상","ebbsmate") ?></label>
						<p><?php _e("공지 글의 배경 색상을 설정할수 있습니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_target="noti-td" style_type="background-color"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="notice_background_color" style="width: 35%;" value="<?php echo $notice_background_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<!-- S:공지 텍스트  -->
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("공지 아이콘 글씨 색상","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="color" style_target="noti-td .noti-icon"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="notice_icon_color" style="width: 35%;" value="<?php echo $notice_icon_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("공지 아이콘 라운드","ebbsmate") ?></label>
						<p><?php _e("공지 아이콘의 라운드를 설정할수 있습니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<span style="margin-right: 20px; font-weight: bold">좁게</span>
						<div class="pavo-style-slider" min-data="0" max-data="30" start-data="<?php echo $notice_icon_border_radius?>" style="width: 60%; display: inline-block;"></div>
						<span style="margin-left: 7px; font-weight: bold">넓게</span>
						<span class="pavo-style-slider-value" style_type="border-radius" style_target=".noti-icon" style="margin-left: 160px;"></span>
					</div>
					<input type="hidden" name="notice_icon_border_radius" value="<?php echo $notice_icon_border_radius?>">
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("공지 아이콘 배경 색상","ebbsmate") ?></label>
						<p><?php _e("공지 아이콘의 배경 색상을 설정할수 있습니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="background" style_target="noti-td .noti-icon" ></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="notice_icon_background_color" style="width: 35%;" value="<?php echo $notice_icon_background_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("공지 제목 색상","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="color" style_target="noti-td > a"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="notice_title_color" style="width: 35%;" value="<?php echo $notice_title_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("공지 텍스트 꾸미기","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<input type="checkbox" name="notice_font_bold" value="bold" onclick="javascript:font_decoration_preview();" <?php checked($notice_font_bold, "bold")?>><?php _e("진하게","ebbsmate") ?>
						<input type="checkbox" name="notice_font_italic" value="italic" onclick="javascript:font_decoration_preview();" <?php checked($notice_font_italic, "italic")?>><?php _e("기울임","ebbsmate") ?>
						<input type="checkbox" name="notice_font_underline" value="underline" onclick="javascript:font_decoration_preview();" <?php checked($notice_font_underline, "underline")?>><?php _e("밑줄","ebbsmate") ?>
					</div>
					<div class="clear"></div>
				</div>
				<!-- E:공지 텍스트  -->
			</div>
			<div class="settings-panel text-setting">
				<div class="setting-title">
					<?php _e("텍스트 설정","ebbsmate") ?>
					<div style="float:right" class="handlediv" title="토글하려면 클릭하세요"><br></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("폰트 설정","ebbsmate") ?></label>
						<p><?php _e("웹폰트를 사용하여 게시판의 폰트를 변경할수 있습니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<select name="basic_font">
							<option value="default" <?php selected( $basic_font_family, "default" ); ?>>기본 폰트</option>
							<option value="nanumgothic" <?php selected( $basic_font_family, "nanumgothic" ); ?>>나눔고딕체</option>
							<option value="nanumbrushscript" <?php selected( $basic_font_family, "nanumbrushscript" ); ?>>나눔붓글씨체</option>
							<option value="nanumcoding" <?php selected( $basic_font_family, "nanumcoding" ); ?>>나눔코딩체</option>
							<option value="nanummyeongjo" <?php selected( $basic_font_family, "nanummyeongjo" ); ?>>나눔명조체</option>
							<option value="nanumpenscript" <?php selected( $basic_font_family, "nanumpenscript" ); ?>>나눔펜글씨체</option>
							<option value="jejumyeongjo" <?php selected( $basic_font_family, "jejumyeongjo" ); ?>>제주명조체</option>
							<option value="kopubbatang" <?php selected( $basic_font_family, "kopubbatang" ); ?>>KoPub 바탕체</option>
							<option value="notosanskr" <?php selected( $basic_font_family, "notosanskr" ); ?>>Noto Sans KR</option>
							<option value="hanna" <?php selected( $basic_font_family, "hanna" ); ?>>한나체</option>
							<option value="jejugothic" <?php selected( $basic_font_family, "jejugothic" ); ?>>제주고딕체</option>
							<option value="jejuhallasan" <?php selected( $basic_font_family, "jejuhallasan" ); ?>>제주한라산체</option>
						</select>
					</div>
					<div class="clear"></div>
				</div>
				<!-- S:헤더 텍스트  -->
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("헤더 텍스트 색상","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_target="header_thead span" style_type="color"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="header_font_color" style="width: 35%;" value="<?php echo $header_font_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("헤더 텍스트 사이즈","ebbsmate") ?></label>
						<p><?php _e("게시판 헤더의 텍스트 크기를 변경할수 있습니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<span style="margin-right: 20px; font-weight: bold">작게</span>
						<div class="pavo-style-slider" min-data="10" max-data="25" start-data="<?php echo $header_font_size?>" style="width: 60%; display: inline-block;"></div>
						<span style="margin-left: 7px; font-weight: bold">크게</span>
						<span class="pavo-style-slider-value" style_type="font-size" style_target="div.pavoboard-wrapper table#pavoboard-table thead tr th span" style="margin-left: 160px;"></span>
					</div>
					<input type="hidden" name="header_font_size" value="<?php echo $header_font_size?>">
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("헤더 텍스트 꾸미기","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<input type="checkbox" name="header_font_bold" value="bold" onclick="javascript:font_decoration_preview()" <?php checked($header_font_bold, "bold")?>><?php _e("진하게","ebbsmate") ?>
						<input type="checkbox" name="header_font_italic" value="italic" onclick="javascript:font_decoration_preview()" <?php checked($header_font_italic, "italic")?>><?php _e("기울임","ebbsmate") ?>
						<input type="checkbox" name="header_font_underline" value="underline" onclick="javascript:font_decoration_preview()" <?php checked($header_font_underline, "underline")?>><?php _e("밑줄","ebbsmate") ?>
					</div>
					<div class="clear"></div>
				</div>
				<!-- E:헤더 텍스트  -->
				<!-- S:링크 텍스트  -->
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("링크 텍스트 색상","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="color" style_target="link_font_color"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="link_font_color" style="width: 35%;" value="<?php echo $link_font_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("링크 텍스트 꾸미기","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<input type="checkbox" name="link_font_bold" value="bold" onclick="javascript:font_decoration_preview();" <?php checked($link_font_bold, "bold")?>><?php _e("진하게","ebbsmate") ?>
						<input type="checkbox" name="link_font_italic" value="italic" onclick="javascript:font_decoration_preview();" <?php checked($link_font_italic, "italic")?>><?php _e("기울임","ebbsmate") ?>
						<input type="checkbox" name="link_font_underline" value="underline" onclick="javascript:font_decoration_preview();" <?php checked($link_font_underline, "underline")?>><?php _e("밑줄","ebbsmate") ?>
					</div>
					<div class="clear"></div>
				</div>
				<!-- E:링크 텍스트  -->
			</div>
			<div class="settings-panel line-setting">
				<div class="setting-title">
					<?php _e("라인 설정","ebbsmate") ?>
					<div style="float:right" class="handlediv" title="토글하려면 클릭하세요"><br></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("라인 스타일","ebbsmate") ?></label>
						<p><?php _e("게시물 중간 라인 스타일을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<select name="line_title_bottom" class="line_title_bottom">
							<option value="solid" <?php selected($post_line_style, "solid")?>><?php _e("실선","ebbsmate") ?></option>
							<option value="dotted" <?php selected($post_line_style, "dotted")?>><?php _e("점선","ebbsmate") ?></option>
							<option value="dashed" <?php selected($post_line_style, "dashed")?>><?php _e("긴 점선","ebbsmate") ?></option>
						</select>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("라인 색상","ebbsmate") ?></label>
						<p><?php _e("게시물 중간 라인 스타일을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="border-color" style_target="pavoboard-table tbody tr td"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="post_line_color" style="width: 35%;" value="<?php echo $post_line_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("마우스 오버 색상","ebbsmate") ?></label>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="mouseover_color" style_target="mouseover_color"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="post_mouseover_color" style="width: 35%;" value="<?php echo $post_mouseover_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("텍스트 간격","ebbsmate") ?></label>
						<p><?php _e("텍스트의 위 아래 여백을 설정할 수 있습니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<span style="margin-right: 20px; font-weight: bold">좁게</span>
						<div class="pavo-style-slider"  min-data="0" max-data="50" start-data="<?php echo $post_line_margin?>" style="width: 60%; display: inline-block;"></div>
						<span style="margin-left: 7px; font-weight: bold">넓게</span>
						<span class="pavo-style-slider-value" style_type="padding" style_target=".pavoboard-list-number" style="margin-left: 160px;"></span>
					</div>
					<input type="hidden" name="post_line_margin" value="<?php echo $post_line_margin?>">
					<div class="clear"></div>
				</div>
			</div>
			<div class="settings-panel button-setting">
				<div class="setting-title">
					<?php _e("버튼 설정","ebbsmate") ?>
					<div style="float:right" class="handlediv" title="토글하려면 클릭하세요"><br></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("버튼 라운드","ebbsmate") ?></label>
						<p><?php _e("버튼의 라운드를 설정할수 있습니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<span style="margin-right: 20px; font-weight: bold">좁게</span>
						<div class="pavo-style-slider" min-data="0" max-data="15" start-data="<?php echo $button_round_px?>" style="width: 60%; display: inline-block;"></div>
						<span style="margin-left: 7px; font-weight: bold">넓게</span>
						<span class="pavo-style-slider-value" style_type="border-radius" style_target="div.pavoboard-wrapper .pavoboard-button" style="margin-left: 160px;"></span>
					</div>
					<input type="hidden" name="button_round_px" value="<?php echo $button_round_px?>">
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("버튼 테두리 색상","ebbsmate") ?></label>
						<p><?php _e("버튼의 테두리 색상을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="border-color" style_target="pavoboard-button"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="button_border_color" style="width: 35%;" value="<?php echo $button_border_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("버튼 배경 색상","ebbsmate") ?></label>
						<p><?php _e("버튼의 배경 색상을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="background-color" style_target="pavoboard-button"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="button_bg_color" style="width: 35%;" value="<?php echo $button_bg_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc">
						<label for="pyre_main_top_padding"><?php _e("버튼 글자 색상","ebbsmate") ?></label>
						<p><?php _e("버튼의 글자 색상을 변경합니다.","ebbsmate") ?></p>
					</div>
					<div class="setting_field">
						<div class="color-box pavobbs-style" style_type="color" style_target="pavoboard-button"></div>
						<div style="margin-top:6px; width:200px;float:right;">
							<input type="text" name="button_text_color" style="width: 35%;" value="<?php echo $button_text_color?>">
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<!-- 추가 커스텀 CSS 설정 -->
			<div class="settings-panel button-setting">
				<div class="setting-title">
					<?php _e("커스텀 CSS 설정","ebbsmate") ?>
					<div style="float:right" class="handlediv" title="토글하려면 클릭하세요"><br></div>
				</div>
				<div class="setting-field">
					<div class="setting_desc" style="width: 100%;">
						<label for="pyre_main_top_padding"><?php _e("커스텀 CSS","ebbsmate") ?></label>
						<p><?php _e("아래의 입력란에 CSS를 추가로 입력해서 게시판 스타일을 변경할 수 있습니다. 단, 미리보기에는 적용되지 않습니다.","ebbsmate") ?></p>
					</div>
					<div class="clear"></div>
					<textarea name="pavoboard_custom_css" style="width: 100%; height: 200px;"><?php echo $ebbsmate_css_content?></textarea>
				</div>
			</div>
			<input type="hidden" name="css_style_action" value="<?php echo $action?>"/>
			<input type="hidden" name="board_id" value="<?php echo $board_id?>"/>
		</div> <!-- E:pavoboard-dashboard -->
	</div> <!-- E:pavoboard-dashboard -->
	</form>
</div>
