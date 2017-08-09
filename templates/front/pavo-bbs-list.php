<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

$author_flag = get_post_meta($board_id, "ebbsmate_author_flag", true);
$date_flag = get_post_meta($board_id, "ebbsmate_date_flag", true);
$vcount_flag = get_post_meta($board_id, "ebbsmate_vcount_flag", true);

//게시판 항목 설정 여부
$board_section_flag = get_post_meta($board_id, "ebbsmate_section_flag", true);

$table_size = 5;
if(!$author_flag) {
	$table_size = $table_size -1;
}
if(!$date_flag) {
	$table_size = $table_size -1;
}
if(!$vcount_flag) {
	$table_size = $table_size -1;
}

//검색어
$search_option = isset($_GET['search_option'])? $_GET['search_option'] : "";
$search_text = isset($_GET['search_text'])? $_GET['search_text'] : "";

$posts_per_page = get_post_meta($board_id, "ebbsmate_list_lines",true);
$paged = isset($_GET['bbspaged']) ? $_GET['bbspaged'] : 1;


$section_metaquery = array();

//분류 항목
if(!empty($_GET['section'])){
	$section_metaquery = array(	'key' => 'pavo_section_val', 'value' => $_GET['section'] );
};

$notice_flag = true;
$post_flag = true;

//게시판 분류 Role
$board_section = get_post_meta($board_id, 'ebbsmate_section', true);
//게시판 관리자
$board_admin = get_post_meta($board_id, "ebbsmate_admin_ids", true);
$board_admin = empty($board_admin)? '' :$board_admin;
//공지글 불러오기
$notice_list = array (
	'post_type' => array('ebbspost'),
	'post_status' => 'publish',
	'posts_per_page' => 5,
	//'paged' => $paged,
	'orderby' => 'post_date',
	'order' => 'DESC',
	'relation' => 'AND',
	'meta_query' => array(
		array(
			'key' => 'pavo_board_origin',
			'value' => $board_id
		),
		array(
			'key' => 'pavo_board_notice_flag',
			'value' => "1"
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
		),$section_metaquery
	)
);


$cur_board_url = get_page_link();

?>
  
<!-- S:목록 -->
<div class="pavoboard-wrapper pavoboard-custom">
<?php 
if($board_section_flag){
	echo "<div class='ebbsmate-section-wrapper'>";
	$section_tab = pavoebbsmate_simple_role_check($current_user, $board_admin, $board_section);
	unset($section_tab[0]);
	if(empty($_GET['section'])){
		echo sprintf("<a %s class='%s'>%s</a>",'','section_tab active','전체');
	}else{
		echo sprintf("<a %s class='%s'>%s</a>",'href="'.$cur_board_url.'"','section_tab','전체');
	}
	foreach ($section_tab as $tab){
		$linkUrl = '';
		$class = 'section_tab active';
		if(empty($_GET['section']) || $tab != $_GET['section']){
			$linkUrl = 'href="'.$cur_board_url.'?section='.$tab.'"';
			$class = 'section_tab';
		}
		echo sprintf("<a %s class='%s'>%s</a>",$linkUrl,$class,$tab);
	}
	echo "</div>";
// 	sprintf("<label for='ebbsmate_itemized_%s'>%s<input type='checkbox' value='%s' id ='ebbsmate_itemized_%s' name='ebbsmate_section[%s][permission][]' %s></label>", $index."_".$key , translate_user_role($role), $key , $index."_".$key, $section['title'], in_array($key, $section['permission']) ? "checked='checked'" : "");
}
?>
  	<table class="pavoboard-table" summary="게시판" id="pavoboard-table">
        <caption class="blind">게시판 전체목록</caption>
        <thead>
          <tr>
            <th class="entry-th-number"><span>번호</span></th>
            <th class="entry-th-title"><span>제목</span></th>
            <?php if($author_flag == "1") {?>
            <th class="entry-th-writer"><span>작성자</span></th>
            <?php }?>
            <?php if($date_flag == "1") {?>
            <th class="entry-th-date"><span>작성일 </span></th>
            <?php }?>
            <?php if($vcount_flag == "1") {?>
            <th class="entry-th-hit"><span>조회</span></th>
            <?php }?>
          </tr>
        </thead>
        <tbody>
        <?php
	
	$notice_query = new WP_Query($notice_list);
	
	/*
	 *  공지글
	 */
	if ( $notice_query->have_posts() ) :
		while ( $notice_query->have_posts() ) : $notice_query->the_post();
	
		//코맨트 개수 가져오기
		$comments_count = wp_count_comments( $notice_query->post->ID );
        $total_comment_cnt = $comments_count->approved;
		
		//파일 첨부했는지 체크
