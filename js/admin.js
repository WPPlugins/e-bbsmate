jQuery(document).ready(function($) {
	
//	font_decoration_preview();
//	border_width_preview();
		
	$('#pavobbs-image-preview-upload').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: '배경 이미지 업로드',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field            
            $(".preface_border_color").css("background-image", "url("+image_url+")");
            $("#pavoboard_headline_image_url").val(image_url);
            $("#pavobbs-image-delete").css("display", "block");
            
            //이미지 스타일
            var align_option = $("#pavoboard_image_align").val();
            
            if(align_option == "left") {
            	$(".preface_border_color").css("background-repeat", "no-repeat");
            	$(".preface_border_color").css("background-position", "left top");
            } else if(align_option == "right") {
            	$(".preface_border_color").css("background-repeat", "no-repeat");
            	$(".preface_border_color").css("background-position", "right top");
            } else if(align_option == "repeat") {
            	$(".preface_border_color").css("background-repeat", "repeat");
            }
        });
    });
	
	$("#pavoboard_image_align").change(function() {
    	//이미지 스타일
        var align_option = $("#pavoboard_image_align").val();
        
        if(align_option == "left") {
        	$(".preface_border_color").css("background-repeat", "no-repeat");
        	$(".preface_border_color").css("background-position", "left top");
        } else if(align_option == "right") {
        	$(".preface_border_color").css("background-repeat", "no-repeat");
        	$(".preface_border_color").css("background-position", "right top");
        } else if(align_option == "repeat") {
        	$(".preface_border_color").css("background-repeat", "repeat");
        }
    });
		
	$( "input[name*='ebbsmate_preface_flag']" ).change(function() {
		 $( ".preface_editor" ).slideToggle( "slow", function() {
		 });
	});
	
	$( '.pavoboard-dashboard .setting-title' ).click( function() {
		
		var panel_wrap =  $(".pavoboard-dashboard .settings-panel");
		panel_wrap.removeClass( 'active' );
		$( this ).parent().addClass( 'active' );
		
		
		/*
		var panel_wrap = $( this ).closest( 'div.panel-wrap' );
		$( 'ul.wc-tabs li', panel_wrap ).removeClass( 'active' );
		$( this ).parent().addClass( 'active' );
		$( 'div.panel', panel_wrap ).hide();
		$( $( this ).attr( 'href' ) ).show();
		*/
		return false;
	});
	
	$("input:checkbox[name='headline_border_width[]']").click(function() {
		border_width_preview();
	});
	
	/*
	 *  colpick 설정
	 */
	
	$('.color-box.pavobbs-style').colpick({
		layout:'hex',
		color:'000000',
		onSubmit:function(hsb,hex,rgb,el) {
			$(el).css('background-color', '#'+hex);
			$(el).colpickHide();
			$(el).siblings().find(":text").eq(0).val('#'+hex);
		},
		onChange:function(hsb,hex,rgb,el)	{
			var target_class = $(el).attr('style_target');
			var target_type = $(el).attr('style_type');
			
			if(target_class == "link_font_color") {
				$(".pavoboard-list-title").hover(function() {
					$(this).children('a').css("color", '#'+hex);
				  }, function() {
					  $(this).children('a').css("color","");
				  }
				);
			} else if(target_class == "pavoboard-button") {
				var cssText = $( '.pavoboard-preview .'+ target_class).attr("style");
				
				if(cssText != "") {
					cssText = cssText + " "+target_type+": #"+hex+" !important";
				} else {
					cssText = target_type+": #"+hex+" !important";
				}
				
				$(".pavoboard-preview ."+target_class).css("cssText", cssText);
			} else if(target_class == "point_color") {
				//헤더 색상 변경
				//$( '.pavoboard-preview tr th').css("background",'#'+hex);
				//$( '.pavoboard-preview tr th').css("border-color",'#'+hex);
				//공지 아이콘 색상 변경
				//$( '.pavoboard-preview .noti-icon').css("background",'#'+hex);
				//페이징 목록 색상 변경
				$( '.pavoboard-preview .pagingNav strong').css("color",'#'+hex);
				//페이징 목록 테두리 색상 변경
				$( '.pavoboard-preview .pagingNav strong').css("border-color",'#'+hex);
				//댓글 갯수 색상 변경
				$( '.pavoboard-preview span.entry_comment_count').css("color",'#'+hex);
				//답글 색상 변경
				$( '.pavoboard-preview span.icon-reply').css("color",'#'+hex);
				//hover 색상 변경
				$(".pavoboard-preview tr td.pavoboard-list-title a").hover(function() {
					$(this).css("color", "#"+hex);
				  }, function() {
					  $(this).css("color", "");
				  }
				);
			} else if(target_class == "mouseover_color") {
				$(".pavoboard-preview tr").hover(function() {
					$(this).css("background-color", "#"+hex);
				  }, function() {
					  $(this).css("background-color", "");
				  }
				);
			} else {
				$( '.pavoboard-preview .'+ target_class).css(target_type,'#'+hex);
				/*
				var cssText = $( '.pavoboard-preview .'+ target_class).attr("style");
				cssText = cssText.replace(/;/gi, " !important;");
				$( '.pavoboard-preview .'+ target_class).attr("style", cssText);
				*/
			}
		}
	})
	.css('background-color', '#000000');
	
	$('.colpick').css("z-index", "999");
		
	/*게시판 미리보기 초기값 설정
	$(".head-st").css("background-image", "url("+$("#pavoboard_headline_image_url").val()+")");
	
	//공통 설정 - 포인트 색상
	$('.color-box.pavobbs-style').eq(0).css("background-color", $('input[name=pavoboard_point_color]').val());
	//말머리 설정 - 테두리 색상
	//$('.color-box.pavobbs-style').eq(1).css("background-color", $('input[name=headline_border_color]').val());
	//말머리 설정 - 배경 색상
	//$('.color-box.pavobbs-style').eq(2).css("background-color", $('input[name=headline_background_color]').val());
	//헤더 설정 - 헤더 배경 색상
	$('.color-box.pavobbs-style').eq(1).css("background-color", $('input[name=header_background_color]').val());
	//헤더 설정 - 헤더 구분선 색상
	$('.color-box.pavobbs-style').eq(2).css("background-color", $('input[name=header_division_color]').val());
	//헤더 - 헤더 테두리 색상
	$('.color-box.pavobbs-style').eq(3).css("background-color", $('input[name=header_border_color]').val());
	//공지 설정 - 공지 배경 색상
	$('.color-box.pavobbs-style').eq(4).css("background-color", $('input[name=notice_background_color]').val());
	//공지 설정 - 아이콘 글씨 색상
	$('.color-box.pavobbs-style').eq(5).css("background-color", $('input[name=notice_icon_color]').val());
	//공지 설정 - 아이콘 배경 색상
	$('.color-box.pavobbs-style').eq(6).css("background-color", $('input[name=notice_icon_background_color]').val());
	//공지 설정 - 공지 제목 색상
	$('.color-box.pavobbs-style').eq(7).css("background-color", $('input[name=notice_title_color]').val());
	//텍스트 설정 - 말머리 텍스트 색상
	//$('.color-box.pavobbs-style').eq(8).css("background-color", $('input[name=headline_font_color]').val());
	//텍스트 설정 - 헤더 텍스트 색상
	$('.color-box.pavobbs-style').eq(8).css("background-color", $('input[name=header_font_color]').val());
	//텍스트 설정 - 링크 텍스트 색상
	$('.color-box.pavobbs-style').eq(9).css("background-color", $('input[name=link_font_color]').val());
	//라인 설정 - 라인 색상
	$('.color-box.pavobbs-style').eq(10).css("background-color", $('input[name=post_line_color]').val());
	//라인 설정 - 마우스 오버 색상
	$('.color-box.pavobbs-style').eq(11).css("background-color", $('input[name=post_mouseover_color]').val());
	//버튼 설정 - 버튼 테두리 색상
	$('.color-box.pavobbs-style').eq(12).css("background-color", $('input[name=button_border_color]').val());
	//버튼 설정 - 버튼 배경 색상
	$('.color-box.pavobbs-style').eq(13).css("background-color", $('input[name=button_bg_color]').val());
	//버튼 설정 - 버튼 글자 색상
	$('.color-box.pavobbs-style').eq(14).css("background-color", $('input[name=button_text_color]').val());
	*/
	
	var color_preview = $('.color-box.pavobbs-style');
	
	
	color_preview.each(function() {
		var color_val = $( this ).siblings().children("input").val();
	  $( this ).css("background-color", color_val);
	});
	
	
	

	//폰트 불러오기
	//var font_family = ebbsmate_get_font($("select[name=basic_font]").val());	
	//$(".pavoboard-preview").css("font-family", font_family);
	
	/*
	 * 라인 스타일 
	 */
	$( ".line_title_bottom" ).change(function() {
		var value = $(this).val();
		var color = $("input[name=post_line_color]").val();
		
		var styles = {
			"border-style" : value,
			"border-color" : color,
			"border-width" : '0 0 1px'
	    };
		
		$(".pavoboard-preview table#pavoboard-table tbody tr td").css(styles);
		
	});
	
	/*
	 *  슬라이드 공통
	 */
 	var sliders = document.getElementsByClassName('pavo-style-slider');
 	var sliders_value = document.getElementsByClassName('pavo-style-slider-value');
 	$(".pavo-style-slider").each(function( i ) {

 		noUiSlider.create(sliders[i], {
 			start: Number( sliders[i].getAttribute("start-data").replace(/px/gi,"").replace(/pt/gi,"") ),
 			range: {
 				'min': Number(sliders[i].getAttribute("min-data")),
 				'max': Number(sliders[i].getAttribute("max-data"))
 			},
 			step: 1
 		});
 		
 		sliders[i].noUiSlider.on('slide', function( values, handle ) {
 			
 			var slide_value = Math.floor(values[handle]);
 			
 			var target = sliders_value[i].getAttribute("style_target");
 			var type = sliders_value[i].getAttribute("style_type");
 			
 			var unit_text = "px";
 			// 스타일 미리보기 적용
  			if(target == "div.pavoboard-wrapper .head-st") {
 				$('input[name="headline_round_px"]').val(slide_value+"px");
 			}
 			
 			if(target == ".noti-icon") {
 				$('input[name="notice_icon_border_radius"]').val(slide_value+"px");
 			}
 			
 			if(target == "div.pavoboard-wrapper .pavoboard-button") {
 				$('input[name="button_round_px"]').val(slide_value+"px");
 			}
 			
 			if(target == "div.pavoboard-wrapper table#pavoboard-table thead tr th span") {
 				$('input[name="header_font_size"]').val(slide_value+"pt");
 				unit_text = "pt";
 			}
 			
 			if(target == ".header_thead th") {
 				$('input[name="header_border_width"]').val(slide_value+"px");		
 			}
 	
 			if(target == ".pavoboard-list-number") { 				
 				$('input[name="post_line_margin"]').val(slide_value+"px");
 			}
 			
 			if(target == "div.pavoboard-wrapper .pavoboard-button") {
 				$('input[name="post_line_margin"]').val(slide_value+"px");
 			}
 			 			
 			if(target == ".head-st") {	
 				$('input[name="header_border_width"]').val(slide_value+"px");
 			}
 			
 			//분류 슬라이드
 			if(target == "div.ebbsmate-section-wrapper a.section_tab") {
 				$('input[name="section_tab_radius_px"]').val(slide_value+"px");
 			}
 			
 			sliders_value[i].innerHTML = slide_value+unit_text;
 
 			$(target).css(type , slide_value+unit_text);
 			
 			$(".noti-icon").css("border-radius", slide_value);
 		});
 		
 		sliders_value[i].innerHTML = sliders[i].getAttribute("start-data");
 	});
 	
 	/*
	 *  투명도 조절 슬라이드
	 */
 	var sliders2 = document.getElementsByClassName('pavo-style-slider2');
 	var sliders_value2 = document.getElementsByClassName('pavo-style-slider-value2');
 	$(".pavo-style-slider2").each(function( i ) {
 		
 		noUiSlider.create(sliders2[i], {
 			start: Number(sliders2[i].getAttribute("start-data")),
 			range: {
 				'min': Number(sliders2[i].getAttribute("min-data")),
 				'max': Number(sliders2[i].getAttribute("max-data"))
 			},
 			step: 0.1
 		});
 		
 		sliders2[i].noUiSlider.on('slide', function( values, handle ) { 			
 			var slide_value2 = parseFloat(values[handle]);
 			slide_value2 = slide_value2.toFixed(1);
 			sliders_value2[i].innerHTML = slide_value2;
 			
 			var target = sliders_value2[i].getAttribute("style_target");
 			var type = sliders_value2[i].getAttribute("style_type");
 			
 			//말머리 배경 색상 투명도
 			if(target == ".head-st") {	
 				$('input[name="headline_bg_opacity"]').val(slide_value2);
 			}
 			
 			$(target).css(type , slide_value2);
 		}); 		
 		sliders_value2[i].innerHTML = sliders2[i].getAttribute("start-data");
 	});
 	
 	$('#doaction, #doaction2, #post-query-submit').click(function(){
		
		var selected = [];
		
		//체크 된 게시판 ID를 가져온다.			
		$('#the-list input:checked').each(function() {
		    selected.push($(this).attr('value'));
		});
		
		if(selected.length == 0 ){
			alert("선택된 아이템이 없습니다.");
			return;
		}		
		
		var value1 = $( "#bulk-action-selector-top option:selected" ).val();
		var value2 = $( "#bulk-action-selector-bottom option:selected" ).val();
		var ebbsmate_nonce = $("#ebbsmate_board_nonce").val();
				
		if(value1 == "deleteboard" || value2 == "deleteboard") {		
			var url = window.location.href+"&ebbsmate_board_nonce="+ebbsmate_nonce+"&action=deleteboard&board_id="+selected;
			window.location = url;
		} else if(value1 == "deletepost" || value2 == "deletepost") {
			var url = window.location.href+"&ebbsmate_post_nonce="+ebbsmate_nonce+"&mode=delete&post_id="+selected;
			window.location = url;
		} else if(value1 == "untrashpost" || value2 == "untrashpost") {
			var url = window.location.href+"&ebbsmate_post_nonce"+ebbsmate_nonce+"&action=delete_u&post_id="+selected;
			window.location = url;
		} else if(value1 == "pdeletepost" || value2 == "pdeletepost") {
			var url = window.location.href+"&ebbsmate_post_nonce="+ebbsmate_nonce+"&action=delete_p&post_id="+selected;
			window.location = url;
		} else if(value1 == "untrashboard" || value2 == "untrashboard") {
			var url = window.location.href+"&ebbsmate_board_nonce="+ebbsmate_nonce+"&action=deleteboard_u&board_id="+selected;
			window.location = url;
		} else if(value1 == "pdeleteboard" || value2 == "pdeleteboard") {
			var url = window.location.href+"&ebbsmate_board_nonce="+ebbsmate_nonce+"&action=deleteboard_p&board_id="+selected;
			window.location = url;
		}
		
		
	});
 	
 	$(".get_taxonomy_template").click(function(){
 		
 		var taxonomy_text = $(this).prev();
 		var element_cnt = jQuery(".ebb-itemized-wrapper .ebbs-metabox").length;
 		
 		
 		var taxonomy_array = taxonomy_text.val().split( ',' );
 		var exist = false;
 		$.each(taxonomy_array, function( index, value ) {
 			var existing_element = jQuery("input[name='ebbsmate_section["+value+"][title]']");
 			if(existing_element.is(':empty')){
 				alert(value+" 는(은) 이미 존재 합니다.");
 				exist = true;
 				return false;
 			}
		});
 		
 		if(exist) return false;
 		
 		var data = {
 				action 			: "ebbsmate_get_taxonomy_template",
 				taxonomy_text 	: taxonomy_text.val(),
 				index			:	element_cnt
 			}
 		
 		
 		$.post(common_js.ajax_url, data, function(response) {
 			var response = jQuery.parseJSON( response );
 			
 			$(".ebb-itemized-wrapper").append(response.template);
			
		});
 		
 	});
 	
 	$("input[name='ebbsmate_section_flag']").on( "click", function() {
 		var target = $(".ebbs-metaboxes-wrapper div.attributes-meta");
 		if($( this ).val() == 1){
 			if(target.css("display") == "none"){
 				target.slideToggle( "fast" );
 			}
 		}else{
 			if(target.css("display") != "none"){
 				target.slideToggle( "fast" );
 			}
 		}
 	});
 	
 	
 	$(".ebbs-metaboxes-wrapper").on('click', '.ebbs-metabox .handlediv', function(e) {
 		var handlediv = $(this);
 		var metabox = handlediv.parents(".ebbs-metabox");
 		//class open , close toggle
 		var attributes = metabox.children(".ebbs_variable_attributes");
 		attributes.slideToggle( "fast", function() {
 			if(attributes.css("display") == "none"){
 				metabox.removeClass( "open" );
 				metabox.addClass( "closed" );
 			} else {
 				metabox.removeClass( "closed" );
 				metabox.addClass( "open" );
 			}
 		});
 		
 		
 	}).on('click', '.ebbs-metabox h3 a.delete', function(e) {
 		e.preventDefault();
 		var handlediv = $(this);
 		var metabox = handlediv.parents(".ebbs-metabox");
 		
 		if(confirm("이 분류를 삭제하시겠습니까?")) {
 			metabox.remove();
 		}
 		
 	});
 	
 	$("select[name='pavo_board_id']").change( function() {
 		var data = {
				action 		: "ebbsmate_get_section_list",
				board_id	: $( this ).val(),
			}
		
		$.post(common_js.ajax_url, data, function(response) {
			var response = $.parseJSON( response );
			$("span.ebbsmate_section_area").html(response.template);
		});
 		
 		
 	});
 	
 	
 	//게시판 edit -> 스타일 수정 이동
 	$("#ebbsmate_board_update_form").on("click",'input.style_edit', function(e){
 		var board_id = $( this ).data("boardid");
 		var fileName = $("#ebbsmate_board_update_form select[name=ebbsmate_css_style]").val();
 		
 		var data = {
			action 		: "ebbsmate_get_editstyleurl",
			file_name	: fileName,
			board_id	: board_id
		}

		$.post(common_js.ajax_url, data, function(response) {
			var response = $.parseJSON( response );
			
			if(!response.error_info){
				$(window).attr('location',response.redirect)
			}else{
				alert(response.message);
			}
		});
 	});
 	
 	//스타일 리스트 버튼
 	$(".style-config-wrap").on("click",'input.style_edit', function(e){
 		var fileName = $( this ).parents("tr.ebbs-style-list").data("filename");
 		window.location = window.location.href+"&action=editstyle&board_id=0&filename="+fileName;
 	}).on("click",'input.style_copy', function(e){
 		var fileName = $( this ).parents("tr.ebbs-style-list").data("filename");

 		$("div[name=ebbsmate_stylecopy_popup]").find("input[name=ebbsmate_stylecopy_name]").val(fileName+"_copy");
 		$("div[name=ebbsmate_stylecopy_popup]").find("input[name=ebbsmate_stylecopy_original]").val(fileName);
 		
 		$("div[name=ebbsmate_stylecopy_popup]").show();
 		
 	}).on("click",'input.style_delete', function(e){
 		var fileName = $( this ).parents("tr.ebbs-style-list").data("filename");
 		var target = $( this ).parents("tr.ebbs-style-list");
 		
 		if(confirm(fileName+" 파일을 삭제하시겠습니까?")) {
 			var data = {
				action 		: "ebbsmate_delete_style",
				file_name	: fileName,
			}

 			$.post(common_js.ajax_url, data, function(response) {
 				var response = $.parseJSON( response );
 				
 				if(!response.error_info){
 					target.remove();
 					alert(response.message);
 				}else{
 					alert(response.message);
 				}
 				
 			});
 		}
 	});
 	
 	
 	//스타일 생성
 	$("div.pavoboard-wrapper").on("click",'input.create-style', function(e){
 		e.preventDefault();
 		var form_data = $(this).parents("form").serialize()+ "&action=ebbsmate_create_style";
 		
 		$.post(common_js.ajax_url, form_data, function(response) {
			var response = $.parseJSON( response );
			
			alert(response.message);
			if(!response.error_info){
				$(window).attr('location',response.redirect)
			}
		});
 	});
 	
 	
 	// css copy popup
 	$("div[name=ebbsmate_stylecopy_popup]").on("click",'input.copy_submit', function(e){
 		e.preventDefault();
 		var parents_popup = $( this ).parents("div[name=ebbsmate_stylecopy_popup]");
 		
 		var copy_file = $( this ).parents("div[name=ebbsmate_stylecopy_popup]").find("input[name=ebbsmate_stylecopy_name]").val();
 		var original_file = $( this ).parents("div[name=ebbsmate_stylecopy_popup]").find("input[name=ebbsmate_stylecopy_original]").val();
 		
 		var data = {
			action 		: "ebbsmate_copy_style",
			copy_file	: copy_file,
			orig_file	: original_file
		}
 		
 		$.post(common_js.ajax_url, data, function(response) {
 			
			var response = $.parseJSON( response );
			
			if(!response.error_info){
				parents_popup.hide();
				$("div.style-config-wrap .wp-list-table tbody").append(response.new_file);
				alert(response.message);
			}else{
				alert(response.message);
			}
		});
 		
 	}).on("click",'input.copy_cancel', function(e){
		$( this ).parents("div[name=ebbsmate_stylecopy_popup]").hide();
	});
 	
 	$(".pavoboard-dashboard .setting_field").on("change",'select[name=basic_font]', function(e){
 	    var font = "";
 	    
    	switch($( this ).val()) {
	    	case "default":
	    		font = "";
	    		break;
	    	case "nanumgothic":
	    		font = "'Nanum Gothic', sans-serif";
	    		break;
	    	case "jejumyeongjo":
	    		font = "'Jeju Myeongjo', serif";
	    		break;
	    	case "kopubbatang":
	    		font = "'KoPub Batang', serif";
	    		break;
	    	case "nanumbrushscript":
	    		font = "'Nanum Brush Script', cursive";
	    		break;
	    	case "nanumcoding":
	    		font = "'Nanum Gothic Coding', monospace";
	    		break;
	    	case "nanummyeongjo":
	    		font = "'Nanum Myeongjo', serif";
	    		break;
	    	case "nanumpenscript":
	    		font = "'Nanum Pen Script', cursive";
	    		break;
	    	case "notosanskr":
	    		font = "'Noto Sans KR', sans-serif";
	    		break;
	    	case "hanna":
	    		font = "'Hanna', sans-serif";
	    		break;
	    	case "jejugothic":
	    		font = "'Jeju Gothic', sans-serif";
	    		break;
	    	case "jejuhallasan":
	    		font = "'Jeju Hallasan', cursive";
	    		break;
    	}
    	
    	$("div.pavoboard-preview .setting-content.preview").css("font-family", font);
    	    	
//    	jQuery("#"+option).css("font-family", font);
//    	jQuery("."+option).css("font-family", font);
    	
    	
 	});
 	
 	
 	
	$( '#tiptip_holder' ).removeAttr( 'style' );
	$( '#tiptip_arrow' ).removeAttr( 'style' );
	$( '.tips' ).tipTip({
		'attribute': 'data-tip',
		'fadeIn': 50,
		'fadeOut': 50,
		'delay': 200
	});
	
	
 	
 	
});



