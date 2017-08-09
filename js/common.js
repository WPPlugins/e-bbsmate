//공통 script 처리
jQuery(document).ready(function($) {
	
	$("form[name=pavo_comment_insert_form]").submit(function(e){
	    e.preventDefault();
	});
	
	var stepSlider = document.getElementById('slider');
	
	if(stepSlider != null) {
		noUiSlider.create(stepSlider, {
			start: [ 15 ],
			step: 1,
			range: {
				'min': [  0 ],
				'max': [ 50 ]
			}
		});
	}

	var stepSliderValueElement = document.getElementById('slider-step-value');

	if(stepSliderValueElement != null) {
	stepSlider.noUiSlider.on('update', function( values, handle ) {
			var num = Math.floor(values[handle]);
			stepSliderValueElement.innerHTML = num;
			
			$('input[name="basic_margin_px"]').val(num+"px");
			//바로 미리보기 적용
			$(".pavoboard-list-number").css("padding-top", num+"px");
			$(".pavoboard-list-number").css("padding-bottom", num+"px");
		});
	}
	
	//게시판 상세 옵션 열기/닫기
	$('#pavo_bbs_option').click(function(e){		
		if($('#pavo_bbs_option_list').css("display") == "block") {
			$( "#pavo_bbs_option_list").hide();		
		} else if($('#pavo_bbs_option_list').css("display") == "none"){
			$( "#pavo_bbs_option_list").show();
		}
	});
	
	//권한 설정
	$( "#pavo_bbs_list_role" ).change(function() {
		  var text = $( "#pavo_bbs_list_role option:selected" ).text();
		  var val = $( "#pavo_bbs_list_role option:selected" ).val();
		  		  
		  $("#pavo_bbs_option_list").after("<input type='hidden' name='_pbbs_board_list_permission' value='"+val+"'/>");
		  
		  $( "#pavo_bbs_list_role_list" ).append("<span name='"+val+"'>"+text+"<a onclick='javascript:pavo_boardmate_delete_role(\"list\", \""+val+"\");'>x</a>&nbsp;&nbsp;&nbsp;</span>");
	});
	
	$( "#pavo_bbs_read_role" ).change(function() {
		  var text = $( "#pavo_bbs_read_role option:selected" ).text();
		  var val = $( "#pavo_bbs_read_role option:selected" ).val();
		  		  
		  $("#pavo_bbs_option_list").after("<input type='hidden' name='_pbbs_board_read_permission' value='"+val+"'/>");
		  
		  $( "#pavo_bbs_read_role_list" ).append("<span name='"+val+"'>"+text+"<a onclick='javascript:pavo_boardmate_delete_role(\"read\", \""+val+"\");'>x</a>&nbsp;&nbsp;&nbsp;</span>");
	});
	
	$( "#pavo_bbs_write_role" ).change(function() {
		  var text = $( "#pavo_bbs_write_role option:selected" ).text();
		  var val = $( "#pavo_bbs_write_role option:selected" ).val();
		  		  
		  $("#pavo_bbs_option_list").after("<input type='hidden' name='_pbbs_board_write_permission' value='"+val+"'/>");
		  
		  $( "#pavo_bbs_write_role_list" ).append("<span name='"+val+"'>"+text+"<a onclick='javascript:pavo_boardmate_delete_role(\"write\", \""+val+"\");'>x</a>&nbsp;&nbsp;&nbsp;</span>");
	});
	
	$( "#pavo_bbs_edit_role" ).change(function() {
		  var text = $( "#pavo_bbs_edit_role option:selected" ).text();
		  var val = $( "#pavo_bbs_edit_role option:selected" ).val();
		  		  
		  $("#pavo_bbs_option_list").after("<input type='hidden' name='_pbbs_board_edit_permission' value='"+val+"'/>");
		  
		  $( "#pavo_bbs_edit_role_list" ).append("<span name='"+val+"'>"+text+"<a onclick='javascript:pavo_boardmate_delete_role(\"edit\", \""+val+"\");'>x</a>&nbsp;&nbsp;&nbsp;</span>");
	});
	
	$( "#pavo_bbs_delete_role" ).change(function() {
		  var text = $( "#pavo_bbs_delete_role option:selected" ).text();
		  var val = $( "#pavo_bbs_delete_role option:selected" ).val();
		  		  
		  $("#pavo_bbs_option_list").after("<input type='hidden' name='_pbbs_board_delete_permission' value='"+val+"'/>");
		  
		  $( "#pavo_bbs_delete_role_list" ).append("<span name='"+val+"'>"+text+"<a onclick='javascript:pavo_boardmate_delete_role(\"delete\", \""+val+"\");'>x</a>&nbsp;&nbsp;&nbsp;</span>");
	});
	
	
});

