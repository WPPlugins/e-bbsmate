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

$return_widget .= "<tr>";
$return_widget .= "<td class='pavoboard-list-title'><a href='".$url."'>".$title."</a>";
$return_widget .= "<td class='pavoboard-list-writer'><span>".ebbsmate_get_the_author($wp_query->post->ID)."</span></td>";
$return_widget .= "<td class='pavoboard-list-date'>".date('Y.m.d', strtotime($wp_query->post->post_date))."</td>";
$return_widget .= "<td class='pavoboard-list-hit'>".$view_count."</td>";
$return_widget .= "</tr>";