// 		$board_id = get_post_meta($notice_query->post->ID, "pavo_board_origin", true);
		$file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
		
		$fileNames=get_post_meta($notice_query->post->ID, "pavo_bbs_file_name", false);
		$filePaths=get_post_meta($notice_query->post->ID, "pavo_bbs_file_path", false);
		
		$total_file_cnt = 0;
		
		if(!empty($fileNames[0])) {
			$total_file_cnt = sizeof($fileNames[0]);
		} else {
			$total_file_cnt = 0;
		}
		
		$notice_readpost_link = get_permalink($cur_post_id);
		$params = array(
				'action' => "readpost",
				'post_id' => $notice_query->post->ID,
				'bbspaged' => $paged,
		);
		
		//분류 항목
		if(!empty($_GET['section'])){
			$params['section'] = $_GET['section'];
		};
		$notice_readpost_link = add_query_arg( $params, $notice_readpost_link );
		
		$notice_title = ebbsmate_display_prohibited_words($board_id, $notice_query->post->post_title);
		$notice_secret_flag = get_post_meta($notice_query->post->ID, "pavo_board_secret_flag", true);
		
		?>
		<tr>
            <td class="pavoboard-list-number noti-td"><span class="noti-icon">공지</span></td>
            <td class="pavoboard-list-title noti-td">
                <a href="<?php echo $notice_readpost_link?>">
	              	<?php echo $notice_title ?>
	              	<?php if($total_comment_cnt > 0) {?>
	            	<span class="entry_comment_count">(<?php echo $total_comment_cnt?>)</span>
	            	<?php }?>
	              	<?php if($notice_secret_flag) {?>
	            	<span class="pavoboard-list-icon pavoboard-secret">비밀글 설정됨</span>
	            	<?php }?>
	              	<?php if($total_file_cnt > 0) {?>
	            	<span class="pavoboard-list-icon pavoboard-attach">파일 첨부됨</span>
	            	<?php }?>
              	</a>
              <div class="mobile-writer-info noti-td">
                <ul>
                  <li><?php echo ebbsmate_get_post_author_template_mobile( $notice_query->post->ID , $cur_board_url); ?></li>
                  <?php if (date('Y/m/d') == date('Y/m/d', strtotime($notice_query->post->post_date))) :?>
                  <li>작성일 : <?php echo date('H:i', strtotime($notice_query->post->post_date))?></li>
                  <?php else :?>
                  <li>작성일 : <?php echo date('Y.m.d', strtotime($notice_query->post->post_date))?></li>
                  <?php endif;?>
                  <li>조회수 : <?php echo get_post_meta($notice_query->post->ID, 'pavo_board_view_count', true)?></li>
                </ul>
              </div></td>
            <?php if($author_flag == "1") {?>
            <td class="pavoboard-list-writer noti-td">
            	<?php echo ebbsmate_get_post_author_template( $reply_query->post->ID , $cur_board_url); ?>
            </td>
            <?php }?>
            <?php if($date_flag == "1") {?>
            <?php if (date('Y/m/d') == date('Y/m/d', strtotime($notice_query->post->post_date))) :?>
            <td class="pavoboard-list-date noti-td"><?php echo date('H:i', strtotime($notice_query->post->post_date))?></td>
            <?php else :?>
            <td class="pavoboard-list-date noti-td"><?php echo date('Y.m.d', strtotime($notice_query->post->post_date))?></td>
            <?php endif;?>
            <?php }?>
            <?php if($vcount_flag == "1") {?>
            <td class="pavoboard-list-hit noti-td"><?php echo get_post_meta($notice_query->post->ID, 'pavo_board_view_count', true)?></td>
            <?php }?>
          </tr>
	<?php 
		endwhile;
	else :
		$notice_flag = false;
	endif;
	 ///////////////////공지사항 불러오기 끝
	 
	//검색어 적용하기
	 $sql = "";
	 if($search_option == "t") {
	 	$sql = "select ID
	 	from $wpdb->posts p
	 	where p.post_title LIKE '%".$search_text."%'
	 	and p.post_type='ebbspost'";
	} else if($search_option == "tc") {
	 	$sql = "select ID
	 	from $wpdb->posts p
	 	where p.post_title LIKE '%".$search_text."%'
	 	or p.post_content LIKE '%".$search_text."%'
	 	and p.post_type='ebbspost'";
	} else if($search_option == "a") {
	 	$user = get_user_by( 'login', $search_text );
	 
	 		$sql = "select p.ID from $wpdb->posts p, $wpdb->postmeta m
	 		where p.ID = m.post_id
	 		and m.meta_key = 'pavo_board_guest_name'
	 		and m.meta_value LIKE '%".$search_text."%'
  			and p.post_type='ebbspost'";
	 
	 		$sql2 = "select p.ID from $wpdb->posts p, $wpdb->users u
	 		where p.post_author = u.ID
	 		and u.user_nicename LIKE '%".$search_text."%'
	 		and p.post_type='ebbspost'";
	}else if($search_option == "id"){
		$sql = "select ID
		from $wpdb->posts p
		where p.post_author = ".$search_text."
	 	and p.post_type='ebbspost'";
	}
	
	if($search_option == "a") {
		$result1 = $wpdb->get_col($sql);
		$result2 = $wpdb->get_col($sql2);
	
		$post_id_search = array_merge($result1, $result2);
	} else {
		$post_id_search = $wpdb->get_col($sql);
	}
	
	if(empty($post_id_search)){
		$post_id_search = array(-1);
	}
	
	
	//검색
 	if(!empty($search_option) && !empty($search_text)) {
 		//게시글 목록 가져오기
	 	$args= array (
	 		'post__in' 			=> $post_id_search,
	 		'post_type' 		=> array('ebbspost'),
	 		'post_status' 		=> 'publish',
	 		'post_parent' 		=> 0,
	 		'posts_per_page'	=> $posts_per_page,
			'paged' 			=> $paged,
			'orderby' 			=> 'ID',
	 		'order' 			=> 'DESC',
	 		'meta_query' 		=> 
	 			array(
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
 					),$section_metaquery
	 			)
	 	);
 	} else {
	 	//게시글 목록 가져오기
	 	$args= array (
	 		'post_type' => array('ebbspost'),
	 		'post_status' => 'publish',
	 		'post_parent' => 0,
	 		'posts_per_page' => $posts_per_page,
			'paged' => $paged,
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
 				),$section_metaquery
	 		)
	 	);
	}
	
	$wp_query = new WP_Query($args);