//게시글 등록
function ebbsmate_insert_post() {
	if(ebbsmate_insert_form_chk()) {
		var formData = new FormData(jQuery("#ebbsmate_post_write_form")[0]);		
		var content;
		var inputid = 'pavo_board_post_content';
		
		if ( typeof tinyMCE != 'undefined' &&  tinyMCE.activeEditor ) {
			var editor = tinyMCE.get(inputid);
			content = editor.getContent();
		}else{
			content = $("textarea[name='"+inputid+"']").val();
		}

		console.log(content);
		
		formData.append('action', 'ebbsmate_insert_post');
		formData.append('pavo_board_post_content', content);


		console.log(ajaxurl);
		
		jQuery.ajax({ 
			type: 'post',
	        url: ajaxurl, 
	        data: formData, 
	        contentType: false,
	        processData: false,
	        success:function(data) {
				console.log(data);
	        	window.location = data;
	        }, 
	        error: function(errorThrown){ 
	            console.log(errorThrown); 
	        } 
		});
	}
}

//게시글 수정
function ebbsmate_update_post() {
	if(ebbsmate_insert_form_chk()) {
		var formData = new FormData(jQuery("#pavo_post_edit_form")[0]);		
		var content;
		var inputid = 'pavo_board_post_content';
		if ( typeof tinyMCE != 'undefined' &&  tinyMCE.activeEditor ) {
			var editor = tinyMCE.get(inputid);
			content = editor.getContent();
		}else{
			content = $("textarea[name='"+inputid+"']").val();
		}
		
		formData.append('action', 'ebbsmate_update_post');
		formData.append('pavo_board_post_content', content);
		
		jQuery.ajax({ 
			type: 'post',
	        url: ajaxurl, 
	        data: formData, 
	        contentType: false,
	        processData: false,
	        success:function(data) {
	        	window.location = data;
	        }, 
	        error: function(errorThrown){ 
	            console.log(errorThrown); 
	        } 
		});
	}
}

//게시글 삭제
function ebbsmate_delete_post() {
	
	var postId = jQuery("input[name=pavo_post_cur_id]").val();
	var return_url = jQuery("input[name=pavo_delete_return_url]").val();
		
	jQuery.ajax({ 
		type: 'post',
        url: ajaxurl, 
        data: {
        	'action' : 'ebbsmate_delete_post', 
            'post_id' : postId
        }, 
        success:function(data) {
        	alert("게시글이 삭제되었습니다.");
        	window.location = return_url;
        }, 
        error: function(errorThrown){ 
            console.log(errorThrown); 
        } 
	});
}

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

function open_file_attach_layer() {	
	if (jQuery('.innerList').is(':visible')) {
		jQuery(".innerList").hide();
	} else {
		jQuery(".innerList").show();
	}
}

// 입력한 게시판 슬러그에 따라 숏코드 생성
function pavo_bbs_create_shortcode(slug) {
		
	if(slug.value.match(/\s/g)) {
		slug.value=slug.value.replace(/\s/g,'');
	}
	
	var result = "[pavo-bbs id=\""+slug.value+"\"]";
	jQuery("#pavo_bbs_shortcode").val(result);
}

//댓글 수정창을 생성한다.
function pavo_board_comment_edit(elem, author) {	
	pavo_board_comment_close(elem);
	jQuery("#comment-edit-"+elem).show();
	
	var content = jQuery("input[name=pavo_comment_content_view_hidden_"+elem+"]").val();
	jQuery("textarea[name=pavo_comment_content_"+elem+"]").val(content);
}

//답댓글 창을 생성한다.
function pavo_board_comment_reply(elem) {
	pavo_board_comment_close(elem);
	jQuery("#comment-reply-"+elem).show();
}

//게시글 익명 팝업 열기
function ebbsmate_open_layer_popup(action) {
	jQuery("#pavoboard-transparent-layer").show();
	jQuery("#ebbsmate_guest_edit_action").val(action);
} 

//게시글 익명 열기
function ebbsmate_close_layer_popup() {
	jQuery("#pavoboard-transparent-layer").hide();
} 

