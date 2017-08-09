<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$post_id = $_GET['post_id'];

//게시글이 존재하는지 체크
if ( get_post_status ( $post_id ) ) { 
	
	wp_enqueue_script('pavoboard-attach', PavoBoardMate::$PLUGIN_URL.'js/front-attachment.js', false);
	
	$paged = isset($_GET['bbspaged']) ? $_GET['bbspaged'] : 1;

	$author_flag = get_post_meta($board_id, "ebbsmate_author_flag", true);
	$date_flag = get_post_meta($board_id, "ebbsmate_date_flag", true);
	$vcount_flag = get_post_meta($board_id, "ebbsmate_vcount_flag", true);

	$board_css_style = get_post_meta($board_id, "ebbsmate_css_style", true);

	$is_notice = get_post_meta($post_id, 'pavo_board_notice_flag', true);

	//add_action( 'init', 'ebbsmate_vcount_dupl_chk');
	//add_action( 'init', 'ebbsmate_vcount_dupl_chk');
	ebbsmate_vcount_dupl_chk($board_id , $post_id);
	
	
	$prev_next_flag = get_post_meta($board_id, 'ebbsmate_prev_next_post', true);
	$prev_next_lines = $prev_next_flag? get_post_meta($board_id, 'ebbsmate_prev_next_lines', true) : 1;
	
	//게시판 항목 설정 여부
	$board_section_flag = get_post_meta($board_id, "ebbsmate_section_flag", true);
	
	if(!$board_section_flag){
		$preview_posts = $wpdb->get_results("select * from (
				select * from (
				select * from $wpdb->posts as p, $wpdb->postmeta as m
				where
				p.id = m.post_id
				and p.id = ".$post_id."
				and p.post_status = 'publish'
				and m.meta_key='pavo_board_origin'
		) a
				union
				select * from (
				select * from $wpdb->posts as p, $wpdb->postmeta as m
				where
				p.id = m.post_id
				and p.id > ".$post_id."
					and p.post_status = 'publish'
					and m.meta_key='pavo_board_origin'
					and m.meta_value = ".$board_id."
					order by p.ID asc limit ".$prev_next_lines."
		) b
				union
				select * from (
				select * from $wpdb->posts as p, $wpdb->postmeta as m
				where
				p.id = m.post_id
				and p.id < ".$post_id."
					and p.post_status = 'publish'
					and m.meta_key='pavo_board_origin'
					and m.meta_value = ".$board_id."
					order by p.ID desc limit ".$prev_next_lines."
			) c
		
		
	) d order by id desc; "
		
		);
	}else{
		global $current_user;
		
		//게시판 분류 Role
		$board_section = get_post_meta($board_id, 'ebbsmate_section', true);

		//게시판 관리자
		$board_admin = get_post_meta($board_id, "ebbsmate_admin_ids", true);
		$board_admin = empty($board_admin)? '' :$board_admin;
		
		$section_list = pavoebbsmate_simple_role_check($current_user, $board_admin, $board_section);
		$section_text = "'all'";
		foreach ($section_list as $section){
			$section_text .= ",'".$section."'";
		}
		
		$preview_posts = $wpdb->get_results("select * from (
				select * from (
				select p.ID , p.post_title,p.post_author,p.post_date
				from $wpdb->posts as p, $wpdb->postmeta as m
				where
				p.id = m.post_id
				and p.id = ".$post_id."
				and p.post_status = 'publish'
				and m.meta_key='pavo_board_origin'
		) a
				union
				select * from (
				SELECT $wpdb->posts.ID, $wpdb->posts.post_title,$wpdb->posts.post_author,$wpdb->posts.post_date
				FROM $wpdb->posts
		
				LEFT JOIN $wpdb->postmeta
				ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
		
				LEFT JOIN $wpdb->postmeta AS mt2
				ON ($wpdb->posts.ID = mt2.post_id AND mt2.meta_key = 'pavo_section_val' )
		
				LEFT JOIN $wpdb->postmeta AS mt3
		
				ON ( $wpdb->posts.ID = mt3.post_id )
		
				WHERE 1=1
				AND $wpdb->posts.post_parent = 0
				AND ( ( $wpdb->postmeta.meta_key = 'pavo_board_origin' AND CAST($wpdb->postmeta.meta_value AS CHAR) = '".$board_id."' )
				AND ( mt2.post_id IS NULL OR ( mt3.meta_key = 'pavo_section_val' AND CAST(mt3.meta_value AS CHAR) IN (".$section_text.") ) ) )
		
				AND $wpdb->posts.post_type = 'ebbspost' AND (($wpdb->posts.post_status = 'publish'))
				AND id > ".$post_id."
		
				GROUP BY $wpdb->posts.ID ORDER BY $wpdb->posts.post_date ASC LIMIT ".$prev_next_lines."
		) b
				union
				select * from (
				SELECT $wpdb->posts.ID, $wpdb->posts.post_title,$wpdb->posts.post_author,$wpdb->posts.post_date
				FROM $wpdb->posts
		
				LEFT JOIN $wpdb->postmeta
				ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
		
				LEFT JOIN $wpdb->postmeta AS mt2
				ON ($wpdb->posts.ID = mt2.post_id AND mt2.meta_key = 'pavo_section_val' )
		
				LEFT JOIN $wpdb->postmeta AS mt3
		
				ON ( $wpdb->posts.ID = mt3.post_id )
		
				WHERE 1=1
				AND $wpdb->posts.post_parent = 0
				AND ( ( $wpdb->postmeta.meta_key = 'pavo_board_origin' AND CAST($wpdb->postmeta.meta_value AS CHAR) = '".$board_id."' )
				AND ( mt2.post_id IS NULL OR ( mt3.meta_key = 'pavo_section_val' AND CAST(mt3.meta_value AS CHAR) IN (".$section_text.") ) ) )
		
				AND $wpdb->posts.post_type = 'ebbspost' AND (($wpdb->posts.post_status = 'publish'))
				AND id < ".$post_id."
		
				GROUP BY $wpdb->posts.ID ORDER BY $wpdb->posts.post_date DESC LIMIT ".$prev_next_lines."
			) c
		
) d order by id desc; "
		
		);
		
	}
	
	$curt_index = 0;
	foreach ($preview_posts as $index=>$preview_post){
		if($preview_post->ID == $post_id){
			$curt_index = $index;
		}
	}
	
	$nextID = empty($preview_posts[$curt_index -1]) ? 0 : $preview_posts[$curt_index -1]->ID ;
	$prevID = empty($preview_posts[$curt_index +1]) ? 0 : $preview_posts[$curt_index +1]->ID ;
	
	//게시물 상세 정보를 불러온다.
	$args= array (
			'post_type' => array('ebbspost'),
			'post_status' => 'publish',
			'posts_per_page'=> 1,
			'p' => $post_id,
	);
	$wp_query = new WP_Query($args);
	
	$cur_board_url = get_page_link();
	
