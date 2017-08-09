<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! empty( $_GET['board_copied'] ) ) : 
	echo "<div id='message' class='updated notice is-dismissible'><p>".__('게시판 복사가 완료되었습니다.')."</p></div>"; 
endif;

if ( ! empty( $_GET['action'] ) ) : 
	switch ( $_GET['action']) {
		case 'success_insertboard'		:
			echo "<div id='message' class='updated notice is-dismissible'><p>".__('신규 게시판이 생성되었습니다.')."</p></div>";
		break;
	}
endif;

if(!empty($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = "";
}

if($action == "newboard") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-newboard.php';
} else if($action == "viewboard") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-viewboard.php';
} else if($action == "editboard") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-editboard.php';
} else if($action == "editstyle") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-editstyle.php';
} else if($action == "updatestyle") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-updatestyle.php';
} else if($action == "updateboard") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-updateboard.php';
} else if($action == "deleteboard") {
	// 휴지통으로 이동
	include plugin_dir_path( __FILE__ ).'pavo-bbs-deleteboard.php';
} else if($action == "deleteboard_u") {
	// 되돌리기
	include plugin_dir_path( __FILE__ ).'pavo-bbs-deleteboard-u.php';
} else if($action == "deleteboard_p") {
	// 게시판 영구 삭제
	include plugin_dir_path( __FILE__ ).'pavo-bbs-deleteboard-p.php';
} else if($action == "insertboard") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-insertboard.php';
} else if($action == "copyboard") {
	include plugin_dir_path( __FILE__ ).'pavo-bbs-copyboard.php';
} else {
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return "권한이 없습니다.";
	}
	
	global $wpdb;
	
	if (array_key_exists("s", $_GET) === true)
	{
		$search_keyword = $_GET['s'];
	} else {
		$search_keyword = "";
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
	
	if (!empty($_GET['post_status']))
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
	
//페이징 관련 처리
$paged = 1;

if(isset($_GET['bbspaged'])){
	$paged = $_GET['bbspaged'];
}

//총 게시판수 조회
$sql = "select count(*) as post_cnt
from $wpdb->posts p
where p.post_type='ebbsboard'
and p.post_status!='trash'
and p.post_title like '%".$search_keyword."%'";

$result = $wpdb->get_row($sql);
$all_post_cnt = $result->post_cnt;

$sql = "select count(*) as post_cnt
from $wpdb->posts p
where p.post_type='ebbsboard'
and p.post_status='publish'
and p.post_title like '%".$search_keyword."%'";

$result = $wpdb->get_row($sql);
$publish_post_cnt = $result->post_cnt;

$sql = "select count(*) as post_cnt
from $wpdb->posts p
where p.post_type='ebbsboard'
and p.post_status='draft'
and p.post_title like '%".$search_keyword."%'";

$result = $wpdb->get_row($sql);
$draft_post_cnt = $result->post_cnt;

$sql = "select count(*) as post_cnt
from $wpdb->posts p
where p.post_type='ebbsboard'
and p.post_status='trash'
and p.post_title like '%".$search_keyword."%'";

$result = $wpdb->get_row($sql);
$trash_post_cnt = $result->post_cnt;

$post_columns = get_user_meta(get_current_user_id(), 'ebbsmate_show_board_menu', true);
$post_list_lines = $post_columns[sizeof($post_columns)-1];
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

$limit = $post_list_lines;
$offset = ( $pagenum - 1 ) * $limit;

//상태별 페이징
if($post_status == "publish") {
	$total = $publish_post_cnt;
}  else if($post_status == "draft") {
	$total = $draft_post_cnt;
}  else if($post_status == "trash") {
	$total = $trash_post_cnt;
} else {
	$total = $all_post_cnt;
}

$num_of_pages = ceil( $total / $limit );

$post_id_search = $wpdb->get_col(
		"select ID
		from $wpdb->posts
		where post_title LIKE '%".$search_keyword."%'
		and post_type='ebbsboard'"
		);

if($post_status == "publish") {
	$post_status_arg = "publish";
} else if($post_status == "draft") {
	$post_status_arg = "draft";
} else if($post_status == "trash") {
	$post_status_arg = "trash";
} else {
	$post_status_arg = array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit');
}

//게시판 목록 가져오기
if($orderby == "title") {
	$args= array (
			'post__in' => $post_id_search,
			'post_type' => array('ebbsboard'),
			'post_status' => $post_status_arg,
			'posts_per_page' => $post_list_lines,
			'paged' => $pagenum,
			'orderby' => 'title',
			'order' => $order
	);
} else if($orderby == "date") {
	$args= array (
			'post__in' => $post_id_search,
			'post_type' => array('ebbsboard'),
			'post_status' => $post_status_arg,
			'posts_per_page' => $post_list_lines,
			'paged' => $pagenum,
			'orderby' => 'date',
			'order' => $order
	);
} else {	
	$args= array (
			'post__in' => $post_id_search,
			'post_type' => array('ebbsboard'),
			'post_status' => $post_status_arg,
			'posts_per_page' => $post_list_lines,
			'paged' => $pagenum,
			'orderby' => 'post_date',
			'order' => 'DESC'
	);
}

$post_status_param = "";

if(!empty($_GET['post_status'])) {
	$post_status_param = "&post_status=".$_GET['post_status'];
}

$board_columns = get_user_meta(get_current_user_id(), 'ebbsmate_show_board_menu', true);

$short_display_flag = in_array("short", $board_columns);
$pcount_display_flag = in_array("pcount", $board_columns);
$date_display_flag = in_array("date", $board_columns);
$btype_display_flag = in_array("btype", $board_columns);

$colspan_cnt = 2;

if($short_display_flag) {
	$colspan_cnt = $colspan_cnt + 1;
}

if($pcount_display_flag) {
	$colspan_cnt = $colspan_cnt + 1;

}

if($date_display_flag) {
	$colspan_cnt = $colspan_cnt + 1;
}

if($btype_display_flag) {
	$colspan_cnt = $colspan_cnt + 1;
}
?>
<div class="wrap">
	<h2>게시판 목록
	<a class="add-new-h2" href="<?php echo admin_url('admin.php?page=ebbsmate_board');?>&action=newboard">새 게시판 추가</a>
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
			echo $length."개의 게시판이 휴지통으로 이동했습니다."?>
			<a href="<?php echo admin_url('admin.php?page=ebbsmate_board')."&action=deleteboard_u".$post_status_param."&board_id=".$ids."&ebbsmate_board_nonce=".$untrash_nonce ?>">되돌리기</a>
			<?php endif; if($cur_post_status == "publish") :?>
			<?php echo $length?>개의 게시판이 휴지통에서 복구됐습니다.
			<?php endif; if(empty($cur_post_status) && $post_status == "trash") :?>
			<?php echo $length?>개의 게시판이 영구적으로 삭제됐습니다.
			<?php endif;?>
			<!-- <a href="edit.php?post_type=post&amp;doaction=undo&amp;action=untrash&amp;ids=1445&amp;_wpnonce=536dae29c4">되돌리기</a> -->
		</p>
		<button class="notice-dismiss" type="button">
			<span class="screen-reader-text">이 알림 무시하기.</span>
		</button>
	</div>
	<?php }?>
	
	<ul class="subsubsub">
		<li class="all">
			<a <?php if(empty($_GET['post_status'])) :?>class="current"<?php endif;?> href="?page=ebbsmate_board"><?php echo __('All')?>
			<span class="count">(<?php echo $all_post_cnt;?>)</span></a>
			|
		</li>
		<li class="publish">
			<a <?php if(!empty($_GET['post_status']) && $_GET['post_status'] == "publish") :?>class="current"<?php endif;?> href="?page=ebbsmate_board&post_status=publish">
			<?php echo __('Published')?>
			<span class="count">(<?php echo $publish_post_cnt;?>)</span>
			</a>
			<?php if($trash_post_cnt > 0 || $draft_post_cnt > 0) :?>
			|
			<?php endif;?>
		</li>
		<?php if($draft_post_cnt > 0) {?>
		<li class="draft">
			<a <?php if(!empty($_GET['post_status']) && $_GET['post_status'] == "draft") :?>class="current"<?php endif;?> href="?page=ebbsmate_board&post_status=draft">
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
			<a <?php if(!empty($_GET['post_status']) && $_GET['post_status'] == "trash") :?>class="current"<?php endif;?> href="?page=ebbsmate_board&post_status=trash">
			<?php echo __('Trash')?>
			<span class="count">(<?php echo $trash_post_cnt;?>)</span>
			</a>
		</li>
		<?php endif;?>
	</ul>
	
	<form id="posts-filter" method="get">
		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input">게시판 검색</label>
			<input type="hidden" name="page" value="ebbsmate_board">
			<input type="search" id="post-search-input" name="s" value="<?php echo $search_keyword?>">
			<?php if(!empty($order) && !empty($orderby)) :?>
			<input type="hidden" name="order" value="<?php echo $order?>">
			<input type="hidden" name="orderby" value="<?php echo $orderby?>">
			<?php endif;?>
			<?php wp_nonce_field( 'ebbsmate_trashboard', 'ebbsmate_board_nonce'); ?>
			<input type="submit" id="search-submit" class="button" value="게시판 검색">
		</p>
	
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
			<label class="screen-reader-text" for="bulk-action-selector-top"><?php echo __( 'Select bulk action' )?></label>
			<select id="bulk-action-selector-top" name="action">
				<?php if($post_status != "trash") {?>
				<option selected="selected" value="-1"><?php echo __('Bulk Actions')?></option>
				<option class="hide-if-no-js" value="deleteboard">게시판 삭제</option>
				<?php } else if($post_status == "trash") {?>
				<option class="hide-if-no-js" value="untrashboard"><?php echo __('Restore')?></option>
				<option class="hide-if-no-js" value="pdeleteboard"><?php echo __('Delete Permanently')?></option>
				<?php }?>
			</select>
			<input id="doaction" class="button action" type="button" value="<?php esc_attr_e('Apply')?>">
			</div>
			<br class="clear">
		</div>
<?php 
	$short_display_flag = in_array("short", $board_columns);
	$pcount_display_flag = in_array("pcount", $board_columns);
	$date_display_flag = in_array("date", $board_columns);
	$btype_display_flag = in_array("btype", $board_columns);
?>		
<table class="wp-list-table widefat fixed striped posts">
<thead>
<tr>
<td id="cb" class="manage-column column-cb check-column">
<label class="screen-reader-text" for="cb-select-all-1"><?php esc_attr_e( 'Select All' )?></label>
<input id="cb-select-all-1" type="checkbox">
</td>
<th id="title" class="manage-column column-title <?php if($orderby == "title") {echo "sorted";} else {echo "sortable";}?> <?php echo $order?>" scope="col">
	<a href="<?php echo admin_url('admin.php?page=ebbsmate_board')?>&orderby=title&order=<?php echo $order_class?>&s=<?php echo $search_keyword?>">
		<span>게시판명</span>
		<span class="sorting-indicator"></span>
	</a>
</th>
<?php if($short_display_flag) {?>
<th class="manage-column" scope="col">숏코드</th>
<?php }?>
<?php if($pcount_display_flag) {?>
<th class="manage-column" scope="col">게시글수</th>
<?php }?>
<?php if($date_display_flag) {?>
<th id="date" class="manage-column column-title <?php if($orderby == "date") {echo "sorted";} else {echo "sortable";}?> <?php echo $order?>" scope="col">
	<a href="<?php echo admin_url('admin.php?page=ebbsmate_board')?>&orderby=date&order=<?php echo $order_class?>&s=<?php echo $search_keyword?>">
		<span>생성일</span>
		<span class="sorting-indicator"></span>
	</a>
</th>
<?php }?>
<?php if($btype_display_flag) {?>
<th class="manage-column" scope="col">게시판 유형</th>
<?php }?>
</tr>
</thead>

<tbody id="the-list">
<?php 
if(!empty($post_id_search)) {
?>
<?php $wp_query = new WP_Query($args); ?>
<?php if ( $wp_query->have_posts() ) : ?>
	<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
	<!-- 리스트 출력 -->
	<tr id="post-<?php the_ID()?>">
		<th class="check-column" scope="row">
			<label class="screen-reader-text" for="cb-select-<?php the_ID()?>"><label class="screen-reader-text" for="cb-select-<?php the_ID()?>"><?php
				printf( __( 'Select %s' ), _draft_or_post_title() );
			?></label></label>
			<input id="cb-select-<?php the_ID()?>" type="checkbox" value="<?php the_ID()?>" name="post[]">
			<div class="locked-indicator"></div>
		</th>
		<td class="post-title page-title column-title">
			<strong>
				<a class="row-title" title="“<?php the_title()?>” <?php echo __('Edit')?>" href="<?php echo admin_url('admin.php?page=ebbsmate_board')."&action=editboard&board_id=".$wp_query->post->ID?>"><?php the_title()?></a>
				<?php if($wp_query->post->post_status != "publish" && $wp_query->post->post_status != "trash") {?>
				—
				<span class="post-state"><?php echo __(ucfirst($wp_query->post->post_status))?></span>
				<?php }?>
			</strong>
			<div class="row-actions">
				<?php if($post_status != "trash") {
					$delete_nonce = wp_create_nonce( 'ebbsmate_trashboard' );
					$edit_nonce = wp_create_nonce( 'ebbsmate_editboard_'.$wp_query->post->ID );
					$copy_nonce = wp_create_nonce( 'ebbsmate_copyboard_'.$wp_query->post->ID );
				?>
				<span class="edit">
				<a title="<?php echo __('Edit this item')?>" href="<?php echo admin_url('admin.php?page=ebbsmate_board')."&action=editboard&board_id=".$wp_query->post->ID."&ebbsmate_board_nonce=".$edit_nonce?>">편집</a>
				|
				</span>
				<span class="trash">
				<a title="<?php echo __('Move this item to the Trash')?>" class="submitdelete" href="<?php echo admin_url('admin.php?page=ebbsmate_board')."&action=deleteboard".$post_status_param."&board_id=".$wp_query->post->ID."&ebbsmate_board_nonce=".$delete_nonce?>"><?php echo __('Trash')?></a>
				</span>
				|
				<span class="copy">
				<a title="<?php echo __('게시판 복사')?>" href="<?php echo admin_url('admin.php?page=ebbsmate_board')."&action=copyboard".$post_status_param."&board_id=".$wp_query->post->ID."&ebbsmate_board_nonce=".$copy_nonce?>"><?php echo __('게시판 복사')?></a>
				</span>
				<?php } else if($post_status == "trash") {
					$untrash_nonce = wp_create_nonce( 'ebbsmate_untrashboard' );
					$delete_nonce = wp_create_nonce( 'ebbsmate_deleteboard' );
				?>
				<span class="untrash">
				<a title="<?php echo __('Restore this item from the Trash')?>" href="<?php echo admin_url('admin.php?page=ebbsmate_board')."&action=deleteboard_u".$post_status_param."&board_id=".$wp_query->post->ID."&ebbsmate_board_nonce=".$untrash_nonce?>"><?php echo __('Restore')?></a>
				|
				</span>
				<span class="delete">
					<a title="<?php echo __('Delete this item permanently')?>" class="submitdelete" href="<?php echo admin_url('admin.php?page=ebbsmate_board')."&action=deleteboard_p&board_id=".$wp_query->post->ID."&ebbsmate_board_nonce=".$delete_nonce?>"><?php echo __('Delete Permanently')?></a>
				</span>
			<?php }?>
			</div>
		</td>
		<?php if($short_display_flag) {?>
		<td>
			[ebbsmate id="<?php echo get_post_meta($wp_query->post->ID, 'ebbsmate_board_id', true)?>"]
			<br/>
			[ebbsmate-widget id="<?php echo get_post_meta($wp_query->post->ID, 'ebbsmate_widget_id', true)?>" rows="5"]
			<br/>
		</td>
		<?php }?>
		<?php 		
		$sql = "select count(*) as post_cnt
				from $wpdb->postmeta
				where meta_key = 'pavo_board_origin'
				and meta_value = ".$wp_query->post->ID;
		
		$total_post_cnt = $wpdb->get_var($sql);
		?>
		<?php if($pcount_display_flag) {?>
		<td><?php echo $total_post_cnt?></td>
		<?php }?>
		<?php if($date_display_flag) {?>
		<td>
		<?php echo date('Y/m/d H:m:s', strtotime($wp_query->post->post_date)); ?>
		<?php }?>
		<?php if($btype_display_flag) {?>
		<td>기본형</td>
		<?php }?>
	</tr>
	
	<?php endwhile; ?>
	<?php else:?>
	<tr>
		<td colspan="<?php echo $colspan_cnt?>">게시판이 없습니다.</td>
	</tr>
	<?php endif;?>
	<?php } else {?>
	<tr>
		<td colspan="<?php echo $colspan_cnt?>">게시판이 없습니다.</td>
	</tr>
	<?php }?>
</tbody>

<tfoot>
<tr>
<td id="cb" class="manage-column column-cb check-column">
<label class="screen-reader-text" for="cb-select-all-1"><?php esc_attr_e( 'Select All' )?></label>
<input id="cb-select-all-1" type="checkbox">
</td>
<th id="title" class="manage-column column-title <?php if($orderby == "title") {echo "sorted";} else {echo "sortable";}?> <?php echo $order?>" scope="col">
	<a href="<?php echo admin_url('admin.php?page=ebbsmate_board')?>&orderby=title&order=<?php echo $order_class?>&s=<?php echo $search_keyword?>">
		<span>게시판명</span>
		<span class="sorting-indicator"></span>
	</a>
</th>
<?php if($short_display_flag) {?>
<th scope="col">숏코드</th>
<?php }?>
<?php if($pcount_display_flag) {?>
<th scope="col">게시글수</th>
<?php }?>
<?php if($date_display_flag) {?>
<th id="date" class="manage-column column-title <?php if($orderby == "date") {echo "sorted";} else {echo "sortable";}?> <?php echo $order?>" scope="col">
	<a href="<?php echo admin_url('admin.php?page=ebbsmate_board')?>&orderby=date&order=<?php echo $order_class?>&s=<?php echo $search_keyword?>">
		<span>생성일</span>
		<span class="sorting-indicator"></span>
	</a>
</th>
<?php }?>
<?php if($btype_display_flag) {?>
<th class="manage-column" scope="col">게시판 유형</th>
<?php }?>
</tr>
</tfoot>

</table>

<div class="tablenav bottom">
<div class="alignleft actions bulkactions">
<label class="screen-reader-text" for="bulk-action-selector-bottom"><?php echo __( 'Select bulk action' )?></label>
<select id="bulk-action-selector-bottom" name="action2">
<option selected="selected" value="-1"><?php echo __('Bulk Actions')?></option>
<?php if($post_status != "trash") {?>
<option class="hide-if-no-js" value="deleteboard">게시판 삭제</option>
<?php } else if($post_status == "trash") {?>
<option class="hide-if-no-js" value="untrashboard"><?php echo __('Restore')?></option>
<option class="hide-if-no-js" value="pdeleteboard"><?php echo __('Delete Permanently')?></option>
<?php }?>
</select>
<input id="doaction2" class="button action" type="button" value="<?php esc_attr_e('Apply')?>">
</div>
<div class="alignleft actions"> </div>
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