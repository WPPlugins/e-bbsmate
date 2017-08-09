/**
 *   pavoboard fron js
 */

jQuery(document).ready(function($) {
	$.fn.exists = function(){return this.length>0;}
	
	$(".password-chk").click(function(){
		var input_pw = $("#ebbsmate_post_edit_popup input:password").val();
		var postId = $("input[name=pavo_post_cur_id]").val();
		var data =  {
				action 		: 	'ebbsmate_post_password_chk', 
	            newPassword : 	input_pw,
	            postId 		: 	postId,
	            type		: 	'editpost',
	            board_id	:	$("input[name=pavo_post_cur_board_page]").val()
	            	
		};
		
		$.post(pavo_common_js.ajax_url, data, function(response) {
			if(response.role_check){
				if(!$.isEmptyObject(response.loadpage)){
					$(location).attr('href',response.loadpage);
				}
			}else{
				alert(response.message);
			}
			
		}, 'json');
	});
	
	//게시글 수정 , 등록
	$(".pavoboard-controller .button-save").click(function(e){
		if(ebbsmate_insert_form_chk()) {
			var action		= $(this).attr('action');
			var formData 	= new FormData($("#pavo_post_form")[0]);		
			var content;
			var inputid 	= 'pavo_board_post_content';
			if ( typeof tinyMCE != 'undefined' &&  tinyMCE.activeEditor ) {
				var editor = tinyMCE.get(inputid);
				content = editor.getContent();
			}else{
				content = $("textarea[name='"+inputid+"']").val();
			}
			
			formData.append('action', action);
			formData.append('pavo_board_post_content', content);
			formData.append('pavo_board_page_id', $("input[name=pavo_post_cur_board_page]").val());
			
			//file 용량확인
			var size_check = $("input[name='pavo_attach_size']").val();
			if(size_check < 0){
				alert("첨부파일 용량이 초과되었습니다.");
				return;
			}
			
			jQuery.ajax({
				type: 'post',
				dataType : 'json',
		        url: pavo_common_js.ajax_url, 
		        data: formData, 
		        contentType: false,
		        processData: false,
		        success:function(response) {
		        	if(response.role_check){
						if(!$.isEmptyObject(response.loadpage)){
							$(location).attr('href',response.loadpage);
						}
					}else{
						alert(response.message);
					}
		        }, 
		        error: function(errorThrown){
		        	alert("오류가 발생하였습니다.");
		            //console.log(errorThrown); 
		        } 
			});
		}
	});
	
	
	//게시글 삭제
	$(".pavoboard-controller .button-delete").click(function(e){
		e.preventDefault();
		
		if(confirm("게시글을 삭제하시겠습니까?")) {
			var target_action = $(this).attr("href");
			var target_post = $(this).attr("post_num");
			
			if($(this).hasClass("guest-delete")){
				$("#pavoboard-transparent-layer").show();
				$("#pavoboard-transparent-layer form[name='pavo_bbs_password_form']").attr("action", target_action);
				$("#pavoboard-transparent-layer input[name='postId']").val(target_post);
				$("#pavoboard-transparent-layer input[name='submit_type']").val("deletepost");
			}else{
				$("#pavoboard-transparent-layer form[name='pavo_bbs_password_form']").attr("action", target_action);
				$("#pavoboard-transparent-layer input[name='postId']").val(target_post);
				$("#pavoboard-transparent-layer input[name='submit_type']").val("deletepost");
				$("#pavoboard-transparent-layer form[name='pavo_bbs_password_chk_form']").submit();
			}
			
			
		}
	});
	
	/////////////////////////////// 익명  ///////////////////////////////////////
	
	$("table.pavoboard-table a.guest-secret").click(function(e){
		e.preventDefault();
		var target_action = $(this).attr("href");
		var target_post = $(this).attr("post_num");
		
		$("#pavoboard-transparent-layer").show();
		$("#pavoboard-transparent-layer form[name='pavo_bbs_password_form']").attr("action", target_action);
		$("#pavoboard-transparent-layer input[name='postId']").val(target_post);
		$("#pavoboard-transparent-layer input[name='submit_type']").val("readpost");
		
	});
	
	
	
	///////////////////////////// password layer //////////////////////////////////
	$("#pavoboard-transparent-layer a.btn_close").on("click",function(e){
		e.preventDefault();
		$("#pavoboard-transparent-layer").hide();
		$("#pavoboard-transparent-layer form[name='pavo_bbs_password_form']").attr("action", "");
		$("#pavoboard-transparent-layer input[name='anonymousPassword']").val("");
	});
	
	$("#pavoboard-transparent-layer a.btn_ok").on("click",function(e){
		e.preventDefault();
		$("#pavoboard-transparent-layer form[name='pavo_bbs_password_chk_form']").submit();
	});
	
	$("#pavoboard-transparent-layer form[name='pavo_bbs_password_chk_form']").submit(function(e){
		e.preventDefault();
		
		var input_pw 	= $("#pavoboard-transparent-layer input[name='anonymousPassword']").val();
		var postId 		= $("#pavoboard-transparent-layer input[name='postId']").val();
		var board_id 	= $("#pavoboard-transparent-layer input[name='board_id']").val();
		var submit_type	= $("#pavoboard-transparent-layer input[name='submit_type']").val();
		var action_type	= "";
		
		if(submit_type == "deletepost"){
			action_type = "ebbsmate_delete_post";
		}else{
			action_type = "ebbsmate_post_password_chk";
		}
		
		var data =  {
				action 		: 	action_type, 
	            newPassword : 	input_pw,
	            postId 		: 	postId,
	            type		: 	submit_type,
	            board_id	:	board_id,
	            pavo_board_page_id 	:	$("input[name=pavo_post_cur_board_page]").val()
	            	
		};
		
		$.post(pavo_common_js.ajax_url, data, function(response) {
			if(response.role_check){
				if($.isEmptyObject(response.loadpage)){
					$("#pavoboard-transparent-layer input[name='newPassword']").val(input_pw);
					$("#pavoboard-transparent-layer form[name='pavo_bbs_password_form']").submit();
					//$(location).attr('href',response.loadpage);
				}else{
					if(!$.isEmptyObject(response.message)){
						alert(response.message);
					}
					$(location).attr('href',response.loadpage);
				}
			}else{
				alert(response.message);
			}
			
		}, 'json');
	});
	
	
	///////////////// 댓글 /////////////////////////////
	
	//댓글 입력창 표시
	//$("div.comment-box .reply-comment").click(function(e){
	$("div.comment-section").on('click', 'div.comment-box .reply-comment', function(e) {
		//댓글 창 보이기
		var target_comment = $(this).parents(".each-comment").children().last();
		var controll = $(this);
		var comment_ctr = $(this).parents(".comment-controller");
		
		if($(".comment-reply").size() > 0){
			$(".comment-reply").animate({"height": "0px", "opacity": 1}, 500, function() {
				$(".comment-reply").remove();
			});
		}
		
		var data = {
			action 		: "pavoboard-get-replycomment-form", 
			comment_id	: $(this).attr('comment-id'),
			board_id 	: $("#pavoboard-transparent-layer input[name='board_id']").val(),
			type		: "write"
		}
		
		$.post(pavo_common_js.ajax_url, data, function(response) {
			try {
				var response = jQuery.parseJSON( response );
				if(!response.role_check){
					alert(response.message);
				}
			} catch (e) {
				target_comment.after(response);
				controll.hide();
				comment_ctr.hide();
				$(".comment-reply").animate({"height": "100%", "opacity": 1}, 500, function() {
					
				});
			}
			
		});
	});
	
	
	//댓글 수정창 표시 or 삭제
	$("div.comment-section").on('click', 'div.comment-controller span a', function(e) {
		//댓글 창 보이기
		var comment_ctr = $(this).parents(".comment-controller");
		var commentId = $(this).attr('comment-id');
		
		var data = {
			action 		: "pavoboard-get-replycomment-form", 
			comment_id	: commentId,
			board_id 	: $("#pavoboard-transparent-layer input[name='board_id']").val(),
			post_id 	: $("input[name=pavo_post_cur_id]").val(),
		}
		
		if($(this).hasClass("reply-comment")){
//			var target_comment = $(this).parents(".each-comment").children().last();
			var target_comment = $(this).parents(".comment-controller").siblings(".comment-box");
			data["type"] = "write";
			
			$.post(pavo_common_js.ajax_url, data, function(response) {
				try {
					var response = jQuery.parseJSON( response );
					if(!response.role_check){
						alert(response.message);
					}
				} catch (e) {
					if($(".comment-reply").size() > 0){
						$(".comment-reply").remove();
					}
					$(".comment-edit").remove();
					$("div.comment-controller").show();
					comment_ctr.hide();
					target_comment.after(response);
				}
			});
		};
		
		if($(this).hasClass("comment_edit")){
			var target_comment = $(this).parents(".comment-controller").siblings(".comment-box");
			data["type"] = "edit";
			
			$.post(pavo_common_js.ajax_url, data, function(response) {
				try {
					var response = jQuery.parseJSON( response );
					if(!response.role_check){
						alert(response.message);
					}
				} catch (e) {
					if($(".comment-reply").size() > 0){
						$(".comment-reply").remove();
					}
					$(".comment-edit").remove();
					$("div.comment-controller").show();
					comment_ctr.hide();
					target_comment.after(response);
				}
			});
			
		}
		
		if($(this).hasClass("comment_delete")){
			var inner_comment = $(this).parents(".comment-controller").siblings(".comment-box");
			var target_comment = $(this).parents(".comment-controller").siblings(".comment-box");
			var comment_controller = $(this).parents(".comment-controller");
			var remove_comment = comment_controller.parent(".each-comment");
			data["type"] = "delete";
			
			$.post(pavo_common_js.ajax_url, data, function(response) {
				try {
					var response = jQuery.parseJSON( response );
					if(!response.role_check){
						alert(response.message);
					}

					if(response.role_check && response.delete_check){
						if(confirm(response.message)) {
							var comment_data = {
								action 					: "pavoboard-delete-comment", 
								pavoboard_comment_id	: commentId,
								pavo_comment_board_id 	: $("#pavoboard-transparent-layer input[name='board_id']").val(),
							}
							//댓글 삭제요청
							$.post(pavo_common_js.ajax_url, comment_data, function(result) {
								try {
									var result = jQuery.parseJSON( result );
									alert(result.message);
									remove_comment.remove();
								} catch (e) {
									if($(".comment-reply").size() > 0){
										$(".comment-reply").remove();
									}
									comment_controller.remove();
									$(".comment-edit").remove();
									target_comment.after(result);
									target_comment.remove();
								}
								
								var cmt_cnt = $("div.comment-section  strong").text();

								cmt_cnt = cmt_cnt.match(/\(.*\)/gi);
								cmt_cnt += "";
								cmt_cnt = cmt_cnt.split("(").join("");
								cmt_cnt = cmt_cnt.split(")").join("");
								cmt_cnt = parseInt(cmt_cnt);
								
								$("div.comment-section  strong").text("("+(cmt_cnt-1)+")");
							});
						}
					}
				} catch (e) {
					if($(".comment-reply").size() > 0){
						$(".comment-reply").remove();
					}
					$(".comment-edit").remove();
					$("div.comment-controller").show();
					comment_ctr.hide();
					inner_comment.after(response);
				}
			});
		}
		
	});
	
	
	
	//댓글 업데이트 , 답글 등록
	$("div.comment-section").on('click', 'div.comment-reply a.comment-save, div.comment-edit button.comment-delete', function(e) {
		e.preventDefault();
		
		var comment_ctr = $(this).parents(".comment-reply").siblings("div.comment-controller");
		var target_comment = ""
		var type = "";
		if($(this).hasClass("comment-update")){
			type = "update";
			var target_content = $(this).parents(".comment-reply").siblings("div.comment-box").find('div.comment-content-text h2');
			var target_writer = $(this).parents(".comment-reply").siblings("div.comment-box").find('div.comment-content-writer span.author');
		}else if($(this).hasClass("comment-delete")){
			type = "delete";
			target_comment = $(this).parents(".each-comment");
		}else{
			target_comment = $(this).parents(".each-comment");
		}
		
		var comment_layer = $(this).parents(".comment-reply");
		var form_data = $(this).parents("form").serialize();
		$.post(pavo_common_js.ajax_url, form_data, function(response) {
			try {
				var response = jQuery.parseJSON( response );
				if(!response.role_check){
					alert(response.message);
				}else{
					if(response.role_result.comment_type == "update"){
						target_content.html(response.role_result.comment_content);
						target_writer.html(response.role_result.comment_author);
						comment_layer.remove();
						comment_ctr.show();
					}
					
					if(response.role_result.comment_type == "delete"){
						alert(response.message);
						target_comment.remove();
						
						var cmt_cnt = $("div.comment-section  strong").text();

						cmt_cnt = cmt_cnt.match(/\(.*\)/gi);
						cmt_cnt += "";
						cmt_cnt = cmt_cnt.split("(").join("");
						cmt_cnt = cmt_cnt.split(")").join("");
						cmt_cnt = parseInt(cmt_cnt);
						
						$("div.comment-section  strong").text("("+(cmt_cnt-1)+")");
					}
				}
			} catch (e) {
				if(type == "delete"){
					target_comment.remove();
				}else{
					target_comment.append(response);
					comment_layer.remove();
					comment_ctr.show();
				}
			}
		});
	});
	
	$("div.comment-section").on('click', 'div.comment-reply a.btn-pavoboard-comment-reply-close, div.comment-edit button.comment-close ', function(e) {
		$(this).parents(".each-comment").children('.comment-controller').show();
		$(this).parents(".comment-reply").remove();
		$(this).parents(".comment-edit").remove();
	}).on('submit', 'div.comment-reply form.comment-delete-form', function(e) {
		e.preventDefault();
		
		var target_comment = $(this).parents(".each-comment");
		var form_data = $(this).serialize();
		
		$.post(pavo_common_js.ajax_url, form_data, function(response) {
			try {
				var response = jQuery.parseJSON( response );
				if(!response.role_check){
					alert(response.message);
				}else{
					alert(response.message);
					target_comment.remove();
				}
			} catch (e) {
				target_comment.after(response);
				target_comment.remove();
			}
		});
	})
	
	//댓글 등록
	$("div.comment-section button.button-comment-write").click(function(e){
		e.preventDefault();
		var form_data = $(this).parents("form[name='pavo_comment_insert_form']").serialize();
		
		$.post(pavo_common_js.ajax_url, form_data, function(response) {
			try {
				var response = jQuery.parseJSON( response );
				if(!response.role_check){
					alert(response.message);
				}
			} catch (e) {
				var target_comment = $('ul.list-wrapper').children(':first-child');
				if(target_comment.exists()){
					target_comment.before(response);
				}else{
					$('ul.list-wrapper').append(response);
				}
				
				var cmt_cnt = $("div.comment-section  strong").text();

				cmt_cnt = cmt_cnt.match(/\(.*\)/gi);
				cmt_cnt += "";
				cmt_cnt = cmt_cnt.split("(").join("");
				cmt_cnt = cmt_cnt.split(")").join("");
				cmt_cnt = parseInt(cmt_cnt);
				
				$("div.comment-section  strong").text("("+(cmt_cnt+1)+")");
				

			}
		});
		
	});
	
	
	/**
	 *  ver 1.0.1 작성자 over 시 메뉴
	 */
	$(".pavoboard-wrapper").on('click', '.user-profile , .m_writer_layer span', function(e) {
		e.preventDefault();
		
		$(".writer_layer ul").hide();
		$(this).next().children("ul").show();
	});
	
	$(".pavoboard-wrapper .writer_layer").live('hover',function(e){
		
		if(e.type == "mouseleave"){
			$(this).children("ul").hide();
		}
    });
	
});


//게시글 입력폼 체크
function ebbsmate_insert_form_chk() {
	//게스트 작성자 비밀번호 체크
	if(jQuery('input[name=pavo_board_guest_name]').length > 0) {
		if(jQuery('input[name=pavo_board_guest_name]').val() == "") {
			alert("작성자명을 입력하세요.");
			jQuery('input[name=pavo_board_guest_name]').focus();
			return false;
		}
	}
	
	if(jQuery('input[name=pavo_board_guest_password]').length > 0) {
		if(jQuery('input[name=pavo_board_guest_password]').val() == "") {
			alert("비밀번호를 입력하세요.");
			jQuery('input[name=pavo_board_guest_password]').focus();
			return false;
		}
	}
	
	//제목 체크
	if(jQuery('input[name=pavo_board_post_title]').val() == "") {
		alert("제목을 입력하세요.");
		jQuery('input[name=pavo_board_post_title]').focus();
		return false;
	}
	
	return true;
}