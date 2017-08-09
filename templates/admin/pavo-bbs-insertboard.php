<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! current_user_can( 'manage_options' ) ) {
	$return = new WP_Error( 'broke', __( "권한이 없습니다." ) );
	echo $return->get_error_message();
	return ;
}

//nonce_check
if ( empty( $_POST['ebbsmate_board_nonce'] ) || ( ! wp_verify_nonce( $_POST['ebbsmate_board_nonce'], 'ebbsmate_newboard' ) ) ) {
	echo "<script>
			alert('잘못된 접근입니다.');
			window.history.back()
		</script>";
	return;
}

global $wpdb;

$user_id        = get_current_user_id()? get_current_user_id() : 0;
$post_title		= sanitize_text_field( $_POST['ebbsmate_board_name'] );
if(empty($post_title)) {
	$post_title = "게시판_". date( 'YmdHis', current_time( 'timestamp', 0 ) );
}
//보드 머릿말 사용 여부
//$board_preface_flag	= $_POST['ebbsmate_preface_flag'];

//보드 말머리
//$post_content	= $board_preface_flag ? $_POST['ebbsmate_board_preface'] : '' ;
$post_content = "";

//노출 목록수
$board_list_lines = sanitize_text_field( $_POST['ebbsmate_list_lines'] );

//위젯 노출 목록수
if(!empty($_POST['ebbsmate_widget_list_lines'])) {
	$board_widget_list_lines = sanitize_text_field( $_POST['ebbsmate_widget_list_lines'] );
} else {
	$board_widget_list_lines = "";
}

//파일 첨부 사용 여부
$board_file_attach_flag = sanitize_text_field( $_POST['ebbsmate_attach_flag'] );

//파일 첨부 최대 용량
$board_file_attach_size = sanitize_text_field( $_POST['ebbsmate_attach_size'] );

//파일 첨부 최대 개수
$board_file_attach_item = sanitize_text_field( $_POST['ebbsmate_attach_item'] );

//최상단 공지글
$board_notice_flag = sanitize_text_field( $_POST['ebbsmate_notice_top_flag'] );

//금칙어 필터링
$board_word_filtering_flag = sanitize_text_field( $_POST['ebbsmate_word_filtering'] );

//부가 기능 - 비밀글
$board_secret_post = sanitize_text_field( $_POST['ebbsmate_secret_post'] );

//부가 기능 - 이전글/다음글
$board_prev_next_post = sanitize_text_field( $_POST['ebbsmate_prev_next_post'] );

//부가 기능 - 이전글/다음글 개수
$board_prev_next_lines = sanitize_text_field( $_POST['ebbsmate_prev_next_lines'] );

//CSS스타일
$board_css_style = sanitize_text_field( $_POST['ebbsmate_css_style'] );

//권한 설정 - 목록 보기
$board_role_list = isset( $_POST['ebbsmate_list_permission'] ) ? (array) $_POST['ebbsmate_list_permission'] : array();
$board_role_list = array_map( 'esc_attr', $board_role_list );

//권한 설정 - 게시글 읽기
$board_role_read = isset( $_POST['ebbsmate_read_permission'] ) ? (array) $_POST['ebbsmate_read_permission'] : array();
$board_role_read = array_map( 'esc_attr', $board_role_read );

//권한 설정 - 게시글 쓰기
$board_role_write = isset( $_POST['ebbsmate_write_permission'] ) ? (array) $_POST['ebbsmate_write_permission'] : array();
$board_role_write = array_map( 'esc_attr', $board_role_write );

//권한 설정 - 댓글 쓰기
$board_role_comment = isset( $_POST['ebbsmate_comment_permission'] ) ? (array) $_POST['ebbsmate_comment_permission'] : array();
$board_role_comment = array_map( 'esc_attr', $board_role_write );

//권한 설정 - 공지글 쓰기
$board_role_notice = isset( $_POST['ebbsmate_notice_permission'] ) ? (array) $_POST['ebbsmate_notice_permission'] : array();
$board_role_notice = array_map( 'esc_attr', $board_role_notice );

//권한 추가 - 게시판 관리자(CUSTOM USER ROLE)
$board_admin_ids = sanitize_text_field( $_POST['ebbsmate_admin_ids'] );
$board_admin_ids = preg_replace('/\s+/', '', $board_admin_ids);

//게시글 항목 설정 - 작성자
$author_flag = sanitize_text_field( $_POST['author_flag'] );

//게시글 항목 설정 - 작성일
$date_flag = sanitize_text_field( $_POST['date_flag'] );

//게시글 항목 설정 - 조회수
$vcount_flag = sanitize_text_field( $_POST['vcount_flag'] );

//게시판 항목 설정 여부
$board_section_flag = sanitize_text_field( $_POST['ebbsmate_section_flag'] );