jQuery( function( $ ) {
	
	// Attribute ordering
	$('.ebb-itemized-wrapper').sortable({
		items: '.ebbs-metabox',
		cursor: 'move',
		axis: 'y',
		handle: 'h3',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
	});
	
});

function pavoboard_delete_image() {
	jQuery("#pavoboard_headline_image_url").val("");
	jQuery(".preface_border_color").css("background-image", "url('')");
	jQuery("#pavoboard_headline_image_url").val("");
}

function ebbsmate_get_font(value) {
   
	var font = "";
	
	switch(value) {
    	case "default":
    		font = "Arial, Helvetica, sans-serif";
    		break;
    	case "nanumgothic":
    		font = "'Nanum Gothic', sans-serif";
    		break;
    	case "jejumyeongjo":
    		font = "'Jeju Myeongjo', serif";
    		break;
    	case "kopubbatang":
    		font = "'KoPub Batang', serif";
    		break;
    	case "nanumbrushscript":
    		font = "'Nanum Brush Script', cursive";
    		break;
    	case "nanumcoding":
    		font = "'Nanum Gothic Coding', monospace";
    		break;
    	case "nanummyeongjo":
    		font = "'Nanum Myeongjo', serif";
    		break;
    	case "nanumpenscript":
    		font = "'Nanum Pen Script', cursive";
    		break;
    	case "notosanskr":
    		font = "'Noto Sans KR', sans-serif";
    		break;
    	case "hanna":
    		font = "'Hanna', sans-serif";
    		break;
    	case "jejugothic":
    		font = "'Jeju Gothic', sans-serif";
    		break;
    	case "jejuhallasan":
    		font = "'Jeju Hallasan', cursive";
    		break;
	}

	return font;
}

