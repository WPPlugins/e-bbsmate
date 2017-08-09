<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$board_id = $_GET['board_id'];

//nonce_check
if ( empty( $_GET['ebbsmate_board_nonce'] ) || empty( $_GET['board_id'] ) || ( ! wp_verify_nonce( $_GET['ebbsmate_board_nonce'], 'ebbsmate_copyboard_'.$post_id ) ) ) {
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

//게시판 정보 조회
global $wpdb;

$user_id        = get_current_user_id()? get_current_user_id() : 0;
$post_title     = get_the_title($board_id)."_복사";
$post_content	= "";

//노출 목록수
$board_list_lines = get_post_meta($board_id, 'ebbsmate_list_lines', true);
if(empty($board_list_lines)) {
	$board_list_lines = 0;
}

//위젯 노출 목록수
$board_widget_list_lines = get_post_meta($board_id, 'ebbsmate_widget_list_lines', true);
if(empty($board_widget_list_lines)) {
	$board_widget_list_lines = 0;
}

//파일 첨부 사용 여부
$board_file_attach_flag = get_post_meta($board_id, 'ebbsmate_attach_flag', true);
if(empty($board_file_attach_flag)) {
	$board_file_attach_flag = 0;
}

//파일 첨부 최대 용량
$board_file_attach_size = get_post_meta($board_id, 'ebbsmate_attach_size', true);

//최상단 공지글
$board_notice_flag = get_post_meta($board_id, 'ebbsmate_notice_top_flag', true);
if(empty($board_notice_flag)) {
	$board_notice_flag = 0;
}

//금칙어 필터링
$board_word_filtering_flag = get_post_meta($board_id, 'ebbsmate_word_filtering', true);
if(empty($board_word_filtering_flag)) {
	$board_word_filtering_flag = 0;
}

//부가 기능 - 비밀글
$board_secret_post = get_post_meta($board_id, 'ebbsmate_secret_post', true);
if(empty($board_secret_post)) {
	$board_secret_post = 0;
}

//부가 기능 - 이전글/다음글
$board_prev_next_post = get_post_meta($board_id, 'ebbsmate_prev_next_post', true);
if(empty($board_prev_next_post)) {
	$board_prev_next_post = 0;
}

//부가 기능 - 이전글/다음글 개수
$board_prev_next_lines = get_post_meta($board_id, 'ebbsmate_prev_next_lines', true);

//CSS스타일
$board_css_style = get_post_meta($board_id, 'ebbsmate_css_style', true);

//권한 설정 - 목록 보기
$board_role_list = get_post_meta($board_id, 'ebbsmate_list_role', true);

//권한 설정 - 게시글 읽기
$board_role_read = get_post_meta($board_id, 'ebbsmate_read_role', true);

//권한 설정 - 게시글 쓰기
$board_role_write = get_post_meta($board_id, 'ebbsmate_write_role', true);

//권한 설정 - 댓글 쓰기
$board_role_comment = get_post_meta($board_id, 'ebbsmate_comment_role', true);

//권한 설정 - 공지글 쓰기
$board_role_notice = get_post_meta($board_id, 'ebbsmate_notice_role', true);

//권한 추가 - 게시판 관리자(CUSTOM USER ROLE)
$board_admin_ids = get_post_meta($board_id, 'ebbsmate_admin_ids', true);
$board_admin_ids = preg_replace('/\s+/', '', $board_admin_ids);

//게시글 항목 설정 - 작성자
$author_flag = get_post_meta($board_id, 'author_flag', true);
if(empty($author_flag)) {
	$author_flag = 0;
}

//게시글 항목 설정 - 작성일
$date_flag = get_post_meta($board_id, 'date_flag', true);
if(empty($date_flag)) {
	$date_flag = 0;
}

//게시글 항목 설정 - 조회수
$vcount_flag = get_post_meta($board_id, 'vcount_flag', true);
if(empty($vcount_flag)) {
	$vcount_flag = 0;
}

//게시판 항목 설정 여부
$board_section_flag = get_post_meta($board_id, 'ebbsmate_section_flag', true);

//게시판 항목 및 권한
if($board_section_flag) {
	$board_section = get_post_meta($board_id, 'ebbsmate_section', true);
}else{
	$board_section = '';
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

echo "<script> window.location='".admin_url('admin.php?page=ebbsmate_board&board_copied=true')."';</script>";
?>