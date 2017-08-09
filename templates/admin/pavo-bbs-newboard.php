<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! current_user_can( 'manage_options' ) ) {
	$return = new WP_Error( 'broke', __( "권한이 없습니다." ) );
	echo $return->get_error_message();
	return ;
}

global $wpdb;

$users = array();
$sql = "select user_login
		from wp_users";

$users = $wpdb->get_col($sql);

function js_str($s)
{
	return '"' . addcslashes($s, "\0..\37\"\\") . '"';
}

function js_array($array)
{
	$temp = array_map('js_str', $array);
	return '[' . implode(',', $temp) . ']';
}
?>
<script>
jQuery(document).ready(function($) {
	
	$(function() { 
		var availableTags = <?php echo js_array($users)?>;
		    
		function split( val ) {
			return val.split( /,\s*/ );
		}

		function extractLast( term ) {
		   return split( term ).pop();
		}

		$( "#ebbsmate_admin_ids" )
			// don't navigate away from the field on tab when selecting an item
		    .bind( "keydown", function( event ) {	    	  
		    if ( event.keyCode === $.ui.keyCode.TAB &&
		    	$( this ).autocomplete( "instance" ).menu.active ) {
		        	event.preventDefault();
		        }
		    })
		    .autocomplete({
		    	minLength: 0,
		        source: function( request, response ) {
		          // delegate back to autocomplete, but extract the last term
		          response( $.ui.autocomplete.filter(
		            availableTags, extractLast( request.term ) ) );
		        },
		        focus: function() {
		          // prevent value inserted on focus
		          return false;
		        },
		        select: function( event, ui ) {
		          var terms = split( this.value );
		          // remove the current input
		          terms.pop();
		          // add the selected item
		          terms.push( ui.item.value );
		          // add placeholder to get the comma-and-space at the end
		          terms.push( "" );
		          this.value = terms.join( ", " );
		          return false;	        
		          }
		});
	});
	
});
</script>

	
<?php 
if ( ! empty( $_GET['settings-updated'] ) ) : ?>
<div id="message" class="updated notice is-dismissible"><p><?php
	_e('게시판이 생성되었습니다.');
?>
</p></div>
<?php endif;?>
<?php
//스타일 정보 가져오기
$board_style_settings = get_option('pavo_board_style_settings');

$basic_font_color    = $board_style_settings ['basic_font_color'];
$basic_link_color    = $board_style_settings ['basic_link_color'];
$notice_font_color   = $board_style_settings ['notice_font_color'];
$link_font_underline = $board_style_settings ['link_font_underline'];
$basic_border_color  = $board_style_settings ['basic_border_color'];
$tb_border_strong    = $board_style_settings ['tb_border_strong'];
$basic_bg_color      = $board_style_settings ['basic_bg_color'];
$tb_bottom_bg_color  = $board_style_settings ['tb_bottom_bg_color'];
$basic_margin_px     = $board_style_settings ['basic_margin_px'];

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

global $wpdb;

$table_name= $wpdb->prefix."postmeta";

