<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$comment_content 	= "";
$writer				= "";
// edit : 수정

if($type == "edit"){
	$get_comment = get_comment( $comment_id );
	$comment_content = $get_comment->comment_content;
	$writer = get_comment_meta( $comment_id, "comment_writer", true );
}


if($type == "delete"){
?>
<div class="comment-edit">
	<form>
		<div class="comment-edit-input">
			<input type="password" placeholder="<?php _e( '비밀번호', 'pavoboard' ) ?>" name="pavo_comment_password" value="">
			<button class="button comment-delete"><?php _e( '삭제', 'pavoboard' ) ?></button>
			<button class="button comment-close"><?php _e( '취소', 'pavoboard' ) ?></button>
		</div>
		<input type="hidden" name="pavo_comment_board_id" value="<?php echo $board_id?>"/>
		<input type="hidden" name="pavo_comment_post_id" value="<?php echo $post_id?>"/>
		<input type="hidden" name="pavoboard_comment_id" value="<?php echo $comment_id ?>"/>
		<input type="hidden" name="pavo_comment_type" value="delete" />
		<input type="hidden" name="action" value="pavoboard-delete-comment" />
	</form>
</div>
<?php 
}else{
?>


<div class="comment-reply">
	<div class="comment-<?php echo $type ?>-header">
		<span style="float:right">
			<a class="btn-pavoboard-comment-reply-close">
				<span class="pavoboard-list-icon pavoboard-comment-close"></span>
				<span><?php _e( '닫기', 'pavoboard' ) ?></span>
			</a>
		</span>
	</div>
	<form class="comment-delete-form">
		<?php if($type == "delete"){?>
		<div class="comment-reply-input">
			<span><?php _e( '비밀번호 입력', 'pavoboard' ) ?></span>
			<input type="password" placeholder="<?php _e( '비밀번호', 'pavoboard' ) ?>" name="pavo_comment_password" value="">
		</div>
		<div class="comment-reply-input">
			<a class="pavoboard-button comment-save comment-delete"><?php _e( '삭제', 'pavoboard' ) ?></a>
		</div>
		<?php }else{?>
		<textarea name="pavo_comment_content"><?php echo $comment_content ?></textarea>
		<?php 
		if($type == "write" && !is_user_logged_in()){
		?>
		<div class="comment-reply-input">
			<input type="text" placeholder="<?php _e( '작성자', 'pavoboard' ) ?>" name="pavo_comment_writer" value="<?php echo $writer;?>">
			<input type="password" placeholder="<?php _e( '비밀번호', 'pavoboard' ) ?>" name="pavo_comment_password">
		</div>	
		<?php 
		}
		
		if($type == "edit" && !is_pvbbsadmin($board_id) && $get_comment->user_id == 0 ){
		?>
		<div class="comment-reply-input">
			<input type="text" placeholder="<?php _e( '작성자', 'pavoboard' ) ?>" name="pavo_comment_writer" value="<?php echo $writer;?>">
			<input type="password" placeholder="<?php _e( '비밀번호', 'pavoboard' ) ?>" name="pavo_comment_password">
		</div>	
		<?php 
		}
		?> 
		<div class="comment-reply-input">
			<a class="pavoboard-button comment-save<?php echo $type == "edit"? ' comment-update' : ''?>"><?php _e( '등록', 'pavoboard' ) ?></a>
		</div>
		<?php 
		}
		?> 
		
		<input type="hidden" name="pavo_comment_board_id" value="<?php echo $board_id?>"/>
		<input type="hidden" name="pavo_comment_post_id" value="<?php echo $post_id?>"/>
		<?php if($type == "edit"){?>
		<input type="hidden" name="pavoboard_comment_id" value="<?php echo $comment_id ?>"/>
		<input type="hidden" name="pavo_comment_type" value="update" />
		<input type="hidden" name="action" value="pavoboard_update_comment"/>
		<?php }?>
		<?php if($type == "write"){?>
		<input type="hidden" name="pavoboard_parent_comment_id" value="<?php echo $comment_id ?>"/>
		<input type="hidden" name="pavo_comment_type" value="reply" />
		<input type="hidden" name="action" value="pavoboard_insert_comment"/>
		<?php }?>
		<?php if($type == "delete"){?>
		<input type="hidden" name="pavoboard_comment_id" value="<?php echo $comment_id ?>"/>
		<input type="hidden" name="pavo_comment_type" value="delete" />
		<input type="hidden" name="action" value="pavoboard-delete-comment" />
		<?php }?>
	</form>
</div>
<?php }?>