//게시글 익명 비밀번호 체크
function ebbsmate_post_edit_pwd_chk() {
	//사용자가 입력한 비밀번호와 기존 비밀번호가 일치하는지 체크
	var newPassword = jQuery("input[id=ebbsmate_edit_pwd]").val();
	var postId = jQuery("input[name=pavo_post_cur_id]").val();
	var action = jQuery("#ebbsmate_guest_edit_action").val();
			
	jQuery.ajax({ 
		type: 'post',
        url: ajaxurl, 
        data: { 
            'action' : 'ebbsmate_post_password_chk', 
            'newPassword' : newPassword,
            'postId' : postId
        }, 
        success:function(data) {
            if(data == "1") {
            	if(action == "edit") {
            		window.location = jQuery("input[name=ebbsmate_post_edit_url]").val();
            	} else if(action == "delete") {
            		window.location = jQuery("input[name=ebbsmate_post_delete_url]").val();
            	}
            } else {
            	alert("비밀번호가 일치하지 않습니다.");
            	return false;
            }
        }, 
        error: function(errorThrown){ 
            console.log(errorThrown); 
        } 
	});
}

//익명 댓글 비밀번호 체크
function pbbs_guest_password_chk(elem, action) {	
	//사용자가 입력한 비밀번호와 기존 비밀번호가 일치하는지 체크
	if(action == "delete") {
		var newPassword = jQuery("input[name=pavo_comment_delete_password_"+elem+"]").val();
	} else {
		var newPassword = jQuery("input[name=pavo_comment_password_"+elem+"]").val();
	}
	var postId = jQuery("input[name=pavo_comment_cur_id]").val();
				
	jQuery.ajax({ 
		type: 'post',
        url: ajaxurl, 
        data: { 
            'action' : 'pbbs_post_password_chk',
            'postId' : postId,
            'newPassword' : newPassword
        }, 
        success:function(data) {        	
            if(data == "1") {
            	if(action == "edit") {
            		pavo_board_comment_update(elem);
            	} else if(action == "delete") {
            		pavo_board_comment_trash(elem);
            	}
            } else {
            	alert("비밀번호가 일치하지 않습니다.");
            	return false;
            }
        }, 
        error: function(errorThrown){ 
            console.log(errorThrown); 
        } 
	});
}

//댓글 글쓴이와 현재 사용자가 일치하는지 체크
function pbbs_comment_edit_chk(elem, action) {
	var postId = jQuery("input[name=pavo_comment_cur_id]").val();
		
	jQuery.ajax({ 
		type: 'post',
        url: ajaxurl, 
        data: { 
            'action' : 'pbbs_comment_edit_chk', 
            'postId' : postId
        }, 
        success:function(data) {
            if(data == "1") {
            	if(action == "edit") {
            		pavo_board_comment_update(elem);
            	} else if(action == "delete") {
            		pavo_board_comment_trash(elem);
            	}
            } else {
            	alert("댓글을 수정할 권한이 없습니다.");
            	return false;
            }
        }, 
        error: function(errorThrown){ 
            console.log(errorThrown); 
        }
        
	});
}

//게시글 비밀번호 체크
function pbbs_post_guest_password_chk(action) {
	//사용자가 입력한 비밀번호와 기존 비밀번호가 일치하는지 체크
	var newPassword = jQuery("input[name=pavo_post_password]").val();
	var postId = jQuery("input[name=pavo_post_cur_id]").val();
	
	jQuery.ajax({ 
		type: 'post',
        url: ajaxurl, 
        data: { 
            'action' : 'ebbsmate_post_password_chk', 
            'newPassword' : newPassword,
            'postId' : postId
        }, 
        success:function(data) {
            if(data == "1") {
            	if(action == "edit") {
            		ebbsmate_update_post();
            	} else if(action == "delete") {
            		window.location = jQuery("input[name=pavo_delete_return_url]").val();
            	} else if(action == "read") {
            		window.location = jQuery("input[name=pavo_read_return_url]").val();
            	}
            } else {
            	alert("비밀번호가 일치하지 않습니다.");
            	return false;
            }
        }, 
        error: function(errorThrown){ 
            console.log(errorThrown); 
        } 
	});
}

//게시글 글쓴이와 현재 사용자가 일치하는지 체크
function ebbsmate_post_edit_chk(action) {
	var postId = jQuery("input[name=pavo_post_cur_id]").val();
	
	jQuery.ajax({ 
		type: 'POST',
        url: ajaxurl, 
        data: { 
            'action' : 'ebbsmate_post_edit_chk', 
            'postId' : postId
        }, 
        success:function(data) {
            if(data == "1") {
            	if(action == "edit") {
            		ebbsmate_update_post();
            	} else if(action == "delete") {
            		if(confirm("게시글을 삭제하시겠습니까?")) {
            			ebbsmate_delete_post();
            		}
            	}
            } else {
            	alert("게시글을 수정할 권한이 없습니다.");
            	return false;
            }
        }, 
        error: function(errorThrown){ 
            console.log(errorThrown); 
        }
        
	});
}