$max_board_id = $wpdb->get_var("SELECT MAX(meta_value+1) AS max_board_id
		FROM ".$table_name."
		WHERE meta_key='ebbsmate_board_id'");

$max_widget_id = $wpdb->get_var("SELECT MAX(meta_value+1) AS max_board_id
		FROM ".$table_name."
		WHERE meta_key='ebbsmate_widget_id'");
?>
<div class="ebbs-admin-wrapper">
	<h2>새 게시판 만들기</h2>
	<form id="ebbsmate_board_create_form" method="POST" action="<?php echo admin_url('admin.php?page=ebbsmate_board')."&action=insertboard"?>">
	<?php wp_nonce_field( 'ebbsmate_newboard', 'ebbsmate_board_nonce'); ?>
	<input type="hidden" name="basic_font_color" value="<?php echo $basic_font_color;?>"/>
	<input type="hidden" name="basic_link_color" value="<?php echo $basic_link_color;?>"/>
	<input type="hidden" name="notice_font_color" value="<?php echo $notice_font_color;?>"/>
	<input type="hidden" name="link_font_underline" value="<?php echo $link_font_underline;?>"/>
	<input type="hidden" name="basic_border_color" value="<?php echo $basic_border_color;?>"/>
	<input type="hidden" name="tb_border_strong" value="<?php echo $tb_border_strong;?>"/>
	<input type="hidden" name="basic_bg_color" value="<?php echo $basic_bg_color;?>"/>
	<input type="hidden" name="tb_bottom_bg_color" value="<?php echo $tb_bottom_bg_color;?>"/>
	<input type="hidden" name="basic_margin_px" value="<?php echo $basic_margin_px;?>"/>

	<div style="float: left; width:70%;">
		<table class="form-table" style="display:<?php if($tab=="basic" || $tab == ""){echo "block";} else {echo "none";}?>">
			<tr>
				<td style="padding: 0px 0px; line-height: 0.7;">
					<div id="titlediv" style="">
						<div id="titlewrap">
							<input name="ebbsmate_board_name" class="pavo_board_name" id="title" placeholder="게시판 제목을 입력하십시오." spellcheck="true" type="text" size="200" value="" autocomplete="off">
						</div>
					</div>
				</td>
			</tr>
		</table>
		<div id="dashboard-widgets" class="metabox-holder">
			<!-- <div id="dashboard_right_now" class="postbox ">
				<div class="handlediv" title="Click to toggle">
					<br>
				</div>
				<h3 class="hndle"><span>머릿말</span></h3>
				<div class="inside" style="padding: 8px 12px 10px;">
					<table>
					 	<tr>
							<th scope="row" style="width: 330px; text-align: left;">
								머릿말 사용여부
							</th>
							<td>
								<label for="ebbsmate_board_preface_y">
								<input type="radio" id="ebbsmate_board_preface_y" name="ebbsmate_preface_flag" checked="checked" value="1">사용
								</label>
								<label for="ebbsmate_board_preface_n">
								<input type="radio" id="ebbsmate_board_preface_n" name="ebbsmate_preface_flag" style="margin-left: 150px;" value="0">사용하지 않음
								</label>
							</td>
						</tr>
					</table>
					<div class='preface_editor'>
					<?php 
						$content = '';
						$editor_id = 'ebbsmate_board_preface';
						
						wp_editor( $content, $editor_id );
					?>
					</div>
				</div>
			</div>  -->
			<div id="dashboard_right_now" class="postbox">
				<div class="handlediv" title="Click to toggle">
					<br>
				</div>
				<h3 class="hndle"><span>권한</span></h3>
				<div class="inside">
					<div style="border: 1px solid #EEEEEE; margin: 8px; padding: 10px 10px 10px;">
						<table style="width: 100%">
						 	<thead>
						 		<tr>
							 		<th width="32%">사용자 권한</th>
							 		<th width="12%">목록 보기</th>
							 		<th width="12%">읽기</th>
							 		<th width="12%">글 쓰기</th>
							 		<th width="12%">댓글 쓰기</th>
							 		<th width="12%">공지글 쓰기</th>
						 		</tr>
						 	</thead>
						 	<tbody>
						 		<tr>
						 			<td style="text-align: center;"><?php _e('방문자 (Guest)')?></td>
						 			<td style="text-align: center; vertical-align: middle;"><input type="checkbox" name="ebbsmate_list_permission[]" value="guest" checked="checked"></td>
						 			<td style="text-align: center; vertical-align: middle;"><input type="checkbox" name="ebbsmate_read_permission[]" value="guest" checked="checked"></td>
						 			<td style="text-align: center; vertical-align: middle;"><input type="checkbox" name="ebbsmate_write_permission[]" value="guest"></td>
						 			<td style="text-align: center; vertical-align: middle;"><input type="checkbox" name="ebbsmate_comment_permission[]" value="guest"></td>
						 		</tr>
					 		<?php						 		
					 		global $wp_roles;						    
						    $roles = $wp_roles->role_names;
						    
						    $key = array_search('Administrator', $roles);
						    
						    if(!empty($key)) {
						    	unset($roles[$key]);
						    }
						    
						    foreach ($roles as $key=>$role) {
							?>
								<tr>
									<td style="text-align: center;"><?php echo translate_user_role($role)." (".$role.")"?></td>
						 			<td style="text-align: center; vertical-align: middle;"><input type="checkbox" name="ebbsmate_list_permission[]" value="<?php echo $key?>" checked="checked"></td>
						 			<td style="text-align: center; vertical-align: middle;"><input type="checkbox" name="ebbsmate_read_permission[]" value="<?php echo $key?>" checked="checked"></td>
						 			<td style="text-align: center; vertical-align: middle;"><input type="checkbox" name="ebbsmate_write_permission[]" value="<?php echo $key?>" checked="checked"></td>
						 			<td style="text-align: center; vertical-align: middle;"><input type="checkbox" name="ebbsmate_comment_permission[]" value="<?php echo $key?>" checked="checked"></td>
						 			<td style="text-align: center; vertical-align: middle;"><input type="checkbox" name="ebbsmate_notice_permission[]" value="<?php echo $key?>"></td>
								</tr>
							<?php
							}
						    ?>
						 	</tbody>
						</table>
					</div>
					<div class='ebbs-metaboxes-wrapper clear'>
						<table>
						 	<tr>
								<th>
									게시판 관리자
								</th>
								<td>
									<input type="text" id="ebbsmate_admin_ids" name="ebbsmate_admin_ids" style="width: 100%">
									<br/>
									관리자(Admin)와 게시판 관리자는 모든 권한을 가집니다. 별도의 게시판 관리자를 지정할 경우 사용자명을 쉼표(,)로 구분해 입력해 주십시오.
								</td>
							</tr>
						</table>
					</div>
					
					<div class='ebbs-metaboxes-wrapper clear'>
						<table>
						 	<tr>
								<th>
									항목별 읽기 권한설정
								</th>
								<td class="panel">
									<label for="ebbsmate_taxonomy_flag_y">
									<input type="radio" id="ebbsmate_taxonomy_flag_y" name="ebbsmate_section_flag" value="1">사용
									</label>
									<label for="ebbsmate_taxonomy_flag_n">
									<input type="radio" id="ebbsmate_taxonomy_flag_n" name="ebbsmate_section_flag" checked="checked" style="margin-left: 150px;" value="0">사용하지 않음
									</label>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<div class="attributes-meta">
										<textarea placeholder="항목별 구분자 &quot;,&quot; 를 넣어주세요. 이항목의 권한이 최우선 되며 선택된 권한과 관리자만이 읽기 권한을 갖습니다." rows="3" cols="50"></textarea>
										<button class="button save_attributes button-primary get_taxonomy_template" type="button">반영</button>
									</div>
								</td>
							</tr>
							<tr>
								<td></td>
								<td class="ebb-itemized-wrapper panel"></td>
							</tr>
						</table>
					</div>
					
				</div>
			</div>
			
			<div id="dashboard_right_now" class="postbox">
				<div class="handlediv" title="Click to toggle">
					<br>
				</div>
				<h3 class="hndle"><span>부가 기능</span></h3>
				<div class="inside">
					<div style="padding: 8px 12px 10px;">
						<table>
						 	<tr>
								<th scope="row" style="width: 330px; text-align: left;">
									첨부파일
								</th>
								<td>
									<label for="ebbsmate_attach_flag_y">
									<input type="radio" id="ebbsmate_attach_flag_y" name="ebbsmate_attach_flag" checked="checked" value="1">사용
									</label>
									<label for="ebbsmate_attach_flag_n">
									<input type="radio" id="ebbsmate_attach_flag_n" name="ebbsmate_attach_flag" style="margin-left: 150px;" value="0">사용하지 않음
									</label>
									<?php 
									$max_file_attach_size = ini_get("upload_max_filesize");
									$max_file_attach_size = str_replace("M", "", $max_file_attach_size);
									?>
									<span style="margin-left: 100px;">최대   </span><input type="number" name="ebbsmate_attach_size" min="1" max="<?php echo $max_file_attach_size?>" value="<?php echo $max_file_attach_size?>" style="width: 12%;">MByte
									<span style="margin-left: 30px;">최대
										<select name="ebbsmate_attach_item">
											<option value="1">1개</option>
											<option value="2">2개</option>
											<option value="3">3개</option>
											<option value="4">4개</option>
											<option value="5">5개</option>
										</select>	
									</span>
								</td>
							</tr>
							<tr>
								<th scope="row" style="width: 330px; text-align: left;">
									최상단 공지글
								</th>
								<td>
									<label for="ebbsmate_notice_top_flag_y">
									<input type="radio" id="ebbsmate_notice_top_flag_y" name="ebbsmate_notice_top_flag" value="1" checked="checked">사용
									</label>
									<label for="ebbsmate_notice_top_flag_n">
									<input type="radio" id="ebbsmate_notice_top_flag_n" name="ebbsmate_notice_top_flag" value="0" style="margin-left: 150px;">사용하지 않음
									</label>
								</td>
							</tr>
							<tr	style="height: 30px;">
								<th scope="row" style="width: 330px; text-align: left;">
									금칙어 필터링
								</th>
								<td>
									<label for="ebbsmate_word_filtering_y">
									<input type="radio" id="ebbsmate_word_filtering_y" name="ebbsmate_word_filtering" value="1">필터링
									</label>
									<label for="ebbsmate_word_filtering_n">
									<input type="radio" id="ebbsmate_word_filtering_n" name="ebbsmate_word_filtering" value="0" style="margin-left: 137px;" checked="checked">필터링하지 않음
									</label>
								</td>
							</tr>
							<tr	style="height: 30px;">
								<th scope="row" style="width: 330px; text-align: left;">
									비밀글
								</th>
								<td>
									<label for="ebbsmate_secret_post_y">
									<input type="radio" id="ebbsmate_secret_post_y" name="ebbsmate_secret_post" value="1" checked="checked">사용
									</label>
									<label for="ebbsmate_secret_post_n">
									<input type="radio" id="ebbsmate_secret_post_n" name="ebbsmate_secret_post" value="0" style="margin-left: 150px;">사용하지 않음
									</label>
								</td>
							</tr>
							<tr	style="height: 30px;">
								<th scope="row" style="width: 330px; text-align: left;">
									이전글/다음글 목록
								</th>
								<td>
									<label for="ebbsmate_prev_next_post_y">
									<input type="radio" id="ebbsmate_prev_next_post_y" name="ebbsmate_prev_next_post" value="1" checked="checked">사용
									</label>
									<label for="ebbsmate_prev_next_post_n">
									<input type="radio" id="ebbsmate_prev_next_post_n" name="ebbsmate_prev_next_post" value="0" style="margin-left: 150px;">사용하지 않음
									</label>
									<span style="margin-left: 100px;">표시할 글 개수: </span>
									<select name="ebbsmate_prev_next_lines">
										<option value="2" selected="selected">2개</option>
										<option value="3">3개</option>
										<option value="5">5개</option>
									</select>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div id="dashboard_right_now" class="postbox">
				<div class="handlediv" title="Click to toggle">
					<br>
				</div>
				<h3 class="hndle"><span>게시글 항목 설정</span></h3>
				<div class="inside">
					<div style="padding: 8px 12px 10px;">
						<table>
							<tr style="height: 30px;">
								<th scope="row" style="width: 330px; text-align: left;">
									작성자
								</th>
								<td>
									<label for="author_flag_y">
									<input type="radio" id="author_flag_y" name="author_flag" value="1" checked="checked">사용
									</label>
									<label for="author_flag_n">
									<input type="radio" id="author_flag_n" name="author_flag" value="0" style="margin-left: 150px;">사용하지 않음
									</label>
								</td>
							</tr>
							<tr style="height: 30px;">
								<th scope="row" style="width: 330px; text-align: left;">
									작성일
								</th>
								<td>
									<label for="date_flag_y">
									<input type="radio" id="date_flag_y" name="date_flag" value="1" checked="checked">사용
									</label>
									<label for="date_flag_n">
									<input type="radio" id="date_flag_n" name="date_flag" value="0" style="margin-left: 150px;">사용하지 않음
									</label>
								</td>
							</tr>
							<tr style="height: 30px;">
								<th scope="row" style="width: 330px; text-align: left;">
									조회수
								</th>
								<td>
									<label for="vcount_flag_y">
									<input type="radio" id="vcount_flag_y" name="vcount_flag" value="1" checked="checked">사용
									</label>
									<label for="vcount_flag_n">
									<input type="radio" id="vcount_flag_n" name="vcount_flag" value="0" style="margin-left: 150px;">사용하지 않음
									</label>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div id="dashboard_right_now" class="postbox ">
				<div class="handlediv" title="Click to toggle">
					<br>
				</div>
				<h3 class="hndle"><span>레이아웃 및 스타일(CSS)</span></h3>
				<div class="inside">
					<div style="padding: 8px 12px 10px;">
						<table>
						 	<tr>
								<th scope="row" style="width: 330px; text-align: left;">
									목록 글수
								</th>
								<td>
									<select name="ebbsmate_list_lines">
										<option value="10">10줄</option>
										<option value="15">15줄</option>
										<option value="20">20줄</option>
										<option value="30">30줄</option>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row" style="width: 330px; text-align: left;">
									스타일(CSS)
								</th>
								<td>
									<select name="ebbsmate_css_style">	
									<option value="default_style">default_style.css</option>
									<?php foreach ($file_list as $filename) {
										$fileval = str_replace(".css", "", $filename);
										if($fileval !== "default_style") {
											echo "<option value='$fileval'>$filename</option>";
										}
									}?>	
									</select>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	</form>
	
	<div id="dashboard-widgets" class="metabox-holder" style="float: right; width:28%;">
		<div id="dashboard_right_now" class="postbox ">
			<div class="handlediv" title="Click to toggle">
				<br>
			</div>
			<div class="inside">
				<div style="height:150px; margin-left: 25px;">
					<input type="submit" onclick="javascript:board_create();" name="publish" class="button button-primary button-large" id="publish" type="submit" value="게시판 만들기" style="width:95%;height:60%; font-size: 17px;">
				</div>
			</div>
		</div>
		
		<div id="dashboard_right_now" class="postbox ">
				<div class="handlediv" title="Click to toggle">
					<br>
				</div>
				<h3 class="hndle"><span>숏코드 안내</span></h3>
				<div class="inside">
					<div style="padding: 8px 12px 10px;">
						<table>
						 	<tr>
								<td>숏코드</td>
							</tr>
							<tr>
								<td>[ebbsmate id=<?php echo $max_board_id?>]</td>
							</tr>
							<tr>
								<td>위젯 숏코드</td>
							</tr>
							<tr>
								<td>[ebbsmate_widget id=<?php echo $max_widget_id?> rows="5"]</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
	</div>
</div>