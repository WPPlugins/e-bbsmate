<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$board_id = 0;

if(!empty($_GET['parent_id'])) {
	$parent_id = $_GET['parent_id'];
} else {
	$parent_id = "";
}

if(!empty($parent_id)) {
	$parent_title = get_the_title($parent_id);
} else {
	$parent_title = "";
}

//운영중인 게시판 목록 가져오기
$use_board_list= array (
	'post_type' => array('ebbsboard'),
	'post_status' => 'publish',
	'orderby' => 'post_date',
	'order' => 'DESC',
	'meta_query' => array(
		array(
			'key' => 'ebbsmate_status_flag',
			'value' => 1
		)
	)
);

$file_attach_size = (int)ini_get("upload_max_filesize");
$file_attach_size = $file_attach_size*1048576;
wp_enqueue_script('pavoboard-attach', PavoBoardMate::$PLUGIN_URL.'js/front-attachment.js', false);
wp_localize_script( 'pavoboard-attach', 'pavoboard_attach',
	array(
		'file_attach_size' 	=> $file_attach_size,
		'attach_type' 		=> 'insert',
	)
);

?>
<div style="padding:20px; padding-top: 40px; padding-right: 40px;">
  <!-- S:글쓰기 -->
  <div class="post-content">
    <div class="pavoboard-wrapper <?php echo ebbsmate_get_current_theme()?>">
      <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin.php?page=ebbsmate')."&mode=insertpost"?>">
        <h2>게시글 쓰기</h2>
        <div class="pavoboard-write-wrapper">
          <div class="write-box" style="display: <?php if(!empty($parent_id)) { ?>none;<?php }?>">
            <label class="pavoboard-write-title" for="pavo_board_id"><span>게시판 선택<span class="label-required">*</span></span></label>
            <span class="write-span">
            <select id="pavo_board_id" name="pavo_board_id">
					<?php 
					$wp_query = new WP_Query($use_board_list); 
					if ( $wp_query->have_posts() ) :
						while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
						$board_id = empty($board_id)? the_ID() : $board_id;
						?>
						<option value="<?php echo the_ID()?>"><?php echo the_title()?></option>
						<?php 
						endwhile;
					endif;
					?>
			</select>
            </span>
            </div>
            <div class="write-box-devider"></div>
          	<div class="write-box">
            <label class="pavoboard-write-title" for="pavoboard-write-title"><span>제목<span class="label-required">*</span></span></label>
            <span class="write-span">
            	<input type="text" title="제목" style="width:100%" name="pavo_board_post_title" id="pavoboard-write-title" value="<?php if(!empty($parent_id)) { echo $parent_title; }?>">
            </span> 
		  </div>
		  <div class="write-box-devider"></div>
          <div class="write-box">
            <label class="pavoboard-write-title" for=""pavo_section_list""><span>분류항목</span></label>
            <span class="write-span ebbsmate_section_area">
            <?php 
            $board_section_flag = get_post_meta($board_id, "ebbsmate_section_flag", true);
            
            if($board_section_flag){
            	global $current_user;
            	
            	//게시판 분류 Role
            	$board_section = get_post_meta($board_id, 'ebbsmate_section', true);
            	//게시판 관리자
            	$board_admin = get_post_meta($board_id, "ebbsmate_admin_ids", true);
            	$board_admin = empty($board_admin)? '' :$board_admin;
            	$section_list = pavoebbsmate_simple_role_check($current_user, $board_admin, $board_section);
            ?>
            	<select id='pavo_section_list' name='ebbs_section_select'>
            	<option value='all'>전체</option>
            <?php
            
            	foreach ($section_list as $section){
            	?>
            	<option value='<?php echo $section ?>'><?php echo $section ?></option>
            	<?php
            	}
            ?></select><?php
            }else{
            	_e('분류가 설정되지 않은 게시판 입니다.');
            }
            ?>
			
            </span>
          </div>
          <div class="write-box-devider"></div>
          <div class="write-box check-box">
            <label for="input-checkbox-notice"><span>공지사항</span></label>
            <input type="checkbox" title="공지사항" value="1" name="pavo_board_notice_flag" class="input-checkbox" id="input-checkbox-notice" value="1">
          </div>
          <div class="write-box-devider"></div>
          <div class="textarea-box"> <span class="write-span">
			<?php 
			wp_editor($wp_query->post->post_content, 'pavo_board_post_content');
			?>
            </span>
          </div>
          <div class="attach-field">
	          <div class="attach-element hidden">
		          <div class="write-box attach-box">
					<label for="entry-file"><span data-title="첨부파일">첨부파일</span></label>
					<span style="position:relative; width:300px" class="write-span">
						<div class="file-upload">
							<input type="text" style="font-size:12px" readonly="readonly" title="파일 첨부하기" class="text" />
		  	              		<div class="upload-btn">
		  	                		<button title="파일 찾아보기" class="img-upload pavoboard-button" type="button"><span>찾아보기</span></button>
		  	                		<input type="file" name="upload[]" title="파일 찾아보기" class="file" />
		  	              		</div>
		  	            	</div>
						</span>
		          </div>
		          <div class="write-box-devider"></div>
	          </div>
	          <div class="attach-element">
		          <div class="write-box attach-box">
					<label for="entry-file"><span data-title="첨부파일">첨부파일</span></label>
					<span style="position:relative; width:300px" class="write-span">
						<div class="file-upload">
							<input type="text" style="font-size:12px" readonly="readonly" title="파일 첨부하기" class="text" />
		  	              		<div class="upload-btn">
		  	                		<button title="파일 찾아보기" class="img-upload pavoboard-button" type="button"><span>찾아보기</span></button>
		  	                		<input type="file" name="upload[]" title="파일 찾아보기" class="file" />
		  	              		</div>
		  	            	</div>
						</span>
		          </div>
		          <div class="write-box-devider"></div>
	          </div>
	          <a class="pavoboard-button button-prev add-attach" >첨부파일 추가</a>
          <div class="write-box-devider"></div>
        </div>
        <div class="pavoboard-controller"><a class="pavoboard-button button-prev" href="javascript:window.location='<?php echo admin_url('admin.php?page=ebbsmate')?>'"> 취소 </a>
        <button style="float:right" class="pavoboard-button button-save" type="submit"> 확인 </button>
        </div>
        <?php wp_nonce_field( 'ebbsmate_newpost', 'ebbsmate_post_nonce'); ?>
      </form>
    </div>
  </div>
  <!-- E:글쓰기 -->
</div>