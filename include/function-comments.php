<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function ebbsmate_custom_comments( $comment, $args, $depth ) {
	
	global $wpdb;
	$GLOBALS['comment'] = $comment;

	switch( $comment->comment_type ) :
	case 'pingback' :
        case 'trackback' : ?>
            <li <?php comment_class(); ?> id="comment<?php comment_ID(); ?>">
            <div class="back-link"><?php comment_author_link(); ?></div>
        <?php break;
        default : 
        	
			if($comment->user_id == 0) {
				$comment_author = get_comment_meta($comment->comment_ID, "comment_writer", true);
			} else {			
				$user_info = get_userdata($comment->user_id);
				$comment_author = $user_info->nickname;
			}
			
			$is_valid_comment_role = false;
						
			$sql = "select comment_post_ID
					from $wpdb->comments
					where comment_ID = ".$comment->comment_ID;
		
			$post_id = $wpdb->get_var($sql);
			
			$sql = "select meta_value from wp_postmeta
					where post_id = ".$post_id."
					and meta_key = 'pavo_board_origin'";
			$board_id = $wpdb->get_var($sql);

			$guest_author = get_comment_meta($comment->comment_ID, 'comment_writer', true);
			$guest_password = get_comment_meta($comment->comment_ID, 'comment_password', true);
			include PavoBoardMate::$PLUGIN_DIR.'templates/front/pavo-comment-single.php';
        break;
    endswitch;
}


function ebbsmate_custom_comments_ava( $comment, $args, $depth ) { ?>
		<?php $add_below = ''; ?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
			<div class="the-comment">
				<div class="avatar"><?php echo get_avatar( $comment, 54 ); ?></div>
				<div class="comment-box">
					<div class="comment-author meta">
						<strong><?php echo get_comment_author_link(); ?></strong>
						<?php printf( __( '%1$s at %2$s', 'Avada' ), get_comment_date(),  get_comment_time() ); ?><?php edit_comment_link( __( ' - Edit', 'Avada' ),'  ','' ); ?><?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( ' - Reply', 'Avada' ), 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div>
					<div class="comment-text">
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'Avada' ); ?></em>
							<br />
						<?php endif; ?>
						<?php comment_text() ?>
					</div>
				</div>
			</div>
		<?php
	}

//댓글 DEPTH 조회
function ebbsmate_get_comment_depth( $my_comment_id ) {
	$depth_level = 0;
	while( $my_comment_id > 0  ) { // if you have ideas how we can do it without a loop, please, share it with us in comments
		$my_comment = get_comment( $my_comment_id );
		$my_comment_id = $my_comment->comment_parent;

		$depth_level++;
	}
	return $depth_level;
}

//댓글 ARRAY 저장
function ebbsmate_get_comm_id_array( $my_comment_id ) {
	$index = 0;
	$test = array();

	while( $my_comment_id > 0  ) { // if you have ideas how we can do it without a loop, please, share it with us in comments
		$my_comment = get_comment( $my_comment_id );
		$my_comment_id = $my_comment->comment_parent;

		$test[$index] = $my_comment_id;

		$index++;
	}

	return $test;
}