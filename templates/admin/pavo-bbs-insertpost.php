<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//nonce_check
if ( empty( $_POST['ebbsmate_post_nonce'] ) || ( ! wp_verify_nonce( $_POST['ebbsmate_post_nonce'], 'ebbsmate_newpost' ) ) ) {
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

//wp_posts 테이블에 INSERT
$user_id        = get_current_user() ? get_current_user_id() : 0;
$post_title		= isset($_POST['pavo_board_post_title']) ? ebbsmate_html_purifier(ebbsmate_htmlclear( $_POST['pavo_board_post_title'] )): __('제목 없음');
$post_content	= isset($_POST['pavo_board_post_content']) ? ebbsmate_html_purifier(trim( $_POST['pavo_board_post_content'] )): '';
$board_id       = sanitize_text_field( $_POST['pavo_board_id'] );
$guest_name     = empty( $_POST['pavo_board_guest_name'] ) ? __('Guest') : sanitize_text_field( $_POST['pavo_board_guest_name'] );
$guest_password = empty( $_POST['pavo_board_guest_password'] ) ? '' : sanitize_text_field( $_POST['pavo_board_guest_password'] );
$notice_flag    = empty( $_POST['pavo_board_notice_flag'] ) ? 0 : sanitize_text_field( $_POST['pavo_board_notice_flag'] );
$parent_id      = empty( $_POST['pavo_parent_post_id'] ) ? 0 : sanitize_text_field( $_POST['pavo_parent_post_id'] );
$pavo_section   = empty( $_POST['ebbs_section_select'] ) ? 'all' : sanitize_text_field( $_POST['ebbs_section_select'] );

$args = array(
		'post_author'   => $user_id,
		'post_title'    => $post_title,
		'post_content'  => $post_content,
		'post_type'     => 'ebbspost',
		'post_status'   => 'publish',
		'post_parent'    => $parent_id,
		'comment_status' => 'open',
		'ping-status'    => 'open',
);

$post_id = wp_insert_post($args);

//wp_postmeta 테이블에 post_meta ADD
//1. 게시글이 속할 게시판
add_post_meta( $post_id, 'pavo_board_origin', $board_id);

//2. 조회수
add_post_meta( $post_id, 'pavo_board_view_count', 0 );

//3. 공지글 여부
add_post_meta( $post_id, 'pavo_board_notice_flag', $notice_flag );

//4. 분류
add_post_meta($post_id, "pavo_section_val", $pavo_section);

if(get_current_user_id() == 0) {

	//비로그인 사용자일경우 게스트용 이름 postmeta ADD
	add_post_meta( $post_id, 'pavo_board_guest_name', $guest_name);

	//비로그인 사용자일경우 비밀번호 postmeta ADD
	add_post_meta( $post_id, 'pavo_board_guest_password', $guest_password);
}

//////////////////////////////////////////첨부파일 저장
//파일 저장 경로 생성
if(count($_FILES['upload']['name']) > 0) {
	$upload_dir = wp_upload_dir();
		
	if (!empty($_FILES['upload']['size'][0]) || !empty($_FILES['upload']['size'][1])) {
		wp_mkdir_p($upload_dir['basedir']."/ebbsmate_attachments/".$post_id."/");
	}
}

$file_path_array = array();
$file_name_array = array();

$upload_file = $_FILES['upload'];

$reduced_name = array_values(array_filter(array_map('trim',$upload_file['name'])));
$reduced_type = array_values(array_filter(array_map('trim',$upload_file['type'])));
$reduced_tmp_name = array_values(array_filter(array_map('trim',$upload_file['tmp_name'])));
$reduced_size = array_values(array_filter(array_map('trim',$upload_file['size'])));

//첨부파일 정보 가져오기
for($i=0; $i<count($reduced_name); $i++) {
		
	$tmpFilePath = $reduced_tmp_name[$i];
		
	if ($tmpFilePath != ""){
			
		$str = str_replace('\\', '/', $upload_dir['basedir']);
			
		// 2. 파일명 중복 체크
		$newFilePath = $str."/ebbsmate_attachments/".$post_id."/";
		$newFileName = ebbsmate_incrementFileName($str."/ebbsmate_attachments/".$post_id."/", $reduced_name[$i]);
		$newFilePath = $newFilePath.$newFileName;
			
		// 4. 파일 경로 등록
		$file_path_array[$i] = $newFilePath;
		update_post_meta($post_id, "pavo_bbs_file_path", $file_path_array);
			
		$file_name_array[$i] = $newFileName;
		update_post_meta($post_id, "pavo_bbs_file_name", $file_name_array);
			
		if(move_uploaded_file($tmpFilePath, $newFilePath)) {
			//첨부등록 실패

		}
	}
}

echo "<script> window.location='".admin_url('admin.php?page=ebbsmate')."&msg=insertpost&msg_arg=".$post_id."';</script>";


?>