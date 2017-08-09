<?php
/**
 * Admin ebbsmate 설명 페이지
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap ebbs-help-wrap">
	<h2 class="nav-tab-wrapper ebbs-help-tab-wrapper">숏 코드 도움말</h2>
	
	<table cellspacing="0" class="ebbs_help_table widefat">
		<thead>
			<tr>
				<th data-export-label="ebbsmate 숏 코드" colspan="3">ebbsmate 숏 코드 속성값</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>id</td>
				<td>게시판의 ID 값에 해당하는 게시판을 출력합니다.</td>
			</tr>
		</tbody>
	</table>
	<table cellspacing="0" class="ebbs_help_table widefat">
		<thead>
			<tr>
				<th data-export-label="ebbsmate-widget 숏 코드" colspan="3">ebbsmate-widget 숏 코드</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>id(필수)</td>
				<td>출력하고자 하는 게시판의 ID 값을 입력한다.</td>
			</tr>
			<tr>
				<td>heade</td>
				<td>true , false  위젯 상단 헤더 노출  여부를 선택한다.</td>
			</tr>
			<tr>
				<td>section</td>
				<td>특정분류 명을 입력, 해당 분류 목록만 출력된다.</td>
			</tr>
			<tr>
				<td>rows</td>
				<td>위젯에 출력하고자 하는 라인수, 미입력시 5 라인이 출력된다.</td>
			</tr>
		</tbody>
	</table>
</div> 