// 	echo "목록 가져오기 SQL-Query: {$wp_query->request}";
	
	$total_post_cnt = $wp_query->found_posts;
	$snum = $total_post_cnt - (($paged-1)*$posts_per_page) +1;
	
	//게시글 불러오기 시작
	$index = 0;
	if ( $wp_query->have_posts() ) :
	while ( $wp_query->have_posts() ) : $wp_query->the_post();
	
	//읽기 권한 여부 확인
	if($wp_query->post->ID == 591) continue;
	
	$index++;
	
	//코맨트 개수 가져오기
	$comments_count = wp_count_comments( $wp_query->post->ID );
    $total_comment_cnt = $comments_count->approved;
	
	//파일 첨부했는지 체크
// 	$board_id = get_post_meta($wp_query->post->ID, "pavo_board_origin", true);
	$file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
	
	$fileNames=get_post_meta($wp_query->post->ID, "pavo_bbs_file_name", false);
	$filePaths=get_post_meta($wp_query->post->ID, "pavo_bbs_file_path", false);
	
	$total_file_cnt = 0;
	
	if(!empty($fileNames) && sizeof($fileNames[0]) > 0) {
		$total_file_cnt = sizeof($fileNames[0]);
	} else {
		$total_file_cnt = 0;
	}
	
	$readpost_link = get_permalink($cur_post_id);
	$params = array( 
		'action' => "readpost",
		'post_id' => $wp_query->post->ID,
		'bbspaged' => $paged,
	);
	//분류 항목
	if(!empty($_GET['section'])){
		$params['section'] = $_GET['section'];
	};
	$readpost_link = add_query_arg( $params, $readpost_link );
	
	
	// 분류명 표시
	$section_title = '';
	if($board_section_flag){
		$section_title = get_post_meta($wp_query->post->ID, 'pavo_section_val', true);
		if($section_title != 'all' && !empty($section_title)){
			$section_title = '['.$section_title.']';
		}else{
			$section_title = '';
		}
	}
	
	$title = ebbsmate_display_prohibited_words($board_id, $wp_query->post->post_title);
	$secret_flag = get_post_meta($wp_query->post->ID, "pavo_board_secret_flag", true);
	?>
          <tr>
            <td class="pavoboard-list-number"><span><?php echo $snum - $index; ?></span></td>
            <td class="pavoboard-list-title">
            	<a href="<?php echo $readpost_link?>" class="<?php if($wp_query->post->post_author == 0 && !is_pvbbsadmin($board_id) && $secret_flag) echo 'guest-secret'?>" post_num="<?php echo $wp_query->post->ID;?>">
	              	<?php echo $section_title . $title ?>
	              	<?php if($total_comment_cnt > 0) {?>
	            	<span class="entry_comment_count">(<?php echo $total_comment_cnt?>)</span>
	            	<?php }?>
	              	<?php if($secret_flag) {?>
	            	<span class="pavoboard-list-icon pavoboard-secret">비밀글 설정됨</span>
	            	<?php }?>
	              	<?php if($total_file_cnt > 0) {?>
	            	<span class="pavoboard-list-icon pavoboard-attach">파일 첨부됨</span>
	            	<?php }?>
              	</a>
              <div class="mobile-writer-info">
                <ul>
                  <li><?php echo ebbsmate_get_post_author_template_mobile( $wp_query->post->ID , $cur_board_url); ?></li>
                  <?php if (date('Y/m/d') == date('Y/m/d', strtotime($wp_query->post->post_date))) :?>
                  <li>작성일 : <?php echo date('H:i', strtotime($wp_query->post->post_date))?></li>
                  <?php else :?>
                  <li>작성일 : <?php echo date('Y.m.d', strtotime($wp_query->post->post_date))?></li>
                  <?php endif;?>
                  <li>조회수 : <?php echo get_post_meta($wp_query->post->ID, 'pavo_board_view_count', true)?></li>
                </ul>
              </div>
             </td>
             <?php if($author_flag == "1") {?>
            <td class="pavoboard-list-writer">
            	<?php echo ebbsmate_get_post_author_template( $wp_query->post->ID , $cur_board_url); ?>
            </td>
             <?php }?>
             <?php if($date_flag == "1") {?>
             <?php if (date('Y/m/d') == date('Y/m/d', strtotime($wp_query->post->post_date))) :?>
            <td class="pavoboard-list-date"><?php echo date('H:i', strtotime($wp_query->post->post_date))?></td>
            <?php else :?>
            <td class="pavoboard-list-date"><?php echo date('Y.m.d', strtotime($wp_query->post->post_date))?></td>
            <?php endif;?>
            <?php }?>
            <?php if($vcount_flag == "1") {?>
            <td class="pavoboard-list-hit"><?php echo get_post_meta($wp_query->post->ID, 'pavo_board_view_count', true)?></td>
            <?php }?>
          </tr>
          <?php 
          /////////////////////////////////////// 답글 ///////////////////////////////////////
          
			$reply_args= array (
				'post_type' => array('ebbspost'),
				'post_status' => 'publish',
				'post_parent' => $wp_query->post->ID,
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
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
					),$section_metaquery
				)
			);
			$reply_query = new WP_Query($reply_args);
	
			if ( $reply_query->have_posts() ) :
			while ( $reply_query->have_posts() ) : $reply_query->the_post();
			
			//코맨트 개수 가져오기
			$comments_count = wp_count_comments( $reply_query->post->ID );
            $total_comment_cnt = $comments_count->approved;
			
			//파일 첨부했는지 체크
