<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//nonce_check
if ( empty( $_GET['ebbsmate_post_nonce'] ) || empty( $_GET['post_id'] ) || ( ! wp_verify_nonce( $_GET['ebbsmate_post_nonce'], 'ebbsmate_editpost_'.$_GET['post_id'] ) ) ) {
	echo "<script>
			alert('잘못된 접근입니다.');
			window.history.back()
		</script>";
	return;
}

$post_id = $_GET['post_id'];

$notice_flag = get_post_meta($post_id, "pavo_board_notice_flag", true);
$board_id = get_post_meta($post_id, "pavo_board_origin", true);
//게시판 항목 설정 여부
$board_section_flag = get_post_meta($board_id, "ebbsmate_section_flag", true);

$file_secret_flag = get_post_meta($board_id, "ebbsmate_secret_post", true);
$file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
$file_attach_size = (int)get_post_meta($board_id, "ebbsmate_attach_size", true);
$file_attach_size = $file_attach_size*1048576;
$file_attach_max	= $file_attach_max	= (int)get_post_meta($board_id, "ebbsmate_attach_item", true);
wp_enqueue_script('pavoboard-attach', PavoBoardMate::$PLUGIN_URL.'js/front-attachment.js', false);
wp_localize_script( 'pavoboard-attach', 'pavoboard_attach',
		array(
				'file_attach_size' 	=> $file_attach_size,
				'attach_type' 		=> 'insert',
		)
);

//게시물 상세 정보를 불러온다.
$args= array (
		'post_type' => array('ebbspost'),
		'post_status' => 'publish',
		'p' => $post_id,
);

$wp_query = new WP_Query($args);

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
$board_list = new WP_Query($use_board_list);
?>
<div style="padding:20px; padding-top: 40px; padding-right: 40px;">
  <!-- S:글쓰기 -->
  <div class="post-content admin">
    <div class="pavoboard-wrapper <?php echo ebbsmate_get_current_theme()?>">
      <form method="post" enctype="multipart/form-data" action="<?php echo admin_url("admin.php?page=ebbsmate&mode=update&post_id=".$wp_query->post->ID)?>">
      	<h2>게시글 편집</h2>
      	<?php if($wp_query->have_posts()) :?>
		<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
      	
        <div class="pavoboard-write-wrapper">
          <div class="write-box">
            <label class="pavoboard-write-title" for="pavo_board_id"><span>게시판 선택<span class="label-required">*</span></span></label>
            <span class="write-span">
            <select id="pavo_board_id" name="pavo_board_id">
			<?php if ( $board_list->have_posts() ) : ?>
			<?php while ( $board_list->have_posts() ) : $board_list->the_post(); ?>
				<option value="<?php echo the_ID()?>" <?php if ( $board_id == $board_list->post->ID ) echo 'selected="selected"'; ?>><?php echo the_title()?></option>
				<?php endwhile;?>
			<?php endif;?>
			</select>
            </span>
          </div>
          <div class="write-box-devider"></div>
          <div class="write-box">
            <label class="pavoboard-write-title" for="entry-title"><span>제목<span class="label-required">*</span></span></label>
            <span class="write-span">
            <input type="text" title="제목" style="width:100%" name="pavo_board_post_title" id="entry-title" value="<?php echo $wp_query->post->post_title?>">
            </span>
          </div>
          <div class="write-box-devider"></div>
          <div class="write-box">
            <label class="pavoboard-write-title" for=""pavo_section_list""><span>분류항목</span></label>
            <span class="write-span ebbsmate_section_area">
            <?php 
            if($board_section_flag){
            	global $current_user;
            	
            	//게시판 분류 Role
            	$board_section = get_post_meta($board_id, 'ebbsmate_section', true);
            	//게시판 관리자
            	$board_admin = get_post_meta($board_id, "ebbsmate_admin_ids", true);
            	$board_admin = empty($board_admin)? '' :$board_admin;
            	$post_section = get_post_meta($wp_query->post->ID, "pavo_section_val", true);
            	$section_list = pavoebbsmate_simple_role_check($current_user, $board_admin, $board_section);
            ?>
            	<select id='pavo_section_list' name='ebbs_section_select'>
            	<option value='all'>전체</option>
            <?php
            
            	foreach ($section_list as $section){
            	?>
            	<option value='<?php echo $section ?>' <?php selected( $section, $post_section ); ?>><?php echo $section ?></option>
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
            <input type="checkbox" title="공지사항" value="1" name="pavo_board_notice_flag" class="input-checkbox" id="input-checkbox-notice" <?php checked($notice_flag, '1')?>>
          </div>
          <div class="write-box-devider"></div>
          <div class="write-box-devider"></div>
          <div class="textarea-box"> <span class="write-span">
            <?php wp_editor($wp_query->post->post_content, 'pavo_board_post_content'); ?>
            </span> </div>
          <div class="write-box-devider"></div>
          <?php
          $fileNames = get_post_meta($wp_query->post->ID, "pavo_bbs_file_name", false);
		  
		  $fileCnt = 0;
		  
		  if(!empty($fileNames) && $file_attach_flag) :
		  
		  	$fileCnt = sizeof($fileNames[0]);
			        	        
		  	for($i = 0; $i < sizeof($fileNames[0]); $i++) {
		  ?>
		  <div class="write-box attach-box">
		  	<label class="pavoboard-write-title" for="entry-attach"><span>첨부파일</span></label>
		  	<span class="write-span">
		  		<div class="pavo_attach_file_div" size="<?php echo filesize($curpost_attach[0][$i])?>"><span><?php echo $fileNames[0][$i]?></span> <a class="pavo_delete_attach">x</a></div>
		  	</span>
		  	<span style="position:relative; width:300px; display: none" class="write-span">
	            <div class="file-upload">
	              <input type="text" style="font-size:12px" readonly="readonly" title="파일 첨부하기" class="text">
	              <div class="upload-btn">
	                <button title="파일 찾아보기" class="img-upload pavoboard-button" type="button"><span>찾아보기</span></button>
	                <input type="file" name="upload[]" title="파일 찾아보기" class="file">
	              </div>
	            </div>
            </span>
		  </div>
		  <div class="write-box-devider"></div>
		  <?php
		  	}
		  endif;
		  
		  //최대 첨부 갯수 이하만큼 생성  3   2
		  if($file_attach_flag && $file_attach_max > $fileCnt){
			for($s = $fileCnt; $s < $file_attach_max; $s++){
			?>
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
  		  <?php 		
  		  	} // end for
  		  } // end if
	      ?>
          <input type="hidden" name="pavo_post_cur_id" value="<?php echo $wp_query->post->ID?>"/>
          </div>
          <?php endwhile; endif;?>
        <div class="pavoboard-controller"><a class="pavoboard-button button-prev" href="<?php echo admin_url("admin.php?page=ebbsmate")?>"> 취소 </a>
          <button style="float:right" name="pavo_board_update_post" class="pavoboard-button button-save" type="submit"> 확인 </button>

        </div>
        <?php wp_nonce_field( 'ebbsmate_editpost_'.$post_id, 'ebbsmate_post_nonce'); ?>
      </form>
    </div>
  </div>

  <!-- E:글쓰기 -->
</div>