<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// 답글의 부모글
$parent_id = empty($_GET['parent_id'])? 0 : $_GET['parent_id'];

$ebbsmate_settings = get_option ( 'ebbsmate_settings' );
$editor            = $ebbsmate_settings ['bbs_editor'];

// 게시판 설정값 로드
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

//게시판 항목 설정 여부
$board_section_flag = get_post_meta($board_id, "ebbsmate_section_flag", true);

$login_user = is_user_logged_in();
?>
<div class="pavoboard-wrapper pavoboard-custom">
	<form method="post" name="ebbsmate_post_write_form" id="pavo_post_form" enctype="multipart/form-data" action="<?php echo add_query_arg( array('action' => 'insert' ), get_permalink($cur_post_id) ) ?>">
		<input type="hidden" name="action" value="ebbsmate_insert_post">
        <div class="pavoboard-write-wrapper">
          <?php if(!$login_user) {?>
          <div class="write-box">
            <label class="pavoboard-write-author" for="entry-author"><span>작성자<span class="label-required">*</span></span></label>
            <span class="write-span">
            <input type="text" title="작성자" style="width:100%" name="pavo_board_guest_name" id="entry-author">
            </span> </div>
          <div class="write-box-devider"></div>
          <div id="post_password_row">
          <div class="write-box">
            <label class="pavoboard-write-password" for="entry-password"><span>비밀번호<span class="label-required">*</span></span></label>
            <span class="write-span">
            <input type="password" title="비밀번호" style="width:100%" name="pavo_board_guest_password" id="entry-password">
            </span> </div>
          <div class="write-box-devider"></div>
          </div>
          <?php }?>
          <div class="write-box">
            <label class="pavoboard-write-title" for="entry-title"><span>제목<span class="label-required">*</span></span></label>
            <span class="write-span">
            <?php if (!empty($parent_id)) :?>
            <input type="text" title="제목" style="width:100%" name="pavo_board_post_title" id="entry-title" value="<?php echo get_the_title($parent_id)?>" />
            <?php else :?>
            <input type="text" title="제목" style="width:100%" name="pavo_board_post_title" id="entry-title" />
            <?php endif;?>
            </span>
          </div>
          <div class="write-box-devider"></div>
          <?php if($board_section_flag) {?>
          <div class="write-box check-box">
            <label for="ebbs-section-select"><span>분류</span></label>
            <select name="ebbs_section_select" id="ebbs-section-select">
	            <option value='all'>전체</option>
          <?php 
          	$board_section = get_post_meta($board_id, "ebbsmate_section" , true);
   			foreach ($board_section as $section){
   				
   				
          		//분류별 권한이 있는경우 권한 없을경우 노출되지 않음
          		$rolecheck = pavoebbsmate_section_rolecheck($board_id,$section['permission']);
          		if($rolecheck)
          			echo "<option value='".$section['title']."'>".$section['title']."</option>";
          	}
          ?>
          	</select>
          </div>
          <div class="write-box-devider"></div>
		  <?php }?>
          <?php if($login_user && get_user_roles($board_id, "notice")) {?>
          <div class="write-box check-box">
            <label for="input-checkbox-notice"><span>공지사항</span></label>
            <input type="checkbox" title="공지사항" value="1" name="pavo_board_notice_flag" class="input-checkbox" id="input-checkbox-notice">
          </div>
          <div class="write-box-devider"></div>
		  <?php }?>
		  <?php if($file_secret_flag) :?>
          <div class="write-box check-box">
            <label for="input-checkbox-secret"><span>비밀글</span></label>
            <input type="checkbox" title="비밀글" value="1" name="pavo_board_secret_flag" class="input-checkbox" id="input-checkbox-secret">
          </div>
          <div class="write-box-devider"></div>
          <?php endif;?>
          <div class="textarea-box"> <span class="write-span">
              <?php if($editor == "wp") {
			  	wp_editor('', 'pavo_board_post_content');
			  } else {?>
			  	<textarea name="pavo_board_post_content" rows="" cols=""></textarea>
			  <?php }?>
            </span> </div>
          <div class="write-box-devider"></div>
          <?php 
          if($file_attach_flag){
          	for($s = 0; $s < $file_attach_max; $s++){
          ?>
			<div class="write-box attach-box attach-box-1">
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
          <?php } ?> 
          <div class="max-attach-size"><?php _e('첨부 가능용량 :')?><span><?php echo round($file_attach_size / 1048576, 2) . ' MB' ?></span></div>
          <?php } ?>
          <div class="write-box-devider"></div>         
        </div>
        <div class="pavoboard-controller">
        	<a class="pavoboard-button button-prev" href="<?php echo get_permalink($cur_post_id)?>"> 취소 </a>
        	<a class="pavoboard-button button-prev button-save" style="float:right" action="ebbsmate_insert_post">확인</a>
			<!-- <button style="float:right" class="pavoboard-button button-save" type="button" onclick="ebbsmate_insert_post();"> 확인 </button> -->
		</div>
		<input type="hidden" name="pavo_board_id" value="<?php echo $board_id;?>"/>
		<input type="hidden" name="pavo_post_cur_board_page" value="<?php echo $cur_post_id ?>"/> 
		<input type="hidden" name="pavo_parent_post_id" value="<?php echo $parent_id;?>">
		<input type="hidden" name="pavo_attach_size" value="0">
	</form>
    <div style="margin-top: 30px;">
		<?php echo apply_filters('ebbsmate_powered_by', '')?>
	</div>
</div>