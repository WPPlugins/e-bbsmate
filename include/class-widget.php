<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'PavoBoardMate' ) ) {
	die();
}

add_action( 'widgets_init', 'ebbsmate_register_widget' );

if ( ! function_exists( 'ebbsmate_register_widget' ) ) {
	function ebbsmate_register_widget() {
		register_widget( 'ebbsmate_widget' );
	}
}



if ( ! class_exists( 'ebbsmate_widget' ) ) {
	
	class ebbsmate_widget extends WP_Widget {
	
		public function __construct() {
			
			$widget_ops = array(
					'classname' => 'ebbsmate_widget',
					'description' => __( '원하는 게시판을 사이드바에 추가할 수 있습니다.'),
			);
			
			parent::__construct( 'ebbsmate_widget', __('e-BBSMate 게시판'), $widget_ops );
		}
		
		public function widget( $args, $instance ) {
			global $wpdb, $current_user;
			
			$title = apply_filters( 'widget_title', $instance['title'] );
			$board_id = empty($instance['board_id'])? '': $instance['board_id'];
			$list_lines = empty($instance['list_lines'])? '3': $instance['list_lines'];
		
			$code_id = get_post_meta( $board_id, 'ebbsmate_board_id', true );
			$shortcode = '[ebbsmate id="'.$code_id.'"]';
		
			$results = $wpdb->get_results('SELECT ID FROM '.$wpdb->posts." WHERE post_content LIKE '%".$shortcode."%' AND post_status = 'publish'");
			 
			$url = "";
			if(!empty($results)){
				$url = get_permalink($results[0]->ID);
			}
		
			echo $args['before_widget'];
			if ( ! empty( $title ) )
				echo $args['before_title'] . $title . $args['after_title'];
			
			
			//게시판 분류 Role
			$board_section = get_post_meta($board_id, 'ebbsmate_section', true);
			//게시판 관리자
			$board_admin = get_post_meta($board_id, "ebbsmate_admin_ids", true);
			$board_admin = empty($board_admin)? '' :$board_admin;
		
			$args= array (
				'post_type' => array('ebbspost'),
				'post_status' => 'publish',
				'post_parent' => 0,
				'posts_per_page' => $list_lines,
				'orderby' => 'post_date',
				'order' => 'DESC',
				'meta_query' => array(
					array(
						'key' => 'pavo_board_origin',
						'value' => $board_id
					),
					array(
						'key' => 'pavo_board_notice_flag',
						'compare' => '!=',
						'value' => 1
					),
					array(
						'relation' => 'OR',
						array(
								'key' => 'pavo_section_val',
								'compare' => 'NOT EXISTS',
						),
						array(
								'key' => 'pavo_section_val',
								'compare' => 'IN',
								'value'     => pavoebbsmate_simple_role_check($current_user, $board_admin, $board_section),
						),
					),
				)
			);
		
			$wp_query = new WP_Query($args);
			?>
			<ul>
			<?php
				if ( $wp_query->have_posts() ) {
				while ( $wp_query->have_posts() ) : $wp_query->the_post();
			?>
				<li><a href="<?php echo $url."?action=readpost&post_id=".$wp_query->post->ID ?>"><?php echo ebbsmate_custom_exceprt(ebbsmate_display_prohibited_words($board_id, $wp_query->post->post_title))?></a></li>
				<?php
				endwhile;
				wp_reset_postdata();
				}else{
				//게시글이 없을 경우
				?>
				<li>게시글이 존재하지 않습니다.</li>
				<?php
				}
				?>
				</ul>
				<?php
				if(!empty($args['after_widget'])) {
					echo $args['after_widget'];
				}
			}
		
		public function form( $instance ) {
			global $wpdb;
			
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			}else {
				$title = __( '게시판' );
			}
				
			if ( isset( $instance[ 'list_lines' ] ) ) {
				$list_lines = $instance[ 'list_lines' ];
			}else {
				$list_lines = "3";
			}
			
			$sql        = "SELECT id, post_title FROM $wpdb->posts	WHERE post_type = 'ebbsboard' AND post_status = 'publish' ORDER BY post_date DESC";
			$ebbs_boardlist = $wpdb->get_results( $sql );
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e( '게시판 선택:' ); ?>
					<select class='widefat' id="<?php echo $this->get_field_id('board_id'); ?>" name="<?php echo $this->get_field_name('board_id'); ?>" type="text">
					<?php 	
					foreach ( $ebbs_boardlist as $ebbs_board ) {
						$selected = '';
						if ( $ebbs_board ->id == $instance[ 'board_id' ] ) {
							$selected = ' selected="selected"';
						}
						
						echo '<option value="' . absint( $ebbs_board ->id ) . '" ' . $selected . '>' . $ebbs_board ->post_title . '</option>';
					}
					?>
					</select>                
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'list_lines' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label> 
				<input id="<?php echo $this->get_field_id( 'list_lines' ); ?>" name="<?php echo $this->get_field_name( 'list_lines' ); ?>" type="text" value="<?php echo esc_attr( $list_lines ); ?>" size="3"/>
			</p>
			<?php 
		}
			
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['board_id'] = $new_instance['board_id'];
			$instance['list_lines'] = $new_instance['list_lines'];
			return $instance;
		}
		
	}
}