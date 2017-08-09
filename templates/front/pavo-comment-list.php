<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Gather comments for a specific page/post
$comments = get_comments(array(
		'post_id' => $post_id,
		'status' => 'approve' //Change this to the type of comments to be displayed
));

$pavo_comment_args = array(
	'walker'            => null,
    'max_depth'         => '',
    'style'             => 'ol',
    'callback'          => 'ebbsmate_custom_comments',
    'end-callback'      => null,
    'type'              => 'all',
    'page'              => '',
    'per_page'          => '',
    'avatar_size'       => 32,
    'reverse_top_level' => null,
    'reverse_children'  => '',
    'format'            => 'xhtml',
    'short_ping'        => false, 
);

//코맨트 개수 가져오기
global $wpdb, $current_user;

$sql = "select count(*) as comment_cnt
from $wpdb->comments c
where c.comment_post_ID='".$post_id."'
and c.comment_approved='1'";

$result = $wpdb->get_row($sql);
$total_comment_cnt = $result->comment_cnt;
?>

<div class="comment-section">
<?php if(get_user_roles($board_id, "comment")) { ?>
	<form name="pavo_comment_insert_form">
		<div class="comment-editor">
			<div class="comment-editor-top"></div>
			<div class="comment-editor-content">
				<span class="comment-editor-avatar"><?php echo get_avatar( $current_user->ID , 54 ); ?></span>
				<div class="comment-editor-text">
					<textarea title="댓글 내용 입력" name="pavo_comment_content" class="comment-editor-textarea"></textarea>
				</div>
				<button class="button-comment-write pavoboard-button" type="submit">등록</button>
			</div>
			<?php if(get_current_user_id() == 0) {?>
			<!-- 비회원 댓글 입력 -->
			<div class="comment-reply-input">
				<input type="text" placeholder="글쓴이" name="pavo_comment_writer">
				<input type="password" placeholder="비밀번호" name="pavo_comment_password">
			</div>
			<?php }?>
		</div>
		<input type="hidden" name="action" value="pavoboard_insert_comment"/>
		<input type="hidden" name="pavo_comment_board_id" value="<?php echo $board_id;?>"/>
		<input type="hidden" name="pavo_comment_post_id" value="<?php echo $post_id?>"/>
		<input type="hidden" name="pavo_post_cur_board_page" value="<?php echo $cur_post_id ?>"/>  
		<input type="hidden" name="pavo_comment_type" value="insert"/>  
	</form>
<?php }?>
	<div class="pavoboard-comment-list">
		<div class="list-header"> <span>댓글 <strong>(<?php echo $total_comment_cnt?>)</strong></span></div>
		<ul class="list-wrapper">
			<?php wp_list_comments('callback=ebbsmate_custom_comments', $comments);?>
		</ul>
	</div>
</div>
