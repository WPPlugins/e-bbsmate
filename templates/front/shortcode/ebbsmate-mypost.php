<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if($total_comment_cnt > 0) {
	$title .= "<span class='entry_comment_count'>(".$total_comment_cnt.")</span>";
}

if($secret_flag) {
	$title .= "<span class='pavoboard-list-icon pavoboard-secret'>비밀글 설정됨</span>";
}

if($total_file_cnt > 0) {
	$title .= "<span class='pavoboard-list-icon pavoboard-attach'>파일 첨부됨</span>";
}

$return_html .= "<tr>";
$return_html .= "<td class='pavoboard-list-title'>".$board_name;
$return_html .= "<td class='pavoboard-list-title'><a href='".$post_url."'>".$title."</a>";
$return_html .= "<td class='pavoboard-list-date'>".date('Y.m.d', strtotime($myposts->post->post_date))."</td>";
$return_html .= "<td class='pavoboard-list-hit'>".$view_count."</td>";
$return_html .= "</tr>";


