<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//nonce_check
if ( empty( $_GET['ebbsmate_post_nonce'] ) || ( ! wp_verify_nonce( $_GET['ebbsmate_post_nonce'], 'ebbsmate_deletepost' ) ) ) {
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

if(!empty($_GET['post_status'])){
	$post_status = "&post_status=".$_GET['post_status'];
} else {
	$post_status = "&post_status=";
}

$del_post_id = explode(',', $post_id);
$length = sizeof($del_post_id);

$cnt = 0;

$upload_dir = wp_upload_dir();
$str = str_replace('\\', '/', $upload_dir['basedir']);

foreach ($del_post_id as $value) {
	if(wp_delete_post($value)) {
		
		$folderPath = $str."/ebbsmate_attachments/".$value;
		
		if(is_dir($folderPath)) {
			if ($dh = opendir($folderPath)) {
				while (($file = readdir($dh)) !== false) {
					if ($file == "." || $file == "..") continue;
					unlink($folderPath."/".$file);
				}
				closedir($dh);
			}
			rmdir($folderPath);
		}
		
		$cnt++;
	}
}

if($cnt == $length) {
	echo "<script> window.location='".admin_url('admin.php?page=ebbsmate')."&post_status=trash&ids=".$post_id."';</script>";
}
?>