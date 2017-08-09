<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//nonce_check
if ( empty( $_GET['ebbsmate_board_nonce'] ) || ( ! wp_verify_nonce( $_GET['ebbsmate_board_nonce'], 'ebbsmate_untrashboard' ) ) ) {
	echo "<script>
			alert('잘못된 접근입니다.');
			window.history.back()
		</script>";
	return;
}

if ( ! current_user_can( 'manage_options' ) ) {
	$return = new WP_Error( 'broke', __( "권한이 없습니다." ) );
	echo $return->get_error_message();
	return ;
}

$post_id = $_GET['board_id'];

if(!empty($_GET['post_status'])){
	$post_status = "&post_status=".$_GET['post_status'];
} else {
	$post_status = "&post_status=";
}

$del_post_id = explode(',', $post_id);
$length = sizeof($del_post_id);

$cnt = 0;

foreach ($del_post_id as $value) {
	if(wp_untrash_post($value)) {
		$cnt++;
	}
}

if($cnt == $length) {
	echo "<script> window.location='".admin_url('admin.php?page=ebbsmate_board')."".$post_status."&ids=".$post_id."';</script>";
}
?>