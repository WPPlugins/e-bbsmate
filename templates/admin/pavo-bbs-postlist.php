<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<script>
function pavoboard_change_post_status(status) {
	var url = "<?php echo admin_url('admin.php?page=ebbsmate');?>";
	var query = "<?php echo $_SERVER['QUERY_STRING']?>";
	
	var vars = query.split("&");
	var return_url = "";
		
	if(status == "all") {
		for (var i=0; i < vars.length; i++) {
			var n = vars[i].indexOf("post_status");
			
			if(n > -1) {
				return_url = vars[i];
			}
		}
	} else if(status == "publish") {
		for (var i=0; i < vars.length; i++) {
			var n = vars[i].indexOf("post_status");
			
			if(n > -1) {
				return_url = vars[i];
			}
		}

		if(return_url == "") {
			return_url = query + "&post_status=publish";
		}		
	} else if(status == "trash") {
		for (var i=0; i < vars.length; i++) {
			var n = vars[i].indexOf("post_status");
			
			if(n > -1) {
				return_url = vars[i];
			}
		}

		if(return_url == "") {
			return_url = query + "&post_status=trash";
		}
	}
	
	window.location=return_url;
}
</script>
<?php 
if(!empty($_GET['mode'])) {
	$mode = $_GET['mode'];
} else {
	$mode = "";
}