//모든 댓글 수정창을 닫는다.
function pavo_board_comment_close(elem) {
	//jQuery("#comment-delete-"+elem).hide();
	jQuery("div[id^='comment-delete-']").hide();
	
	//jQuery("#comment-reply-"+elem).hide();
	jQuery("div[id^='comment-reply-']").hide();
	
	//jQuery("#comment-edit-"+elem).hide();
	jQuery("div[id^='comment-edit-']").hide();
}

//댓글 삭제창을 생성한다.
function pavo_board_comment_delete(elem) {
	pavo_board_comment_close(elem);
	jQuery("#comment-delete-"+elem).show();
}
	
//댓글 삭제
function pavo_board_comment_delete_chk(elem, author) {		
	if(author == "guest") {
		pbbs_guest_password_chk(elem, 'delete');
	} else {
		pbbs_comment_edit_chk(elem, 'delete');
	}
}

function pavo_board_comment_trash(elem) {
	
	if(confirm("댓글을 삭제하시겠습니까?")) {
		
		jQuery.ajax({ 
			type: 'POST',
	        url: ajaxurl, 
	        data: jQuery("form[name='pavo_comment_delete_form_"+elem+"']").serialize()+"&action=ebbsmate_delete_comment", 
	        success:function(data) {
	        	window.location = data;
	        	jQuery("input[name=pavo_comment_delete_password_"+elem+"]").val("");
	        }, 
	        error: function(errorThrown){ 
	            console.log(errorThrown); 
	        }
	        
		});
	}
}

function pavo_board_comment_insert() {
	if(confirm("댓글을 등록하시겠습니까?")) {
		jQuery.ajax({ 
			type: 'POST',
	        url: ajaxurl, 
	        data: jQuery("form[name=pavo_comment_insert_form]").serialize()+"&action=ebbsmate_insert_comment", 
	        success:function(result) {
				if(result.error_info.error == 'none'){
		        	jQuery("input[name=pavo_comment_writer]").val("");
		        	jQuery("input[name=pavo_comment_password]").val("");
			    	jQuery("textarea[name=pavo_comment_content]").val("");
					window.location = result.data.url;
				}else {
					alert(result.error_info.message);
				}
	        }, 
	        error: function(errorThrown){ 
	            console.log(errorThrown); 
	        }
	        
		});
	}
}

function pavo_board_comment_reply_insert(elem) {	
	if(confirm("댓글을 등록하시겠습니까?")) {
		jQuery.ajax({ 
			type: 'POST',
	        url: ajaxurl, 
	        data: jQuery("form[name=pavo_comment_reply_form_"+elem+"]").serialize()+"&action=ebbsmate_insert_comment&type=reply&elem="+elem, 
	        success:function(data) {
	        	window.location = data;
	        	jQuery("textarea[name=pavo_comment_reply_content_"+elem+"]").val("");
	        	jQuery("input[name=pavo_comment_reply_writer_"+elem+"]").val("");
	        	jQuery("input[name=pavo_comment_reply_password__"+elem+"]").val("");
	        }, 
	        error: function(errorThrown){ 
	            console.log(errorThrown); 
	        }
	        
		});
	}
}

function pavo_board_comment_update(elem) {
	if(confirm("댓글을 수정하시겠습니까?")) {
		jQuery.ajax({ 
			type: 'POST',
	        url: ajaxurl, 
	        data: jQuery("form[name=pavo_comment_edit_form_"+elem+"]").serialize()+"&action=ebbsmate_update_comment", 
	        success:function(data) {
	        	jQuery("textarea[name=pavo_comment_content_"+elem+"]").val("");
	        	jQuery("input[name=pavo_comment_writer_"+elem+"]").val("");
	        	jQuery("input[name=pavo_comment_password__"+elem+"]").val("");
	        	window.location = data;
	        }, 
	        error: function(errorThrown){ 
	            console.log(errorThrown); 
	        }
	        
		});
	}
}

//사용자 권한 체크
function pavo_board_userrole_chk(boardId, postId, type, page) {
	jQuery.ajax({ 
		type: 'POST',
        url: ajaxurl, 
        data: { 
            'board_id' : boardId, 
            'post_id' : postId,
            'type' : type,
            'page' : page,
            'action' : 'ebbsmate_userrole_chk'
        },
        dataType: "json",
        success:function(data) {
        	window.location = data[0];
        	
        	if(data[1] != "") {
        		alert(data[1]);
        	}
        }, 
        error: function(errorThrown){ 
            console.log(errorThrown); 
        }
        
	});
}