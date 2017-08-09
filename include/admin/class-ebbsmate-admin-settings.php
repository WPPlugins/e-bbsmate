<?php
/**
 * e-BBSMate Admin Settings Class
 *
 * @author   Netville
 * @category Admin
 * @package  e-BBSMate/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'EBBSMate_Admin_Settings' ) ) :

/**
 * EBBSMate_Admin_Settings.
 */
class EBBSMate_Admin_Settings {
	
	/**
	 * 설정 저장.
	 */
	public static function save() {
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'ebbsmate_settings' ) ) {
			wp_die( __( '권한이 없습니다.', 'ebbsmate' ) );
		}
		
		$tab = isset($_GET['tab']) ? $_GET['tab'] : 'basic';
		
		//1. GET_OPTION
		$ebbsmate_settings = get_option ( 'ebbsmate_settings' );
		$prohibit_db       = $ebbsmate_settings ['prohibit_db'];
		$prohibited_words  = $ebbsmate_settings ['prohibited_words'];
		$bbs_editor        = $ebbsmate_settings ['bbs_editor'];
		
		if($tab == "basic") {
			$prohibit_db      = sanitize_text_field( $_POST ['ebbsmate_prohibit_db'] );
			$prohibited_words = sanitize_text_field( $_POST ['ebbsmate_prohibited_words'] );
		} else if($tab == "editor") {
			$bbs_editor       = sanitize_text_field( $_POST ['ebbsmate_editor'] );
		}
		
		//3. SAVE
		$set_option_value = array (
			"prohibit_db"      => $prohibit_db,
			"prohibited_words" => $prohibited_words,
			"bbs_editor"       => $bbs_editor,
		);
		
		if ( is_object( $set_option_value ) )
			$set_option_value = clone $set_option_value;
		
		$value = sanitize_option( "ebbsmate_settings", $set_option_value );
		$old_value = get_option( "ebbsmate_settings" );
		
		$message = "";
		if(update_option ( "ebbsmate_settings", $set_option_value ) || $value === $old_value) {
			$message = __("설정을 저장했습니다.");
		} else {
			$message = __("설정 저장을 실패했습니다.");
		}
		echo "<div class='updated settings-error' id='setting-error-settings_updated'><p><strong>".$message."</strong></p></div>";
	}
	
	
	/**
	 * 설정 페이지.
	 *
	 * 관지자 화면의 e-BBSMate 설정 화면을 보여준다.
	 */
	public static function output() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$return = new WP_Error( 'broke', __( "권한이 없습니다." ) );
			echo $return->get_error_message();
			return ;
		}
		
		// Save settings if data has been posted
		if ( ! empty( $_POST ) ) {
			self::save();
		}
		$tab = isset($_GET['tab']) ? $_GET['tab'] : 'basic';
		
		//1. GET_OPTION
		$ebbsmate_settings = get_option ( 'ebbsmate_settings' );
		$prohibit_db       = $ebbsmate_settings ['prohibit_db'];
		$prohibited_words  = $ebbsmate_settings ['prohibited_words'];
		$bbs_editor        = $ebbsmate_settings ['bbs_editor'];
		
		$bbs_editor = empty( $bbs_editor ) ? 'txt' : $bbs_editor;
		
		include PavoBoardMate::$PLUGIN_DIR.'templates/admin/pavo-bbs-settings.php';
	}
	
	
}

endif;
