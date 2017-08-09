<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$post_id = $_GET['post_id'];

//게시글이 존재하는지 체크
if ( get_post_status ( $post_id ) ) {
	
// 게시글 설정값 로드
$notice_flag = get_post_meta($post_id, "pavo_board_notice_flag", true);
$secret_flag = get_post_meta($post_id, "pavo_board_secret_flag", true);

// 게시판 설정값 로드
$file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
$file_attach_size = (int)get_post_meta($board_id, "ebbsmate_attach_size", true);
$file_attach_size = $file_attach_size*1048576;
$file_attach_max	= (int)get_post_meta($board_id, "ebbsmate_attach_item", true);

//사용가능한 첨부용량 확인
$curpost_attach = get_post_meta($post_id, "pavo_bbs_file_path");
$curpost_attach_size = 0;

if($curpost_attach){
	foreach ($curpost_attach[0] as $filepath){
		if(file_exists($filepath)){
			$curpost_attach_size = $curpost_attach_size + (int)filesize($filepath);
		}
	}
}


$allowed_size = 0;
$allowed_text = "";

if($file_attach_size > $curpost_attach_size){
	$allowed_size = $file_attach_size - $curpost_attach_size;
	$allowed_text = round($allowed_size / 1048576, 2) . ' MB';
}else{
	$allowed_text = __("최대 첨부 가능 용량을 초과되었습니다.", 'pavoboard' );
}

wp_enqueue_script('pavoboard-attach', PavoBoardMate::$PLUGIN_URL.'js/front-attachment.js', false);
wp_localize_script( 'pavoboard-attach', 'pavoboard_attach',
		array(
				'file_attach_size' 	=> $file_attach_size,
				'attach_type' 		=> 'edit',
		)
);

$ebbsmate_settings = get_option ( 'ebbsmate_settings' );
$editor            = $ebbsmate_settings ['bbs_editor'];

//게시판 항목 설정 여부
$board_section_flag = get_post_meta($board_id, "ebbsmate_section_flag", true);

//게시물 상세 정보를 불러온다.
$args= array (
		'post_type' => array('ebbspost'),
		'post_status' => 'publish',
		'p' => $post_id,
);
$wp_query = new WP_Query($args);
?>
<div class="pavoboard-wrapper pavoboard-custom">
      <form method="post" name="pavo_post_edit_form" id="pavo_post_form" enctype="multipart/form-data" action="<?php echo add_query_arg( array('action' => 'updatepost' , 'post_id' => $post_id), get_permalink($cur_post_id) ) ?>">
        <div class="pavoboard-write-wrapper">
        <?php 
        if($wp_query->have_posts()) :
			while ( $wp_query->have_posts() ) : $wp_query->the_post();
				if($wp_query->post->post_author == 0) {?>
				
          <div class="write-box">
            <label class="pavoboard-write-author" for="entry-author"><span>작성자<span class="label-required">*</span></span></label>
            <span class="write-span">
            	<input type="text" title="작성자" style="width:100%" name="pavo_board_guest_name" id="entry-author" value="<?php echo get_post_meta($wp_query->post->ID, "pavo_board_guest_name", true)?>">
            </span> </div>
          <div class="write-box-devider"></div>
          <div id="post_password_row">
	          <div class="write-box">
	            <label class="pavoboard-write-password" for="entry-password"><span>비밀번호<span class="label-required">*</span></span></label>
	            <span class="write-span">
	            	<input type="password" title="비밀번호" style="width:100%" name="pavo_board_guest_password" id="entry-password" value="">
	            </span>
	          </div>
	          <div class="write-box-devider"></div>
          </div>
          
          <?php }?>
          <div class="write-box">
            <label class="pavoboard-write-title" for="entry-title"><span>제목<span class="label-required">*</span></span></label>
            <span class="write-span">
            <input type="text" title="제목" style="width:100%" name="pavo_board_post_title" id="entry-title" value="<?php echo $wp_query->post->post_title;?>">
            </span> </div>
          <div class="write-box-devider"></div>
          <?php if($board_section_flag) {?>
          <div class="write-box check-box">
            <label for="ebbs-section-select"><span>분류</span></label>
            <select name="ebbs_section_select" id="ebbs-section-select">
	            <option value='all'>전체</option>
          <?php 
          	$board_section = get_post_meta($board_id, "ebbsmate_section" , true);
   			foreach ($board_section as $section){
   				$post_section = get_post_meta($wp_query->post->ID, "pavo_section_val", true);
   				
          		//분류별 권한이 있는경우 권한 없을경우 노출되지 않음
          		$rolecheck = pavoebbsmate_section_rolecheck($board_id,$section['permission']);
          		if($rolecheck){
          		?>
          			<option value='<?php echo $section['title'] ?>' <?php selected( $section['title'], $post_section ); ?>><?php echo $section['title'] ?></option>
          		<?php 
          		}
          	}
          ?>
          	</select>
          </div>
          <div class="write-box-devider"></div>
		  <?php }?>
          <?php if(is_user_logged_in()) {
		  			if(get_user_roles($board_id, "notice")) :?>
          <div class="write-box check-box">
            <label for="input-checkbox-notice"><span>공지사항</span></label>
            <input type="checkbox" title="공지사항" value="1" name="pavo_board_notice_flag" class="input-checkbox" id="input-checkbox-notice" <?php checked($notice_flag, '1')?>>
          </div>
          <div class="write-box-devider"></div>
          <?php endif;}	
          		if(get_post_meta($board_id, "ebbsmate_secret_post", true)) :?>
          <div class="write-box check-box">
            <label for="input-checkbox-secret"><span>비밀글</span></label>
            <input type="checkbox" title="비밀글" value="1" name="pavo_board_secret_flag" class="input-checkbox" id="input-checkbox-secret" <?php checked($secret_flag, '1')?>>
          </div>
          <div class="write-box-devider"></div>
          <?php endif;?>
          <div class="textarea-box"> <span class="write-span">
              <?php if($editor == "wp") {
			  	wp_editor($wp_query->post->post_content, 'pavo_board_post_content');
			  } else {?>
			  	<textarea name="pavo_board_post_content" rows="" cols=""><?php echo $wp_query->post->post_content;?></textarea>
			  <?php }?>
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
		  }
		  if($file_attach_flag){
		  ?>
		  <div class="max-attach-size"><?php _e('첨부 가능용량 :')?><span><?php echo $allowed_text ?></span></div>
		  <input type="hidden" name="pavo_attach_size" value="<?php echo $allowed_size ?>"/>
		  <?php 
		  }
		  ?>
          <input type="hidden" name="pavo_post_cur_id" value="<?php echo $wp_query->post->ID ?>"/>
          <input type="hidden" name="pavo_post_cur_board_page" value="<?php echo $cur_post_id ?>"/>  
          <input type="hidden" name="pavo_post_cur_board_id" value="<?php echo $board_id ?>"/>
          <?php 
          endwhile; 
          wp_reset_postdata();
          endif;
          ?>        
          </div>
        <div class="pavoboard-controller">
        	<a class="pavoboard-button button-prev" href="<?php echo add_query_arg( array('action' => 'readpost' , 'post_id' => $post_id), get_permalink($cur_post_id) ) ?>">취소</a>
        	<a class="pavoboard-button button-save" style="float:right" action="ebbsmate_update_post">확인</a>
        </div>
       
      </form>
      <div style="margin-top: 30px;">
	    <?php echo apply_filters('ebbsmate_powered_by', '')?>
	  </div>
    </div>
	<?php 
	} else { // 게시글이 없을 경우	?>
	ebbsmate_print_no_page();
<?php }?>