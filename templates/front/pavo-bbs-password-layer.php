<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div id="pavoboard-transparent-layer">
	<form method="post" name="pavo_bbs_password_chk_form" enctype="multipart/form-data" action="">
	  <div class="pavoboard-layer" style="top:150px;margin-left:-150px;"> <!-- layer의 위치값은 직접 style삽입 -->
	    <div class="layer-head">
	        <h3>패스워드 확인</h3>
	        <a href="" class="btn_close" title="close">X</a>
	    </div>
	    <div class="layer-body">
	      <div class="pavoboard-input">
	        <label for="anonymousPassword">비밀번호</label>
	        <input type="password" id="anonymousPassword" name="anonymousPassword" value="">
	      </div>
	      <div class="pavoboard-controller">
	         <a href="" class="pavoboard-button btn_close" title="취소">취소</a>
			 <a href="" class="pavoboard-button btn_ok" title="확인">확인</a>
	      </div>
	    </div><!-- E:layer-body -->
	  </div><!-- E:pavoboard-layer -->
	</form>
	<form method="post" name="pavo_bbs_password_form" enctype="multipart/form-data" action="">
		<input type="password" name="newPassword" value="">
		<input type="hidden" name="board_id" value="<?php echo $board_id;?>">
	  	<input type="hidden" name="postId" value="">
	  	<input type="hidden" name="submit_type" value="">
  	</form>
</div><!-- E:pavoboard-transparent-layer -->