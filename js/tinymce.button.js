(function() { 
	tinymce.PluginManager.add('ebbsmate_tc_button', function( editor, url ) {	
		editor.addButton( 'ebbsmate_tc_button', { 
			title: 'e-BBSMate 게시판',
			image: url+'/../images/icon_ebbsmate.png',			
			onclick: function() { 
				var data = {
							'action': 'ebbsmate_get_all_boards',
						   };
								
				jQuery.post(ajaxurl, data, function(response) {
					editor.windowManager.open({ 
						title: '게시판 선택',
		                width  : 225, 
		                height : 50, 
		                inline : 1,
		                html: response,
		                buttons: [
						           {
						               text: '확인',
						               onclick: function(){
						            	   var board_id = '[ebbsmate id="'+jQuery("#ebbsmate_board_id").val()+'"]';
						            	   						            	   
						            	   editor.insertContent(board_id);
						                   tinymce.activeEditor.windowManager.close();
						               }
						           },
						           {
						               text: '취소',
						               onclick: 'close'
						           },
						        ]				
					});
				});
			} 
		}); 
	}); 
})();