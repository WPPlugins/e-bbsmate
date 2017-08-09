<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="wrap">
	<h2>설정</h2>
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab <?php if ($tab=="basic" || $tab == "") {echo "nav-tab-active";}?>" href="<?php echo admin_url('admin.php?page=ebbsmate_config&tab=basic')?>">금칙어</a>
		<a class="nav-tab <?php if ($tab=="editor") {echo "nav-tab-active";}?>" href="<?php echo admin_url('admin.php?page=ebbsmate_config&tab=editor')?>">에디터</a>
	</h2>
	
	<form method="post" action="">
	<input type="hidden" name="ebbs_set_submit_hidden" value="Y">
	<?php wp_nonce_field( 'ebbsmate_settings'); ?>
	<?php if($tab == "basic" || $tab == "") {?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row" style="width: 330px">
					금칙어
				</th>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="ebbsmate_prohibit_db" value="0" />
	<div style="margin-bottom: 15px;">일반적인 금칙어 이외에 사이트의 특성에 따른 금칙어를 추가할 경우, 쉼표(,)로 입력 구분해 입력해 주십시오.</div>
	<div><textarea name="ebbsmate_prohibited_words" cols="100" rows="10" onkeyup="nospaces(this)"><?php echo $prohibited_words;?></textarea></div>
	<?php } else if($tab == "editor") {?>
	<table class="form-table" style="display:<?php if($tab=="editor"){echo "block";} else {echo "none";}?>">
		<tbody>
			<tr>
				<th scope="row" style="width: 330px">
					게시판 에디터 선택
				</th>
				<td>
					게시판 에디터를 <span style="font-weight: bold;">워드프레스 기본 에디터</span>, <span style="font-weight: bold;">TEXTAREA</span> 중에서 선택할 수 있습니다.<br/><br/>
					<input type="radio" name="ebbsmate_editor" value="wp" <?php checked($bbs_editor, 'wp');?>/>워드프레스 기본 에디터 사용<br/><br/>
					<input type="radio" name="ebbsmate_editor" value="txt" <?php checked($bbs_editor, 'txt');?>/>TEXTAREA 사용
				</td>
			</tr>
		</tbody>
	</table>
	<?php }?>
	<p>
		<input type="submit" name="" class='button button-primary' value="<?php esc_attr_e( 'Save Changes' ); ?>" />
	</p>
	</form>
</div>