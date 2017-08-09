<?php
/**
 * Plugin Name: e-BBSMate for Wordpress
 * Plugin URI: http://www.netville.co.kr/
 * Description: 한국형 게시판 플러그인입니다. 
 * Version: 1.0.1
 * Author: 네트빌
 * Author URI: http://www.netville.co.kr/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'PavoBoardMate' ) ) :

class PavoBoardMate {
	
	public static $PLUGIN_DIR;
	public static $PLUGIN_URL;
	public static $PLUGIN_VER;
	
	
	function __construct() {
		self::init();
		
	
		if(get_option('ebbsmate_settings') == false) {
				
			$set_option_value = array(
				"prohibit_db"      => 0,
				"prohibited_words" => "",
				"bbs_font"         => "default",
				"bbs_editor"       => "txt"
			);
				
			add_option("ebbsmate_settings",  $set_option_value);
		}
		
		$this->includes();
		
		
		
		if(!isset($_SESSION)){
			session_start();
		}
				
		// 포스트 타입 등록
		add_action('init', array($this, 'add_ebbsmate_post_type'));
		
		// 숏코드 
		add_action( 'init', array( 'ebbsmate_Shortcode', 'init' ) );
		
		// 관리자단에 메뉴 추가
		add_action('admin_menu', array($this, 'pavo_boardmate_add_menu_page'));
		
		// 게시판 대시보드 생성
		add_action('wp_dashboard_setup', array($this, 'pavo_bbs_create_dashboard'));
		
		// 게시판 생성 숏코드
		add_shortcode('ebbsmate', array($this, 'pavo_boardmate_shortcode'));
		
		// 게시판 위젯 생성 숏코드
		add_shortcode('ebbsmate_widget', array($this, 'pavo_boardmate_wiget'));
		
		// BUTTON
		add_action('admin_head', array($this, 'ebbsmate_add_tc_button'));
				
		
		add_filter( 'post_type_link',  array($this, 'remove_cpt_slug'), 10, 3 );
		
		// SCREEN OPTIONS
		add_action('admin_init', array($this, 'save_screen_option'));
		
		// download
		add_action('template_redirect',array( $this, 'ebbsmate_attachment' ));
		
		//Powered By 필터
		add_filter('ebbsmate_powered_by', array($this, 'ebbsmate_powered_by'));
		
		add_action('wp_enqueue_scripts', array($this, 'ebbsmate_enqueue_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'ebbsmate_admin_enqueue_scripts'));
		
		add_action( 'admin_enqueue_scripts', array($this, 'load_wp_media_files'));
	}
	
	// 초기화
	public static function init() {
		self::$PLUGIN_DIR = plugin_dir_path( __FILE__ );
		self::$PLUGIN_URL = plugin_dir_url( __FILE__ );
		self::$PLUGIN_VER = "1.0";
		
	}
	
	public function includes() {
		
		if ( $this->ebbsmate_request_type( 'admin' ) ) {
			include_once( 'include/admin/pavo-bbs-admin-controller.php' );
			include_once( 'include/admin/class-ebbsmate-admin-settings.php' );
		}
		
		include_once( 'include/class-shortcode.php' );			// Shortcodes class
		include_once( 'include/pavo-bbs-userrole.php' );		// User Role class
		include_once( 'include/functions.php' );
		include_once( 'include/function-comments.php' );
		include_once( 'include/pavo-bbs-controller.php' );
		include_once( 'include/class-widget.php' );
		include_once( 'include/class-ajax.php' );
		include_once( 'include/class-create-style.php' );
		include_once( 'include/class-security.php' );
		
	}
	
	public function ebbsmate_enqueue_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-autocomplete');
		
		wp_enqueue_script('pavo-common-js', self::$PLUGIN_URL.'js/common.js', false);
		wp_localize_script( 'pavo-common-js', 'pavo_common_js', 
				array(
						'ajax_url' => admin_url('admin-ajax.php')
				)
		);
		
		wp_enqueue_script('pavoboard-front-js', self::$PLUGIN_URL.'js/front.js', false);

		wp_enqueue_script( 'jquery-tiptip', self::$PLUGIN_URL.'js/jquery-tiptip/jquery.tipTip.js', array( 'jquery' ), self::$PLUGIN_VER, true );
		wp_enqueue_style('google-fonts-css', self::$PLUGIN_URL.'css/googlefonts.css', false);
		wp_enqueue_style('ebbsmate-default-css', self::$PLUGIN_URL.'css/style.css', false);
		wp_enqueue_style('ebbsmate-front-css', self::$PLUGIN_URL.'css/ebbsmate_front.css', false);
		
		
	}
	
	public function ebbsmate_admin_enqueue_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-autocomplete');
		
		wp_enqueue_script('common_js', self::$PLUGIN_URL.'js/common.js', false);
		wp_localize_script( 'common_js', 'common_js',
				array(
						'ajax_url' => admin_url('admin-ajax.php')
				)
		);
		
		wp_enqueue_script('jquery-colorpicker', self::$PLUGIN_URL.'js/colpick.js', array('jquery'), self::$PLUGIN_VER, true);
		wp_enqueue_script('jquery-slider', self::$PLUGIN_URL.'js/nouislider.min.js', array('jquery'), self::$PLUGIN_VER, true);
		wp_enqueue_script('ebbsmate-admin', self::$PLUGIN_URL.'js/admin.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' ), self::$PLUGIN_VER, true);
	
		wp_enqueue_style('jquery-colorpicker-css', self::$PLUGIN_URL.'css/colpick.css', false);
		wp_enqueue_style('jquery-slider-css', self::$PLUGIN_URL.'css/nouislider.min.css', false);
		wp_enqueue_style('google-fonts-css', self::$PLUGIN_URL.'css/googlefonts.css', false);
		wp_enqueue_style('ebbsmate-default-css', self::$PLUGIN_URL.'css/style.css', false);
		wp_enqueue_style('ebbsmate-admin-css', self::$PLUGIN_URL.'css/admin.css', false);
	}
	
	function ebbsmate_attachment(){
		if ( ! empty( $_GET['action'] ) && $_GET['action'] == "ebbsmate_download") {
			
			$board_id = $_GET['board_id'];
			$post_id = $_GET['post_id'];
			$file_name = $_GET['file_name'];
			
			include_once( 'include/class-bbs-download.php' );
			$downloader  = new EBBSMateDownloader($board_id, $post_id,$file_name);
			$downloader->download();
			
		}
	}
	
	function ebbsmate_powered_by() {
		$power_url = "<a href='http://www.netville.co.kr' target='_blank' style='text-decoration: none;'>Powered by Netville</a>";
		return $power_url;
	}
	
	function ebbsmate_add_tc_button() { 
		
		global $typenow; // check user permissions 
		
		if ( !current_user_can('edit_posts') || !current_user_can('edit_pages') ) {
			return; 
		} 
	
		// verify the post type 
		if( ! in_array( $typenow, array( 'post', 'page' ) ) ) 
			return; 
	
		// check if WYSIWYG is enabled 
		if ( get_user_option('rich_editing') == 'true') { 
			add_filter("mce_external_plugins", array($this, "ebbsmate_add_tinymce_plugin")); 
			add_filter('mce_buttons', array($this, 'ebbsmate_register_tc_button')); 
		} 
	}
	
	function ebbsmate_add_tinymce_plugin($plugin_array) { 
		$plugin_array['ebbsmate_tc_button'] = plugins_url( '/js/tinymce.button.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE 
		
		return $plugin_array;
	}
	
	function ebbsmate_register_tc_button($buttons) {
		array_push($buttons, "ebbsmate_tc_button"); 
		return $buttons; 
	}
	
	function remove_cpt_slug( $post_link, $post, $leavename ) {
		
		global $wpdb;
	 
	    if ( ('ebbspost' != $post->post_type) || 'publish' != $post->post_status ) {
	        return $post_link;
	    }
	    
		$board_id = get_post_meta($post->ID, 'pavo_board_origin', true);
		$board_slug = get_post_meta($board_id, 'ebbsmate_board_id', true);
		
		//숏코드를 가지고있는 포스트 체크
		$sql = "select ID
		from $wpdb->posts p
		where p.post_status = 'publish'";
		
		$post_id_search = $wpdb->get_col($sql);
		$found_id = "";
		$regex = '/[ebbsmate id]*="'.$board_slug.'"/';
		
		for($i = 0; $i < sizeof($post_id_search); $i++) {
			$content_post = get_post($post_id_search[$i]);
			$content = $content_post->post_content;
				
			if(preg_match($regex, $content, $matches)) {
				$found_id = $post_id_search[$i];
				break 1;
			}
		}
		
		$post_link = get_permalink($found_id)."?action=readpost&post_id=".$post->ID;
	    	 		    
	    return $post_link;
	}	
	
	// 게시판 대시보드
	function pavo_bbs_create_dashboard() {
		wp_add_dashboard_widget("pavobbs", "e-BBSMate 최근 게시글", array($this, 'pavo_bbs_display_dashboard'));
	}
	
	// 게시판 위젯
	function pavo_bbs_display_dashboard() {
		require_once (dirname(__FILE__).'/templates/admin/pavo-bbs-dashboard.php');
	}
	
	function pavo_boardmate_add_menu_page() {
		
		global $post_options_page;
		global $board_options_page;
		
		$post_options_page = add_menu_page('e-BBSMate', 'e-BBSMate', 'administrator', 'ebbsmate', array($this, 'ebbsmate_post_menu'), self::$PLUGIN_URL.'images/icon_ebbsmate.png');
		add_submenu_page('ebbsmate', '게시글', '게시글', 'administrator', 'ebbsmate');
		$board_options_page = add_submenu_page('ebbsmate', '게시판', '게시판', 'administrator', 'ebbsmate_board', array($this, 'ebbsmate_board_menu') );
		add_submenu_page('ebbsmate', '스타일 관리', '스타일 관리', 'administrator', 'ebbsmate_style_config', array($this, 'ebbsmate_style_config_menu') );
		add_submenu_page('ebbsmate', '설정', '설정', 'administrator', 'ebbsmate_config', array($this, 'ebbsmate_config_menu') );
		add_submenu_page('ebbsmate', 'Help', __('도움말'), 'administrator', 'ebbsmate_help', array($this, 'ebbsmate_help_menu') );
	
		add_action("load-$post_options_page", array($this, "ebbsmate_post_screen_options"));
		add_action("load-$board_options_page", array($this, "ebbsmate_board_screen_options"));
		
		$post_columns = get_user_meta(get_current_user_id(), 'ebbsmate_show_post_menu', true);
		
		if(empty($post_columns)) {
			$post_columns_array = array("board", "comment", "vcount", "author", "date", 20);
			update_user_meta( get_current_user_id(), 'ebbsmate_show_post_menu', $post_columns_array );
		}
		
		$board_columns = get_user_meta(get_current_user_id(), 'ebbsmate_show_board_menu', true);

		if(empty($board_columns)) {
			$board_columns_array = array("short", "pcount", "date", "btype", 20);
			update_user_meta( get_current_user_id(), 'ebbsmate_show_board_menu', $board_columns_array );
		}
	}
	
	function ebbsmate_post_screen_options() {
	
		global $post_options_page;
					
		$screen = get_current_screen();
		
		if(!is_object($screen) || $screen->id != $post_options_page)
			return;
		
		add_filter('screen_layout_columns', array($this, 'display_post_option'));
		$screen->add_option('ebbsmate_post_screen_option', '');
	}
	
	function ebbsmate_board_screen_options() {
		global $board_options_page;
					
		$screen = get_current_screen();
		
		if(!is_object($screen) || $screen->id != $board_options_page)
			return;
		
		add_filter('screen_layout_columns', array($this, 'display_board_option'));
		$screen->add_option('ebbsmate_board_screen_option', '');
	}
	
	function display_post_option(){
	?>
		<form name="ebbsmate_screen_option_form" method="post">
			<input type="hidden" name="ebbsmate_screen_option_submit" value="1">
			<div style="display:block; margin:0; padding:8px 20px 12px; position:relative;">
				<h5 style="margin: 8px 0; font-size: 13px;"><?php _e( 'Show on screen' ); ?></h5>
				<div class="metabox-prefs">
					<label for="board-hide">
						<?php
						$post_columns = get_user_meta(get_current_user_id(), 'ebbsmate_show_post_menu', true);
						?>
						<input name="board-hide" class="hide-column-tog" id="board-hide" type="checkbox" <?php if(in_array("board", $post_columns)) {?>checked="checked"<?php }?> value="board">
						게시판명
					</label>
					<label for="comment-hide">
						<input name="comment-hide" class="hide-column-tog" id="comment-hide" type="checkbox" <?php if(in_array("comment", $post_columns)) {?>checked="checked"<?php }?> value="comment">
						댓글수
					</label>
					<label for="vcount-hide">
						<input name="vcount-hide" class="hide-column-tog" id="vcount-hide" type="checkbox" <?php if(in_array("vcount", $post_columns)) {?>checked="checked"<?php }?> value="vcount">
						조회수
					</label>
					<label for="author-hide">
						<input name="author-hide" class="hide-column-tog" id="author-hide" type="checkbox" <?php if(in_array("author", $post_columns)) {?>checked="checked"<?php }?> value="author">
						<?php echo __('Author')?>
					</label>
					<label for="date-hide">
						<input name="date-hide" class="hide-column-tog" id="date-hide" type="checkbox" <?php if(in_array("date", $post_columns)) {?>checked="checked"<?php }?> value="date">
						작성일
					</label>
				</div>
				<div class="screen-options">
					<label for="ppost_list_lines"><?php echo __('Number of items per page:')?></label>
					<input name="ppost_list_lines" class="screen-per-page" id="ppost_list_lines" type="number" maxlength="3" min="1" max="999" step="1" value="<?php echo $post_columns[sizeof($post_columns)-1]?>">
					<input name="screen-options-apply" class="button" id="screen-options-apply" type="submit" value="<?php esc_attr_e('Apply')?>">
				</div>
			</div>
		</form>
	<?php
	}
	
	function display_board_option() {
	?>
		<form name="ebbsmate_board_screen_option_form" method="post">
			<input type="hidden" name="ebbsmate_board_screen_option_submit" value="1">
			<div style="display:block; margin:0; padding:8px 20px 12px; position:relative;">
				<h5 style="margin: 8px 0; font-size: 13px;"><?php _e( 'Show on screen' ); ?></h5>
				<div class="metabox-prefs">
					<label for="short-hide">
						<?php
						$board_columns = get_user_meta(get_current_user_id(), 'ebbsmate_show_board_menu', true);						
						?>
						<input name="short-hide" class="hide-column-tog" id="short-hide" type="checkbox" <?php if(in_array("short", $board_columns)) {?>checked="checked"<?php }?> value="short">
						숏코드
					</label>
					<label for="pcount-hide">
						<input name="pcount-hide" class="hide-column-tog" id="pcount-hide" type="checkbox" <?php if(in_array("pcount", $board_columns)) {?>checked="checked"<?php }?> value="pcount">
						게시글수
					</label>
					<label for="date-hide">
						<input name="date-hide" class="hide-column-tog" id="date-hide" type="checkbox" <?php if(in_array("date", $board_columns)) {?>checked="checked"<?php }?> value="date">
						생성일
					</label>
					<label for="btype-hide">
						<input name="btype-hide" class="hide-column-tog" id="btype-hide" type="checkbox" <?php if(in_array("btype", $board_columns)) {?>checked="checked"<?php }?> value="btype">
						게시판 유형
					</label>
				</div>
				<div class="screen-options">
					<label for="pboard_list_lines"><?php echo __('Number of items per page:')?></label>
					<input name="pboard_list_lines" class="screen-per-page" id="pboard_list_lines" type="number" maxlength="3" min="1" max="999" step="1" value="<?php echo $board_columns[sizeof($board_columns)-1]?>">
					<input name="screen-options-apply" class="button" id="screen-options-apply" type="submit" value="<?php esc_attr_e('Apply')?>">
				</div>
			</div>
		</form>
	<?php
	}
	
	function save_screen_option(){
		if(isset($_POST['ebbsmate_screen_option_submit']) AND $_POST['ebbsmate_screen_option_submit'] == 1){
			$array = array();
			$i = 0;
						
			if( !empty( $_POST['board-hide'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['board-hide'] );
			}
			
			if( !empty( $_POST['comment-hide'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['comment-hide'] );
			}
			
			if( !empty( $_POST['vcount-hide'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['vcount-hide'] );
			}
			
			if( !empty( $_POST['author-hide'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['author-hide'] );
			}
			
			if( !empty( $_POST['date-hide'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['date-hide'] );
			}
			
			if( !empty( $_POST['ppost_list_lines'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['ppost_list_lines'] );
			}
			
			update_user_meta( get_current_user_id(), 'ebbsmate_show_post_menu', $array );
		}
		
		if(isset($_POST['ebbsmate_board_screen_option_submit']) AND $_POST['ebbsmate_board_screen_option_submit'] == 1){
			$array = array();
			$i = 0;
		
			if( !empty( $_POST['short-hide'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['short-hide'] );
			}
				
			if( !empty( $_POST['pcount-hide'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['pcount-hide'] );
			}
				
			if( !empty( $_POST['date-hide'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['date-hide'] );
			}
				
			if( !empty( $_POST['btype-hide'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['btype-hide'] );
			}
			
			if( !empty( $_POST['pboard_list_lines'])) {
				$i++;
				$array[sizeof($array)] = sanitize_text_field( $_POST['pboard_list_lines'] );
			}
			
			update_user_meta( get_current_user_id(), 'ebbsmate_show_board_menu', $array );
		}
	}
		
	// 게시판 관리자 메뉴 생성
	function ebbsmate_post_menu() {
		require_once (dirname(__FILE__).'/templates/admin/pavo-bbs-postlist.php');
	}
	
	function ebbsmate_board_menu() {
		require_once (dirname(__FILE__).'/templates/admin/pavo-bbs-boardlist.php');
	}
	
	function ebbsmate_style_config_menu() {
		require_once (dirname(__FILE__).'/templates/admin/pavo-bbs-configstyle.php');
	}

	function ebbsmate_config_menu() {
		EBBSMate_Admin_Settings::output();
	}

	function ebbsmate_help_menu() {
		require_once (dirname(__FILE__).'/templates/admin/pavo-bbs-help.php');
	}
	
	
	//게시판 생성 숏코드
	function pavo_boardmate_shortcode($attr) {
		global $wpdb;
		
		$board_slug = $attr['id'];
		
		$sql = "select post_id
		from $wpdb->postmeta
		where meta_key = 'ebbsmate_board_id'
		and meta_value = '".$board_slug."'";
		
		$result = $wpdb->get_row($sql);
		$post_id_search = $wpdb->get_col($sql);
		
		// 게시판 post id
		$board_id = $result->post_id;
		$post_id = empty($_GET['post_id']) ? 0 : sanitize_text_field( $_GET['post_id'] );
		$action_type = empty($_GET['action']) ? "list" :  sanitize_text_field( $_GET['action'] );
		
		ob_start();
		//컨트롤러 실행
		PavoBBSMateController::get_bbspage_load($board_id , $post_id , $action_type  );
		$bbsmateContent = ob_get_contents();
		ob_end_clean();
		return $bbsmateContent;
	}
	
	//게시판 위젯 생성 숏코드
	function pavo_boardmate_wiget($attr) {
		
		global $wpdb;
		
		$board_slug = $attr['id'];
		
		$heade_flag = true;
		if ( isset($attr['heade']) && $attr['heade'] == 'false') {
			$heade_flag = false;
			
		}
		$section = isset($attr['section'])? $attr['section'] : '';
		
		$sql = "select post_id
		from $wpdb->postmeta
		where meta_key = 'ebbsmate_board_id'
		and meta_value = '".$board_slug."'";
		
		$result = $wpdb->get_row($sql);
		// 게시판 post id
		$board_id = $result->post_id;
		
		return PavoBBSMateController::get_bbswidget_load($board_id , $attr['id'] , $attr['rows'] , $heade_flag ,$section);
		
		/*
		ob_start();
		//컨트롤러 실행
		PavoBBSMateController::get_bbswidget_load($board_id , $attr['rows'] );
		$bbsmateContent = ob_get_contents();
		ob_end_clean();
		return $bbsmateContent;
		*/
	}

	
	function add_ebbsmate_post_type() {
	
		register_post_type( 'ebbsboard',
			array(
				'labels' => array(
				'name' => __( 'e-BBSMate 게시판' ),
			),
			'public' => true,
			'show_ui' => false,
			'show_in_menu' => false,
			)
		);
	
		register_post_type( 'ebbspost',
			array(
				'labels' => array(
				'name' => __( 'e-BBSMate 게시글' ),
			),
			'public' => true,
			'show_ui' => false,
			'show_in_menu' => false,
			)
		);
		
		flush_rewrite_rules();
	}
	
	function ebbsmate_get_all_users() {
		global $wpdb;
		
		$users = array();
		$sql = "select user_login
				from wp_users";
		
		$users = $wpdb->get_col($sql);
		
		echo $users;
		
		die();
	}
	
	// UPLOAD ENGINE
	function load_wp_media_files() {
		wp_enqueue_media();
	}
	
	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin.
	 *
	 * @return bool
	 */
	private function ebbsmate_request_type( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
	
	
}

endif;

new PavoBoardMate();
?>