// 			$board_id = get_post_meta($reply_query->post->ID, "pavo_board_origin", true);
			$file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
			
			$fileNames=get_post_meta($reply_query->post->ID, "pavo_bbs_file_name", false);
			$filePaths=get_post_meta($reply_query->post->ID, "pavo_bbs_file_path", false);
			
			$total_file_cnt = 0;
			
			if(!empty($fileNames) && sizeof($fileNames[0]) > 0) {
				$total_file_cnt = sizeof($fileNames[0]);
			} else {
				$total_file_cnt = 0;
			}
			
			$reply_readpost_link = get_permalink($cur_post_id);
			$params = array(
					'action' => "readpost",
					'post_id' => $wp_query->post->ID,
					'bbspaged' => $paged,
			);
			//분류 항목
			if(!empty($_GET['section'])){
				$params['section'] = $_GET['section'];
			};
			$reply_readpost_link = add_query_arg( $params, $reply_readpost_link );
			
			$reply_title = ebbsmate_display_prohibited_words($board_id, $reply_query->post->post_title);
			$reply_secret_flag = get_post_meta($reply_query->post->ID, "pavo_board_secret_flag", true);
			?>
          <tr>
            <td class="pavoboard-list-number"><span></span></td>
            <td class="pavoboard-list-title reply-list-title">
            	<a href="<?php echo $reply_readpost_link?>" class="<?php if($reply_query->post->post_author == 0 && !is_pvbbsadmin($board_id) && $reply_secret_flag) echo 'guest-secret'?>" post_num="<?php echo $reply_query->post->ID;?>">
	              	<span class="icon-reply">Re:<?php echo $reply_title ?></span>
	              	<?php if($total_comment_cnt > 0) {?>
	            	<span class="entry_comment_count">(<?php echo $total_comment_cnt?>)</span>
	            	<?php }?>
	              	<?php if($reply_secret_flag) {?>
	            	<span class="pavoboard-list-icon pavoboard-secret">비밀글 설정됨</span>
	            	<?php }?>
	              	<?php if($total_file_cnt > 0) {?>
	            	<span class="pavoboard-list-icon pavoboard-attach">파일 첨부됨</span>
	            	<?php }?>
              	</a>
              <div class="mobile-writer-info">
                <ul>
                  <li><?php echo ebbsmate_get_post_author_template_mobile( $reply_query->post->ID , $cur_board_url); ?></li>
                  <?php if (date('Y/m/d') == date('Y/m/d', strtotime($reply_query->post->post_date))) :?>
                  <li>작성일 : <?php echo date('H:i', strtotime($reply_query->post->post_date))?></li>
                  <?php else :?>
                  <li>작성일 : <?php echo date('Y.m.d', strtotime($reply_query->post->post_date))?></li>
                  <?php endif;?>
                  <li>조회수 : <?php echo get_post_meta($reply_query->post->ID, 'pavo_board_view_count', true)?></li>
                </ul>
              </div></td>
            <?php if($author_flag == "1") {?>
            <td class="pavoboard-list-writer">
            	<?php echo ebbsmate_get_post_author_template( $reply_query->post->ID , $cur_board_url); ?>
            </td>
            <?php }?>
            <?php if($date_flag == "1") {?>
            <?php if (date('Y/m/d') == date('Y/m/d', strtotime($reply_query->post->post_date))) :?>
            <td class="pavoboard-list-date"><?php echo date('H:i', strtotime($reply_query->post->post_date))?></td>
            <?php else :?>
            <td class="pavoboard-list-date"><?php echo date('Y.m.d', strtotime($reply_query->post->post_date))?></td>
            <?php endif;?>
            <?php }?>
            <?php if($vcount_flag == "1") {?>
            <td class="pavoboard-list-hit"><?php echo get_post_meta($reply_query->post->ID, 'pavo_board_view_count', true)?></td>
            <?php }?>
          </tr>
          <?php endwhile;
          endif;?>
          <?php endwhile;
          else :
          	$post_flag = false;
          endif;?>
          
         	<?php 
			//게시글이 없을 경우
			if( empty($search_option) && !$post_flag  && !$notice_flag) :
			?>
			<tr>
				<td colspan=<?php echo $table_size ?>>게시글이 존재하지 않습니다.</td>
			</tr>
			<?php
			endif;
			
			if(!empty($search_option) && !empty($search_text) && !$post_flag) :
			?>
			<tr>
				<td colspan=<?php echo $table_size ?>>검색 결과가 없습니다.</td>
			</tr>
			<?php
			endif;
			?>
			
			
        </tbody>
      </table>
     <!-- S:paging-area --> 
       <div class="paging-area">
         <div id="pagingBar" class="pagingNav up">
         <?php
		if(!empty($search_option) && !empty($search_text)) {
			$page_args= array (
				'post__in' => $post_id_search,
				'post_type' => array('ebbspost'),
				'post_status' => 'publish',
				'posts_per_page'=>-1,
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
					),$section_metaquery
				)
			);
		} else {
			$page_args= array (
				'post_type' => array('ebbspost'),
				'post_status' => 'publish',
				'posts_per_page'=>-1,
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
					),$section_metaquery
				)
			 );
		 }
		 
		 //검색어 파라미터
		 $search_param = "";
		 	
		 if(!empty($search_option) && !empty($search_text)) {
		 	$search_param = "&search_option=".$search_option."&search_text=".urlencode($search_text);
		 }
		 	
		 //화면에 5페이지씩 표시
		 $screenCnt = 5;
		 
		 $totalCnt = count(get_posts($page_args));
		 $pageCnt = intval($totalCnt/$posts_per_page);
		 	
		 if($totalCnt%$posts_per_page <= 0) {
		 	$max_page = $pageCnt;
		 } else {
		 	$pageCnt = $pageCnt + 1;
		 	$max_page = $pageCnt;
		 }
		 	
		 $page = 0;
		 	
		 $minPage = ((ceil($paged / $screenCnt) -1) * $screenCnt) + 1;
		 $maxPage = ceil($paged / $screenCnt) * $screenCnt;
		 
		 
		 //페이지 갯수가 0일 경우에
		 if($pageCnt == 0) {
		 ?>
 		 	<strong>1</strong> 
 		 <?php
 		 }
 		 ?>
 		 <?php if($paged > 1) { ?>
         	  <a title="pre_end" href="<?php echo get_permalink($cur_post_id); ?>?bbspaged=1<?php echo $search_param?>" class="pre_end"><span>처음</span></a>
         <?php }?>
		 <?php 
		 if($paged > 1) {
		 ?>
              <a title="pre" href="<?php echo get_permalink($cur_post_id); ?>?bbspaged=<?php echo ($paged)-1 ."".$search_param?>" class="pre"><span>이전</span></a>
         <?php
		 } else {
		 ?>
		 <?php
		 }
	
		 ?>
		 <?php	
		 for($page = $minPage; $page <= $maxPage; $page++) {
		 	if($paged == $page && $pageCnt != 0) {
			?>
              <strong><?php echo $page ?></strong>
            <?php
			} else if($page<= $max_page) {
			?>
              <a title="<?php echo $page?>" href="<?php echo get_permalink($cur_post_id); ?>?bbspaged=<?php echo $page ."".$search_param?>"><?php echo $page?></a>
            <?php
			}
			}
			if($page > $max_page) {
			?>
			 <?php
			 } else {
			 ?>
              <a title="next" href="<?php echo get_permalink($cur_post_id) ?>?bbspaged=<?php echo ($paged)+1 ."".$search_param?>" class="next"><span>다음</span></a>
             <?php
			 }
			 ?>
			 <?php if($max_page > $paged): ?>
              <a title="next_end" href="<?php echo get_permalink($cur_post_id) ?>?bbspaged=<?php echo $max_page ."".$search_param ?>" class="next_end"><span>끝</span></a>
             <?php endif;?>
          </div>
          <?php if(get_user_roles($board_id, "write")) :?>
          <a class="pavoboard-button write-button" href="<?php echo get_permalink($cur_post_id)."?action=insertpost"?>"><span>글쓰기</span></a>
      	  <?php endif;?>
      </div>
      <!-- E:paging-area --> 
      
      <!-- S:pavoboard-controller -->
      <div class="pavoboard-controller search-center">
        <!-- S:pavoboard-search -->
        <div class="pavoboard-search">      
          <form method="get" name="ebbsmate_search_form">
               <select class="styled" name="search_option">
                <option value="t" <?php selected("t", $search_option)?>>제목</option>
 				<option value="tc" <?php selected("tc", $search_option)?>>제목+내용</option>
 				<option value="a" <?php selected("a", $search_option)?>>작성자</option>
              </select>
              <input type="text" class="pavoboard-keyword" name="search_text" value="<?php echo ($search_option != 'id')? $search_text : ''?>">
              <?php 
              if(!empty($_GET['section'])){
              	echo sprintf("<input type='hidden' name='section' value='%s' />" , $_GET['section']);
              };
              ?>
              <a class="btn-pavoboard-search pavoboard-button" href="#" onclick="document.forms.ebbsmate_search_form.submit();">검색</a>
          </form>
        </div>
        <!-- E:pavoboard-search --> 
       </div>
       <!-- E:pavoboard-controller -->
	</div>
	<input type="hidden" name="pavo_post_cur_board_page" value="<?php echo $cur_post_id ?>"/>  
  <!-- E:목록 --> 
  <?php
  wp_reset_postdata();
