<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//nonce_check
if ( empty( $_REQUEST['ebbsmate_post_nonce'] ) || empty( $_REQUEST['post_id'] ) || ( ! wp_verify_nonce( $_REQUEST['ebbsmate_post_nonce'], 'ebbsmate_editpost_'.$_REQUEST['post_id'] ) ) ) {
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

if(!empty($_GET['action']) && ($_GET['action'] == "notice" || $_GET['action'] = "unnotice")) {
		
	if($_GET['action'] == "notice") {
		$notice_flag = 1;
	} else if($_GET['action'] = "unnotice") {
		$notice_flag = 0;
	}
	
	$post_id        = sanitize_text_field( $_GET['post_id'] );
	update_post_meta($post_id, "pavo_board_notice_flag", $notice_flag);
	
	echo "<script> window.location='".admin_url('admin.php?page=ebbsmate')."&msg=".$_GET['action']."&msg_arg=".$post_id."';</script>";
} else {


$post_id        = sanitize_text_field( $_GET['post_id'] );
$post_title		= isset($_POST['pavo_board_post_title']) ? ebbsmate_html_purifier(ebbsmate_htmlclear( $_POST['pavo_board_post_title'] )): __('제목 없음');
$post_content	= isset($_POST['pavo_board_post_content']) ? ebbsmate_html_purifier(trim( $_POST['pavo_board_post_content'] )): '';
$board_id       = sanitize_text_field( $_POST['pavo_board_id'] );
$pavo_section   = empty($_POST['ebbs_section_select']) ? 'all' : sanitize_text_field( $_POST['ebbs_section_select'] );

$notice_flag    = empty($_REQUEST['pavo_board_notice_flag']) ? 0 : sanitize_text_field( $_REQUEST['pavo_board_notice_flag'] );
$deleted_file   = empty($_REQUEST['pavo_board_deleted_file']) ? array() : (array) $_REQUEST['pavo_board_deleted_file'] ;


$args = array(
	'ID'            => $post_id,
	'post_title'    => $post_title,
	'post_content'  => $post_content,
);

wp_update_post($args);

update_post_meta($post_id, 'pavo_board_origin', $board_id);
update_post_meta($post_id, "pavo_board_notice_flag", $notice_flag);
update_post_meta($post_id, "pavo_section_val", $pavo_section);


//첨부파일 삭제
$upload_dir = wp_upload_dir();

$pavo_file_path = get_post_meta($post_id, "pavo_bbs_file_path", false);
$pavo_file_name = get_post_meta($post_id, "pavo_bbs_file_name", false);

if(!empty($deleted_file)) {
for($i = 0; $i < sizeof($deleted_file); $i++) {
	
	$str = str_replace('\\', '/', $upload_dir['basedir']);
	$delete_file_path = $str."/ebbsmate_attachments/".$post_id."/".$deleted_file[$i];
		
	for($j = 0; $j < sizeof($pavo_file_path[0]); $j++) {		
		if($delete_file_path == $pavo_file_path[0][$j]) {
			unlink($pavo_file_path[0][$j]);
			
			unset($pavo_file_path[0][$j]);
			unset($pavo_file_name[0][$j]);
			
			$pavo_file_path[0] = array_values($pavo_file_path[0]);
			$pavo_file_name[0] = array_values($pavo_file_name[0]);
			
			update_post_meta($post_id, "pavo_bbs_file_path", $pavo_file_path[0]);
			update_post_meta($post_id, "pavo_bbs_file_name", $pavo_file_name[0]);
		}
	}
}
}

if(!empty($_FILES['upload']['name'])) {
	////////////////////////첨부파일 등록
	if(sizeof($pavo_file_name) == 0 || sizeof($pavo_file_path) == 0) {
		//첨부파일 저장 폴더 생성
		
		$filename = $upload_dir['basedir']."/ebbsmate_attachments/".$post_id."/";
		
		if (!empty($_FILES['upload']['size'][0]) || !empty($_FILES['upload']['size'][1])) {
			wp_mkdir_p($upload_dir['basedir']."/ebbsmate_attachments/".$post_id."/");
		}
	}
	
	//첨부파일 정보 가져오기
	for($i=0; $i<count($_FILES['upload']['name']); $i++) {
	
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
	
		if ($tmpFilePath != ""){
			$str = str_replace('\\', '/', $upload_dir['basedir']);
	
			$newFilePath = $str."/ebbsmate_attachments/".$post_id."/".$_FILES['upload']['name'][$i];
	
			// 2. 파일명 중복 체크
			$newFilePath = $str."/ebbsmate_attachments/".$post_id."/";
			$newFilePath = $newFilePath.ebbsmate_incrementFileName($str."/ebbsmate_attachments/".$post_id."/", $_FILES['upload']['name'][$i]);
			
			// 4. 파일 경로 등록
			//////////////////현재 첨부파일 meta 가져오기
			$oldFilePath = get_post_meta($post_id, "pavo_bbs_file_path", false);
			$oldFileName = get_post_meta($post_id, "pavo_bbs_file_name", false);
					
			$oldFilePath[0][sizeof($oldFilePath[0])] = $newFilePath;
			$oldFileName[0][sizeof($oldFileName[0])] = $_FILES['upload']['name'][$i];
	
			update_post_meta($post_id, "pavo_bbs_file_path", $oldFilePath[0]);
			update_post_meta($post_id, "pavo_bbs_file_name", $oldFileName[0]);
	
			if(move_uploaded_file($tmpFilePath, $newFilePath)) {
			}
		}
	}
}

echo "<script> window.location='".admin_url('admin.php?page=ebbsmate')."&msg=updatepost&msg_arg=".$post_id."';</script>";

}
?>