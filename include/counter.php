<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post_Views_Counter_Counter class.
 */
class Ebbs_Views_Counter {
	
	private $cookie = array(
			'exists'		 => false,
			'visited_posts'	 => array(),
			'expiration'	 => 0
	);
	
	
	public static function init() {
		// action
		add_action( 'plugins_loaded', array( __CLASS__, 'ebbsmate_check_cookie' ) );
		add_action( 'ebbsmate_vcount_chk', array( __CLASS__, 'ebbsmate_vcount_chk' ), 1 );
	}
	
	public static function ebbsmate_check_cookie() {
		if ( isset( $_COOKIE['ebbsmateVcount'] ) && ! empty( $_COOKIE['ebbsmateVcount'] ) ) {
			$visited_posts = $expirations = array();
			
			foreach ( $_COOKIE['ebbsmateVcount'] as $content ) {
				// is cookie valid?
				if ( preg_match( '/^(([0-9]+b[0-9]+a?)+)$/', $content ) === 1 ) {
					// get single id with expiration
					$expiration_ids = explode( 'a', $content );

					// check every expiration => id pair
					foreach ( $expiration_ids as $pair ) {
						$pair = explode( 'x', $pair );
						$expirations[] = (int) $pair[0];
						$visited_posts[(int) $pair[1]] = (int) $pair[0];
					}
				}
			}
			
			$this->cookie = array(
					'exists'		 => true,
					'visited_posts'	 => $visited_posts,
					'expiration'	 => max( $expirations )
			);
			
		}
	}
	
	
	function ebbsmate_vcount_chk() {
		
		// 관리자 조회수 증가 여부 (옵션)
		
		// 방문자 조회수 증가 여부 (옵션)
		
		// cookie already existed?
		if ( $this->cookie['exists'] ) {
			// 조회 여부 확인
			if ( in_array( $id, array_keys( $this->cookie['visited_posts'] ), true ) && current_time( 'timestamp', true ) < $this->cookie['visited_posts'][$id] ) {
				// 쿠키 업데이트
				$this->ebbsmate_update_cookie( $id, $this->cookie, false );
		
				return;
			} else {
				// 쿠키 업데이트 및 조회수 증가
				$this->ebbsmate_update_cookie( $id, $this->cookie );
			}
		} else{
			// 쿠키 생성 및 조회수 증가
			$this->ebbsmate_update_cookie( $id );
		}
		// count visit
		$this->count_visit( $id );
		
	}
	
	function ebbsmate_update_cookie($id, $cookie = array(), $expired = true){
		
		$expiration = time()+86400 ;  //24시간 (추후 옵션으로 설정
		
		
		
		// 쿠키 등록
		if ( empty( $cookie ) ) {
			// set cookie
			setcookie( 'ebbsmateVcount[0]', $expiration . 'x' . $id, $expiration, COOKIEPATH, COOKIE_DOMAIN );
		} else {
			if ( $expired ) {
				// add new id or chang expiration date if id already exists
				$cookie['visited_posts'][$id] = $expiration;
			}else{
				$this->ebbsmate_update_vcount($id);
			}
			
			// create copy for better foreach performance
			$visited_posts_expirations = $cookie['visited_posts'];
			
			// get current gmt time
			$time = current_time( 'timestamp', true );
			
			// check whether viewed id has expired - no need to keep it in cookie (less size)
			foreach ( $visited_posts_expirations as $post_id => $post_expiration ) {
				if ( $time > $post_expiration )
					unset( $cookie['visited_posts'][$post_id] );
			}
			
			// set new last expiration date if needed
			$cookie['expiration'] = max( $cookie['visited_posts'] );
			
			$cookies = $imploded = array();
			
			// create pairs
			foreach ( $cookie['visited_posts'] as $id => $exp ) {
				$imploded[] = $exp . 'x' . $id;
			}
			
			// split cookie into chunks (4000 bytes to make sure it is safe for every browser)
			$chunks = str_split( implode( 'a', $imploded ), 4000 );
			
			// more then one chunk?
			if ( count( $chunks ) > 1 ) {
				$last_id = '';
			
				foreach ( $chunks as $chunk_id => $chunk ) {
					// new chunk
					$chunk_c = $last_id . $chunk;
			
					// is it full-length chunk?
					if ( strlen( $chunk ) === 4000 ) {
						// get last part
						$last_part = strrchr( $chunk_c, 'a' );
			
						// get last id
						$last_id = substr( $last_part, 1 );
			
						// add new full-lenght chunk
						$cookies[$chunk_id] = substr( $chunk_c, 0, strlen( $chunk_c ) - strlen( $last_part ) );
					} else {
						// add last chunk
						$cookies[$chunk_id] = $chunk_c;
					}
				}
			} else {
				// only one chunk
				$cookies[] = $chunks[0];
			}
			
			foreach ( $cookies as $key => $value ) {
				// set cookie
				setcookie( 'ebbsmateVcount[' . $key . ']', $value, $cookie['expiration'], COOKIEPATH, COOKIE_DOMAIN );
			}
			
		}
		
		
	}
	
	function ebbsmate_update_vcount($post_id) {
		//조회수 1 증가처리
		$cur_view_user = get_post_meta($post_id, 'ebbsmate_post_view_users', true);
		
		$cur_view_count = $cur_view_count + 1;
		update_post_meta($post_id, 'pavo_board_view_count', $cur_view_count);
	}
	
}

Ebbs_Views_Counter::init();