if(!empty($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = "";
}

if($mode == "write") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-newpost.php';
} else if($mode == "view") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-viewpost.php';
} else if($mode == "insertpost") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-insertpost.php';
} else if($mode == "edit") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-editpost.php';
} else if($action == "notice") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-updatepost.php';
} else if($action == "unnotice") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-updatepost.php';
} else if($mode == "update") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-updatepost.php';
} else if($mode == "delete") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-deletepost.php';
} else if($action == "delete_u") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-deletepost-u.php';
} else if($action == "delete_p") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-deletepost-p.php';
} else {
	//게시글 검색어
	if (array_key_exists("s", $_GET) === true)
	{
		$search_keyword = $_GET['s'];
	} else {
		$search_keyword = "";
	}
	
	//날짜 필터
	if (array_key_exists("m", $_GET) === true)
	{
		if($_GET['m'] == 0) {
			$year_keyword = "";
			$month_keyword = "";
		} else {
			$year_keyword = substr($_GET['m'], 0, 4);
			$month_keyword = substr($_GET['m'], 4, 2);
		}
	} else {
		$year_keyword = "";
		$month_keyword = "";
	}
	
	//게시판 필터
	if (array_key_exists("b", $_GET) === true)
	{
		if($_GET['b'] == 0) {
			$board_keyword = "";
		} else {
			$board_keyword = $_GET['b'];
		}
	} else {
		$board_keyword = "";
	}
	
	if (array_key_exists("orderby", $_GET) === true)
	{
		$orderby = $_GET['orderby'];
	} else {
		$orderby = "";
	}
	
	if (array_key_exists("order", $_GET) === true)
	{
		$order = $_GET['order'];
	} else {
		$order = "desc";
	}
	
	if($order == "desc") {
		$order_class = "asc";
	} else {
		$order_class = "desc";
	}
	
	if (array_key_exists("post_status", $_GET) === true)
	{
		$post_status = $_GET['post_status'];
	} else {
		$post_status = "";
	}
	
	if (array_key_exists("ids", $_GET) === true)
	{
		$ids = $_GET['ids'];
	} else {
		$ids = "";
	}
	
	if(!empty($_GET['mode'])) {
		$view_mode = $_GET['mode'];
	} else {
		$view_mode = "";
	}
	
	//페이징 관련 처리
	$paged = 1;
	
	if(isset($_GET['pagenum'])){
		$paged = $_GET['pagenum'];
	}
	
	//총 게시물수 조회
	global $wpdb;

	$sql_parameter = "and p.post_type='ebbspost'";
	
	if(!empty($year_keyword)) {
		$sql_parameter .= "and year(p.post_date) = '$year_keyword'";
	}
	
	if(!empty($month_keyword)) {
		$sql_parameter .= "and month(p.post_date) = '$month_keyword'";
	}
	
	if(!empty($board_keyword)) {
		$sql_parameter .= "and m.meta_key = 'pavo_board_origin'
				 and m.meta_value= '$board_keyword'";
	}
	
	$sql = "select count(p.ID)
	from wp_posts p
	where exists(select m.post_id 
	             from wp_postmeta m 
	             where p.ID = m.post_id $sql_parameter 
	             and p.post_status!='trash')";
	
	$all_post_cnt = $wpdb->get_var($sql);
		
	//발행된 게시물 조회
	$sql_parameter = "and p.post_type='ebbspost'";

	if(!empty($year_keyword)) {
		$sql_parameter .= "and year(p.post_date) = '$year_keyword'";
	}
	
	if(!empty($month_keyword)) {
		$sql_parameter .= "and month(p.post_date) = '$month_keyword'";
	}
	
	if(!empty($board_keyword)) {
		$sql_parameter .= "and m.meta_key = 'pavo_board_origin'
				 and m.meta_value= '$board_keyword'";
	}
		
	$sql = "select count(p.ID)
	from wp_posts p
	where exists(select m.post_id from wp_postmeta m where p.ID = m.post_id $sql_parameter and p.post_title LIKE '%$search_keyword%' and p.post_status='publish')";
		
	$publish_post_cnt = $wpdb->get_var($sql);

	//발행된 게시물 조회
	$sql_parameter = "and p.post_type='ebbspost'";
	
	if(!empty($year_keyword)) {
		$sql_parameter .= "and year(p.post_date) = '$year_keyword'";
	}
	
	if(!empty($month_keyword)) {
		$sql_parameter .= "and month(p.post_date) = '$month_keyword'";
	}
	
	if(!empty($board_keyword)) {
		$sql_parameter .= "and m.meta_key = 'pavo_board_origin'
		and m.meta_value= '$board_keyword'";
	}
	
	$sql = "select count(p.ID)
	from wp_posts p
	where exists(select m.post_id from wp_postmeta m where p.ID = m.post_id $sql_parameter and p.post_title LIKE '%$search_keyword%' and p.post_status='draft')";
	
	$draft_post_cnt = $wpdb->get_var($sql);
	
	$sql_parameter = "and p.post_type='ebbspost'";
	
	if(!empty($year_keyword)) {
		$sql_parameter .= "and year(p.post_date) = '$year_keyword'";
	}
	
	if(!empty($month_keyword)) {
		$sql_parameter .= "and month(p.post_date) = '$month_keyword'";
	}
	
	if(!empty($board_keyword)) {
		$sql_parameter .= "and m.meta_key = 'pavo_board_origin'
				 and m.meta_value= '$board_keyword'";
	}
	
	$sql = "select count(p.ID)
	from wp_posts p
	where exists(select m.post_id from wp_postmeta m where p.ID = m.post_id $sql_parameter and p.post_status='trash')";
	
	$trash_post_cnt = $wpdb->get_var($sql);
	
	$post_columns = get_user_meta(get_current_user_id(), 'ebbsmate_show_post_menu', true);
	$post_list_lines = $post_columns[sizeof($post_columns)-1];		
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	
	$limit = $post_list_lines; // number of rows in page
	$offset = ( $pagenum - 1 ) * $limit;
	//상태별 페이징
	if($post_status == "publish") {
		$total = $publish_post_cnt;
	} else if($post_status == "trash") {
		$total = $trash_post_cnt;
	} else if(empty($post_status) || $post_status == "all") {
		$total = $all_post_cnt;
	}
	
	$num_of_pages = ceil( $total / $limit );
		
	/* echo "페이지 번호: $pagenum<br>";
	echo "오프셋: $offset<br>";
	echo "게시물수: $total<br>";
	echo "총 페이지 개수: $num_of_pages<br>"; */
	
	$sql = "select ID 
			from $wpdb->posts p, $wpdb->postmeta m
			where p.ID = m.post_id
			and p.post_title LIKE '%$search_keyword%'
			and p.post_type='ebbspost'";
		
	if(!empty($year_keyword)) {
		$sql .= "and year(p.post_date) = '".$year_keyword."'";
	}
	
	if(!empty($month_keyword)) {
		$sql .= "and month(p.post_date) = '".$month_keyword."'";
	}
	
	if(!empty($board_keyword)) {
		$sql .= "and m.meta_key = 'pavo_board_origin'
				 and m.meta_value= '".$board_keyword."'";
	}
		
	$post_id_search = $wpdb->get_col($sql);
	
	if($post_status == "publish") {
		$post_status_arg = "publish";
	} else if($post_status == "draft") {
		$post_status_arg = "draft";
	} else if($post_status == "trash") {
		$post_status_arg = "trash";
	} else {
		$post_status_arg = array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit');
	}
			
	//게시물 목록 가져오기
	if($orderby == "title") {
		$args= array (
				'post__in' => $post_id_search,
				'post_type' => array('ebbspost'),
				'post_status' => $post_status_arg,
				'posts_per_page' => $post_list_lines,
				'paged' => $paged,
				'orderby' => 'title',
				'order' => $order
		);
	} else if($orderby == "board") {
		$args= array (
			'post__in' => $post_id_search,
			'post_type' => array('ebbspost'),
			'post_status' => $post_status_arg,
			'posts_per_page' => $post_list_lines,
			'paged' => $paged,
			'meta_key' => 'ebbsmate_board_id',
			'orderby' => 'meta_value_num',
			'order' => $order
		);
	} else if($orderby == "date") {
		$args= array (
				'post__in' => $post_id_search,
				'post_type' => array('ebbspost'),
				'post_status' => $post_status_arg,
				'posts_per_page' => $post_list_lines,
				'paged' => $paged,
				'orderby' => 'post_date',
				'order' => $order
		);
	} else {		
		$args= array (
			'post__in' => $post_id_search,
			'post_type' => array('ebbspost'),
			'post_status' => $post_status_arg,
			'posts_per_page' => $post_list_lines,
			'paged' => $paged,
			'orderby' => 'post_date',
			'order' => 'DESC'
		);
	}
	
	if(empty($post_status)) {
		unset($args[0]);
	}
	
	if(!empty($_GET['post_status'])) {
		$post_status_param = "&post_status=".$_GET['post_status'];
	} else {
		$post_status_param = "&post_status=all";
	}
	
	$post_columns = get_user_meta(get_current_user_id(), 'ebbsmate_show_post_menu', true);
	
	$board_display_flag = in_array("board", $post_columns);
	$comment_display_flag = in_array("comment", $post_columns);
	$vcount_display_flag = in_array("vcount", $post_columns);
	$author_display_flag = in_array("author", $post_columns);
	$date_display_flag = in_array("date", $post_columns);
		
	$colspan_cnt = 2;
		
	if($board_display_flag) {
		$colspan_cnt = $colspan_cnt + 1;
	}
		
	if($comment_display_flag) {
		$colspan_cnt = $colspan_cnt + 1;
	
	}
		
	if($vcount_display_flag) {
		$colspan_cnt = $colspan_cnt + 1;
	}
		
	if($author_display_flag) {
		$colspan_cnt = $colspan_cnt + 1;
	}
		
	if($date_display_flag) {
		$colspan_cnt = $colspan_cnt + 1;
	}
	
?>
<div class="wrap">
	<h2>게시글 목록
	<a class="add-new-h2" href="?page=ebbsmate&mode=write">게시글 쓰기</a>
	</h2>
	
	<?php 
	if(!empty($ids)) {
		$del_post_id = explode(',', $ids);
		$length = sizeof($del_post_id);
		
		$cur_post_status = get_post_status($del_post_id[0]);
	?>
	<div class="updated notice is-dismissible below-h2" id="message">
		<p>
			<?php 
			if($cur_post_status == "trash") :
			$untrash_nonce = wp_create_nonce( 'ebbsmate_untrashboard' );
			echo $length."개의 게시글이 휴지통으로 이동했습니다."
			?>
			<a href="<?php echo admin_url('admin.php?page=ebbsmate')."&action=delete_u".$post_status_param."&post_id=".$ids."&ebbsmate_post_nonce=".$untrash_nonce ?>">되돌리기</a>
			<?php endif; if($cur_post_status == "publish") :?>
			<?php echo $length?>개의 게시글이 휴지통에서 복구됐습니다.
			<?php endif; if(empty($cur_post_status) && $post_status == "trash") :?>
			<?php echo $length?>개의 게시글이 영구적으로 삭제됐습니다.
			<?php endif;?>
		</p>
		<button class="notice-dismiss" type="button">
			<span class="screen-reader-text">이 알림 무시하기.</span>
		</button>
	</div>
	<?php }?>
	
	<?php 
	if(!empty($_GET['msg'])) {
	?>
		<div class="updated notice is-dismissible below-h2" id="message">
			<p>
			<?php if($_GET['msg'] == "notice") {
				$updated_title = get_the_title($_GET['msg_arg']);
			?>
			게시글 <?php echo "\"".$updated_title."\""?>이(가) 공지글로 설정되었습니다.
			<?php } ?>
			<?php if ($_GET['msg'] == "unnotice") {
				$updated_title = get_the_title($_GET['msg_arg']);
			?>
			게시글 <?php echo "\"".$updated_title."\""?>이(가) 공지글에서 해제되었습니다.
			<?php } ?>
			<?php if ($_GET['msg'] == "insertpost") {
				$updated_title = get_the_title($_GET['msg_arg']);
			?>
			게시글 <?php echo "\"".$updated_title."\""?>이(가) 생성되었습니다.
			<?php } ?>
			<?php if ($_GET['msg'] == "updatepost") {
				$updated_title = get_the_title($_GET['msg_arg']);
			?>
			게시글 <?php echo "\"".$updated_title."\""?>이(가) 수정되었습니다.
			<?php } ?>
			</p>
			<button class="notice-dismiss" type="button">
				<span class="screen-reader-text">이 알림 무시하기.</span>
			</button>
		</div>
	<?php
	}
	?>
	
	<ul class="subsubsub">
		<li class="all">
			<?php $publish_post_cnt;?>
			<a <?php if(empty($_GET['post_status'])) :?>class="current"<?php endif;?> href="?page=ebbsmate"><?php echo __('All')?>
			<span class="count">(<?php echo $all_post_cnt?>)</span></a>
			|
		</li>
		<li class="publish">
			<a <?php if(!empty($_GET['post_status']) && $_GET['post_status'] == "publish") :?>class="current"<?php endif;?> href="?page=ebbsmate&post_status=publish">
			<?php echo __('Published')?>
			<span class="count">(<?php echo $publish_post_cnt?>)</span>
			</a>
			<?php if($trash_post_cnt > 0 || $draft_post_cnt > 0) :?>
			|
			<?php endif;?>
		</li>
		<?php if($draft_post_cnt > 0) {?>
		<li class="draft">
			<a <?php if(!empty($_GET['post_status']) && $_GET['post_status'] == "draft") :?>class="current"<?php endif;?> href="?page=ebbsmate&post_status=draft">
			<?php echo __('Draft')?>
			<span class="count">(<?php echo $draft_post_cnt;?>)</span>
			</a>
			<?php if($trash_post_cnt > 0) :?>
			|
			<?php endif;?>
		</li>
		<?php }?>
		<?php if($trash_post_cnt > 0) :?>
		<li class="trash">
			<a <?php if(!empty($_GET['post_status']) && $_GET['post_status'] == "trash") :?>class="current"<?php endif;?> href="?page=ebbsmate&post_status=trash">
			<?php echo __('Trash')?>
			<span class="count">(<?php echo $trash_post_cnt?>)</span>
			</a>
		</li>
		<?php endif;?>
	</ul>

	<form id="posts-filter" method="get">
		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input">게시글 검색</label>
			<input type="hidden" name="page" value="ebbsmate">
			<input type="search" id="post-search-input" name="s" value="<?php echo $search_keyword?>">
			<?php if(!empty($order) && !empty($orderby)) :?>
			<input type="hidden" name="order" value="<?php echo $order?>">
			<input type="hidden" name="orderby" value="<?php echo $orderby?>">
			<?php endif;?>
			<?php wp_nonce_field( 'ebbsmate_trashpost', 'ebbsmate_board_nonce'); ?>
			<input type="submit" id="search-submit" class="button" value="게시글 검색">
		</p>

	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<label class="screen-reader-text" for="bulk-action-selector-top"><?php echo __( 'Select bulk action' )?></label>
			<select id="bulk-action-selector-top" name="action">
			<option selected="selected" value="-1"><?php echo __('Bulk Actions')?></option>
			<?php if($post_status != "trash") {?>
			<option class="hide-if-no-js" value="deletepost"><?php echo __('Move to Trash')?></option>
			<?php } else if($post_status == "trash") {?>
			<option class="hide-if-no-js" value="untrashpost"><?php echo __('Restore')?></option>
			<option class="hide-if-no-js" value="pdeletepost"><?php echo __('Delete Permanently')?></option>
			<?php }?>
			</select>
			<input id="doaction" class="button action" type="button" value="<?php esc_attr_e('Apply')?>">
		</div>
		
		<div class="alignleft actions">
		<label class="screen-reader-text" for="filter-by-board">게시판명으로 필터</label>
		<select name="b" id="filter-by-board">
			<option value="0">모든 게시판</option>
		<?php 
		//운영중인 게시판 목록 가져오기
		$args2= array (
				'post_type' => array('ebbsboard'),
				'post_status' => 'publish',
				'orderby' => 'post_date',
				'order' => 'DESC',
		);
		?>
		<?php $wp_query2 = new WP_Query($args2); ?>
		<?php if ( $wp_query2->have_posts() ) : ?>
			<?php while ( $wp_query2->have_posts() ) : $wp_query2->the_post(); ?>
			<option value="<?php echo the_ID()?>" <?php selected($wp_query2->post->ID, $board_keyword)?>><?php echo the_title()?></option>
			<?php endwhile;?>
		<?php endif;?>
		</select>
		<label class="screen-reader-text" for="filter-by-date">날짜로 필터</label>
		<select name="m" id="filter-by-date">
			<option selected="selected" value="0">모든 날짜</option>
			<?php
			$sql= "SELECT DISTINCT YEAR(post_date)
					FROM $wpdb->posts
					WHERE post_status != 'trash'
					AND post_type = 'ebbspost'
					ORDER BY post_date DESC";
			
			$years = $wpdb->get_col($sql);
			
			for($i = 0; $i < sizeof($years); $i++) {		
				$sql= "SELECT DISTINCT MONTH(post_date)
						FROM $wpdb->posts
						WHERE post_status != 'trash'
						AND post_type = 'ebbspost'
						AND YEAR(post_date) = '".$years[$i]."'
						ORDER BY post_date DESC";
				
				$result = $wpdb->get_col($sql);
				
				for($j = 0; $j < sizeof($result); $j++) {
					if($result[$j] < 10 ) {
						$monthval = "0".$result[$j];
					} else {
						$monthval = "";
					}
					?>
					<option value="<?php echo $years[$i]."".$monthval?>" <?php selected($years[$i]."".$monthval, $year_keyword."".$month_keyword)?>><?php echo $years[$i]?>년 <?php echo $result[$j]?>월</option>
					<?php 
				}
			}
			?>
		</select>
		<input name="filter_action" class="button" id="post-query-submit" type="submit" value="<?php esc_attr_e('Filter')?>">		
		</div>
		<input type="hidden" value="<?php echo $view_mode?>" name="mode">
		<div class="view-switch">
			<a id="view-switch-list" class="view-list <?php if(empty($_GET['mode']) || $_GET['mode'] == "list") :?>current<?php endif;?>" href="<?php echo admin_url('admin.php')."?page=ebbsmate".$post_status_param."&mode=list&pagenum=".$paged?>">
			<span class="screen-reader-text"><?php esc_attr_e('List View')?></span>
			</a>
			<a id="view-switch-excerpt" class="view-excerpt <?php if($_GET['mode'] == "excerpt") :?>current<?php endif;?>" href="<?php echo admin_url('admin.php')."?page=ebbsmate".$post_status_param."&mode=excerpt&pagenum=".$paged?>">
			<span class="screen-reader-text"><?php esc_attr_e('Excerpt View')?></span>
			</a>
		</div>
		<br class="clear">
	</div>
	<?php 
	$board_display_flag = in_array("board", $post_columns);
	$comment_display_flag = in_array("comment", $post_columns);
	$vcount_display_flag = in_array("vcount", $post_columns);
	$author_display_flag = in_array("author", $post_columns);
	$date_display_flag = in_array("date", $post_columns);
	?>
	<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column">
					<label class="screen-reader-text" for="cb-select-all-1"><?php esc_attr_e( 'Select All' )?></label>
					<input id="cb-select-all-1" type="checkbox">
				</td>
				<th id="title" class="manage-column column-title column-primary <?php if($orderby == "title") {echo "sorted";} else {echo "sortable";}?> <?php echo $order?>" scope="col">
					<a href="<?php echo admin_url('admin.php?page=ebbsmate')?>&orderby=title&order=<?php echo $order_class?>&s=<?php echo $search_keyword?>">
						<span><?php echo __('Title')?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<?php if($board_display_flag) :?>
				<th class="manage-column" scope="col">게시판명</th>
				<?php endif;?>
				<?php if($comment_display_flag) :?>
				<th class="manage-column" scope="col">댓글수</th>
				<?php endif;?>
				<?php if($vcount_display_flag) :?>
				<th class="manage-column" scope="col">조회수</th>
				<?php endif;?>
				<?php if($author_display_flag) :?>
				<th id="author" class="manage-column column-author" scope="col"><?php echo __('Author')?></th>
				<?php endif;?>
				<?php if($date_display_flag) :?>
				<th class="manage-column <?php if($orderby == "date") {echo "sorted";} else {echo "sortable";}?> <?php echo $order?>" scope="col">
					<a href="<?php echo admin_url('admin.php?page=ebbsmate')?>&orderby=date&order=<?php echo $order_class?>&s=<?php echo $search_keyword?>">
						<span>작성일</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<?php endif;?>
			</tr>
		</thead>		
		
		<tbody id="the-list">
		<?php 
		if(!empty($post_id_search)) {
		?>
		<?php $wp_query = new WP_Query($args); ?>
		<?php if ( $wp_query->have_posts() ) { ?>
			<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
				<?php $is_notice = get_post_meta($wp_query->post->ID, "pavo_board_notice_flag", true);?>
		<tr id="post-<?php the_ID()?>">
		<th class="check-column" scope="row">
		<label class="screen-reader-text" for="cb-select-<?php the_ID()?>">
		<?php printf( __( 'Select %s' ), _draft_or_post_title() );?>
		</label>
		<input id="cb-select-<?php the_ID()?>" type="checkbox" value="<?php the_ID()?>" name="post[]">
		</th>
		<td class="post-title page-title column-title column-primary">
		<strong>
			<a class="row-title" title="“<?php the_title()?>” 편집" href="<?php echo admin_url('admin.php?page=ebbsmate')."&mode=view&post_id=".$wp_query->post->ID;?>"><?php if($is_notice == "1") {?>[공지] <?php }?><?php echo ebbsmate_custom_exceprt($wp_query->post->post_title)?></a>
			<?php if($wp_query->post->post_status != "publish" && $wp_query->post->post_status != "trash") {?>
			—
			<span class="post-state"><?php echo __(ucfirst($wp_query->post->post_status))?></span>
			<?php }?>
		</strong>
		<?php if($mode == "excerpt") :?>
		<?php the_excerpt()?>
		<?php endif;?>
		<div class="row-actions">
			<?php if($post_status != "trash") {
				$delete_nonce = wp_create_nonce( 'ebbsmate_trashpost' );
				$edit_nonce = wp_create_nonce( 'ebbsmate_editpost_'.$wp_query->post->ID );
			?>
			<span class="edit">
				<a title="<?php echo __('Edit this item')?>" href="<?php echo admin_url("admin.php?page=ebbsmate&mode=edit&post_id=".$wp_query->post->ID."&ebbsmate_post_nonce=".$edit_nonce);?>">편집</a>
				|
			</span>
			<span class="trash">
				<a title="<?php echo __('Move this item to the Trash')?>" class="submitdelete" href="<?php echo admin_url("admin.php?page=ebbsmate&mode=delete".$post_status_param."&post_id=".$wp_query->post->ID."&ebbsmate_post_nonce=".$delete_nonce)?>"><?php echo __('Trash')?></a>
				|
			</span>
			<span class="view">
				<a rel="permalink" title="“<?php echo $wp_query->post->post_title?>” 보기" href="<?php echo admin_url("admin.php?page=ebbsmate&mode=view&post_id=".$wp_query->post->ID)?>">보기</a>
				|
			</span>
			<?php if(empty($is_notice)) :?>
			<span class="edit">
				<a title="<?php _e("이 아이템을 공지글로 설정","ebbsmate") ?>" href="<?php echo admin_url('admin.php?page=ebbsmate')."&action=notice&post_id=".$wp_query->post->ID."&ebbsmate_post_nonce=".$edit_nonce?>">공지</a>
			</span>
			<?php endif;?>
			<?php if($is_notice == "1") :?>
			<span class="edit">
				<a title="<?php _e("이 아이템을 공지글에서 해제","ebbsmate") ?>" href="<?php echo admin_url('admin.php?page=ebbsmate')."&action=unnotice&post_id=".$wp_query->post->ID."&ebbsmate_post_nonce=".$edit_nonce?>">공지 해제</a>
			</span>
			<?php endif;?>
			<?php } else if($post_status == "trash") {
				$untrash_nonce = wp_create_nonce( 'ebbsmate_untrashpost' );
				$delete_nonce = wp_create_nonce( 'ebbsmate_deletepost' );
			?>
			<span class="untrash">
				<a title="<?php echo __('Restore this item from the Trash')?>" href="<?php echo admin_url('admin.php?page=ebbsmate')."&action=delete_u".$post_status_param."&post_id=".$wp_query->post->ID."&ebbsmate_post_nonce=".$untrash_nonce ?>"><?php echo __('Restore')?></a>
				|
			</span>
			<span class="delete">
				<a title="<?php echo __('Delete this item permanently')?>" class="submitdelete" href="<?php echo admin_url('admin.php?page=ebbsmate')."&action=delete_p&post_id=".$wp_query->post->ID."&ebbsmate_post_nonce=".$delete_nonce ?>"><?php echo __('Delete Permanently')?></a>
			</span>
			<?php }?>
		</div>
		</td>
		<?php 
		//게시판명 가져오기
		$board_id = get_post_meta($wp_query->post->ID, 'pavo_board_origin', true);
		
		//조회수
		$view_count = get_post_meta($wp_query->post->ID, 'pavo_board_view_count', true);
				
		$sql = "select post_title from $wpdb->posts
		where ID = '".$board_id."'";
		
		$result = $wpdb->get_row($sql);
		if(!empty($result->post_title)) {
			$board_name = $result->post_title;
		} else {
			$board_name = "";
		}
		
		$table_col_span = 2;
		
		?>		
		<?php if($board_display_flag) :?>
		<td class="pboard_category column-pboard_category"><?php echo $board_name?></td>
		<?php endif;?>
		<?php if($comment_display_flag) :?>
		<td class="pboard_url column-pboard_url"><?php echo $wp_query->post->comment_count?></td>
		<?php endif;?>
		<?php if($vcount_display_flag) :?>
		<td class="pboard_desc column-pboard_desc"><?php echo $view_count?></td>
		<?php endif;?>
		<?php if($author_display_flag) :?>
		<td class="pboard_desc column-pboard_desc"><?php echo ebbsmate_get_the_author_admin($wp_query->post->ID);?></td>
		<?php endif;?>
		<?php if($date_display_flag) :?>
		<td class="date column-date"><?php echo date('Y/m/d H:i:s', strtotime($wp_query->post->post_date)); ?></td>
		<?php endif;?>
		</tr>
		<?php endwhile; ?>
		<?php } else {
		?>
		<tr>
			<td colspan="<?php echo $colspan_cnt?>">게시글이 없습니다.</td>
		</tr> 
		<?php }
		
		} else {
		?>
		<tr>
			<td colspan="<?php echo $colspan_cnt?>">게시글이 없습니다.</td>
		</tr>
		<?php
		}
		?>
	</tbody>
	<tfoot>
	<tr>
		<td id="cb" class="manage-column column-cb check-column">
			<label class="screen-reader-text" for="cb-select-all-1"><?php esc_attr_e( 'Select All' )?></label>
			<input id="cb-select-all-1" type="checkbox">
		</td>
		<th id="title" class="manage-column column-title <?php if($orderby == "title") {echo "sorted";} else {echo "sortable";}?> <?php echo $order?>" scope="col">
			<a href="<?php echo admin_url('admin.php?page=ebbsmate')?>?page=ebbsmate&orderby=title&order=<?php echo $order_class?>&s=<?php echo $search_keyword?>">
				<span><?php echo __('Title')?></span>
				<span class="sorting-indicator"></span>
			</a>
		</th>
		<?php if($board_display_flag) :?>
		<th class="manage-column" scope="col">게시판명</th>
		<?php endif;?>
		<?php if($comment_display_flag) :?>
		<th class="manage-column" scope="col">댓글수</th>
		<?php endif;?>
		<?php if($vcount_display_flag) :?>
		<th class="manage-column" scope="col">조회수</th>
		<?php endif;?>
		<?php if($author_display_flag) :?>
		<th id="author" class="manage-column column-author" scope="col"><?php echo __('Author')?></th>
		<?php endif;?>
		<?php if($date_display_flag) :?>
		<th class="manage-column <?php if($orderby == "date") {echo "sorted";} else {echo "sortable";}?> <?php echo $order?>" scope="col">
			<a href="<?php echo admin_url('admin.php?page=ebbsmate')?>&orderby=date&order=<?php echo $order_class?>&s=<?php echo $search_keyword?>">
				<span>작성일</span>
				<span class="sorting-indicator"></span>
			</a>
		</th>
		<?php endif;?>
		</tr>
	</tfoot>
</table>

<div class="tablenav bottom">
<div class="alignleft actions bulkactions">
	<label class="screen-reader-text" for="bulk-action-selector-bottom"><?php echo __( 'Select bulk action' )?></label>
	<select id="bulk-action-selector-bottom" name="action2">
	<option selected="selected" value="-1"><?php echo __('Bulk Actions')?></option>
	<?php if($post_status != "trash") {?>
	<option class="hide-if-no-js" value="deletepost"><?php echo __('Move to Trash')?></option>
	<?php } else if($post_status == "trash") {?>
	<option class="hide-if-no-js" value="untrashpost"><?php echo __('Restore')?></option>
	<option class="hide-if-no-js" value="pdeletepost"><?php echo __('Delete Permanently')?></option>
	<?php }?>
	</select>
	<input id="doaction2" class="button action" type="button" value="<?php esc_attr_e('Apply')?>">
</div>
<?php
$page_links = paginate_links( array(
    'base' => add_query_arg( 'pagenum', '%#%' ),
    'format' => '',
    'prev_text' => __( '&laquo;', 'text-domain' ),
    'next_text' => __( '&raquo;', 'text-domain' ),
    'total' => $num_of_pages,
    'current' => $pagenum
) );

if ( $page_links ) {
    echo '<div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div>';
}
?>
<br class="clear">
</div>
</form>
</div>
<?php }?>