//게시판 항목 및 권한
if($board_section_flag) {
	$board_section = isset( $_POST['ebbsmate_section'] ) ? (array) $_POST['ebbsmate_section'] : array();
}else{
	$board_section = array();
}

$args = array(
		'post_author'   => $user_id,
		'post_title'    => $post_title,
		'post_content'  => $post_content,
		'post_type'     => 'ebbsboard',
		'post_status'   => 'publish',
		'comment_status' => 'closed',
		'ping-status' => 'closed',
);

$post_id = wp_insert_post($args);

////////////////////////
//게시판 상세 설정 POST META//
////////////////////////
$table_name= $wpdb->prefix."postmeta";

$max_board_id = $wpdb->get_var("SELECT MAX(meta_value+1) AS max_board_id
		FROM ".$table_name."
		WHERE meta_key='ebbsmate_board_id'");

if(empty($max_board_id)) {
	$max_board_id = 1;
}

$max_widget_id = $wpdb->get_var("SELECT MAX(meta_value+1) AS max_widget_id
		FROM ".$table_name."
		WHERE meta_key='ebbsmate_widget_id'");

if(empty($max_widget_id)) {
	$max_widget_id = 1;
}

//게시판 ID
add_post_meta($post_id, 'ebbsmate_board_id', $max_board_id);

//위젯 ID
add_post_meta($post_id, 'ebbsmate_widget_id', $max_widget_id);

//게시판 활성화 여부
add_post_meta($post_id, 'ebbsmate_status_flag', 1);

//권한 설정 - 목록 보기
add_post_meta($post_id, 'ebbsmate_list_role', $board_role_list);

//권한 설정 - 게시글 읽기
add_post_meta($post_id, 'ebbsmate_read_role', $board_role_read);

//권한 설정 - 게시글 쓰기
add_post_meta($post_id, 'ebbsmate_write_role', $board_role_write);

//권한 설정 - 댓글 쓰기
add_post_meta($post_id, 'ebbsmate_comment_role', $board_role_comment);

//권한 설정 - 공지글 쓰기
add_post_meta($post_id, 'ebbsmate_notice_role', $board_role_notice);

//권한 추가 - 게시판 관리자(CUSTOM USER ROLE)
add_post_meta($post_id, 'ebbsmate_admin_ids', $board_admin_ids);

//부가 기능 - 첨부파일 사용 여부
add_post_meta($post_id, 'ebbsmate_attach_flag', $board_file_attach_flag);

//부가 기능 - 첨부파일 최대 용량
add_post_meta($post_id, 'ebbsmate_attach_size', $board_file_attach_size);

//부가 기능 - 첨부파일 최대 개수
add_post_meta($post_id, 'ebbsmate_attach_item', $board_file_attach_item);

//부가 기능 - 최상단 공지글
add_post_meta($post_id, 'ebbsmate_notice_flag', $board_notice_flag);

//부가 기능 - 금칙어 필터링
add_post_meta($post_id, 'ebbsmate_word_filtering', $board_word_filtering_flag);

//부가 기능 - 비밀글
add_post_meta($post_id, 'ebbsmate_secret_post', $board_secret_post);

//부가 기능 - 이전글/다음글
add_post_meta($post_id, 'ebbsmate_prev_next_post', $board_prev_next_post);

//부가 기능 - 이전글/다음글 개수
add_post_meta($post_id, 'ebbsmate_prev_next_lines', $board_prev_next_lines);

//레이아웃 및 스타일 - 목록 글수
add_post_meta($post_id, 'ebbsmate_list_lines', $board_list_lines);

//레이아웃 및 스타일 - 스타일
add_post_meta($post_id, 'ebbsmate_css_style', $board_css_style);

//게시글 항목 설정 - 작성자
add_post_meta($post_id, 'ebbsmate_author_flag', $author_flag);

//게시글 항목 설정 - 작성일
add_post_meta($post_id, 'ebbsmate_date_flag', $date_flag);

//게시글 항목 설정 - 조회수
add_post_meta($post_id, 'ebbsmate_vcount_flag', $vcount_flag);

//항목별 권한 설정 여부
add_post_meta($post_id, 'ebbsmate_section_flag', $board_section_flag);

//항목별 권한
add_post_meta($post_id, 'ebbsmate_section', $board_section);

//머릿말 사용 여부
//add_post_meta($post_id, 'ebbsmate_preface_flag', $board_preface_flag);

if(!empty($post_id)){
	echo "<script> window.location='".admin_url('admin.php?page=ebbsmate_board&action=success_insertboard')."';</script>";	
}else{
	echo "<script> window.location='".admin_url('admin.php?page=ebbsmate_board&action=fail_insertboard')."';</script>";
}

?>