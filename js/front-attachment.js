/**
 *   pavoboard fron Attachment js
 */
jQuery(document).ready(function($) {
	
	$('.file-upload .text').val('첨부할 파일을 선택하세요.');
    
    $("div.attachFile > a").click(function(e){
    	$(this).siblings(".attach_layer").children("ul").toggle();
    });
	
	//첨부파일 삭제
	$('.pavo_delete_attach').click(function(){
		var write_span 		= $(this).parents("span.write-span");
		var new_fileform	= $(this).parents("span.write-span").siblings("span.write-span");
		var deleted 		= $(this).siblings().text();
		var cur_size 		= parseInt($(this).parents("div.pavo_attach_file_div").attr("size"));
		var allowed_size = parseInt($("input[name='pavo_attach_size']").val());
		allowed_size += cur_size;
		
		console.log(new_fileform);
		
		$("input[name='pavo_attach_size']").val(allowed_size);
		$("div.max-attach-size span").html( (allowed_size / (1024*1024)).toFixed(2)+"Mb" );
						
		$(this).parents("div.attach-box").before('<input type="hidden" name="pavo_board_deleted_file[]" value="'+deleted+'"/>');
		
		write_span.remove();
		new_fileform.show();
		
	});
	
	//첨부파일 삭제
	/*
	$('.pavo_delete_attach').click(function(){			
		var index = $(this).parent().index();
		var deleted = $(this).siblings().text();
						
		$(this).siblings().remove();
		$(this).before('<label for="entry-file"><span data-title="첨부파일">첨부파일</span></label>'+
	            '<span style="position:relative; width:300px" class="write-span">'+
	            '<div class="file-upload">'+
	              '<input type="text" style="font-size:12px" readonly="readonly" title="파일 첨부하기" class="text">'+
	              '<div class="upload-btn">'+
	                '<button title="파일 찾아보기" class="img-upload pavoboard-button" type="button"><span>찾아보기</span></button>'+
	                '<input type="file" name="upload[]" title="파일 찾아보기" class="file">'+
	              '</div>'+
	            '</div>'+
	            '</span><input type="hidden" name="pavo_board_deleted_file[]" value="'+deleted+'"/>');
		$(this).remove();
	});
	*/
	$("div.pavoboard-write-wrapper").on('change', 'input[name="upload[]"]', function(e) {
		
		//sizeInMB = (sizeInBytes / (1024*1024)).toFixed(2);
		
		var max_size = pavoboard_attach.file_attach_size;

		var totalfilesize = 0;
		
		if(pavoboard_attach.attach_type == "edit"){
			
			var attached_size = 0;
			var attached = $("div.pavo_attach_file_div");
			attached.each(function( index ) {
				attached_size += parseInt($(this).attr("size"));
			});
			
			totalfilesize +=attached_size;
		}
		var file_input = $("#pavo_post_form div.attach-box input.file");
		file_input.each(function( index ) {
			if(!$.isEmptyObject(this.files[0])){
				totalfilesize += this.files[0].size;
			}
		});

		if(totalfilesize >= max_size) {
			$("div.max-attach-size span").html("최대 첨부 가능 용량을 초과되었습니다.");
			$("input[name='pavo_attach_size']").val(-1);
		}else{
			$("input[name='pavo_attach_size']").val(max_size-totalfilesize);
			$("div.max-attach-size span").html( ((max_size-totalfilesize) / (1024*1024)).toFixed(2)+"Mb" );
		}
		
		
		
	}).on('click', 'div.file-upload input.text', function(e) {
		$(this).siblings().children("input.file").click();
	}).on('change', '.file-upload .file', function(e) {
		var filename = $(this).val();
        $(this).parents("div.file-upload").find('.text').val(filename);
	}).on('click', 'a.add-attach', function(e) {
		var orgin = $(".attach-element.hidden");
		var cloned = orgin.clone(false);
		var target = $(".attach-element:last");
		cloned.insertAfter(target); 
		
		$(".attach-element:last").removeClass( "hidden" );
		
		/*
		var target = $(".attach-element:last").clone();
		$(".attach-element:last").append(target);
		
		var control = $(".attach-element:last");
		console.log(control);
		control.replaceWith( control.val('').clone( true ) );
		
		*/
	});
	
	
	/*
	$('.file-upload .file').change(function(){
        var filename = $(this).val();
        $(this).parents("div.file-upload").find('.text').val(filename);
    });
    */
});