<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! current_user_can( 'manage_options' ) ) {
	$return = new WP_Error( 'broke', __( "권한이 없습니다." ) );
	echo $return->get_error_message();
	return ;
}

$post_id = $_GET['post_id'];

//게시물 상세 정보를 불러온다.
$args= array (
		'post_type' => array('ebbspost'),
		'post_status' => 'publish',
		'posts_per_page'=> 1,
		'p' => $post_id,
);
$wp_query = new WP_Query($args);
?>
<div style="padding:20px; padding-top: 40px; padding-right: 40px;">


  <!-- S:상세 -->
  <div class="post-content">
    <div class="pavoboard-wrapper <?php echo ebbsmate_get_current_theme()?>">
      <h2>게시글 조회</h2>
      <div id="pavoboard-read-table">
        <div class="title-section">
          <h1 class="pavoboard-read-h1"><?php echo $wp_query->post->post_title?></h1>
          <div class="regist-date">
            <h2 class="pavoboard-read-h2"><?php echo date('Y.m.d H:i', strtotime($wp_query->post->post_date))?></h2>
          </div>
        </div>
        <div class="writer-section">
          <div class="regist-writer">
            <h2 class="pavoboard-read-h2"><?php echo ebbsmate_get_the_author($wp_query->post->ID)?></h2>
          </div>
          <div class="regist-info"><span style="padding-right: 10px;">조회<b><?php echo get_post_meta($wp_query->post->ID, 'pavo_board_view_count', true)?></b></span> </div>
        </div>



		<?php 
        //파일 첨부했는지 체크
        $board_id = get_post_meta($post_id, "pavo_board_origin", true);
        $file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
        
        $fileNames=get_post_meta($post_id, "pavo_bbs_file_name", false);
        $filePaths=get_post_meta($post_id, "pavo_bbs_file_path", false);
        
        $total_file_cnt = 0;
        
        if(!empty($fileNames) && sizeof($fileNames[0]) > 0) {
        	$total_file_cnt = sizeof($fileNames[0]);
        } else {
        	$total_file_cnt = 0;
        }
        ?>
		<?php if(!empty($fileNames) && sizeof($fileNames[0]) > 0) { ?>

        <div class="board_detail">
            <div class="attachFile"><a title="<?php _e('첨부파일목록', 'pavoboard') ?>" href="javascript:open_file_attach_layer();" style="padding-right: 10px;"><?php _e('첨부파일', 'pavoboard') ?><em>(<?php echo $total_file_cnt?>)</em></a>
                <div class="attach_layer">
                    <ul class="innerList" style="display:none; margin-right: 10px;">
                         <li class="toparrow"></li>
                         <li class="btn_close"><a title="닫기" href="javascript:open_file_attach_layer();"></a></li>
                         <?php for($i = 0; $i < sizeof($fileNames[0]); $i++) {
                         	$file_url = get_permalink($post_id);
                         	$params = array(
                         			'action' => "ebbsmate_download",
                         			'board_id' => $board_id,
                         			'post_id' => $post_id,
                         			'file_name' => $fileNames[0][$i],
                         	);
                         	$file_url = add_query_arg( $params, get_site_url() );
                         	
                         ?>                      
                    	<li class="item">
	                    	<a title="<?php echo $fileNames[0][$i]?> 파일 다운로드" href="<?php echo $file_url ?>">
	                    	<?php echo $fileNames[0][$i]?>(<?php echo number_format(filesize($filePaths[0][$i])/1024,2)?> KB)
	                    	</a>
                    	</li>
                    <?php }?>
                    </ul>
                </div>
            </div><!-- // attachFile -->
         </div>

		<?php }?>


        <div class="content-section"  style="min-height: 300px;">
          <?php $content = apply_filters('the_content', $wp_query->post->post_content);?>
          <p><?php echo $content?></p>
        </div>
        <div class="pavoboard-controller">
          <div class="pavoboard-controller-left">
            <a class="pavoboard-button" href="<?php echo admin_url('admin.php?page=ebbsmate')?>">목록</a>
            <a class="pavoboard-button" href="<?php echo admin_url('admin.php?page=ebbsmate&mode=write&parent_id=')."".$wp_query->post->ID?>">답글</a>
          </div>
          <div class="pavoboard-controller-right">
            <a class="pavoboard-button" href="<?php echo admin_url('admin.php?page=ebbsmate')."&mode=edit&post_id=".$post_id?>">수정</a>
            <a data-board="11842" data-id="11859" class="pavoboard-button pavoboard-entry-delete" href="<?php echo admin_url('admin.php?page=ebbsmate')."&mode=delete&post_id=".$post_id?>">삭제</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- E:상세 -->
  </div>