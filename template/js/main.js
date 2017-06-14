function send() {
			var msg = $('#send').serializeArray();

			$.ajax({
				type: "POST",
                url: "/",
                dataType: "html",
                data: msg,
                
                success: function(result) {
					$('body').html(result);
					if($('.errors').length) { $('.editor').slideDown(); }
                }
	        });
		}

		function hide_editor() {
			$('.editor').slideUp();
		}

		function add_tag(i) {
			if (document.getSelection) {
				var tag;
				switch(i) {
					case 1: tag = 'b'; 
					break;

					case 2: tag = 'i'; 
					break;

					case 3: tag = 'code'; 
					break;

					case 4: tag = 'a'; 
					break;
				}
	            var txt = $('.editor_area').find('.txt');
	      		var txtElem = txt[0];
	      		var txtVal = txt.val();
	      		if (txtElem.selectionStart !== txtElem.selectionEnd) {
			        var prefix = txtVal.substr(0 , txtElem.selectionStart);
			        var select = txtVal.substr(txtElem.selectionStart, txtElem.selectionEnd - txtElem.selectionStart);
			        var postfix = txtVal.substr(txtElem.selectionEnd);
			        if(tag == 'a') {
			        	txt.val(prefix + '<'+tag+' href="">' + select + '</'+tag+'>' + postfix);
			        } else { txt.val(prefix + '<'+tag+'>' + select + '</'+tag+'>' + postfix); }
	    		}
	    	}
		}

		$(document).ready(function () {

			$('.add-comment').click(function(){
				var $editor = $('.editor');
				$editor.hide();
				$('div.errors').remove();
		    	var clone = $editor.clone();
		    	$editor.remove();
		    	setTimeout(function(){
		    		$(clone).css("margin", "5px 0 5px 0px");
		      		$(clone).insertAfter($(".comments-all")).slideDown();
		      		$("input[name=parent]").val(0);
		      		$('input[name=curren_comment]').val(-1);
		    	}, 200);
			    return false;
			});
		  
			$('.comment-ans').click(function(){
				var $editor = $('.editor');
				$editor.hide();
				$('div.errors').remove();
			    var mid = $(this).attr("id");
			    var clone = $editor.clone();
			    $editor.remove();
			    setTimeout(function(){
			      $(clone).css("margin", "5px 0 5px 0");
			      $(clone).insertAfter("div#comment_msg_"+mid).slideDown();
			      $("input[name=parent]").val(mid);
			      $('input[name=curren_comment]').val(mid);
			    }, 200);
			});

			$("div.holder").jPages({
		      	containerID : "commentRoot",
		      	previous : "←",
		      	next : "→",
		      	perPage : 25,
		      	delay : 0,
		      	startPage : $.cookie("page"),
		    });

		    $('a.nav').click(function(){
		    	$.cookie("page", $(this).html());
		    });

		   	$('.jp-previous').click(function(){
				if($(this).attr('class') != 'jp-previous jp-disabled') {
					var previous = parseInt($('.jp-current').html())-1;
			    	$.cookie("page", previous);
		    	}
		    });

			$('.jp-next').click(function(){
				if($(this).attr('class') != 'jp-next jp-disabled') {
					var next = parseInt($('.jp-current').html())+1;
			    	$.cookie("page", next);
		    	}
		    });

		    $('.add-tag').click(function(){
		    	alert('fd');
		        if (document.getSelection) {
		        	var tag = $(this).data('mode-btn-add-tag');
		            var txt = $(this).parents('.editor_area').find('.txt');
		      		var txtElem = txt[0];
		      		var txtVal = txt.val();
		      		if (txtElem.selectionStart !== txtElem.selectionEnd) {
				        var prefix = txtVal.substr(0 , txtElem.selectionStart);
				        var select = txtVal.substr(txtElem.selectionStart, txtElem.selectionEnd - txtElem.selectionStart);
				        var postfix = txtVal.substr(txtElem.selectionEnd);
				        txt.val(prefix + '<'+tag+'>' + select + '</'+tag+'>' + postfix);
		    		}
		    	}
		  	});
		});