<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="pavoboard-error">
	<p class="error_title"><span>요청하신 작업을</span> 실행 할 수 없습니다. </p>
    <p class="error_desc"><?php echo $role_check["message"];?></p>
    <a class="pavoboard-button" href="javascript:history.go(-1);">이전 페이지</a>
</div><!-- // pavoboard-error  -->