//echo "<h2>이전글 ID: ".$prevID."</h2><br/>";
//echo "<h2>다음글 ID: ".$nextID."</h2>";
?>
<div class="pavoboard-wrapper pavoboard-custom">
      <div id="pavoboard-read-table">
      <?php if($wp_query->have_posts()) :?>
	  <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
  				$section_title = '';
				if($board_section_flag){
					$section_title = get_post_meta($wp_query->post->ID, 'pavo_section_val', true);
					if($section_title != 'all' && !empty($section_title)){
						$section_title = '['.$section_title.']';
					}else{
						$section_title = '';
					}
  				}
	  ?>
        <div class="title-section">
          <h1 class="pavoboard-read-h1"><?php echo  $section_title . ebbsmate_display_prohibited_words($board_id, $wp_query->post->post_title)?></h1>
          <div class="regist-date">
            <h2 class="pavoboard-read-h2">
            <?php if($date_flag == "1") {?>
            <?php echo date('Y.m.d H:i', strtotime($wp_query->post->post_date))?>
            <?php }?>
            </h2>
          </div>
        </div>
        <div class="writer-section" style="display: <?php if($author_flag == "0" && $vcount_flag == "0") {?>none;<?php }?>">
          <div class="regist-writer">
            <h2 class="pavoboard-read-h2">
            <?php if($author_flag == "1") {
            	echo ebbsmate_get_post_author_template($post_id , $cur_board_url);
			}?>
            </h2>
          </div>
          <div class="regist-info">
          <?php if($vcount_flag == "1") {?>
          <span>조회<b><?php echo get_post_meta($wp_query->post->ID, 'pavo_board_view_count', true)?></b></span>
          <?php }?>
          </div>
        </div>
        <?php 
        //파일 첨부했는지 체크
        $board_id = get_post_meta($post_id, "pavo_board_origin", true);
        $file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
        
        $fileNames=get_post_meta($post_id, "pavo_bbs_file_name", false);
        $filePaths=get_post_meta($post_id, "pavo_bbs_file_path", false);
        
        $upload_dir = wp_upload_dir();
        
        $total_file_cnt = 0;
        
        if(!empty($fileNames) && sizeof($fileNames[0]) > 0) {
        	$total_file_cnt = sizeof($fileNames[0]);
        } else {
        	$total_file_cnt = 0;
        }
        ?>
        <?php if(!empty($fileNames) && sizeof($fileNames[0]) > 0) { ?>
        <div class="board_detail">
            <div class="attachFile">
            	<a title="<?php _e('첨부파일목록', 'pavoboard') ?>" href="#"><?php _e('첨부파일', 'pavoboard') ?><em>(<?php echo $total_file_cnt?>)</em></a>
                <div class="attach_layer">
                    <ul class="innerList" style="display:none;">
                         <li class="toparrow"></li>       
                         <li class="btn_close"><a title="닫기" onclick="open_file_attach_layer();"></a></li>     
                    <?php for($i = 0; $i < sizeof($fileNames[0]); $i++) {
                    	echo "";
                    	$file_url = get_permalink($cur_post_id);
                    	$params = array(
                    			'action' => "ebbsmate_download",
                    			'board_id' => $board_id,
                    			'post_id' => $wp_query->post->ID,
                    			'file_name' => $fileNames[0][$i],
                    			
//                     			'file_path' => $filePaths[0][$i],
                    	);
                    	
                    	$file_url = add_query_arg( $params, $file_url );
                    	
                    ?>                      
                    	<li class="item">
	                    	<a title="<?php echo $fileNames[0][$i]?> 파일 다운로드" href="<?php echo  $file_url ?>">
	                    	<?php echo $fileNames[0][$i]?>(<?php 
	                    	if(file_exists($filePaths[0][$i])){
	                    		echo number_format(filesize($filePaths[0][$i])/1024,2);
	                    	}else{
	                    		echo "0.00";
	                    	}
	                    	?> KB)
	                    	</a>
                    	</li>
                    <?php }?>
                    </ul>
                </div>
            </div><!-- // attachFile -->
         </div>
         <?php }?>
        <div class="content-section">
          <?php $content = apply_filters('the_content', strip_shortcodes($wp_query->post->post_content));?>
          <p><?php echo ebbsmate_display_prohibited_words($board_id, $content)?></p>
        </div>
        <div class="pavoboard-controller">
          <div class="pavoboard-controller-left">
          <?php if(get_user_roles($board_id, "list")) :
	          $params = array(
	          		'bbspaged' => $paged
	          );
	          if(!empty($_GET['section'])){
	          	$params['section'] = $_GET['section'];
	          };
          ?>
          <a class="pavoboard-button" href="<?php echo add_query_arg( $params, get_permalink($cur_post_id) )?>">목록</a>
          <?php endif;?>
          <?php if(get_user_roles($board_id, "read")) : ?>
			<?php if (!empty($prevID)) {
				$params = array(
					'action' => 'readpost',
					'post_id' => $prevID,
					'bbspaged' => $paged
				);
				if(!empty($_GET['section'])){
					$params['section'] = $_GET['section'];
				};
				
			?>
          	<a class="pavoboard-button" href="<?php echo add_query_arg( $params, get_permalink($cur_post_id) )?>">이전</a>
          	<?php }?>
          	<?php if (!empty($nextID)) { 
          		$params = array(
          				'action' => 'readpost',
          				'post_id' => $nextID,
          				'bbspaged' => $paged
          		);
          		if(!empty($_GET['section'])){
          			$params['section'] = $_GET['section'];
          		};
          	?>
          	<a class="pavoboard-button" href="<?php echo add_query_arg( $params, get_permalink($cur_post_id) ) ?>">다음</a>
          	<?php }?>
          	<?php endif;?>
          <?php if(get_user_roles($board_id, "write") && empty($is_notice) && $wp_query->post->post_parent == 0): 
	          $params = array(
	          		'action' => 'insertpost',
	          		'parent_id' => $post_id,
	          		'bbspaged' => $paged
	          );
	          if(!empty($_GET['section'])){
	          	$params['section'] = $_GET['section'];
	          };
          ?>
          <a class="pavoboard-button" href="<?php echo  add_query_arg( $params, get_permalink($cur_post_id) ) ?>">답글</a> 
          <?php endif;?>
          </div>
		  <?php if( $wp_query->post->post_author == get_current_user_id() || is_pvbbsadmin($board_id) || $wp_query->post->post_author == 0 ) : ?>
          <div class="pavoboard-controller-right">
          <?php if(get_user_roles($board_id, "write")) :
          //비접속자글인경우
          
          $edit_params = array(
          		'action' => 'editpost',
          		'post_id' => $post_id
          );
          if(!empty($_GET['section'])){
          	$edit_params['section'] = $_GET['section'];
          };
          $edit_url = add_query_arg( $edit_params, get_permalink($cur_post_id) );
          
          $delete_params = array(
          		'action' => 'deletepost',
          		'post_id' => $wp_query->post->ID
          );
          if(!empty($_GET['section'])){
          	$delete_params['section'] = $_GET['section'];
          };
          $deletepost_link = add_query_arg( $delete_params, get_permalink($cur_post_id) ); 
          
          if( !is_pvbbsadmin($board_id) &&  $wp_query->post->post_author == 0) {
          	?>
          	<!-- 비로그인 사용자 버튼 -->
          	<a class="pavoboard-button" href="<?php echo $edit_url ?>"><?php _e('익명수정', 'pavoboard');?></a>
          	<a href="<?php echo $deletepost_link;?>" class="pavoboard-button button-delete<?php if($wp_query->post->post_author == 0 && !is_pvbbsadmin($board_id)) echo ' guest-delete'?>" post_num="<?php echo $wp_query->post->ID;?>"><?php _e('익명삭제', 'pavoboard');?></a>
          	<?php 
          // 본인 글이거나 관리자일경우
          }else if( is_pvbbsadmin($board_id) || $wp_query->post->post_author == get_current_user_id() ){
          	?>
          	<!-- 로그인 사용자 버튼 -->
          	<a class="pavoboard-button" href="<?php echo $edit_url ?>"><?php _e('수정', 'pavoboard');?></a>
          	<a href="<?php echo $deletepost_link;?>" class="pavoboard-button button-delete" post_num="<?php echo $wp_query->post->ID;?>"><?php _e('삭제', 'pavoboard');?></a>
          	<?php 
	          }
			?>
            <?php endif;?>
          </div>
		  <?php endif;?>
        </div>
       	<?php 
       		endwhile; 
       		endif;
       		include plugin_dir_path( __FILE__ ).'pavo-comment-list.php'; 
			
	
			if($prev_next_flag == "1") {
		?>
      	<!-- S:이전글다음글 -->    
        <div class="prenext">  
            
            <table class="pavoboard-table" summary="게시판" id="pavoboard-table">
            <caption class="blind">게시판 전체목록</caption>
             <thead>
                <th class="entry-th-title"><span>제목</span></th>
                <?php if($author_flag == "1") {?>
                <th class="entry-th-writer"><span>작성자</span></th>
                <?php }
                	if($author_flag == "1") {?>
                <th class="entry-th-date"><span>작성일 </span></th>
                <?php }
                	if($vcount_flag == "1") {?>
                <th class="entry-th-hit"><span>조회</span></th>
                <?php }?>
            </thead>
            <tbody>
            
            <?php
            /*
             *    이전글/다음글 목록 
             */
            foreach ($preview_posts as $index=>$preview_post){

            	//코맨트 개수 가져오기
            	$preview_comments_count = wp_count_comments( $preview_post->ID );
            	$preview_total_comment_cnt = $preview_comments_count->approved;

	           	//파일 첨부했는지 체크
            	$board_id = get_post_meta($preview_post->ID, "pavo_board_origin", true);
            	$file_attach_flag = get_post_meta($board_id, "ebbsmate_attach_flag", true);
            	
            	$fileNames=get_post_meta($preview_post->ID, "pavo_bbs_file_name", false);
            	$filePaths=get_post_meta($preview_post->ID, "pavo_bbs_file_path", false);
            	
            	$total_file_cnt = 0;
            	
            	if(!empty($fileNames) && sizeof($fileNames[0]) > 0) {
            		$total_file_cnt = sizeof($fileNames[0]);
            	} else {
            		$total_file_cnt = 0;
            	}
            	
            	$params = array(
            			'action' => 'readpost',
            			'post_id' => $preview_post->ID,
            	);
            	if(!empty($_GET['section'])){
            		$params['section'] = $_GET['section'];
            	};
            	$readpost_link = add_query_arg( $params, get_permalink($cur_post_id) );
            	$secret_flag = get_post_meta($preview_post->ID, "pavo_board_secret_flag", true);
            	?>
				<tr <?php if($curt_index == $index) echo "class='current'"; ?>>
					<td class="pavoboard-list-title">
					<?php if($curt_index != $index) {?>
						<a href="<?php echo $readpost_link;?>" class="<?php if($preview_post->post_author == 0 && !is_pvbbsadmin($board_id) && $secret_flag) echo 'guest-secret'?>" post_num="<?php echo $preview_post->ID;?>">
					<?php }?>
			              	<?php echo $preview_post->post_title ?>
			              	<?php if($preview_total_comment_cnt > 0) {?>
			            	<span class="entry_comment_count">(<?php echo $preview_total_comment_cnt?>)</span>
			            	<?php }?>
			            	<?php if($secret_flag) {?>
			            	<span class="pavoboard-list-icon pavoboard-secret">비밀글 설정됨</span>
			            	<?php }?>
			              	<?php if($total_file_cnt > 0) {?>
			            	<span class="pavoboard-list-icon pavoboard-attach">파일 첨부됨</span>
			            	<?php }?>
					<?php if($curt_index != $index) {?>
						</a>
					<?php }?>
						<div class="mobile-writer-info">
			                <ul>
			                  <li>작성자 : <?php echo ebbsmate_get_the_author($preview_post->ID);?></li>
			                  <?php if (date('Y/m/d') == date('Y/m/d', strtotime($preview_post->post_date))) :?>
			                  <li>작성일 : <?php echo date('H:i', strtotime($preview_post->post_date))?></li>
			                  <?php else :?>
			                  <li>작성일 : <?php echo date('Y.m.d', strtotime($preview_post->post_date))?></li>
			                  <?php endif;?>
			                  <li>조회수 : <?php echo get_post_meta($preview_post->ID, 'pavo_board_view_count', true)?></li>
			                </ul>
		              	</div>
					</td>
					<?php if($author_flag == "1") {?>
		            <td class="pavoboard-list-writer"><span><?php echo ebbsmate_get_the_author($preview_post->ID)?></span></td>
		            <?php }?>
		            <?php if($date_flag == "1") {?>
		            <?php if (date('Y/m/d') == date('Y/m/d', strtotime($preview_post->post_date))) :?>
		            <td class="pavoboard-list-date"><?php echo date('H:i', strtotime($preview_post->post_date))?></td>
		            <?php else :?>
		            <td class="pavoboard-list-date"><?php echo date('Y.m.d', strtotime($preview_post->post_date))?></td>
		            <?php endif;?>
		            <?php }?>
		            <?php if($vcount_flag == "1") {?>
		            <td class="pavoboard-list-hit"><?php echo get_post_meta($preview_post->ID, 'pavo_board_view_count', true)?></td>
		            <?php }?>
				</tr>
            	<?php
            }
            ?>
            </tbody>
          </table>
      </div>  
       <!-- E:이전글다음글 -->   
<?php }
wp_reset_postdata();
?>        
          
    <input type="hidden" name="pavo_post_cur_board_page" value="<?php echo $cur_post_id ?>"/>  
    <input type="hidden" name="pavo_post_cur_id" value="<?php echo $wp_query->post->ID?>"/>  
    <input type="hidden" name="pavo_delete_return_url" value="<?php echo get_permalink($cur_post_id)?>"/>
    <input type="hidden" name="ebbsmate_post_edit_url" value="<?php echo add_query_arg( array('action' => 'editpost', 'post_id' => $post_id), get_permalink($cur_post_id) ) ?>"/>
    <input type="hidden" name="ebbsmate_post_delete_url" value="<?php echo add_query_arg( array('action' => 'delete' , 'post_id' => $post_id), get_permalink($cur_post_id) ) ?>"/>
    
    <div style="margin-top: 30px;">
	<?php echo apply_filters('ebbsmate_powered_by', '')?>
    </div>
    
  </div><!-- pavoboard-read-table -->
</div> <!-- pavoboard-wrapper -->
<?php } else {
	ebbsmate_print_no_page();
} ?>