<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//nonce_check
if ( empty( $_GET['ebbsmate_post_nonce'] ) || ( ! wp_verify_nonce( $_GET['ebbsmate_post_nonce'], 'ebbsmate_trashpost' ) ) ) {
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

$post_id = $_GET['post_id'];

$del_post_id = explode(',', $post_id);
$length = sizeof($del_post_id);

$cnt = 0;

foreach ($del_post_id as $value) {
	if(wp_trash_post($value)) {
		$cnt++;
	}
}

if($cnt == $length) {
	echo "<script> window.location='".admin_url('admin.php?page=ebbsmate')."&ids=".$post_id."';</script>";
}
?>