function border_width_preview() {
	var top = "0";
	var bottom = "0";
	var left = "0";
	var right = "0";
	
	jQuery("input:checkbox[name='headline_border_width[]']:checked").each(function (index) {  
				
		var position = jQuery(this).val();
		var css_file_name = jQuery("input[name=css_file_name]").val();
		var border = "1px";
				
		if(css_file_name == "custom_flatsome") {
			border = "2px";
		}
		
		
		if(position == "top") {
			top = border;
		} else if(position == "bottom") {
			bottom = border;
		} else if(position == "left") {
			left = border;
		} else if(position == "right") {
			right = border;
		}
	});
			
	var border_width = top + " " + right + " " + bottom + " " + left;
		
	jQuery("div.pavoboard-wrapper .head-st").css("border-width", border_width);
}

function font_decoration_preview() {
	//헤더 텍스트
	if(jQuery("input:checkbox[name='header_font_bold']").is(":checked") == true) {
		jQuery(".th-text-span").css("font-weight", "bold");
	} else {
		jQuery(".th-text-span").css("font-weight", "normal");
	}
	
	if(jQuery("input:checkbox[name='header_font_italic']").is(":checked") == true) {
		jQuery(".th-text-span").css("font-style", "italic");
	} else {
		jQuery(".th-text-span").css("font-style", "normal");
	}

	if(jQuery("input:checkbox[name='header_font_underline']").is(":checked") == true) {
		jQuery(".th-text-span").css("text-decoration", "underline");
	} else {
		jQuery(".th-text-span").css("text-decoration", "none");
	}
	
	//링크 텍스트
	if(jQuery("input:checkbox[name='link_font_bold']").is(":checked") == true) {
		jQuery(".pavoboard-list-title > a").not('.noti-td').css("font-weight", "bold");
	} else {
		jQuery(".pavoboard-list-title > a").not('.noti-td').css("font-weight", "normal");
	}
	
	if(jQuery("input:checkbox[name='link_font_italic']").is(":checked") == true) {
		jQuery(".pavoboard-list-title > a").not('.noti-td').css("font-style", "italic");
	} else {
		jQuery(".pavoboard-list-title > a").not('.noti-td').css("font-style", "normal");
	}

	if(jQuery("input:checkbox[name='link_font_underline']").is(":checked") == true) {
		jQuery(".pavoboard-list-title > a").not('.noti-td').css("text-decoration", "underline");
	} else {
		jQuery(".pavoboard-list-title > a").not('.noti-td').css("text-decoration", "none");
	}
	
	//공지 텍스트
	if(jQuery("input:checkbox[name='notice_font_bold']").is(":checked") == true) {
		jQuery(".noti-td > a").css("font-weight", "bold");
	} else {
		jQuery(".noti-td > a").css("font-weight", "normal");
	}
	
	if(jQuery("input:checkbox[name='notice_font_italic']").is(":checked") == true) {
		jQuery(".noti-td > a").css("font-style", "italic");
	} else {
		jQuery(".noti-td > a").css("font-style", "normal");
	}

	if(jQuery("input:checkbox[name='notice_font_underline']").is(":checked") == true) {
		jQuery(".noti-td > a").css("text-decoration", "underline");
	} else {
		jQuery(".noti-td > a").css("text-decoration", "none");
	}
}

