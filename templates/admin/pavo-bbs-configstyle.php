<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! empty( $_GET['style-created'] ) ) : ?>
<div id="message" class="updated notice is-dismissible"><p><?php
	_e('CSS 파일이 저장되었습니다.');
?>
</p></div>
<?php endif;
 
if ( ! empty( $_GET['style-deleted'] ) ) : ?>
<div id="message" class="updated notice is-dismissible"><p><?php
	_e('CSS 파일이 삭제되었습니다.');
?>
</p></div>
<?php endif;

if ( ! empty( $_GET['style-updated'] ) ) : ?>
<div id="message" class="updated notice is-dismissible"><p><?php
	_e('CSS 파일이 수정되었습니다.');
?>
</p></div>
<?php endif;

if ( ! empty( $_GET['style-copied'] ) ) : ?>
<div id="message" class="updated notice is-dismissible"><p><?php
	_e('CSS 파일이 복사되었습니다.');
?>
</p></div>
<?php endif;

if(!empty($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = "";
}

if($action == "editstyle" || $action == "newstyle") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-manageStyle.php';
// 	include plugin_dir_path( __FILE__ ).'pavo-bbs-editstyle.php';
} else {
global $wpdb;

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

$default_dir = str_replace('\\', '/', $default_dir);

sort($file_list);

//게시판별 사용중 
$use_style_sql = "select post_title, p.id , m.meta_value
from $wpdb->posts p, $wpdb->postmeta m
where p.ID = m.post_id
and p.post_type = 'ebbsboard'
and m.meta_key = 'ebbsmate_css_style'
order by m.meta_value desc";

$use_style_list = $wpdb->get_results($use_style_sql);

?>
<div class="wrap style-config-wrap">
	<h2><?php _e('스타일 관리')?>
	<a class="add-new-h2" href="<?php echo admin_url('admin.php?page=ebbsmate_style_config');?>&action=newstyle">새 스타일 만들기</a>
	</h2>
	<!-- 테이블 시작 -->
	<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th class="manage-column" scope="col"><?php _e('CSS 파일명')?></th>
				<th class="manage-column" scope="col"><?php _e('사용중인 게시판')?></th>
				<th class="manage-column" scope="col"><?php _e('액션')?></th>
    		</tr>
		</thead>		
		<tbody id="the-list">
		<?php if(in_array('default_style.css' , $file_list) && file_exists($default_dir."default_style.css")){?>
			<tr class="ebbs-style-list" data-filename="default_style">
				<td class="post-title page-title column-title">
					<strong>
					<a href="<?php echo admin_url('admin.php?page=ebbsmate_style_config')."&action=editstyle&filename=default_style" ?>" class="row-title" title="">default_style.css</a>
					</strong>
					- <?php _e("기본 CSS 파일") ?>
				</td>
				<td>
				<?php 
				$index = 0;
				foreach ($use_style_list as $use_style){
					if($use_style->meta_value == 'default_style'){
						if($index > 0) echo " , ";
						echo sprintf("<a href='%s'>%s</a>",admin_url('admin.php?page=ebbsmate_board')."&action=editboard&board_id=".$use_style->id,$use_style->post_title);
						$index ++;
					}
				}
				if(!$index) echo " - ";
				?>
				</td>
				<td>
					<input class="button style_edit" type="button" value="<?php esc_attr_e('Edit')?>" >
					<input class="button style_copy" type="button" value="<?php esc_attr_e('Copy')?>" >				
				</td>
				<input type="hidden" name="css_file_name" value="<?php echo $default_dir."default_style" ?>">
			</tr>
		<?php 
		}
			
			foreach ($file_list as $filename) { 
		      $fileval = str_replace(".css", "", $filename);
		      
		      if($fileval !== "default_style") {
			?>
			<tr class="ebbs-style-list" data-filename='<?php echo str_replace(".css", "", $filename)?>'>
				<td class="post-title page-title column-title">
					<strong>
					<a href="<?php echo admin_url('admin.php?page=ebbsmate_style_config')."&action=editstyle&filename=".$fileval?>" class="row-title" title=""><?php echo $filename; ?></a>
					</strong>
				</td>
				<td>
				<?php 
				$index = 0;
				foreach ($use_style_list as $use_style){
					if($use_style->meta_value == $fileval){
						if($index > 0) echo " , ";
						echo sprintf("<a href='%s'>%s</a>",admin_url('admin.php?page=ebbsmate_board')."&action=editboard&board_id=".$use_style->id,$use_style->post_title);
						$index ++;
					}
				}
				
				if(!$index) echo " - ";
				?>
				</td>
				<td>
				<?php 
				if(!empty($result)) {
				?>
				<input class="button style_edit" type="button" value="<?php esc_attr_e('Edit')?>" >
				<input class="button style_copy" type="button" value="<?php esc_attr_e('Copy')?>" >
				<?php
				} else {
				?>
				<input class="button style_edit" type="button" value="<?php esc_attr_e('Edit')?>" >
				<input class="button style_copy" type="button" value="<?php esc_attr_e('Copy')?>" >
				<input class="button style_delete" type="button" value="<?php esc_attr_e('Delete')?>" >
				<?php
				}
				?>
				</td>
			<input type="hidden" name="css_file_name" value="<?php echo $default_dir."".$filename?>">
			</tr>
			<?php }
			 }?>
			
		</tbody>
	</table>
	<!-- 테이블 끝 -->	
</div>
<!-- CSS 파일 복사 팝업 시작 -->
<div name="ebbsmate_stylecopy_popup" class="metabox-holder" style="display: none;">
	<div class="postbox " style="position:absolute; top:35%; left: 35%; width: 24%; height: 17%; background-color: white;">
		<div class="handlediv" title="Click to toggle">
			<br>
		</div>
		<h3 class="hndle"><span>CSS 파일명 입력</span></h3>
		<div class="inside">
			<div style="padding: 8px 12px 10px;">
				<table>
					<tr>
						<td>
							<input type="text" name="ebbsmate_stylecopy_name" maxlength="20">.css
						</td>
						<input type="hidden" name="ebbsmate_stylecopy_original">
					</tr>
					<tr>
						<td>
							<input class="button copy_submit" type="button" value="확인" />
							<input class="button copy_cancel" type="button" value="취소" />
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- CSS 파일 복사 팝업 끝 -->
<?php }?>
