<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//최근 게시글 5건 가져오기
$args= array (
		'post_type' => array('ebbspost'),
		'post_status' => 'publish',
		'posts_per_page' => 5,
		'paged' => 1,
		'orderby' => 'post_date',
		'order' => 'DESC',
);
?>
<div id="published-posts">
<!-- <h4>최근 게시글</h4> -->
<ul>
<?php $wp_query = new WP_Query($args); ?>
<?php if ( $wp_query->have_posts() ) : ?>
	<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
	<li>
		<span><?php echo the_time('F j일, g:i a');?></span>
		<a href='<?php echo admin_url('admin.php?page=ebbsmate')."&action=viewpost"?>'><?php echo ebbsmate_custom_exceprt(ebbsmate_display_prohibited_words(0, $wp_query->post->post_title))?></a>
	</li>
	<?php endwhile;?>
<?php else:?>
	<li>게시글이 존재하지 않습니다.</li>
<?php endif;?>
</ul>
</div>