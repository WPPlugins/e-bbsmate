<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$date_format = get_option( 'date_format' )." ".get_option( 'time_format' );
$delete_flag = get_comment_meta($comment->comment_ID, "comment_delete_flag", true);


if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $delete_flag) {
?>
	<div class="comment-box">
		<div class="comment-content">
			<div class="comment-content-text">
				<h2 class="pavoboard-read-h2"><?php echo nl2br(ebbsmate_display_prohibited_words($board_id, $comment->comment_content));?></h2>
			</div>
		</div>
	</div>
<?php 
}else{
?>
<li class="each-comment">
<?php if($depth > 1 ) {?>
	<div class="comment-reply-icon-wrapper">
		<span class="pavoboard-list-icon pavoboard-comment-reply02"></span>
	</div>
<?php 
	}
	if(!$delete_flag){?>	
	<div class="comment-box">
		<span class="comment-avatar"><?php echo get_avatar( $comment, 54 ); ?></span>
		<div class="comment-content">
			<div class="comment-content-writer">
				<h2 class="pavoboard-read-h2"><span class="author"><?php echo $comment_author?></span></h2>
				<h2 class="pavoboard-read-h2">
					<span class="date">
					<?php echo mysql2date($date_format, $comment->comment_date) ?>
					</span>
				</h2>
			</div>
			<div class="comment-content-text">
				<h2 class="pavoboard-read-h2"><?php echo nl2br(ebbsmate_display_prohibited_words($board_id, $comment->comment_content));?></h2>
			</div>
		</div>
	</div>
	<div class="comment-controller">
		<?php if(get_user_roles($board_id, "comment")) { ?>
		<span><a class="reply-comment" comment-id="<?php echo $comment->comment_ID?>"><span><?php _e('답글달기','pavoboard')?></span></a></span>
		<span><a class="comment_edit" comment-id="<?php echo $comment->comment_ID?>"><span><?php _e('수정','pavoboard')?></span></a></span>
		<span><a class="comment_delete" comment-id="<?php echo $comment->comment_ID?>"><span><?php _e('삭제','pavoboard')?></span></a></span>
		<?php }?>
	</div>
<?php }else{?>
	<div class="comment-box">
		<div class="comment-content">
			<div class="comment-content-text">
				<h2 class="pavoboard-read-h2"><?php echo nl2br(ebbsmate_display_prohibited_words($board_id, $comment->comment_content));?></h2>
			</div>
		</div>
	</div>
<?php }}?>
<!-- 
<input type="hidden" name="pavo_comment_content_view_hidden_<?php echo $comment->comment_ID?>" value="<?php echo ebbsmate_display_prohibited_words($board_id, $comment->comment_content);?>">
<input type="hidden" name="pavo_comment_cur_id" value="<?php echo $comment->comment_ID?>"/>
<input type="hidden" name="pavo_comment_cur_board_id" value="<?php echo $board_id?>"/>
<input type="hidden" name="pavo_comment_content_<?php echo $comment->comment_ID?>" value=""/>
<input type="hidden" name="pavo_comment_writer_<?php echo $comment->comment_ID?>" value=""/>
<input type="hidden" name="pavo_comment_password_<?php echo $comment->comment_ID?>" value=""/>  
 -->
      