function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); } 

function nospaces(t) {
  	if(t.value.match(/\s/g)){
    	t.value=t.value.replace(/\s/g,'');
  	}
}

function inputchar(evt) {
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode( key );
	
	var regex = /^[a-zA-Z\b]+$/;
	if( !regex.test(key) ) {
		theEvent.returnValue = false;
	    if(theEvent.preventDefault) theEvent.preventDefault();
	}
}

function style_edit(boardid) {		
	var selected = jQuery( "#ebbsmate_css_style option:selected" ).val();
	var url = window.location.href+"&action=editstyle&board_id="+boardid+"&filename="+selected;
	window.location = url;
}

function board_update() {	
	jQuery("#ebbsmate_board_update_form").submit();
}

function style_preview() {	
	jQuery("#ebbsmate_style_form").submit();
}

function board_preview() {
	var oldurl = jQuery("#ebbsmate_board_create_form").attr("action");
	var newurl = jQuery("#ebbsmate_board_preview_url").val();
	
	jQuery("#ebbsmate_board_create_form").attr("action", newurl);	
	jQuery("#ebbsmate_board_create_form").attr("target", "_blank");
	jQuery("#ebbsmate_board_create_form").submit();
	
	jQuery("#ebbsmate_board_create_form").attr("action", oldurl);
	jQuery("#ebbsmate_board_create_form").attr("target", "");
}

function board_update_preview() {
	var oldurl = jQuery("#ebbsmate_board_update_form").attr("action");
	var newurl = jQuery("#ebbsmate_update_board_preview_url").val();
		
	jQuery("#ebbsmate_board_update_form").attr("action", newurl);	
	jQuery("#ebbsmate_board_update_form").attr("target", "_blank");
	jQuery("#ebbsmate_board_update_form").submit();
	
	jQuery("#ebbsmate_board_update_form").attr("action", oldurl);
	jQuery("#ebbsmate_board_update_form").attr("target", "");
}

function board_create() {
	jQuery("#ebbsmate_board_create_form").submit();
}


//선택한 회원 등급을 삭제한다.
function pavo_boardmate_delete_role(type,role) {
	//이름 삭제
	jQuery("#pavo_bbs_"+type+"_role_list span[name="+role+"]").remove();
	
	//값이 role인 input 삭제
	jQuery("input[name=_pbbs_board_"+type+"_permission]").find('[value="'+role+'"]').remove();
}
